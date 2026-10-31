@if (count($tracks))
    <ul class="track-preview list-unstyled" aria-label="Избранные композиции">
        @foreach ($tracks as $track)
            <li>
                <span class="track-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <span>{{ $track['title'] }}</span><span class="track-duration">{{ $track['duration'] }}</span>
            </li>
        @endforeach
    </ul>
@endif
