@if(env('APP_ENV') == 'local')
	<img src="{{ asset('images/tbb.png') }}" alt="Logo" class="w-20 h-20">
@else
	<img src="{{ asset('public/images/tbb.png') }}" alt="Logo" class="w-20 h-20">
@endif
