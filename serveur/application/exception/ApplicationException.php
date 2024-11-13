<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	class ApplicationException extends Exception
	{
		private $infos;

		public function __construct($message, $code = 0, $infos = array())
		{
			parent::__construct($message, $code);
			$this -> infos = $infos;
		}

		public function getInfos()
		{
			return $this -> infos;
		}
	};
