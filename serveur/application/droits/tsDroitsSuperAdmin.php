<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsDroitsSuperAdmin extends tsDroitsAdmin implements tsDroitsInterface
	{
		
		const SQL_UTILISATEURS = "SELECT idUtilisateur FROM sitUtilisateur WHERE idGroupe='%d' AND idUtilisateur!='%d'";
		const SQL_TERRITOIRES = "SELECT idTerritoire FROM sitGroupeTerritoire WHERE idGroupe='%d'";
		const SQL_PROFILS_ADMINISTRABLES = "SELECT idProfil FROM sitProfilDroit WHERE idGroupe='%d'";
		
		private $profilsGroupe = null;
		
		
		
		/**
		 * Chargement des groupes administrables 
		 */
		protected function loadGroupesAdministrables()
		{
			$this -> groupesAdministrables = groupeDb::getGroupesChilds(groupeDb::getGroupe($this -> idGroupe));
			$this -> groupesAdministrables[] = $this -> idGroupe;
		}
		
		
		/**
		 * Chargement des utilisateurs administrables 
		 */
		protected function loadUtilisateursAdministrables()
		{
			foreach ($this -> groupesAdministrables as $idGroupe)
			{
				$this -> utilisateursAdministrables = array_merge(
					$this -> utilisateursAdministrables,
					tsDatabase::getRecords(self::SQL_UTILISATEURS, array($idGroupe, $this -> idUtilisateur))
				);
			}
			
			$this -> utilisateursAdministrables = array_unique($this -> utilisateursAdministrables);
		}
		
		
		/**
		 * Chargement des territoires administrables 
		 */
		protected function loadTerritoiresAdministrables()
		{
			foreach ($this -> groupesAdministrables as $idGroupe)
			{
				$this -> territoiresAdministrables = array_merge(
					$this -> territoiresAdministrables,
					tsDatabase::getRecords(self::SQL_TERRITOIRES, array($idGroupe))
				);
			}
			
			$this -> territoiresAdministrables = array_unique($this -> territoiresAdministrables);
		}
		
		
		
		
		public function getDroitGroupe(groupeModele $oGroupe)
		{
			if ($oGroupe -> idGroupe != $this -> idGroupe)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET | DROIT_ADMIN;
		}
		
		
		public function getDroitChamp(champModele $oChamp)
		{
			return DROIT_GET;
		}
		
		
		/*public function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp)
		{
			$droit = new droitChampModele();
			$droit -> setVisualisation(true);
			$droit -> setModification(true);
			$droit -> setValidation(true);
			return $droit -> getDroit();
		}*/
		
		
		/*public function getDroitFiche(ficheModele $oFiche)
		{
			if (in_array($oFiche -> idFiche, $this -> fichesAdministrables) === false)
			{
				throw new SecuriteException("Vous n'avez pas accès à cette fiche.");
			}
			$droit = new droitFicheModele();
			$droit -> setVisualisation(true);
			$droit -> setModification(true);
			$droit -> setValidation(true);
			$droit -> setSuppressionFiches(true);
			return $droit -> getDroit();
		}*/
		
		
		public function getDroitUtilisateur(utilisateurModele $oUtilisateur)
		{
			$idUtilisateur = $oUtilisateur -> getIdUtilisateur();
			if (in_array($idUtilisateur, $this -> utilisateursAdministrables) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
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
			
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		
		public function getDroitTerritoire(territoireModele $oTerritoire)
		{
			$idTerritoire = $oTerritoire -> getIdTerritoire();
			if (in_array($idTerritoire, $this -> territoiresAdministrables) === false)
			{
				throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
			}
			return DROIT_GET | DROIT_ADMIN;
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
		
	}
