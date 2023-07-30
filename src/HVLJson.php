<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLFileSys;

use Exception;

class HVLJson
{
	public static function isJson(string $string): bool
	{
		if (empty($string)) throw new Exception('The input string must not be empty.');

		$result = json_decode($string, true);

		if (json_last_error() !== JSON_ERROR_NONE) return false;

		return is_array($result);
	}

	public static function jsonDecode(string $string, bool $return_array = true): array|object|null
	{
		if ($string === '') return null;

		$result = json_decode($string, $return_array);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception("The input string is not a valid JSON string.");
		}

		return $result;
	}

	public static function jsonEncode(array|object $data, bool $pretty = false): string
	{
		if (empty($data)) throw new Exception("The input data must not be empty.");

		if (is_object($data)) $data = (array) $data;

		$options = JSON_UNESCAPED_SLASHES;

		if ($pretty) {
			$options |= JSON_PRETTY_PRINT;

			ksort($data);
		}

		$result = json_encode($data, $options);

		if ($result === false) {
			throw new Exception("Failed to encode data as JSON: " . json_last_error_msg());
		}

		return $result;
	}

	public static function loadJsonFile(string $path, bool $return_array = true): array|object|null
	{
		$path = HVLFileSys::appendFileExtension($path, 'json');
		
		if (!HVLFileSys::fileExists($path)) throw new Exception('File not found: ' . $path);

		$str = file_get_contents($path);

		if ($str === false) throw new Exception('Failed to read the contents: ' . $path);

		return self::jsonDecode($str, $return_array);
	}

	public static function multiJsonDecode(array $arrayOfJsonStrings, bool $return_array = true): array
	{
		foreach ($arrayOfJsonStrings as &$jsonData) {
			if (!self::isJson($jsonData)) continue;

			$jsonData = self::jsonDecode($jsonData, $return_array);
		}

		return $arrayOfJsonStrings;
	}

	public static function multiJsonEncode(array $arrayOfArrayData): array
	{
		foreach ($arrayOfArrayData as &$jsonData) {
			if (!is_array($jsonData) && !is_object($jsonData)) continue;

			$jsonData = self::jsonEncode($jsonData);
		}

		return $arrayOfArrayData;
	}

	public static function multiLoadJsonFile(array $paths, bool $return_array = true): array
	{
		$data = [];

		foreach ($paths as $filePath) {
			$data[$filePath] = self::loadJsonFile($filePath, $return_array);
		}

		return $data;
	}

	public static function multiSaveJsonFile(array $data, bool $pretty = false): void
	{
		foreach ($data as $filePath => $fileData) {
			self::saveJsonFile($filePath, $fileData, $pretty);
		}
	}

	public static function saveJsonFile(string $path, array $data, bool $pretty = false): void
	{
		$jsonStr = self::jsonEncode($data, $pretty);
		$path = HVLFileSys::appendFileExtension($path, 'json');

		HVLFileSys::createFile($path, $jsonStr, true);
	}
}
