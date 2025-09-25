@extends('admin.layout')

@section('title','Tạo phim')
@section('page_title','Tạo phim')

@section('content')
    <div class="card">
        <form method="post" action="{{ route('admin.movies.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-row">
                    <label>Tiêu đề</label>
                    <input type="text" name="title" placeholder="Nhập tên phim" required>
                </div>
                <div class="form-row">
                    <label>Mô tả</label>
                    <textarea name="description" rows="4" placeholder="Mô tả ngắn về nội dung..."></textarea>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Thời lượng (phút)</label>
                    <input type="number" name="duration" min="1" placeholder="Ví dụ: 120" required>
                </div>
                <div class="form-row">
                    <label>Ngày phát hành</label>
                    <input type="date" name="release_date">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Danh mục</label>
                    <select name="category_id">
                        <option value="">-- Chưa chọn --</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Diễn viên</label>
                    <select name="actor_ids[]" multiple>
                        @foreach($actors as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                    <div class="hint">Giữ Ctrl/Cmd để chọn nhiều.</div>
                </div>
            </div>

            <div class="section-title">Media</div>
            <div class="form-grid">
                <div class="form-row">
                    <label>Poster URL</label>
                    <input type="url" name="poster_url" placeholder="https://...">
                </div>
                <div class="form-row">
                    <label>Trailer URL</label>
                    <input type="url" name="trailer_url" placeholder="https://youtube.com/...">
                </div>
            </div>

            <div class="form-actions">
                <button class="btn" type="submit">Lưu</button>
                <a class="btn" href="{{ route('admin.movies.index') }}" style="background:transparent;border-color:var(--border);color:var(--text)">Hủy</a>
            </div>
        </form>
    </div>
@endsection
