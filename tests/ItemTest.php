<?php
include '_start.php';

use Menu\Items\Item;
use Menu\Items\ItemList;

class ItemTest extends MenuTests
{
  public function testCanCreateAnItem()
  {
    $list = new ItemList();
    $item = new Item($list, 'li', 'foo');
    $matcher = "\r\n\t<li>\r\n\t\t<a href=\"http://:\">foo</a>\r\n\t</li>";

    $this->assertEquals($matcher, $item->render());
  }
}