<?php
use Menu\Menu;
use Menu\Items\ItemList;

class MenuTest extends MenuTests
{
  public function testCanReturnAMenuHandler()
  {
    $menu = Menu::handler('foo');

    $this->assertInstanceOf('Menu\MenuHandler', $menu);
  }

  public function testCanGetAMenuThatHandlesEverything()
  {
    Menu::handler('foo');
    Menu::handler('bar');
    $menu = Menu::allHandlers();

    $this->assertEquals(array('foo', 'bar'), $menu->getHandles());
  }

  public function testCanResetAllHandles()
  {
    Menu::handler('foo');
    Menu::reset();

    $this->assertEquals(array(), Menu::getItemList());
  }

  public function testCanReturnItemLists()
  {
    $itemList = Menu::items('foo');

    $this->assertInstanceOf('Menu\Items\ItemList', $itemList);
  }

  public function testCanSetItemLists()
  {
    $itemList = Menu::items('foo');
    Menu::setItemList('foo', $itemList);

    $this->assertArrayHasKey('foo', Menu::getItemList());
  }

  public function testCanGetItemLists()
  {
    $itemList = Menu::items('foo');
    Menu::setItemList('foo', $itemList);

    $this->assertEquals($itemList, Menu::getItemList('foo'));
  }

  public function testCanGetValueFromConfig()
  {
    $config = Menu::getOption('max_depth');
    $this->assertEquals(0, $config);

    $config = Menu::getOption();
    $this->assertInternalType('array', $config);
  }

  public function testClassesPassTheirConfigurationToChildren()
  {
    $list = static::$itemList;
    $list->setOption('item.element', 'dl');
    $list->add('#', 'foo');

    $this->assertEquals('<ul><dl><a href="#">foo</a></dl></ul>', $list->render());
  }

  public function testMenuCanSetGlobalOptions()
  {
    Menu::setOption('item.element', 'dl');

    $list = new ItemList;
    $list->add('#', 'foo');

    $this->assertEquals('<ul><dl><a href="#">foo</a></dl></ul>', $list->render());
  }
}