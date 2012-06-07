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
	public function add($url, $title, $children = null, $link_attributes = array(), $list_attributes = array(), $list_element = 'li')
	{
		$this->items[] = compact('url', 'title', 'children', 'link_attributes', 'list_attributes', 'list_element');

		return $this;
	}

	/**
	 * Add a raw html item to the MenuItems instance.
	 *
	 * <code>
	 *		// Add a raw item to the default main menu
	 *		Menu::container()->raw('<img src="img/seperator.gif">');
	 * </code>
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  array   $children
	 * @return MenuItems
	 */
	public function raw($html, $children = null, $list_attributes = array(), $list_element = 'li')
	{
		$this->items[] = compact('html', 'children', 'list_attributes', 'list_element');
		
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
	 * @param  array  		$attributes 		Attributes for the ul element
	 * @param  string  		$element 			The type of the element (ul or ol)
	 * @param  MenuItems 	$items          	Reusing the method for child menu items
	 * @return string
	 */
	public function render($attributes = array(), $element = 'ul', $items = null)
	{
		if(is_null($items)) $items = $this->items;

		if(is_null($items)) return '';

		$menu_items = array();
		foreach($items as $item)
		{
			$url = ($this->prefix_links ? (gettype($this->prefix_links) == 'string' ? $this->prefix_links : $this->container) . '/' : '') . $item['url'];

			if(URI::is($url))
			{
				$item['attributes'] = merge_attributes($item['attributes'], array('class' => 'active'));
			}

			if(URI::is($url . '/*'))
			{
				$item['attributes'] = merge_attributes($item['attributes'], array('class' => 'active_subs'));
			}

			$children = array_key_exists('children', $item) && ! is_null($item['children']) ? $this->render($attributes, $element, $item['children']->items) : '';

			$menu_items[] = $this->render_item($item, $children);
		}
		
		return MenuHTML::$element($menu_items, $attributes);
	}

	/**
	 * Turn item data into HTML
	 * 
	 * @param 	array 	$item 		The menu item
	 * @param 	string 	$children 	The children HTML
	 * @return 	string 	The HTML
	 */
	protected function render_item($item, $children = '')
	{
		extract($item);

		if(array_key_exists('html', $item))
		{
			$html = HTML::$list_element($html.PHP_EOL.$children, $list_attributes);
		}
		else
		{
			$html = HTML::$list_element(MenuHTML::link($url, $title, $link_attributes).PHP_EOL.$children, $list_attributes);
		}

		return $html;
	}

}