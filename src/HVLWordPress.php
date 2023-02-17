<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLArray;
use HValverde\HVLAssistant\HVLFileSys;
use HValverde\HVLAssistant\HVLJson;
use HValverde\HVLAssistant\HVLUrl;

class HVLWordPress
{
	const API_PATH		= '/wp-json/wp/v2/';
	const MEDIA_PATH	= '/wp-content/uploads/';

	protected $config = [
		'jsonDir'	=> '',
		'mediaDir'	=> '',
		'url'		=> ''
	];
	protected $data = [];
	protected $loopLimit = 100;
	protected $perPageLimit = 100;
	protected $resources = [
		'categories',
		'media',
		'pages',
		'posts',
		'tags'
	];

	public function __construct(string $url = '', string $jsonDir = '', string $mediaDir = '')
	{
		if (!empty($jsonDir))	$this->setJsonDir($jsonDir);
		if (!empty($mediaDir))	$this->setMediaDir($mediaDir);
		if (!empty($url))		$this->setUrl($url);
	}

	public function buildApiUrl(string $resource, array $query = []): string
	{
		if (empty($resource)) throw new \Exception(
			'Resource input should not be empty.'
		);

		if (!$this->validResource($resource)) throw new \Exception(
			"Invalid WP resource: " . $resource
		);

		$apiUrl = $this->getApiUrl() . $resource;

		if (!empty($query)) $apiUrl .= '?' . http_build_query($query);

		return $apiUrl;
	}

	public function buildResourceQuery(): array
	{
		$query = [
			'order'		=> 'asc',
			'orderby'	=> 'id',
			'page'		=> 0,
			'per_page'	=> $this->getPerPageLimit(),
		];

		return $query;
	}

	public function clearDataCache(string $resource = ''): array
	{
		$data = $this->data;

		if (empty($resource)) {
			$this->data = [];
		} elseif ($this->validResource($resource)) {
			$data = $data[$resource] ?? [];

			$this->data[$resource] = [];
		} else {
			throw new \Exception('Invalid resource given: ' . $resource);
		}

		return $data;
	}

	public function downloadMedia(): array
	{
		$mediaDir = $this->getMediaDir();

		if (empty($mediaDir)) throw new \Exception(
			"Media directory is not set."
		);

		$mediaData = $this->getData('media');
		$mediaDir = HVLFileSys::addDirSeparator($mediaDir);
		$copiedFiles = [];

		foreach ($mediaData as $mediaItem) {
			$mediaUrlPath	= $this->getMediaUrlPath($mediaItem['source_url']);
			$mediaDestPath	= $mediaDir . $mediaUrlPath;
			$mediaDestDir	= dirname($mediaDestPath);
			$mediaDestDate	= HVLFileSys::fileExists($mediaDestPath)
				? date('YmdHis', filemtime($mediaDestPath))
				: 0;
			$mediaDate		= date('YmdHis', strtotime($mediaItem['date']));
			$mediaSrc		= $mediaItem['source_url'];

			if ($mediaDate > $mediaDestDate) {
				HVLFileSys::createDir($mediaDestDir);

				if (!copy($mediaSrc, $mediaDestPath)) throw new \Exception(
					"File failed to copy: '$mediaSrc' -> '$mediaDestPath'"
				);

				$copiedFiles[] = $mediaDestPath;
			}
		}

		return $copiedFiles;
	}

	public function getAllData(bool $checkChase = true): array
	{
		$data = [];

		foreach ($this->resources as $resource) {
			$data[$resource] = $this->getData($resource, $checkChase);
		}

		return $data;
	}

	public function getApiUrl(): string
	{
		$url = $this->getConfig('url');

		if (empty($url)) throw new \Exception('URL host is not set.');

		$apiUrl = $url . self::API_PATH;

		return $apiUrl;
	}

	public function getConfig(mixed $key = ''): array | string
	{
		if (is_array($key)) {
			$result = [];

			foreach ($key as $k) {
				if (empty($k)) continue;

				$result[$k] = $this->config[$k] ?? null;
			}

			return $result;
		} elseif (is_string($key)) {
			if (empty($key)) return null;

			return $this->config[$key] ?? null;
		} else {
			throw new \Exception("Parameter 'key' must be an array or a string.");
		}
	}

	public function getData(string $resource, bool $checkCache = true): array
	{
		if (!$this->validResource($resource)) throw new \Exception(
			"Invalid resource: '$resource'"
		);

		if ($checkCache && !empty($this->data[$resource] ?? [])) {
			return $this->data[$resource];
		}

		return $this->loadData($resource);
	}

	public function getJsonDir(): string
	{
		return $this->getConfig('jsonDir');
	}

	public function getLoopLimit(): int
	{
		return $this->loopLimit;
	}

	public function getMediaDir(): string
	{
		return $this->getConfig('mediaDir');
	}

	public function getMediaUrlPath(string $mediaUrl): string
	{
		if (empty($mediaUrl)) throw new \Exception(
			"Media URL cannot be empty."
		);

		if (!HVLUrl::validUrl($mediaUrl)) throw new \Exception(
			"Media URL is not valid: $mediaUrl"
		);

		$url = $this->getMediaUrlPrefix();
		$pattern = '/^' . preg_quote($url, '/') . '/';
		$mediaUrlPath = preg_replace($pattern, '', $mediaUrl);

		return $mediaUrlPath;
	}

	public function getMediaUrlPrefix(): string
	{
		$url = $this->getConfig('url');

		if (empty($url)) throw new \Exception('URL host is not set.');

		return $url . self::MEDIA_PATH;
	}

	public function getPerPageLimit(): int
	{
		return $this->perPageLimit;
	}

	public function loadData(string $resource): array
	{
		$data = [];
		$loopLimit = $this->getLoopLimit();
		$perPageLimit = $this->getPerPageLimit();
		$query = $this->buildResourceQuery();

		for ($a = 0; $a < $loopLimit; $a++) {
			$query['page']++;

			$apiUrl = $this->buildApiUrl($resource, $query);
			$result = @file_get_contents($apiUrl);

			if (
				$result === false ||
				!is_string($result) ||
				empty($result) ||
				!HVLJson::isJson($result)
			) break;

			$result = HVLJson::jsonDecode($result);
			$data = array_merge($data, $result);

			if (count($result) < $perPageLimit) break;
		}

		$ids = array_column($data, 'id');
		$data = array_combine($ids, $data);

		return $this->data[$resource] = $data;
	}

	public function saveAllData(): array
	{
		$data = [];

		foreach ($this->resources as $resource) {
			$data[$resource] = $this->saveData($resource);
		}

		return $data;
	}

	public function saveData(string $resource): array
	{
		$jsonDir = $this->getJsonDir();

		if (empty($jsonDir)) throw new \Exception(
			"JSON directory is not set."
		);

		$jsonDir .= $resource . '/';

		HVLFileSys::createDir($jsonDir);

		$data = $this->getData($resource);
		$data = HVLArray::pregFilterKeys('/^(.*)$/', $jsonDir . '$1.json', $data);

		if (count($data)) HVLJson::multiSaveJsonFile($data, true);

		return $data;
	}

	public function setConfig(mixed $key, string $data = ''): array
	{
		if (is_array($key)) {
			empty($key)
				? $this->config = []
				: $this->config = array_merge($this->config, $key);
		} elseif (is_string($key)) {
			if (strlen($key)) $this->config[$key] = $data;
		} else {
			throw new \Exception("Parameter 'key' must be an array or a string.");
		}

		return $this->config;
	}

	public function setJsonDir(string $dirPath): string
	{
		if (empty($dirPath)) throw new \Exception(
			"Invalid directory path: '$dirPath'"
		);

		$dirPath = HVLFileSys::addDirSeparator($dirPath);

		$this->setConfig('jsonDir', $dirPath);

		return $dirPath;
	}

	public function setLoopLimit(int $limit): int
	{
		if ($limit <= 0) throw new \Exception("Limit should be higher than 0.");

		if ($limit >= PHP_INT_MAX) throw new \Exception("Limit should be less than " . PHP_INT_MAX . ".");

		return $this->loopLimit = $limit;
	}

	public function setMediaDir(string $dirPath): string
	{
		if (empty($dirPath)) throw new \Exception(
			"Invalid directory path: '$dirPath'"
		);

		$dirPath = HVLFileSys::addDirSeparator($dirPath);

		$this->setConfig('mediaDir', $dirPath);

		return $dirPath;
	}

	public function setPerPageLimit(int $limit): int
	{
		if ($limit < 1) throw new \Exception("Limit should be higher than 0.");

		if ($limit > 100) throw new \Exception("Limit should be less than or equal to 100.");

		return $this->perPageLimit = $limit;
	}

	public function setUrl(string $url): string
	{
		if (!HVLUrl::validUrl($url)) throw new \Exception("Invalid URL: $url");

		$this->setConfig('url', $url);

		return $url;
	}

	public function validResource(string $resource): bool
	{
		if (empty($resource)) return false;

		return in_array($resource, $this->resources);
	}
}
