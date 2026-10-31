<form action="{{ route('albums.destroy', $album) }}" method="POST" class="delete-form">
    @csrf
    @method('DELETE')
    <button class="btn btn-link text-danger p-0 delete-button" type="submit" aria-label="Удалить альбом {{ $album->title }}">Удалить</button>
</form>
