<?php
namespace Menu\Items\Contents;

use Menu\HTML;
use Menu\Traits\Content;
use HtmlObject\Traits\Helpers;
use Underscore\Methods\StringMethods;

/**
 * A Link in an Item
 */
class Link extends Content
{
  /**
   * URL segments that you want to hide from the
   * generated URL when using ->prefixParents()
   *
   * @var array
   */
  private $hidden = array(
    '#',
    'javascript:',
  );

  protected $element = 'a';

  /**
   * Build a new Link
   *
   * @param string $url        Its URL
   * @param string $value      Its text
   * @param array  $attributes Facultative attributes
   */
  public function __construct($url, $value = null, $attributes = array())
  {
    $this->attributes = $attributes;
    $this->value      = $value;
    $this->href       = $url;
  }

  /**
   * Render the Link
   *
   * @return string
   */
  public function render()
  {
    $href = $this->getEvaluatedUrl();
    $attributes = $this->attributes;

    // Don't compote URL if special URL
    if (!$this->isSpecialUrl()) {
      $href = HTML::getUrlGenerator()->to($href);
      unset($attributes['href']);
    }

    return '<a href="' .$href.'"'.Helpers::parseAttributes($attributes).'>'.$this->getContent().$this->close();
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
   * Whether the link is special or not
   *
   * @return boolean
   */
  public function isSpecialUrl()
  {
    foreach ($this->hidden as $hidden) {
      if (StringMethods::startsWith($this->href, $hidden)) return true;
    }

    return false;
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
    if (!$this->getParent() or $this->isSpecialUrl()) {
      return $this->href;
    }

    // Prepend list prefix
    $listPrefix = $this->getParent()->getParent()->getOption('item_list.prefix');
    if (!is_null($listPrefix)) {
      $segments[] = $listPrefix;
    }

    // Prepend parent item prefix
    $prefixParents = $this->getParent()->getParent()->getOption('item_list.prefix_parents');
    if ($prefixParents) {
      $segments += $this->get_parent_item_urls();
    }

    // Prepend handler prefix
    $prefixHandler = $this->getParent()->getParent()->getOption('item_list.prefix_handler');
    if ($prefixHandler) {
      $segments[] = $this->get_handler_segment();
    }

    $segments[] = $this->href;

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

    $list = $this->getParent()->getParent();

    while ( ! is_null($list->getParent())) {
      $parents[] = $list->getParent();

      $parent = $list->getParent()->getParent();
      $list = isset($parent) ? $list->getParent()->getParent() : null;
    }

    $parents = array_reverse($parents);

    return $parents;
  }

  public function get_parent_items()
  {
    $parents = array();

    $list = $this->getParent()->getParent();

    while ( ! is_null($list->getParent())) {
      $parents[] = $list->getParent();

      $parent = $list->getParent()->getParent();
      $list = isset($parent) ? $list->getParent()->getParent() : null;
    }

    $parents = array_reverse($parents);

    return $parents;
  }

  public function get_parent_lists()
  {
    $parents = array();

    $list = $this->getParent()->getParent();
    $parent = $list->getParent() ?: null;

    $parents[] = $list;

    while ($list and isset($parent) && ! is_null($parent)) {

      $parents[] = $parent;

      $list = isset($parent) ? $parent : null;
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
      if ($item->value->isLink() && ! is_null($item->value->href) && !$item->value->isSpecialUrl()) {
        $urls[] = $item->value->href;
      }
    }

    return $urls;
  }
}
