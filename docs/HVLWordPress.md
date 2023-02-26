# HVL-Assistant / HVL-WordPress

## Examples

The following example will save all of the JSON data from the WordPress API and download all of the media files.

```php
use HValverde\HVLAssistant\HVLWordPress;

require_once './vendor/autoload.php';

$wp_host = 'https://www.my-wordpress-domain.com';
$wp_json = __DIR__ . '/wp_json/';
$wp_media = __DIR__ . '/wp_media/';
$wp = new HVLWordPress($wp_host, $wp_json, $wp_media);

$wp->saveAllData();
$wp->downloadMedia();
```