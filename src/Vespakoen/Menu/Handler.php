<?php namespace Vespakoen\Menu;

class Handler {

	public $menuObjects;

	public $lastResults;

	public static $override = array(
		'add',
		'addRaw',
		'addCustom',
		'addItem',
		'setName',
		'setItems'
	);

	public static $responses = array(
		'getHandlerFromResults' => array(
			'getAllItemLists',
			'getItemListsAtDepth',
			'getItemListsAtDepthRange',
			'filter'
		),
		'getItemListFromResults' => array(
			'getAllItems',
			'getItemsAtDepth',
			'getItemsAtDepthRange'
		),
		'getMatchFromResults' => array(
			'findItemListByName',
			'findByName',
			'find'
		),
		'getCombinedResult' => array(
			'lists'
		)
	);

	public function __construct($menuObjects = array())
	{
		$this->menuObjects = $menuObjects;
	}

	public function setMenuObjects($menuObjects)
	{
		$this->menuObjects = $menuObjects;

		return $this;
	}

	public function getMenuObjects()
	{
		return $this->menuObjects;
	}

	public function addMenuObject($menuObject)
	{
		$this->menuObjects[] = $menuObject;

		return $this;
	}

	public function getItemsWithDepth()
	{
		$this->__call('getItemsWithDepth');

		$results = array();
		foreach ($this->lastResults as $result)
		{
			foreach($result as $depth => $items)
			{
				foreach($items as $item)
				{
					$results[$depth][] = $item;
				}
			}
		}

		return $results;
	}

	public function getItemListsWithDepth()
	{
		$this->__call('getItemListsWithDepth');

		$results = array();
		foreach ($this->lastResults as $result)
		{
			foreach($result as $depth => $items)
			{
				foreach($items as $item)
				{
					$results[$depth][] = $item;
				}
			}
		}

		return $results;
	}

	public function render()
	{
		$this->__call('render');

		return implode('', $this->lastResults);
	}

	protected function getMenuObjectsFromHandlers()
	{
		$results = array();
		foreach($this->lastResults as $result)
		{
			foreach($result->getMenuObjects() as $item)
			{
				$results[] = $item;
			}
		}

		return $results;
	}

	protected function getItemsFromItemLists()
	{
		$results = array();
		foreach($this->lastResults as $result)
		{
			foreach($result->getItems() as $item)
			{
				$results[] = $item;
			}
		}

		return $results;
	}

	protected function getMatchFromResults()
	{
		foreach($this->lastResults as $result)
		{
			if($result !== false)
			{
				return $result;
			}
		}

		return false;
	}

	public function getCombinedResult()
	{
		return call_user_func_array('array_merge', $this->lastResults);
	}

	protected function getHandlerFromResults()
	{
		return new Handler($this->getMenuObjectsFromHandlers());
	}

	protected function getItemListFromResults()
	{
		return new ItemList($this->getItemsFromItemLists());
	}

	public function __call($method, $parameters = array())
	{
		$results = array();
		foreach($this->menuObjects as &$menuObject)
		{
			$result = call_user_func_array(array($menuObject, $method), $parameters);

			if(in_array($method, static::$override))
			{
				$menuObject = $result;
			}

			$results[] = $result;
		}

		$this->lastResults = $results;

		foreach (static::$responses as $responseMethod => $methods)
		{
			if(in_array($method, $methods))
			{
				return $this->$responseMethod();
			}
		}

		return $this;
	}

	public function __toString()
	{
		return $this->render();
	}

}
