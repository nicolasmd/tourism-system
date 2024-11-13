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
	require_once('application/modele/champModele.php');
	require_once('application/modele/champFicheValidationModele.php');
	require_once('application/modele/ficheModele.php');
	
	final class ficheValidationDb
	{
	
		const SQL_FICHES_A_VALIDER = "SELECT DISTINCT idFiche FROM sitFicheValidationChamp WHERE etat='a_valider' AND idFiche IN ('%s')";
		
		const SQL_CHAMP_FICHE_VALIDATION = "SELECT * FROM sitFicheValidationChamp WHERE idValidationChamp='%d'";
		const SQL_CHAMPS_FICHE_A_VALIDER = "SELECT idValidationChamp FROM sitFicheValidationChamp WHERE idFiche='%d' AND etat='a_valider'";
		const SQL_CHAMP_FICHE_A_VALIDER = "SELECT idValidationChamp FROM sitFicheValidationChamp WHERE idFiche='%d' AND idChamp='%d' AND etat='a_valider'";
		
		const SQL_SET_CHAMP_A_VALIDER = "INSERT INTO sitFicheValidationChamp (idFiche, idChamp, valeur, etat, idUtilisateur, dateModification) VALUES ('%d', '%d', '%s', 'a_valider', '%d', NOW())";
		const SQL_UPDATE_CHAMP_A_VALIDER = "UPDATE sitFicheValidationChamp SET valeur='%s', idUtilisateur='%d', dateModification=NOW() WHERE idValidationChamp='%d'";
		
		const SQL_ACCEPTE_CHAMP = "UPDATE sitFicheValidationChamp SET idValidateur='%s', etat='accepte', dateValidation=NOW() WHERE idValidationChamp='%d'";
		const SQL_REFUSE_CHAMP = "UPDATE sitFicheValidationChamp SET idValidateur='%s', etat='refuse', dateValidation=NOW() WHERE idValidationChamp='%d'";
		

		public static function getFichesAValider()
		{
			$oFicheSimpleCollection = new ficheSimpleCollection();
			$idFiches = tsDatabase::getRecords(self::SQL_FICHES_A_VALIDER, array(tsDroits::getFichesAdministrables()));
			foreach($idFiches as $idFiche)
			{
				$oFicheSimpleCollection[] = ficheDb::getFicheSimpleByIdFiche($idFiche);
			}
			return $oFicheSimpleCollection -> getCollection();
		}
		
		
		public static function getChampFicheValidation($idValidationChamp)
		{
			$champFicheValidation = tsDatabase::getObject(self::SQL_CHAMP_FICHE_VALIDATION, array($idValidationChamp));
			$oChampFicheValidation = champFicheValidationModele::getInstance($champFicheValidation, 'champFicheValidationModele');
			
			$oChamp = champDb::getChamp($oChampFicheValidation -> idChamp);
			if (is_array($oChamp -> champs) && count($oChamp -> champs) > 0)
			{
				$oChampFicheValidation -> setValeur(json_decode($champFicheValidation -> valeur, true));
			}
			$oChampFicheValidation -> setIdentifiant($oChamp -> identifiant);
			$oChampFicheValidation -> setLibelle($oChamp -> libelle);
			
			return $oChampFicheValidation -> getObject();
		}
		
		
		public static function getChampsFicheAValider(ficheModele $oFiche)
		{
			$oChampFicheValidationCollection = new champFicheValidationCollection();
			$idValidationChamps = tsDatabase::getRecords(self::SQL_CHAMPS_FICHE_A_VALIDER, array($oFiche -> idFiche));
			
			foreach($idValidationChamps as $idValidationChamp)
			{
				$oChampFicheValidationCollection[] = self::getChampFicheValidation($idValidationChamp);
			}
			
			return $oChampFicheValidationCollection;
		}
		
		
		public static function getChampFicheAValider(ficheModele $oFiche, champModele $oChamp)
		{
			$idValidationChamp = tsDatabase::getRecord(self::SQL_CHAMP_FICHE_A_VALIDER, array($oFiche -> idFiche, $oChamp -> idChamp));
			
			return ($idValidationChamp !== false) ? self::getChampFicheValidation($idValidationChamp) : false;
			
		}
		
		
		public static function setChampFicheAValider(ficheModele $oFiche, champModele $oChamp, $valeur)
		{
			if (!is_string($valeur))
			{
				$valeur = json_encode($valeur);
			}
			
			$oChampFicheValidation = self::getChampFicheAValider($oFiche, $oChamp);
			
			return $oChampFicheValidation === false
				? tsDatabase::query(self::SQL_SET_CHAMP_A_VALIDER, array($oFiche -> idFiche, $oChamp -> idChamp, $valeur, tsDroits::getIdUtilisateur()))
				: tsDatabase::query(self::SQL_UPDATE_CHAMP_A_VALIDER, array($valeur, tsDroits::getIdUtilisateur(), $oChampFicheValidation -> idValidationChamp));
		}
		
		
		public static function accepteChampFiche(champFicheValidationModele $oChampFicheValidation)
		{
			return tsDatabase::query(self::SQL_ACCEPTE_CHAMP, array(tsDroits::getIdUtilisateur(), $oChampFicheValidation -> idValidationChamp));
		}
		
		
		public static function refuseChampFiche(champFicheValidationModele $oChampFicheValidation)
		{
			return tsDatabase::query(self::SQL_REFUSE_CHAMP, array(tsDroits::getIdUtilisateur(), $oChampFicheValidation -> idValidationChamp));			
		}
		
		
	}
