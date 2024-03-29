<?php

require_once 'PHPUnit/Autoload.php';

require_once dirname(__FILE__) . '/../String.php';
require_once dirname(__FILE__) . '/../StringIterator.php';

/**
 * Test class for StringIterator.
 * Generated by PHPUnit on 2011-11-19 at 18:31:42.
 */
class StringIteratorTest extends PHPUnit_Framework_TestCase {

  /**
   * @var StringIterator
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp() {
    $this->object = new StringIterator(new String('This is a string!'));
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown() {
    
  }

  public function testGetPosition() {
    $this->assertEquals(0, $this->object->getPosition());
    $this->object->setPosition(3);
    $this->assertEquals(3, $this->object->getPosition());
  }

  public function testSetPosition() {
    $this->object->setPosition(3);
    $this->assertEquals(3, $this->object->getPosition());
  }

  public function testGetString() {
    $this->assertInstanceOf('String', $this->object->getString());
    $this->assertEquals('This is a string!', $this->object->getString()->getValue());
  }

  public function testSetString() {
    $rawString = 'This is another string!';
    $this->object->setString(new String($rawString));
    $this->assertAttributeInstanceOf('String', 'string', $this->object);
    $this->assertEquals($rawString, $this->object->getString()->getValue());
  }

  public function testCurrent() {
    $this->assertEquals('T', $this->object->current());
    $this->object->next();
    $this->assertEquals('h', $this->object->current());
  }

  public function testKey() {
    $this->assertEquals(0, $this->object->key());
    $this->object->next();
    $this->assertEquals(1, $this->object->key());
  }

  public function testNext() {
    $this->assertEquals('T', $this->object->next());
    $this->assertEquals('h', $this->object->current());
  }

  public function testRewind() {
    $this->object->setPosition(5)->rewind();
    $this->assertEquals('T', $this->object->current());
  }

  public function testValid() {
    $this->assertTrue($this->object->valid());
    $lastPosition = $this->object->getString()->getLength();
    $this->object->setPosition($lastPosition);
    $this->assertFalse($this->object->valid());
  }

}
