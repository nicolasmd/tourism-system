<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/droitModele.php');
	require_once('application/modele/droitFicheModele.php');
	require_once('application/modele/droitTerritoireModele.php');
	require_once('application/modele/droitChampModele.php');
	require_once('application/db/groupeDb.php');
	require_once('application/db/territoireDb.php');

	/*
	 * Classe abstraite avec les méthodes par défaut pour la récupération des droits
	 */
	abstract class tsDroitsDefault
	{

		// Identifiant de l'utilisateur courant
		protected $idUtilisateur = null;

		// Groupe auquel appartient l'utilisateur courant
		protected $idGroupe = null;

		// Liste des bordereaux administrables par l'utilisateur courant
		protected $bordereauxAdministrables = array();

		// Liste des communes administrables par l'utilisateur courant
		protected $communesAdministrables = array();

		// Liste des territoires administrables par l'utilisateur courant
		protected $territoiresAdministrables = array();

		// Liste d'identifiants de fiches administrables par l'utilisateur courant
		protected $fichesAdministrables = array();

		// Liste des utilisateurs administrables par l'utilisateur courant
		protected $utilisateursAdministrables = array();

		// Liste des groupes administrables par l'utilisateur courant
		protected $groupesAdministrables = array();


		protected $droitsBordereauTerritoire = array();
		protected $droitsBordereauTerritoireChamp = array();

		protected $droitsBordereauCommune = array();
		protected $droitsBordereauCommuneChamp = array();

		protected $droitsBordereauCommunePrivees = array();

		protected $droitsFiche = array();
		protected $droitsFicheChamp = array();


		// Sélection des groupes de l'utilisateur
		const SQL_GROUPE_UTILISATEUR = "SELECT idGroupe FROM sitUtilisateur WHERE idUtilisateur='%d'";

		const SQL_TERRITOIRES_ADMINISTRABLES = 'SELECT bordereau, idTerritoire, droit FROM sitUtilisateurDroitTerritoire WHERE idUtilisateur=\'%1$d\' AND idProfil IS NULL UNION
								SELECT udt.bordereau, udt.idTerritoire, pd.droit FROM sitProfilDroit pd, sitUtilisateurDroitTerritoire udt WHERE udt.idUtilisateur=\'%1$d\' AND udt.idProfil=pd.idProfil';

		const SQL_GROUPE_PARTENAIRES_EXCLUDE = "SELECT idGroupePartenaire FROM sitGroupePartenaire WHERE idGroupe='%d' AND typePartenaire = 'exclude'";

		const SQL_GROUPE_PARTENAIRES_INCLUDE = "SELECT idGroupePartenaire FROM sitGroupePartenaire WHERE idGroupe='%d' AND typePartenaire = 'include'";

		const SQL_FICHES_NON_PARTENAIRE = "SELECT idFiche FROM sitGroupePartenaireFicheExclude WHERE idGroupe='%d'";

		const SQL_FICHES_PARTENAIRE = "SELECT idFiche FROM sitGroupePartenaireFicheInclude WHERE idGroupe='%d'";

		const SQL_COMMUNE_BORDEREAU_FICHES = "SELECT idFiche, codeInsee, bordereau, idGroupe FROM sitFiche WHERE codeInsee IN ('%s') AND bordereau IN ('%s')";

		const SQL_GROUPE_COMMUNE_PRIVE = "SELECT codeInsee FROM sitTerritoireCommune tc, sitGroupeTerritoire gt WHERE gt.idTerritoire = tc.idTerritoire AND gt.idGroupe = '%d' AND prive = 'Y'";

		const SQL_DROIT_FICHE = 'SELECT droit, idFiche FROM sitUtilisateurDroitFiche WHERE idUtilisateur=\'%1$d\' AND idProfil IS NULL UNION
								SELECT pd.droit, udf.idFiche FROM sitProfilDroit pd, sitUtilisateurDroitFiche udf WHERE udf.idUtilisateur=\'%1$d\' AND udf.idProfil=pd.idProfil';


		/*const SQL_DROIT_FICHE_CHAMP = 'SELECT droit FROM sitUtilisateurDroitFicheChamp WHERE idUtilisateur=\'%1$d\' AND idFiche=\'%2$d\' AND idChamp=\'%3$d\' UNION
								SELECT pd.droit FROM sitProfilDroitChamp pd, sitUtilisateurDroitFiche udf WHERE udf.idUtilisateur=\'%1$d\' AND udf.idFiche=\'%2$d\' AND udf.idProfil=pd.idProfil AND pd.idChamp=\'%3$d\'';*/

		const SQL_DROIT_FICHE_CHAMP = 'SELECT udfc.droit FROM sitUtilisateurDroitFicheChamp udfc, sitUtilisateurDroitFiche udf WHERE udfc.idUtilisateur=udf.idUtilisateur AND udfc.idFiche=udf.idFiche AND udfc.idUtilisateur=\'%1$d\' AND udfc.idFiche=\'%2$d\' AND udfc.idChamp=\'%3$d\' AND udf.idProfil IS NULL
									UNION SELECT pd.droit FROM sitProfilDroitChamp pd, sitUtilisateurDroitFiche udf WHERE udf.idUtilisateur=\'%1$d\' AND udf.idFiche=\'%2$d\' AND udf.idProfil=pd.idProfil AND pd.idChamp=\'%3$d\'';

		const SQL_DROIT_TERRITOIRE_CHAMP = 'SELECT udtc.droit FROM sitUtilisateurDroitTerritoireChamp udtc, sitUtilisateurDroitTerritoire udt, sitTerritoireCommune tc WHERE udtc.idUtilisateur=udt.idUtilisateur AND udtc.idTerritoire=udt.idTerritoire AND udtc.bordereau=udt.bordereau AND udtc.idUtilisateur=\'%1$d\' AND udtc.idTerritoire=tc.idTerritoire AND udtc.bordereau=\'%2$s\' AND tc.codeInsee=\'%3$s\' AND udtc.idChamp=\'%4$d\' AND udt.idProfil IS NULL
									UNION SELECT pd.droit FROM sitProfilDroitChamp pd, sitUtilisateurDroitTerritoire udt, sitTerritoireCommune tc WHERE udt.idUtilisateur=\'%1$d\' AND udt.idTerritoire=tc.idTerritoire AND udt.bordereau=\'%2$s\' AND tc.codeInsee=\'%3$s\' AND udt.idProfil=pd.idProfil AND pd.idChamp=\'%4$d\'';


		/*const SQL_TERRITOIRES_ADMINISTRABLES_CHAMP = 'SELECT bordereau, idTerritoire, droit, idChamp FROM sitUtilisateurDroitTerritoireChamp WHERE idUtilisateur=\'%1$d\' UNION
								SELECT pd.droit, udt.idTerritoire, udt.bordereau, pd.idChamp FROM sitProfilDroitChamp pd, sitUtilisateurDroitTerritoire udt WHERE udt.idUtilisateur=\'%1$d\' AND udt.idProfil=pd.idProfil';


		const SQL_FICHES_BORDEREAU_COMMUNE = "SELECT idFiche FROM sitFiche WHERE bordereau='%s' AND codeInsee='%s' AND (idGroupe='%d' OR idGroupe IS NULL)";


		const SQL_FICHES_BORDEREAU_COMMUNE_PRIVE = "SELECT idFiche FROM sitFiche WHERE bordereau='%s' AND codeInsee='%s' AND idGroupe='%d'";*/




		/**
		 * Constructeur : set de idUtilisateur
		 * @param int $idUtilisateur : identifiant de l'utilisateur
		 */
		public function __construct($idUtilisateur)
		{
			$this -> idUtilisateur = $idUtilisateur;
		}


		/**
		 * Charge les droits de l'utilisateur courant
		 */
		public function loadDroits()
		{
			$this -> loadGroupeUtilisateur();
			$this -> loadDroitsTerritoire();
			$this -> loadDroitsFiche();
			$this -> loadGroupesAdministrables();
			$this -> loadUtilisateursAdministrables();
			$this -> loadTerritoiresAdministrables();
		}


		/**
		 * Chargement du groupe de l'utilisateur courant
		 */
		protected function loadGroupeUtilisateur()
		{
			$this -> idGroupe = tsDatabase::getRecord(self::SQL_GROUPE_UTILISATEUR, array($this -> idUtilisateur));
		}


		/**
		 * Chargement des droits sur territoires de l'utilisateur courant
		 */
		protected function loadDroitsTerritoire()
		{
			$droitsBT = tsDatabase::getRows(self::SQL_TERRITOIRES_ADMINISTRABLES, array($this -> idUtilisateur));

			$territoiresAdministrables = array();
			$territoireBordereauxCommunes = array();

			foreach($droitsBT as $droitBordereauTerritoire)
			{
				$bordereau = $droitBordereauTerritoire['bordereau'];
				$idTerritoire = $droitBordereauTerritoire['idTerritoire'];
				$droit = $droitBordereauTerritoire['droit'];

				$territoiresAdministrables[] = $idTerritoire;
				$territoireBordereauxCommunes[$idTerritoire]['bordereaux'][] = $bordereau;

				$this -> bordereauxAdministrables[] = $bordereau;

				$bt = $bordereau . $idTerritoire;
				$this -> droitsBordereauTerritoire[$bt] = (isset($this -> droitsBordereauTerritoire[$bt]))
					? $this -> droitsBordereauTerritoire[$bt] | $droit
					: $droit;
			}

			$territoiresAdministrables = array_unique($territoiresAdministrables);
			$this -> bordereauxAdministrables = array_unique($this -> bordereauxAdministrables);

			foreach($territoiresAdministrables as $idTerritoire)
			{
				$oTerritoire = territoireDb::getTerritoire($idTerritoire);
				$territoireCommunes = territoireDb::getCommunesByTerritoire($oTerritoire);

				$droitT = array();
				foreach($territoireBordereauxCommunes[$idTerritoire]['bordereaux'] as $bordereau)
				{
					$bt = $bordereau . $idTerritoire;

					$droitT[$bordereau] = $this -> droitsBordereauTerritoire[$bt];
				}

				foreach($territoireCommunes as $oCommune)
				{
					foreach($droitT as $bordereau => $droit)
					{
						$this -> droitsBordereauCommune[$bordereau][$oCommune -> codeInsee] =
							(isset($this -> droitsBordereauCommune[$bordereau][$oCommune -> codeInsee]))
							? $this -> droitsBordereauCommune[$bordereau][$oCommune -> codeInsee] | $droit
							: $droit;
					}

					$this -> communesAdministrables[] = $oCommune -> codeInsee;

					$territoireBordereauxCommunes[$idTerritoire]['communes'][] = $oCommune  -> codeInsee;
				}
			}

			$this -> communesAdministrables = array_unique($this -> communesAdministrables);

			foreach($territoireBordereauxCommunes as $idTerritoire => $values)
			{
				$bordereaux = $values['bordereaux'];
				$communes = $values['communes'];

				if(!empty($bordereaux) && !empty($communes))
				{
					$groupeChilds = groupeDb::getGroupesChilds(groupeDb::getGroupe(tsDroits::getGroupeUtilisateur()));

					$groupesPartenairesExclude = tsDatabase::getRecords(self::SQL_GROUPE_PARTENAIRES_EXCLUDE, array(tsDroits::getGroupeUtilisateur()));
					$fichesNonPartenaire = tsDatabase::getRecords(self::SQL_FICHES_NON_PARTENAIRE, array(tsDroits::getGroupeUtilisateur()));

					$groupesPartenairesInclude = tsDatabase::getRecords(self::SQL_GROUPE_PARTENAIRES_INCLUDE, array(tsDroits::getGroupeUtilisateur()));
					$fichesPartenaire = tsDatabase::getRecords(self::SQL_FICHES_PARTENAIRE, array(tsDroits::getGroupeUtilisateur()));

					$fichesInfos = tsDatabase::getRows(self::SQL_COMMUNE_BORDEREAU_FICHES, array($communes, $bordereaux));

					$groupeCommunePrive = array();
					foreach($fichesInfos as $ficheInfo)
					{
						$idFiche = $ficheInfo['idFiche'];
						$bordereau = $ficheInfo['bordereau'];
						$commune = $ficheInfo['codeInsee'];
						$idGroupe = $ficheInfo['idGroupe'];

						$droitBC = $this -> droitsBordereauCommune[$bordereau][$commune];

						$groupeCommunePrive[$idGroupe] = !isset($groupeCommunePrive[$idGroupe])
									? tsDatabase::getRecords(self::SQL_GROUPE_COMMUNE_PRIVE, array($idGroupe))
									: $groupeCommunePrive[$idGroupe];

						if ($idGroupe == tsDroits::getGroupeUtilisateur())
						{
							$this -> fichesAdministrables[] = $idFiche;
							$this -> droitsFiche[$idFiche] = (isset($this -> droitsFiche[$idFiche])) ? $this -> droitsFiche[$idFiche] | $droitBC : $droitBC;
						}
						elseif (in_array($idGroupe, $groupeChilds))
						{
							if (!in_array($commune, $groupeCommunePrive[$idGroupe]))
							{
								$this -> fichesAdministrables[] = $idFiche;
								$this -> droitsFiche[$idFiche] = (isset($this -> droitsFiche[$idFiche])) ? $this -> droitsFiche[$idFiche] | $droitBC : $droitBC;
							}
						}
						else
						{
							if (in_array($idGroupe, $groupesPartenairesExclude) && !in_array($idFiche, $fichesNonPartenaire))
							{
								if (!in_array($commune, $groupeCommunePrive[$idGroupe]))
								{
									$this -> fichesAdministrables[] = $idFiche;
									$this -> droitsFiche[$idFiche] = DROIT_VISUALISATION;
								}
							}
							elseif (in_array($idGroupe, $groupesPartenairesInclude) && in_array($idFiche, $fichesPartenaire))
							{
								$this -> fichesAdministrables[] = $idFiche;
								$this -> droitsFiche[$idFiche] = DROIT_VISUALISATION;
							}
						}
					}
				}
			}
		}


		/**
		 * Chargement des droits sur fiches de l'utilisateur courant
		 */
		protected function loadDroitsFiche()
		{
			$droitsFiche = tsDatabase::getRows(self::SQL_DROIT_FICHE, array($this -> idUtilisateur));
			foreach($droitsFiche as $droitFiche)
			{
				$idFiche = $droitFiche['idFiche'];
				$droit = $droitFiche['droit'];
				$this -> fichesAdministrables[] = $idFiche;
				$this -> droitsFiche[$idFiche] = (isset($this -> droitsFiche[$idFiche])) ? $this -> droitsFiche[$idFiche] & $droit : $droit;
			}

			$this -> fichesAdministrables = array_unique($this -> fichesAdministrables);
		}




		/**
		 * Retourne le groupe de l'utilisateur courant
		 */
		final public function getGroupeUtilisateur()
		{
			return $this -> idGroupe;
		}


		/**
		 * Retourne les bordereaux administrables par l'utilisateur courant
		 */
		final public function getBordereauxAdministrables()
		{
			return $this -> bordereauxAdministrables;
		}


		/**
		 * Retourne les communes administrables par l'utilisateur courant
		 */
		final public function getCommunesAdministrables()
		{
			return $this -> communesAdministrables;
		}


		/**
		 * Retourne les territoires administrables par l'utilisateur courant
		 */
		final public function getTerritoiresAdministrables()
		{
			return $this -> territoiresAdministrables;
		}


		/**
		 * Retourne les fiches administrables par l'utilisateur courant
		 */
		final public function getFichesAdministrables()
		{
			return $this -> fichesAdministrables;
		}


		/**
		 * Retourne les utilisateurs administrables par l'utilisateur courant
		 */
		final public function getUtilisateursAdministrables()
		{
			return $this -> utilisateursAdministrables;
		}


		/**
		 * Retourne les groupes administrables par l'utilisateur courant
		 */
		final public function getGroupesAdministrables()
		{
			return $this -> groupesAdministrables;
		}




		public function isFicheAdministrable($oFiche)
		{
			return in_array($oFiche -> idFiche, $this -> fichesAdministrables);
		}


		public function getDroitFiche(ficheModele $oFiche)
		{
			// Si fiche en partage => visualisation
			// Si fiche de sous groupe ou groupe courant => droit en base
			
			if ($this -> isFicheAdministrable($oFiche) === false)
			{
				throw new SecuriteException("Vous n'avez pas accès à cette fiche.", 516, array('idFiche' => $oFiche -> idFiche));
			}
			$oDroit = new droitModele();
			$oDroit -> loadDroit($this -> droitsFiche[$oFiche -> idFiche]);
			return $oDroit -> getDroit();
		}


		public function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp)
		{
			// Si fiche en partage => visualisation sauf si champ en base avec scope groupe => droit en base
			// Si fiche de sous groupe ou groupe courant => droit en base
			
			$droitChamp = tsDatabase::getRecord(self::SQL_DROIT_FICHE_CHAMP, array($this -> idUtilisateur, $oFiche -> idFiche, $oChamp -> idChamp));
			
			if ($droitChamp === false)
			{
				$droitChamp = tsDatabase::getRecord(self::SQL_DROIT_TERRITOIRE_CHAMP, array($this -> idUtilisateur, $oFiche -> bordereau, $oFiche -> codeInsee, $oChamp -> idChamp));
			}
			
			return $droitChamp;
		}




		/*protected function loadDroitsTerritoireChamp()
		{
			$droitsBT = tsDatabase::getRows(self::SQL_TERRITOIRES_ADMINISTRABLES_CHAMP, array($this -> idUtilisateur));
			foreach($droitsBTC as $droitBordereauTerritoireChamp)
			{
				$bordereau = $droitBordereauTerritoireChamp['bordereau'];
				$idTerritoire = $droitBordereauTerritoireChamp['idTerritoire'];
				$idChamp = $droitBordereauTerritoireChamp['idChamp'];
				$droit = $droitBordereauTerritoireChamp['droit'];
				$btc = $bordereau . $idTerritoire . '-' . $idChamp;
				$this -> droitBordereauTerritoireChamp[$btc] = (isset($this -> droitBordereauTerritoireChamp[$btc])) ?
							$this -> droitBordereauTerritoireChamp[$btc] | $droit : $droit;
				$oTerritoire = territoireDb::getTerritoire($idTerritoire);
				foreach(territoireDb::getCommunesByTerritoire($oTerritoire) as $commune)
				{
					$this -> droitBordereauCommuneChamp[$bordereau][$commune -> codeInsee][$idChamp] =
							(isset($this -> droitBordereauCommuneChamp[$bordereau][$commune -> codeInsee][$idChamp])) ?
							$this -> droitBordereauCommuneChamp[$bordereau][$commune -> codeInsee][$idChamp] | $droit : $droit;
				}
			}

			foreach($this -> droitBordereauCommuneChamp as $bordereauCommune => $bordereau)
			{
				foreach($bordereau as $commune => $droitBC)
				{
					$fiches = tsDatabase::getRows(self::SQL_FICHES_BORDEREAU_COMMUNE, array($bordereau, $commune));
					foreach($fiches as $fiche)
					{
						$this -> droitsFicheChamp[$idFiche][$idChamp] = (isset($this -> droitsFiche[$idFiche])) ?
								$this -> droitsFiche[$idFiche] | $droitBC : $droitBC;
					}
				}
			}
		}*/

	}
