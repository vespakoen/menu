<?php
namespace Menu\Traits;

class Content extends MenuObject
{
  /**
   * The link text
   *
   * @var string
   */
  protected $text;

  /**
   * Render the content
   *
   * @return string
   */
  public function render()
  {
    return $this->text;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the content type
   *
   * @return boolean
   */
  public function isLink()
  {
    return false;
  }
}