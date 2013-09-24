<?php namespace Vespakoen\Menu\Elements;

trait HTMLElement {

	public $element = 'div';

	public $attributes = array();

	public $parent;

	public $children = array();

	public function addChild($child)
	{
		$child->setParent($this);

		$this->children[] = $child;

		return $this;
	}

	public function setChildren($children = array())
	{
		$this->children = array();

		foreach($children as $child)
		{
			$this->addChild($child);
		}

		return $this;
	}

	public function getChildren()
	{
		return $this->children;
	}

	public function setParent($parent)
	{
		$this->parent = $parent;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function setElement($element)
	{
		$this->element = $element;
	}

	public function getElement()
	{
		return $this->element;
	}

	public function addClass($class)
	{
		$classes = $this->getClasses();

		$classes[] = $class;

		$this->setClasses($classes);

		return $this;
	}

	public function setClasses($classes)
	{
		$this->attributes['class'] = implode(' ', $classes);
	}

	public function getClasses()
	{
		if( ! array_key_exists('class', $this->attributes))
		{
			return array();
		}

		return explode(' ', $this->attributes['class']);
	}

	public function setId($id)
	{
		$this->attributes['id'] = $id;
	}

	public function getId()
	{
		return $this->attributes['id'];
	}

	public function setAttribute($key, $value)
	{
		$this->attributes[$key] = $value;
	}

	public function setAttributes($attributes)
	{
		$this->attributes = $attributes;
	}

	public function getAttribute($key)
	{
		return array_get($this->attributes, $key);
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function setContent($content)
	{
		$textNode = new TextNode($content);

		$this->setChildren(array($textNode));

		return $this;
	}

	public function getContent()
	{
		$content = '';
		foreach($this->getChildren() as $child)
		{
			$content .= $child->render();
		}

		return $content;
	}

	public function getFirstChild()
	{
		return array_get($this->children, 0, null);
	}

	public function beforeRender()
	{
	}

	public function render()
	{
		$this->beforeRender();

		extract((array) $this);

		$processedAttributes = array();
		foreach($attributes as $key => $value)
		{
			$processedAttributes[] = $key.'="'.$value.'"';
		}

		$attributesString = count($processedAttributes) ? ' '.implode(' ', $processedAttributes) : '';

		$content = $this->getContent();

		if( ! is_null($element))
		{
			$content = '<'.$element.$attributesString.'>'.$content.'</'.$element.'>';
		}

		return $content;
	}

}
