<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h2>Weather Data</h2>
<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Temperature</th>
        <th>Humidity</th>
        <th>Wind</th>
        <th>Pressure</th>
        <th>Precipitation</th>
        <th>Condition</th>
    </tr>
    </thead>
    <tbody>
    @foreach($weatherData as $weather)
        <tr>
            <td>{{ $weather['date'] }}</td>
            <td>{{ $weather['temp'] }}</td>
            <td>{{ $weather['humidity'] }}</td>
            <td>{{ $weather['wind'] }}</td>
            <td>{{ $weather['pressure'] }}</td>
            <td>{{ $weather['precipitation'] }}</td>
            <td>{{ $weather['condition'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
