<?php

namespace Drupal\current_weather\Service;

use GuzzleHttp\Client;

/**
 * Class WeatherService.
 */
class WeatherService {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * Constructs a new WeatherService object.
   */
  public function __construct() {
    $this->client = new Client();
  }

  /**
   * Retrieves weather data from OpenWeatherMap API.
   *
   * @param string $api_key
   *   The API key.
   * @param string $lat
   *   The latitude.
   * @param string $lon
   *   The longitude.
   *
   * @return array
   *   Weather data including temperature and icon.
   */
  public function getWeatherData($api_key, $lat, $lon) {
    $response = $this->client->request('GET', "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$api_key}&units=metric");
    $data = json_decode($response->getBody(), TRUE);
    $icon = $data['weather'][0]['icon'];
    $icon_url = "http://openweathermap.org/img/w/{$icon}.png";

    return [
      'temperature' => $data['main']['temp'],
      'icon' => $icon_url,
    ];
  }
}
