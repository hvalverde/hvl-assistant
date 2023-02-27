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

	public static function execHidden(string $app_path, string $php_path = '/usr/local/bin/php', bool $test_only = false): string
	{
		$exec_command = "$php_path -f $app_path > /dev/null 2>/dev/null &";

		if (!$test_only) exec($exec_command);

		return $exec_command;
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

	public static function validEmail(string $email): bool
	{
		return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
	}
}
