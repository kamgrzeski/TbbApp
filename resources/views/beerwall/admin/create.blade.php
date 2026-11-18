<x-app-layout>

	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ isset($beerwall) ? __('Edytuj piwo na kranie') : __('Dodaj nowe piwo') }}
		</h2>
	</x-slot>

	<div class="py-12">

		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

			<div class="bg-white shadow-sm sm:rounded-lg p-6">


				<form
						action="{{ isset($beerwall)
                        ? route('beerwall.admin.update', $beerwall)
                        : route('beerwall.admin.store') }}"
						method="POST"
				>

					@csrf

					@if(isset($beerwall))
						@method('PUT')
					@endif


					@include('beerwall.admin.partials.form-basic')

					@include('beerwall.admin.partials.form-strength')

					@include('beerwall.admin.partials.form-prices')

					@include('beerwall.admin.partials.form-description')

					@php

						$currentStatus = old('beer_status');

						if ($currentStatus === null && isset($beerwall)) {

							if ($beerwall->is_premiere) {
								$currentStatus = 'premiere';
							} elseif ($beerwall->is_coming_soon) {
								$currentStatus = 'coming_soon';
							} elseif ($beerwall->is_ended) {
								$currentStatus = 'ended';
                            } elseif($beerwall->is_draft) {
                                    $currentStatus = 'is_draft';
							} else {
								$currentStatus = 'normal';
							}

						}

						if ($currentStatus === null) {
							$currentStatus = 'normal';
						}

					@endphp

					@include('beerwall.admin.partials.form-status')

					@include('beerwall.admin.partials.form-actions')

				</form>

			</div>

		</div>

	</div>

</x-app-layout>
