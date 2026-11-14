<x-app-layout>
	{{-- Nagłówek strony w layoucie (tak jak w pozostałych widokach), nie wewnątrz komponentu Livewire --}}
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			@if(!empty($isClone) && isset($recipe))
				Klonowanie: {{ $recipe->name }} (Kopia)
			@elseif(isset($recipe) && $recipe)
				Edycja: {{ $recipe->name }}
			@else
				Nowa receptura
			@endif
		</h2>
	</x-slot>

	@livewire('recipe-form', ['recipe' => $recipe ?? null, 'isClone' => $isClone ?? false])

	{{-- Ściągawka: wszystkie wpisy z receptury (przy edycji i klonowaniu) --}}
	@if(isset($comments) && $comments->isNotEmpty())
		<div class="py-6">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 p-6">
					<h3 class="font-bold text-gray-800 uppercase tracking-widest text-sm mb-4">
						Ściągawka – wpisy z receptury ({{ $comments->count() }})
					</h3>
					<div class="space-y-4">
						@foreach($comments as $comment)
							<div class="border-l-4 border-blue-300 bg-gray-50 rounded p-3">
								<div class="text-xs text-gray-500 mb-1">
									<span class="font-semibold text-gray-700">{{ $comment->user->name ?? '—' }}</span>
									· {{ $comment->created_at->format('d.m.Y H:i') }}
								</div>
								<p class="text-sm text-gray-800 whitespace-pre-line">{{ $comment->body }}</p>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	@endif
</x-app-layout>
