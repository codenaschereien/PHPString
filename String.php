<?php

require_once('StringIterator.php');
require_once('StringException.php');

/**
 * @author Martin Will
 * 
 */
class String implements IteratorAggregate, ArrayAccess {
  
  /**
   * @const bool
   */
  const OVERWRITE_FOLLING_CHARACTERS = true;
  
  /**
   * @const bool
   */
  const DO_NOT_OVERWRITE_FOLLING_CHARACTERS = false;
  
  /**
   * @const int
   */
  const OFFSET_NORMAL = -1;
  
  /**
   * @const int
   */
  const OFFSET_EXTENDED = 0;
  
  /**
   * Default encoding of this class.
   * @const int
   */
  const DEFAULT_ENCODING = 'UTF-8';
  
  /**
   * @const string
   */
  const LINE_ENDING_N = "\n";
  
  /**
   * @const string
   */
  const LINE_ENDING_RN = "\r\n";
  
  /**
   * @const string
   */
  const LINE_ENDING_R = "\r";
  
  /**
   * Stores the real string value.
   * @var string 
   */
  private $value = null;
  
  /**
   * Stores the encoding which is used for all multibyte functions like mb_xyz().
   * @var string
   */
  private $encoding = null;
  
  /**
   * Stores the default encoding which will be used for all new strings.
   * @var string 
   */
  private static $defaultEncoding = self::DEFAULT_ENCODING;


  /**
   * @param string|String $value
   * @param string $encoding 
   */
  public function __construct($value = null, $encoding = null) {
    if(null !== $value) {
      $this->setValue($value);
    }
    
    if(null === $encoding) {
      $encoding = $this->getDefaultEncoding();
    }
    $this->setEncoding($encoding);
  }
  
  /**
   * return string
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @param string|String
   * @return String
   */
  public function setValue($value) {   
    if(is_string($value)) {
      $this->value = $value;
    } elseif($value instanceof String) {
      $this->value = $value->getValue();
    } else {
      throw new StringException('Invalid value given: ' . $value);
    }

    return $this;
  }
  
  /**
   * Get string encoding which is used for all multibyte functions like mb_xyz().
   * @return string 
   */
  public function getEncoding() {
    return $this->encoding;
  }

  /**
   * Set string encoding which is used for all multibyte functions like mb_xyz().
   * @param string $encoding 
   */
  public function setEncoding($encoding) {
    $this->encoding = $encoding;
  }
   
  /**
   * Return the current string length
   * @return int 
   */
  public function getLength() {
    return mb_strlen($this->getValue(), $this->getEncoding());
  }

  /**
   * Create an Iterator
   * @return StringIterator 
   */
  public function getIterator() {
    return new StringIterator($this);
  }
  
  /**
   * Check if a given offset exists in the string
   * @param int $offset
   * @return bool 
   */
  public function offsetExists($offset) {
    return $this->offsetExistsExtended($offset);
  }

  /**
   * Check if the given offset is valid. $byteCorrection allows you to modify acceptance. E.g. if you want
   * to increase the possible offset, to be able to append characters at the end of the string.
   * @param int $offset
   * @param bool $byteCorrection
   * @return String 
   */
  private function offsetExistsExtended($offset, $byteCorrection = self::OFFSET_NORMAL) {
    return ctype_digit("$offset") && $offset >= 0 && ($offset < $this->getLength() + $byteCorrection);
  }
  
  /**
   * Check if an offset exists. If not throw an exception.
   * @param int $offset
   * @param bool $byteCorrection
   * @return String 
   */
  private function checkOffset($offset, $byteCorrection = self::OFFSET_NORMAL) {
    if(!$this->offsetExistsExtended($offset, $byteCorrection)) {
      throw new StringException('Invalid offset given: ' . $offset);
    }
    return $this;
  }
  
  /**
   * Get character of the given position
   * @param int $offset
   * @return String 
   */
  public function offsetGet($offset) {
    $this->checkOffset($offset);
    $value = $this->getValue();
    return new String(mb_substr($value, $offset, 1, $this->getEncoding()));
  }

  /**
   * Set character at a given position
   * @param int $offset
   * @param string|String $char
   * @return String 
   */
  public function offsetSet($offset, $char) {
    
    $this->checkOffset($offset, self::OFFSET_EXTENDED);
        
    if(is_string($char)) {
      if(mb_strlen($char, $this->getEncoding()) > 1) {
        throw new StringException('Invalid character given: ' . $char);
      }
    } elseif($char instanceof String) {
      if($char->getLength() > 1) {
        throw new StringException('String contains more than one character: ' . $char);
      }
    } else {
      throw new StringException('Invalid character given: ' . $char);
    }    
    
    $value = $this->getValue();
    $this->insert($char, $offset, self::OVERWRITE_FOLLING_CHARACTERS);
    
    return $this;
  }

  /**
   * Unset character at the given position.
   * @param int $offset
   * @return String 
   */
  public function offsetUnset($offset) {
    $this->checkOffset($offset);
    $value = $this->getValue();

    if(0 == $offset) {
      $prefix = '';
      $postfix = mb_substr($value, 1, null, $this->getEncoding()); ###########Achtung Kontrolle!!!!!!!!!!!!
    } elseif($this->getLength() - 1 == $offset) {
      $prefix = mb_substr($value, 0, $offset, $this->getEncoding());
      $postfix = '';
    } else {
      $prefix = mb_substr($value, 0, $offset, $this->getEncoding());
      $postfix = mb_substr($value, mb_strlen($prefix, $this->getEncoding()) + 1);
    }
        
    $this->setValue($prefix . $postfix);
    return $this;
  }
  
  /**
   * Insert a string into the current string. Use the third parameter to overwrite following characters
   * @param string|String $string
   * @param int $offset
   * @param bool $overwrite
   * @return String 
   */
  public function insert($string, $offset, $overwrite = self::DO_NOT_OVERWRITE_FOLLING_CHARACTERS) {
    $this->checkOffset($offset, self::OFFSET_EXTENDED);
    if($string instanceof String) {
      $string = $string->getValue();
    }
    if($overwrite) {
      $prefix = mb_substr($this->getValue(), 0, $offset, $this->getEncoding());
      $postfix = mb_substr(
        $this->getValue(), 
        $offset + mb_strlen($string, $this->getEncoding()), 
        $this->getLength(), //rest of the string can never be longer than this
        $this->getEncoding()
      );
    } else {
      $value = $this->getValue();
      $prefix = mb_substr($value, 0, $offset, $this->getEncoding());
      $postfix = mb_substr($value, $offset, $this->getLength(), $this->getEncoding());
    }
    $this->setValue($prefix . $string . $postfix);
    
    return $this;
  }
  
  /**
   * Append a string at the end of the current string
   * @param string|String $string
   * @return String 
   */
  public function append($string) {
    $this->insert($string, $this->getLength());
    return $this;
  }
  
  /**
   * Return the real used number of bytes
   * @return string 
   */
  public function countBytes() {
    return strlen($this->getValue());
  }
  
  /**
   * Return an array of all bytes of the string
   * @return array 
   */
  public function getBytes() {
    $ret = array();
    $value = $this->getValue();
    for($i = 0; $i < strlen($value); $i++) {
      $ret[] = $value[$i];
    }
    return $ret;
  }
  
  /**
   * Alias of getValue()
   * @return string
   */
  public function __toString() {
    return $this->getValue();
  }

  /**
   * @param int $padLength
   * @param string|String $padString
   * @param int $padType
   * @return string
   */
  private function pad($padLength, $padString =' ', $padType = STR_PAD_RIGHT) {
    $value = $this->getValue();
    return new String(str_pad(
      $value, 
      $padLength + strlen($value) - mb_strlen($value, $this->getEncoding())), 
      $padString, 
      $padType
    );
  }
  
  /**
   * Pad the string from the left side using the given character
   * @param int $padLength
   * @param string|String $padString
   * @return String 
   */
  public function lpad($padLength, $padString = ' ') {
    $this->setValue($this->pad($padLength, $padString, STR_PAD_LEFT));
    return $this;
  }
  
  /**
   * Pad the string from the right side using the given character
   * @param int $padLength
   * @param string/String $padString
   * @return String 
   */
  public function rpad($padLength, $padString = ' ') {
    $this->setValue($this->pad($padLength, $padString, STR_PAD_RIGHT));
    return $this;
  }
  
  /**
   *
   * @param string|String $string
   * @param int $offset
   * @return bool 
   */
  public function startsWith($string, $offset = 0) {
    $this->checkOffset($offset);
    return mb_substr($this->getValue(), $offset, mb_strlen($string), $this->getEncoding()) == $string;
  }
  
  /**
   *
   * @param string|String $string
   * @return bool 
   */
  public function endsWith($string) {
    return mb_substr(
      $this->getValue(), 
      $this->getLength() - mb_strlen($string), 
      $this->getLength(), //String cannot be longer than this
      $this->getEncoding()
    ) == $string;
  }
  
  /**
   * Detects line endings by counting the most used line ending
   * @param string $str
   * @param string $encoding for $str (e.g. UTF-8)
   * @return string e.g \n
   */
  public function detectLineBreaks() {
    $ret = "\r\n";
    $encoding = $this->getEncoding();

    $backslashRNCount = mb_substr_count($this->__toString(), "\r\n", $encoding);
    $backslashRCount = mb_substr_count($this->__toString(), "\r", $encoding) - $backslashRNCount;
    $backslashNCount = mb_substr_count($this->__toString(), "\n", $encoding) - $backslashRNCount;
    
    if($backslashRCount > $backslashNCount && $backslashRCount > $backslashRNCount) {
      $ret = "\r";
    } elseif($backslashNCount > $backslashRCount && $backslashNCount > $backslashRNCount) {
      $ret = "\n";
    }

    return $ret;
  }
  
  /**
   * Normalize Line endings to a given line ending.
   * @param string $lineEnding
   * @return String
   */
  public function normalizeLineBreaks($lineEnding = self::LINE_ENDING_N) {
    switch($lineEnding) {
      case self::LINE_ENDING_N:
        $this->setValue(str_replace(self::LINE_ENDING_RN, self::LINE_ENDING_N, $this->getValue()));
        $this->setValue(str_replace(self::LINE_ENDING_R, self::LINE_ENDING_N, $this->getValue()));
        break;
      case self::LINE_ENDING_R:
        $this->setValue(str_replace(self::LINE_ENDING_RN, self::LINE_ENDING_R, $this->getValue()));
        $this->setValue(str_replace(self::LINE_ENDING_N, self::LINE_ENDING_R, $this->getValue()));
        break;
      case self::LINE_ENDING_RN:
        $this->setValue(str_replace(self::LINE_ENDING_RN, self::LINE_ENDING_N, $this->getValue()));
        $this->setValue(str_replace(self::LINE_ENDING_R, self::LINE_ENDING_N, $this->getValue()));
        $this->setValue(str_replace(self::LINE_ENDING_N, self::LINE_ENDING_RN, $this->getValue()));
        break;
    }
    return $this;
  }
  
  /**
   * Set the default encoding which will automatically be set for each new String object.
   * @param string $encoding 
   */
  public static function setDefaultEncoding($encoding) {
    self::$defaultEncoding = $encoding;
  }
  
  /**
   * Get the default encoding which will automatically be used for each new String object.
   * @return string 
   */
  public static function getDefaultEncoding() {
    return self::$defaultEncoding;
  }

  /**
   * Create a new String object using a certain encoding. Use this function for smaller code (see example)
   * @example String::factory('test')->method1()->method2()->getValue(); instead of $s = new String('test')...
   * @param string|String $value
   * @param string $encoding
   * @return String 
   */
  public static function factory($value, $encoding=null) {
    return new String($value, $encoding);
  }
  
}

