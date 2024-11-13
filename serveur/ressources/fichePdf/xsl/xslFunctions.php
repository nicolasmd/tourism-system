<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	/**
	 * Classe xslFunctions
	 * 
	 */
	final class xslFunctions extends wsEndpoint
	{

		public static function convertirDate($date)
		{
			$timestamp = strtotime($date);

			$date_array = array_reverse(explode('-', $date));

			$jours_array = explode('/', date("N/n", $timestamp));

			$jours = array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");
			$mois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

			$retour = $jours[$jours_array[0]-1] . " " . $date_array[0] . " " . $mois[$jours_array[1]-1] . " " . $date_array[2];
			
			return $retour;
		}
		
		public static function traduireTIF($codeTIF)
		{
			try
			{
				$entreeThesaurus = thesaurusDb::getEntreeThesaurus($codeTIF, 'fr');
				$libelle = $entreeThesaurus -> libelle;
			}
			catch (DatabaseException $e)
			{
				$libelle = '';
			}
			
			return $libelle;
		}
	}


?>