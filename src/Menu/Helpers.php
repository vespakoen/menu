<?php
/**
 * Helpers
 *
 * Various helpers used troughout the classes
 */
namespace Menu;

use Underscore\Types\Arrays;
use Underscore\Types\String;

class Helpers
{
  /**
   * Adds a class to an array of attributes
   *
   * @param array  $attributes The attributes
   * @param string $class      The class to add
   */
  public static function addClassTo($attributes, $class)
  {
    // Explode class if array passed
    if (is_array($class)) $class = explode(' ', $class);
    $classes = Arrays::get($attributes, 'class');

    // Append class if it's not already here
    if (!$classes) $classes = $class;
    elseif (!String::contains($classes, $class)) $classes .= ' ' .$class;

    $attributes['class'] = $classes;

    return $attributes;
  }
}
