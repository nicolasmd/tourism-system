<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	abstract class tsProxy
	{

		public function __construct()
		{
			if (isset(PSession::$SESSION['tsSessionId']) === false)
			{
				throw new SessionException("La session est expirée");
			}
			tsDroits::loadDroits();
			tsCache::load(PSession::$SESSION['nocache'] === true ? 'nocache' : TS_CACHE, is_null(PSession::$SESSION['purgecache']) === false);
		}

	}

?>
