<?php
namespace Menu;

class Uri
{
  /**
   * Get the URI for the current request.
   *
   * @return string
   */
  public static function current()
  {
    return static::format(Request::getPathInfo());
  }

  /**
   * Format a given URI.
   *
   * @param  string  $uri
   * @return string
   */
  protected static function format($uri)
  {
    return trim($uri, '/') ?: '/';
  }

}