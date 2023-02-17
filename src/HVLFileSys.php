<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLArray;

class HVLFileSys
{
	public static function addDirSeparator(string $path, string $separator = '/'): string
	{
		return rtrim($path, $separator) . $separator;
	}

	public static function addFileExtension(string $fileName, string $fileExt): string
	{
		$fileExt = ltrim($fileExt, '.');
		$fileName = self::removeFileExtension($fileName, $fileExt);

		return $fileName . '.' . $fileExt;
	}

	public static function copyDir(string $srcPath, string $destPath): array
	{
		$destPath = self::addDirSeparator($destPath);
		$srcPath = self::addDirSeparator($srcPath);

		if (!self::dirExists($srcPath)) return [];

		$dirTree = self::getDirTree($srcPath);
		$dirTree = HVLArray::pregFilterValues('/^' . preg_quote($srcPath, '/') . '/', '', $dirTree);
		$logData = [];

		self::createDir($destPath);

		foreach ($dirTree as $item) {
			$curPath = $srcPath . $item;
			$newPath = $destPath . $item;

			if (is_dir($curPath)) {
				self::createDir($newPath);
			} else {
				self::copyFile($curPath, dirname($newPath), true);
			}

			$logData[$curPath] = $newPath;
		}

		return $logData;
	}

	public static function copyFile(string $srcPath, string $destPath, bool $replace = false): void
	{
		$fileName = basename($srcPath);
		$destPath = self::addDirSeparator($destPath) . $fileName;

		if (self::fileExists($destPath)) {
			if (!$replace) return;

			self::deleteFiles([$destPath]);
		}

		self::createDir(dirname($destPath));

		copy($srcPath, $destPath);
	}

	public static function createDir(string $dirPath): void
	{
		if (!self::dirExists($dirPath)) mkdir($dirPath, 0777, true);
	}

	public static function createFile(string $filePath, string $data, bool $replace = false): void
	{
		self::createDir(dirname($filePath));

		if (self::fileExists($filePath)) self::deleteFiles([$filePath]);

		file_put_contents($filePath, $data, LOCK_EX);
	}

	public static function deleteDir(array $dirPaths): array
	{
		$deletedDir = [];

		foreach ($dirPaths as $path) {
			if (!self::dirExists($path)) {
				$deletedDir[] = $path;
				continue;
			}

			$dirTree = self::getDirTree($path);

			self::deleteFiles($dirTree);

			rsort($dirTree);

			$result = self::_deleteFileSystemItem($dirTree, true);
			$deletedDir = array_merge($deletedDir, $result);
		}

		return $deletedDir;
	}

	public static function deleteFiles(array $filePaths): array
	{
		return self::_deleteFileSystemItem($filePaths, false);
	}

	public static function dirExists(string $directoryPath): bool
	{
		return file_exists($directoryPath) && is_dir($directoryPath);
	}

	public static function fileExists(string $filePath): bool
	{
		return file_exists($filePath) && is_file($filePath);
	}

	public static function forceDownload(string $contentType, string $fileName, string $data): void
	{
		header("Content-Type: $contentType");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
		echo $data;
	}

	public static function getDirTree(string $path): array
	{
		$tree = [];

		if (self::dirExists($path)) {
			$path = self::addDirSeparator($path);
			$tree[] = $path;
			$list = glob($path . '*');

			foreach ($list as $item) {
				if (is_dir($item)) {
					$tree = array_merge($tree, self::getDirTree($item));
				} else {
					$tree[] = $item;
				}
			}
		}

		return $tree;
	}

	public static function getFilePathExt(string $filePath): string
	{
		$pathInfo = pathinfo($filePath);
		$ext = strlen($pathInfo['filename']) && strlen($pathInfo['extension'] ?? '')
			? $pathInfo['extension']
			: '';

		return $ext;
	}

	public static function getMultiFileContent(array $filePaths): array
	{
		$data = [];

		foreach ($filePaths as $path) {
			$data[$path] = file_get_contents($path);
		}

		return $data;
	}

	public static function moveDirContent(string $srcPath, string $destPath): array
	{
		$result = self::copyDir($srcPath, $destPath);

		self::deleteDir([$srcPath]);

		return $result;
	}

	public static function removeFileExtension(string $fileName, string $fileExt = ''): string
	{
		if (empty($fileName)) throw new \Exception("File name cannot be empty.");

		if ($fileName[-1] === '/') return $fileName;

		$fileExt = ltrim($fileExt, '.');
		$fileName = rtrim($fileName, '.');
		$pathInfo = pathinfo($fileName);
		$piDir = $pathInfo['dirname'];
		$piExt = $pathInfo['extension'] ?? '';
		$piFile = $pathInfo['filename'];

		if (empty($piExt) || empty($piFile)) return $fileName;
		if (empty($fileExt)) $fileExt = $piExt;
		if (strcasecmp($piExt, $fileExt) === 0) return $piDir . '/' . $piFile;

		return $fileName;
	}

	protected static function _deleteFileSystemItem(array $paths, bool $isDir = true): array
	{
		foreach ($paths as $itemPath) {
			if ($isDir) {
				if (!self::dirExists($itemPath)) continue;

				rmdir($itemPath);
			} else {
				if (!self::fileExists($itemPath)) continue;

				unlink($itemPath);
			}

			$deletedItems[] = $itemPath;
		}

		return $deletedItems ?? [];
	}
}
