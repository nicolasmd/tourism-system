<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/groupeDb.php');
	require_once('application/db/utilisateurDb.php');
	require_once('application/db/utilisateurDroitFicheDb.php');
	require_once('application/db/utilisateurDroitTerritoireDb.php');

	/**
	 * Classe wsUtilisateur - endpoint du webservice Utilisateur
	 * Gestion des utilisateurs de Tourism System
	 * @access root superadmin admin
	 */
	final class wsUtilisateur extends wsEndpoint
	{
	
		/**
		 * Création d'un utilisateur
		 * @param string $email : email de l'utilisateur à créer
		 * @param string $typeUtilisateur : niveau d'accès de l'utilisateur
		 * 				manager : propriétaire d'établissement
		 * 				desk : accueillant d'office de tourisme
		 * 				admin : administrateur de territoires
		 * @param int $idGroupe [optional] : identifiant du groupe parent de l'utilisateur
		 * @return int idUtilisateur : identifiant de l'utilisateur créé sitUtilisateur.idUtilisateur 
		 * @access root superadmin 
		 */
		protected function _createUtilisateur($email, $typeUtilisateur, $idGroupe = null)
		{
			$this -> restrictAccess('superadmin', 'root');
			$idGroupe = (is_null($idGroupe) === false ? $idGroupe : tsDroits::getGroupeUtilisateur());
			$idUtilisateur = utilisateurDb::createUtilisateur($email, $typeUtilisateur, $idGroupe);
			
			// #hook OpenId ajout utilisateur pour le plugin à coder !
			//$openidClient = new wsOpenidClient();
			//$openidClient -> addUser($email, $password);
			
			return array('idUtilisateur' => $idUtilisateur);
		}
		
		
		/**
		 * Suppression d'un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @access root superadmin
		 */
		protected function _deleteUtilisateur($idUtilisateur)
		{
			$this -> restrictAccess('superadmin', 'root');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_DELETE);
			utilisateurDb::deleteUtilisateur($oUtilisateur);
			return array();
		}
		
		
		/**
		 * Méthode de chagement de mot de passe d'un utilisateur
		 * @param string $oldPassword : ancien mot de passe
		 * @param object $newPassword : nouveau mot de passe
		 * 		le mot de passe doit être composé de 4 à 64 caractères alphanumériques
		 * @param int $idUtilisateur [optional] : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * 				Utilisateur courant par défaut 
		 * @access root superadmin
		 */
		protected function _updateUtilisateurPassword($oldPassword, $newPassword, $idUtilisateur = null)
		{
			$this -> restrictAccess('superadmin', 'root');
			if (is_null($idUtilisateur))
			{
				$idUtilisateur = tsDroits::getIdUtilisateur();
			}
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			utilisateurDb::updateUtilisateurPassword($oldPassword, $newPassword, $oUtilisateur);
			return array();
		}
		
		
		/**
		 * Méthode de chagement de groupe d'un utilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @access root superadmin
		 */
		protected function _updateUtilisateurGroupe($idUtilisateur, $idGroupe)
		{
			$this -> restrictAccess('superadmin', 'root');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			utilisateurDb::updateUtilisateurGroupe($oUtilisateur, $oGroupe);
			return array();
		}
		
		
		/**
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @return utilisateur utilisateurModele
		 * @access root superadmin admin
		 */
		protected function _getUtilisateur($idUtilisateur = null)
		{
			if (is_null($idUtilisateur) === false)
			{
				$this -> restrictAccess('admin', 'superadmin', 'root');
			}
			else
			{
				$idUtilisateur = tsDroits::getIdUtilisateur();
			}
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_GET);
			return array('utilisateur' => $oUtilisateur);
		}
		
		
		/**
		 * @return utilisateur utilisateurModele
		 * @return utilisateurCollection utilisateurs : collection de utilisateurModele
		 * @access root superadmin admin
		 */
		protected function _getUtilisateurs()
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			return array('utilisateurs' => utilisateurDb::getUtilisateurs());
		}
		
		
		/**
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @return sessionCollection sessions : collection de sessionModele
		 * @access root superadmin
		 */
		protected function _getSessionsUtilisateur($idUtilisateur)
		{
			$this -> restrictAccess('superadmin', 'root');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$sessions = utilisateurDb::getSessionsUtilisateur($oUtilisateur);
			return array('sessions' => $sessions);
		}
		
		
		/**
		 * @param int $idUtilisateur : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * @return sessionCollection sessions : collection de sessionModele
		 * @access root superadmin
		 */
		protected function _getDroitsUtilisateur($idUtilisateur)
		{
			$this -> restrictAccess('superadmin', 'root', 'admin');
			$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
			$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
			$droitsFiche = utilisateurDroitFicheDb::getDroitsFiche($oUtilisateur);
			$droitsTerritoire = utilisateurDroitTerritoireDb::getDroitsTerritoire($oUtilisateur);
			$droits = array('droitsFiche' => $droitsFiche, 'droitsTerritoire' => $droitsTerritoire);
			return array('droits' => $droits);
		}
		
	}
