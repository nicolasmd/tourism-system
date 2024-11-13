<?php

	/**
	 * @version        0.4 alpha-test - 2013-06-03
	 * @package        Tourism System Client
	 * @copyright      Copyright (C) 2010 Raccourci Interactive
	 * @license        GNU GPLv3 ; see LICENSE.txt
	 * @author         Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	final class tsCache
	{

		private static $me;
		private static $instance;
		private static $cacheType;
		private static $purge;
		private static $shortKeyMode = false;

		/**
		 * Constructeur de la classe tsCache
		 *
		 * @param string $cacheType : type de cache à utiliser
		 */
		private function __construct($cacheType)
		{
			if (defined('CACHE_LOADED'))
			{
				throw new Exception("Le cache est déjà initialisé");
			}

			define('CACHE_LOADED', true);
			self::$cacheType = $cacheType;
			$this->factory();
		}

		/**
		 * Singleton
		 *
		 * @param string $databaseType : type de connection à utiliser
		 *
		 * @return object : instance de la classe tsDatabase
		 */
		public static function load($cacheType, $purge = false)
		{
			if (isset(self::$me) === false)
			{
				$c = __CLASS__;
				self::$me = new $c(strtolower($cacheType));
				self::$purge = $purge;
			}
		}

		/**
		 * Factory de la classe tsCache[type]
		 */
		private function factory()
		{
			switch (self::$cacheType)
			{
				case 'mysql':
					require_once( '../cache/tsCacheMySQL.php' );
					self::$instance = new tsCacheMySQL();
					break;
				case 'redis':
					require_once( '../cache/tsCacheRedis.php' );
					self::$instance = new tsCacheRedis();
					break;
				case 'apc':
					require_once( '../cache/tsCacheAPC.php' );
					self::$instance = new tsCacheAPC();
					break;
				case 'memcache':
					require_once( '../cache/tsCacheMemcache.php' );
					self::$instance = new tsCacheMemcache();
					break;
				case 'session':
					require_once( '../cache/tsCacheSession.php' );
					self::$instance = new tsCacheSession();
					break;
				case 'nocache':
					require_once( '../cache/tsCacheNocache.php' );
					self::$instance = new tsCacheNocache();
					break;
				default:
					throw new Exception("Le cache n'a pas pu être initialisé");
					break;
			}
		}

		public static function set(&$varName, &$value, $timeOut = null)
		{
			return self::$instance->set(self::makeKey($varName), $value, $timeOut);
		}

		public static function &get(&$varName)
		{
			if (self::$purge === true)
			{
				$retour = false;
			}
			else
			{
				$retour = self::$instance->get(self::makeKey($varName));
			}

			return $retour;
		}

		public static function delete(&$varName)
		{
			self::$instance->delete(self::makeKey($varName));
		}

		private static function &makeKey(&$varName)
		{
			if (self::$shortKeyMode)
			{
				$retour = TS_CACHE_PREFIXE . '.' . md5(json_encode($varName));
			}
			else
			{
				$retour = TS_CACHE_PREFIXE . '.' . serialize($varName);
			}

			return $retour;
		}

	}

?>
