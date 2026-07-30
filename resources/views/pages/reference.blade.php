@extends('layouts.app')

@section('content')
<div class="content_case">
	<h2>Инструкция по редактору</h2>
	<iframe width="638" height="440" src="{{ $game['reference_video'] }}" frameborder="0"
		allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	<hr>
</div>
@endsection
