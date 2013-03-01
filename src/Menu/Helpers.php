<?php
namespace Menu;

use Underscore\Methods\ArraysMethods;
use Underscore\Methods\StringMethods;

/**
 * Various helpers used troughout the classes
 */
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
    $classes = ArraysMethods::get($attributes, 'class');

    // Append class if it's not already here
    if (!$classes) $classes = $class;
    elseif (!StringMethods::contains($classes, $class)) $classes .= ' ' .$class;

    $attributes['class'] = $classes;

    return $attributes;
  }
}
