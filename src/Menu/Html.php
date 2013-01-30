<?php
namespace Menu;

use \Meido\HTML\HTMLFacade;

class Html extends HTMLFacade
{
  /**
   * Defer calls to Meido's HTML
   *
   * @return HTML
   */
  public static function getFacadeAccessor()
  {
    if (class_exists('App')) return 'html';

    return Menu::getContainer('Meido\HTML\HTML');
  }

  /**
   * Dynamic generation of tags
   *
   * @param string $method     The tag to create
   * @param array  $parameters Value and attributes
   *
   * @return string The tag
   */
  public static function __callStatic($method, $parameters)
  {
    // Magic method for quickly generating basic tags
    if (in_array($method, array('li', 'ul', 'dl', 'dt', 'dd'))) {
      $value = isset($parameters[0]) ? $parameters[0] : null;
      $attributes = isset($parameters[1]) ? $parameters[1] : array();

      return static::element($method, $value, $attributes);
    }

    return parent::__callStatic($method, $parameters);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create a generic tag
   *
   * @param string $element    The element
   * @param string $value      Its value
   * @param array  $attributes Potential attributes
   *
   * @return string
   */
  private static function element($element, $value, $attributes)
  {
    return '<'.$element.static::attributes($attributes).'>'.$value.'</' .$element. '>';
  }
}
