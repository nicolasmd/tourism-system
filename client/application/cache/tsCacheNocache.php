<?php

	require_once('tsCacheInterface.php');

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	final class tsCacheNocache implements tsCacheInterface
	{

		public function __construct()
		{

		}

		public function set($varName, $value, $timeOut = null, $noUser = false)
		{

		}

		public function get($varName, $noUser = false)
		{
			$retour = false;
			return $retour;
		}

		public function delete($varName)
		{

		}

		public function purge()
		{

		}

	}

?>
