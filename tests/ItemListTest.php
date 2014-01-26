<?php
include '_start.php';

use Menu\Menu;
use Menu\Items\Item;
use Menu\Items\ItemList;

class ItemListTest extends MenuTests
{
  public function testEmptyItemListRendersNothing()
  {
    $this->assertTrue('' == static::$itemList->render());
  }

  public function testCanCreateListsOfADifferentElement()
  {
    $list = static::$itemList->add('some', 'item');
    $list->setElement('ol');

    $this->assertHTML($this->matchList('ol'), $list->render());
  }

  public function testCanPrefixItems()
  {
    $list = static::$itemList;
    $list->prefixParents()->prefix('foo');
    $list->add('bar', 'foo');

    $matcher = $this->matchListWithItem();
    $matcher['child']['child'] = $this->matchLink(URL::to('foo/bar'));

    $this->assertHTML($matcher, $list->render());
  }

  public function testCanSetClassOnLists()
  {
    $list = static::$itemList;
    $list->addClass('foo')->data_foo('bar');
    $list->add('some', 'item');
    $matcher = $this->matchList();
    $matcher['attributes']['class'] = 'foo';
    $matcher['attributes']['data-foo'] = 'bar';

    $this->assertHTML($matcher, $list);
  }

  public function testCanSetClassOnItems()
  {
    $list = static::$itemList;
    $list->add('#', 'foo')->onItem()->addClass('foo')
        ->getContent()->href('#lol');

    $matcher = $this->matchListWithItem('ul', 'li');
    $matcher['child']['attributes']['class'] = 'foo';
    $matcher['child']['child']['attributes']['href'] = '#lol';

    $this->assertHTML($matcher, $list);
  }

  public function testChainingMethods()
  {
    $menu = static::$itemList
      ->add('#', 'foo')->onItem()->data_foo('bar')->addClass('active')
        ->getContent()->href('lol')->stop()
      ->add('#', 'bar');

    $this->assertEquals(
      '<ul>'.
        '<li data-foo="bar" class="active">'.
          '<a href="'.URL::to('lol').'">foo</a>'.
        '</li>'.
        '<li>'.
          '<a href="#">bar</a>'.
        '</li>'.
      '</ul>', $menu->render());
  }

  public function testCanAttachMenus()
  {
    $list = static::$itemList;
    $list->add('#', 'foo');
    $list->attach(Menu::items()->add('#', 'bar'));

    $this->assertHTML($this->matchListWithItem(), $list);
    $this->assertHTML($this->matchLink('#', 'bar'), $list);
  }
}
