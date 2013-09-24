<?php namespace Vespakoen\Menu;

use Vespakoen\Menu\Contracts\Renderable;
use Vespakoen\Menu\Elements\MenuElement;
use Vespakoen\Menu\Elements\HTMLElement;

class Item implements Renderable {

	use MenuElement;

	use HTMLElement {
		setChildren as originalSetChildren;
	}

	public $renderable;

	public function __construct(Renderable $renderable, $itemList = null)
	{
		$this->renderable = $renderable;
		$this->addChild($renderable);

		if(is_null($itemList))
		{
			$itemList = new ItemList();
		}
		$this->setItemList($itemList);
		
		$this->setElement('li');
	}

	public function setItemList($itemList)
	{
		$this->itemList = $itemList;
	}

	public function getItemList()
	{
		return $this->itemList;
	}

	public function setRenderable(Renderable $renderable)
	{
		return $this->renderable = $renderable;
	}

	public function getRenderable()
	{
		return $this->renderable;
	}

	public function setChildren($children)
	{
		if($children instanceof ItemList)
		{
			$children = $children->getChildren();
		}

		$this->originalSetChildren($children);

		return $this;
	}

	public function hasChildren()
	{
		$children = $this->getItemList()
			->getChildren();

		return count($children) > 0;
	}

	public function findByName($name)
	{
		$children = $this->getChildren();
		
		$itemLists = $children->getAllItemLists();
		foreach($itemLists as $itemList)
		{
			if($itemList->getName() == $name)
			{
				return $itemList;
			}
		}

		return false;
	}

	public function beforeRender()
	{
		// if($this->isActive())
		// {
			
		// }

		$itemList = $this->getItemList();
		if(count($itemList->getChildren()) > 0)
		{
			$this->addChild($itemList);
		}
	}

}
