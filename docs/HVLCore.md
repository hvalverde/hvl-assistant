# HVL-Assistant / HVL-Core

This class contains miscellaneous methods. When multiple methods fit under one scope, a separate class is created and the methods are moved to it.

## Class Reference

- HVLCore

	> camelCaseToSnakeCase(string $str): string

		This method converts camel cased string to snake cased string.

		Example:
		```php
		echo HVLCore::camelCaseToSnakeCase('helloWorld'); // Outputs: hello_world
		```

		* DEPRECATED: This method has been moved to the HVLString class.
	
	> execHidden(string $appPath, string $phpPath = '/usr/local/bin/php', bool $testOnly = false): string

		This method executes a PHP application is the background and returns the command executed.

	> getRandomStr(int $length, string $chars = ''): string

		This method returns a random string from the characters provided. If no characters is provided, it will create a range from A-Z, a-z, and 0-9.

		* DEPRECATED: This method has been moved to the HVLString class.

	> getStringRange($start, $end): string

		This method returns a string of the desired character range.

		Example:
		```php
		echo HVLCore::getStringRange('A', 'Z'); // Outputs: ABCDEFGHIJKLMNOPQRSTUVWXYZ
		```

		* DEPRECATED: This method has been moved to the HVLString class.

	> isCli(): bool
	
		This method returns true if app is running in CLI.

	> snakeCaseToCamelCase(string $str): string

		This method converts snake cased string to camel cased.

		Example:
		```php
		echo HVLCore::snakeCaseToCamelCase('hello_world'); // Outputs: helloWorld
		```

		* DEPRECATED: This method has been moved to the HVLString class.