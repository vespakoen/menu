<?php
use Menu\Items\Contents\Raw;

class RawTest extends MenuTests
{
  public function testCanCreateRawContent()
  {
    $raw = new Raw('foobar');

    $this->assertEquals('foobar', $raw->render());
  }
}