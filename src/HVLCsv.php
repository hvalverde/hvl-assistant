<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLFileSys;

class HVLCsv
{
	public static function arrayToCsv(array $data): string
	{
		if (empty($data)) throw new \InvalidArgumentException("The input data must not be empty.");

		if (!self::validCsvArray($data)) throw new \InvalidArgumentException("Invalid CSV array.");

		$handle = fopen('php://temp', 'r+');

		foreach ($data as $row) fputcsv($handle, $row);

		rewind($handle);

		$csvData = fread($handle, fstat($handle)['size']);

		fclose($handle);

		return trim($csvData);
	}

	public static function associativeArrayToCsv(array $data): string
	{
		$colNames = array_keys(current($data));

		array_unshift($data, $colNames);

		return self::arrayToCsv($data);
	}

	public static function csvToArray(string $csvString): array
	{
		$csvString = self::prepCsvString($csvString);

		return array_map(
			'str_getcsv',
			explode("\n", $csvString)
		);
	}

	public static function csvToAssociativeArray(string $csvString): array
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

	public static function loadCsvFile(string $path, bool $associativeData = false): array
	{
		$path = HVLFileSys::addFileExtension($path, 'csv');

		if (!HVLFileSys::fileExists($path)) throw new \InvalidArgumentException('File not found: ' . $path);

		$str = file_get_contents($path);

		if ($str === false) throw new \RuntimeException('Failed to read the contents: ' . $path);

		return $associativeData
			? self::csvToAssociativeArray($str)
			: self::csvToArray($str);
	}

	public static function multiLoadCsvFile(array $paths): array
	{
		$data = [];

		foreach ($paths as $filePath) {
			$data[$filePath] = self::loadCsvFile($filePath);
		}

		return $data;
	}

	public static function multiSaveCsvFile(array $data, bool $associativeData = false): void
	{
		foreach ($data as $filePath => $fileData) {
			if (!is_array($fileData)) {
				throw new \RuntimeException(
					"Invalid input type, expected array. Received: " . gettype($fileData)
				);
			}

			self::saveCsvFile($filePath, $fileData, $associativeData);
		}
	}

	public static function prepCsvString(string $csvString): string
	{
		$csvString = str_replace("\r", "\n", $csvString);
		$csvString = preg_replace('/\n+/', "\n", $csvString);

		return trim($csvString);
	}

	public static function saveCsvFile(string $path, array $data, bool $associativeData = false): void
	{
		$path = HVLFileSys::addFileExtension($path, 'csv');
		$str = $associativeData
			? self::associativeArrayToCsv($data)
			: self::arrayToCsv($data);

		HVLFileSys::createFile($path, $str, true);
	}

	public static function validCsvArray(array $data): bool
	{
		if (empty($data)) throw new \InvalidArgumentException("The input data must not be empty.");

		$colNum = null;

		foreach ($data as $row) {
			if (empty($row)) throw new \Exception('Row is empty.');

			if (is_null($colNum)) $colNum = count($row);

			if ($colNum !== count($row)) return false;
		}

		return true;
	}
}
