<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenWeatherMapService;
use \PDF;
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
        $iconUrl = $this->iconsWeather($weatherData);
        $forDayTemp = $this->forDayTemp($weatherDataForHour);
        $today = $this->today($weatherData);
        return view('weather', compact('iconUrl', 'message','weatherData', 'today', 'weatherDataForHour', 'forDayTemp'));
    }

    public function today($weatherData)
    {
        if (is_null($weatherData)) {
            return ['date' => '', 'time' => '', 'sunset' => '', 'sunrise' => '', 'utc' => ''];
        }

        //---------------------------------------------------------------------
        $dt = $weatherData['dt'];
        $timezone_offset = $weatherData['timezone'];
        $datetime = $dt + $timezone_offset;
        $sunrise =date('H:i', $weatherData['sys']['sunrise']+$timezone_offset);
        $sunset = date('H:i', $weatherData['sys']['sunset']+$timezone_offset);
        $today = date('d.m.Y', $datetime);
        $time_now = date('H:i', $datetime);
        $utc = $this->utc($timezone_offset);
        return ['date' => $today, 'time' => $time_now, 'sunset' => $sunset, 'sunrise' => $sunrise, 'utc' => $utc];
    }
    public function utc($timezone_offset)
    {
        $timezone_offset_hours = $timezone_offset / 3600;
        $absolute_offset_hours = abs($timezone_offset_hours);
        $sign = ($timezone_offset < 0) ? '-' : '+';
        $timezone_string = sprintf("UTC%s%02d:%02d", $sign, floor($absolute_offset_hours), ($absolute_offset_hours - floor($absolute_offset_hours)) * 60);
        return $timezone_string;
    }
    public function forDayTemp($weatherDataForHour)
    {
        if (is_null($weatherDataForHour)) {
            return 0;
        }
        $resultDayTemp = []; // Массив для хранения температуры за каждый день

        foreach ($weatherDataForHour['list'] as $day) {
            $dateTime = $day['dt_txt'];
            $date = substr($dateTime, 0, 10); // Извлекаем только дату
                if (!isset($resultDayTemp[$date])) {
                    $resultDayTemp[$date] = $day['main']['temp_max'];
                }
            }

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

    public function view()
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
        $pdf = PDF::loadView('weather-for-month', compact('weatherData'));
        return $pdf->download('weather_data.pdf');
    }

    public function iconsWeather($weatherData)
    {
        if (is_null($weatherData)) {
            return 0;
        }
        // Access the icon code from the weather data
        $iconCode = $weatherData['weather'][0]['icon'];

        // Determine the URL for the weather icon based on the code
        switch ($iconCode) {
            case '01d': // Clear sky (day)
                return 'https://openweathermap.org/img/wn/01d@2x.png';
            case '01n': // Clear sky (night)
                return 'https://openweathermap.org/img/wn/01n@2x.png';
            case '02d': // Few clouds (day)
                return 'https://openweathermap.org/img/wn/02d@2x.png';
            case '02n': // Few clouds (night)
                return 'https://openweathermap.org/img/wn/02n@2x.png';
            case '03d': // Scattered clouds (day)
            case '03n': // Scattered clouds (night)
                return 'https://openweathermap.org/img/wn/03d@2x.png';
            case '04d': // Broken clouds (day)
            case '04n': // Broken clouds (night)
                return 'https://openweathermap.org/img/wn/04d@2x.png';
            case '09d': // Shower rain (day)
            case '09n': // Shower rain (night)
                return 'https://openweathermap.org/img/wn/09d@2x.png';
            case '10d': // Rain (day)
            case '10n': // Rain (night)
                return 'https://openweathermap.org/img/wn/10d@2x.png';
            case '11d': // Thunderstorm (day)
            case '11n': // Thunderstorm (night)
                return 'https://openweathermap.org/img/wn/11d@2x.png';
            case '13d': // Snow (day)
            case '13n': // Snow (night)
                return 'https://openweathermap.org/img/wn/13d@2x.png';
            case '50d': // Mist (day)
            case '50n': // Mist (night)
                return 'https://openweathermap.org/img/wn/50d@2x.png';
            default:
                return 'https://openweathermap.org/img/wn/01d@2x.png'; // Default icon for unexpected cases
        }
    }


}

