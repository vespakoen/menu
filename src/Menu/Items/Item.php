<?php
namespace Menu\Items;

use \Menu\Helpers;
use \Menu\Html;
use \Menu\Menu;

class Item
{
  /**
   * The list this item is in
   *
   * @var ItemList
   */
  public $list;

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
    'active_class'       => 'active',
    'active_child_class' => 'active-child'
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
  public function __construct($list, $type, $text, $children, $options, $url = null)
  {
    $this->list = $list;
    $this->type = $type;
    $this->text = $text;
    $this->children = $children;
    $this->options = array_replace_recursive($this->options, $options);
    $this->url = $url;
  }

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

    if ($this->list->prefix_parents) {
      $segments = $segments + $this->get_parent_item_urls();
    }

    if ($this->list->prefix_handler) {
      $segments[] = $this->get_handler_segment();
    }

    $segments[] = $this->url;

    return implode('/', $segments);
  }

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

  /**
   * Check if this item is active
   *
   * @return boolean
   */
  public function is_active()
  {
    return $this->get_url() == $this->getRequest()->fullUrl();
  }

  /**
   * Check if this item has children
   *
   * @return boolean
   */
  public function has_children()
  {
    return ! is_null($this->children);
  }

  /**
   * Check if this item has an active child
   *
   * @return boolean
   */
  public function has_active_child()
  {
    if ( ! $this->has_children()) {
      return false;
    }

    foreach ($this->children->items as $child) {
      if ($child->is_active()) {
        return true;
      }

      if ($child->has_children()) {
        return $child->has_active_child();
      }
    }
  }

  /**
   * Render the item
   *
   * @param array
   *
   * @return string
   */
  public function render($options = array())
  {
    unset($options['list_attributes'], $options['link_attributes'], $options['list_element']);

    $options = array_replace_recursive($this->options, $options);

    extract($options);

    if ($this->is_active()) {
      $item_attributes = Helpers::merge_attributes($item_attributes, array('class' => $active_class));
    }

    if ($this->has_active_child()) {
      $item_attributes = Helpers::merge_attributes($item_attributes, array('class' => $active_child_class));
    }

    $children_options = $options;
    $children_options['render_depth']++;
    unset($children_options['item_attributes'], $children_options['item_element']);

    $children = $this->has_children() ? PHP_EOL.$this->children->render($children_options).str_repeat("\t", $render_depth) : '';

    if ($this->type == 'raw') {
      $content = $this->text;
    } else {
      $content = PHP_EOL.str_repeat("\t", $render_depth + 1).HTML::to($this->get_url(), $this->text, $link_attributes);
    }

    return str_repeat("\t", $render_depth).Html::$item_element($content.$children.PHP_EOL.str_repeat("\t", $render_depth), $item_attributes).PHP_EOL;
  }

}
