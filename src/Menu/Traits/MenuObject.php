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
  public $options = array();

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
    $this->options = array();

    foreach($options as $key => $value) {
      $this->setOption($key, $value);
    }

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
    $globalOptions = Menu::getOption();
    $combinedOptions = array_replace_recursive($globalOptions, $this->options);
    if (!$option) return $combinedOptions;
    return ArraysMethods::get($combinedOptions, $option);
  }
}
