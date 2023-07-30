<?php

use HValverde\HVLAssistant\HVLArray;
use HValverde\HVLAssistant\HVLFileSys;
use HValverde\HVLAssistant\HVLJson;
use PHPUnit\Framework\TestCase;

class HVLJsonTest extends TestCase
{
	const JSON_ARRAY = [
		'foo' => 'bar',
		'hello' => 'world'
	];

	const JSON_STRING = '{"foo":"bar","hello":"world"}';

	public static $jsonObject;

	public function setUp(): void
	{
		parent::setUp();

		self::$jsonObject = (object) self::JSON_ARRAY;
	}

	public function testIsJson()
	{
		$this->assertTrue(
			HVLJson::isJson(self::JSON_STRING)
		);
	}

	public function testJsonDecode()
	{
		$this->assertTrue(
			HVLJson::jsonDecode(self::JSON_STRING) === self::JSON_ARRAY
		);

		$this->assertTrue(
			HVLJson::jsonDecode(self::JSON_STRING, false) == self::$jsonObject
		);
	}

	public function testJsonEncode()
	{
		$this->assertTrue(
			HVLJson::jsonEncode(self::JSON_ARRAY) === self::JSON_STRING
		);

		$this->assertTrue(
			HVLJson::jsonEncode(self::$jsonObject) === self::JSON_STRING
		);
	}

	public function testLoadJsonFile()
	{
		$filePath = __DIR__ . '/test.json';

		if (file_exists($filePath)) unlink($filePath);

		file_put_contents($filePath, self::JSON_STRING);

		$this->assertTrue(
			HVLJson::loadJsonFile($filePath) === self::JSON_ARRAY
		);

		$this->assertTrue(
			HVLJson::loadJsonFile($filePath, false) == self::$jsonObject
		);

		unlink($filePath);
	}

	public function testMultiJsonDecode()
	{
		$jsonArrays = [
			self::JSON_ARRAY,
			self::JSON_ARRAY,
			self::JSON_ARRAY
		];
		$jsonObjects = [
			self::$jsonObject,
			self::$jsonObject,
			self::$jsonObject
		];
		$jsonStrings = [
			self::JSON_STRING,
			self::JSON_STRING,
			self::JSON_STRING
		];

		$this->assertTrue(
			HVLJson::multiJsonDecode($jsonStrings) === $jsonArrays
		);

		$this->assertTrue(
			HVLJson::multiJsonDecode($jsonStrings, false) == $jsonObjects
		);
	}

	public function testMultiJsonEncode()
	{
		$jsonArrays = [
			self::JSON_ARRAY,
			self::JSON_ARRAY,
			self::JSON_ARRAY
		];
		$jsonObjects = [
			self::$jsonObject,
			self::$jsonObject,
			self::$jsonObject
		];
		$jsonStrings = [
			self::JSON_STRING,
			self::JSON_STRING,
			self::JSON_STRING
		];

		$this->assertTrue(
			HVLJson::multiJsonEncode($jsonArrays) === $jsonStrings
		);

		$this->assertTrue(
			HVLJson::multiJsonEncode($jsonObjects) === $jsonStrings
		);
	}

	public function testMultiLoadJsonFile()
	{
		$jsonFiles = [];

		for ($i = 0; $i < 3; $i++) {
			$filePath = __DIR__ . '/test' . $i . '.json';
			$jsonFiles[$filePath] = self::JSON_ARRAY;
		}

		HVLJson::multiSaveJsonFile($jsonFiles);

		$this->assertTrue(
			HVLJson::multiLoadJsonFile(array_keys($jsonFiles)) === $jsonFiles
		);

		foreach ($jsonFiles as $filePath => $jsonArray) {
			unlink($filePath);
		}
	}

	public function testMultiSaveJsonFile()
	{
		$jsonFiles = [];

		for ($i = 0; $i < 3; $i++) {
			$filePath = __DIR__ . '/test' . $i . '.json';
			$jsonFiles[$filePath] = self::JSON_ARRAY;
		}

		HVLJson::multiSaveJsonFile($jsonFiles);

		foreach ($jsonFiles as $filePath => $jsonArray) {
			$this->assertTrue(
				HVLJson::loadJsonFile($filePath) === $jsonArray
			);

			unlink($filePath);
		}
	}

	public function testSaveJsonFile()
	{
		$filePath = __DIR__ . '/test.json';

		if (file_exists($filePath)) unlink($filePath);

		HVLJson::saveJsonFile($filePath, self::JSON_ARRAY);

		$this->assertTrue(
			HVLJson::loadJsonFile($filePath) === self::JSON_ARRAY
		);

		unlink($filePath);
	}
}
