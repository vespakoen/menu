<?php
namespace Menu\Items;

use HtmlObject\Element;
use HtmlObject\Traits\Tag;

use Menu\Menu;
use Menu\Traits\Content;
use Menu\Traits\MenuObject;
use Menu\MenuHandler;

use Underscore\Methods\ArraysMethods;

/**
 * An Item in a list
 */
class Item extends MenuObject
{
  /**
   * Array of patterns to match the active state
   *
   * @var array
   */
  protected $patterns = array();

  /**
   * Create a new item instance
   *
   * @param ItemList $parent   The parent
   * @param Tag      $value    The content
   * @param array    $children Facultative children ItemLists
   * @param array    $element  The Item element
   */
  public function __construct(ItemList $parent, Tag $value, $children = null, $element = null)
  {
    $this->parent   = $parent;
    $this->children = is_null($children) ? new ItemList() : $children;
    $this->element = $element;

    // Create content
    $this->value = $value->setParent($this);
  }

  /**
   * Add an pattern to $patterns array
   *
   * @param string|array  $pattern The pattern
   * @param string        $name  Its name
   *
   */
  public function setActivePatterns($pattern, $name = null)
  {
    if (!$name) $name = sizeof($this->patterns);
    $this->patterns[$name] = $pattern;
  }

  /**
   * Break off a chain
   *
   * @return ItemList
   */
  public function stop()
  {
    return $this->getParent();
  }

  /**
   * Get the Item's content
   *
   * @return Content
   */
  public function getContent()
  {
    return $this->value;
  }

  /**
   * Set the Item's element
   *
   * @param string $element
   */
  public function setElement($element = null)
  {
    $this->setOption('item.element', $element);

    return $this;
  }

  /**
   * Get the Item's element
   *
   * @return string
   */
  public function getElement()
  {
    $element = $this->getOption('item.element');
    if(is_null($element))
    {
      $element = $this->element;
    }

    return $element;
  }

  /**
   * Render the item
   *
   * @param array
   *
   * @return string
   */
  public function render($depth = 0)
  {
    // Add the active classes
    $value = $this->value->render();
    $this->addActiveClasses();

    // Render children if any
    if ($this->hasChildren()) {
      $value .= $this->children->render($depth);
    }

    // Facultatively render an element around the item
    $element = $this->getElement();
    if ($element) $value = Element::create($element, $value, $this->attributes)->render();
    return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// PUBLIC INTERFACE /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Check if this item is active
   *
   * @return boolean
   */
  public function isActive()
  {
    if( ! $this->value->isLink()) {
      return false;
    }

    return
      trim($this->getUrl(), '/') == trim($this->getRequest()->getPathInfo(), '/') or
      $this->getUrl() == $this->getRequest()->fullUrl() or
      $this->getUrl() == $this->getRequest()->url() or
      $this->hasActivePatterns();
  }

  public function hasChildren()
  {
    return count($this->children->getChildren()) > 0;
  }

  /**
   * Check if this item has an active pattern
   *
   * @return boolean
   */
  protected function hasActivePatterns()
  {
    foreach ($this->patterns as $pattern) {
      $path = $this->getRequest()->getPathInfo();

      if (is_array($pattern)) {
        foreach ($pattern as $p)
          $isActive = preg_match('/'.$p.'/i', $path);
      } else {
        $isActive = preg_match('/'.$pattern.'/i', $path);
      }

      if($isActive) return true;
    }

    return false;
  }

  /**
   * Check if this item has an active child
   *
   * @return boolean
   */
  public function hasActiveChild()
  {
    if (!$this->hasChildren()) {
      return false;
    }

    foreach ($this->children->getChildren() as $child) {
      if ($child->isActive()) {
        return true;
      }

      if ($child->hasChildren()) {
        return $child->hasActiveChild();
      }
    }

    return false;
  }

  /**
   * Get the url of the Item's content
   *
   * @return string
   */
  protected function getUrl()
  {
    return $this->value->isLink() ? $this->value->getEvaluatedUrl() : null;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Add the various active classes to an array of attributes
   */
  private function addActiveClasses()
  {
    if ($this->isActive()) {
      $this->addClass($this->getOption('item.active_class'));
    }

    if ($this->hasActiveChild()) {
      $this->addClass($this->getOption('item.active_child_class'));
    }
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// DEPENDENCIES //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the Request instance
   *
   * @return Request
   */
  public function getRequest()
  {
    return Menu::getContainer('request');
  }

}
