<?php
namespace Menu\Items\Contents;

use HtmlObject\Traits\Helpers;
use HtmlObject\Link as HtmlLink;
use Menu\Menu;
use Menu\Traits\Content;
use Underscore\Methods\StringMethods;

/**
 * A Link in an Item
 */
class Link extends HtmlLink
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
   * The link's URL
   *
   * @var string
   */
  protected $href;

  /**
   * An array of properties to be injected as attributes
   *
   * @var array
   */
  protected $injectedProperties = array('href');

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
   * Change the Link's URL
   *
   * @param string $href An URL
   *
   * @return Link
   */
  public function href($href)
  {
    $this->href = $href;

    return $this;
  }

  /**
   * Get the link's href
   *
   * @return string the href
   */
  public function getUrl()
  {
    return $this->href;
  }

  /**
   * Break off a chain
   *
   * @return Item
   */
  public function stop()
  {
    return $this->getParent(1);
  }

  /**
   * Render the Link
   *
   * @return string
   */
  public function render()
  {
    $this->href = $this->getEvaluatedUrl();

    // Don't compote URL if special URL
    if (!$this->isSpecialUrl()) {
      $this->href = Menu::getContainer()->bound('url')
        ? Menu::getContainer('url')->to($this->href)
        : $this->href;
    }

    return parent::render();
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

    foreach ($this->getParentItems() as $item) {
      if (!is_object($item)) continue;

      if (
        $item->value->isLink() and
        !$item->value->isSpecialUrl() and
        !is_null($item->value->href)) {
          $urls[] = $item->value->href;
      }
    }

    return $urls;
  }
}
