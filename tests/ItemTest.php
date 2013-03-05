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
    $item->setElement('dl');

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

    $matchSublist = array(
      'tag' => 'li',
      'child' => array(
        'tag' => 'ul',
        'child' => $this->matchItem(),
      ),
    );

    $this->assertHTML($this->matchItem(), $item->render());
    $this->assertHTML($matchSublist, $item->render());
  }

  public function testCanCreateElementlessItems()
  {
    $item = new Item(static::$itemList, static::$link);
    $item->setElement(null);

    $this->assertHTML($this->matchLink(), $item->render());
  }
}