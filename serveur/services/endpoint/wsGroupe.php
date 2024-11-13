<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/ficheDb.php');
	require_once('application/db/groupeDb.php');
	require_once('application/db/pluginDb.php');
	require_once('application/db/territoireDb.php');
	require_once('application/db/utilisateurDb.php');

	/**
	 * Classe wsGroupe - endpoint du webservice Groupe
	 * Gestion des groupes d'utilisateurs dans Tourism System
	 * @access root superadmin admin
	 */
	final class wsGroupe extends wsEndpoint
	{
		
		/**
		 * Retourne un groupe via son identifiant
		 * @return groupeModele groupe : le groupe demandé
		 * @access root superadmin
		 */
		protected function _getGroupe($idGroupe)
		{
			$this -> restrictAccess('root', 'superadmin');
			return array('groupe' => groupeDb::getGroupe($idGroupe));
		}
		
		/**
		 * Retourne la liste des groupes d'utilisateurs
		 * @return groupeCollection groupes : collection de groupeModele
		 * @access root superadmin
		 */
		protected function _getGroupes($idGroupeParent = null)
		{
			$this -> restrictAccess('root', 'superadmin');
			return array('groupes' => groupeDb::getGroupes($idGroupeParent));
		}
	
		/**
		 * Création d'un groupe d'utilisateurs
		 * @param string $nomGroupe : nom du groupe à créer
		 * @param string $descriptionGroupe : description du groupe à créer
		 * @param int $idGroupeParent : parent du groupe à créer
		 * @return int idGroupe : identifiant du groupe sitGroupe.idGroupe 
		 * @access root
		 */
		protected function _createGroupe($nomGroupe, $descriptionGroupe, $idGroupeParent = null)
		{
			$this -> restrictAccess('root');
			$idGroupe = groupeDb::createGroupe($nomGroupe, $descriptionGroupe, $idGroupeParent);
			return array('idGroupe' => $idGroupe);
		}
		
		/**
		 * Mise à jour du nom d'un groupe
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param string $nomGroupe : nom du groupe
		 * @param string $descriptionGroupe : description du groupe
		 * @access root
		 */
		protected function _updateGroupe($idGroupe, $nomGroupe, $descriptionGroupe)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			groupeDb::updateGroupe($oGroupe, $nomGroupe, $descriptionGroupe);
			return array();
		}
		
		
		/**
		 * Suppression d'un groupe d'utilisateurs
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @access root
		 */
		protected function _deleteGroupe($idGroupe)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitGroupe($oGroupe, DROIT_DELETE);
			groupeDb::deleteGroupe($oGroupe);
			return array();
		}
		
		/**
		 * Définit l'utilisateur administrateur d'un groupe
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param int $idUtilisateur [optional] : identifiant de l'utilisateur sitUtilisateur.idUtilisateur
		 * 		si vide, on supprime le super admin du groupe
		 * @access root
		 */
		protected function _setSuperAdminGroupe($idGroupe, $idUtilisateur = null)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			if (is_null($idUtilisateur) === false)
			{
				$oUtilisateur = utilisateurDb::getUtilisateur($idUtilisateur);
				$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
				$this -> checkDroitUtilisateur($oUtilisateur, DROIT_ADMIN);
				groupeDb::setSuperAdminGroupe($oGroupe, $oUtilisateur);
			}
			else
			{
				groupeDb::unsetSuperAdminGroupe($oGroupe);
			}
			return array();
		}
		
		/**
		 * Retourne la liste des utilisateurs d'un groupe
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @return array utilisateurs : tableau de sitUtilisateur.idUtilisateur
		 * @access root
		 */
		protected function _getUtilisateursGroupe($idGroupe)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitGroupe($oGroupe, DROIT_GET);
			$utilisateurs = groupeDb::getUtilisateursGroupe($oGroupe);
			return array('utilisateurs' => $utilisateurs);
		}
		
		/**
		 * Récupération de tous les territoires d'un groupe
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @access root
		 */
		protected function _getGroupeTerritoires($idGroupe)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitGroupe($oGroupe, DROIT_GET);
			$territoires = groupeDb::getGroupeTerritoires($oGroupe);
			return array('territoires' => $territoires);
		}
		
		/**
		 * Liaison d'un groupe à un territoire
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param int $idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @access root
		 */
		protected function _addGroupeTerritoire($idGroupe, $idTerritoire)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_GET);
			groupeDb::addGroupeTerritoire($oGroupe, $oTerritoire);
			return array();
		}
		
		/**
		 * Suppression de la liaison d'un groupe à un territoire
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param int $idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @access root
		 */
		protected function _deleteGroupeTerritoire($idGroupe, $idTerritoire)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_GET);
			groupeDb::deleteGroupeTerritoire($oGroupe, $oTerritoire);
			return array();
		}
		
		/**
		 * Récupération de tous les partenaires d'un groupe
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @access root
		 */
		protected function _getGroupePartenaires($idGroupe)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$this -> checkDroitGroupe($oGroupe, DROIT_GET);
			$partenaires = groupeDb::getGroupePartenaires($oGroupe);
			return array('partenaires' => $partenaires);
		}
		
		/**
		 * Liaison d'un groupe à un partenaire
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param int $idGroupePartenaire : identifiant du partenaire sitGroupe.idGroupe
		 * @access root
		 */
		protected function _addGroupePartenaire($idGroupe, $idGroupePartenaire, $typePartenaire)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$oGroupePartenaire = groupeDb::getGroupe($idGroupePartenaire);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			$this -> checkDroitGroupe($oGroupePartenaire, DROIT_GET);
			groupeDb::addGroupePartenaire($oGroupe, $oGroupePartenaire, $typePartenaire);
			return array();
		}
		
		/**
		 * Suppression de la liaison d'un groupe à un partenaire
		 * @param int $idGroupe : identifiant du groupe sitGroupe.idGroupe
		 * @param int $idGroupePartenaire : identifiant du partenaire sitGroupe.idGroupe
		 * @access root
		 */
		protected function _deleteGroupePartenaire($idGroupe, $idGroupePartenaire)
		{
			$this -> restrictAccess('root');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$oGroupePartenaire = groupeDb::getGroupe($idGroupePartenaire);
			$this -> checkDroitGroupe($oGroupe, DROIT_ADMIN);
			$this -> checkDroitGroupe($oGroupePartenaire, DROIT_GET);
			groupeDb::deleteGroupePartenaire($oGroupe, $oGroupePartenaire);
			return array();
		}
		
		
		/**
		 * Suppression d'une fiche partenaire pour le groupe de l'utilisateur connecté
		 * Exlusion pour un partenaire Exclude / Suppresion de l'inclusion pour un partenaire include
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _deleteGroupePartenaireFiche($idFiche)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_VISUALISATION);
			$oGroupe = groupeDb::getGroupe(tsDroits::getGroupeUtilisateur());
			$oGroupePartenaire = groupeDb::getGroupe($oFiche -> idGroupe);
			$typePartenaire = groupeDb::getGroupePartenaireType($oGroupe, $oGroupePartenaire);
			switch ($typePartenaire)
			{
				case 'exclude' :
					groupeDb::addGroupePartenaireFicheExclude($oFiche);
					break;
				case 'include' :
					groupeDb::deleteGroupePartenaireFicheInclude($oFiche);
					break;
			}
			return array();
		}
		
		
		/**
		 * Récupère la liste des plugins disponibles pour un groupe
		 * @param int $idGroupe : identifiant du groupe
		 * @return pluginCollection plugins : collection de pluginModele
		 * @access root
		 */
		protected function _getGroupePlugins($idGroupe)
		{
			$this -> restrictAccess('superadmin', 'admin');
			$oGroupe = groupeDb::getGroupe($idGroupe);
			$plugins = groupeDb::getGroupePlugins($oGroupe);
			return array('plugins' => $plugins);
		}
		
		/**
		 * Active un plugin pour un groupe
		 * @param string $nomPlugin : identifiant du plugin
		 * @access root
		 */
		protected function _addGroupePlugin($idGroupe, $nomPlugin)
		{
			$this -> restrictAccess('root');
			$oPlugin = pluginDb::getPlugin($nomPlugin);
			groupeDb::addGroupePlugin($idGroupe, $oPlugin);
			return array();
		}
		
		/**
		 * Désactive un plugin pour un groupe
		 * @param string $nomPlugin : identifiant du plugin
		 * @access root
		 */
		protected function _deleteGroupePlugin($idGroupe, $nomPlugin)
		{
			$this -> restrictAccess('root');
			$oPlugin = pluginDb::getPlugin($nomPlugin);
			groupeDb::deleteGroupePlugin($idGroupe, $oPlugin);
			return array();
		}
		
	}
