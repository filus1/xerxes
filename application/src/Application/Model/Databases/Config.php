<?php

namespace Application\Model\Databases;

use Xerxes\Utility\Registry;

/**
 * Databases Config
 *
 * @author David Walker <dwalker@calstate.edu>
 */

class Config extends Registry
{
	protected $config_file = "config/databases";
	private static $instance; // singleton pattern
	
	/**
	 * Get an instance of the file; Singleton to ensure correct data
	 *
	 * @return Config
	 */	
	
	public static function getInstance()
	{
		if ( empty( self::$instance ) )
		{
			self::$instance = new Config();
		}
		
		return self::$instance;
	}
}
