<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLFileSys;

class HVLJson
{
	public static function isJson(string $string): bool
	{
		if (empty($string)) throw new \InvalidArgumentException('The input string must not be empty.');

		$result = json_decode($string, true);

		if (json_last_error() !== JSON_ERROR_NONE) return false;

		return is_array($result);
	}

	public static function jsonDecode(string $string): array
	{
		if (empty($string)) throw new \InvalidArgumentException('The input string must not be empty.');

		$result = json_decode($string, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \InvalidArgumentException("The input string is not a valid JSON string.");
		}

		if (!is_array($result)) throw new \UnexpectedValueException("The decoded JSON is not an array.");

		return $result;
	}

	public static function jsonEncode(array $data, bool $pretty = false): string
	{
		if (empty($data)) throw new \InvalidArgumentException("The input data must not be empty.");

		$options = JSON_UNESCAPED_SLASHES;

		if ($pretty) {
			$options |= JSON_PRETTY_PRINT;

			ksort($data);
		}

		$result = json_encode($data, $options);

		if ($result === false) {
			throw new \RuntimeException("Failed to encode data as JSON: " . json_last_error_msg());
		}

		return $result;
	}

	public static function loadJsonFile(string $path): array
	{
		$path = HVLFileSys::addFileExtension($path, 'json');
		
		if (!HVLFileSys::fileExists($path)) throw new \InvalidArgumentException('File not found: ' . $path);

		$str = file_get_contents($path);

		if ($str === false) throw new \RuntimeException('Failed to read the contents: ' . $path);

		return self::jsonDecode($str);
	}

	public static function multiJsonDecode(array $arrayOfJsonStrings): array
	{
		foreach ($arrayOfJsonStrings as &$jsonData) {
			if (!self::isJson($jsonData)) continue;

			$jsonData = self::jsonDecode($jsonData);
		}

		return $arrayOfJsonStrings;
	}

	public static function multiJsonEncode(array $arrayOfArrayData): array
	{
		foreach ($arrayOfArrayData as &$jsonData) {
			if (!is_array($jsonData)) continue;

			$jsonData = self::jsonEncode($jsonData);
		}

		return $arrayOfArrayData;
	}

	public static function multiLoadJsonFile(array $paths): array
	{
		$data = [];

		foreach ($paths as $filePath) {
			$data[$filePath] = self::loadJsonFile($filePath);
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
		$path = HVLFileSys::addFileExtension($path, 'json');

		HVLFileSys::createFile($path, $jsonStr, true);
	}
}
