@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

<div class="dropdown" {{ $attributes }}>
    {{ $trigger ?? '' }}
    <div class="dropdown-menu dropdown-menu-end {{ $contentClasses }}">
        {{ $content ?? $slot }}
    </div>
</div>
