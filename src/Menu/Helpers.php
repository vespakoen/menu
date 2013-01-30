<?php
/**
 * Helpers
 *
 * Various helpers used troughout the classes
 */
namespace Menu;

use Underscore\Types\Methods;

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
    $classes = Arrays::get($attributes, 'class');

    // Append class if it's not already here
    if (!$classes) $classes = $class;
    elseif (!String::contains($classes, $class)) $classes .= ' ' .$class;

    $attributes['class'] = $classes;

    return $attributes;
  }
}
