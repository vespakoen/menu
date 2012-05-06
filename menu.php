<?php

use Laravel\HTML;

class Menu {

	/**
	 * All the created menu containers.
	 *
	 * @var array
	 */
	public static $containers = array();

	/**
	 * The containers that are being handled for this instance
	 *
	 * @var array
	 */
	public $handles = array();

	/**
	 * Set the containers where to act on
	 * 
	 * @param array $handles The containers
	 */
	public function __construct($handles)
	{
		$this->handles = $handles;
	}

	/**
	 * Magic method for calling methods on the containers
	 * 
	 * @param  string $method
	 * @param  array $parameters
	 * @return Menu
	 */
	public function __call($method, $parameters)
	{
		foreach ($this->handles as $container)
		{
			static::$containers[$container] = call_user_func_array(array(static::$containers[$container], $method), $parameters);
		}

		return $this;
	}


	/**
	 * Get the evaluated string content for the menu containers this menu acts upon.
	 *
	 * @return string
	 */
	public function render($list_attributes = array(), $link_attributes = array())
	{
		$html = '';
		foreach($this->handles as $container)
		{
			$html .= static::$containers[$container]->render($list_attributes, $link_attributes);
		}

		return $html;
	}

	public function items()
	{
		return new MenuItems();
	}

	/**
	 * Get the evaluated string content for the menu containers this menu acts upon.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Get a menu instance.
	 *
	 * <code>
	 *		// Get the default menu container
	 *		$container = Menu::container();
	 *
	 *		// Get a named menu container
	 *		$container = Menu::container('backend');
	 * 
	 *		// Get a menu that acts on multiple containers
	 *		$container = Menu::container(array('admin', 'sales'));
	 * </code>
	 *
	 * @param  string            $container
	 * @return Menu
	 */
	public static function container($containers = '', $prefix_links = false)
	{
		if( ! is_array($containers)) $containers = array($containers);

		foreach ($containers as $container)
		{
			if( ! array_key_exists($container, static::$containers))
			{
				static::$containers[$container] = new MenuItems($container, $prefix_links);
			}
		}

		return new Menu($containers);
	}

	/**
	 * Magic Method for calling methods on the default container.
	 *
	 * <code>
	 *		// Call the "render" method on the default container
	 *		echo Menu::render();
	 *
	 *		// Call the "add" method on the default container
	 *		Menu::add('home', 'Home');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::container(), $method), $parameters);
	}

}

class MenuItems {

	/**
	 * The menu items
	 * @var array
	 */
	public $items = array();
	
	/**
	 * The container name
	 * @var string
	 */
	public $container;
	
	/**
	 * Prefix the links with the container name of a custom string
	 * 
	 * @var mixed
	 */
	public $prefix_links;
	
	/**
	 * Creating a new MenuItems instance
	 * 
	 * @param string  $container
	 * @param mixed $prefix_links
	 */
	public function __construct($container = '', $prefix_links = false)
	{
		$this->container = $container;
		$this->prefix_links = $prefix_links;
	}
	
	/**
	 * Creating a new MenuItems instance
	 * 
	 * @param string  $container
	 * @param mixed $prefix_links
	 */
	public static function factory()
	{
		return new MenuItems;
	}
	
	/**
	 * Add a menu item to the MenuItems instance.
	 *
	 * <code>
	 *		// Add a item to the default main menu
	 *		Menu::container()->add('home', 'Homepage');
	 *
	 *		// Add a subitem to the homepage
	 *		Menu::container()->add('home', 'Homepage', array(), MenuItems::factory()->add('home/sub', 'Subitem'));
	 *
	 *		// Add a item that has attributes applied to its tag
	 *		Menu::add('home', 'Homepage', array('class' => 'fancy'));
	 * </code>
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  array   $children
	 * @return MenuItems
	 */
	public function add($url, $title, $attributes = array(), $children = null)
	{
		$this->items[] = array(
			'url' => $url,
			'title' => $title,
			'attributes' => $attributes,
			'children' => $children
		);
		
		return $this;
	}

	/**
	 * Add menu items to another MenuItems instance.
	 *
	 * <code>
	 * 		// Attach menu items to the default menu container
	 *		Menu::attach(MenuItems::factory()->add('home', 'Homepage'));
	 * </code>
	 *
	 * @param  MenuItems  $menuitems
	 * @return Void
	 */	
	public function attach($menuitems)
	{
		$this->items = array_merge($this->items, $menuitems->items);
	}
	
	/**
	 * Get the evaluated string content of the view.
	 * 
	 * @param  array  		$list_attributes 	Extra attributes for the list
	 * @param  array  		$link_attributes 	Extra attributes for the link
	 * @param  MenuItems 	$items          	Reusing the method for child menu items
	 * @return string
	 */
	public function render($list_attributes = array(), $link_attributes = array(), $items = null)
	{
		if(is_null($items)) $items = $this->items;
		if(is_null($items)) return '';

		$menu_items = array();
		foreach($items as $item)
		{
			$url = ($this->prefix_links ? (gettype($this->prefix_links) == 'string' ? $this->prefix_links : $this->container) . '/' : '') . $item['url'];

			$attributes = $link_attributes;
			if(URI::is($url))
			{
				$attributes = array_merge_recursive($attributes, array('class' => 'active'));
			}

			if(URI::is($url . '/*'))
			{
				$attributes = array_merge_recursive($attributes, array('class' => 'active_subs'));
			}

			$menu_item = MenuHTML::link($url, $item['title'], $attributes);
			if( ! is_null($item['children']))
			{
				$menu_item .= $this->render($list_attributes, $link_attributes, $item['children']->items);
			}

			$menu_items[] = $menu_item;
		}
		
		return MenuHTML::ul($menu_items, $list_attributes);
	}

}