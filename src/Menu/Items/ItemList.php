<?php
/**
 * ItemList
 *
 * A container for Items
 */
namespace Menu\Items;

use \Menu\HTML;
use \Menu\Menu;
use \Menu\Traits\MenuObject;
use \Menu\Items\Contents\Link;

class ItemList extends MenuObject
{
  /**
   * The name of this ItemList
   *
   * @var string
   */
  public $name;

  /**
   * The menu Items
   *
   * @var array
   */
  protected $items = array();

  /**
   * The ItemList's parent item
   *
   * @var Item
   */
  public $parentItem;

  /**
   * The default render options for this item list
   *
   * @var array
   */
  protected $options = array();

  /**
   * Prefix the links with a custom string
   *
   * @var mixed
   */
  public $prefix;

  /**
   * Prefix the links with the parent(s) ItemList name(s)
   *
   * @var boolean
   */
  public $prefixParents = false;

  /**
   * Prefix links with the name of the ItemList at the very top of the tree
   *
   * @var boolean
   */
  public $prefixHandler = false;

  /**
   * Create a new Item List instance
   *
   * @param string  $name        The ItemList's name
   * @param array   $attributes  Attributes for the ItemList's HMTL element
   * @param string  $element     The HTML element for the ItemList
   *
   * @return void
   */
  public function __construct($name = null, $attributes = array(), $element = null)
  {
    $this->name       = $name;
    $this->attributes = $attributes;
    $this->element    = $element ?: $this->getOption('item_list.element');
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// PUBLIC INTERFACE /////////////////////////
  ////////////////////////////////////////////////////////////////////

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
   * @return MenuItems
   */
  public function add($url, $value, $children = null, $linkAttributes = array(), $itemAttributes = array(), $itemElement = 'li')
  {
    $content = new Link($url, $value, $linkAttributes);

    $item = new Item($this, $content, $children);
    $item->element($itemElement);
    $item->setAttributes($itemAttributes);

    // Set Item as parent of its children
    if (!is_null($children)) {
      $children->inItem($item);
    }

    $this->items[] = $item;

    return $this;
  }

  /**
   * Add a raw html item to the MenuItems instance.
   *
   * <code>
   *    // Add a raw item to the default main menu
   *    Menu::raw('<img src="img/seperator.gif">');
   * </code>
   *
   * @param  string   $raw
   * @param  ItemList $children
   * @param  array    $attributes
   * @param  array    $children
   *
   * @return MenuItems
   */
  public function raw($raw, $children = null, $itemAttributes = array(), $itemElement = 'li')
  {
    // Create Item
    $item = new Item($this, new Raw($raw), $children);
    $item->setAttributes($itemAttributes)->element($itemElement);

    // Set Item as parent of its children
    if (!is_null($children)) {
      $children->inItem($item);
    }

    $this->items[] = $item;

    return $this;
  }

  /**
   * Add menu items to another ItemList.
   *
   * <code>
   *    // Attach menu items to the default menuhandler
   *    Menu::attach(Menu::items()->add('home', 'Homepage'));
   * </code>
   *
   * @param  MenuItems $menuitems
   * @return Void
   */
  public function attach($itemList)
  {
    foreach ($itemList->items as $item) {
      $item->list = $this;

      $this->items[] = $item;
    }

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
   * Set the parent of the ItemList
   *
   * @param Item $item
   *
   * @return ItemList
   */
  public function inItem($item)
  {
    $this->parentItem = $item;

    return $this;
  }

  /**
   * Get all of the list's items
   *
   * @return array
   */
  public function getItems()
  {
    return $this->items;
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
    $this->prefix = $prefix;

    return $this;
  }

  /**
   * Prefix this ItemList with the parent ItemList(s) name(s)
   *
   * @return ItemList
   */
  public function prefixParents()
  {
    $this->prefixParents = true;

    return $this;
  }

  /**
   * Prefix this ItemList with the name of the ItemList at the very top of the tree
   *
   * @return ItemList
   */
  public function prefixHandler()
  {
    $this->prefixHandler = true;

    return $this;
  }

  /**
   * Get the evaluated string content of the ItemList.
   *
   * @param  array $options
   *
   * @return string
   */
  public function render($depth = 0)
  {
    // Check for maximal depth
    $maxDepth = $this->getOption('max_depth');
    if ($maxDepth != 0 and $depth > $maxDepth) return false;

    // Render contained items
    $contents = null;
    foreach ($this->items as $item) {
      $contents .= $item->render($depth + 1);
    }

    $element = $this->element;
    if ($element) $content = HTML::$element($contents, $this->attributes);

    return $content;
  }

  /**
   * Find itemslists by name (itself, or on of it's children)
   *
   * @param array $names the names to find
   *
   * @return ItemList
   */
  public function find($names)
  {
    $names = (array) $names;

    $results = array();

    foreach ($names as $name) {
      if ($this->name == $name) {
        $results[] = $this;
      }

      foreach ($this->items as $item) {
        if ($item->hasChildren() && $found = $item->children->find($name)) {
          foreach ($found as $list_item) {
            $results[] = $list_item;
          }
        }
      }
    }

    return $results;
  }
}
