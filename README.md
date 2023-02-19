# HVL-Assistant

HVL Assistant is a lightweight PHP library that provides a set of utility functions and classes to simplify common tasks in PHP development. It is designed to be easy to use, flexible, and extensible, making it a great choice for all types of PHP projects.

## Features

Some of the key features of HVL Assistant include:

- Array key manipulation.
- CSV/JSON file loader.
- File system management.
- WordPress API client.

## Requirements

HVL Assistant was tested using PHP 8.1.

## Installation

You can use Composer to install HVL Assistant as a dependency in your project:

```
composer require hvalverde/hvl-assistant
```

## Usage

Once you've installed HVL Assistant, you can use the composer autoloader with the following library namespaces:

- HValverde\HVLAssistant\HVLArray
- HValverde\HVLAssistant\HVLCore
- HValverde\HVLAssistant\HVLCsv
- HValverde\HVLAssistant\HVLFileSys
- HValverde\HVLAssistant\HVLJson
- HValverde\HVLAssistant\HVLUrl
- HValverde\HVLAssistant\HVLWordPress

## Examples

The following example will save all of the JSON data from the WordPress API and download all of the media files.

```php
use HValverde\HVLAssistant\HVLWordPress;

$wp_host = 'https://www.my-wordpress-domain.com';
$wp_json =  __DIR__ . '/wp_json/';
$wp_media = __DIR__ . '/wp_media/';
$wp = new HVLWordPress($wp_host, $wp_json, $wp_media);

$wp->saveAllData();
$wp->downloadMedia();
```

## License

HVL Assistant is licensed under the MIT License. See [LICENSE] for more information.