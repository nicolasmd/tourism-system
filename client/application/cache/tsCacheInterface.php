<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	interface tsCacheInterface
	{

		public function set($varName, $value, $timeOut = null, $noUser = false);

		public function get($varName, $noUser = false);

		public function delete($varName);

		public function purge();
	}

?>
