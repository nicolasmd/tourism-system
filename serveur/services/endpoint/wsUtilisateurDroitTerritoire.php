<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/champDb.php');
	require_once('application/db/profilDroitDb.php');
	require_once('application/db/territoireDb.php');
	require_once('application/db/utilisateurDb.php');
	require_once('application/db/utilisateurDroitTerritoireDb.php');
	require_once('application/modele/bordereauModele.php');
	require_once('application/modele/droitChampModele.php');
	require_once('application/modele/droitTerritoireModele.php');

	/**
	 * Classe wsUtilisateurDroitTerritoire - endpoint du webservice UtilisateurDroitTerritoire
	 * Gestion des droits sur bordereau - territoire
	 * @access root superadmin
	 */
	final class wsUtilisateurDroitTerritoire extends wsEndpoint
	{
		
		/**
		 * Récupère les droits sur bordereau - territoire pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @return droitTerritoireCollection : collection de droitTerritoireModele
		 * @access root superadmin
		 */
		protected function _getDroitsTerritoire($idUtilisateur)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$droitsTerritoire = utilisateurDroitTerritoireDb::getDroitsTerritoire($oUtilisateur);
			return array('droitsTerritoire' => $droitsTerritoire);
		}
		
		/**
		 * Retourne le droit sur bordereau - territoire d'un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param string $bordereau : code bordereau (bordereauModele)
		 * @param int $idTerritoire : identifiant de territoire sitTerritoire.idTerritoire
		 * @return droitTerritoireModele : droit sur bordereau - territoire
		 * @access root superadmin admin
		 */
		protected function _getDroitTerritoire($idUtilisateur, $bordereau, $idTerritoire)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oBordereau = baseModele::getInstance($bordereau, 'bordereauModele');
			$oBordereau -> setBordereau($bordereau);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitBordereauTerritoire($oBordereau, $oTerritoire, DROIT_ADMIN);
			$droitTerritoire = utilisateurDroitTerritoireDb::getDroitTerritoire($oUtilisateur, $oBordereau, $oTerritoire);
			return array('droitTerritoire' => $droitTerritoire);
		}
		
		/**
		 * Crée ou met à jour un droit sur bordereau - territoire pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param string $bordereau : code bordereau (bordereauModele)
		 * @param int $idTerritoire : identifiant de territoire sitTerritoire.idTerritoire
		 * @param droitTerritoireModele $droit : droit sur le bordereau - territoire
		 * @access root superadmin
		 */
		protected function _setDroitTerritoire($idUtilisateur, $bordereau, $idTerritoire, $droit)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oBordereau = baseModele::getInstance($bordereau, 'bordereauModele');
			$oBordereau -> setBordereau($bordereau);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$oDroit = baseModele::getInstance($droit, 'droitTerritoireModele');
			
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitBordereauTerritoire($oBordereau, $oTerritoire, DROIT_ADMIN);
			
			utilisateurDroitTerritoireDb::setDroitTerritoire($oUtilisateur, $oBordereau, $oTerritoire, $oDroit);
			return array();
		}
		
		/**
		 * Crée ou met à jour un droit sur bordereau - territoire - champ pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param string $bordereau : code bordereau (bordereauModele)
		 * @param int $idTerritoire : identifiant de territoire sitTerritoire.idTerritoire
		 * @param int $idChamp : identifiant de champ sitChamp.idChamp
		 * @param droitTerritoireModele $droit : droit sur le bordereau - territoire
		 * @access root superadmin
		 */
		protected function _setDroitTerritoireChamp($idUtilisateur, $bordereau, $idTerritoire, $idChamp, $droit)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oBordereau = baseModele::getInstance($bordereau, 'bordereauModele');
			$oBordereau -> setBordereau($bordereau);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$oChamp = champDb::getChamp($idChamp);
			$oDroit = baseModele::getInstance($droit, 'droitChampModele');
			
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitBordereauTerritoire($oBordereau, $oTerritoire, DROIT_ADMIN);
			$this -> checkDroitChamp($oChamp, DROIT_GET);
			
			utilisateurDroitTerritoireDb::setDroitTerritoireChamp($oUtilisateur, $oBordereau, $oTerritoire, $oChamp, $oDroit);
			return array();
		}
		
		/**
		 * Récupère un droit sur bordereau - territoire - champ pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param string $bordereau : code bordereau (bordereauModele)
		 * @param int $idTerritoire : identifiant de territoire sitTerritoire.idTerritoire
		 * @param int $idChamp : identifiant de champ sitChamp.idChamp
		 * @return droitTerritoireModele droitTerritoire : droit sur le bordereau - territoire
		 * @access root superadmin
		 */
		/*protected function _getDroitTerritoireChamp($idUtilisateur, $bordereau, $idTerritoire, $idChamp)
		{
			$oWsUtilisateurDroitTerritoire = new wsUtilisateurDroitTerritoireImplementation();
			$droitTerritoireChamp = $oWsUtilisateurDroitTerritoire -> getDroitTerritoireChamp($idUtilisateur, $bordereau, $idTerritoire, $idChamp);
			return array('droitsTerritoireChamp' => $droitTerritoireChamp);
		}*/
		
		/**
		 * Supprime un droit sur bordereau - territoire pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param string $bordereau : code bordereau (bordereauModele)
		 * @param int $idTerritoire : identifiant de territoire sitTerritoire.idTerritoire
		 * @access root superadmin
		 */
		protected function _deleteDroitTerritoire($idUtilisateur, $bordereau, $idTerritoire)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oBordereau = baseModele::getInstance($bordereau, 'bordereauModele');
			$oBordereau -> setBordereau($bordereau);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
						
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitBordereauTerritoire($oBordereau, $oTerritoire, DROIT_ADMIN);
			
			utilisateurDroitTerritoireDb::deleteDroitTerritoire($oUtilisateur, $oBordereau, $oTerritoire);
			return array();
		}
		
		/**
		 * Supprime un droit sur bordereau - territoire - champ pour un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param string $bordereau : code bordereau (bordereauModele)
		 * @param int $idTerritoire : identifiant de territoire sitTerritoire.idTerritoire
		 * @param int $idChamp : identifiant de champ sitChamp.idChamp
		 * @access root superadmin
		 */
		protected function _deleteDroitTerritoireChamp($idUtilisateur, $bordereau, $idTerritoire, $idChamp)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oBordereau = baseModele::getInstance($bordereau, 'bordereauModele');
			$oBordereau -> setBordereau($bordereau);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$oChamp = champDb::getChamp($idChamp);
						
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitBordereauTerritoire($oBordereau, $oTerritoire, DROIT_ADMIN);
			$this -> checkDroitChamp($oChamp, DROIT_GET);
			
			utilisateurDroitTerritoireDb::deleteDroitTerritoireChamp($oUtilisateur, $oBordereau, $oTerritoire, $oChamp);
			return array();
		}
		
		/**
		 * Associe un droit sur bordereau - territoire d'un utilisateur à un profil de droits
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param string $bordereau : code bordereau (bordereauModele)
		 * @param int $idTerritoire : identifiant de territoire sitTerritoire.idTerritoire
		 * @param int $idProfil : identifiant de profil sitProfil.idProfil
		 * @access root superadmin
		 */
		protected function _setDroitTerritoireProfil($idUtilisateur, $bordereau, $idTerritoire, $idProfil)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oBordereau = baseModele::getInstance($bordereau, 'bordereauModele');
			$oBordereau -> setBordereau($bordereau);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitBordereauTerritoire($oBordereau, $oTerritoire, DROIT_ADMIN);
			
			$oProfil = profilDroitDb::getProfil($idProfil);
			//$this -> checkDroitProfil($oProfil);
			
			utilisateurDroitTerritoireDb::setDroitTerritoireProfil($oUtilisateur, $oBordereau, $oTerritoire, $oProfil);
			return array();
		}
		
		/**
		 * Désassocie un droit sur bordereau - territoire d'un utilisateur d'un profil de droits
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param string $bordereau : code bordereau (bordereauModele)
		 * @param int $idTerritoire : identifiant de territoire sitTerritoire.idTerritoire
		 * @access root superadmin
		 */
		protected function _unsetDroitTerritoireProfil($idUtilisateur, $bordereau, $idTerritoire)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oBordereau = baseModele::getInstance($bordereau, 'bordereauModele');
			$oBordereau -> setBordereau($bordereau);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitBordereauTerritoire($oBordereau, $oTerritoire, DROIT_ADMIN);
			
			utilisateurDroitTerritoireDb::unsetDroitTerritoireProfil($oUtilisateur, $oBordereau, $oTerritoire);
			return array();
		}
		
	}
