<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	define('DB_FAIL_ON_ERROR', 1);

	final class tsDatabase
	{

		private static $controller;
		private static $instance;
		private static $databaseType;


		/**
		 * Constructeur de la classe tsDatabase
		 * @param string $databaseType : type de connection à utiliser
		 */
		private function __construct($databaseType)
		{
			if (defined('DATABASE_LOADED'))
			{
				throw new ApplicationException("L'accès à la BDD est déjà chargé");
			}

			define('DATABASE_LOADED', true);
			self::$databaseType = $databaseType;
			$this -> factory();
		}



		/**
		 * Singleton
		 * @param string $databaseType : type de connection à utiliser
		 * @return object : instance de la classe tsDatabase
		 */
		public static function load($databaseType)
	    {
	        if (isset(self::$instance) === false)
			{
	            $c = __CLASS__;
	            self::$instance = new $c(strtolower($databaseType));
	        }
	    }



		/**
		 * Factory de la classe tsDatabase[type]
		 */
		private function factory()
		{
			switch (self::$databaseType)
			{
				case 'mysql':
					require_once('application/database/tsDatabaseMySql.php');
					self::$controller = new tsDatabaseMySql();
				break;
				default:
					throw new ApplicationException("Le type de BDD demandé n'est pas prévu");
				break;
			}
		}






		public static function &query($sql, array $params)
		{
			return self::$controller -> query($sql, $params);
		}

		public static function insert($sql, array $params)
		{
			return self::$controller -> insert($sql, $params);
		}

		public static function &getRecord($sql, array $params)
		{
			return self::$controller -> getRecord($sql, $params);
		}

		public static function &getRecords($sql, array $params)
		{
			return self::$controller -> getRecords($sql, $params);
		}

		public static function &getRow($sql, array $params)
		{
			$result = self::$controller -> getRows($sql, $params);
			$args = func_get_args();
			if (count($result) != 1 &&
				array_key_exists(2, $args) &&
				($args[2] & DB_FAIL_ON_ERROR))
			{
				throw new DatabaseException("Erreur : aucun enregistrement retourné : " . vsprintf($sql, $params));
			}
			return $result[0];
		}

		public static function &getObject($sql, array $params)
		{
			$result = self::$controller -> getObjects($sql, $params);
			$args = func_get_args();
			if (count($result) != 1 &&
				array_key_exists(2, $args) &&
				($args[2] & DB_FAIL_ON_ERROR))
			{
				throw new DatabaseException("Erreur : aucun enregistrement retourné : " . vsprintf($sql, $params));
			}
			return $result[0];
		}


		public static function &getObjects($sql, array $params)
		{
			return self::$controller -> getObjects($sql, $params);
		}


		public static function &getRows($sql, array $params)
		{
			return self::$controller -> getRows($sql, $params);
		}

		public static function connect($dbServer, $dbLogin, $dbPassword)
		{
			return self::$controller -> connect($dbServer, $dbLogin, $dbPassword);
		}

		public static function selectDatabase($dbName)
		{
			return self::$controller -> selectDatabase($dbName);
		}



	}
