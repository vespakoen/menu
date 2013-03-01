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

  public function testCanRenderManuallyBindedItemLists()
  {
    $menu = Menu::handler('categories')
      ->add('algorithms', 'Algorithms', Menu::items()->prefixParents()
        ->add('cryptography', 'Cryptography')
        ->add('data-structures', 'Data Structures')
        ->add('digital-image-processing', 'Digital Image Processing')
        ->add('memory-management', 'Memory Management'))
      ->add('graphics-and-multimedia', 'Graphics & Multimedia', Menu::items()->prefixParents()
        ->add('directx', 'DirectX')
        ->add('flash', 'Flash')
        ->add('opengl', 'OpenGL'));

    $matcher =
    '<ul>'.
      '<li>'.
        '<a href="http://:/algorithms">Algorithms</a>'.
        '<ul>'.
          '<li><a href="http://:/algorithms/cryptography">Cryptography</a></li>'.
          '<li><a href="http://:/algorithms/data-structures">Data Structures</a></li>'.
          '<li><a href="http://:/algorithms/digital-image-processing">Digital Image Processing</a></li>'.
          '<li><a href="http://:/algorithms/memory-management">Memory Management</a></li>'.
        '</ul>'.
      '</li>'.
      '<li>'.
        '<a href="http://:/graphics-and-multimedia">Graphics & Multimedia</a>'.
        '<ul>'.
          '<li><a href="http://:/graphics-and-multimedia/directx">DirectX</a></li>'.
          '<li><a href="http://:/graphics-and-multimedia/flash">Flash</a></li>'.
          '<li><a href="http://:/graphics-and-multimedia/opengl">OpenGL</a></li>'.
        '</ul>'.
      '</li>'.
    '</ul>';

    $this->assertEquals($matcher, $menu->render());
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