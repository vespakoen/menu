<?php
use Menu\Items\Contents\Link;

class LinkTest extends MenuTests
{
  public function testCanCreateRawContent()
  {
    $this->assertHTML($this->matchLink(), static::$link->render());
  }

  public function testLinksAreLinks()
  {
    $this->assertTrue(static::$link->isLink());
  }

  public function testCanSetAttributesOnLinks()
  {
    $link = static::$link;
    $link->setClass('foobar');

    $matcher = $this->matchLink();
    $matcher['attributes']['class'] = 'foobar';

    $this->assertHTML($matcher, $link->render());
  }
}