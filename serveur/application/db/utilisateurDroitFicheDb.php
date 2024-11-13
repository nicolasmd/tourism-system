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
	require_once('application/modele/droitFicheModele.php');
	require_once('application/modele/ficheModele.php');
	require_once('application/modele/profilDroitModele.php');
	require_once('application/modele/utilisateurModele.php');
	
	final class utilisateurDroitFicheDb
	{
		const SQL_DROIT = "SELECT idProfil, droit FROM sitUtilisateurDroitFiche WHERE idUtilisateur='%d' AND idFiche='%d'";
		const SQL_DROITS_UTILISATEUR = "SELECT idFiche FROM sitUtilisateurDroitFiche WHERE idUtilisateur='%d'";
		const SQL_DROIT_CHAMP = "SELECT droit, idChamp FROM sitUtilisateurDroitFicheChamp WHERE idUtilisateur='%d' AND idFiche='%d'";
		const SQL_SET_DROIT = "REPLACE INTO sitUtilisateurDroitFiche (idUtilisateur, idFiche, idProfil, droit) VALUES ('%d', '%d', NULL, '%d')";
		const SQL_SET_DROIT_CHAMP = "REPLACE INTO sitUtilisateurDroitFicheChamp (idUtilisateur, idFiche, idChamp, droit) VALUES ('%d', '%d', '%d', '%d')";
		const SQL_DELETE_DROIT = "DELETE FROM sitUtilisateurDroitFiche WHERE idUtilisateur='%d' AND idFiche='%d'";
		const SQL_DELETE_DROIT_CHAMP = "DELETE FROM sitUtilisateurDroitFicheChamp WHERE idUtilisateur='%d' AND idFiche='%d' AND idChamp='%d'";
		const SQL_DELETE_DROITS_CHAMP = "DELETE FROM sitUtilisateurDroitFicheChamp WHERE idUtilisateur='%d' AND idFiche='%d'";
		const SQL_SET_DROIT_PROFIL = "REPLACE INTO sitUtilisateurDroitFiche (idUtilisateur, idFiche, idProfil) VALUES ('%d', '%d', '%d')";
		const SQL_UNSET_DROIT_PROFIL = "UPDATE sitUtilisateurDroitFiche SET idProfil = NULL WHERE idUtilisateur = '%d' AND idFiche = '%d'";
		const SQL_DROIT_FICHE_UTILISATEURS = "SELECT idUtilisateur FROM sitUtilisateurDroitFiche WHERE idFiche = '%d'";
		
		
		public static function getDroitFiche(utilisateurModele $oUtilisateur, ficheModele $oFiche)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$idFiche = $oFiche -> idFiche;
			$result = tsDatabase::getRow(self::SQL_DROIT, array($idUtilisateur, $idFiche), DB_FAIL_ON_ERROR);
			
			/*$oDroit = new droitFicheModele();
			$oDroit -> setIdFiche($idFiche);
			$oDroit -> setIdUtilisateur($idUtilisateur);
			$oDroit -> loadDroit($result['droit']);
			$oDroit -> setRaisonSociale($oFiche -> raisonSociale);
			$oDroit -> setBordereau($oFiche -> bordereau);
			$oDroit -> setDroitsChamp(self::getDroitsFicheChamp($oUtilisateur, $oFiche));*/
			
			if (is_null($result['idProfil']))
			{
				$oDroit = new droitFicheModele();
				$oDroit -> loadDroit($result['droit']);
				$oDroit -> setDroitsChamp(self::getDroitsFicheChamp($oUtilisateur, $oFiche));
			}
			else
			{
				$oDroit = profilDroitDb::getProfil($result['idProfil']);
			}
			
			$oDroit -> setIdFiche($idFiche);
			$oDroit -> setIdUtilisateur($idUtilisateur);
			$oDroit -> setRaisonSociale($oFiche -> raisonSociale);
			$oDroit -> setBordereau($oFiche -> bordereau);
			
			return $oDroit;
		}
		
		
		private static function getDroitsFicheChamp(utilisateurModele $oUtilisateur, ficheModele $oFiche)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$idFiche = $oFiche -> idFiche;
			
			$oDroitChampCollection = new droitChampCollection();
			$droitsChamp = tsDatabase::getRows(self::SQL_DROIT_CHAMP, array($idUtilisateur, $idFiche));
			foreach($droitsChamp as $droitChamp)
			{
				$oDroit = new droitChampModele();
				$oDroit -> setIdChamp($droitChamp['idChamp']);
				$oDroit -> loadDroit($droitChamp['droit']);
				$oDroitChampCollection[] = $oDroit -> getObject();
			}
			return $oDroitChampCollection -> getCollection();
		}
		
		public static function getDroitsFiche(utilisateurModele $oUtilisateur)
		{
			$oDroitFicheCollection = new droitFicheCollection();
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$droitsFiche = tsDatabase::getRows(self::SQL_DROITS_UTILISATEUR, array($idUtilisateur));
			foreach($droitsFiche as $droitFiche)
			{
				$oFiche = ficheDb::getFicheByIdFiche($droitFiche['idFiche']);
				$oDroit = self::getDroitFiche($oUtilisateur, $oFiche);
				$oDroitFicheCollection[] = $oDroit -> getObject();
			}
			return $oDroitFicheCollection -> getCollection();
		}
		
		
		/*public static function getDroitFicheChamp(utilisateurModele $oUtilisateur, ficheModele $oFiche, champModele $oChamp)
		{
			$idUtilisateur = $oUtilisateur -> getIdUtilisateur();
			$idFiche = $oFiche -> getIdFiche();
			$idChamp = $oChamp -> getIdChamp();
			
			$result = tsDatabase::getRow(self::SQL_DROIT_CHAMP, array($idUtilisateur, $idFiche, $idChamp), DB_FAIL_ON_ERROR);
			$oDroit = new droitChampModele();
			$oDroit -> loadDroit($result['droit']);
			return $oDroit;
		}*/
		
		
		public static function setDroitFiche(utilisateurModele $oUtilisateur, ficheModele $oFiche, droitFicheModele $oDroit)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$idFiche = $oFiche -> idFiche;
			$droit = $oDroit -> getDroit();
			return tsDatabase::query(self::SQL_SET_DROIT, array($idUtilisateur, $idFiche, $droit), DB_FAIL_ON_ERROR);
		}
		
		
		public static function setDroitFicheChamp(utilisateurModele $oUtilisateur, ficheModele $oFiche, champModele $oChamp, droitChampModele $oDroit)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$idFiche = $oFiche -> idFiche;
			$idChamp = $oChamp -> idChamp;
			$droit = $oDroit -> getDroit();
			
			return tsDatabase::query(self::SQL_SET_DROIT_CHAMP, array($idUtilisateur, $idFiche, $idChamp, $droit), DB_FAIL_ON_ERROR);
		}
		
		
		
		public static function deleteDroitFiche(utilisateurModele $oUtilisateur, ficheModele $oFiche)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$idFiche = $oFiche -> idFiche;
			$deleteChamps = tsDatabase::query(self::SQL_DELETE_DROITS_CHAMP, array($idUtilisateur, $idFiche));
			$deleteDroit = tsDatabase::query(self::SQL_DELETE_DROIT, array($idUtilisateur, $idFiche));
			return $deleteChamps && $deleteDroit;
		}
		
		
		public static function deleteDroitFicheChamp(utilisateurModele $oUtilisateur, ficheModele $oFiche, champModele $oChamp)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$idFiche = $oFiche -> idFiche;
			$idChamp = $oChamp -> idChamp;
			
			return tsDatabase::query(self::SQL_DELETE_DROIT_CHAMP, array($idUtilisateur, $idFiche, $idChamp));
		}
		
		
		public static function setDroitFicheProfil(utilisateurModele $oUtilisateur, ficheModele $oFiche, profilDroitModele $oProfil)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$idFiche = $oFiche -> idFiche;
			$idProfil = $oProfil -> idProfil;
			
			return tsDatabase::query(self::SQL_SET_DROIT_PROFIL, array($idUtilisateur, $idFiche, $idProfil));
		}
		
		
		public static function unsetDroitFicheProfil(utilisateurModele $oUtilisateur, ficheModele $oFiche)
		{
			$idUtilisateur = $oUtilisateur -> idUtilisateur;
			$idFiche = $oFiche -> idFiche;
			
			return tsDatabase::query(self::SQL_UNSET_DROIT_PROFIL, array($idUtilisateur, $idFiche));
		}
		
		
		public function getDroitFicheUtilisateurs(ficheModele $oFiche)
		{
			$oUtilisateurCollection = new UtilisateurCollection();
			$idUtilisateurs = tsDatabase::getRecords(self::SQL_DROIT_FICHE_UTILISATEURS, array($oFiche -> idFiche));
			foreach ($idUtilisateurs as $idUtilisateur)
			{
				$oUtilisateurCollection[] = utilisateurDb::getUtilisateur($idUtilisateur);
			}
			return $oUtilisateurCollection -> getCollection();
		}
		
		
		
	}
