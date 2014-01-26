<?php
namespace Menu;

use Menu\Items\ItemList;
use Illuminate\Support\Str;
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
    'set',
    'wrap'
  );

  public static $responses = array(
    'getHandlerFromResults' => array(
      'getAllItems',
      'getItemsAtDepth',
      'getItemsAtDepthRange',
      'getItemsByContentType',
      'getAllItemLists',
      'getSubmenu',
      'getItemListsAtDepth',
      'getItemListsAtDepthRange',
      'onItem',
      'getContent',
      'stop',
      'filter'
    ),
    'getMatchFromResults' => array(
      'findItemListByName',
      'findActiveItem',
      'findByName',
      'findItemByUrl',
      'find'
    ),
    'getCombinedResults' => array(
      'lists'
    ),
    'getCombindedResultsByKey' => array(
      'getItemsWithDepth',
      'getItemListsWithDepth'
    ),
    'getImplodedResults' => array(
      'render'
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

  public function map($callback)
  {
    array_map($callback, $this->getMenuObjects());
  }

  public function breadcrumbs($choosePath = null)
  {
    if(is_null($choosePath)) {
      $choosePath = function($itemLists) {
        return $itemLists[0];
      };
    }

    $menuObjects = array();
    foreach (Menu::allHandlers()->getMenuObjects() as $itemList) {
      $breadcrumbs = $itemList->breadcrumbs();
      if($breadcrumbs->hasChildren()) {
        $menuObjects[] = $breadcrumbs;
      }
    }

    if(count($menuObjects) > 1)
    {
      return $choosePath($menuObjects);
    }

    return isset($menuObjects[0]) ? $menuObjects[0] : new MenuHandler;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// RESPONDERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  protected function getHandlerFromResults($menuHandlers)
  {
    if(is_array($menuHandlers) && count($menuHandlers) > 0 && ! $menuHandlers[0] instanceof MenuHandler) {
      foreach ($menuHandlers as &$menuHandler) {
        $menuHandler = new MenuHandler(array($menuHandler));
      }
    }

    $menuObjects = $this->getMenuObjectsFromHandlers($menuHandlers);

    return new MenuHandler($menuObjects);
  }

  protected function getMatchFromResults($results)
  {
    foreach($results as $result)
    {
      if($result !== false)
      {
        return $result;
      }
    }

    return false;
  }

  protected function getCombinedResults($results)
  {
    return call_user_func_array('array_merge', $results);
  }

  protected function getCombindedResultsByKey($results)
  {
    $combinedResults = array();
    foreach ($results as $result)
    {
      foreach($result as $key => $items)
      {
        foreach($items as $item)
        {
          $combinedResults[$key][] = $item;
        }
      }
    }

    return $combinedResults;
  }

  protected function getImplodedResults($results)
  {
    return implode('', $results);
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// RESULT EXTRACTORS ////////////////////////
  ////////////////////////////////////////////////////////////////////

  protected function getMenuObjectsFromHandlers($menuHandlers)
  {
    $results = array();
    foreach($menuHandlers as $menuHandler)
    {
      foreach($menuHandler->getMenuObjects() as $item)
      {
        $results[] = $item;
      }
    }

    return $results;
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

      if (Str::startsWith($method, static::$override)) {
        $menuObject = $result;
      }

      $results[] = $result;
    }

    foreach (static::$responses as $responseMethod => $methods) {
      if(in_array($method, $methods)) {
        return $this->$responseMethod($results);
      }
    }

    return $this;
  }

  /**
   * Render the MenuHandler
   *
   * @return string
   */
  public function __toString()
  {
    return $this->render();
  }

}
