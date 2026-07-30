@extends('layouts.app')

@section('content')
<div class="content_case">

	@foreach ($sections as $title => $items)
		<h2>{{ $title }}</h2>
		<table class="download" width="550px" cellspacing="3" cellpadding="4" frame="border" rules="all">
			@foreach ($items as $item)
				<tr>
					<td>{{ $item['label'] }}</td>
					<td width="65">{{ $item['date'] }}</td>
					<td width="40">{{ $item['size'] }}</td>
					<td width="1">
						@if ($item['external'])
							<a class="downloadlink" href="{{ $item['href'] }}" target="_blank" rel="noopener">Скачать</a>
						@else
							<a class="downloadlink" href="{{ $item['href'] }}" download>Скачать</a>
						@endif
					</td>
				</tr>
			@endforeach
		</table>
		<hr>
	@endforeach

</div>
@endsection
