<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('expeses_tracker_icon.png') }}" type="image/x-icon" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">

<div class="container mx-auto px-4 py-6">
    <header class="mb-8">
    </header>
    {{-- Vue app mount point --}}
    <div id="app"></div>
</div>

</body>
</html>
