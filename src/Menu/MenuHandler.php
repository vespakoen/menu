<?php
namespace Menu;

use \Exception;

class MenuHandler
{
  /**
   * The names of the itemlists this handler acts on
   *
   * @var array
   */
  public $handles = array();

  /**
   * Set the names of this handler on which it should act
   *
   * @param array $names The names of the itemlists
   *
   * @return void
   */
  public function __construct($names)
  {
    $this->handles = $names;
  }

  /**
   * Magic method that will pass the incoming calls to
   * all of the itemlists this handler acts on
   *
   * @param string  $method
   * @param array   $parameters
   *
   * @return MenuHandler
   */
  public function __call($method, $parameters)
  {
    // Loop through the ItemLists this handler handles
    foreach ($this->handles as $name) {

      // Forward the call to the ItemList
      $item_list = Menu::$names[$name];
      Menu::$names[$name] = call_user_func_array(array($item_list, $method), $parameters);
    }

    return $this;
  }

  /**
   * Render all the itemlists this handler acts on and return the HTML
   *
   * @param array $options Optional render settings
   *
   * @return string
   */
  public function render($options = array())
  {
    $contents = '';
    // Loop through the ItemLists this handler handles
    foreach ($this->handles as $name) {

      // Call the render method
      $contents .= Menu::$names[$name]->render($options);
    }

    return $contents;
  }

  /**
   * Find itemslists by name in any of the itemlists this menuhandler acts on
   *
   * @param array $names the names to find
   *
   * @return MenuHandler
   */
  public function find($names)
  {
    $names = (array) $names;

    $results = array();

    // Loop through the listitems this handler handles
    foreach ($this->handles as $name) {

      // Find the menuitems
      foreach (Menu::$names[$name]->find($names) as $item_list) {
        $results[] = $item_list;
      }
    }

    $not_found_list_items = array_diff($names, array_pluck($results, 'name'));
    if ( ! empty($not_found_list_items)) {
      throw new Exception('Some list items you are trying to find do not exist ('.implode(', ', $not_found_list_items).')');
    }

    foreach ($results as $item_list) {
      Menu::$names[$item_list->name] = $item_list;
    }

    return new MenuHandler($names);
  }

  /**
   * Get the evaluated string content for the itemlists this menuhandler acts on.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->render();
  }

}