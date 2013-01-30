<?php
/**
 * MenuHandler
 *
 * Handles various instances of ItemList at once
 */
namespace Menu;

use \Exception;

class MenuHandler
{
  /**
   * The names of the itemlists this handler acts on
   *
   * @var array
   */
  protected $handles = array();

  /**
   * Set the handles on which this menu should act
   *
   * @param array $names The names of the ItemLists
   *
   * @return void
   */
  public function __construct($names)
  {
    $this->handles = $names;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// CODE METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Render all the ItemLists this handler acts on and return the HTML
   *
   * @param array $options Optional render settings
   *
   * @return string
   */
  public function render($options = array())
  {
    $contents = '';

    // Loop through the ItemLists this handler handles
    // And render each one in the content
    foreach ($this->handles as $name) {
      $contents .= Menu::getItemList($name)->render($options);
    }

    return $contents;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// PUBLIC INTERFACE ////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the handles this Menu hooks into
   *
   * @return array
   */
  public function getHandles()
  {
    return $this->handles;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// MAGIC METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

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
      $item_list = Menu::getItemList($name);
      $item_list = call_user_func_array(array($item_list, $method), $parameters);
      Menu::setItemList($name, $item_list);
    }

    return $this;
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
      foreach (Menu::getItemList($name)->find($names) as $item_list) {
        $results[] = $item_list;
      }
    }

    $not_found_list_items = array_diff($names, array_pluck($results, 'name'));
    if ( ! empty($not_found_list_items)) {
      throw new Exception('Some list items you are trying to find do not exist ('.implode(', ', $not_found_list_items).')');
    }

    foreach ($results as $item_list) {
      Menu::setItemList($item_list->name, $item_list);
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