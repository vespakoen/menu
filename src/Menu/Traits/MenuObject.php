<?php
namespace Menu\Traits;

use HtmlObject\Traits\Tag;
use Menu\Menu;
use Underscore\Methods\ArraysMethods;

/**
 * Allows dynamic setting and getting of attributes
 * on the various parts of a menu (items, ItemLists, etc)
 */
abstract class MenuObject extends Tag
{

  /**
   * Per-element configuration
   *
   * @var array
   */
  protected $options;

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CONFIGURATION //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Replace an array of options
   *
   * @param array $options The new options
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

    $this->options = ArraysMethods::set($this->options, $option, $value);

    return $this;
  }

  /**
   * Get a particular option in the array
   *
   * @param string $option An option
   *
   * @return mixed Its value
   */
  public function getOption($option = null)
  {
    // Load the config file if it isn't yet
    if (!$this->options) $this->options = Menu::getOption();
    if (!$option) return $this->options;
    return ArraysMethods::get($this->options, $option);
  }
}
