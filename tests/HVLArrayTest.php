<?php

use HValverde\HVLAssistant\HVLArray;
use PHPUnit\Framework\TestCase;

class HVLArrayTest extends TestCase
{
	protected $associativeArray = [
		'key1' => 'value1',
		'key2' => 'value2',
		'key3.a' => 'value3a',
		'key3.b' => 'value3b',
	];

	protected $camelCaseArray = [
		'keyOne' => 'value1',
		'keyTwo' => 'value2',
		'keyThree.a' => 'value3a',
		'keyThree.b' => 'value3b',
	];

	protected $indexedArray = [
		'value1',
		'value2',
		'value3a',
		'value3b',
	];

	protected $pascalCaseArray = [
		'KeyOne' => 'value1',
		'KeyTwo' => 'value2',
		'KeyThree.a' => 'value3a',
		'KeyThree.b' => 'value3b',
	];

	protected $multiArray = [
		'key1' => 'value1',
		'key2' => 'value2',
		'key3' => [
			'a' => 'value3a',
			'b' => 'value3b',
		],
	];

	protected $snakeCaseArray = [
		'key_one' => 'value1',
		'key_two' => 'value2',
		'key_three.a' => 'value3a',
		'key_three.b' => 'value3b',
	];

	public function testAssociativeToMulti()
	{
		$this->assertTrue(
			HVLArray::associativeToMulti($this->associativeArray) === $this->multiArray
		);
	}

	public function testCamelCaseToPascalCaseKeys()
	{
		$this->assertTrue(
			HVLArray::camelCaseToPascalCaseKeys($this->camelCaseArray) === $this->pascalCaseArray
		);
	}

	public function testCamelCaseToSnakeCaseKeys()
	{
		$this->assertTrue(
			HVLArray::camelCaseToSnakeCaseKeys($this->camelCaseArray) === $this->snakeCaseArray
		);
	}

	public function testIsAssociative()
	{
		$this->assertTrue(
			HVLArray::isAssociative($this->associativeArray)
		);

		$this->assertTrue(
			!HVLArray::isAssociative($this->indexedArray)
		);
	}

	public function testIsSequential()
	{
		$this->assertTrue(
			!HVLArray::isSequential($this->associativeArray)
		);

		$this->assertTrue(
			HVLArray::isSequential($this->indexedArray)
		);
	}

	public function testMultiToAssociative()
	{
		$this->assertTrue(
			HVLArray::multiToAssociative($this->multiArray) === $this->associativeArray
		);
	}

	public function testPascalCaseToCamelCaseKeys()
	{
		$this->assertTrue(
			HVLArray::pascalCaseToCamelCaseKeys($this->pascalCaseArray) === $this->camelCaseArray
		);
	}

	public function testPascalCaseToSnakeCaseKeys()
	{
		$this->assertTrue(
			HVLArray::pascalCaseToSnakeCaseKeys($this->pascalCaseArray) === $this->snakeCaseArray
		);
	}

	public function testPregReplaceKeys()
	{
		$this->assertTrue(
			HVLArray::pregReplaceKeys(
				'/^key/',
				'newKey',
				$this->associativeArray
			) === [
				'newKey1' => 'value1',
				'newKey2' => 'value2',
				'newKey3.a' => 'value3a',
				'newKey3.b' => 'value3b',
			]
		);
	}

	public function testPregReplaceValues()
	{
		$this->assertTrue(
			HVLArray::pregReplaceValues(
				'/^value/',
				'newValue',
				$this->associativeArray
			) === [
				'key1' => 'newValue1',
				'key2' => 'newValue2',
				'key3.a' => 'newValue3a',
				'key3.b' => 'newValue3b',
			]
		);
	}

	public function testSnakeCaseToCamelCaseKeys()
	{
		$this->assertTrue(
			HVLArray::snakeCaseToCamelCaseKeys($this->snakeCaseArray) === $this->camelCaseArray
		);
	}

	public function testSnakeCaseToPascalCaseKeys()
	{
		$this->assertTrue(
			HVLArray::snakeCaseToPascalCaseKeys($this->snakeCaseArray) === $this->pascalCaseArray
		);
	}

	public function testTrimAllValues()
	{
		$this->assertTrue(
			HVLArray::trimAllValues([
				'key1' => 'value1 ',
				'key2' => ' value2',
				'key3.a' => ' value3a ',
				'key3.b' => 'value3b',
			]) === $this->associativeArray
		);
	}
}
