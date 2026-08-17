<x-app-layout>
	@livewire('recipe-form', ['recipe' => $recipe ?? null, 'isClone' => $isClone ?? false])
</x-app-layout>