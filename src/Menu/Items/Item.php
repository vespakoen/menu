<?php
/**
 * Item
 *
 * An Item in a list
 */
namespace Menu\Items;

use \Underscore\Types\Arrays;
use \Menu\Traits\MenuObject;
use \Menu\Helpers;
use \Menu\Html;
use \Menu\Menu;

class Item extends MenuObject
{
  /**
   * The list this item is in
   *
   * @var ItemList
   */
  public $list;

  /**
   * The item element
   * @var string
   */
  protected $element = 'li';

  /**
   * The item's content
   * @var Link/Raw
   */
  protected $content;

  /**
   * The children this item has
   *
   * @var ItemList
   */
  public $children;

  /**
   * The default render options for this item
   *
   * @var array
   */
  public $options = array(
    'activeClass'      => 'active',
    'activeChildClass' => 'active-child'
  );

  /**
   * Create a new item instance
   *
   * @param ItemList $list
   * @param string   $type
   * @param string   $text
   * @param ItemList $children
   * @param array    $options
   * @param string   $url
   *
   * @return void
   */
  public function __construct($list, $type, $text, $children = array(), $options = array(), $url = null)
  {
    $this->list     = $list;
    $this->children = $children;
    $this->options  = array_replace_recursive($this->options, $options);

    // Create content
    $this->content = ($type == 'link')
      ? new Contents\Link($url, $text)
      : new Contents\Raw($text);
  }

  /**
   * Render the item
   *
   * @param array
   *
   * @return string
   */
  public function render()
  {
    // Add the active classes
    $this->addActiveClasses($this->attributes);

    // Increment the render depth
    $this->options['renderDepth'] = $this->getOption('renderDepth', 0) + 1;

    // Render main content
    $content = $this->renderTabbed($this->content, $this->options['renderDepth'] + 1);

    // Render children if any
    if ($this->hasChildren()) {
      $content .=
        PHP_EOL.$this->children.
        str_repeat("\t", $this->options['renderDepth']);
    }

    $element = $this->element;
    $content = HTML::$element($content.PHP_EOL.str_repeat("\t", $this->options['renderDepth']), $this->attributes);

    return $this->renderTabbed($content, $this->options['renderDepth']);
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get all the parent items of this item
   *
   * @return array
   */
  public function get_parents()
  {
    $parents = array();

    $list = $this->list;

    while ( ! is_null($list->item)) {
      $parents[] = $list->item;

      $list = isset($list->item->list) ? $list->item->list : null;
    }

    $parents = array_reverse($parents);

    return $parents;
  }

  public function get_parent_items()
  {
    $parents = array();

    $list = $this->list;

    while ( ! is_null($list->item)) {
      $parents[] = $list->item;

      $list = isset($list->item->list) ? $list->item->list : null;
    }

    $parents = array_reverse($parents);

    return $parents;
  }

  public function get_parent_lists()
  {
    $parents = array();

    $list = $this->list;

    $parents[] = $list;

    while (isset($list->item->list) && ! is_null($list->item->list)) {
      $parents[] = $list->item->list;

      $list = isset($list->item->list) ? $list->item->list : null;
    }

    $parents = array_reverse($parents);

    return $parents;
  }

  public function get_handler_segment()
  {
    $parent_lists = $this->get_parent_lists();

    $handler = array_pop($parent_lists);

    return is_null($handler->name) ? '' : $handler->name;
  }

  public function get_parent_item_urls()
  {
    $urls = array();

    $parent_items = $this->get_parent_items();

    foreach ($parent_items as $item) {
      if ($item->content->isLink() && ! is_null($item->content->getUrl()) && $item->content->getUrl() !== '#') {
        $urls[] = $item->url;
      }
    }

    return $urls;
  }

  /**
   * Get the evaluated URL based on the prefix settings
   *
   * @return string
   */
  public function getEvaluatedUrl()
  {
    $segments = array();

    if ($this->getUrl() == '#') {
      return $this->getUrl();
    }

    if ( ! is_null($this->list->prefix)) {
      $segments[] = $this->list->prefix;
    }

    if ($this->list->prefixParents) {
      $segments = $segments + $this->get_parent_item_urls();
    }

    if ($this->list->prefixHandler) {
      $segments[] = $this->get_handler_segment();
    }

    $segments[] = $this->getUrl();

    return implode('/', $segments);
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
    return $this->getEvaluatedUrl() == $this->getRequest()->fullUrl();
  }

  /**
   * Check if this item has children
   *
   * @return boolean
   */
  public function hasChildren()
  {
    return !is_null($this->children) and !empty($this->children);
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

    foreach ($this->children->getItems() as $child) {
      if ($child->isActive()) {
        return true;
      }

      if ($child->hasChildren()) {
        return $child->hasActiveChild();
      }
    }
  }

  /**
   * Get the url of the Item's content
   *
   * @return string
   */
  protected function getUrl()
  {
    return $this->content->isLink() ? $this->content->getUrl() : null;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get an option from the options array
   *
   * @param string $option The option key
   *
   * @return string Its content
   */
  private function getOption($option, $fallback = null)
  {
    return Arrays::get($this->options, $option, $fallback);
  }

  /**
   * Add the various active classes to an array of attributes
   *
   * @param  array $attributes
   * @return array
   */
  private function addActiveClasses($attributes)
  {
    if ($this->isActive()) {
      $attributes = Helpers::addClass($attributes, $this->getOption('activeClass'));
    }

    if ($this->hasActiveChild()) {
      $attributes = Helpers::addClass($attributes, $this->getOption('activeChildClass'));
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
    // Defer to Illuminate/Request if possible
    if (class_exists('App')) {
      return App::make('Request');
    }

    return Menu::getContainer('Symfony\Component\HttpFoundation\Request');
  }

}
