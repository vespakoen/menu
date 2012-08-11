<?php

Autoloader::map(array(
	'MenuHTML'	=> __DIR__ . DS . 'menuhtml' . EXT,
	'Menu'		=> __DIR__ . DS . 'menu' . EXT,
	'MenuItems'	=> __DIR__ . DS . 'menu' . EXT,
));

function merge_attributes($array1, $array2)
{
	$array = $array1;
	foreach ($array2 as $key => $value)
	{
		if($key !== 'class') return;
		
		if(array_key_exists($key, $array1))
		{
			$array[$key] = $array1[$key] .= ' '.$array2[$key];
		}
		else
		{
			$array[$key] = $array2[$key];
		}
	}

	return $array;
}

if ( ! function_exists('menu'))
{
	/**
	 * Simple wrapper around the menu render method, best used in view file
	 * to keep things nice and pretty. Does not support ->prefix() or
	 * ->prefix_container() as theres no clean way to do this.
	 *
	 * <code>
	 *    {{ menu( 'default', array('class' => 'some class'), 'ul' ) }}
	 * </code>
	 *
	 */
	function menu($key, $attributes = array(), $element = 'ul')
	{
		return Menu::handler($key)->render($attributes, $element);
	}
}