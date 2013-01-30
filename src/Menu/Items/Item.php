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
   * The type of this item (link / raw)
   *
   * @var string
   */
  public $type;

  /**
   * The text of this item
   *
   * @var string
   */
  public $text;

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
   * The URL for this item (without prefixes)
   *
   * @var string
   */
  public $url;

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
    $this->list = $list;
    $this->type = $type;
    $this->text = $text;
    $this->children = $children;
    $this->options = array_replace_recursive($this->options, $options);
    $this->url = $url;
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
      if ($item->type == 'link' && ! is_null($item->url) && $item->url !== '#') {
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
  public function get_url()
  {
    $segments = array();

    if ($this->url == '#') {
      return $this->url;
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

    $segments[] = $this->url;

    return implode('/', $segments);
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

    // Render children if any
    if (!$this->hasChildren()) $children = null;
    else $children =
      PHP_EOL.
      $this->children->render($childrenOptions).
      str_repeat("\t", $this->options['renderDepth']);

    if ($this->type == 'raw') {
      $content = $this->text;
    } else {
      $content = HTML::to($this->get_url(), $this->text, $this->getOption('linkAttributes'));
      $content = $this->renderTabbed($content, $this->options['renderDepth'] + 1);
    }

    $element = $this->element;
    $content = HTML::$element($content.$children.PHP_EOL.str_repeat("\t", $this->options['renderDepth']), $this->attributes);

    return $this->renderTabbed($content, $this->options['renderDepth']);
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
    return $this->get_url() == $this->getRequest()->fullUrl();
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

    foreach ($this->children->items as $child) {
      if ($child->isActive()) {
        return true;
      }

      if ($child->hasChildren()) {
        return $child->hasActiveChild();
      }
    }
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
