<?php namespace Vespakoen\Menu;

use ReflectionObject;

use Vespakoen\Menu\Support\Collection;
use Vespakoen\Menu\Contracts\Renderable;
use Vespakoen\Menu\Elements\MenuElement;
use Vespakoen\Menu\Elements\HTMLElement;
use Vespakoen\Menu\Items\LinkItem;
use Vespakoen\Menu\Items\RawItem;

class ItemList extends Collection implements Renderable {

	use MenuElement;

	use HTMLElement;

	public $name;

	public static $types = array();

	public function __construct($items = array(), $name = '')
	{
		$this->setName($name);
		$this->setChildren($items);

		$this->setElement('ul');
	}

	public function add($url, $label = '', $children = null, $interact = null)
	{
		$renderable = new LinkItem($url, $label);

		return $this->addCustom($renderable, $children, $interact);
	}

	public function addRaw($content = '', $children = null, $interact = null)
	{
		$renderable = new RawItem($content);

		return $this->addCustom($renderable, $children, $interact);
	}

	public function addCustom(Renderable $renderable, $children = null, $interact = null)
	{
		if(is_callable($children))
		{
			$itemList = new ItemList();
			$children($itemList);
			$children = $itemList;
		}

		$item = new Item($renderable, $children);

		if(is_callable($interact))
		{
			$interact($item);
		}

		$this->addItem($item);

		return $this;
	}

	public static function registerType($type, $callback)
	{
		static::$types[$type] = $callback;
	}

	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setItems($items)
	{
		if($items instanceof ItemList)
		{
			$items = $items->getChildren();
		}

		$this->setChildren($items);

		return $this;
	}

	public function getItems()
	{
		return $this->getChildren();
	}

	public function addItem($item)
	{
		$this->addChild($item);

		return $this;
	}

	public function getItemsWithDepth()
	{
		return $this->getItemsRecursivelyWithDepth($this->getChildren());
	}

	protected function getItemsRecursivelyWithDepth($items, $depth = 1)
	{
		$results = array();
		foreach($items as $item)
		{
			$results[$depth][] = $item;
	
			$subItems = $item->getItemList()
				->getChildren();
			foreach($this->getItemsRecursivelyWithDepth($subItems, $depth + 1) as $childrenDepth => $children)
			{
				foreach($children as $child)
				{
					$results[$childrenDepth][] = $child;
				}
			}
		}

		return $results;
	}

	public function getItemListsWithDepth()
	{
		return $this->getItemListsRecursivelyWithDepth($this);
	}

	protected function getItemListsRecursivelyWithDepth($itemList, $depth = 1)
	{
		$results = array();

		$results[$depth][] = $itemList;
		foreach($itemList->getChildren() as $item)
		{
			foreach($this->getItemListsRecursivelyWithDepth($item->getItemList(), $depth + 1) as $childrenDepth => $children)
			{
				foreach($children as $child)
				{
					$results[$childrenDepth][] = $child;
				}
			}
		}

		return $results;
	}

	public function getAllItems()
	{
		$results = array();

		foreach($this->getItemsWithDepth() as $depth => $items)
		{
			foreach($items as $item)
			{
				$results[] = $item;
			}
		}

		return new ItemList($results);
	}

	public function getItemsByRenderableType($renderableType)
	{
		$results = array();
		$items = $this->getAllItems();

		foreach($items as $item)
		{
			$renderable = $item->getRenderable();
			if(get_class($renderable) == $renderableType)
			{
				$results[] = $item;
			}
		}

		return new ItemList($results);
	}

	public function getAllItemLists()
	{
		$results = array();

		foreach($this->getItemListsWithDepth() as $depth => $items)
		{
			foreach($items as $item)
			{
				$results[] = $item;
			}
		}

		return new Handler($results);
	}

	public function getAllItemListsIncludingThisOne()
	{
		return $this->getAllItemLists()
			->addMenuObject($this);
	}

	public function getItemListsAtDepth($depth)
	{
		$itemListsWithDepth = $this->getItemListsWithDepth();

		return new Handler($itemListsWithDepth[$depth]);
	}

	public function getItemListsAtDepthRange($from, $to)
	{
		$itemListsWithDepth = $this->getItemListsWithDepth();
		
		$results = array();
		foreach($itemListsWithDepth as $depth => $itemLists)
		{
			if($depth >= $from && $depth <= $to)
			{
				foreach($itemLists as $itemList)
				{
					$results[] = $itemList;
				}
			}
		}

		return new Handler($results);
	}

	public function getItemsAtDepth($depth)
	{
		$itemsWithDepth = $this->getItemsWithDepth();

		return new ItemList($itemsWithDepth[$depth]);
	}

	public function getItemsAtDepthRange($from, $to)
	{
		$itemsWithDepth = $this->getItemsWithDepth();
		
		$results = array();
		foreach($itemsWithDepth as $depth => $items)
		{
			if($depth >= $from && $depth <= $to)
			{
				foreach($items as $item)
				{
					$results[] = $item;
				}
			}
		}

		return new ItemList($results);
	}

	public function findItemListByName($name)
	{
		$itemLists = $this->getAllItemListsIncludingThisOne()
			->getMenuObjects();

		foreach($itemLists as $itemList)
		{
			if($itemList->getName() == $name)
			{
				return $itemList;
			}
		}

		return false;
	}

	public function findByName($name)
	{
		return $this->findItemListByName($name);
	}

	public function find($name)
	{
		return $this->findItemListByName($name);
	}

	public function findItemByAttribute($key, $value)
	{
		$itemList = $this->getAllItemListsIncludingThisOne()
			->getMenuObjects();

		foreach($itemLists as $itemList)
		{
			if($itemList->getAttibute($key) == $value)
			{
				return $itemList;
			}
		}

		return false;
	}

	public function findItemByUrl($url)
	{
		$items = $this->getItemsByRenderableType('LinkItem');

		foreach($items as $item)
		{
			$renderable = $item->getRenderable();
			if($renderable->getUrl() == $url)
			{
				return $item;
			}
		}

		return false;
	}

	public function __call($method, $parameters = array())
	{
		$type = str_replace('add', '', $method);
		if(in_array($type, array_keys(static::$types)))
		{
			$callback = static::$types[$type];
			$item = call_user_func_array($callback, $parameters);

			$reflection = new ReflectionObject($callback);
			$result = $reflection->getMethod('__invoke');
			$totalParameters = $result->getNumberOfParameters() + 2;
			$givenParameters = count($parameters);

			if($totalParameters - 1 == $givenParameters)
			{
				return $this->addCustom($item, $parameters[$totalParameters - 2]);
			}

			if($totalParameters == $givenParameters)
			{
				return $this->addCustom($item, $parameters[$totalParameters - 2], $parameters[$totalParameters - 1]);
			}

			return $this->addCustom($item);
		}
	}

}
