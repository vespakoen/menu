<?php
namespace Menu\Traits;

use \Menu\Items\Item;

class Content extends MenuObject
{
  /**
   * The link text
   *
   * @var string
   */
  protected $value;

  /**
   * The Item this content belongs to
   *
   * @var Item
   */
  protected $item;

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
   * Set the Item this Content belongs to
   *
   * @param Item $item
   *
   * @return Content
   */
  public function inItem(Item $item)
  {
    $this->item = $item;

    return $this;
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