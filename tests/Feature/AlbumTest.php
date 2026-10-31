<?php

namespace Tests\Feature;

use App\Models\Album;
use Database\Seeders\AlbumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlbumTest extends TestCase
{
    use RefreshDatabase;

    private function albumData(array $changes = []): array
    {
        return array_replace([
            'title' => 'Test Album', 'artist' => 'Test Artist', 'genre' => 'Рок',
            'release_date' => '2020-05-10', 'description' => 'Описание тестового альбома.',
            'tracks' => [['title' => 'First Track', 'duration' => '3:45']],
        ], $changes);
    }

    private function createAlbum(array $changes = []): Album
    {
        return Album::create($this->albumData(array_replace(['cover_path' => 'albums/am.jpg'], $changes)));
    }

    public function test_seed_is_repeatable_and_displays_five_albums(): void
    {
        $this->seed(AlbumSeeder::class);
        $this->seed(AlbumSeeder::class);
        $this->assertDatabaseCount('albums', 5);
        $this->get('/')->assertRedirect('/albums');
        $this->get(route('albums.index'))->assertOk()->assertSee('Arctic Monkeys')->assertSee('Melodrama');
    }

    public function test_detail_create_and_edit_pages_are_available(): void
    {
        $album = $this->createAlbum();
        $this->get(route('albums.show', $album))->assertOk()->assertSee('10.05.2020')->assertSee('First Track');
        $this->get(route('albums.create'))->assertOk()->assertSee('Добавить альбом');
        $this->get(route('albums.edit', $album))->assertOk()->assertSee('Test Album');
    }

    public function test_upload_creates_album_and_square_jpeg(): void
    {
        Storage::fake('public');
        $data = $this->albumData(['title' => '  New Album  ', 'cover' => UploadedFile::fake()->image('cover.png', 1200, 600)]);
        $response = $this->post(route('albums.store'), $data);
        $album = Album::firstOrFail();
        $response->assertRedirect(route('albums.show', $album));
        $this->assertSame('New Album', $album->title);
        Storage::disk('public')->assertExists($album->cover_path);
        $size = getimagesize(Storage::disk('public')->path($album->cover_path));
        $this->assertSame([900, 900, 'image/jpeg'], [$size[0], $size[1], $size['mime']]);
    }

    public function test_update_without_upload_keeps_existing_cover(): void
    {
        $album = $this->createAlbum();
        $this->put(route('albums.update', $album), $this->albumData(['title' => 'Changed']))->assertRedirect();
        $this->assertSame('Changed', $album->fresh()->title);
        $this->assertSame('albums/am.jpg', $album->fresh()->cover_path);
    }

    public function test_replacing_upload_removes_previous_uploaded_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('albums/uploads/old.jpg', 'old content');
        $album = $this->createAlbum(['cover_path' => 'albums/uploads/old.jpg']);
        $data = $this->albumData(['cover' => UploadedFile::fake()->image('new.jpg', 500, 1100)]);
        $this->put(route('albums.update', $album), $data)->assertRedirect();
        Storage::disk('public')->assertMissing('albums/uploads/old.jpg');
        Storage::disk('public')->assertExists($album->fresh()->cover_path);
    }

    public function test_invalid_data_does_not_change_album_or_cover(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('albums/uploads/old.jpg', 'old content');
        $album = $this->createAlbum(['cover_path' => 'albums/uploads/old.jpg']);
        $data = $this->albumData(['title' => '', 'cover' => UploadedFile::fake()->image('new.jpg')]);
        $this->put(route('albums.update', $album), $data)->assertSessionHasErrors('title');
        $this->assertSame('Test Album', $album->fresh()->title);
        $this->assertCount(1, Storage::disk('public')->allFiles());
    }

    public function test_server_validates_required_fields_date_and_image(): void
    {
        Storage::fake('public');
        $data = $this->albumData([
            'title' => ' ', 'description' => 'abc', 'release_date' => '2020-02-31',
            'cover' => UploadedFile::fake()->create('script.php', 1, 'text/plain'),
        ]);
        $this->post(route('albums.store'), $data)->assertSessionHasErrors(['title', 'description', 'release_date', 'cover']);
        $this->assertDatabaseCount('albums', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_future_date_and_missing_cover_are_rejected(): void
    {
        $data = $this->albumData(['release_date' => now()->addDay()->format('Y-m-d')]);
        $this->post(route('albums.store'), $data)->assertSessionHasErrors(['release_date', 'cover']);
    }

    public function test_large_dimensions_and_size_are_rejected(): void
    {
        $data = $this->albumData(['cover' => UploadedFile::fake()->image('wide.jpg', 4100, 10)]);
        $this->post(route('albums.store'), $data)->assertSessionHasErrors('cover');
        $data['cover'] = UploadedFile::fake()->image('big.jpg')->size(3000);
        $this->post(route('albums.store'), $data)->assertSessionHasErrors('cover');
    }

    public function test_track_title_and_duration_are_validated_together(): void
    {
        $album = $this->createAlbum();
        foreach ([['title' => 'Track', 'duration' => '3:99'], ['title' => '', 'duration' => '3:45'], ['title' => 'Track', 'duration' => '']] as $track) {
            $this->put(route('albums.update', $album), $this->albumData(['tracks' => [$track]]))->assertSessionHasErrors();
        }
        $this->assertSame('First Track', $album->fresh()->tracks[0]['title']);
    }

    public function test_empty_track_rows_are_removed(): void
    {
        $album = $this->createAlbum();
        $this->put(route('albums.update', $album), $this->albumData(['tracks' => [['title' => '', 'duration' => '']]]))->assertSessionHasNoErrors();
        $this->assertSame([], $album->fresh()->tracks);
    }

    public function test_delete_is_soft_and_hidden_from_normal_routes(): void
    {
        $album = $this->createAlbum();
        $this->delete(route('albums.destroy', $album))->assertRedirect(route('albums.index'));
        $this->assertSoftDeleted('albums', ['id' => $album->id]);
        $this->get(route('albums.index'))->assertDontSee('Test Album')->assertSee('Пока здесь пусто');
        $this->get(route('albums.show', $album))->assertNotFound();
        $this->get(route('albums.edit', $album))->assertNotFound();
        $this->put(route('albums.update', $album), $this->albumData())->assertNotFound();
        $this->delete(route('albums.destroy', $album))->assertNotFound();
    }

    public function test_reseeding_does_not_restore_a_deleted_album(): void
    {
        $this->seed(AlbumSeeder::class);
        Album::firstOrFail()->delete();
        $this->seed(AlbumSeeder::class);
        $this->assertSame(4, Album::count());
        $this->assertSame(5, Album::withTrashed()->count());
    }

    public function test_mutators_and_casts_normalize_values(): void
    {
        $album = $this->createAlbum(['title' => '  Album  ', 'artist' => '  Artist  ', 'release_date' => '2020-05-10 15:30:00']);
        $album->refresh();
        $this->assertSame('Album', $album->title);
        $this->assertSame('Artist', $album->artist);
        $this->assertSame('2020-05-10', $album->getRawOriginal('release_date'));
        $this->assertSame('10.05.2020', $album->release_date->format('d.m.Y'));
        $this->assertIsArray($album->tracks);
    }

    public function test_blade_escapes_user_text(): void
    {
        $album = $this->createAlbum(['title' => '<script>alert(1)</script>']);
        $this->get(route('albums.show', $album))->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
