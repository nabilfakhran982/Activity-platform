@props(['id', 'title', 'maxWidth' => 'max-w-md'])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 px-4 py-6 sm:px-0"
    style="background:rgba(0,0,0,0.6); align-items:center; justify-content:center; display:none;">
    <div class="bg-white rounded-2xl w-full {{ $maxWidth }} p-6 sm:p-8 relative mx-4" style="max-height:90vh; overflow-y:auto">
        <button onclick="closeModal('{{ $id }}')"
            class="absolute top-4 right-4 text-[#a09890] hover:text-[#1a1a18] text-xl font-bold">✕</button>
        <h2 class="font-display text-xl sm:text-2xl font-bold mb-6">{{ $title }}</h2>
        {{ $slot }}
    </div>
</div>
