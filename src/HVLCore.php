<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

class HVLCore
{
	public static function camelCaseToSnakeCase(string $str): string
	{
		$str = preg_replace('/([A-Z])/', '_$1', $str);
		$str = trim($str, '_');
		return strtolower((string) $str);
	}

	public static function execHidden(string $appPath, string $phpPath = '/usr/local/bin/php', bool $testOnly = false): string
	{
		$execCommand = "$phpPath -f $appPath > /dev/null 2>/dev/null &";

		if (!$testOnly) exec($execCommand);

		return $execCommand;
	}

	public static function getRandomStr(int $length, string $chars = ''): string
	{
		if (!strlen($chars)) {
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

	public static function isCli(): bool
	{
		return PHP_SAPI === 'cli';
	}

	public static function snakeCaseToCamelCase(string $str): string
	{
		$str = str_replace('_', ' ', $str);
		$str = trim($str);
		$str = ucwords($str);
		$str = preg_replace('/\s+/', '', $str);
		return lcfirst($str);
	}
}
