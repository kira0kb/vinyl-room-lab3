<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Album extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'artist', 'genre', 'edition', 'release_date',
        'description', 'sound_notes', 'tracks', 'cover_path',
    ];

    protected function casts(): array
    {
        return ['release_date' => 'date', 'tracks' => 'array'];
    }

    protected function title(): Attribute
    {
        return Attribute::make(set: fn (string $value) => trim($value));
    }

    protected function artist(): Attribute
    {
        return Attribute::make(set: fn (string $value) => trim($value));
    }

    protected function releaseDate(): Attribute
    {
        // В БД дата всегда записывается в одном формате.
        return Attribute::make(set: fn ($value) => Carbon::parse($value)->format('Y-m-d'));
    }

    public function musicUrl(): string
    {
        return 'https://music.yandex.ru/search?text='.rawurlencode($this->artist.' '.$this->title);
    }
}
