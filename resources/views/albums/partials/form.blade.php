{{-- Одна форма используется при создании и редактировании. --}}
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Проверь заполненные поля.</strong>
        <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
<div class="row g-4">
    <div class="col-lg-8">
        <div class="row g-3">
            @foreach (['title' => 'Название альбома', 'artist' => 'Исполнитель', 'genre' => 'Жанр', 'edition' => 'Подпись к альбому'] as $field => $label)
                <div class="col-md-6">
                    <label for="{{ $field }}" class="form-label">{{ $label }}{{ $field !== 'edition' ? ' *' : '' }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="text" class="form-control @error($field) is-invalid @enderror"
                           value="{{ old($field, $album->$field) }}" maxlength="{{ $field === 'genre' ? 80 : 150 }}" @required($field !== 'edition')>
                    <div class="invalid-feedback">@error($field){{ $message }}@else Заполни это поле. @enderror</div>
                </div>
            @endforeach
            <div class="col-md-6">
                <label for="release_date" class="form-label">Дата выпуска *</label>
                <input id="release_date" name="release_date" type="date" class="form-control @error('release_date') is-invalid @enderror"
                       value="{{ old('release_date', $album->release_date?->format('Y-m-d')) }}" min="1900-01-01" max="{{ now()->format('Y-m-d') }}" required>
                <div class="invalid-feedback">@error('release_date'){{ $message }}@else Укажи дату выпуска от 1900 года до сегодняшнего дня. @enderror</div>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Описание *</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="5" minlength="10" maxlength="3000" required>{{ old('description', $album->description) }}</textarea>
                <div class="invalid-feedback">@error('description'){{ $message }}@else Напиши описание от 10 до 3000 символов. @enderror</div>
            </div>
            <div class="col-12">
                <label for="sound_notes" class="form-label">О звучании</label>
                <textarea id="sound_notes" name="sound_notes" class="form-control @error('sound_notes') is-invalid @enderror"
                          rows="3" maxlength="1000">{{ old('sound_notes', $album->sound_notes) }}</textarea>
                @error('sound_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <fieldset class="mt-4">
            <legend class="h6 fw-bold">Избранные композиции</legend>
            <p class="form-text">Можно добавить до трёх треков. Время укажи в формате 3:45. Ненужные строки оставь пустыми.</p>
            @for ($i = 0; $i < 3; $i++)
                <div class="row g-2 mb-3 track-row">
                    <div class="col-8">
                        <label class="form-label small" for="track_title_{{ $i }}">Композиция {{ $i + 1 }}</label>
                        <input id="track_title_{{ $i }}" name="tracks[{{ $i }}][title]" type="text" maxlength="150"
                               class="form-control track-title @error('tracks.'.$i.'.title') is-invalid @enderror" value="{{ old('tracks.'.$i.'.title', $album->tracks[$i]['title'] ?? '') }}">
                        <div class="invalid-feedback">Укажи название композиции.</div>
                    </div>
                    <div class="col-4">
                        <label class="form-label small" for="track_duration_{{ $i }}">Длительность</label>
                        <input id="track_duration_{{ $i }}" name="tracks[{{ $i }}][duration]" type="text" pattern="[0-9]{1,2}:[0-5][0-9]" placeholder="3:45" maxlength="5"
                               class="form-control track-duration-input @error('tracks.'.$i.'.duration') is-invalid @enderror" value="{{ old('tracks.'.$i.'.duration', $album->tracks[$i]['duration'] ?? '') }}">
                        <div class="invalid-feedback">Укажи время, например 3:45.</div>
                    </div>
                </div>
            @endfor
        </fieldset>
    </div>
    <div class="col-lg-4">
        <label for="cover" class="form-label">Обложка{{ ! $album->exists ? ' *' : '' }}</label>
        <input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp" class="form-control @error('cover') is-invalid @enderror" @required(! $album->exists)>
        <div class="invalid-feedback">@error('cover'){{ $message }}@else Выбери обложку в формате JPG, PNG или WebP. @enderror</div>
        <p class="form-text">JPG, PNG или WebP, до 2 МБ и 4096 × 4096 пикселей. Изображение будет обрезано по центру до квадрата 900 × 900.</p>
        <p class="text-danger small" id="cover-error" role="status"></p>
        <img id="cover-preview" class="cover-preview img-fluid {{ $album->exists ? '' : 'd-none' }}"
             @if ($album->exists) src="{{ asset('storage/'.$album->cover_path) }}" @endif alt="Предпросмотр обложки" width="900" height="900">
    </div>
</div>
<div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
    <button class="btn btn-primary" type="submit">{{ $album->exists ? 'Сохранить изменения' : 'Добавить альбом' }}</button>
    <a class="btn btn-outline-secondary" href="{{ $album->exists ? route('albums.show', $album) : route('albums.index') }}">Отмена</a>
</div>
