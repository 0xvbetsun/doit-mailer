<?php
declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Gmopx\LaravelOWM\LaravelOWM;

/**
 * Class WeatherForecast
 * @package App\Mail
 */
class WeatherForecast extends Mailable
{
    use Queueable, SerializesModels;

    public $location;

    public $message;


    /**
     * Create a new message instance.
     * @param string $location
     * @param string $message
     */
    public function __construct(string $location, string $message)
    {
        $this->location = $location;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $weather = $this->getWeather();
        $weatherImage = $this->getWeatherImage($weather->description);

        return $this->markdown('emails.weather-forecast', ['weatherImage' => $weatherImage]);
    }

    /**
     * @return \Cmfcmf\OpenWeatherMap\Util\Weather
     */
    private function getWeather()
    {
        $currentLocation = $this->getCurrentLocation();

        $owm = new LaravelOWM();
        $currentWeather = $owm->getCurrentWeather($currentLocation);

        return $currentWeather->weather;
    }

    /**
     * @return array|mixed
     */
    private function getCurrentLocation()
    {
        $currentLocation = explode(' ', $this->location);

        return array_pop($currentLocation);
    }

    /**
     * @param string $weather
     * @return string
     */
    private function getWeatherImage(string $weather): string
    {
        if ($weather === 'clear sky') {
            return 'sun';
        } elseif ($weather === 'few clouds') {
            return 'cloudy_sun';
        } elseif ($weather === 'scattered clouds' || $weather === 'broken clouds') {
            return 'cloud';
        } elseif ($weather === 'shower rain' || $weather === 'rain') {
            return 'rain_cloud';
        } elseif ($weather === 'thunderstorm') {
            return 'thunder_cloud';
        } elseif ($weather === 'snow') {
            return 'snow_cloud';
        } else {
            return 'mist';
        }
    }
}
