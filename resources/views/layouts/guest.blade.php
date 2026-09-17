<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>{{ $title ?? 'Study Czechia Guide' }}</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}" />
</head>
<body>
{{ $slot }}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>