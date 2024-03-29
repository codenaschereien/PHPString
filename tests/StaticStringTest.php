<?php

require_once 'PHPUnit/Autoload.php';

require_once dirname(__FILE__) . '/../StaticString.php';

/**
 * Test class for StaticString.
 * Generated by PHPUnit on 2011-11-19 at 18:31:40.
 */
class StaticStringTest extends PHPUnit_Framework_TestCase {

  /**
   * @var StaticString
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp() {

  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown() {
    
  }

  /**
   */
  public function test__callStatic() {
    $rawStringOriginal = 'This is a String!';
    $rawStringChanged = 'This is a new String!';
    $this->assertInternalType('string', StaticString::setValue($rawStringOriginal, $rawStringChanged));
  }

}
