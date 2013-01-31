<?php
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
   * Build a new Link
   *
   * @param string $url        Its URL
   * @param string $text       Its text
   * @param array  $attributes Facultative attributes
   */
  public function __construct($url, $text = null, $attributes = array())
  {
    $this->url        = $url;
    $this->text       = $text;
    $this->attributes = $attributes;
  }

  /**
   * Render the Link
   *
   * @return string
   */
  public function render()
  {
    return HTML::to($this->url, $this->text, $this->attributes);
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