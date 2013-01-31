<?php
use Menu\Items\Contents\Link;

class LinkTest extends MenuTests
{
  public function testCanCreateRawContent()
  {
    $link = new Link('', 'foo');

    $this->assertHTML($this->matchLink(), $link->render());
  }

  public function testLinksAreLinks()
  {
    $link = new Link('', 'foo');

    $this->assertTrue($link->isLink());
  }

  public function testCanSetAttributesOnLinks()
  {
    $link = new Link('', 'foo');
    $link->setClass('foobar');

    $matcher = $this->matchLink();
    $matcher['attributes']['class'] = 'foobar';

    $this->assertHTML($matcher, $link->render());
  }
}