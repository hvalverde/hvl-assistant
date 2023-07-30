<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use Exception;

class HVLArray
{
	public static function associativeToMulti(array $data, string $separator = '.'): array
	{
		$newArr = [];

		foreach ($data as $key => $value) {
			$arrKeys = explode($separator, $key);
			$temp = &$newArr;

			foreach ($arrKeys as $k) {
				if (!array_key_exists($k, $temp)) $temp[$k] = [];

				$temp = &$temp[$k];
			}

			$temp = $value;
		}

		return $newArr;
	}

	protected static function _blankCaseToBlankCaseKeys(array $callback, array $array, bool $recursive = true): array
	{
		$newArr = [];

		foreach ($array as $key => $value) {
			$key = is_string($key) && !is_numeric($key)
				? call_user_func_array($callback, [$key])
				: $key;

			$newArr[$key] = $recursive && is_array($value) && count($value)
				? self::_blankCaseToBlankCaseKeys($callback, $value, $recursive)
				: $value;
		}

		return $newArr;
	}

	public static function camelCaseToPascalCaseKeys(array $array, bool $recursive = true): array
	{
		return self::_blankCaseToBlankCaseKeys(
			[HVLString::class, 'camelCaseToPascalCase'],
			$array,
			$recursive
		);
	}

	public static function camelCaseToSnakeCaseKeys(array $array, bool $recursive = true): array
	{
		return self::_blankCaseToBlankCaseKeys(
			[HVLString::class, 'camelCaseToSnakeCase'],
			$array,
			$recursive
		);
	}

	public static function isAssociative(array $array): bool
	{
		return !empty($array) && array_keys($array) !== range(0, count($array) - 1);
	}

	public static function isIndexed(array $array): bool
	{
		return !self::isAssociative($array);
	}

	public static function isSequential(array $array): bool
	{
		return self::isIndexed($array);
	}

	public static function multiToAssociative(array $data, string $separator = '.', string $root_key = NULL): array
	{
		$newArr = [];

		foreach ($data as $key => $value) {
			if (!empty($root_key)) $key = $root_key . $separator . $key;

			is_array($value)
				? $newArr = array_merge($newArr, self::multiToAssociative($value, $separator, $key))
				: $newArr[$key] = $value;
		}

		return $newArr;
	}

	public static function orderMultiArrayByKey(array $array, string $key): array
	{
		$keys = array_column($array, $key);

		if (count($keys) !== count($array)) throw new Exception(
			"Some rows are missing the key '$key'."
		);

		array_multisort($keys, SORT_ASC, SORT_NATURAL, $array);

		return $array;
	}

	public static function pascalCaseToCamelCaseKeys(array $array, bool $recursive = true): array
	{
		return self::_blankCaseToBlankCaseKeys(
			[HVLString::class, 'pascalCaseToCamelCase'],
			$array,
			$recursive
		);
	}

	public static function pascalCaseToSnakeCaseKeys(array $array, bool $recursive = true): array
	{
		return self::_blankCaseToBlankCaseKeys(
			[HVLString::class, 'pascalCaseToSnakeCase'],
			$array,
			$recursive
		);
	}

	/**
	 * @deprecated Use pregReplaceKeys() instead.
	 */
	public static function pregFilterKeys(string $pattern, string $replacement, array $array, bool $recursive = false): array
	{
		return self::pregReplaceKeys($pattern, $replacement, $array, $recursive);
	}

	public static function pregReplaceKeys(string $pattern, string $replacement, array $array, bool $recursive = false): array
	{
		$newArr = [];

		foreach ($array as $key => $value) {
			$key = (string) $key;
			$newKey = preg_replace($pattern, $replacement, $key);

			if (!strlen($newKey)) continue;

			if ($recursive && is_array($value)) {
				$value = self::pregReplaceKeys($pattern, $replacement, $value, $recursive);
			}

			$newArr[$newKey] = $value;
		}

		return $newArr;
	}

	/**
	 * @deprecated Use pregReplaceKeys() instead.
	 */
	public static function pregFilterValues(string $pattern, string $replacement, array $array, bool $recursive = false): array
	{
		return self::pregReplaceValues($pattern, $replacement, $array, $recursive);
	}

	public static function pregReplaceValues(string $pattern, string $replacement, array $array, bool $recursive = false): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = self::pregReplaceValues($pattern, $replacement, $value, $recursive);
				continue;
			}

			$value = (string) $value;
			$value = preg_replace($pattern, $replacement, $value);

			if (empty($value)) {
				unset($array[$key]);
			} else {
				$array[$key] = $value;
			}
		}

		return $array;
	}

	public static function snakeCaseToCamelCaseKeys(array $array, bool $recursive = true): array
	{
		return self::_blankCaseToBlankCaseKeys(
			[HVLString::class, 'snakeCaseToCamelCase'],
			$array,
			$recursive
		);
	}

	public static function snakeCaseToPascalCaseKeys(array $array, bool $recursive = true): array
	{
		return self::_blankCaseToBlankCaseKeys(
			[HVLString::class, 'snakeCaseToPascalCase'],
			$array,
			$recursive
		);
	}

	public static function trimAllValues(array $array): array
	{
		array_walk_recursive($array, function (&$value) {
			if (is_string($value)) $value = trim($value);
		});

		return $array;
	}
}
