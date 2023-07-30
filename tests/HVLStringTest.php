<?php

use HValverde\HVLAssistant\HVLString;
use PHPUnit\Framework\TestCase;

class HVLStringTest extends TestCase
{
	protected $camelCaseString = 'helloWorldHowAreYou';
	protected $pascalCaseString = 'HelloWorldHowAreYou';
	protected $snakeCaseString = 'hello_world_how_are_you';

	public function testCamelCaseToPascalCase()
	{
		$this->assertTrue(
			HVLString::camelCaseToPascalCase($this->camelCaseString) === $this->pascalCaseString
		);
	}

	public function testCamelCaseToSnakeCase()
	{
		$this->assertTrue(
			HVLString::camelCaseToSnakeCase($this->camelCaseString) === $this->snakeCaseString
		);
	}

	public function testGetRandomStr()
	{
		$this->assertTrue(strlen(HVLString::getRandomStr(10)) == 10);
		$this->assertTrue(strlen(HVLString::getRandomStr(10, '0123456789')) == 10);

		$randomStr = HVLString::getRandomStr(10, 'ABCDEFGH');
		
		$this->assertTrue(
			preg_match('/^[ABCDEFGH]{10}$/', $randomStr) === 1
		);
	}

	public function testGetStringRange()
	{
		$this->assertTrue(
			HVLString::getStringRange(0, 9) === '0123456789'
		);
		$this->assertTrue(
			HVLString::getStringRange('A', 'Z') === 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
		);
	}

	public function testPascalCaseToCamelCase()
	{
		$this->assertTrue(
			HVLString::pascalCaseToCamelCase($this->pascalCaseString) === $this->camelCaseString
		);
	}

	public function testPascalCaseToSnakeCase()
	{
		$this->assertTrue(
			HVLString::pascalCaseToSnakeCase($this->pascalCaseString) === $this->snakeCaseString
		);
	}

	public function testSnakeCaseToCamelCase()
	{
		$this->assertTrue(
			HVLString::snakeCaseToCamelCase($this->snakeCaseString) === $this->camelCaseString
		);
	}

	public function testSnakeCaseToPascalCase()
	{
		$this->assertTrue(
			HVLString::snakeCaseToPascalCase($this->snakeCaseString) === $this->pascalCaseString
		);
	}

	public function testValidRegex()
	{
		$this->assertTrue(
			HVLString::validRegex('/^.*$/')
		);
		$this->assertFalse(
			HVLString::validRegex('^.*$/')
		);
	}
}
