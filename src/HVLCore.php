<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLString;

class HVLCore
{
	/**
	 * @deprecated Use HVLString::camelCaseToPascalCase() instead.
	 */
	public static function camelCaseToSnakeCase(string $str): string
	{
		return HVLString::camelCaseToSnakeCase($str);
	}

	public static function execHidden(string $app_path, string $php_path = '/usr/local/bin/php', bool $skip_exec = false): string
	{
		$command = "$php_path -f $app_path > /dev/null 2>/dev/null &";

		if (!$skip_exec) exec($command);

		return $command;
	}

	/**
	 * @deprecated Use HVLString::getRandomStr() instead.
	 */
	public static function getRandomStr(int $length, string $chars = ''): string
	{
		return HVLString::getRandomStr($length, $chars);
	}

	/**
	 * @deprecated Use HVLString::getStringRange() instead.
	 */
	public static function getStringRange($start, $end): string
	{
		return HVLString::getStringRange($start, $end);
	}

	public static function isCli(): bool
	{
		return PHP_SAPI === 'cli';
	}

	/**
	 * @deprecated Use HVLString::pascalCaseToCamelCase() instead.
	 */
	public static function snakeCaseToCamelCase(string $str): string
	{
		return HVLString::snakeCaseToCamelCase($str);
	}

	public static function validEmail(string $email): bool
	{
		return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
	}
}
