<?php
// Serializes and caches data to a filesystem
// Before use set the "cache_dir" to the location you wish to store your tmp files

/*
$cache_key = 'your-cache-key';
if (!$data = FCache::get($cache_key)){
	$data = 'bob';
	FCache::set($cache_key, $data, 60*60);
}

// $data now contains your data.
*/

class FCache {
	
	private $data;
	static $set_keys;
	static $fetched_keys;
	
	// expires = seconds until it expires eg 86400
	static function set($key,$value,$expires=0){
		$_this =& self::getInstance();
		
		$content = array();
		$content['key'] 	= $key;
		$content['created'] = time();
		$content['expires'] = time()+$expires;
		$content['data'] 	= $value;
		file_put_contents($_this->cache_dir.$key, serialize($content));
		
		self::$set_keys[] = $key;
	}
	
	static function get($key, $time=null){
		if (isset($_GET['nocache'])) return false;
		
		if ($time===null) $time = time();
		
		$_this =& self::getInstance();
		if (!file_exists($_this->cache_dir.$key) || !is_readable($_this->cache_dir.$key)){
			return false;
		}
		
		$content = file_get_contents($_this->cache_dir.$key);
		$content = unserialize($content);
		if ($content['expires'] < $time){
			// remove the cached file
			unlink($_this->cache_dir.$key);
			return false;
		}
		self::$fetched_keys[] = $key;
		return $content['data'];
	}
	
	static public function clear($key){
		$_this =& self::getInstance();
		if (!file_exists($_this->cache_dir.$key) || !is_readable($_this->cache_dir.$key)){
			return;
		}
		unlink($_this->cache_dir.$key);
	}
	
	// clear all caches with a key starting...
	static public function clearAll($key){
		$_this =& self::getInstance();
		
		// loop over all files and remove the ones that match
		if ($handle = opendir($_this->cache_dir)) {
		    while (false !== ($entry = readdir($handle))) {
		    	if (substr($entry, 0, strlen($key)) == $key){
		        	unlink($_this->cache_dir.$entry);
		        }
		    }
		    closedir($handle);
		}
	}
	
	static function &getInstance(){
		static $instance;
		if (!isset($instance)) {
		   $c = __CLASS__;
		   $instance = new $c;
		   $instance->cache_dir = realpath(dirname(__FILE__).'/../tmp/fcache/').'/';
		}
		return $instance;
	}
	
	
}
?>
