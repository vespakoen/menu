<?php
include '_start.php';

use Menu\MenuHandler;

class MenuHandlerTest extends MenuTests
{
  public function testCanCreateMenuHandlerWithMultipleHandles()
  {
    $handles = array('foo', 'bar');
    $menu = new MenuHandler($handles);

    $this->assertEquals($handles, $menu->getHandles());
  }
}