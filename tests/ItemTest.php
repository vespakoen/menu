<?php
use Menu\Items\Item;
use Menu\Items\ItemList;

class ItemTest extends MenuTests
{
  public function testCanCreateAnItem()
  {
    $item = new Item(new ItemList, 'link', 'foo');

    $this->assertHTML($this->matchItem(), $item->render());
  }

  public function testCanCreateItemOfADifferentElement()
  {
    $item = new Item(new ItemList, 'link', 'foo');
    $item->element('dl');

    $this->assertHTML($this->matchItem('dl'), $item->render());
  }

  public function testCanCreateRawItem()
  {
    $item = new Item(new ItemList, 'raw', 'foo');
    $matcher = array('tag' => 'li', 'content' => 'foo');

    $this->assertHTML($matcher, $item->render());
  }

  public function testCanCreateItemWithSublist()
  {
    $sublist = new ItemList();
    $sublist->add('#', 'foo');
    $item = new Item(new ItemList, 'link', 'foo', $sublist);

    $matcher =
    '<li>'.
      '<a href="http://:">foo</a>'.
      '<ul><li><a href="http://:/#">foo</a></li></ul>'.
    '</li>';

    $this->assertStripedEquals($matcher, $item->render());
  }
}