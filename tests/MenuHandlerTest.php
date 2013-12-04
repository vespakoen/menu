<?php
use Menu\Menu;

class MenuHandlerTest extends MenuTests
{
  public function testCanHandle()
  {
    $handles = array('foo', 'bar');
    $menu = Menu::handler($handles);

    $this->assertEquals(array_values(Menu::getItemList()), $menu->getMenuObjects());
  }
}
