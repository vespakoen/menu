<?php

use Laravel\HTML;

class Menu {

	/**
	 * All the registered names and the associated itemlists
	 *
	 * @var array
	 */
	public static $names = array();

	/**
	 * Create a new ItemList
	 *
	 * @param string	$name		The name of the ItemList
	 * @param array		$attributes	The HTML attributes for the list element
	 * @param string	$element	The HTML element for the list (ul or dd)
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
	 *		// Get the menu handler that handles the default name
	 *		$handler = Menu::handler();
	 *
	 *		// Get a named menu handler for a single name
	 *		$handler = Menu::handler('backend');
	 *
	 *		// Get a menu handler that handles multiple names
	 *		$handler = Menu::handler(array('admin', 'sales'));
	 * </code>
	 *
	 * @param  string	$name
	 *
	 * @return MenuHandler
	 */
	public static function handler($names = '', $attributes = array(), $element = 'ul')
	{
		$names = (array) $names;

		// Create a new Items instance for the names that don't exist yet
		foreach ($names as $name)
		{
			if( ! array_key_exists($name, static::$names))
			{
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
	 *		// Call the "render" method on the default handler
	 *		echo Menu::render();
	 *
	 *		// Call the "add" method on the default handler
	 *		Menu::add('home', 'Home');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::handler(), $method), $parameters);
	}

}

class MenuHandler {

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
	 * @param string	$method
	 * @param array		$parameters
	 *
	 * @return MenuHandler
	 */
	public function __call($method, $parameters)
	{
		// Loop through the ItemLists this handler handles
		foreach($this->handles as $name)
		{
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
		foreach($this->handles as $name)
		{
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
		foreach($this->handles as $name)
		{
			// Find the menuitems
			foreach(Menu::$names[$name]->find($names) as $item_list)
			{
				$results[] = $item_list;
			}
		}

		$not_found_list_items = array_diff($names, array_pluck($results, 'name'));
		if( ! empty($not_found_list_items))
		{
			throw new Exception('Some list items you are trying to find do not exist ('.implode(', ', $not_found_list_items).')');
		}

		foreach ($results as $item_list)
		{
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
	 * @param string	$name 				The itemlist's name
	 * @param array		$list_attributes	Attributes for the itemlist's HMTL element
	 * @param string	$list_element		The HTML element for the itemlist
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
	 *		// Add a item to the default menu
	 *		Menu::add('home', 'Homepage');
	 *
	 *		// Add a item with a subitem to the default menu
	 *		Menu::add('home', 'Homepage', Menu::items()->add('home/sub', 'Subitem'));
	 *
	 *		// Add a item with attributes for the item's HTML element
	 *		Menu::add('home', 'Homepage', null, array('class' => 'fancy'));
	 * </code>
	 *
	 * @param string	$url
	 * @param string	$title
	 * @param ItemList	$children
	 * @param array		$link_attributes
	 * @param array		$item_attributes
	 * @param string	$item_element
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
	 *		// Add a raw item to the default main menu
	 *		Menu::raw('<img src="img/seperator.gif">');
	 * </code>
	 *
	 * @param  string	$html
	 * @param  ItemList	$children
	 * @param  array	$attributes
	 * @param  array	$children
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
	 * 		// Attach menu items to the default menuhandler
	 *		Menu::attach(Menu::items()->add('home', 'Homepage'));
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
	 * @param string	$name
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
	 * @param  array  		$options
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

class Item {

	/**
	 * The list this item is in
	 *
	 * @var ItemList
	 */
	public $list;

	/**
	 * The type of this item (link / raw)
	 *
	 * @var string
	 */
	public $type;

	/**
	 * The text of this item
	 *
	 * @var string
	 */
	public $text;

	/**
	 * The children this item has
	 *
	 * @var ItemList
	 */
	public $children;

	/**
	 * The default render options for this item
	 *
	 * @var array
	 */
	public $options = array(
		'active_class' => 'active',
		'active_child_class' => 'active-child'
	);

	/**
	 * The URL for this item (without prefixes)
	 *
	 * @var string
	 */
	public $url;

	/**
	 * URL segments that you want to hide from the
	 * generated URL when using ->prefix_parents()
	 *
	 * @var array
	 */
	public static $hidden = array(
		'#',
		'javascript:;',
		'javascript:void(0);',
		'javascript:void(0)',
	);

	/**
	 * Create a new item instance
	 *
	 * @param ItemList	$list
	 * @param string	$type
	 * @param string	$text
	 * @param ItemList	$children
	 * @param array		$options
	 * @param string	$url
	 *
	 * @return void
	 */
	public function __construct($list, $type, $text, $children, $options, $url = null)
	{
		$this->list = $list;
		$this->type = $type;
		$this->text = $text;
		$this->children = $children;
		$this->options = array_replace_recursive($this->options, $options);
		$this->url = $url;
	}

	/**
	 * Get all the parent items of this item
	 *
	 * @return array
	 */
	public function get_parents()
	{
		$parents = array();

		$list = $this->list;

		while( ! is_null($list->item))
		{
			$parents[] = $list->item;

			$list = isset($list->item->list) ? $list->item->list : null;
		}

		$parents = array_reverse($parents);

		return $parents;
	}

	public function get_parent_items()
	{
		$parents = array();

		$list = $this->list;

		while( ! is_null($list->item))
		{
			$parents[] = $list->item;

			$list = isset($list->item->list) ? $list->item->list : null;
		}

		$parents = array_reverse($parents);

		return $parents;
	}


	public function get_parent_lists()
	{
		$parents = array();

		$list = $this->list;

		$parents[] = $list;

		while(isset($list->item->list) && ! is_null($list->item->list))
		{
			$parents[] = $list->item->list;

			$list = isset($list->item->list) ? $list->item->list : null;
		}

		$parents = array_reverse($parents);

		return $parents;
	}

	public function get_handler_segment()
	{
		$parent_lists = $this->get_parent_lists();

		$handler = array_pop($parent_lists);

		return is_null($handler->name) ? '' : $handler->name;
	}

	public function get_parent_item_urls()
	{
		$urls = array();

		$parent_items = $this->get_parent_items();

		foreach ($parent_items as $item)
		{
			if($item->type == 'link' && ! is_null($item->url) && ! in_array($item->url, static::$hidden))
			{
				$urls[] = $item->url;
			}
		}

		return $urls;
	}

	/**
	 * Get the evaluated URL based on the prefix settings
	 *
	 * @return string
	 */
	public function get_url()
	{
		$segments = array();

		if (in_array($this->url, static::$hidden))
		{
			return $this->url;
		}

		if( ! is_null($this->list->prefix))
		{
			$segments[] = $this->list->prefix;
		}

		if($this->list->prefix_handler)
		{
			$segments[] = $this->get_handler_segment();
		}

		if($this->list->prefix_parents)
		{
			$segments = $segments + $this->get_parent_item_urls();
		}

		$segments[] = $this->url;

		return implode('/', $segments);
	}

	/**
	 * Check if this item is active
	 *
	 * @return boolean
	 */
	public function is_active()
	{
		return
			$this->get_url() == URI::current() or
			$this->get_url() == URI::full();
	}

	/**
	 * Check if this item has children
	 *
	 * @return boolean
	 */
	public function has_children()
	{
		return ! is_null($this->children);
	}

	/**
	 * Check if this item has an active child
	 *
	 * @return boolean
	 */
	public function has_active_child()
	{
		if( ! $this->has_children())
		{
			return false;
		}

		foreach ($this->children->items as $child)
		{
			if($child->is_active())
			{
				return true;
			}

			if($child->has_children())
			{
				return $child->has_active_child();
			}
		}
	}

	/**
	 * Render the item
	 *
	 * @param array
	 *
	 * @return string
	 */
	public function render($options = array())
	{
		unset($options['list_attributes'], $options['list_element'], $options['link_attributes']);

		$options = array_replace_recursive($this->options, $options);

		extract($options);

		if($this->is_active())
		{
			$item_attributes = merge_attributes($item_attributes, array('class' => $active_class));
		}

		if($this->has_active_child())
		{
			$item_attributes = merge_attributes($item_attributes, array('class' => $active_child_class));
		}

		$children_options = $options;
		$children_options['render_depth']++;
		unset($children_options['item_attributes'], $children_options['item_element']);

		$children = $this->has_children() ? PHP_EOL.$this->children->render($children_options).str_repeat("\t", $render_depth) : '';

		if($this->type == 'raw')
		{
			$content = $this->text;
		}
		else
		{
			$content = PHP_EOL.str_repeat("\t", $render_depth + 1).MenuHTML::link($this->get_url(), $this->text, $link_attributes);
		}

		// Wrap link in an element if one is provided
		$link = $content.$children.PHP_EOL.str_repeat("\t", $render_depth);
		if($item_element) $link = MenuHTML::$item_element($link, $item_attributes);

		return str_repeat("\t", $render_depth).$link.PHP_EOL;
	}

}