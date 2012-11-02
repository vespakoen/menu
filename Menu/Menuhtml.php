<?php
namespace Menu;

use \Laravel\HTML;

class MenuHTML
{
  /**
   * Create a LI item
   *
   * @param  string $value
   * @param  array  $attributes
   * @return string
   */
  public static function li($value, $attributes)
  {
    return '<li'.HTML::attributes($attributes).'>'.$value.'</li>';
  }

  /**
   * Create a DT item
   *
   * @param  string $value
   * @param  array  $attributes
   * @return string
   */
  public static function dt($value, $attributes)
  {
    return '<dt'.HTML::attributes($attributes).'>'.$value.'</dt>';
  }

  /**
   * Create a set of DD breasts
   *
   * @param  string $value
   * @param  array  $attributes
   * @return string
   */
  public static function dd($value, $attributes)
  {
    return '<dd'.HTML::attributes($attributes).'>'.$value.'</dd>';
  }

  /**
   * Creates an unordered list
   *
   * @param  string $value      Its content
   * @param  array  $attributes Its attributes
   * @return string             An unordered list
   */
  public static function ul($value, $attributes)
  {
    return '<ul'.HTML::attributes($attributes).'>'.$value.'</ul>';
  }

}
