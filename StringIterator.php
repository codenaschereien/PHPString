<?php

require_once('String.php');

/**
 * @author Martin Will
 */
final class StringIterator implements Iterator {
  
  /**
   * Store iterator position
   * @var int 
   */
  private $position;
  
  /**
   * Store string object
   * @var String 
   */
  private $string;
  
  /**
   *
   * @param String $string 
   */
  function __construct(String $string) {
    $this->string = $string;
  }
  
  /**
   *
   * @return int 
   */
  public function getPosition() {
    return $this->position;
  }

  /**
   *
   * @param int $position
   * @return StringIterator 
   */
  public function setPosition($position) {
    $this->position = $position;
    return $this;
  }
  
  /**
   *
   * @return string 
   */
  public function getString() {
    return $this->string;
  }

  /**
   *
   * @param String $string 
   */
  public function setString(String $string) {
    $this->string = $string;
  }

  /**
   *
   * @return string 
   */
  public function current() {
    $value = $this->getString()->getValue();
    return $value[$this->getPosition()];
  }

  /**
   * Returns the current position. (Alias for getPosition())
   * @return int 
   */
  public function key() {
    return $this->getPosition();
  }


  /**
   *
   * @return string 
   */
  public function next() {
    $this->setPosition($this->getPosition() + 1);
    return $this->current();
  }

  /**
   * @return StringIterator 
   */
  public function rewind() {
    $this->setPosition(0);
    return $this;
  }

  /**
   *
   * @return bool 
   */
  public function valid() {
    return $this->getPosition() < $this->getString()->getLength();
  }

}

