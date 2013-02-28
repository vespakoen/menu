<?php
namespace Menu\Items;

use Menu\Helpers;
use Menu\HTML;
use Menu\Menu;
use Menu\Traits\Content;
use Menu\Traits\MenuObject;

/**
 * An Item in a list
 */
class Item extends MenuObject
{
  /**
   * Create a new item instance
   *
   * @param ItemList $list     The parent
   * @param Content  $value    The content
   * @param array    $children Facultative children ItemLists
   * @param array    $element  The Item element
   */
  public function __construct(ItemList $list, Content $value, $children = array(), $element = null)
  {
    $this->parent   = $list;
    $this->children = $children;
    $this->options  = $list->getOption();

    // Load various defaults
    $this->element = $element ?: $this->getOption('item.element');

    // Create content
    $this->value = $value->setParent($this);
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
    $this->attributes = $this->addActiveClasses($this->attributes);

    // Render children if any
    if ($this->hasChildren()) {
      $value .= $this->children->render($depth + 1);
    }

    // Facultatively render an element around the item
    $element = $this->element;
    if ($element) $value = HTML::$element($value, $this->attributes);
    return HTML::decode($value);
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
    return
      $this->getUrl() == trim($this->getRequest()->getPathInfo(), '/') or
      $this->getUrl() == $this->getRequest()->fullUrl() or
      $this->getUrl() == $this->getRequest()->url();
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
   *
   * @param  array $attributes
   * @return array
   */
  private function addActiveClasses($attributes)
  {
    if ($this->isActive()) {
      $attributes = Helpers::addClassTo($attributes, $this->getOption('item.active_class'));
    }

    if ($this->hasActiveChild()) {
      $attributes = Helpers::addClassTo($attributes, $this->getOption('item.active_child_class'));
    }

    return $attributes;
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
    return Menu::getContainer('Symfony\Component\HttpFoundation\Request');
  }

}
