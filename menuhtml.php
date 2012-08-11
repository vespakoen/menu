<?php

use Laravel\HTML;

class MenuHTML extends HTML {

	/**
	 * Generate a HTML link.
	 *
	 * <code>
	 *		// Generate a link to a location within the application
	 *		echo HTML::link('user/profile', 'User Profile');
	 *
	 *		// Generate a link to a location outside of the application
	 *		echo HTML::link('http://google.com', 'Google');
	 * </code>
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return string
	 */
	public static function link($url, $title = null, $attributes = array(), $https = null)
	{
		$url = URL::to($url, $https);

		if (is_null($title)) $title = $url;

		return '<a href="'.$url.'"'.static::attributes($attributes).'>'.static::entities($title).'</a>';
	}

	/**
	 * Generate an ordered or un-ordered list.
	 *
	 * @param  string  $type
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	public static function listing($type, $list, $attributes = array())
	{
		$html = '';

		if (count($list) == 0) return $html;

		foreach ($list as $key => $value)
		{
			// If the value is an array, we will recurse the function so that we can
			// produce a nested list within the list being built. Of course, nested
			// lists may exist within nested lists, etc.
			if (is_array($value))
			{
				if (is_int($key))
				{
					$html .= static::listing($type, $value);
				}
				else
				{
					$html .= '<li>'.$key.static::listing($type, $value).'</li>';
				}
			}
			else
			{
				$html .= $value;
			}
		}

		return '<'.$type.static::attributes($attributes).'>'.$html.'</'.$type.'>';
	}

	/**
	 * Build a list of HTML attributes from an array.
	 *
	 * @param  array   $attributes
	 * @return string
	 */
	public static function attributes($attributes)
	{
		$html = array();

		foreach ((array) $attributes as $key => $value)
		{
			// For numeric keys, we will assume that the key and the value are the
			// same, as this will conver HTML attributes such as "required" that
			// may be specified as required="required", etc.
			if (is_numeric($key)) $key = $value;

			if ( ! is_null($value))
			{
				$html[] = $key.'="'.static::entities($value).'"';
			}
		}

		return (count($html) > 0) ? ' '.implode(' ', $html) : '';
	}

	/**
	 * Create a LI item
	 *
	 * @param  string   $value
	 * @param  array   	$attributes
	 * @return string
	 */
	public static function li($value, $attributes)
	{
		return '<li'.static::attributes($attributes).'>'.$value.'</li>';
	}

	/**
	 * Create a DT item
	 *
	 * @param  string   $value
	 * @param  array   	$attributes
	 * @return string
	 */
	public static function dt($value, $attributes)
	{
		return '<dt'.static::attributes($attributes).'>'.$value.'</dt>';
	}

	/**
	 * Create a set of DD breasts
	 *
	 * @param  string   $value
	 * @param  array   	$attributes
	 * @return string
	 */
	public static function dd($value, $attributes)
	{
		return '<dd'.static::attributes($attributes).'>'.$value.'</dd>';
	}

}