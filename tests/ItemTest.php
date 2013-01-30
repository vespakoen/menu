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

    $this->assertHTML($this->matchItem(), $item->render());
  }
}