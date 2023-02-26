<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLString;

class HVLCore
{
	public static function camelCaseToSnakeCase(string $str): string
	{
		return HVLString::camelCaseToSnakeCase($str);
	} // Deprecated

	public static function execHidden(string $appPath, string $phpPath = '/usr/local/bin/php', bool $testOnly = false): string
	{
		$execCommand = "$phpPath -f $appPath > /dev/null 2>/dev/null &";

		if (!$testOnly) exec($execCommand);

		return $execCommand;
	}

	public static function getRandomStr(int $length, string $chars = ''): string
	{
		return HVLString::getRandomStr($length, $chars);
	} // Deprecated

	public static function getStringRange($start, $end): string
	{
		return HVLString::getStringRange($start, $end);
	} // Deprecated

	public static function isCli(): bool
	{
		return PHP_SAPI === 'cli';
	}

	public static function snakeCaseToCamelCase(string $str): string
	{
		return HVLString::snakeCaseToCamelCase($str);
	} // Deprecated
}
