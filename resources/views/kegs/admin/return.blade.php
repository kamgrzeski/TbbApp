@extends('layouts.app')

@section('content')
	
	<div class="container">
		
		<h1>Zwrot pustych kegów</h1>
		
		@if($errors->any())
			<div class="alert alert-danger">
				@foreach($errors->all() as $error)
					<div>{{ $error }}</div>
				@endforeach
			</div>
		@endif
		
		<form method="POST"
			  action="{{ route('kegs.return.store') }}">
			
			@csrf
			
			<div class="mb-3">
				
				<label class="form-label">
					Piwo, które było w kegach
				</label>
				
				<select name="recipe_id"
						class="form-select"
						required>
					
					<option value="">
						-- wybierz piwo --
					</option>
					
					@foreach($recipes as $recipe)
						
						<option value="{{ $recipe->id }}">
							
							{{ $recipe->name }}
						
						</option>
					
					@endforeach
				
				</select>
			
			</div>
			
			<div class="mb-3">
				
				<label class="form-label">
					Liczba pustych kegów
				</label>
				
				<input type="number"
					   name="quantity"
					   class="form-control"
					   min="1"
					   max="20"
					   value="1"
					   required>
			
			</div>
			
			<div class="mb-3">
				
				<label class="form-label">
					Notatka
				</label>
				
				<textarea name="note"
						  class="form-control"></textarea>
			
			</div>
			
			<button class="btn btn-success">
				Zapisz zwrot
			</button>
		
		</form>
	
	</div>

@endsection