<?php

class String {
  private $value = null;

  
  public function __construct($value=null) {
    if(null !== $value) {
      $this->setValue($value);
    }
  }

  /**
   * return string
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @param string
   * @return String
   */
  public function setValue($value) {
    $this->value = $value;
    return $this;
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->getValue();
  }

  /**
   * @param int ¤padLength
   * @param string $padString
   * @param int $padType
   * @return string
   */
  public function pad($padLength, $padString =' ', $padType = STR_PAD_RIGHT) {
    return new String(self::spad($this->getValue(), $padLength, $padString, $padType));
  }

  /**
   * @param string $input
   * @param int ¤padLength
   * @param string $padString
   * @param int $padType
   * @return string
   */
  public static function spad($input, $padLength, $padString =' ', $padType = STR_PAD_RIGHT) {
    return str_pad($input, $padLength + strlen($input) - mb_strlen($input), $padString, $padType);
  }
}

