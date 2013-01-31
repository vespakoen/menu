<?php
include '_start.php';

use Menu\Items\Item;
use Menu\Items\ItemList;

class ItemListTest extends MenuTests
{
  public function testCanCreateItemList()
  {
    $this->assertHTML($this->matchList(), static::$itemList->render());
  }

  public function testCanCreateListsOfADifferentElement()
  {
    $list = static::$itemList;
    $list->element('ol');

    $this->assertHTML($this->matchList('ol'), $list->render());
  }
}