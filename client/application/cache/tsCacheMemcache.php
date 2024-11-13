<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	final class tsCacheMemcache implements tsCacheInterface
	{

		const timeout = 21600; // six heures

		public function __construct()
		{
			if (extension_loaded('memcache') === false)
			{
				throw new Exception("memcache n'est pas installé sur le serveur");
			}

			$this->memcache = new Memcache();
			$this->memcache->pconnect(TS_MEMCACHE_SERVER, TS_MEMCACHE_PORT);
		}

		public function set($varName, $value, $timeOut = null, $noUser = false)
		{
			if (is_null($timeOut) || !is_numeric($timeOut))
			{
				$timeOut = self::timeout;
			}
			return($this->memcache->set($varName, $value, 0, $timeOut));
		}

		public function get($varName, $noUser = false)
		{
			return($this->memcache->get($varName));
		}

		public function delete($varName)
		{
			return($this->memcache->delete($varName));
		}

		public function purge()
		{

		}

	}

?>
