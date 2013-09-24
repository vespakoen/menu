<?php namespace Vespakoen\Menu\Elements;

trait MenuElement {

	public $activeClass;

	public $activeChildClass;

	public function setActiveClass($activeClass)
	{
		$this->activeClass = $activeClass;
	}

	public function setActiveChildClass($activeChildClass)
	{
		$this->activeChildClass = $activeChildClass;
	}

	public function getActiveClass()
	{
		return $this->activeClass;
	}

	public function getActiveChildClass()
	{
		return $this->activeChildClass;
	}

}
