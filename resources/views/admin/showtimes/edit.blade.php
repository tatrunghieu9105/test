@extends('admin.layout')

@section('title','Sửa suất chiếu')
@section('page_title','Sửa suất chiếu #'.$showtime->id)

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.showtimes.update', $showtime) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-row">
                    <label>Phim</label>
                    <select name="movie_id" required>
                        @foreach ($movies as $m)
                            <option value="{{ $m->id }}" {{ $showtime->movie_id==$m->id?'selected':'' }}>{{ $m->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Phòng</label>
                    <select name="room_id" required>
                        @foreach ($rooms as $r)
                            <option value="{{ $r->id }}" {{ $showtime->room_id==$r->id?'selected':'' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Bắt đầu</label>
                    <input type="datetime-local" name="start_time" value="{{ str_replace(' ', 'T', $showtime->start_time) }}" required>
                </div>
                <div class="form-row">
                    <label>Kết thúc</label>
                    <input type="datetime-local" name="end_time" value="{{ str_replace(' ', 'T', $showtime->end_time) }}" required>
                </div>
            </div>

            <div class="form-row">
                <label>Giá</label>
                <input type="number" name="price" min="0" step="1000" value="{{ $showtime->price }}" required>
            </div>

            <div class="form-actions">
                <button class="btn" type="submit">Cập nhật</button>
                <a class="btn" href="{{ route('admin.showtimes.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>

    @if ($errors->any())
        <div class="flash error" style="margin-top:8px;">{{ $errors->first() }}</div>
    @endif
@endsection

@section('scripts')
<script>
  const movieDurations = {
    @foreach($movies as $m)
      {{ $m->id }}: {{ (int)($m->duration ?? 0) }},
    @endforeach
  };
  const movieSel = document.querySelector('select[name="movie_id"]');
  const roomSel  = document.querySelector('select[name="room_id"]');
  const startInp = document.querySelector('input[name="start_time"]');
  const endInp   = document.querySelector('input[name="end_time"]');

  function addMinutes(isoLocal, minutes){
    if(!isoLocal) return '';
    const d = new Date(isoLocal);
    if (isNaN(d.getTime())) return '';
    d.setMinutes(d.getMinutes() + minutes);
    const pad = n => String(n).padStart(2,'0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
  }

  function autoCalcEnd(){
    const movieId = movieSel.value;
    const dur = movieDurations[movieId] || 0;
    const startVal = startInp.value;
    if (dur > 0 && startVal){
      endInp.value = addMinutes(startVal, dur);
    }
  }

  async function checkOverlap(){
    const params = new URLSearchParams({
      room_id: roomSel.value,
      start_time: startInp.value.replace('T',' '),
      end_time: endInp.value.replace('T',' '),
      exclude_id: '{{ $showtime->id }}'
    });
    if(!params.get('room_id') || !params.get('start_time') || !params.get('end_time')) return;
    try{
      const res = await fetch(`{{ route('admin.showtimes.api') }}?${params.toString()}`, {headers:{'Accept':'application/json'}});
      if(!res.ok) return;
      const data = await res.json();
      endInp.setCustomValidity('');
      startInp.setCustomValidity('');
      if(data.overlap){
        startInp.setCustomValidity(data.message);
        endInp.setCustomValidity(data.message);
      }
    }catch(e){/* ignore */}
  }

  movieSel.addEventListener('change', () => { autoCalcEnd(); checkOverlap(); });
  startInp.addEventListener('change', () => { autoCalcEnd(); checkOverlap(); });
  endInp.addEventListener('change', checkOverlap);
  roomSel.addEventListener('change', checkOverlap);

  // initial validation
  checkOverlap();
</script>
@endsection


