<?php

use HValverde\HVLAssistant\HVLUrl;
use PHPUnit\Framework\TestCase;

class HVLUrlTest extends TestCase
{
	public function testGetFullUrl()
	{
		$this->assertTrue(
			HVLUrl::getFullUrl() === 'http://localhost'
		);
	}

	public function testGetUrlHost()
	{
		$this->assertTrue(
			HVLUrl::getUrlHost('http://localhost') === 'localhost'
		);
	}

	public function testGetUrlPath()
	{
		$this->assertTrue(
			HVLUrl::getUrlPath('http://localhost/controller/method') === 'controller/method'
		);
	}

	public function testValidUrl()
	{
		$this->assertTrue(
			HVLUrl::validUrl('http://localhost')
		);
	}
}
