@props(['tier' => 'Bronze'])

@php
$colors = [
    'Bronze' => '#cd7f32',
    'Silver' => '#c0c0c0',
    'Gold' => '#ffd700',
    'Diamond' => '#b9f2ff'
];
$textColors = [
    'Bronze' => 'white',
    'Silver' => 'black',
    'Gold' => 'black',
    'Diamond' => 'black'
];
$icons = [
    'Bronze' => 'medal',
    'Silver' => 'award',
    'Gold' => 'trophy',
    'Diamond' => 'gem'
];
$color = $colors[$tier] ?? '#6c757d';
$textColor = $textColors[$tier] ?? 'white';
$icon = $icons[$tier] ?? 'star';
@endphp

<span class="badge" style="background: {{ $color }}; color: {{ $textColor }};">
    <i class="fas fa-{{ $icon }} me-1"></i>
    {{ $tier }}
</span>
