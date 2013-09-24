<?php namespace Vespakoen\Menu\Items;

use Vespakoen\Menu\Contracts\Renderable;
use Vespakoen\Menu\Elements\HTMLElement;

class RawItem implements Renderable {

	use HTMLElement;

	public function __construct($content)
	{
		$this->content = $content;

		$this->setElement(null);
	}

}