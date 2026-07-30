<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>{{ $title ?? 'Ваша Игра' }}</title>
	<link rel="stylesheet" type="text/css" href="/style.css">
	<link rel="shortcut icon" href="/res/icon.png" type="image/png">
</head>

<body>

	<nav class="header" id="page-header">
		<div class="header_case">
			<div class="header_links">
				@foreach (config_get('nav') as $item)
					@if ($item['route'] === ($active ?? null))
						<a class="header_links_cases" href="{{ route($item['route']) }}"><u>{{ $item['label'] }}</u></a>
					@else
						<a class="header_links_cases" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
					@endif
				@endforeach
			</div>
			<div class="header_logo">
				<a href="{{ route('home') }}"><img alt="Логотип" src="/res/logo.png"></a>
			</div>
		</div>
	</nav>

	<div class="content" id="page-content">
		@yield('content')

		<div class="content_bottom">
			<img alt="Конец" src="/res/ground.png">
		</div>
	</div>

	<footer> </footer>

	<script src="/js/nav.js" defer></script>

</body>

</html>
