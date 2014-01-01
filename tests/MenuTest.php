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
    $allHandlers = Menu::allHandlers();

    $this->assertEquals(array('foo', 'bar'), array_keys($allHandlers->getMenuObjects()));
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
        '<a href="'.URL::to('algorithms').'">Algorithms</a>'.
        '<ul>'.
          '<li><a href="'.URL::to('algorithms/cryptography').'">Cryptography</a></li>'.
          '<li><a href="'.URL::to('algorithms/data-structures').'">Data Structures</a></li>'.
          '<li><a href="'.URL::to('algorithms/digital-image-processing').'">Digital Image Processing</a></li>'.
          '<li><a href="'.URL::to('algorithms/memory-management').'">Memory Management</a></li>'.
        '</ul>'.
      '</li>'.
      '<li>'.
        '<a href="'.URL::to('graphics-and-multimedia').'">Graphics & Multimedia</a>'.
        '<ul>'.
          '<li><a href="'.URL::to('graphics-and-multimedia/directx').'">DirectX</a></li>'.
          '<li><a href="'.URL::to('graphics-and-multimedia/flash').'">Flash</a></li>'.
          '<li><a href="'.URL::to('graphics-and-multimedia/opengl').'">OpenGL</a></li>'.
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
    $this->assertEquals(-1, $config);

    $config = Menu::getOption();
    $this->assertInternalType('array', $config);
  }

  public function testClassesPassTheirConfigurationToChildren()
  {
    $list = static::$itemList;
    $list->add('#', 'foo');
    $list->setOption('item.element', 'dl');

    $this->assertHTML($this->matchListWithItem('ul', 'dl'), $list->render());
    $this->assertHTML($this->matchLink(), $list->render());
  }

  public function testMenuCanSetGlobalOptions()
  {
    Menu::setOption('item.element', 'dl');

    $list = new ItemList;
    $list->add('#', 'foo');

    $this->assertHTML($this->matchListWithItem('ul', 'dl'), $list->render());
    $this->assertHTML($this->matchLink(), $list->render());
  }

  public function testChainingMethods()
  {
    $menu = Menu::handler('foo')
      ->add('#', 'foo')->onItem()->data_foo('bar')->addClass('active')
        ->getContent()->href('lol')->stop()
      ->add('#', 'bar');

    $this->assertEquals(
      '<ul>'.
        '<li data-foo="bar" class="active">'.
          '<a href="http://localhost/lol">foo</a>'.
        '</li>'.
        '<li>'.
          '<a href="#">bar</a>'.
        '</li>'.
      '</ul>', $menu->render());
  }
}
