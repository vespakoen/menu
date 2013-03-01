<?php
include '_start.php';

use Menu\Menu;
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
    $list->setElement('ol');

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

  public function testCanSetClassOnLists()
  {
    $list = static::$itemList;
    $list->addClass('foo')->setAttribute('data-foo', 'bar');
    $matcher = $this->matchList();
    $matcher['attributes']['class'] = 'foo';
    $matcher['attributes']['data-foo'] = 'bar';

    $this->assertHTML($matcher, $list);
  }

  public function testCanAttachMenus()
  {
    $list = static::$itemList;
    $list->add('#', 'foo');
    $list->attach(Menu::items()->add('#', 'bar'));

    $this->assertHTML($this->matchListWithItem(), $list->render());
    $this->assertHTML($this->matchLink('#', 'bar'), $list->render());
  }
}