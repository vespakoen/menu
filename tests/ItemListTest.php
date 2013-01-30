<?php
include '_start.php';

use Menu\Items\Item;
use Menu\Items\ItemList;

class ItemListTest extends MenuTests
{
  public function testCanCreateItemList()
  {
    $list = new ItemList();

    $this->assertHTML($this->matchList(), $list->render());
  }

  public function testCanCreateListsOfADifferentElement()
  {
    $list = new ItemList();
    $list->element('ol');

    $this->assertHTML($this->matchList('ol'), $list->render());
  }
}