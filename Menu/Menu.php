<?php
namespace Menu;

use \Laravel\HTML;

class Menu
{
  /**
   * All the registered names and the associated itemlists
   *
   * @var array
   */
  public static $names = array();

  /**
   * Create a new ItemList
   *
   * @param string $name       The name of the ItemList
   * @param array  $attributes The HTML attributes for the list element
   * @param string $element    The HTML element for the list (ul or dd)
   *
   * @return ItemList
   */
  public static function items($name = null, $attributes = array(), $element = 'ul')
  {
    return new ItemList($name, $attributes, $element);
  }

  /**
   * Get a MenuHandler.
   *
   * This method will retrieve itemlists by name,
   * If an ItemList doesn't already exist, it will
   * be registered and added to the handler.
   *
   * <code>
   *    // Get the menu handler that handles the default name
   *    $handler = Menu::handler();
   *
   *    // Get a named menu handler for a single name
   *    $handler = Menu::handler('backend');
   *
   *    // Get a menu handler that handles multiple names
   *    $handler = Menu::handler(array('admin', 'sales'));
   * </code>
   *
   * @param string $name
   *
   * @return MenuHandler
   */
  public static function handler($names = '', $attributes = array(), $element = 'ul')
  {
    $names = (array) $names;

    // Create a new Items instance for the names that don't exist yet
    foreach ($names as $name) {
      if ( ! array_key_exists($name, static::$names)) {
        static::$names[$name] = new ItemList($name, $attributes, $element);
      }
    }

    // Return a Handler for the given names
    return new MenuHandler($names);
  }

  /**
   * Get a MenuHandler for all registered itemlists
   *
   * @return MenuHandler
   */
  public static function all()
  {
    return new MenuHandler(array_keys(static::$names));
  }

  /**
   * Magic Method for calling methods on the default handler.
   *
   * <code>
   *    // Call the "render" method on the default handler
   *    echo Menu::render();
   *
   *    // Call the "add" method on the default handler
   *    Menu::add('home', 'Home');
   * </code>
   */
  public static function __callStatic($method, $parameters)
  {
    return call_user_func_array(array(static::handler(), $method), $parameters);
  }

}
