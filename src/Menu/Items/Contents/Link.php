<?php
/**
 * Link
 *
 * A Link in an Item
 */
namespace Menu\Items\Contents;

use \Menu\HTML;
use \Menu\Traits\Content;
use \Underscore\Types\String;

class Link extends Content
{
  /**
   * The link URL
   *
   * @var string
   */
  protected $url;

  /**
   * The link's element
   *
   * @var string
   */
  protected $element = 'a';

  /**
   * URL segments that you want to hide from the
   * generated URL when using ->prefix_parents()
   *
   * @var array
   */
  private $hidden = array(
    '#',
    'javascript:;',
    'javascript:void(0);',
    'javascript:void(0)',
  );

  /**
   * Build a new Link
   *
   * @param string $url        Its URL
   * @param string $text       Its text
   * @param array  $attributes Facultative attributes
   */
  public function __construct($url, $value = null, $attributes = array())
  {
    $this->url        = $url;
    $this->value      = $value;
    $this->attributes = $attributes;
  }

  /**
   * Render the Link
   *
   * @return string
   */
  public function render()
  {
    return HTML::to($this->getEvaluatedUrl(), $this->value, $this->attributes);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the content type
   *
   * @return boolean
   */
  public function isLink()
  {
    return true;
  }

  /**
   * Get the Link's url
   *
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////// PREFIXES AND SEGMENTS //////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the evaluated URL based on the prefix settings
   *
   * @return string
   */
  public function getEvaluatedUrl()
  {
    $segments = array();

    // If the URL is just an hash, don't do shit
    if (!$this->item or String::startsWith($this->url, '#')) {
      return $this->url;
    }

    // Prepend list prefix
    if (!is_null($this->item->getList()->prefix)) {
      $segments[] = $this->item->getList()->prefix;
    }

    // Prepend parent item prefix
    if ($this->item->getList()->prefixParents) {
      $segments += $this->get_parent_item_urls();
    }

    // Prepend handler prefix
    if ($this->item->getList()->prefixHandler) {
      $segments[] = $this->get_handler_segment();
    }

    $segments[] = $this->url;

    return implode('/', $segments);
  }

  /**
   * Get all the parent items of this item
   *
   * @return array
   */
  public function get_parents()
  {
    $parents = array();

    $list = $this->item->getList();

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

    $list = $this->item->getList();

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

    $list = $this->item->getList();

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
      if ($item->content->isLink() && ! is_null($item->content->getUrl()) && !in_array($item->content->getUrl(), $this->hidden)) {
        $urls[] = $item->content->getUrl();
      }
    }

    return $urls;
  }
}