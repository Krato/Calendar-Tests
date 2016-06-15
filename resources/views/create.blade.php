@extends('layouts.default')
@section('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.min.css">
	<link rel="stylesheet" href="{{ asset('calendar_assets/css/types.css') }}">
@endsection
@section('content')
	<div class="container">
		@if( view()->exists('vendor.infinety.calendar.form') )
			@include('vendor.infinety.calendar.form');
		@else
			@include('calendar:calendar.form')
		@endif
	</div>
@endsection
@section('scripts')
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
	<script type="text/javascript" src="{{ asset('calendar_assets/js/datetimepicker/jquery.moment.datetimepicker.min.js') }}"></script>
	<script>var formatDate = "{{ config('calendar.formatDate', 'Y-m-d H:i')}}"; </script>
	<script type="text/javascript" src="{{ asset('calendar_assets/js/calendar-events.js') }}"></script>

@endsection