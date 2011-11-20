<?php

require_once('String.php');

/**
 * @author Martin Will
 */
abstract class StaticString {
  
  /**
   * Create a new String object and call a method on it
   * @param string $name
   * @param array $arguments
   * @return String 
   */
  public static function __callStatic($name, $arguments) {
    return call_user_func_array(array(String::factory(array_shift($arguments)), $name), $arguments)
      ->getValue();
  }
}