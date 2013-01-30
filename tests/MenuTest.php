<?php
use Menu\Menu;

class MenuTest extends PHPUnit_Framework_TestCase
{
  public function testCanGetAMenu()
  {
    $menu = Menu::handler('foo');

    $this->assertInstanceOf('Menu\MenuHandler', $menu);
  }
}