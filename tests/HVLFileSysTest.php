<?php

use HValverde\HVLAssistant\HVLArray;
use HValverde\HVLAssistant\HVLFileSys;
use PHPUnit\Framework\TestCase;

class HVLFileSysTest extends TestCase
{
	const FILE_SYS_DIR = __DIR__ . '/file_sys_dir/';
	const FILE_SYS_TREE = [
		'file1.txt',
		'file2.txt',
		'folder1' => [
			'file1_1.txt',
			'file1_2.txt',
			'folder1_1' => [
				'file1_1_1.txt',
				'file1_1_2.txt'
			],
			'folder1_2' => [
				'file1_2_1.txt',
				'file1_2_2.txt'
			]
		],
		'folder2' => [
			'file2_1.txt',
			'file2_2.txt',
			'folder2_1' => [
				'file2_1_1.txt',
				'file2_1_2.txt'
			],
			'folder2_2' => [
				'file2_2_1.txt',
				'file2_2_2.txt'
			]
		]
	];

	public function createFileSysDirTree(array $tree, string $path = self::FILE_SYS_DIR)
	{
		foreach ($tree as $key => $value) {
			if (is_array($value)) {
				$this->createFileSysDirTree($value, $path . $key . '/');
			} else {
				if (!is_dir($path)) mkdir($path, 0777, true);

				if (file_exists($path . $value)) unlink($path . $value);

				file_put_contents($path . $value, 'Hello world!');
			}
		}
	}

	public function deleteFileSysDirTree(array $tree, string $path = self::FILE_SYS_DIR)
	{
		foreach ($tree as $key => $value) {
			if (is_array($value)) {
				$this->deleteFileSysDirTree($value, $path . $key . '/');
			} else {
				if (file_exists($path . $value) && is_file($path . $value)) unlink($path . $value);
			}
		}

		if (is_dir($path)) rmdir($path);
	}

	public function setUp(): void
	{
		parent::setUp();

		$this->createFileSysDirTree(self::FILE_SYS_TREE);
	}

	public function tearDown(): void
	{
		parent::tearDown();

		HVLFileSys::deleteDir([self::FILE_SYS_DIR]);
	}

	public function testAppendDirSeparator()
	{
		$dirPath = '/path/to/dir';
		$dirPathWithSeparator = '/path/to/dir/';

		$this->assertTrue(HVLFileSys::appendDirSeparator($dirPath) === $dirPathWithSeparator);
		$this->assertTrue(HVLFileSys::appendDirSeparator($dirPathWithSeparator) === $dirPathWithSeparator);
	}

	public function testAppendFileExtension()
	{
		$fileName = 'file';
		$fileExt = 'txt';
		$fileNameWithExt = 'file.txt';

		$this->assertTrue(HVLFileSys::appendFileExtension($fileName, $fileExt) === $fileNameWithExt);
		$this->assertTrue(HVLFileSys::appendFileExtension($fileNameWithExt, $fileExt) === $fileNameWithExt);
	}

	public function testCopyDir()
	{
		$destPath = rtrim(self::FILE_SYS_DIR, '/') . '_copy/';
		$destTree = HVLFileSys::copyDir(self::FILE_SYS_DIR, $destPath);
		$destTree = array_values($destTree);
		$destTree = HVLArray::pregReplaceValues(
			'/^' . preg_quote($destPath, '/') . '/',
			'',
			$destTree
		);
		$srcTree = HVLFileSys::getDirTree(self::FILE_SYS_DIR);
		$srcTree = array_values($srcTree);
		$srcTree = HVLArray::pregReplaceValues(
			'/^' . preg_quote(self::FILE_SYS_DIR, '/') . '/',
			'',
			$srcTree
		);

		$this->assertTrue($destTree === $srcTree);

		HVLFileSys::deleteDir([$destPath]);
	}

	public function testCopyFile()
	{
		$srcPath = self::FILE_SYS_DIR . 'file1.txt';
		$destPath = self::FILE_SYS_DIR . 'folder1/file1.txt';

		HVLFileSys::copyFile($srcPath, $destPath);

		$this->assertTrue(file_exists($destPath));

		HVLFileSys::deleteFiles([$destPath]);
	}

	public function testCreateDir()
	{
		$dirPath = self::FILE_SYS_DIR . 'folder1/folder1_1/folder1_1_1/';

		HVLFileSys::createDir($dirPath);

		$this->assertTrue(is_dir($dirPath));

		HVLFileSys::deleteDir([$dirPath]);
	}

	public function testCreateFile()
	{
		$filePath = self::FILE_SYS_DIR . 'folder1/folder1_1/folder1_1_1/file1_1_1_1.txt';
		$fileData = 'test';

		HVLFileSys::createFile($filePath, $fileData);

		$this->assertTrue(file_exists($filePath));

		HVLFileSys::deleteFiles([$filePath]);
	}

	public function testDeleteDir()
	{
		$dirPath = self::FILE_SYS_DIR . 'folder1/folder1_1/folder1_1_1/';

		HVLFileSys::createDir($dirPath);

		$this->assertTrue(is_dir($dirPath));

		HVLFileSys::deleteDir([$dirPath]);

		$this->assertFalse(is_dir($dirPath));
	}

	public function testDeleteFiles()
	{
		$filePath = self::FILE_SYS_DIR . 'folder1/folder1_1/folder1_1_1/file1_1_1_1.txt';

		HVLFileSys::createFile($filePath, '');

		$this->assertTrue(file_exists($filePath));

		HVLFileSys::deleteFiles([$filePath]);

		$this->assertFalse(file_exists($filePath));
	}

	public function testDirExists()
	{
		$this->assertTrue(HVLFileSys::dirExists(self::FILE_SYS_DIR));
	}

	public function testFileExists()
	{
		$this->assertTrue(HVLFileSys::fileExists(self::FILE_SYS_DIR . 'file1.txt'));
	}

	public function testGetDirTree()
	{
		$expectedRes = HVLArray::multiToAssociative(self::FILE_SYS_TREE, '/');
		$receivedRes = HVLFileSys::getDirTree(self::FILE_SYS_DIR);
		$receivedRes = HVLArray::pregReplaceValues('/^.*\/$/', '', $receivedRes);

		$this->assertTrue(count($expectedRes) === count($receivedRes));
	}

	public function testGetFilePathExt()
	{
		$filePath = self::FILE_SYS_DIR . 'file1.txt';
		$fileExt = 'txt';

		$this->assertTrue(HVLFileSys::getFilePathExt($filePath) === $fileExt);
	}

	public function testGetMultiFileContent()
	{
		$expectedRes = [
			'file1.txt' => 'Hello world!',
			'file2.txt' => 'Hello world!',
			'folder1/file1_1.txt' => 'Hello world!',
			'folder1/file1_2.txt' => 'Hello world!',
			'folder1/folder1_1/file1_1_1.txt' => 'Hello world!',
			'folder1/folder1_1/file1_1_2.txt' => 'Hello world!',
			'folder1/folder1_2/file1_2_1.txt' => 'Hello world!',
			'folder1/folder1_2/file1_2_2.txt' => 'Hello world!',
			'folder2/file2_1.txt' => 'Hello world!',
			'folder2/file2_2.txt' => 'Hello world!',
			'folder2/folder2_1/file2_1_1.txt' => 'Hello world!',
			'folder2/folder2_1/file2_1_2.txt' => 'Hello world!',
			'folder2/folder2_2/file2_2_1.txt' => 'Hello world!',
			'folder2/folder2_2/file2_2_2.txt' => 'Hello world!'
		];
		$expectedRes = HVLArray::pregReplaceKeys(
			'/^(.*)$/',
			self::FILE_SYS_DIR . '$1',
			$expectedRes
		);
		$filePaths = HVLFileSys::getDirTree(self::FILE_SYS_DIR);
		$filePaths = array_values($filePaths);
		$filePaths = HVLArray::pregReplaceValues('/^.*\/$/', '', $filePaths);
		$receivedRes = HVLFileSys::getMultiFileContent($filePaths);

		$this->assertTrue($expectedRes === $receivedRes);
	}

	public function testMoveDirContent()
	{
		$srcPath = self::FILE_SYS_DIR . 'folder1/';
		$destPath = self::FILE_SYS_DIR . 'folder2/';
		$expectedCount = count(HVLFileSys::getDirTree($srcPath)) + count(HVLFileSys::getDirTree($destPath)) - 1;

		HVLFileSys::moveDirContent($srcPath, $destPath);

		$this->assertTrue(count(HVLFileSys::getDirTree($destPath)) === $expectedCount);
	}

	public function testRemoveFileExtension()
	{
		$fileName = 'file.txt';
		$fileExt = 'txt';
		$fileNameWithoutExt = 'file';

		$this->assertTrue(HVLFileSys::removeFileExtension($fileName, $fileExt) === $fileNameWithoutExt);
		$this->assertTrue(HVLFileSys::removeFileExtension($fileNameWithoutExt, $fileExt) === $fileNameWithoutExt);
	}
}
