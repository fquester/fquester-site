@extends('layouts.app')

@section('content')
<div class="content_case">
	<h2>Описание</h2>
	@foreach ($game['description'] as $paragraph)
		<p>{{ $paragraph }}</p>
	@endforeach
	<p>
		<center style="color: white; font-size: 20px;"> {{ $game['tagline'] }} </center>
	</p>
	<hr>

	<h2>Скриншоты</h2>

	<div class="slider">
		@foreach ($game['screenshots'] as $i => $src)
			<input type="radio" name="switch" id="btn{{ $i + 1 }}" @if ($i === 0) checked @endif>
		@endforeach

		<div class="switch">
			@foreach ($game['screenshots'] as $i => $src)
				<label for="btn{{ $i + 1 }}"></label>
			@endforeach
		</div>

		<div class="slider-inner">
			<div class="slides">
				@foreach ($game['screenshots'] as $src)
					<img src="/{{ $src }}" />
				@endforeach
			</div>
		</div>
	</div>

	<hr style="top: -5px;">

	<h2>Системные требования</h2>
	<table class="tb" width="600px" cellspacing="3" cellpadding="4" frame="border" rules="all">
		@foreach ($game['requirements'] as $label => $value)
			<tr>
				<td>{{ $label }}</td>
				<td>{{ $value }}</td>
			</tr>
		@endforeach
	</table>
	<hr>

	<h2>Авторы</h2>
	<table class="tb" width="600px" cellspacing="3" cellpadding="4" frame="border" rules="all">
		@foreach ($game['authors'] as $name => $role)
			<tr>
				<td>{{ $name }}</td>
				<td>{{ $role }}</td>
			</tr>
		@endforeach
	</table>
	<hr>

	<h2>Связь</h2>
	<div class="content_links">
		@foreach ($game['contacts'] as $contact)
			<a href="{{ $contact['url'] }}"><img width="75px" class="content_links_cases" src="/{{ $contact['icon'] }}" alt="{{ $contact['name'] }}"></a>
		@endforeach
	</div>
	<hr>
</div>
@endsection
