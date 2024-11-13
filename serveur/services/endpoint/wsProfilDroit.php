<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/champDb.php');
	require_once('application/db/groupeDb.php');
	require_once('application/db/profilDroitDb.php');
	require_once('application/modele/droitModele.php');
	require_once('application/modele/droitChampModele.php');

	/**
	 * Classe wsProfilDroit - endpoint du webservice ProfilDroit
	 * Gestion des profils de droits
	 * @access root superadmin admin
	 */
	final class wsProfilDroit extends wsEndpoint
	{
		
		/**
		 * Création d'un profil de droits
		 * @param string $libelle : libellé du profil
		 * @param string $idGroupe [optional] : identifiant du groupe sitGroupe.idGroupe
		 * 			paramètre seulement pris en compte pour l'utilisateur "root"
		 * 			sinon setté par défaut avec l'idGroupe de l'utilisateur  
		 * @return int idProfil : identifiant du profil sitProfil.idProfil
		 * @access root superadmin
		 */
		protected function _createProfil($libelle, $idGroupe = null)
		{
			$this -> restrictAccess('root', 'superadmin');
			if (tsDroits::isRoot() === false)
			{
				$idGroupe = tsDroits::getGroupeUtilisateur();
			}
			
			if (is_null($idGroupe))
			{
				$idProfil = profilDroitDb::createProfil($libelle);
			}
			else
			{
				$oGroupe = groupeDb::getGroupe($idGroupe);
				$idProfil = profilDroitDb::createProfilGroupe($libelle, $oGroupe);
			}
			return array('idProfil' => $idProfil);
		}
		
		
		/**
		 * Mise à jour d'un profil de droits
		 * @param int idProfil : identifiant du profil sitProfil.idProfil
		 * @param profilDroitModele $droit : droits généraux du profil (hors champs) 
		 * @access root superadmin
		 */
		protected function _updateProfil($idProfil, $droit)
		{
			$this -> restrictAccess('root', 'superadmin');
			$oProfil = profilDroitDb::getProfil($idProfil);
			$this -> checkDroitProfil($oProfil, DROIT_ADMIN);
			$oDroit = baseModele::getInstance($droit, 'droitModele');
			profilDroitDb::updateProfil($oProfil, $oDroit);
			return array();
		}
		
		
		/**
		 * Suppression d'un profil de droits
		 * @param int idProfil : identifiant du profil sitProfil.idProfil
		 * @access root
		 */
		protected function _deleteProfil($idProfil)
		{
			$this -> restrictAccess('root', 'superadmin');
			$oProfil = profilDroitDb::getProfil($idProfil);
			$this -> checkDroitProfil($oProfil, DROIT_ADMIN);
			profilDroitDb::deleteProfil($oProfil);
			return array();
		}
		
		
		/**
		 * Définit les droits sur un champ pour un profil
		 * @param int idProfil : identifiant du profil sitProfil.idProfil
		 * @param int $idChamp : identifiant du champ sitChamp.idChamp
		 * @param droitChampModele $droit : droits sur le champ
		 * @access root superadmin
		 */
		protected function _setProfilDroitChamp($idProfil, $idChamp, $droit)
		{
			$this -> restrictAccess('root', 'superadmin');
			$oProfil = profilDroitDb::getProfil($idProfil);
			$oChamp = champDb::getChamp($idChamp);
			$this -> checkDroitProfil($oProfil, DROIT_ADMIN);
			$this -> checkDroitChamp($oChamp, DROIT_GET);
			$oDroit = baseModele::getInstance($droit, 'droitChampModele');
			profilDroitDb::setProfilDroitChamp($oProfil, $oChamp, $oDroit);
			return array();
		}
		
		
		/**
		 * Supprime les droits sur un champ d'un profil
		 * @param int idProfil : identifiant du profil sitProfil.idProfil
		 * @param int $idChamp : identifiant du champ sitChamp.idChamp
		 * @param droitChampModele $droit : droits sur le champ
		 * @access root superadmin
		 */
		protected function _deleteProfilDroitChamp($idProfil, $idChamp)
		{
			$this -> restrictAccess('root', 'superadmin');
			$oProfil = profilDroitDb::getProfil($idProfil);
			$oChamp = champDb::getChamp($idChamp);
			$this -> checkDroitProfil($oProfil, DROIT_ADMIN);
			$this -> checkDroitChamp($oChamp, DROIT_GET);
			profilDroitDb::deleteProfilDroitChamp($oProfil, $oChamp);
			return array();
		}
		
		
		/**
		 * Retourne la liste des profils
		 * Pour les utilisateurs hors "root", retourne tous les profils non liés
		 * à un groupe, et tous les profils liés au groupe de l'utilisateur
		 * @return ProfilCollection profils : collection de profilDroitModele 
		 * @access root superadmin admin
		 */
		protected function _getProfils()
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			if (tsDroits::isRoot())
			{
				$profils = profilDroitDb::getProfils();
			}
			else
			{
				$idGroupe = tsDroits::getGroupeUtilisateur();
				$oGroupe = groupeDb::getGroupe($idGroupe);
				$profils = profilDroitDb::getProfilsGroupe($oGroupe);
			}
			return array('profils' => $profils);
		}
		
		
		/**
		 * Retourne les droits d'un profil
		 * @return profilDroits : collection de profilDroitModele 
		 * @access root superadmin admin
		 */
		protected function _getProfilDroits($idProfil)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oProfil = profilDroitDb::getProfil($idProfil);
			$this -> checkDroitProfil($oProfil, DROIT_GET);
			$profilDroits = $oProfil -> getObject();
			return array('profilDroits' => $profilDroits);
		}
		
		
		/**
		 * Retourne les droits sur un champ d'un profil
		 * @param int idProfil : identifiant du profil sitProfil.idProfil
		 * @param int $idChamp : identifiant du champ sitChamp.idChamp
		 * @return droitChampModele droitChamp : droits sur le champ
		 * @access root superadmin admin
		 */
		protected function _getProfilDroitChamp($idProfil, $idChamp)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oProfil = profilDb::getProfil($idProfil);
			$oChamp = champDb::getChamp($idChamp);
			
			$this -> checkDroitProfil($oProfil, DROIT_GET);
			$this -> checkDroitChamp($oChamp, DROIT_GET);
			
			$droitChamp = profilDroitDb::getProfilDroitChamp($oProfil, $oChamp);
			return array('droitChamp' => $droitChamp);
		}
		
	}
