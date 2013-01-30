<?php
use Menu\Menu;

abstract class MenuTests extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    Menu::reset();
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Enhanced version of assertTag
   *
   * @param array  $matcher The tag matcher
   * @param string $html    The HTML
   */
  protected function assertHTML($matcher, $html)
  {
    $this->assertTag(
      $matcher,
      $input,
      "Failed asserting that the HTML matches the provided format :\n\t"
        .$input."\n\t"
        .json_encode($matcher));
  }
}