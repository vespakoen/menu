<?php
namespace Menu\Traits;

use HtmlObject\Text;

class Content extends Text
{
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
