<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsDroitsDesk extends tsDroitsDefault implements tsDroitsInterface
	{
		
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
		
		
		public function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp)
		{
			throw new SecuriteException(sprintf('%1$s : %2$s', __CLASS__, __LINE__));
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
