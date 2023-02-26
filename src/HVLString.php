<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

class HVLString
{
	public static function camelCaseToPascalCase(string $str): string
	{
		return ucfirst($str);
	}

	public static function camelCaseToSnakeCase(string $str): string
	{
		$str = preg_replace('/([A-Z])/', '_$1', $str);
		$str = trim($str, '_');
		return strtolower((string) $str);
	}

	public static function getRandomStr(int $length, string $chars = ''): string
	{
		if (empty($chars)) {
			$chars = self::getStringRange('a', 'z')
				. self::getStringRange('A', 'Z')
				. self::getStringRange(0, 9);
		}

		$charsLength = strlen($chars) - 1;
		$randomStr = '';

		if ($charsLength <= 0) throw new \Exception('Character string length is too short.');

		while (strlen($randomStr) < $length) {
			$charPos = random_int(0, $charsLength);
			$randomStr .= $chars[$charPos];
		}

		return $randomStr;
	}

	public static function getStringRange($start, $end): string
	{
		$range = range($start, $end);
		$str = implode('', $range);

		return $str;
	}

	public static function pascalCaseToCamelCase(string $str): string
	{
		return lcfirst($str);
	}

	public static function pascalCaseToSnakeCase(string $str): string
	{
		$str = self::pascalCaseToCamelCase($str);
		return self::camelCaseToSnakeCase($str);
	}

	public static function snakeCaseToPascalCase(string $str): string
	{
		$str = str_replace('_', ' ', $str);
		$str = trim($str);
		$str = ucwords($str);
		return preg_replace('/\s+/', '', $str);
	}

	public static function snakeCaseToCamelCase(string $str): string
	{
		$str = self::snakeCaseToPascalCase($str);
		return lcfirst($str);
	}
}
