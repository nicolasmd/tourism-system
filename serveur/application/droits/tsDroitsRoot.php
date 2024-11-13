<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsDroitsRoot extends tsDroitsDefault implements tsDroitsInterface
	{
		
		const SQL_GROUPES = "SELECT idGroupe FROM sitGroupe";
		const SQL_UTILISATEURS = "SELECT idUtilisateur FROM sitUtilisateur";
		const SQL_TERRITOIRES = "SELECT idTerritoire FROM sitTerritoire";
		const SQL_DROIT_FICHE = "SELECT idFiche FROM sitFiche";
		
		
		
		
		/**
		 * Chargement du groupe de l'utilisateur courant 
		 */
		protected function loadGroupeUtilisateur() {}
		
		
		/**
		 * Chargement des groupes administrables 
		 */
		protected function loadGroupesAdministrables()
		{
			$this -> groupesAdministrables = tsDatabase::getRecords(self::SQL_GROUPES, array());
		}
		
		
		/**
		 * Chargement des droits sur territoires de l'utilisateur courant
		 */
		protected function loadDroitsTerritoire() {}
		
		
		/**
		 * Chargement des droits sur fiches de l'utilisateur courant
		 */
		protected function loadDroitsFiche() {}
		
		
		/**
		 * Chargement des utilisateurs administrables 
		 */
		protected function loadUtilisateursAdministrables()
		{
			$this -> utilisateursAdministrables = tsDatabase::getRecords(self::SQL_UTILISATEURS, array());
		}
		
		
		/**
		 * Chargement des territoires administrables
		 */
		protected function loadTerritoiresAdministrables()
		{
			$this -> territoiresAdministrables = tsDatabase::getRecords(self::SQL_TERRITOIRES, array());
		}
		
		
		
		
		public function isFicheAdministrable($oFiche)
		{
			return true;
		}
		
		
		public function getDroitGroupe(groupeModele $oGroupe)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		
		public function getDroitChamp(champModele $oChamp)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		
		public function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp)
		{
			$droit = new droitChampModele();
			$droit -> setVisualisation(true);
			$droit -> setModification(true);
			$droit -> setValidation(true);
			return $droit -> getDroit();
		}
		
		
		public function getDroitFiche(ficheModele $oFiche)
		{
			$droit = new droitFicheModele();
			$droit -> setVisualisation(true);
			$droit -> setModification(true);
			$droit -> setValidation(true);
			$droit -> setSuppressionFiches(true);
			return $droit -> getDroit();
		}
		
		
		public function getDroitUtilisateur(utilisateurModele $oUtilisateur)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		
		public function getDroitProfil(profilDroitModele $oProfil)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		
		public function getDroitTerritoire(territoireModele $oTerritoire)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		
		public function getDroitThesaurus(thesaurusModele $oThesaurus)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
		
		public function getDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire)
		{
			$droit = new droitTerritoireModele();
			$droit -> setVisualisation(true);
			$droit -> setModification(true);
			$droit -> setValidation(true);
			$droit -> setSuppressionFiches(true);
			$droit -> setCreationFiches(true);
			$droit -> setAdministration(true);
			return $droit -> getDroit();
			
		}
		
		
		public function getDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune)
		{
			return DROIT_GET | DROIT_ADMIN | DROIT_DELETE;
		}
		
	}
