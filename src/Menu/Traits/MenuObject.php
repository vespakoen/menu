<?php
/**
 * MenuObject
 *
 * Allows dynamic setting and getting of attributes
 * on the various parts of a menu (items, ItemLists, etc)
 */
namespace Menu\Traits;

use \Menu\Menu;
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

  /**
   * Per-element configuration
   *
   * @var array
   */
  protected $options;

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// ATTRIBUTES ///////////////////////////
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
  /////////////////////////// CONFIGURATION //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Replace an array of options
   *
   * @return MenuObject
   */
  public function replaceOptions($options)
  {
    $this->options = $options;

    return $this;
  }

  /**
   * Set a particular option in the array
   *
   * @param string $option The option
   * @param mixed  $value  Its new value
   *
   * @return MenuObject
   */
  public function setOption($option, $value)
  {
    // Load the config file if it isn't yet
    if (!$this->options) $this->options = Menu::getOption();

    $this->options = Arrays::set($this->options, $option, $value);

    return $this;
  }

  /**
   * Get a particular option in the array
   *
   * @param string $option An option
   *
   * @return mixed Its value
   */
  protected function getOption($option = null)
  {
    // Load the config file if it isn't yet
    if (!$this->options) $this->options = Menu::getOption();
    if (!$option) return $this->options;

    return Arrays::get($this->options, $option);
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
}