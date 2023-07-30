<?php

use HValverde\HVLAssistant\HVLArray;
use HValverde\HVLAssistant\HVLCsv;
use HValverde\HVLAssistant\HVLFileSys;
use PHPUnit\Framework\TestCase;

class HVLCsvTest extends TestCase
{
	const CSV_INDEXED_ARRAY = [
		['Column A', 'Column B', 'Column C'],
		['Cell A1', 'Cell B1', 'Cell C1'],
		['Cell A2', 'Cell B2', 'Cell C2'],
	];

	const CSV_MULTI_ARRAY = [
		[
			'Column A' => 'Cell A1',
			'Column B' => 'Cell B1',
			'Column C' => 'Cell C1'
		],
		[
			'Column A' => 'Cell A2',
			'Column B' => 'Cell B2',
			'Column C' => 'Cell C2'
		]
	];

	const CSV_STRING = '"Column A","Column B","Column C"' . "\n"
		. '"Cell A1","Cell B1","Cell C1"' . "\n"
		. '"Cell A2","Cell B2","Cell C2"';

	public function testArrayToCsv()
	{
		$this->assertTrue(
			HVLCsv::arrayToCsv(self::CSV_INDEXED_ARRAY) === self::CSV_STRING
		);
	}

	public function testCsvToArray()
	{
		$this->assertTrue(
			HVLCsv::csvToArray(self::CSV_STRING) === self::CSV_INDEXED_ARRAY
		);
	}

	public function testCsvToMultiArray()
	{
		$this->assertTrue(
			HVLCsv::csvToMultiArray(self::CSV_STRING) === self::CSV_MULTI_ARRAY
		);
	}

	public function testLoadCsvFile()
	{
		$filePath = __DIR__ . '/test.csv';

		HVLCsv::saveCsvFile($filePath, self::CSV_INDEXED_ARRAY);

		$this->assertTrue(file_exists($filePath));

		$expectedRes = self::CSV_INDEXED_ARRAY;
		$receivedRes = HVLCsv::loadCsvFile($filePath);

		$this->assertTrue($expectedRes === $receivedRes);

		unlink($filePath);
	}

	public function testMultiArrayToCsv()
	{
		$this->assertTrue(
			HVLCsv::multiArrayToCsv(self::CSV_MULTI_ARRAY) === self::CSV_STRING
		);
	}

	public function testMultiLoadCsvFile()
	{
		$csvFiles = [];

		for ($i = 0; $i < 3; $i++) {
			$filePath = __DIR__ . '/test' . $i . '.csv';
			$csvFiles[$filePath] = self::CSV_INDEXED_ARRAY;

			HVLCsv::saveCsvFile($filePath, self::CSV_INDEXED_ARRAY);
		}

		$this->assertTrue(
			HVLCsv::multiLoadCsvFile(array_keys($csvFiles)) === $csvFiles
		);

		foreach ($csvFiles as $filePath => $csvData) {
			unlink($filePath);
		}
	}

	public function testMultiSaveCsvFile()
	{
		$csvFiles = [];

		for ($i = 0; $i < 3; $i++) {
			$filePath = __DIR__ . '/test' . $i . '.csv';
			$csvFiles[$filePath] = self::CSV_MULTI_ARRAY;
		}

		HVLCsv::multiSaveCsvFile($csvFiles, true);

		foreach ($csvFiles as $filePath => $csvData) {
			$this->assertTrue(file_exists($filePath));

			unlink($filePath);
		}
	}

	public function testPrepCsvString()
	{
		$csvString = str_replace("\n", PHP_EOL, self::CSV_STRING);
		$csvString = HVLCsv::prepCsvString($csvString);

		$this->assertTrue($csvString === self::CSV_STRING);
	}

	public function testSaveCsvFile()
	{
		$filePath = __DIR__ . '/test.csv';

		HVLCsv::saveCsvFile($filePath, self::CSV_MULTI_ARRAY, true);

		$this->assertTrue(file_exists($filePath));

		unlink($filePath);
	}

	public function testValidCsvArray()
	{
		$this->assertTrue(
			HVLCsv::validCsvArray(self::CSV_INDEXED_ARRAY)
		);
	}
}
