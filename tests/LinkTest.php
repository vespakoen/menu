<?php
use Menu\Items\Contents\Link;

class LinkTest extends MenuTests
{
  public function testCanCreateRawContent()
  {
    $link = new Link('', 'foo');

    $this->assertHTML($this->matchLink(), $link->render());
  }
}