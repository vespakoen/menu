<?php
namespace Menu;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Menu\Items\ItemList;

/**
 * Basic interface between the package and the user
 * Redirects and dispatch calls to the different menus in memory
 */
class Menu
{
  /**
   * The current IoC container
   * @var Container
   */
  private static $container;

  /**
   * All the registered names and the associated ItemLists
   *
   * @var array
   */
  protected static $names = array();

  /**
   * Get a MenuHandler.
   *
   * This method will retrieve ItemLists by name,
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
   * @param string|array $names      The name this handler should respond to
   * @param array        $attributes Its attributes
   * @param string       $element    Its element
   *
   * @return MenuHandler
   */
  public static function handler($names = '', $attributes = array(), $element = 'ul')
  {
    $names = (array) $names;

    // Create a new Items instance for the names that don't exist yet
    foreach ($names as $name) {
      if ( ! array_key_exists($name, static::$names)) {
        $itemList = new ItemList($element, $name, $attributes);
        static::setItemList($name, $itemList);
      }
    }

    // Return a Handler for the given names
    return new MenuHandler($names);
  }

  /**
   * Get a MenuHandler for all registered ItemLists
   *
   * @return MenuHandler
   */
  public static function allHandlers()
  {
    $handles = array_keys(static::$names);

    return new MenuHandler($handles);
  }

  /**
   * Erase all menus in memory
   */
  public static function reset()
  {
    static::$names = array();
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////// ITEM LISTS MANAGING ///////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create a new ItemList
   *
   * @param string $element    The HTML element for the list (ul or dd)
   * @param string $name       The name of the ItemList
   * @param array  $attributes The HTML attributes for the list element
   *
   * @return ItemList
   */
  public static function items($name = null, $attributes = array(), $element = 'ul')
  {
    return new ItemList($element, $name, $attributes);
  }

  /**
   * Store an ItemList in memory
   *
   * @param  string   $name     The handle to store it to
   * @param  ItemList $itemList
   *
   * @return ItemList
   */
  public static function setItemList($name, $itemList)
  {
    static::$names[$name] = $itemList;

    return $itemList;
  }

  /**
   * Get an ItemList from the memory
   *
   * @param string $name The ItemList handle
   *
   * @return ItemList
   */
  public static function getItemList($name = null)
  {
    if (!$name) return static::$names;

    return static::$names[$name];
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// MAGIC METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

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
   *
   * @param string $method
   * @param array  $parameters
   *
   * @return mixed
   */
  public static function __callStatic($method, $parameters)
  {
    return call_user_func_array(array(static::handler(), $method), $parameters);
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////// DEPENDENCY INJECTIONS //////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the current dependencies
   *
   * @param string $dependency A dependency to make on the fly
   *
   * @return Container
   */
  public static function getContainer($dependency = null)
  {
    if (!static::$container) static::$container = new Container;

    // Create basic Request instance to use
    static::$container->bindIf('Symfony\Component\HttpFoundation\Request', function() {
      return Request::createFromGlobals();
    });

    // Create basic Config handler
    static::$container->bind('files', 'Illuminate\Filesystem\Filesystem');
    static::$container->singleton('config', function($container) {
      $fileLoader = new \Illuminate\Config\FileLoader($container['files'], __DIR__.'/../');

      return new \Illuminate\Config\Repository($fileLoader, 'config');
    });

    // Shortcut for getting a dependency
    if ($dependency) {
      return static::$container->make($dependency);
    }

    return static::$container;
  }

  /**
   * Get an option from the options array
   *
   * @param string $option The option key
   *
   * @return mixed Its value
   */
  public static function getOption($option = null)
  {
    if ($option) $option = '.'.$option;
    return static::getContainer('config')->get('config'.$option);
  }

  /**
   * Set a global option
   *
   * @param key   $option The option
   * @param mixed $value  Its value
   */
  public static function setOption($option, $value)
  {
    static::getContainer('config')->set('config.'.$option, $value);
  }

}
