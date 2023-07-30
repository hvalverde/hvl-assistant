<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLFileSys;

use Exception;

class HVLCsv
{
	/**
	 * @see HVLCsv::indexedArrayToCsv()
	 */
	public static function arrayToCsv(array $data): string
	{
		return self::indexedArrayToCsv($data);
	}

	/**
	 * @deprecated Use HVLCsv::multiArrayToCsv() instead.
	 */
	public static function associativeArrayToCsv(array $data): string
	{
		return self::multiArrayToCsv($data);
	}

	/**
	 * @see HVLCsv::csvToSequentialArray()
	 */
	public static function csvToArray(string $csvString): array
	{
		return self::csvToSequentialArray($csvString);
	}

	/**
	 * @deprecated Use HVLCsv::csvToMultiArray() instead.
	 */
	public static function csvToAssociativeArray(string $csvString): array
	{
		return self::csvToMultiArray($csvString);
	}

	public static function csvToMultiArray(string $csvString): array
	{
		$csvString = self::prepCsvString($csvString);
		$csvLines = explode("\n", $csvString);
		$fieldNames = str_getcsv(array_shift($csvLines));
		$csvArray = array_map(
			function ($line) use ($fieldNames) {
				return array_combine($fieldNames, str_getcsv($line));
			},
			$csvLines
		);

		return $csvArray;
	}

	public static function csvToSequentialArray($csvString): array
	{
		$csvString = self::prepCsvString($csvString);

		return array_map(
			'str_getcsv',
			explode("\n", $csvString)
		);
	}

	public static function indexedArrayToCsv(array $data): string
	{
		if (empty($data)) throw new Exception("The input data must not be empty.");

		if (!self::validCsvArray($data)) throw new Exception("Invalid CSV array.");

		$handle = fopen('php://temp', 'r+');

		foreach ($data as $row) fputcsv($handle, $row);

		rewind($handle);

		$csvData = fread($handle, fstat($handle)['size']);

		fclose($handle);

		return trim($csvData);
	}

	public static function loadCsvFile(string $path, bool $multiArray = false): array
	{
		$path = HVLFileSys::appendFileExtension($path, 'csv');

		if (!HVLFileSys::fileExists($path)) throw new Exception('File not found: ' . $path);

		$str = file_get_contents($path);

		if ($str === false) throw new Exception('Failed to read the contents: ' . $path);

		return $multiArray
			? self::csvToMultiArray($str)
			: self::csvToArray($str);
	}

	public static function multiArrayToCsv(array $data): string
	{
		$colNames = array_keys(current($data));

		array_unshift($data, $colNames);

		return self::arrayToCsv($data);
	}

	public static function multiLoadCsvFile(array $paths, bool $multiArray = false): array
	{
		$data = [];

		foreach ($paths as $filePath) {
			$data[$filePath] = self::loadCsvFile($filePath, $multiArray);
		}

		return $data;
	}

	public static function multiSaveCsvFile(array $data, bool $multiArray = false): void
	{
		foreach ($data as $filePath => $fileData) {
			if (!is_array($fileData)) {
				throw new Exception(
					"Invalid input type, expected array. Received: " . gettype($fileData)
				);
			}

			self::saveCsvFile($filePath, $fileData, $multiArray);
		}
	}

	public static function prepCsvString(string $csvString): string
	{
		$csvString = str_replace("\r", "\n", $csvString);
		$csvString = preg_replace('/\n+/', "\n", $csvString);

		return trim($csvString);
	}

	public static function saveCsvFile(string $path, array $data, bool $multiArray = false): void
	{
		$path = HVLFileSys::appendFileExtension($path, 'csv');
		$str = $multiArray
			? self::multiArrayToCsv($data)
			: self::arrayToCsv($data);

		HVLFileSys::createFile($path, $str, true);
	}

	public static function validCsvArray(array $data): bool
	{
		if (empty($data)) throw new Exception("The input data must not be empty.");

		$colNum = null;

		foreach ($data as $row) {
			if (empty($row)) throw new Exception('Row is empty.');

			if (is_null($colNum)) $colNum = count($row);

			if ($colNum !== count($row)) return false;
		}

		return !is_null($colNum);
	}
}
