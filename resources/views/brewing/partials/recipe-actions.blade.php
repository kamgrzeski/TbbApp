

<div x-data="{ open: false, top: 0, left: 0 }"
     @click.outside="open = false"
     @scroll.window="open = false"
     @close-recipe-menus.window="open = false"
     onclick="event.stopPropagation()"
     class="inline-flex justify-end">
    <button type="button" aria-label="Akcje"
            @click="const r = $el.getBoundingClientRect(); const wasOpen = open; window.dispatchEvent(new CustomEvent('close-recipe-menus')); top = r.bottom + 6; left = r.right - 192; open = !wasOpen"
            class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>
    </button>

    <div x-show="open" x-transition x-cloak :style="`top: ${top}px; left: ${left}px`"
         class="fixed z-50 w-48 rounded-xl bg-white border border-gray-200 shadow-lg overflow-hidden divide-y divide-gray-100 text-left">
        @if($locked)
            <span class="block px-4 py-3 text-sm font-medium text-gray-400 cursor-not-allowed select-none"
                  title="Nie można usunąć zrealizowanej warki z przeszłości">
                Zablokowana
            </span>
        @else
            <form action="{{ route('brewing.destroy', $recipe) }}" method="POST"
                  onsubmit="return confirm('Czy na pewno chcesz usunąć recepturę: {{ $recipe->name }}?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full text-left px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50">
                    Usuń
                </button>
            </form>
        @endif
    </div>
</div>
