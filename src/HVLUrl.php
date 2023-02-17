<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLCore;

class HVLUrl
{
	public static function getFullUrl(): string
	{
		$scheme = 'http';
		$host = 'localhost';
		$urlPath = '';
		$queryStr = '';

		if (HVLCore::isCli()) {
			global $argv;

			$urlPath = $argv[1] ?? '';
		} else {
			$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
			$host = $_SERVER['HTTP_HOST'];
			$urlPath = $_SERVER['REQUEST_URI'];
			$queryStr = isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING'])
				? '?' . $_SERVER['QUERY_STRING']
				: '';
		}

		return $scheme . '://' . $host . $urlPath . $queryStr;
	}

	public static function getUrlHost(string $url = ''): string
	{
		if (!self::validUrl($url)) {
			throw new \Exception("Invalid URL provided: '" . print_r($url, true) . "'");
		}

		if (!strlen($url)) $url = self::getFullUrl();

		return parse_url($url, PHP_URL_HOST);
	}

	public static function getUrlPath(string $url = ''): string
	{
		if (!self::validUrl($url)) {
			throw new \Exception("Invalid URL provided: '" . print_r($url, true) . "'");
		}

		if (!strlen($url)) $url = self::getFullUrl();

		$url_path = parse_url($url, PHP_URL_PATH);

		return trim($url_path, '/');
	}

	public static function validUrl(string $url): bool
	{
		return (bool) filter_var($url, FILTER_VALIDATE_URL);
	}
}
