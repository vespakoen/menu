<?php
namespace Menu;

use Illuminate\Support\ServiceProvider;

/**
 * The "start" file for laravel
 */
class MenuServiceProvider extends ServiceProvider
{

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $configPath = __DIR__ . '/../config/config.php';
    $this->mergeConfigFrom($configPath, 'menu');

    $container = Menu::getContainer();
    $container['url'] = $this->app['url'];
    $container['config'] = $this->app['config'];
    Menu::setContainer($container);
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array('menu');
  }

  /**
   * Declare publishable assets
   *
   * @return void
   */
  public function boot()
  {
        $configPath = __DIR__ . '/../config/config.php';
        $this->publishes([$configPath => config_path('menu.php')], 'config');
  }
}
