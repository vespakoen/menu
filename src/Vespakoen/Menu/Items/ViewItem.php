<?php namespace Vespakoen\Menu\Items;

use Vespakoen\Menu\Contracts\Renderable;
use Vespakoen\Menu\Elements\HTMLElement;

class LinkItem implements Renderable {

	use HTMLElement;

	public function __construct($url, $label)
	{
		$this->setUrl($url);
		$this->setLabel($label);

		$this->setElement('a');
	}

	public function setUrl($url)
	{
		$this->setAttribute('href', $url);
	}

	public function setLabel($label)
	{
		$this->setContent($label);
	}

}