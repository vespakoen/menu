<?php
/**
 * Helpers
 *
 * Various helpers used troughout the classes
 */
namespace Menu;

use Underscore\Types\Methods;

class Helpers
{
  /**
   * Merges two arrays of attributes together
   *
   * @param  array $array1
   * @param  array $array2
   * @return array         A merged array of attributes
   */
  public static function mergeAttributes($array1, $array2)
  {
    $array = $array1;
    foreach ($array2 as $key => $value) {
      if($key !== 'class') continue;

      if (array_key_exists($key, $array1)) {
        $array[$key] = $array1[$key] .= ' '.$array2[$key];
      } else {
        $array[$key] = $array2[$key];
      }
    }

    return $array;
  }
}
