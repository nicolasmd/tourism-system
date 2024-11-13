<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/communeDb.php');
	require_once('application/db/ficheDb.php');
	require_once('application/modele/bordereauModele.php');
	require_once('application/modele/ficheSimpleModele.php');


	/**
	 * Classe wsFiche - endpoint du webservice Fiche
	 * Service de gestion des fiches (liste, création, suppression)
	 * @access root superadmin admin desk manager
	 */
	final class wsFiche extends wsEndpoint
	{


		/**
		 * Retourne un objet ficheModele
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idFicheVersion [optional] : identifiant de la version de la fiche sitFicheVersion.idFicheVersion
		 * @return ficheModele fiche
		 * @access root superadmin admin desk manager
		 */
		protected function _getFiche($idFiche, $idFicheVersion = null)
		{
			//$this -> restrictAccess('root', 'superadmin', 'admin', 'desk', 'manager');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche, $idFicheVersion);
			if (is_null($idFicheVersion) === false)
			{
				$oFicheSimple = ficheSimpleModele::loadByXml($oFiche -> xml);
				$oFiche -> raisonSociale = $oFicheSimple -> raisonSociale;
				$oFiche -> gpsLat = $oFicheSimple -> gpsLat;
				$oFiche -> gpsLng = $oFicheSimple -> gpsLng;
			}
			$this -> checkAccesFiche($oFiche);
			$droitsFiche = tsDroits::getDroitFiche($oFiche);
			$fiche = ficheDb::getFiche($oFiche, $droitsFiche);
			return array('fiche' => $fiche);
		}


		/**
		 * Retourne un objet ficheModele
		 * @param string $reference : identifiant externe de la fiche
		 * @return ficheModele fiche
		 * @access root superadmin admin desk manager
		 */
		protected function _getFicheByRefExterne($reference)
		{
			//$this -> restrictAccess('root', 'superadmin', 'admin', 'desk', 'manager');
			$idFiche = ficheDb::getIdFicheByRefExterne($reference);
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkAccesFiche($oFiche);
			$droitsFiche = tsDroits::getDroitFiche($oFiche);
			$fiche = ficheDb::getFiche($oFiche, $droitsFiche);
			return array('fiche' => $fiche);
		}


		/**
		 * Création d'une fiche
		 * @param string $bordereau : bordereau Tourinfrance de la fiche à créer
		 * @param string $codeInsee : code insee de la commune
		 * @param string $referenceExterne [optional] : référence externe de la fiche
		 * @return int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _createFiche($bordereau, $codeInsee, $referenceExterne = null)
		{
			// @todo:Retourne une fiche simplifiée ?
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oBordereau = new bordereauModele();
			$oBordereau -> setBordereau($bordereau);
			$oCommune = communeDb::getCommune($codeInsee);
			$this -> checkDroitBordereauCommune($oBordereau, $oCommune, DROIT_CREATION_FICHES);
			$idFiche = ficheDb::createFiche($oBordereau, $oCommune, $referenceExterne);
			return array('idFiche' => $idFiche);
		}


		/**
		 * Supression d'une fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _deleteFiche($idFiche)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_SUPPRESSION_FICHES);
			ficheDb::deleteFiche($oFiche);
			return array();
		}


		/**
		 * Retourne la liste des fiches visibles par l'utilisateur courant
		 *
		 * @return fiches : ficheCollection (collection de ficheModele)
		 * @access root superadmin admin desk manager
		 */
		protected function _getFiches()
		{
			return array( 'fiches' => ficheDb::getFiches() );
		}
                
                
                /**
                 * Return the list of fiches own by the current user with pagination
                 * @param int $start
                 * @param int $limit
                 * @return fiches : ficheCollection (collection de ficheModele)
                 * @access root superadmin admin desk manager
                 */
		protected function _listFiches($parameters = array())
		{
                        $response = ficheDb::listFiches($parameters);
                        return array('fiches' => $response);
		}

                


		/**
		 * Retourne la liste des fiches visibles par l'utilisateur courant
		 *
		 * @return fiches : ficheCollection (collection de ficheModele)
		 * @access root superadmin admin desk manager
		 */
		protected function _getFichesIds()
		{
			return array( 'fiches' => ficheDb::getFichesIds() );
		}



		/**
		 * Retourne la liste des fiches correspondant aux critères de recherche envoyés
		 * @return fiches : ficheCollection (collection de ficheModele)
		 * @param string $bordereau : code bordereau de filtrage
		 * @param array $filters : filtres de recherche : tableau de codes Tourinfrance
		 * @access root superadmin admin desk manager
		 */
		protected function _rechercheFiches($bordereau = null, $filtersParam = array())
		{
			$fiches = ficheDb::getFichesBordereau($bordereau);
//			$fiches = ficheDb::getFiches();
			$hasFilters = (count($filtersParam) > 0);

			$filters = array();
			foreach($filtersParam as $filterListOr)
			{
				$filters[] = explode('|', $filterListOr);
			}

			$fichesRetourB = array();

			foreach($fiches as $k => &$fiche)
			{
				// Bordereau
				if (is_null($bordereau) === false) // y'a un bordereau
				{
					if ($bordereau == $fiche -> bordereau) // ne charger que les fiches ayant ce bordereau
					{
						$fichesRetourB[] = $fiche;
					}
				}
				else // pas de bordereau : charger toutes les fiches
				{
					$fichesRetourB[ ] = $fiche;
				}

			}

			$fichesRetour = array();

			foreach($fichesRetourB as $fichearray)
			{
				$oFiche = ficheDb::getFicheByIdFiche($fichearray->idFiche);

				// Filters
				if ($hasFilters)
				{
					$keepAll = true;

					foreach($filters as $filter)
					{
						$keepAcceptOne = false;

						foreach($filter as $filterOr)
						{
							// si on trouve au moins un contvoc : pas la peine de continuer les filtres OR
							if( strpos( $oFiche->xml , '="' . $filterOr . '"' ) )
							{
								$keepAcceptOne = true;
								break;
							}
						}

						$keepAll = $keepAll && $keepAcceptOne;

						if (!$keepAll)
						{
							break;
						}
					}
					if( $keepAll )
					{
						$fichesRetour[] = $fichearray;
					}

				}
				else // si pas de filtre : ajouter toutes les fiches du bordereau
				{
					$fichesRetour[ ] = $fichearray;
				}

			}

			return array('fiches' => $fichesRetour);
		}


		/**
		 * Méthode de sauvegarde de la fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param stdClass $stdFiche : objet de type stdClass tel que retourné par getFiche
		 * @access root superadmin admin desk manager
		 */
		protected function _sauvegardeFiche($idFiche, $stdFiche, $champsValide = null, $champsRefuse = null)
		{
			$champsValide = !is_null($champsValide) ? $champsValide : array();
			$champsRefuse = !is_null($champsRefuse) ? $champsRefuse : array();
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkAccesFiche($oFiche);
			$droitsFiche = tsDroits::getDroitFiche($oFiche);

			$idFicheVersion = null;

			if( ficheDb::sauvegardeFiche($oFiche, $stdFiche, $droitsFiche, $champsValide, $champsRefuse))
			{
				$newVersion = ficheDb::getFicheVersion($idFiche);
				$idFicheVersion = $newVersion['idFicheVersion'];
			}

			return array('idVersion' => $idFicheVersion);
		}


		/**
		 * Change l'état de publication d'une fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param bool $publication : le statut de publication
		 * @access root superadmin admin desk manager
		 */
		protected function _setPublicationFiche($idFiche, $publication)
		{
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkAccesFiche($oFiche);
			ficheDb::setPublicationFiche($oFiche, $publication);
			return array();
		}


		/**
		 * Récupère les versions d'une fiche
		 * @return versions : Versions de la fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _getFicheVersions($idFiche)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkAccesFiche($oFiche);
			$versions = ficheDb::getFicheVersions($oFiche);
			return array('versions' => $versions);
		}


		/**
		 * Supprime une version de fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idFicheVersion : identifiant de la version sitFicheVersion.idFicheVersion
		 * @access root superadmin admin
		 */
		protected function _deleteFicheVersion($idFiche, $idFicheVersion)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_SUPPRESSION_FICHES);
			ficheDb::deleteFicheVersion($oFiche, $idFicheVersion);
			return array();
		}


		/**
		 * Restaure une version de fiche en créant une nouvelle version
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @param int $idFicheVersion : identifiant de la version sitFicheVersion.idFicheVersion
		 * @access root superadmin admin
		 */
		protected function _restoreFicheVersion($idFiche, $idFicheVersion)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			$oFiche = ficheDb::getFicheByIdFiche($idFiche, $idFicheVersion);
			$this -> checkDroitFiche($oFiche, DROIT_MODIFICATION);

			$newIdFicheVersion = null;

			if( ficheDb::createFicheVersion($oFiche -> idFiche, $oFiche -> xml) )
			{
				$newVersion = ficheDb::getFicheVersion($idFiche);
				$newIdFicheVersion = $newVersion['idFicheVersion'];
			}

			return array('idVersion' => $newIdFicheVersion);
		}

	}
