<?php

class OST_Factory
{
	
	static $instances = array();
	
	public static function getInstance($name)
	{
		$key = strtolower($name);
		if ( ! isset(self::$instances[$key]))
		{
			self::$instances[$key] = new $name();
		}
		
		return self::$instances[$key];
	}
	
}