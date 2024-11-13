<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/champModele.php');
	require_once('application/modele/droitModele.php');
	require_once('application/modele/droitChampModele.php');
	require_once('application/modele/groupeModele.php');
	require_once('application/modele/profilDroitModele.php');

	final class profilDroitDb
	{
	
		const SQL_PROFILS = "SELECT idProfil FROM sitProfilDroit";
		const SQL_PROFILS_GROUPE = "SELECT idProfil FROM sitProfilDroit WHERE (idGroupe='%d' OR idGroupe IS NULL)";
		const SQL_PROFIL = "SELECT idProfil, idGroupe, libelle, droit FROM sitProfilDroit WHERE idProfil='%d'";
		const SQL_DELETE_PROFIL = "DELETE FROM sitProfilDroit WHERE idProfil='%d'";
		const SQL_PROFIL_DROITS_CHAMP = "SELECT droit, idChamp FROM sitProfilDroitChamp WHERE idProfil='%d'";
		const SQL_CREATE_PROFIL = "INSERT INTO sitProfilDroit (idGroupe, libelle, droit) VALUES (NULL, '%s', 0)";
		const SQL_CREATE_PROFIL_GROUPE = "INSERT INTO sitProfilDroit (idGroupe, libelle, droit) VALUES ('%d', '%s', 0)";
		const SQL_UPDATE_PROFIL = "UPDATE sitProfilDroit SET droit='%d' WHERE idProfil='%d'";
		const SQL_UPDATE_PROFIL_CHAMP = "REPLACE INTO sitProfilDroitChamp(droit, idProfil, idChamp) VALUES ('%d', '%d', '%d')";
		const SQL_DELETE_PROFIL_CHAMP = "DELETE FROM sitProfilDroitChamp WHERE idProfil='%d' AND idChamp='%d'";

		
		public static function getProfil($idProfil)
		{
			$result = tsDatabase::getRow(self::SQL_PROFIL, array($idProfil), DB_FAIL_ON_ERROR);
			$oProfil = new profilDroitModele();
			$oProfil -> loadDroit($result['droit']);
			$oProfil -> setIdProfil($result['idProfil']);
			$oProfil -> setIdGroupe($result['idGroupe']);
			$oProfil -> setLibelle($result['libelle']);
			$oProfil -> setDroitsChamp(self::getDroitsChamp($oProfil));
			return $oProfil;
		}
		
		
		public static function getDroitsChamp(profilDroitModele $oProfil)
		{
			$idProfil = $oProfil -> idProfil;
			$oDroitChampCollection = new droitChampCollection();
			$droitsChamp = tsDatabase::getRows(self::SQL_PROFIL_DROITS_CHAMP, array($idProfil));
			foreach($droitsChamp as $droitChamp)
			{
				$oDroit = new droitChampModele();
				$oDroit -> setIdChamp($droitChamp['idChamp']);
				$oDroit -> loadDroit($droitChamp['droit']);
				$oDroitChampCollection[] = $oDroit -> getObject();
			}
			return $oDroitChampCollection -> getCollection();
		}
		
		
		public static function getProfils()
		{
			$oProfilCollection = new profilDroitCollection();
			$idProfils = tsDatabase::getRecords(self::SQL_PROFILS, array());
			foreach($idProfils as $idProfil)
			{
				$oProfilCollection[] = self::getProfil($idProfil) -> getObject();
			}
			return $oProfilCollection -> getCollection();
		}
		
		
		public static function getProfilsGroupe(groupeModele $oGroupe)
		{
			$idGroupe = $oGroupe -> idGroupe;
			$oProfilCollection = new profilDroitCollection();
			$idProfils = tsDatabase::getRecords(self::SQL_PROFILS_GROUPE, array($idGroupe));
			foreach($idProfils as $idProfil)
			{
				$oProfilCollection[] = self::getProfil($idProfil);
			}
			return $oProfilCollection -> getCollection();
		}
		
		
		public static function createProfil($libelle)
		{
			return tsDatabase::insert(self::SQL_CREATE_PROFIL, array($libelle));
		}
		
		
		public static function deleteProfil($oProfil)
		{
			return tsDatabase::query(self::SQL_DELETE_PROFIL, array($oProfil -> idProfil));
		}
		
		
		public static function createProfilGroupe($libelle, groupeModele $oGroupe)
		{
			$idGroupe = $oGroupe -> idGroupe;
			return tsDatabase::insert(self::SQL_CREATE_PROFIL_GROUPE, array($idGroupe, $libelle));
		}
		
		
		public static function updateProfil(profilDroitModele $oProfil, droitModele $oDroit)
		{
			$idProfil = $oProfil -> idProfil;
			$droit = $oDroit -> getDroit();
			return tsDatabase::query(self::SQL_UPDATE_PROFIL, array($droit, $idProfil));
		}
		
		
		public static function setProfilDroitChamp(profilDroitModele $oProfil, champModele $oChamp, droitModele $oDroit)
		{
			$idProfil = $oProfil -> idProfil;
			$idChamp = $oChamp -> idChamp;
			$droit = $oDroit -> getDroit();
			return tsDatabase::query(self::SQL_UPDATE_PROFIL_CHAMP, array($droit, $idProfil, $idChamp));
		}
		
		
		public static function deleteProfilDroitChamp(profilDroitModele $oProfil, champModele $oChamp) 
		{
			$idProfil = $oProfil -> idProfil;
			$idChamp = $oChamp -> idChamp;
			return tsDatabase::query(self::SQL_DELETE_PROFIL_CHAMP, array($idProfil, $idChamp));
		}
		
		
	}
