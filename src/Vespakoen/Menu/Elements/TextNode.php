<?php namespace Vespakoen\Menu\Elements;

class TextNode {

	use HTMLElement;

	public $content = '';

	public function __construct($content)
	{
		$this->content = $content;

		$this->setElement(null);
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

}
