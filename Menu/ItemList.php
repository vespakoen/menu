<?php
class ItemList {

  /**
   * The name of this itemlist
   *
   * @var string
   */
  public $name;

  /**
   * The menu items
   *
   * @var array
   */
  public $items = array();

  /**
   * The itemlist's parent item
   *
   * @var Item
   */
  public $item;

  /**
   * Prefix the links with a custom string
   *
   * @var mixed
   */
  public $prefix;

  /**
   * The default render options for this item list
   *
   * @var array
   */
  public $options = array();

  /**
   * Prefix the links with the parent(s) itemlist name(s)
   *
   * @var boolean
   */
  public $prefix_parents = false;

  /**
   * Prefix links with the name of the itemlist at the very top of the tree
   *
   * @var boolean
   */
  public $prefix_handler = false;

  /**
   * Create a new Item List instance
   *
   * @param string  $name         The itemlist's name
   * @param array   $list_attributes  Attributes for the itemlist's HMTL element
   * @param string  $list_element   The HTML element for the itemlist
   *
   * @return void
   */
  public function __construct($name = null, $list_attributes = array(), $list_element = 'ul')
  {
    $this->name = $name;
    $this->options = compact(
      'list_attributes',
      'list_element'
    );
  }

  /**
   * Add a link item to the itemlist instance.
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
   * @param string  $url
   * @param string  $title
   * @param ItemList  $children
   * @param array   $link_attributes
   * @param array   $item_attributes
   * @param string  $item_element
   *
   * @return MenuItems
   */
  public function add($url, $title, $children = null, $link_attributes = array(), $item_attributes = array(), $item_element = 'li')
  {
    $options = compact('link_attributes', 'item_attributes', 'item_element');

    $item = new Item($this, 'link', $title, $children, $options, $url);

    if( ! is_null($children))
    {
      $children->item = $item;
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
   * @param  string $html
   * @param  ItemList $children
   * @param  array  $attributes
   * @param  array  $children
   *
   * @return MenuItems
   */
  public function raw($html, $children = null, $item_attributes = array(), $item_element = 'li')
  {
    $options = compact('item_attributes', 'item_element');

    $item = new Item($this, 'raw', $html, $children, $options);

    if( ! is_null($children))
    {
      $children->item = $item;
    }

    $this->items[] = $item;

    return $this;
  }

  /**
   * Prefix this itemlist with a string
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
   * Prefix this itemlist with the parent itemlist(s) name(s)
   *
   * @return ItemList
   */
  public function prefix_parents()
  {
    $this->prefix_parents = true;

    return $this;
  }

  /**
   * Prefix this itemlist with the name of the itemlist at the very top of the tree
   *
   * @return ItemList
   */
  public function prefix_handler()
  {
    $this->prefix_handler = true;

    return $this;
  }

  /**
   * Add menu items to another itemlist.
   *
   * <code>
   *    // Attach menu items to the default menuhandler
   *    Menu::attach(Menu::items()->add('home', 'Homepage'));
   * </code>
   *
   * @param  MenuItems  $menuitems
   * @return Void
   */
  public function attach($item_list)
  {
    foreach ($item_list->items as $item)
    {
      $item->list = $this;

      $this->items[] = $item;
    }

    return $this;
  }

  /**
   * Set the name for this itemlist
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
   * Get the evaluated string content of the itemlist.
   *
   * @param  array      $options
   *
   * @return string
   */
  public function render($options = array())
  {
    $options = array_replace_recursive($this->options, $options);

    if( ! array_key_exists('current_depth', $options))
    {
      $options['current_depth'] = 1;
      $options['render_depth'] = 1;
    }
    else
    {
      $options['current_depth']++;
      $options['render_depth']++;
    }

    if(array_key_exists('max_depth', $options) && $options['current_depth'] > $options['max_depth'])
    {
      return;
    }

    extract($options);

    $contents = '';
    foreach ($this->items as $item)
    {
      $contents .= $item->render($options);
    }

    return str_repeat("\t", $render_depth - 1).MenuHTML::$list_element(PHP_EOL.$contents.PHP_EOL.str_repeat("\t", $render_depth - 1), $list_attributes).PHP_EOL;
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

    foreach ($names as $name)
    {
      if($this->name == $name)
      {
        $results[] = $this;
      }

      foreach ($this->items as $item)
      {
        if($item->has_children() && $found = $item->children->find($name))
        {
          foreach ($found as $list_item)
          {
            $results[] = $list_item;
          }
        }
      }
    }

    return $results;
  }

  public function __toString()
  {
    return $this->render();
  }

}