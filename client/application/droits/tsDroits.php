<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	class tsDroits
	{

		public static function loadDroits()
		{
			// Droits généraux
			if (isset(PSession::$SESSION['typeUtilisateur']))
			{
				require_once(TS_CLIENT_PATH . 'application/droits/' . PSession::$SESSION['typeUtilisateur'] . '.php');
			}

			// Droits des plugins
			tsPlugins::loadDroits();
		}

		public static function getDroit($droit)
		{
			return (defined($droit) && constant($droit) === true);
		}

		public static function printDroit($droit)
		{
			echo (defined($droit) && constant($droit) === true) ? 'true' : 'false';
		}

		public static function checkDroit($droit)
		{
			if (!(defined($droit) && constant($droit) === true))
			{
				header('Refresh:1; url=' . TS_CLIENT_URL, true);
				throw new Exception('Access denied.');
			}
		}

	}

?>
