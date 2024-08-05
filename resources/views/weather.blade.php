@extends('layouts.app')

@section('content')
    <section class="vh-100" style="background-color: #519eb5;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-12 col-xl-10">
                    <div class="card shadow-0 border border-dark border-5 text-dark" style="border-radius: 10px;">
                        <div class="card-body p-4">
                            <div class="row text-center">
                                <div class="col-md-9 text-center border-end border-5 border-dark py-4"
                                     style="margin-top: -1.5rem; margin-bottom: -1.5rem;">
                                    @if ($message)
                                        <p class="small">{{ $message }}</p>
                                    @else
                                    <div class="d-flex justify-content-around mt-3">
                                        <form action="/" method="GET">
                                            <div class="input-group">
                                                <input type="text" name="city" class="form-control" placeholder="Enter city name" required>
                                                <button name="submit" class="btn btn-info" type="submit">Search</button>
                                            </div>
                                        </form>
                                        @if ($weatherData)
                                        <p class=""> {{$today['date']}} </p>
                                        <a href="https://yandex.kz/pogoda/maps/nowcast" style="color: inherit; text-decoration: none;">Rain map</a>
                                    </div>
                                        <div class="d-flex justify-content-around align-items-center py-5 my-4">
                                            <p class="fw-bold mb-0" style="font-size: 2rem;">{{ $weatherData['name'] }}</p>
                                            <p class="fw-bold mb-0" style="font-size: 3rem;">{{ number_format($weatherData['main']['temp'], 0) }}°C</p>
                                            <div class="text-start">
                                                <p class="small">{{$today['time']}}</p>
                                                <p class="h3 mb-3">{{ date('l', $weatherData['dt']) }}</p>
                                                <p class=" mb-0">{{ $weatherData['weather'][0]['description'] }}</p>
                                                <p class=" mb-0">Wind: {{ number_format($weatherData['wind']['speed'], 0) . ' m/s'}}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($weatherDataForHour)
                                        <div class="d-flex justify-content-around align-items-center mb-3">
                                            <div class="flex-column">
                                                <i class="fas fa-minus"></i>
                                            </div>
                                            @for($i = 0; $i < 5; $i++)
                                                <div class="flex-column border cell-size" style="border-radius: 10px; padding: .75rem; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                                    <p class="small mb-1">{{ array_keys($forDayTemp)[$i] }}</p>
                                                    <p class="small mb-0"><strong>{{ $forDayTemp[array_keys($forDayTemp)[$i]] }}°C</strong></p>
                                                </div>
                                            @endfor
                                        </div>
                                </div>
{{--                                =================================================--}}
                                <div class="col-md-3 text-end">
                                    <p class="mt-3 mb-5 pb-5">
                                        <a type="button" class="btn btn-info" href="{{route('weatherForMonth')}}" style="color: inherit; text-decoration: none;">For a month</a>
                                    <p> For 12 hour ahead </p>
                                    @foreach(array_slice($weatherDataForHour['list'], 0, 6) as $dt)
                                        <p class="pb-1">
                                            <span class="pe-2">{{ substr($dt['dt_txt'], 11, 5) }}</span>
                                            <strong>{{ number_format($dt['main']['temp'], 0) }}°C</strong>
                                        </p>
                                        @endforeach</p>
                                </div>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
