<?php namespace Vespakoen\Menu;

class Menu {

	public static $itemLists = array();

	public static function handler($names = '')
	{
		$names = (array) $names;

		$itemLists = array();
		foreach($names as $name)
		{
			if( ! array_key_exists($name, static::$itemLists))
			{
				static::$itemLists[$name] = new ItemList(array(), $name);
			}

			$itemLists[] = static::$itemLists[$name];
		}

		return new Handler($itemLists);
	}

	public static function items($name = '', $children = array())
	{
		if(is_callable($children))
		{
			$interact = $children;
			$children = array();
		}

		if($children instanceof ItemList)
		{
			$children = $children->getChildren();
		}

		$itemList = new ItemList($children, $name);

		if(isset($interact))
		{
			$interact($itemList);
		}

		return $itemList;
	}

	public static function registerType($type, $callback)
	{
		ItemList::registerType($type, $callback);
	}

	public static function reset()
	{
		static::$itemLists = array();
	}

	public static function __callStatic($method, $parameters = array())
	{
		return call_user_func_array(array(static::handler(), $method), $parameters);
	}

}
