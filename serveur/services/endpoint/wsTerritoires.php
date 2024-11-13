<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/communeDb.php');
	require_once('application/db/territoireDb.php');
	require_once('application/db/thesaurusDb.php');

	/**
	 * Classe wsTerritoires - endpoint du webservice Territoires
	 * Gestion des territoires
	 * @access root superadmin admin
	 */
	final class wsTerritoires extends wsEndpoint
	{
	
		/**
		 * Retourne la liste des territoires (visibles de l'utilisateur)
		 * @return territoireCollection territoires : collection de territoireModele
		 * @access root superadmin admin
		 */
		protected function _getTerritoires()
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			return array('territoires' => territoireDb::getTerritoires());
		}
		
		/**
		 * Retourne la liste des communes d'un territoire
		 * @param int $idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @return communeCollection communes : collection de communeModele
		 * @access root superadmin admin
		 */
		protected function _getCommunesByTerritoire($idTerritoire)
		{
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_GET);
			$communes = territoireDb::getCommunesByTerritoire($oTerritoire);
			return array('communes' => $communes);
		}
		
		/**
		 * Retourne la liste des thésaurii d'un territoire
		 * @param int $idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @return thesaurusCollection thesaurii : collection de thesaurusModele
		 * @access root superadmin admin
		 */
		protected function _getThesaurusByTerritoire($idTerritoire)
		{
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_GET);
			$thesaurii = territoireDb::getThesaurusByTerritoire($oTerritoire);
			return array('thesaurii' => $thesaurii);
		}
		
		/**
		 * Méthode de création d'un territoire
		 * @param string $libelle : libellé du territoire
		 * @return int idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @access root
		 */
		protected function _createTerritoire($libelle)
		{
			$this -> restrictAccess('root');
			$idTerritoire = territoireDb::createTerritoire($libelle);
			return array('idTerritoire' => $idTerritoire);
		}
		
		/**
		 * Méthode de mise à jour du nom d'un territoire
		 * @param int idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @param string $libelle : libellé du territoire
		 * @access root
		 */
		protected function _updateTerritoire($idTerritoire, $libelle)
		{
			$this -> restrictAccess('root');
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$oTerritoire -> setLibelle($libelle);
			territoireDb::updateTerritoire($oTerritoire);
			return array();
		}
		
		/**
		 * Méthode de suppression d'un territoire
		 * @param int idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @access root
		 */
		protected function _deleteTerritoire($idTerritoire)
		{
			$this -> restrictAccess('root');
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_DELETE);
			territoireDb::deleteTerritoire($oTerritoire);
			return array();
		}
		
		/**
		 * Ajout d'une commune à un territoire
		 * @param int idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @param string $codeInsee : code insee de la commune
		 * @access root
		 */
		protected function _addCommuneTerritoire($idTerritoire, $codeInsee)
		{
			$this -> restrictAccess('root');
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$oCommune = communeDb::getCommune($codeInsee);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_ADMIN);
			$this -> checkDroitCommune($oCommune, DROIT_GET);
			territoireDb::addCommuneTerritoire($oTerritoire, $oCommune);
			return array();
		}
		
		
		/**
		 * Changement de la visibilté d'une commune
		 * @param int idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @param string $codeInsee : code insee de la commune
		 * @param bool $prive : visibilité de l'ensemble des fiches (false) ou seulement les fiches de l'utilisateur (true)
		 * @access root
		 */
		protected function _setCommuneTerritoirePrive($idTerritoire, $codeInsee, $prive)
		{
			$this -> restrictAccess('root');
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$oCommune = communeDb::getCommune($codeInsee);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_ADMIN);
			$this -> checkDroitCommune($oCommune, DROIT_GET);
			territoireDb::setCommuneTerritoirePrive($oTerritoire, $oCommune, $prive);
			return array();
		}
		
		
		/**
		 * Suppression d'une commune d'un territoire
		 * @param int idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @param string $codeInsee : code insee de la commune
		 * @access root
		 */
		protected function _deleteCommuneTerritoire($idTerritoire, $codeInsee)
		{
			$this -> restrictAccess('root');
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$oCommune = communeDb::getCommune($codeInsee);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_ADMIN);
			$this -> checkDroitCommune($oCommune, DROIT_GET);
			territoireDb::deleteCommuneTerritoire($oTerritoire, $oCommune);
			return array();
		}
		
		/**
		 * Ajout d'un thésaurus à un territoire
		 * @param int idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @param string $codeThesaurus : code du thésaurus
		 * @access root
		 */
		protected function _addThesaurusTerritoire($idTerritoire, $codeThesaurus)
		{
			$this -> restrictAccess('root');
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$oThesaurus = thesaurusDb::getThesaurus($codeThesaurus);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_ADMIN);
			$this -> checkDroitThesaurus($oThesaurus, DROIT_GET);
			territoireDb::addThesaurusTerritoire($oTerritoire, $oThesaurus);
			return array();
		}
		
		/**
		 * Suppression d'un thésaurus d'un territoire
		 * @param int idTerritoire : identifiant du territoire sitTerritoire.idTerritoire
		 * @param string $codeThesaurus : code du thésaurus
		 * @access root
		 */
		protected function _deleteThesaurusTerritoire($idTerritoire, $codeThesaurus)
		{
			$this -> restrictAccess('root');
			$oTerritoire = territoireDb::getTerritoire($idTerritoire);
			$oThesaurus = thesaurusDb::getThesaurus($codeThesaurus);
			$this -> checkDroitTerritoire($oTerritoire, DROIT_ADMIN);
			$this -> checkDroitThesaurus($oThesaurus, DROIT_GET);
			territoireDb::deleteThesaurusTerritoire($oTerritoire, $oThesaurus);
			return array();
		}
		
		
	}
