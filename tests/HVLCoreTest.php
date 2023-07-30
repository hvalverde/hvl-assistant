<?php

use HValverde\HVLAssistant\HVLCore;
use PHPUnit\Framework\TestCase;

class HVLCoreTest extends TestCase
{
	public function testExecHidden()
	{
		$command = HVLCore::execHidden('/path/to/app.php', '/usr/local/bin/php', true);
		$expected = '/usr/local/bin/php -f /path/to/app.php > /dev/null 2>/dev/null &';

		$this->assertTrue($command === $expected);
	}

	public function testIsCli()
	{
		$this->assertTrue(HVLCore::isCli());
	}
}
