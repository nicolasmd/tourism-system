<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/champDb.php');
	require_once('application/db/ficheDb.php');
	require_once('application/db/ficheValidationDb.php');

	/**
	 * Classe wsFicheValidation - endpoint du webservice FicheValidation
	 * Gestion des validations à effectuer par les administrateurs
	 * Accessible aux utilisateurs root, superadmin, admin
	 */
	final class wsFicheValidation extends wsEndpoint
	{
		
		/**
		 * Fiches à valider par l'utilisateur courant
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @return array fiches : ficheSimpleCollection collection de ficheSimpleModele
		 */
		protected function _getFichesAValider()
		{
			$this -> restrictAccess('superadmin', 'admin');
			$fiches = ficheValidationDb::getFichesAValider();
			return array('fiches' => $fiches);
		}
		
		
		/**
		 * Champs d'une fiche à valider
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @param int idFiche : identifiant de la fiche sitFiche.idFiche
		 * @return array champs : champFicheValidationCollection collection de champFicheValidationModele
		 */
		protected function _getChampsFicheAValider($idFiche)
		{
			//$this -> restrictAccess('superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkAccesFiche($oFiche);
			$champs = ficheValidationDb::getChampsFicheAValider($oFiche);
			return array('champs' => $champs);
		}
		
		
		/**
		 * Validation d'une valeur de champ d'une fiche
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @param int idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int idChamp : identifiant du champ sitChamp.idChamp
		 */
		/*protected function _accepteChampFiche($idFiche, $idChamp)
		{
			$this -> restrictAccess('superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$oChamp = champDb::getChamp($idChamp);
			//$this -> checkDroitFicheChamp($oFiche, $oChamp, DROIT_VALIDATION);
			$oChampFicheValidation = ficheValidationDb::getChampFicheAValider($oFiche, $oChamp);
			ficheValidationDb::accepteChampFiche($oChampFicheValidation);
			return array();
		}*/
		
		
		/**
		 * Refus d'une valeur de champ d'une fiche
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @param int idValidationChamp : identifiant du champ à valider sitFicheValidationChamp.idValidationChamp
		 */
		protected function _refuseChampFiche($idValidationChamp)
		{
			$this -> restrictAccess('superadmin', 'admin');
			$oChampFicheValidation = ficheValidationDb::getChampFicheValidation($idValidationChamp);
			$oFiche = ficheDb::getFicheByIdFiche($oChampFicheValidation -> idFiche);
			$oChamp = champDb::getChamp($oChampFicheValidation -> idChamp);
			//$this -> checkDroitFicheChamp($oFiche, $oChamp, DROIT_VALIDATION);
			ficheValidationDb::refuseChampFiche($oChampFicheValidation);
			return array();
		}
		
	}
