<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Services\CoverImage;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index()
    {
        return view('albums.index', ['albums' => Album::orderBy('id')->get()]);
    }

    public function create()
    {
        return view('albums.create', ['album' => new Album]);
    }

    public function store(Request $request, CoverImage $images)
    {
        $data = $this->validatedData($request);
        $data['cover_path'] = $images->store($request->file('cover'));
        try {
            $album = Album::create($data);
        } catch (\Throwable $error) {
            $images->deleteUploaded($data['cover_path']);
            throw $error;
        }

        return redirect()->route('albums.show', $album)->with('success', 'Альбом добавлен.');
    }

    public function show(Album $album)
    {
        $previous = Album::where('id', '<', $album->id)->orderByDesc('id')->first();
        $next = Album::where('id', '>', $album->id)->orderBy('id')->first();

        return view('albums.show', compact('album', 'previous', 'next'));
    }

    public function edit(Album $album)
    {
        return view('albums.edit', compact('album'));
    }

    public function update(Request $request, Album $album, CoverImage $images)
    {
        $data = $this->validatedData($request, true);
        $oldCover = $album->cover_path;
        if ($request->hasFile('cover')) {
            $data['cover_path'] = $images->store($request->file('cover'));
        }

        try {
            $album->update($data);
        } catch (\Throwable $error) {
            $images->deleteUploaded($data['cover_path'] ?? null);
            throw $error;
        }
        if (isset($data['cover_path'])) {
            $images->deleteUploaded($oldCover);
        }

        return redirect()->route('albums.show', $album)->with('success', 'Изменения сохранены.');
    }

    public function destroy(Album $album)
    {
        // SoftDeletes скрывает альбом из списка, сохраняя запись и обложку.
        $album->delete();

        return redirect()->route('albums.index')->with('success', 'Альбом удалён из коллекции.');
    }

    private function validatedData(Request $request, bool $editing = false): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'artist' => ['required', 'string', 'max:150'],
            'genre' => ['required', 'string', 'max:80'],
            'edition' => ['nullable', 'string', 'max:150'],
            'release_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:1900-01-01', 'before_or_equal:today'],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            'sound_notes' => ['nullable', 'string', 'max:1000'],
            'tracks' => ['nullable', 'array', 'max:3'],
            'tracks.*' => ['array:title,duration'],
            'tracks.*.title' => ['nullable', 'required_with:tracks.*.duration', 'string', 'max:150'],
            'tracks.*.duration' => ['nullable', 'required_with:tracks.*.title', 'regex:/^\d{1,2}:[0-5]\d$/'],
            'cover' => [$editing ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4096,max_height=4096'],
        ]);

        // Пустые строки формы не сохраняем как композиции.
        $data['tracks'] = array_values(array_filter($data['tracks'] ?? [], fn ($track) => ! empty($track['title'])));
        unset($data['cover']);

        return $data;
    }
}
