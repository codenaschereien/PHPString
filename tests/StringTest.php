<?php

require_once 'PHPUnit/Autoload.php';

require_once dirname(__FILE__) . '/../String.php';

/**
 * Test class for String.
 * Generated by PHPUnit on 2011-11-19 at 18:31:43.
 */
class StringTest extends PHPUnit_Framework_TestCase {

  /**
   * @var String
   */
  protected $object;
  
  const TEST_STRING = 'This is a string!';

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp() {
    $this->object = new String(self::TEST_STRING);
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown() {
    
  }

  public function testGetValue() {
    $this->assertInternalType('string', $this->object->getValue());
    $this->assertEquals(self::TEST_STRING, $this->object->getValue());
  }

  public function testSetValue() {
    $rawString = 'Teststring!';
    $this->object->setValue($rawString);
    $this->assertAttributeEquals($rawString, 'value', $this->object);
  }

  public function testGetEncoding() {
    $encoding = 'UTF-8';
    $this->object->setEncoding($encoding);
    $this->assertEquals($encoding, $this->object->getEncoding());
  }

  public function testSetEncoding() {
    $this->assertEquals($this->object->getDefaultEncoding(), $this->object->getEncoding());
  }

  public function testGetLength() {
    $this->object->setValue('öüä€test');
    $this->assertFalse(0 === $this->object->getLength());
    $this->assertFalse(7 === $this->object->getLength());
    $this->assertEquals(8, $this->object->getLength());
  }

  public function testGetIterator() {
    $this->assertInstanceOf('Iterator', $this->object->getIterator());
  }

  public function testOffsetExists() {
    $this->object->setValue('öäü€test');
    $this->assertTrue($this->object->offsetExists(0));
    $this->assertTrue($this->object->offsetExists(7));
    $this->assertFalse($this->object->offsetExists(8));
    $this->assertFalse($this->object->offsetExists(-1));
  }

  public function testOffsetGet() {
    $this->object->setValue('öäü€test');
    $this->assertEquals('ö', $this->object->offsetGet(0));
    $this->assertEquals('ä', $this->object->offsetGet(1));
    $this->assertEquals('€', $this->object->offsetGet(3));
    $this->assertEquals('t', $this->object->offsetGet(7));
  }

  public function testOffsetSet() {
    $this->object->setValue('öäü€test');
    $this->object->offsetSet(0, 'Ö');
    $this->assertEquals('Ö', $this->object->offsetGet(0));
    
    $this->object->offsetSet(1, 'Ä');
    $this->assertEquals('Ä', $this->object->offsetGet(1));
    
    $this->object->offsetSet(3, 'E');
    $this->assertEquals('E', $this->object->offsetGet(3));
    
    $this->object->offsetSet(7, 'T');
    $this->assertEquals('T', $this->object->offsetGet(7));
  }

  public function testOffsetUnset() {
    $this->object->setValue('öäü€test');
    $this->object->offsetUnset(0);
    $this->assertEquals('äü€test', $this->object->getValue());
    
    $this->object->setValue('öäü€test');
    $this->object->offsetUnset(3);
    $this->assertEquals('öäütest', $this->object->getValue());
    
    $this->object->setValue('öäü€test');
    $this->object->offsetUnset(7);
    $this->assertEquals('öäü€tes', $this->object->getValue());
  }

  public function testInsertNoOverwrite() {
    $this->object->setValue('öäütest');
    $this->assertEquals('€öäütest', $this->object->insert('€', 0)->getValue());
    
    $this->object->setValue('öäütest');
    $this->assertEquals('ö€äütest', $this->object->insert('€', 1)->getValue());
    
    $this->object->setValue('öäütest');
    $this->assertEquals('öäü€test', $this->object->insert('€', 3)->getValue());
    
    $this->object->setValue('öäütest');
    $this->assertEquals('öäütes€t', $this->object->insert('€', 6)->getValue());
    
    $this->object->setValue('öäütest');
    $this->assertEquals('öäütest€', $this->object->insert('€', 7)->getValue());
  }
  
  public function testInsertOverwrite() {
    $this->object->setValue('öäütest');
    $this->assertEquals('€äütest', $this->object->insert(
      '€', 
      0, 
      String::OVERWRITE_FOLLING_CHARACTERS)->getValue()
    );
    
    $this->object->setValue('öäütest');
    $this->assertEquals('ö€ütest', $this->object->insert(
      '€', 
      1, 
      String::OVERWRITE_FOLLING_CHARACTERS)->getValue()
    );
    
    $this->object->setValue('öäütest');
    $this->assertEquals('öäü€est', $this->object->insert(
      '€', 
      3, 
      String::OVERWRITE_FOLLING_CHARACTERS)->getValue()
    );
    
    $this->object->setValue('öäütest');
    $this->assertEquals('öäütes€', $this->object->insert(
      '€', 
      6, 
      String::OVERWRITE_FOLLING_CHARACTERS)->getValue()
    );
    
    $this->object->setValue('öäütest');
    $this->assertEquals('öäütest€', $this->object->insert(
      '€', 
      7, 
      String::OVERWRITE_FOLLING_CHARACTERS)->getValue()
    );
    
    $this->object->setValue('öäütest');
    $this->assertEquals('öäütes€€€', $this->object->insert(
      '€€€', 
      6, 
      String::OVERWRITE_FOLLING_CHARACTERS)->getValue()
    );
  }

  public function testAppend() {
    $this->object->setValue('öäü€test');
    $this->assertEquals('öäü€test€€€', $this->object->append('€€€'));
  }

  public function testCountBytes() {
    //using one-, two- and three-byte letters
    $this->object->setValue('öäü€test');
    $this->assertEquals(13, $this->object->countBytes());
  }

  public function testGetBytes() {
    $this->object->setValue('öäü€test');
    $this->assertInternalType('array', $this->object->getBytes());
    $bytes = array(
      chr(195),
      chr(182),
      chr(195),
      chr(164),
      chr(195),
      chr(188),
      chr(226),
      chr(130),
      chr(172), 
      't', 
      'e', 
      's', 
      't'
    );
    $this->assertEquals($bytes, $this->object->getBytes());
  }

  public function test__toString() {
    $this->assertInternalType('string', $this->object->__toString());
    $this->assertEquals(self::TEST_STRING, $this->object->__toString());
  }

  public function testLpad() {
    $this->object->lpad(20, '€');
    $this->assertEquals('€€€' . self::TEST_STRING, $this->object->__toString());
  }

  public function testRpad() {
    $this->object->rpad(20, '€');
    $this->assertEquals(self::TEST_STRING . '€€€', $this->object->__toString());
  }
  
  public function testMpadOdd() {    
    //test with odd length of 17
    $this->object->mpad(20, '€');
    $this->assertEquals('€' . self::TEST_STRING . '€€', $this->object->__toString());
  }
  
  public function testMpadEven() {
    //test with even length of 16
    $rawString = 'This is a string';
    $this->object->setValue($rawString);
    $this->object->mpad(20, '€');
    $this->assertEquals('€€' . $rawString . '€€', $this->object->__toString());
  }

  public function testStartsWith() {
    $this->object->setValue('öäü€test');
    $this->assertTrue($this->object->startsWith('öäü'));
  }

  public function testEndsWith() {
    $this->object->setValue('öäü€');
    $this->assertTrue($this->object->endsWith('€'));
  }

  public function testDetectLineBreaksUnix() {
    $this->object->setValue("This is a string\n with unix \nline breaks");
    $this->assertEquals("\n", $this->object->detectLineBreaks());
  }
  
  public function testDetectLineBreaksMax() {
    $this->object->setValue("This is a string\r with mac \rline breaks");
    $this->assertEquals("\r", $this->object->detectLineBreaks());
  }
  
  public function testDetectLineBreaksWin() {
    $this->object->setValue("This is a string\r\n with windows \r\nline breaks");
    $this->assertEquals("\r\n", $this->object->detectLineBreaks());
  }

  public function testNormalizeLineBreaksUnix() {
    $this->object->setValue("This is a string\n with unix \nline breaks");
    $this->object->normalizeLineBreaks(String::LINE_ENDING_N);
    $this->assertEquals("\n", $this->object->detectLineBreaks());
  }
   
  public function testNormalizeLineBreaksMax() {
    $this->object->setValue("This is a string\r with mac \rline breaks");
    $this->object->normalizeLineBreaks(String::LINE_ENDING_R);
    $this->assertEquals("\r", $this->object->detectLineBreaks());
  }
  
  public function testNormalizeLineBreaksWin() {
    $this->object->setValue("This is a string\r\n with windows \r\nline breaks");
    $this->object->normalizeLineBreaks(String::LINE_ENDING_RN);
    $this->assertEquals("\r\n", $this->object->detectLineBreaks());
  }

  public function testWordwrap() {
    // Remove the following lines when you implement this test.
    $this->markTestIncomplete(
            'This test has not been implemented yet.'
    );
  }

  public function testSetDefaultEncoding() {
    String::setDefaultEncoding('ISO-8859-1');
    $this->assertAttributeEquals('ISO-8859-1', 'defaultEncoding','String');
    String::setDefaultEncoding('UTF-8');
    $this->assertAttributeEquals('UTF-8', 'defaultEncoding','String');
  }

  public function testGetDefaultEncoding() {
    String::setDefaultEncoding('ISO-8859-1');
    $this->assertEquals('ISO-8859-1', String::getDefaultEncoding());
    String::setDefaultEncoding('UTF-8');
    $this->assertEquals('UTF-8', String::getDefaultEncoding());
  }

  public function testFactory() {
    $rawString = 'This is a string!';
    $encoding = 'ISO-8859-1';
    $string = String::factory($rawString, $encoding);
    $this->assertInstanceOf('String', $string);
    $this->assertEquals($encoding, $string->getEncoding());
    $this->assertEquals($rawString, $string->getValue());
  }

}

