<?php
namespace Menu\Items\Contents;

use Menu\HTML;
use Menu\Traits\Content;

/**
 * A Link in an Item
 */
class Link extends Content
{
  /**
   * The link URL
   *
   * @var string
   */
  protected $url;

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
   * @param string $value      Its text
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
    // Create link and correct potential special URLs
    $link = HTML::to($this->getEvaluatedUrl(), $this->value, $this->attributes);
    if ($this->isSpecialUrl()) {
      $link = preg_replace('/href="([^"]+)"/', 'href="' .$this->getEvaluatedUrl(). '"', $link);
    }

    return $link;
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
    return in_array($this->url, $this->hidden);
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
    if (!$this->getParent() or $this->isSpecialUrl()) {
      return $this->url;
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
      if ($item->value->isLink() && ! is_null($item->value->getUrl()) && !$item->value->isSpecialUrl()) {
        $urls[] = $item->value->getUrl();
      }
    }

    return $urls;
  }
}
