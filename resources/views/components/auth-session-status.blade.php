@props(['status'])

@if ($status)
<<<<<<< HEAD
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
=======
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 dark:text-green-400']) }}>
>>>>>>> 7a2424f8a67bc4281ef5e9a434b27e005fb8a7ed
        {{ $status }}
    </div>
@endif
