<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tilt-Coaster</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>window.MQTT_HOST = "{{ env('PI_IP') }}";</script>
</head>
<body class="bg-gray-50">
<x-header/>
{{$slot}}
<x-footer/>
</body>
</html>
