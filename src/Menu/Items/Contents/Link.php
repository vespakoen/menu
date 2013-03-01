<?php
namespace Menu\Items\Contents;

use Menu\HTML;
use Menu\Traits\Content;
use HtmlObject\Traits\Helpers;
use Underscore\Methods\ArraysMethods;
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

  /**
   * The default element
   *
   * @var string
   */
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
    $listPrefix = $this->getParent(1)->getOption('item_list.prefix');
    if (!is_null($listPrefix)) {
      $segments[] = $listPrefix;
    }

    // Prepend parent item prefix
    $prefixParents = $this->getParent(1)->getOption('item_list.prefix_parents');
    if ($prefixParents) {
      $segments += $this->getParentItemsUrls();
    }

    // Prepend handler prefix
    $prefixHandler = $this->getParent(1)->getOption('item_list.prefix_handler');
    if ($prefixHandler) {
      $segments[] = $this->getHandlerSegment();
    }

    $segments[] = $this->href;

    return implode('/', $segments);
  }

  /**
   * Get all the parent Items
   *
   * @return array An array of Items
   */
  protected function getParentItems()
  {
    $parents = array();

    $list = $this->getParent(1);

    while ($list->getParent()) {
      $parents[] = $list->getParent();

      $parent = $list->getParent(1);
      $list = $parent ? $list->getParent(1) : null;
    }

    return array_reverse($parents);
  }

  /**
   * Get all the parent Lists
   *
   * @return array An array of Lists
   */
  protected function getParentLists()
  {
    $parents = array();

    $list   = $this->getParent(1);
    $parent = $list->getParent() ?: null;
    $parents[] = $list;

    while ($list and $parent) {
      $parents[] = $parent;
      $list = $parent ?: null;
    }

    return array_reverse($parents);
  }

  /**
   * Get the handler of the Menu containing this link
   *
   * @return string
   */
  protected function getHandlerSegment()
  {
    $parentLists = $this->getParentLists();
    $handler = array_pop($parentLists);

    return $handler->name;
  }

  /**
   * Get the URL of the parent Items
   *
   * @return array
   */
  protected function getParentItemsUrls()
  {
    $urls = array();

    $parentItems = $this->getParentItems();
    $parentContents = ArraysMethods::each($parentItems, function($item) {
      return $item->value;
    });

    foreach ($parentContents as $content) {
      if (
        $content->isLink() and
        !$content->isSpecialUrl() and
        !is_null($content->href)) {
          $urls[] = $content->href;
      }
    }

    return $urls;
  }
}
