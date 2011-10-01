<?php

require_once('String.php');

final class StringIterator implements Iterator {
  private $position;
  private $string;
  
  function __construct(String $string) {
    $this->string = $string;
  }
  
  public function getPosition() {
    return $this->position;
  }

  public function setPosition($position) {
    $this->position = $position;
    return $this;
  }
  
  public function getString() {
    return $this->string;
  }

  public function setString($string) {
    $this->string = $string;
  }

  public function current() {
    $value = $this->getString()->getValue();
    return $value[$this->getPosition()];
  }

  public function key() {
    return $this->getPosition();
  }

  public function next() {
    $this->setPosition($this->getPosition() + 1);
    return $this->current();
  }

  public function rewind() {
    $this->setPosition(0);
  }

  public function valid() {
    return $this->getPosition() < $this->getString()->getLength();
  }

}

