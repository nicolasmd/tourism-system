<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */


	final class tsCacheMemcache implements tsCacheInterface
	{
		
		const timeout = 21600;
		
		public function __construct()
		{
			if (extension_loaded('memcache') === false)
			{
				throw new ApplicationException("memcache n'est pas installé sur le serveur");
			}

			$this -> memcache = new Memcache();
			$this -> memcache -> pconnect(tsConfig::get('TS_MEMCACHE_SERVER'), tsConfig::get('TS_MEMCACHE_PORT'));
		}
		
		
		public function set($varName, $value, $timeOut = null)
		{
			if (is_null($timeOut) || !is_numeric($timeOut))
			{
				$timeOut = self::timeout;
			}
			return($this -> memcache -> set($varName, $value, 0, $timeOut));
		}
		
		
		public function get($varName)
		{
			return($this -> memcache -> get($varName));
		}
		
		
		public function delete($varName)
		{
			return($this -> memcache -> delete($varName));
		}
		
		
	}
