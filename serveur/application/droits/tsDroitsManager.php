<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsDroitsManager extends tsDroitsDefault implements tsDroitsInterface
	{
		
		const SQL_FICHES_ADMINISTRABLES = "SELECT DISTINCT(idFiche) FROM sitUtilisateurDroitFiche WHERE idUtilisateur='%d'";
		
		const SQL_DROIT_FICHE_ID = 'SELECT droit FROM sitUtilisateurDroitFiche WHERE idUtilisateur=\'%1$d\' AND idFiche=\'%2$d\'
									AND idProfil IS NULL UNION SELECT pd.droit FROM sitProfilDroit pd, sitUtilisateurDroitFiche udf
									WHERE udf.idUtilisateur=\'%1$d\' AND udf.idFiche=\'%2$d\' AND udf.idProfil=pd.idProfil';
		
		const SQL_DROIT_FICHE_ID_CHAMP = 'SELECT droit FROM sitUtilisateurDroitFicheChamp WHERE idUtilisateur=\'%1$d\' AND idFiche=\'%2$d\'
										AND idChamp=\'%3$d\' UNION SELECT pd.droit FROM sitProfilDroitChamp pd, sitUtilisateurDroitFiche udf 
										WHERE udf.idUtilisateur=\'%1$d\' AND udf.idFiche=\'%2$d\' AND udf.idProfil=pd.idProfil AND pd.idChamp=\'%3$d\'';
		
		
		
		
		/**
		 * Chargement des groupes administrables 
		 */
		protected function loadGroupesAdministrables() {}
		
		
		/**
		 * Chargement des utilisateurs administrables 
		 */
		protected function loadUtilisateursAdministrables() {}
		
		
		/**
		 * Chargement des territoires administrables 
		 */
		protected function loadTerritoiresAdministrables() {}
		
		
		
		
		public function getDroitGroupe(groupeModele $oGroupe)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		
		public function getDroitChamp(champModele $oChamp)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		
		public function getDroitFiche(ficheModele $oFiche)
		{
			return tsDatabase::getRecord(self::SQL_DROIT_FICHE_ID, array($this -> idUtilisateur, $oFiche -> idFiche));
		}
		
		
		public function getDroitUtilisateur(utilisateurModele $oUtilisateur)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		
		public function getDroitProfil(profilDroitModele $oProfil)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		
		public function getDroitTerritoire(territoireModele $oTerritoire)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		
		public function getDroitThesaurus(thesaurusModele $oThesaurus)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
		public function getDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
		}
		
	}
