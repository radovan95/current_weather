<?php

namespace Drupal\current_weather\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\current_weather\Service\WeatherService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a 'Current Weather' block.
 *
 * @Block(
 *   id = "current_weather",
 *   admin_label = @Translation("Current Weather"),
 *   category = @Translation("Weather"),
 * )
 */
class CurrentWeatherBlock extends BlockBase implements ContainerFactoryPluginInterface {

  const DEFAULT_API_KEY = '26d4b2455e0fa3fb05c7baf417e1075d';
  const DEFAULT_LATITUDE = '46.1009';
  const DEFAULT_LONGITUDE = '19.6676';

  /**
   * The weather service.
   *
   * @var \Drupal\current_weather\Service\WeatherService
   */
  protected $weatherService;

  /**
   * Constructs a new CustomWeatherBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the block.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\current_weather\Service\WeatherService $weatherService
   *   The weather service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, WeatherService $weatherService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->weatherService = $weatherService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_weather.weather_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $weather_data = $this->weatherService->getWeatherData(
      $this->getConfigurationValue('api_key', self::DEFAULT_API_KEY),
      $this->getConfigurationValue('latitude', self::DEFAULT_LATITUDE),
      $this->getConfigurationValue('longitude', self::DEFAULT_LONGITUDE)
    );
    return [
      '#theme' => 'current_weather',
      '#temperature' => $weather_data['temperature'],
      '#icon' => $weather_data['icon'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $this->configuration['api_key'] ?? self::DEFAULT_API_KEY,
      '#description' => $this->t('Enter the API key for the weather service.'),
    ];

    $form['latitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#default_value' => $this->configuration['latitude'] ?? self::DEFAULT_LATITUDE,
      '#description' => $this->t('Enter the latitude for weather data.'),
    ];

    $form['longitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#default_value' => $this->configuration['longitude'] ?? self::DEFAULT_LONGITUDE,
      '#description' => $this->t('Enter the longitude for weather data.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['api_key'] = $form_state->getValue('api_key');
    $this->configuration['latitude'] = $form_state->getValue('latitude');
    $this->configuration['longitude'] = $form_state->getValue('longitude');
  }

  /**
   * Helper method to get configuration value.
   */
  protected function getConfigurationValue(string $name, string $default): string {
    return $this->configuration[$name] ?? $default;
  }
}
