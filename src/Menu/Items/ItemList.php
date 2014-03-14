<?php
namespace Menu\Items;

use Exception;

use HtmlObject\Element;

use Menu\Menu;
use Menu\MenuHandler;
use Menu\Items\Contents\Link;
use Menu\Items\Contents\Raw;
use Menu\Traits\MenuObject;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use Underscore\Methods\ArraysMethods;

/**
 * A container for Items
 */
class ItemList extends MenuObject
{
  /**
   * The name of this ItemList
   *
   * @var string
   */
  public $name;

  /**
   * Create a new Item List instance
   *
   * @param string  $name        The ItemList's name
   * @param array   $attributes  Attributes for the ItemList's HMTL element
   * @param string  $element     The HTML element for the ItemList
   *
   * @return void
   */
  public function __construct($items = array(), $name = null, $attributes = array(), $element = null)
  {
    $this->children   = $items;
    $this->name       = $name;
    $this->attributes = $attributes;
    $this->element  = $element;
  }

  /**
   * Get the last Item
   *
   * @return Item
   */
  public function onItem()
  {
    return $this->children[sizeof($this->children) - 1];
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// PUBLIC INTERFACE /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set a particular option in the array
   *
   * @param string $option The option
   * @param mixed  $value  Its new value
   *
   * @return MenuObject
   */
  public function setOption($option, $value)
  {
    // forward item config values to the items
    if(Str::startsWith($option, 'item.')) {
      foreach($this->children as $child) {
        $child->setOption($option, $value);
      }
    }
    elseif(Str::startsWith($option, 'item_list.')) {
      $this->options = ArraysMethods::set($this->options, $option, $value);
    }
    else
    {
      Menu::setOption($option, $value);
    }

    return $this;
  }

  /**
   * Add a link item to the ItemList instance.
   *
   * <code>
   *    // Add a item to the default menu
   *    Menu::add('home', 'Homepage');
   *
   *    // Add a item with a subitem to the default menu
   *    Menu::add('home', 'Homepage', Menu::items()->add('home/sub', 'Subitem'));
   *
   *    // Add a item with attributes for the item's HTML element
   *    Menu::add('home', 'Homepage', null, array('class' => 'fancy'));
   * </code>
   *
   * @param string   $url
   * @param string   $value
   * @param ItemList $children
   * @param array    $linkAttributes
   * @param array    $itemAttributes
   * @param string   $itemElement
   *
   * @return ItemList
   */
  public function add($url, $value, $children = null, $linkAttributes = array(), $itemAttributes = array(), $itemElement = null)
  {
    $content = new Link($url, $value, $linkAttributes);
    $item = $this->addContent($content, $children, $itemAttributes, $itemElement);

    return $this;
  }

  /**
   * Add a raw html item to the ItemList instance.
   *
   * <code>
   *    // Add a raw item to the default main menu
   *    Menu::raw('<img src="img/seperator.gif">');
   * </code>
   *
   * @param string $raw            The raw content
   * @param array $children        Possible children
   * @param array  $itemAttributes The item attributes
   * @param string $itemElement    The item element
   *
   * @return ItemList
   */
  public function raw($raw, $children = null, $itemAttributes = array(), $itemElement = null)
  {
    $content = new Raw($raw);
    $item = $this->addContent($content, $children, $itemAttributes, $itemElement);

    return $this;
  }

  /**
   * Add content to the ItemList
   *
   * @param Content $content
   * @param array   $children
   * @param array   $itemAttributes
   * @param string  $itemElement
   */
  public function addContent($content, $children = null, $itemAttributes = array(), $itemElement = null)
  {
    $item = new Item($this, $content, $children, $itemElement);
    $item->setAttributes($itemAttributes);

    // Set Item as parent of its children
    if (!is_null($children)) {
      $children->setParent($item);
    }

    $this->setChild($item);

    return $item;
  }

  /**
   * Add an active pattern to the ItemList instance.
   *
   * <code>
   *    // Add a item to the default menu and set an active class for /user/5/edit
   *    Menu::add('user', 'Users')->activePattern('\/user\/\d\/edit');
   * </code>
   *
   * @param string   $pattern
   *
   * @return ItemList
   */
  public function activePattern($pattern)
  {
    $pattern = (array) $pattern;
    $item = end($this->children);
    $item->setActivePatterns($pattern);

    return $this;
  }

  /**
   * Add menu items to another ItemList.
   *
   * <code>
   *    // Attach menu items to the default MenuHandler
   *    Menu::attach(Menu::items()->add('home', 'Homepage'));
   * </code>
   *
   * @param  ItemList $itemList
   *
   * @return ItemList
   */
  public function attach(ItemList $itemList)
  {
    $this->nestChildren($itemList->getChildren());

    return $this;
  }

  /**
   * Set the name for this ItemList
   *
   * @param string  $name
   *
   * @return ItemList
   */
  public function name($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get the name of the ItemList
   *
   * @return string Name of the ItemList
   */
  public function getName()
  {
    return $this->name;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// PREFIXES /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Prefix this ItemList with a string
   *
   * @param string $prefix
   *
   * @return ItemList
   */
  public function prefix($prefix)
  {
    $this->setOption('item_list.prefix', $prefix);

    return $this;
  }

  /**
   * Prefix this ItemList with the parent ItemList(s) name(s)
   *
   * @param boolean $prefixParents
   *
   * @return ItemList
   */
  public function prefixParents($prefixParents = true)
  {
    $this->setOption('item_list.prefix_parents', $prefixParents);

    return $this;
  }

  /**
   * Prefix this ItemList with the name of the ItemList at the very top of the tree
   *
   * @param boolean $prefixMenuHandler
   *
   * @return ItemList
   */
  public function prefixMenuHandler($prefixMenuHandler = true)
  {
    $this->setOption('item_list.prefix_handler', $prefixMenuHandler);

    return $this;
  }

  /**
   * Set the Item's element
   *
   * @param string $element
   */
  public function setElement($element = null)
  {
    $this->setOption('item_list.element', $element);

    return $this;
  }

  /**
   * Get the Item's element
   *
   * @return string
   */
  public function getElement()
  {
    $element = $this->getOption('item_list.element');
    if( ! is_null($this->element))
    {
      $element = $this->element;
    }

    return $element;
  }

  /**
   * Get all items with the depth as key
   *
   * @return array
   */
  public function getItemsWithDepth()
  {
    return $this->getItemsRecursivelyWithDepth($this->getChildren());
  }

  /**
   * Get all items for an array of items recursively for a specific depth
   *
   * @return array
   */
  protected function getItemsRecursivelyWithDepth($items, $depth = 0)
  {
    $results = array();
    foreach($items as $item)
    {
      $results[$depth][] = $item;

      $subItems = $item->getChildren()
        ->getChildren();
      foreach($this->getItemsRecursivelyWithDepth($subItems, $depth + 1) as $childrenDepth => $children)
      {
        foreach($children as $child)
        {
          $results[$childrenDepth][] = $child;
        }
      }
    }

    return $results;
  }

  /**
   * Get all itemlists with the depth as key
   *
   * @return array
   */
  public function getItemListsWithDepth()
  {
    return $this->getItemListsRecursivelyWithDepth($this);
  }

  /**
   * Get all itemlists for an itemlsit recursively for a specific depth
   *
   * @return array
   */
  protected function getItemListsRecursivelyWithDepth($itemList, $depth = 0)
  {
    $results = array();

    $results[$depth][] = $itemList;

    $items = $itemList->getChildren();
    foreach($items as $item)
    {
      foreach($this->getItemListsRecursivelyWithDepth($item->getChildren(), $depth + 1) as $childrenDepth => $children)
      {
        foreach($children as $child)
        {
          $results[$childrenDepth][] = $child;
        }
      }
    }

    return $results;
  }

  /**
   * Get all items
   *
   * @return \Vespakoen\Menu\MenuHandler
   */
  public function getAllItems()
  {
    $results = array();

    foreach($this->getItemsWithDepth() as $depth => $items)
    {
      foreach($items as $item)
      {
        $results[] = $item;
      }
    }

    return new MenuHandler($results);
  }

  /**
   * Get items by their content type
   *
   * @param  string $contentType The full object name
   *
   * @return \VEspakoen\Menu\MenuHandler
   */
  public function getItemsByContentType($contentType)
  {
    $results = array();

    $itemList = $this->getAllItems();
    foreach($itemList->getMenuObjects() as $item)
    {
      $content = $item->getContent();
      if(get_class($content) == $contentType)
      {
        $results[] = $item;
      }
    }

    return new MenuHandler($results);
  }

  /**
   * Get all itemlists
   *
   * @return \Vespakoen\Menu\MenuHandler
   */
  public function getAllItemLists()
  {
    $results = array();

    foreach($this->getItemListsWithDepth() as $depth => $items)
    {
      foreach($items as $item)
      {
        $results[] = $item;
      }
    }

    return new MenuHandler($results);
  }

  /**
   * Get all itemslists including this one
   *
   * @return \Vespakoen\Menu\MenuHandler
   */
  public function getAllItemListsIncludingThisOne()
  {
    return $this->getAllItemLists()
      ->addMenuObject($this);
  }

  /**
   * Get itemlists at a certain depth
   *
   * @return \Vespakoen\Menu\MenuHandler
   */
  public function getItemListsAtDepth($depth)
  {
    $itemListsWithDepth = $this->getItemListsWithDepth();

    return new MenuHandler($itemListsWithDepth[$depth]);
  }

  /**
   * Get itemlists in a range of depths
   *
   * @return \Vespakoen\Menu\MenuHandler
   */
  public function getItemListsAtDepthRange($from, $to)
  {
    $itemListsWithDepth = $this->getItemListsWithDepth();

    $results = array();
    foreach($itemListsWithDepth as $depth => $itemLists)
    {
      if($depth >= $from && $depth <= $to)
      {
        foreach($itemLists as $itemList)
        {
          $results[] = $itemList;
        }
      }
    }

    return new MenuHandler($results);
  }

  /**
   * Get all items at a certain depth
   *
   * @return \Vespakoen\Menu\MenuHandler
   */
  public function getItemsAtDepth($depth)
  {
    $itemsWithDepth = $this->getItemsWithDepth();

    return new MenuHandler($itemsWithDepth[$depth]);
  }

  /**
   * Get items in a range of depths
   *
   * @return \Vespakoen\Menu\MenuHandler
   */
  public function getItemsAtDepthRange($from, $to)
  {
    $itemsWithDepth = $this->getItemsWithDepth();

    $results = array();
    foreach($itemsWithDepth as $depth => $items)
    {
      if($depth >= $from && $depth <= $to)
      {
        foreach($items as $item)
        {
          $results[] = $item;
        }
      }
    }

    return new MenuHandler($results);
  }

  public function reverse()
  {
    $this->children = array_reverse($this->children);

    return $this;
  }

  public function findActiveItem()
  {
    $items = $this->getAllItems()
      ->getMenuObjects();

    // Find the active one
    foreach($items as $item) {
      if($item->isActive()) {
        return $item;
      }
    }

    return null;
  }

  public function getSubmenu()
  {
    if($activeItem = $this->findActiveItem())
    {
      return $activeItem->getChildren();
    }

    return new ItemList;
  }

  public function breadcrumbs()
  {
    // Collect all items
    $activeItem = $this->findActiveItem();

    $separator  = $this->getOption('item_list.breadcrumb_separator');

    // Make the breadcrumbs
    $itemList = new ItemList(array(), 'breadcrumbs');

    // Fill her up if we found the active link
    if( ! is_null($activeItem)) {
      // Add the found item
      $itemList->addContent($activeItem->getContent());
      // Loop throught the parents until we hit the root
      while($nextItem = $activeItem->getParent()) {
        if(is_null($nextItem->getParent())) break;

        // Add a separator and the link
        if ( ! empty($separator))
        {
          $itemList->raw($separator);
        }

        $itemList->addContent($nextItem->getParent()->getContent());

        // Set the activeItem for the next iteration
        $activeItem = $nextItem->getParent();
      }
    }

    // Correct order
    $itemList->reverse();

    return $itemList;
  }

  public function map($callback)
  {
    array_map($callback, $this->children);

    return $this;
  }

  /**
   * Find an itemlist by it's name
   *
   * @return \Vespakoen\Menu\Items\ItemLists|false
   */
  public function findItemListByName($name)
  {
    $itemLists = $this->getAllItemListsIncludingThisOne()
      ->getMenuObjects();
    foreach($itemLists as $itemList)
    {
      if($itemList->getName() == $name)
      {
        return $itemList;
      }
    }

    return false;
  }

  /**
   * Find an itemlist by it's name
   *
   * alias for findItemListByName
   *
   * @return \Vespakoen\Menu\Items\ItemLists|false
   */
  public function findByName($name)
  {
    return $this->findItemListByName($name);
  }

  /**
   * Find an itemlist by it's name
   *
   * alias for findItemListByName
   *
   * @return \Vespakoen\Menu\Items\ItemLists|false
   */
  public function find($name)
  {
    return $this->findItemListByName($name);
  }

  /**
   * Find an item by an attribute
   *
   * @return \Vespakoen\Menu\Items\Item|false
   */
  public function findItemByAttribute($key, $value)
  {
    $itemList = $this->getAllItemListsIncludingThisOne()
      ->getMenuObjects();

    foreach($itemLists as $itemList)
    {
      if($itemList->getAttibute($key) == $value)
      {
        return $itemList;
      }
    }

    return false;
  }

  /**
   * Find an item by it's link's URL
   *
   * @return \Vespakoen\Menu\Items\Item|false
   */
  public function findItemByUrl($url)
  {
    $itemList = $this->getItemsByContentType('Menu\Items\Contents\Link');
    foreach($itemList->getChildren() as $item)
    {
      $content = $item->getContent();
      if($content->getUrl() == $url)
      {
        return $item;
      }
    }

    return false;
  }

  /**
   * Easily create items while looping over DB results
   * that have a reference to the parent (usually via parentId)
   *
   * <code>
   *     Menu::hydrate(function($parentId)
   *       {
   *         return Page::where('parent_id', $parentId)
   *           ->get();
   *       },
   *       function($children, $page)
   *       {
   *         $children->add($page->slug, $page->name);
   *       });
   * </code>
   *
   * @param Closure $resolver   the callback to resolve results for a given parentId
   * @param Closure $decorator  the callback that modifies the ItemList for the given node
   * @param integer $idField    the id column that matches with the parentId
   * @param integer $parentId   the parentId to start hydrating from
   *
   * @return ItemList the
   */
  public function hydrate($resolver, $decorator, $idField = 'id', $parentIdField = 'parent_id', $parentId = 0)
  {
    $items = is_callable($resolver) ? $resolver() : $resolver;

    if($items instanceof Collection)
    {
      $items = $items->all();
    }

    $itemsForThisLevel = array_filter($items, function($item) use ($parentId, $parentIdField)
    {
      return $parentId == (is_object($item) ? (isset($item->$parentIdField) ? $item->$parentIdField : 0) : (isset($item[$parentIdField]) ? $item[$parentIdField] : 0));
    });

    foreach($itemsForThisLevel as $item)
    {
      // Let the decorator add the item(s) (and maybe set some attributes)
      $decorator($this, $item);

      // Grab the newest item
      $newestItem = end($this->children);

      // If there is an item, add hydrate it
      if($newestItem)
      {
        // Grab the newest itemlist
        $newestItemList = $newestItem->getChildren();

        // Get the id of the item
        $parentId = is_object($item) ? $item->$idField : $item[$idField];

        // Hydrate the children
        $newestItemList->hydrate($items, $decorator, $idField, $parentIdField, $parentId);
      }
    }

    return $this;
  }

  /**
   * Get the evaluated string content of the ItemList.
   *
   * @param  integer $depth The depth at which the ItemList should be rendered
   *
   * @return string
   */
  public function render($depth = 0)
  {
    if( ! is_int($depth))
    {
      throw new Exception("The render method doesn't take any arguments anymore, you can now configure your menu via the config file.");
    }

    // Check for maximal depth
    $maxDepth = $this->getOption('max_depth');
    if ($maxDepth !== -1 and $depth > $maxDepth) return false;

    // Render contained items
    $contents = null;
    if(count($this->children) == 0)
    {
      return "";
    }

    foreach ($this->children as $item) {
      $contents .= $item->render($depth + 1);
    }

    $element = $this->getElement();
    if ($element) $contents = Element::create($element, $contents, $this->attributes)->render();
    return $contents;
  }
}
