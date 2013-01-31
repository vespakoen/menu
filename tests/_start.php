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
   * Basic matcher for an ItemList
   *
   * @param string $element
   *
   * @return array
   */
  protected function matchList($element = 'ul')
  {
    return array(
      'tag' => $element,
    );
  }

  /**
   * Basic matcher for an Item
   *
   * @param string $element
   *
   * @return array
   */
  protected function matchItem($element = 'li')
  {
    return array(
      'tag'   => $element,
      'child' => $this->matchLink(),
    );
  }

  /**
   * Basic matcher for a Link
   *
   * @return array
   */
  protected function matchLink()
  {
    return array(
      'tag' => 'a',
      'content' => 'foo',
      'attributes' => array(
        'href' => 'http://:'
      ),
    );
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Asserts some HTML is, once stripped, equals to a matcher
   *
   * @param string $matcher The matcher
   * @param string $html    The HTML
   */
  protected function assertStripedEquals($matcher, $html)
  {
    $html = preg_replace("/[\n\r\t]/", null, $html);

    return $this->assertEquals($matcher, $html);
  }

  /**
   * Enhanced version of assertTag
   *
   * @param array  $matcher The tag matcher
   * @param string $html    The HTML
   */
  protected function assertHTML($matcher, $html)
  {
    return $this->assertTag(
      $matcher,
      $html,
      "Failed asserting that the HTML matches the provided format :\n\t"
        .$html."\n\t"
        .json_encode($matcher));
  }
}