@extends('client.layout')

@section('title', 'Đặt vé')

@section('content')
<h1>Đặt vé xem phim</h1>
<form id="booking-form" method="post" action="{{ route('booking.store') }}">
    @csrf
    <div class="row" style="margin-bottom:12px;">
        <label>Chọn phim:
            <select id="movie-select" name="movie_id" required>
                <option value="">-- Chọn phim --</option>
                @foreach($movies as $movie)
                    <option value="{{ $movie->id }}">{{ $movie->title }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="row" style="margin-bottom:12px;">
        <label>Chọn suất chiếu:
            <select id="showtime-select" name="showtime_id" required disabled>
                <option value="">-- Chọn suất chiếu --</option>
            </select>
        </label>
    </div>
    <div id="seats-area" style="margin-bottom:12px;display:none;"></div>
    <div class="row" style="margin-bottom:12px;">
        <select name="discount_code" style="padding:8px;border:1px solid #e5e7eb;border-radius:6px;min-width:200px">
            <option value="">-- Chọn mã giảm giá (tuỳ chọn) --</option>
            @foreach ($discountCodes as $d)
                <option value="{{ $d->code }}">
                    {{ $d->code }} -
                    @if($d->type==='percent') Giảm {{ $d->value }}% @else Giảm {{ number_format($d->value,0,',','.') }}đ @endif
                </option>
            @endforeach
        </select>
    </div>
    <div class="row" style="margin-bottom:12px;">
        <select name="combo_id" style="padding:8px;border:1px solid #e5e7eb;border-radius:6px;">
            <option value="">-- Chọn combo (tuỳ chọn) --</option>
            @foreach ($combos as $c)
                <option value="{{ $c->id }}">{{ $c->name }} ({{ number_format($c->price,0,',','.') }} đ)</option>
            @endforeach
        </select>
    </div>
    <div class="row">
        <button type="submit" class="button">Đặt vé</button>
    </div>
</form>
<script>
const showtimeSelect = document.getElementById('showtime-select');
const movieSelect = document.getElementById('movie-select');
const seatsArea = document.getElementById('seats-area');
let allShowtimes = @json($showtimesByMovie);

movieSelect.addEventListener('change', function() {
    const movieId = this.value;
    showtimeSelect.innerHTML = '<option value="">-- Chọn suất chiếu --</option>';
    showtimeSelect.disabled = true;
    seatsArea.style.display = 'none';
    seatsArea.innerHTML = '';
    if (movieId && allShowtimes[movieId]) {
        allShowtimes[movieId].forEach(st => {
            const opt = document.createElement('option');
            opt.value = st.id;
            opt.textContent = `${st.room_name} | ${st.start_time} → ${st.end_time}`;
            showtimeSelect.appendChild(opt);
        });
        showtimeSelect.disabled = false;
    }
});

showtimeSelect.addEventListener('change', function() {
    const showtimeId = this.value;
    seatsArea.innerHTML = '';
    seatsArea.style.display = 'none';
    if (!showtimeId) return;
    fetch(`/api/showtimes/${showtimeId}/seats`)
        .then(res => res.json())
        .then(data => {
            if (!data.seats) return;
            // Render lưới ghế giống seats.blade.php
            let html = '<div style="display:grid;grid-template-columns:repeat(8, 40px);gap:8px;margin-bottom:8px;">';
            data.seats.forEach(seat => {
                html += `<div class='seat ${seat.type === 'VIP' ? 'vip' : ''} ${seat.is_taken ? 'taken' : ''}' data-id='${seat.id}' title='${seat.code}' style='width:40px;height:40px;line-height:40px;text-align:center;border-radius:6px;cursor:pointer;border:1px solid #e5e7eb;${seat.type==='VIP' ? 'background:#fde68a;' : ''}${seat.is_taken ? 'background:#fca5a5;cursor:not-allowed;' : ''}'>${seat.code}</div>`;
            });
            html += '</div>';
            seatsArea.innerHTML = html;
            seatsArea.style.display = '';
            // seat selection logic
            const selected = new Set();
            document.querySelectorAll('.seat').forEach(el => {
                if (!el.classList.contains('taken')) {
                    el.addEventListener('click', () => {
                        const id = el.getAttribute('data-id');
                        if (selected.has(id)) { selected.delete(id); el.style.outline = 'none'; }
                        else { selected.add(id); el.style.outline = '2px solid #111827'; }
                    });
                }
            });
            document.getElementById('booking-form').addEventListener('submit', (e) => {
                if (selected.size === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất 1 ghế');
                    return;
                }
                // remove old seat_ids
                document.querySelectorAll('input[name="seat_ids[]"]').forEach(i => i.remove());
                for (const id of selected) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'seat_ids[]';
                    input.value = id;
                    e.target.appendChild(input);
                }
            }, { once: true });
        });
});
</script>
@endsection
