@props(['value' => 0, 'editable' => false, 'name' => 'rating', 'id' => 'rating'])

<div {{ $attributes->merge(['class' => 'star-rating']) }}
     @if($editable) 
        data-editable="true" 
        data-name="{{ $name }}" 
        data-id="{{ $id }}"
     @endif>
    @for($i = 1; $i <= 5; $i++)
        <span class="star {{ $i <= $value ? 'active' : '' }}" data-value="{{ $i }}">
            <i class="fas fa-star"></i>
        </span>
    @endfor
    @if($editable)
        <input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}">
    @endif
</div>

@if($editable)
@push('styles')
<style>
.star-rating {
    display: inline-flex;
    direction: rtl;
    unicode-bidi: bidi-override;
}

.star-rating[data-editable="true"] .star {
    cursor: pointer;
    color: #ddd;
    font-size: 1.5rem;
    transition: color 0.2s;
}

.star-rating[data-editable="true"] .star:hover,
.star-rating[data-editable="true"] .star:hover ~ .star,
.star-rating[data-editable="true"] .star.active,
.star-rating[data-editable="true"] .star.active ~ .star {
    color: #ffc107;
}

.star-rating:not([data-editable="true"]) .star {
    color: #ddd;
    font-size: 1.2rem;
}

.star-rating:not([data-editable="true"]) .star.active {
    color: #ffc107;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.star-rating[data-editable="true"]').forEach(rating => {
        const stars = rating.querySelectorAll('.star');
        const input = rating.querySelector('input[type="hidden"]');
        
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = star.getAttribute('data-value');
                input.value = value;
                
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    });
});
</script>
@endpush
@endif
