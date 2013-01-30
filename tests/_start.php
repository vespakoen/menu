<?php
use Menu\Menu;

abstract class MenuTests extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    Menu::reset();
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// MATCHERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Basic matcher for an Item
   *
   * @return array
   */
  protected function matchItem()
  {
    return array(
      'tag' => 'li',
      'child' => array(
        'tag' => 'a',
        'content' => 'foo',
        'attributes' => array(
          'href' => 'http://:'
        ),
      ),
    );
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
      $html,
      "Failed asserting that the HTML matches the provided format :\n\t"
        .$html."\n\t"
        .json_encode($matcher));
  }
}