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

  public function testCanPrefixItems()
  {
    $list = static::$itemList;
    $list->prefixParents()->prefix('foo');
    $list->add('bar', 'foo');

    $matcher = $this->matchListWithItem();
    $matcher['child']['child'] = $this->matchLink('http://:/foo/bar');

    $this->assertHTML($matcher, $list->render());
  }
}