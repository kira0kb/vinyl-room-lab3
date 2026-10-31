<?php

namespace Database\Seeders;

use App\Models\Album;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    public function run(): void
    {
        $albums = json_decode(file_get_contents(database_path('data/albums.json')), true, 512, JSON_THROW_ON_ERROR);

        foreach ($albums as $album) {
            // Повторный запуск не создаёт дубликаты и не восстанавливает удалённые записи.
            Album::withTrashed()->firstOrCreate(
                ['title' => $album['title'], 'artist' => $album['artist']], $album,
            );
        }
    }
}
