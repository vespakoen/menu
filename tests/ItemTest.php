<?php
use Menu\Items\Item;
use Menu\Items\ItemList;

class ItemTest extends MenuTests
{
  public function testCanCreateAnItem()
  {
    $this->assertHTML($this->matchItem(), static::$item->render());
  }

  public function testCanCreateItemOfADifferentElement()
  {
    $item = static::$item;
    $item->element('dl');

    $this->assertHTML($this->matchItem('dl'), $item->render());
  }

  public function testCanCreateRawItem()
  {
    $item = new Item(static::$itemList, static::$raw);
    $matcher = array('tag' => 'li', 'content' => 'foo');

    $this->assertHTML($matcher, $item->render());
  }

  public function testCanCreateItemWithSublist()
  {
    $sublist = static::$itemList;
    $sublist->add('#', 'foo');
    $item = new Item(static::$itemList, static::$link, $sublist);

    $matcher =
    '<li>'.
      '<a href="http://:/#">foo</a>'.
      '<ul><li><a href="http://:/#">foo</a></li></ul>'.
    '</li>';

    $this->assertStripedEquals($matcher, $item->render());
  }
}