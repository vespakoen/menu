<?php
/**
 * MenuServiceProvider
 *
 * Register the Menu package with the Laravel framework
 */
namespace Menu;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  public function register()
  {
    Request::start();
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
}
