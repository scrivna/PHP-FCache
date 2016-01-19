# PHP FCache
Cache &amp; read data to / from the filesystem

## Usage
```php
$cache_key = 'your-cache-key';
if (!$data = FCache::get($cache_key)){
	$data = 'bob';
	FCache::set($cache_key, $data, 60*60);
}
// $data now contains your data.
```
```php
FCache::set($cache_key, $data, $expires_in_seconds);
```
