<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	final class tsCacheSession implements tsCacheInterface
	{

		public function __construct()
		{

		}

		public function set($varName, $value, $timeOut = null, $noUser = false)
		{
			PSession::$SESSION[$varName] = $value;
		}

		public function get($varName, $noUser = false)
		{
			return (isset(PSession::$SESSION[$varName]) ? PSession::$SESSION[$varName] : false);
		}

		public function delete($varName)
		{
			unset(PSession::$SESSION[$varName]);
		}

		public function purge()
		{

		}

	}

?>