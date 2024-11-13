<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/communeModele.php');
	
	final class communeDb
	{
	
		const SQL_COMMUNE = "SELECT codeInsee, codePostal, codePays, libelle, gpsLat, gpsLng FROM sitCommune WHERE codeInsee='%s'";
		
		
		public static function getCommune($codeInsee)
		{
			$result = tsDatabase::getObject(self::SQL_COMMUNE, array($codeInsee), DB_FAIL_ON_ERROR);
			$oCommune = communeModele::getInstance($result, 'communeModele');
			return $oCommune;
		}

		
	}
