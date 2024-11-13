<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/communeDb.php');

	/**
	 * Classe wsCommune - endpoint du webservice Commune
	 * Gestion des commune
	 * @access root superadmin admin
	 */
	final class wsCommune extends wsEndpoint
	{
	
		/**
		 * Retourne une commune via son code INSEE
		 * @param string $codeInsee : code INSEE de la commune
		 * @return communeModele commune : la commune demandée
		 * @access root superadmin admin
		 */
		protected function _getCommune($codeInsee)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			return array('commune' => communeDb::getCommune($codeInsee));
		}
		
		
	}
