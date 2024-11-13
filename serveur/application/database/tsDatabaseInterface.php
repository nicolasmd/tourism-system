<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	interface tsDatabaseInterface
	{

		public function &query($sql, array $params);

		public function insert($sql, array $params);

		public function &getRecord($sql, array $params);

		public function &getRecords($sql, array $params);

		public function &getObjects($sql, array $params);

		public function &getRows($sql, array $params);

		public function connect($dbServer, $dbLogin, $dbPassword);

		public function selectDatabase($dbName);

	}
