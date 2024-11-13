<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	class tsDroitsAdmin extends tsDroitsDefault implements tsDroitsInterface
	{

		const SQL_UTILISATEURS = "SELECT DISTINCT su.idUtilisateur FROM sitUtilisateur su, sitUtilisateurDroitFiche sud
					WHERE su.idGroupe='%d' AND su.idUtilisateur=sud.idUtilisateur AND sud.idFiche IN('%s')";

		const SQL_TERRITOIRES = "SELECT idTerritoire FROM sitGroupeTerritoire WHERE idGroupe='%d'";

		const SQL_FICHES_UTILISATEUR = "SELECT DISTINCT(idFiche) FROM sitUtilisateurDroitFiche WHERE idUtilisateur='%d'";

		const SQL_BORDEREAU_COMMUNE_FICHE = "SELECT bordereau, codeInsee FROM sitFiche WHERE idFiche='%d'";




		/**
		 * Chargement des groupes administrables
		 */
		protected function loadGroupesAdministrables() {}


		/**
		 * Chargement des utilisateurs administrables
		 */
		protected function loadUtilisateursAdministrables()
		{
			$this -> utilisateursAdministrables = tsDatabase::getRecords(self::SQL_UTILISATEURS, array($this -> idGroupe, $this -> fichesAdministrables));
		}


		/**
		 * Chargement des territoires administrables
		 */
		protected function loadTerritoiresAdministrables()
		{
			$this -> territoiresAdministrables = tsDatabase::getRecords(self::SQL_TERRITOIRES, array($this -> idGroupe));
		}




		public function getDroitGroupe(groupeModele $oGroupe)
		{
			if ($oGroupe -> idGroupe != $this -> idGroupe)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET;
		}


		public function getDroitChamp(champModele $oChamp)
		{
			return DROIT_GET;
		}


		public function getDroitUtilisateur(utilisateurModele $oUtilisateur)
		{
			$idUtilisateur = $oUtilisateur -> getIdUtilisateur();

			if (in_array($idUtilisateur, $this -> utilisateursAdministrables) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}

			// Droit d'administration
			$oDroit = new droitModele();
			$oDroit -> setAdministration(true);
			$intDroit = $oDroit -> getDroit();

			$droit = $intDroit;
			// Récupération des fiches administrables par l'utilisateur
			$fiches = tsDatabase::getRows(self::SQL_FICHES_UTILISATEUR, array($idUtilisateur));
			foreach($fiches as $fiche)
			{
				// Commune - bordereau de chacune des fiches -> déduction des droits
				$infoFiche = tsDatabase::getRecord(self::SQL_BORDEREAU_COMMUNE_FICHE, array($idUtilisateur));
				$bordereau = $infoFiche['bordereau'];
				$codeInsee = $infoFiche['codeInsee'];
				$droit = $droit & $this -> droitsBordereauCommune[$bordereau][$codeInsee];
			}

			$oDroitAdmin = new droitModele();
			$oDroitAdmin -> loadDroit($droit);
			$droitAdmin = $oDroitAdmin -> getAdministration();
			return ($droitAdmin) ? DROIT_GET | DROIT_ADMIN | DROIT_DELETE : DROIT_GET;
		}


		public function getDroitProfil(profilDroitModele $oProfil)
		{
			if (is_null($this -> profilsGroupe))
			{
				$this -> profilsGroupe = tsDatabase::getRecords(self::SQL_PROFILS_ADMINISTRABLES, array($this -> idGroupe));
			}

			if (in_array($oProfil -> idProfil, $this -> profilsGroupe) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}

			return DROIT_GET;
		}


		public function getDroitTerritoire(territoireModele $oTerritoire)
		{
			$idTerritoire = $oTerritoire -> getIdTerritoire();

			if (in_array($idTerritoire, $this -> territoiresAdministrables) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET;
		}


		public function getDroitThesaurus(thesaurusModele $oThesaurus)
		{
			return DROIT_GET;
		}


		public function getDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire)
		{
			$idTerritoire = $oTerritoire -> getIdTerritoire();
			$bordereau = $oBordereau -> getBordereau();
			$bt = $bordereau . $idTerritoire;

			if (isset($this -> droitsBordereauTerritoire[$bt]) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return $this -> droitsBordereauTerritoire[$bt];
		}


		public function getDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune)
		{
			$bordereau = $oBordereau -> getBordereau();
			$codeInsee = $oCommune -> getCodeInsee();

			if (isset($this -> droitsBordereauCommune[$bordereau][$codeInsee]) === false)
			{
				throw new SecuriteException("Vous n'avez pas accès à ce bordereau/cette commune.",0,array($bordereau,$codeInsee));
			}
			return $this -> droitsBordereauCommune[$bordereau][$codeInsee];
		}

	}
