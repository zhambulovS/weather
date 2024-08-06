<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
    <body>
    <div class="row">
        <div class="col-md-12">
            <div class="fixed-header-wrapper">
                <table class="table table-bordered table-striped fixed-header-table">
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
                    @foreach(array_chunk($weatherData, 5) as $weatherRow)
                        @foreach($weatherRow as $weather)
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
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
