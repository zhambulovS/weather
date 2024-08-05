<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenWeatherMapService;
use Barryvdh\DomPDF\Facade as PDF;
class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(OpenWeatherMapService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index(Request $request)
    {
        $weatherData = null;
        $weatherDataForHour = null;
        $message = null;
        if ($request->has('submit')) {
            $city = $request->input('city');
            $weatherData = $this->weatherService->getWeatherByCity($city);
            $weatherDataForHour = $this->weatherService->getWeatherByCityForHour($city);
            if (isset($weatherData['cod']) && $weatherData['cod'] == 404) {
                $message = "City not found.";
                $weatherData = null; // Ensure $weatherData is null if city not found
                $weatherDataForHour = null;
                return view('404');
            }
        }
        $forDayTemp = $this->forDayTemp($weatherDataForHour);
        $today = $this->today($weatherData);
        return view('weather', compact('message','weatherData', 'today', 'weatherDataForHour', 'forDayTemp'));
    }

    public function today($weatherData)
    {
        if (is_null($weatherData)) {
            return ['date' => '', 'time' => ''];
        }
        $dt = $weatherData['dt'];
        $timezone_offset = $weatherData['timezone'];
        $datetime = $dt + $timezone_offset;
        $today = date('d.m.Y', $datetime);
        $time_now = date('H:i', $datetime);
        return ['date' => $today, 'time' => $time_now];
    }
    public function forDayTemp($weatherDataForHour)
    {
        if (is_null($weatherDataForHour)) {
            return $resultDayTemp = [];
        }
        $resultDayTemp = []; // Массив для хранения температуры за каждый день

        foreach ($weatherDataForHour['list'] as $day) {
            $dateTime = $day['dt_txt'];
            $date = substr($dateTime, 0, 10); // Извлекаем только дату
            $time = substr($dateTime, 11, 5); // Извлекаем время (часы и минуты)

            // Проверяем, чтобы время было равно 15:00
            if ($time === '15:00') {
                if (!isset($resultDayTemp[$date])) {
                    // Если даты нет в массиве, добавляем её с температурой
                    $resultDayTemp[$date] = $day['main']['temp'];
                }
            }
        }

        // Преобразование дат в названия дней недели
        $resultDayTempWithDays = [];
        foreach ($resultDayTemp as $date => $temp) {
            $timestamp = strtotime($date); // Преобразование даты в временную метку
            $dayOfWeek = date('l', $timestamp); // Получение названия дня недели
            $resultDayTempWithDays[$dayOfWeek] = number_format($temp, 0);
        }

        return $resultDayTempWithDays;
    }

    public function forMonth()
    {
        return view('weatherForMonth');
    }

    public function notFound(){
        return view('404');
    }

    public function showMonthlyWeather()
    {
        // Пример данных, замените на реальные данные из вашей базы данных или API
        $weatherData = [
            ['date' => '01-08-2024', 'temp' => '25°C', 'humidity' => '60%', 'wind' => '15 km/h', 'pressure' => '1015 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '02-08-2024', 'temp' => '26°C', 'humidity' => '62%', 'wind' => '10 km/h', 'pressure' => '1013 hPa', 'precipitation' => '0 mm', 'condition' => 'Partly Cloudy'],
            ['date' => '03-08-2024', 'temp' => '27°C', 'humidity' => '58%', 'wind' => '12 km/h', 'pressure' => '1012 hPa', 'precipitation' => '1 mm', 'condition' => 'Rain'],
            ['date' => '04-08-2024', 'temp' => '24°C', 'humidity' => '65%', 'wind' => '18 km/h', 'pressure' => '1016 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '05-08-2024', 'temp' => '28°C', 'humidity' => '60%', 'wind' => '14 km/h', 'pressure' => '1010 hpa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '06-08-2024', 'temp' => '29°C', 'humidity' => '55%', 'wind' => '13 km/h', 'pressure' => '1009 hPa', 'precipitation' => '2 mm', 'condition' => 'Thunderstorm'],
            ['date' => '07-08-2024', 'temp' => '30°C', 'humidity' => '50%', 'wind' => '20 km/h', 'pressure' => '1011 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '08-08-2024', 'temp' => '27°C', 'humidity' => '57%', 'wind' => '10 km/h', 'pressure' => '1014 hPa', 'precipitation' => '1 mm', 'condition' => 'Partly Cloudy'],
            ['date' => '09-08-2024', 'temp' => '28°C', 'humidity' => '59%', 'wind' => '15 km/h', 'pressure' => '1013 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '10-08-2024', 'temp' => '26°C', 'humidity' => '62%', 'wind' => '12 km/h', 'pressure' => '1015 hPa', 'precipitation' => '3 mm', 'condition' => 'Rain'],
            ['date' => '11-08-2024', 'temp' => '25°C', 'humidity' => '64%', 'wind' => '11 km/h', 'pressure' => '1012 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '12-08-2024', 'temp' => '24°C', 'humidity' => '65%', 'wind' => '14 km/h', 'pressure' => '1016 hPa', 'precipitation' => '0 mm', 'condition' => 'Partly Cloudy'],
            ['date' => '13-08-2024', 'temp' => '27°C', 'humidity' => '58%', 'wind' => '16 km/h', 'pressure' => '1013 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '14-08-2024', 'temp' => '28°C', 'humidity' => '56%', 'wind' => '13 km/h', 'pressure' => '1014 hPa', 'precipitation' => '1 mm', 'condition' => 'Thunderstorm'],
            ['date' => '15-08-2024', 'temp' => '29°C', 'humidity' => '54%', 'wind' => '18 km/h', 'pressure' => '1011 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '16-08-2024', 'temp' => '30°C', 'humidity' => '52%', 'wind' => '20 km/h', 'pressure' => '1010 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '17-08-2024', 'temp' => '28°C', 'humidity' => '55%', 'wind' => '12 km/h', 'pressure' => '1013 hPa', 'precipitation' => '2 mm', 'condition' => 'Rain'],
            ['date' => '18-08-2024', 'temp' => '27°C', 'humidity' => '58%', 'wind' => '11 km/h', 'pressure' => '1014 hPa', 'precipitation' => '1 mm', 'condition' => 'Partly Cloudy'],
            ['date' => '19-08-2024', 'temp' => '26°C', 'humidity' => '61%', 'wind' => '14 km/h', 'pressure' => '1016 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '20-08-2024', 'temp' => '25°C', 'humidity' => '63%', 'wind' => '13 km/h', 'pressure' => '1012 hPa', 'precipitation' => '0 mm', 'condition' => 'Partly Cloudy'],
            ['date' => '21-08-2024', 'temp' => '24°C', 'humidity' => '65%', 'wind' => '15 km/h', 'pressure' => '1017 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '22-08-2024', 'temp' => '28°C', 'humidity' => '55%', 'wind' => '18 km/h', 'pressure' => '1014 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '23-08-2024', 'temp' => '29°C', 'humidity' => '54%', 'wind' => '20 km/h', 'pressure' => '1013 hPa', 'precipitation' => '1 mm', 'condition' => 'Thunderstorm'],
            ['date' => '24-08-2024', 'temp' => '27°C', 'humidity' => '58%', 'wind' => '12 km/h', 'pressure' => '1014 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '25-08-2024', 'temp' => '26°C', 'humidity' => '60%', 'wind' => '14 km/h', 'pressure' => '1012 hPa', 'precipitation' => '2 mm', 'condition' => 'Rain'],
            ['date' => '26-08-2024', 'temp' => '28°C', 'humidity' => '57%', 'wind' => '16 km/h', 'pressure' => '1015 hPa', 'precipitation' => '0 mm', 'condition' => 'Partly Cloudy'],
            ['date' => '27-08-2024', 'temp' => '27°C', 'humidity' => '58%', 'wind' => '13 km/h', 'pressure' => '1013 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '28-08-2024', 'temp' => '29°C', 'humidity' => '54%', 'wind' => '17 km/h', 'pressure' => '1011 hPa', 'precipitation' => '0 mm', 'condition' => 'Sunny'],
            ['date' => '29-08-2024', 'temp' => '30°C', 'humidity' => '52%', 'wind' => '18 km/h', 'pressure' => '1010 hPa', 'precipitation' => '1 mm', 'condition' => 'Thunderstorm'],

        ];

        return view('weatherForMonth', compact('weatherData'));
    }

    public function downloadPDF()
    {

        $pdf = PDF::loadView('pdf.weather', compact('weatherData'));
        return $pdf->download('weather_data.pdf');
    }
}

