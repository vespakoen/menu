<?php
include '_start.php';

use Menu\Items\Item;
use Menu\Items\ItemList;

class ItemTest extends MenuTests
{
  public function testCanCreateAnItem()
  {
    $list = new ItemList();
    $item = new Item($list, 'link', 'foo');

    $this->assertHTML($this->matchItem(), $item->render());
  }

  public function testCanCreateItemOfADifferentElement()
  {
    $list = new ItemList();
    $item = new Item($list, 'link', 'foo');
    $item->element('dl');

    $this->assertHTML($this->matchItem('dl'), $item->render());
  }
}