<?php

use Laravel\HTML;

class MenuHTML {

	/**
	 * Convert HTML characters to entities.
	 *
	 * The encoding specified in the application configuration file will be used.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, Config::get('application.encoding'), false);
	}

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
	public static function link($url, $title = null, $attributes = array(), $https = false)
	{
		$url = URL::to($url, $https);

		if (is_null($title)) $title = $url;

		return '<a href="'.$url.'"'.static::attributes($attributes).'>'.$title.'</a>';
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

	public static function ul($value, $attributes)
	{
		return '<ul'.static::attributes($attributes).'>'.$value.'</ul>';
	}

}