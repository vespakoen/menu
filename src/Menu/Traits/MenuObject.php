<?php
/**
 * MenuObject
 *
 * Allows dynamic setting and getting of attributes
 * on the various parts of a menu (items, ItemLists, etc)
 */
namespace Menu\Traits;

use \Underscore\Types\Arrays;

abstract class MenuObject
{
  /**
   * The object element
   *
   * @var string
   */
  protected $element;

  /**
   * The object attributes
   *
   * @var array
   */
  protected $attributes = array();

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// PUBLIC INTERFACE ////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Change the element used by the Item
   *
   * @param string $element The element
   *
   * @return MenuObject
   */
  public function element($element)
  {
    $this->element = $element;

    return $this;
  }

  /**
   * Set the Object's class
   *
   * @param string $class The new class
   *
   * @return MenuObject
   */
  public function setClass($class)
  {
    $this->setAttribute('class', $class);

    return $this;
  }

  /**
   * Replace the current attributes with other ones
   *
   * @param array $attributes The new attributes
   *
   * @return MenuObject
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;

    return $this;
  }

  /**
   * Set a single attribute
   *
   * @param string $attribute The attribute
   * @param string $value     Its value
   *
   * @return MenuObject
   */
  public function setAttribute($attribute, $value)
  {
    $this->attributes = Arrays::set($this->attributes, $attribute, $value);

    return $this;
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// CORE METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Render element on string cast
   *
   * @return string
   */
  public function __toString()
  {
    return $this->render();
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Renders content in a new line, tabbed
   *
   * @param string  $content The content to render
   * @param integer $tabs    The number of tabs
   *
   * @return string
   */
  protected function renderTabbed($content, $tabs = 0)
  {
    return PHP_EOL.str_repeat("\t", $tabs).$content;
  }
}