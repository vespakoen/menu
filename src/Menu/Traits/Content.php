<?php
namespace Menu\Traits;

class Content extends MenuObject
{
  /**
   * The link text
   *
   * @var string
   */
  protected $value;

  /**
   * Build a new content
   *
   * @param string $value The content
   */
  public function __construct($value)
  {
    $this->value = $value;
  }

  /**
   * Render the content
   *
   * @return string
   */
  public function render()
  {
    return $this->value;
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