@extends('layouts.app')

@section('content')
	
	<div class="container">
		
		<h1>Historia kegów</h1>
		
		<table class="table table-striped">
			
			<thead>
			
			<tr>
				<th>Data</th>
				<th>Piwo</th>
				<th>Operacja</th>
				<th>Ilość</th>
				<th>Notatka</th>
			</tr>
			
			</thead>
			
			<tbody>
			
			@foreach($movements as $movement)
				
				<tr>
					
					<td>
						{{ $movement->created_at->format('d.m.Y H:i') }}
					</td>
					
					<td>
						{{ $movement->recipe->name }}
					</td>
					
					<td>
						
						@switch($movement->type)
							
							@case('production')
								Rozlew
								@break
							
							@case('issue')
								Wydanie
								@break
							
							@case('return')
								Zwrot
								@break
							
							@case('fill')
								Napełnienie
								@break
						
						@endswitch
					
					</td>
					
					<td>
						{{ $movement->quantity }}
					</td>
					
					<td>
						{{ $movement->note }}
					</td>
				
				</tr>
			
			@endforeach
			
			</tbody>
		
		</table>
		
		{{ $movements->links() }}
	
	</div>

@endsection