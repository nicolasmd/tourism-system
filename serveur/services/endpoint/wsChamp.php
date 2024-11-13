<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/champDb.php');

	/**
	 * Classe wsChamp - endpoint du webservice Champ
	 * Gestion des champs Tourinfrance disponibles pour les droits et les interfaces d'édition
	 * Accessible aux utilisateurs root, superadmin, admin
	 */
	final class wsChamp extends wsEndpoint
	{

		/**
		 * Création d'un champ Tourinfrance
		 * Accessible aux utilisateurs root
		 * @param string $identifiant : identifiant non numérique du champ
		 * @param string $libelle : libellé du champ
		 * @param string $xpath : requête xpath de sélection dans le format TIF v3
		 * @param string $liste [optional] : liste TourinFrance à associer au champ (ex: LS_Prestations)
		 * @param array $bordereaux [optional] : tableau de bordereaux (identifiant 3 caractères)
		 * @return int idChamp [optional] : identifiant numérique du champ sitChamp.idChamp
		 */
		protected function _createChamp($identifiant, $libelle, $xpath, $liste = null, $bordereaux = null, $idChampParent = null)
		{
			$this -> restrictAccess('root');
			if (is_null($idChampParent) === false)
			{
				$oChampParent = champDb::getChamp($idChampParent);
				$this -> checkDroitChamp($oChampParent, DROIT_ADMIN);
			}
			else
			{
				$oChampParent = null;
			}
			$idChamp = champDb::createChamp($identifiant, $libelle, $xpath, $liste, $bordereaux, $oChampParent);
			return array('idChamp' => $idChamp);
		}


		/**
		 * Mise à jour d'un champ Tourinfrance
		 * Accessible aux utilisateurs root
		 * @param int $idChamp : identifiant du champ sitChamp.idChamp
		 * @param string $identifiant : nouvel identifiant non numérique du champ
		 * @param string $libelle : nouveau libellé du champ
		 * @param string $xpath : nouvelle requête xpath
		 * @param string $liste [optional] : liste TourinFrance à associer au champ (ex: LS_Prestations)
		 * @void
		 */
		protected function _updateChamp($idChamp, $identifiant, $libelle, $xpath, $liste = null, $bordereaux = null)
		{
			$this -> restrictAccess('root');
			$oChamp = champDb::getChamp($idChamp);
			$this -> checkDroitChamp($oChamp, DROIT_ADMIN);
			champDb::updateChamp($oChamp, $identifiant, $libelle, $xpath, $liste, $bordereaux);
			return array();
		}


		/**
		 * Retourne la liste des champs Tourinfrance
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @param string $bordereau [optional] : bordereau pour filtrer les champs
		 * @return champCollection champs : collection de champModele
		 */
		protected function _getChamps($bordereau = null)
		{
			$champs = champDb::getChamps($bordereau);
			return array('champs' => $champs);
		}

		/**
		 * Retourne un champ Tourinfrance
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @param int $idChamp : identifiant du champ sitChamp.idChamp
		 * @return champModele champ
		 */
		protected function _getChamp($idChamp)
		{
			$champ = champDb::getChamp($idChamp);
			return array('champ' => $champ);
		}


		/**
		 * Retourne un champ Tourinfrance
		 * Accessible aux utilisateurs root, superadmin, admin
		 * @param int $idChamp : identifiant du champ sitChamp.idChamp
		 * @return champModele champ
		 */
		protected function _getChampByIdentifiant($identifiant)
		{
			$champ = champDb::getChampByIdentifiant($identifiant);
			return array('champ' => $champ);
		}


		/**
		 * Suppression d'un champ Tourinfrance
		 * Accessible aux utilisateurs root
		 * @param int $idChamp : identifiant du champ sitChamp.idChamp
		 */
		protected function _deleteChamp($idChamp)
		{
			$this -> restrictAccess('root');
			$oChamp = champDb::getChamp($idChamp);
			$this -> checkDroitChamp($oChamp, DROIT_DELETE);
			champDb::deleteChamp($oChamp);
			return array();
		}


	}
