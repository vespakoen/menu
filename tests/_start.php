<?php
use Menu\Menu;

abstract class MenuTests extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    Menu::reset();
  }
}