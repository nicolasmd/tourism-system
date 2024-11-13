<?php
	require_once('application/database/tsDatabase.php');

	/**
	 * @version        0.4 alpha-test - 2013-06-03
	 * @package        Tourism System Client
	 * @copyright      Copyright (C) 2010 Raccourci Interactive
	 * @license        GNU GPLv3 ; see LICENSE.txt
	 * @author         Benjamin
	 */

	final class tsCacheMySQL implements tsCacheInterface
	{

		const timeout = 3600; // une heure

		public function __construct()
		{
			try
			{
				tsDatabase::load( TS_BDD_TYPE );
				tsDatabase::connect( TS_BDD_SERVER , TS_BDD_USER , TS_BDD_PASSWORD );
				tsDatabase::selectDatabase( TS_BDD_NAME );
			}
			catch( Exception $e )
			{

			}
		}


		// return false si erreur
		public function set($varName , $value , $timeOut = null)
		{
			if( is_null( $timeOut ) || !is_numeric( $timeOut ) )
			{
				$timeOut = self::timeout;
			}
			try
			{
				$type = gettype( $value );
				if( $type == 'object' )
				{
					$type = get_class( $value );
				}
				$serialized = serialize($value);
				tsDatabase::query( 'REPLACE INTO dCacheS VALUES(\'%1$s\',\'%2$s\',UNIX_TIMESTAMP(),UNIX_TIMESTAMP()+%3$s,\'%4$s\',0) ;' , array( $varName , $type , $timeOut , $serialized ) );
			}
			catch( Exception $e )
			{
				return false;
			}
			return true;
		}


		// false si échec
		public function get($varName)
		{
			$retour = false;
			try
			{
				tsDatabase::query( 'UPDATE dCacheS SET hits = hits+1 WHERE dkey = \'%1$s\' AND datee >= UNIX_TIMESTAMP() ;' , array( $varName ) );
				$sqlretour = tsDatabase::getRecord( 'SELECT dvalue FROM dCacheS WHERE dkey = \'%1$s\' AND datee >= UNIX_TIMESTAMP() ;' , array( $varName ) );
				$retour    = unserialize( $sqlretour );
			}
			catch( Exception $e )
			{
				return false;
			}
			return $retour;
		}


		public function delete($varName)
		{
			try
			{
				tsDatabase::query( 'DELETE FROM dCacheS WHERE dkey = \'%1$s\' LIMIT 1;' , array( $varName ) );
			}
			catch( Exception $e )
			{
				throw $e;
			}
		}

	}
