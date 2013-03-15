<?php
namespace Menu\Traits;

use HtmlObject\Text;

/**
 * The base class around the different content types
 */
class Content extends Text
{
  /**
   * Whether the content is a link or not
   *
   * @return boolean
   */
  public function isLink()
  {
    return false;
  }

  /**
   * Break off a chain
   *
   * @return Item
   */
  public function stop()
  {
    return $this->getParent(1);
  }
}
