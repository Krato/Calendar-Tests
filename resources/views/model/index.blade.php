@extends('layouts.default')
@section('styles')
<link rel="stylesheet" href="{{ asset('calendar_assets/css/types.css') }}">
	
@endsection
@section('content')
	<div class="container">
		
	<table  class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>{{ config('calendar.modelColumn', 'name') }}</th>
				<th>Color</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
		@foreach($model as $item)
            <tr>
				<td>{{ $item->id }}</td>
				<td>{{ $item->$modelColumn }}</td>
				<td><div id="show-box" style="position:inherit" class="{{ $colorModel->getColor($item->id) }}"></div></td>
				<td>
					<a class="btn btn-primary" href="{{ url('calendar/model/edit/'.$item->id) }}">Edit</a>	
				</td>
			</tr>
        @endforeach
			
		</tbody>
	</table>


	</div>
@endsection
@section('scripts')
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>

@endsection