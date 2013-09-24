<?php

use Vespakoen\Menu\Menu;
use Vespakoen\Menu\Handler;
use Vespakoen\Menu\ItemList;

class MenuTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->menu = Menu::add('first', 'first', function($children)
		{
			$children->add('second', 'second', function($children)
			{
				$children->setName('named');
				$children->add('third', 'third');
			});
		});
	}

	public function tearDown()
	{
		Menu::reset();
	}

	public function testMenuReturnsHandler()
	{
		$this->assertTrue($this->menu instanceof Handler);
	}

	public function testGetItemsWithDepthReturnsArray()
	{
		$this->assertEquals(gettype($this->menu->getItemsWithDepth()), "array");
	}

	public function testGetItemsAtDepthReturnsItemList()
	{
		$this->assertTrue($this->menu->getItemsAtDepth(1) instanceof ItemList);
	}

	public function testGetAllItemsReturnsAllItems()
	{
		$allItems = $this->menu->getAllItems();
		foreach($allItems as $item)
		{
			$url = $item->getRenderable()->getUrl();
			$this->assertTrue(in_array($url, array('first', 'second', 'third')));
		}
	}

	public function testGetItemsAtDepthReturnsCorrectResults()
	{
		// Check return type
		$itemList = $this->menu->getItemsAtDepth(1);
		$this->assertTrue($itemList instanceof ItemList);

		// Check first level item
		$firstLevelItem = reset($this->menu->getItemsAtDepth(1)
			->getItems());
		$url = $firstLevelItem->getRenderable()->getUrl();
		$this->assertEquals($url, 'first');

		// Check second level item
		$secondLevelItem = reset($this->menu->getItemsAtDepth(2)
			->getItems());

		$url = $secondLevelItem->getRenderable()->getUrl();
		$this->assertEquals($url, 'second');

		// Check third level item
		$thirdLevelItem = reset($this->menu->getItemsAtDepth(3)
			->getItems());
		$url = $thirdLevelItem->getRenderable()->getUrl();
		$this->assertEquals($url, 'third');
	}

	public function testGetItemsAtDepthRangeReturnsCorrectResults()
	{
		$firstLevelItem = reset($this->menu->getItemsAtDepthRange(1,2)
			->getItems());

		$url = $firstLevelItem->getRenderable()->getUrl();
		$this->assertEquals($url, 'first');

		$secondLevelItem = reset($this->menu->getItemsAtDepthRange(2,3)
			->getItems());
		
		$url = $secondLevelItem->getRenderable()->getUrl();
		$this->assertEquals($url, 'second');

		$thirdLevelItem = reset($this->menu->getItemsAtDepthRange(3,4)
			->getItems());
		
		$url = $thirdLevelItem->getRenderable()->getUrl();
		$this->assertEquals($url, 'third');

		$allItems = $this->menu->getItemsAtDepthRange(1,4);
		foreach($allItems as $item)
		{
			$url = $item->getRenderable()->getUrl();
			$this->assertTrue(in_array($url, array('first', 'second', 'third')));
		}
	}

	public function testNamedItemLists()
	{
 		$match = $this->menu->findItemListByName('named');
 		$this->assertEquals($match->getName(), 'named');

 		$noMatch = $this->menu->findItemListByName('non-existing-name');
 		$this->assertFalse($noMatch);
	}

}
