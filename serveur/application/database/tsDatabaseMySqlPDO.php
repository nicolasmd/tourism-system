<?php

	/**
	 * @version        0.4 alpha-test - 2013-06-03
	 * @package        Tourism System Server
	 * @copyright      Copyright (C) 2010 Raccourci Interactive
	 * @license        Qt Public License; see LICENSE.txt
	 * @author         Nicolas Marchand <nicolas.raccourci@gmail.com>
	 */

	defined( 'DATABASE_LOADED' ) or die;

	final class tsDatabaseMysqlPDO // implements tsDatabaseInterface
	{

		private $port = '3306';
		private static $pdo = false;
		private $dbName = false;

		static $prepared = array();


		public function __construct()
		{
		}


		public function connect( $dbServer, $dbLogin, $dbPassword )
		{
			$dsn = 'mysql:dbname=' . $this->dbName . ';host=' . $dbServer . ';charset=utf-8';

			try
			{
				self::$pdo = new PDO( $dsn, $dbLogin, $dbPassword );
			}
			catch( PDOException $e )
			{
				throw new DatabaseException( "La connexion au serveur BDD a échoué : " . $e->getMessage() );
			}
		}



		public function &query( $sql, array $params )
		{
			return $this->execute( $sql, $params );
		}


		public function insert( $sql, array $params )
		{
			$this->execute( $sql, $params );
			return ( self::$pdo->lastInsertId() );
		}


		public function &getRows( $sql, array $params )
		{
			$rows   = array();
			$result = $this->execute( $sql, $params );
			while( $row = $result->fetch( PDO::FETCH_ASSOC ) )
			{
				$rows[] = $row;
			}

			return ( $rows );
		}


		/*public function getRow($sql, array $params)
		{
			$this -> checkConnection();
			$this -> cleanParams($params);
			$result = $this -> execute(vsprintf($sql, $params));
			//if (mysql_num_rows($result) != 1) return false;
			return mysql_fetch_assoc($result);
		}*/


		public function &getObjects( $sql, array $params )
		{
			$result  = $this->execute( $sql, $params );
			$objects = array();
			while( $obj = $result->fetchObject() )
			{
				$objects[] = $obj;
			}

			return $objects;
		}


		public function &getRecord( $sql, array $params )
		{
			$result = $this->execute( $sql, $params );
			if( mysql_num_rows( $result ) != 1 )
			{
				return false;
			}

			return mysql_result( $result, 0 );
		}



		public function &getRecords( $sql, array $params )
		{
			$result  = $this->execute( $sql, $params );
			$records = array();
			while( $row = $result->fetchAll((PDO::FETCH_ASSOC)) ) // mysql_fetch_row
			{
				$records[] = $row[0];
			}

			return $records;
		}



		public function selectDatabase( $dbName )
		{
			$this->dbName = $dbName;
		}


		/**
		 * @return PDOStatement
		 */
		private function execute( $sql, $params )
		{
			$this->checkConnection();
			$this->cleanParams( $params );

			$sql    = vsprintf( $sql, $params );
			$result = mysql_query( $sql, $this->connexion );

			if( $result === false )
			{
				throw new DatabaseException( "La requête $sql a échoué : " . mysql_error() );
			}

			return ( $result );
		}



		private function checkConnection()
		{
			if( is_resource( $this->connexion ) === false )
			{
				throw new DatabaseException( "La connexion à la BDD n'est pas configurée" );
			}
		}



		private function isDatabaseSelected()
		{
			return ( $this->dbName !== false );
		}


		private function cleanParams( &$params )
		{
			foreach( $params as &$param )
			{
				if( is_array( $param ) )
				{
					foreach( $param as $k => &$v )
					{
						$param[$k] = mysql_real_escape_string( $v, $this->connexion );
					}
					$param = implode( "','", $param );
				}
				else
				{
					$param = mysql_real_escape_string( $param, $this->connexion );
				}
			}

			return ( $params );
		}

	}
