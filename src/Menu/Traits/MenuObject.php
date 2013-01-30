<?php
/**
 * MenuObject
 *
 * Allows dynamic setting and getting of attributes
 * on the various parts of a menu (items, itemlists, etc)
 */
namespace Menu\Traits;

abstract class MenuObject
{
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