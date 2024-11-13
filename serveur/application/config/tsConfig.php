<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsConfig
	{
		
		const CONFIG_PATH = "application/common/%s.php";
		
		private static $instance;
		
		
		private function __construct()
		{
			
		}
		
		public static function load()
	    {
	        if (isset(self::$instance) === false)
			{
	            $c = __CLASS__;
	            self::$instance = new $c();
	        }
	    }
		
		
		public static function loadConfig($file)
		{
			$fileRealpath = sprintf(self::CONFIG_PATH, $file);
			if (is_file($fileRealpath) === false)
			{
				throw new ApplicationException("Le fichier de configuration $file est introuvable");
			}
			require_once($fileRealpath);
		}
		
		
		public static function get($constName, $serialized = false)
		{
			if (defined($constName) === false)
			{
				throw new ConfigException("La constante $constName n'est pas définie");	
			}
			
			if ($serialized === true)
			{
				$value = unserialize(constant($constName));
				if ($value === false)
				{
					throw new ConfigException("La variable $constName n'a pas pu être déserialisée");
				}
			}
			else
			{
				$value = constant($constName);
			}
			return $value;
		}
		
		
		public static function set($constName, $value)
		{
			if (defined($constName) === true)
			{
				throw new ConfigException("La constante $constName est déjà définie");				
			}
			
			if (is_object($value) || is_array($value))
			{
				$value = serialize($value);				
			}
			
			define($constName, $value);
		}

	}
