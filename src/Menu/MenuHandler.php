<?php
namespace Menu;

use Exception;

/**
 * Handles various instances of ItemList at once
 */
class MenuHandler
{

  /**
   * The ItemList or Item instances this handler acts on
   *
   * @var array
   */
  protected $menuObjects = array();

  public static $override = array(
    'add',
    'addRaw',
    'addCustom',
    'addItem',
    'setName',
    'setItems',
    'addClass',
  );

  public static $responses = array(
    'getHandlerFromResults' => array(
      'getAllItemLists',
      'getItemListsAtDepth',
      'getItemListsAtDepthRange',
      'filter'
    ),
    'getItemListFromResults' => array(
      'getAllItems',
      'getItemsAtDepth',
      'getItemsAtDepthRange',
    ),
    'getMatchFromResults' => array(
      'findItemListByName',
      'findByName',
      'findItemByUrl',
      'find'
    ),
    'getCombinedResult' => array(
      'lists'
    )
  );

  /**
   * Set the menuobjects on which this menu should act
   *
   * @param array $menuObjects The menuobjects
   *
   * @return void
   */
  public function __construct($menuObjects = array())
  {
    $this->menuObjects = $menuObjects;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// PUBLIC INTERFACE ////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function setMenuObjects($menuObjects)
  {
    $this->menuObjects = $menuObjects;

    return $this;
  }

  public function getMenuObjects()
  {
    return $this->menuObjects;
  }

  public function addMenuObject($menuObject)
  {
    $this->menuObjects[] = $menuObject;

    return $this;
  }

  public function getItemsWithDepth()
  {
    $this->__call('getItemsWithDepth');

    $results = array();
    foreach ($this->lastResults as $result)
    {
      foreach($result as $depth => $items)
      {
        foreach($items as $item)
        {
          $results[$depth][] = $item;
        }
      }
    }

    return $results;
  }

  public function getItemListsWithDepth()
  {
    $this->__call('getItemListsWithDepth');

    $results = array();
    foreach ($this->lastResults as $result)
    {
      foreach($result as $depth => $items)
      {
        foreach($items as $item)
        {
          $results[$depth][] = $item;
        }
      }
    }

    return $results;
  }

  public function render()
  {
    $this->__call('render');

    return implode('', $this->lastResults);
  }

  protected function getMenuObjectsFromHandlers()
  {
    $results = array();
    foreach($this->lastResults as $result)
    {
      foreach($result->getMenuObjects() as $item)
      {
        $results[] = $item;
      }
    }

    return $results;
  }

  protected function getItemsFromItemLists()
  {
    $results = array();
    foreach($this->lastResults as $result)
    {
      foreach($result->getItems() as $item)
      {
        $results[] = $item;
      }
    }

    return $results;
  }

  protected function getMatchFromResults()
  {
    foreach($this->lastResults as $result)
    {
      if($result !== false)
      {
        return $result;
      }
    }

    return false;
  }

  public function getCombinedResult()
  {
    return call_user_func_array('array_merge', $this->lastResults);
  }

  protected function getHandlerFromResults()
  {
    return new MenuHandler($this->getMenuObjectsFromHandlers());
  }

  protected function getItemListFromResults()
  {
    return new ItemList($this->getItemsFromItemLists());
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// MAGIC METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Magic method that will pass the incoming calls to
   * all of the ItemLists this handler acts on
   *
   * @param string  $method
   * @param array   $parameters
   *
   * @return MenuHandler
   */
  public function __call($method, $parameters = array())
  {
    $results = array();
    foreach ($this->menuObjects as &$menuObject) {
      $result = call_user_func_array(array($menuObject, $method), $parameters);

      if (in_array($method, static::$override)) {
        $menuObject = $result;
      }

      $results[] = $result;
    }

    $this->lastResults = $results;

    foreach (static::$responses as $responseMethod => $methods) {
      if(in_array($method, $methods)) {
        return $this->$responseMethod();
      }
    }

    return $this;
  }

  /**
   * Find itemslists by name in any of the ItemLists this menuhandler acts on
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
      foreach (Menu::getItemList($name)->find($names) as $itemList) {
        $results[] = $itemList;
      }
    }

    $not_found_list_items = array_diff($names, array_pluck($results, 'name'));
    if ( ! empty($not_found_list_items)) {
      throw new Exception('Some list items you are trying to find do not exist ('.implode(', ', $not_found_list_items).')');
    }

    foreach ($results as $itemList) {
      Menu::setItemList($itemList->name, $itemList);
    }

    return new MenuHandler($names);
  }

}
