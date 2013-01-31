<?php
/**
 * Link
 *
 * A Link in an Item
 */
namespace Menu\Items\Contents;

use \Menu\HTML;
use \Menu\Traits\Content;

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
    return HTML::to($this->url, $this->value, $this->attributes);
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
}