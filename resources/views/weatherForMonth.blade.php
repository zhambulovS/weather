@extends('layouts.app')

@section('content')
    <section class="vh-100" style="background-color: #519eb5;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-12 col-xl-10">
                    <div class="card shadow-0 border border-dark border-5 text-dark" style="border-radius: 10px;">
                        <div class="card-body p-4" style="max-height: 80vh; overflow-y: auto;">
                            <div class="row text-center mb-3">
                                <div class="d-flex justify-content-around align-items-center mb-3">
                                    <a href="{{ route('weather') }}" type="button" class="btn btn-secondary">Back</a>
                                    <a href="{{ route('downloadPDF') }}" class="btn btn-primary">Download PDF</a>

                                </div>
                            </div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
