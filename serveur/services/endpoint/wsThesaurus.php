<?php

	/**
	 * @version        0.4 alpha-test - 2013-06-03
	 * @package        Tourism System Server
	 * @copyright      Copyright (C) 2010 Raccourci Interactive
	 * @license        Qt Public License; see LICENSE.txt
	 * @author         Nicolas Marchand <nicolas.raccourci@gmail.com>
	 */

	require_once('application/db/groupeDb.php');
	require_once('application/db/territoireDb.php');
	require_once('application/db/thesaurusDb.php');
	require_once('application/utils/stemming/stem.php');

	/**
	 * Classe wsThesaurus - endpoint du webservice Thesaurus
	 * Gestion des thésaurus
	 */
	final class wsThesaurus extends wsEndpoint
	{

		/**
		 * Méthode de créaton d'un thésaurus local
		 *
		 * @param string $codeThesaurus : identifiant de thésaurus à créer
		 *                              code thésaurus sous la forme MTH.LOC.XXX
		 * @param string $libelle       : libellé du thésaurus à créer
		 *
		 * @return int prefixe : préfixe numérique pour la codification TourinFrance
		 * @access root superadmin admin desk manager
		 */
		protected function _createThesaurus( $codeThesaurus , $libelle , $idNorme )
		{
			$this->restrictAccess( 'root' );
			$prefixe = thesaurusDb::createThesaurus( $codeThesaurus , $libelle , $idNorme );

			return array( 'prefixe' => $prefixe );
		}


		/**
		 * Retourne la liste des thésaurii visibles de l'utilisateur
		 *
		 * @return thesaurusCollection thesaurii : collection de thesaurusModele
		 * @access root
		 */
		protected function _getThesaurii()
		{
			// @todo : ouvrir cette méthode à d'autres utilisateurs
			$this->restrictAccess( 'root' );
			$thesaurii = thesaurusDb::getThesaurii();

			return array( 'thesaurii' => $thesaurii );
		}


		/**
		 * Retourne la liste des thésaurii visibles de l'utilisateur
		 *
		 * @return thesaurusCollection thesaurii : collection de thesaurusModele
		 * @access root
		 */
		protected function _getUserThesaurii()
		{
			// récupération des thesaurii
			$idGroupe     = tsDroits::getGroupeUtilisateur();
			$oGroupe      = groupeDb::getGroupe( $idGroupe );
			$oTerritoires = groupeDb::getGroupeTerritoires( $oGroupe );

			// concaténation des listes de thesaurii
			$thesaurii = array();
			$thesaurii[] = thesaurusDb::getThesaurus('MTH.NAT.TIFV30');
			foreach( $oTerritoires as $oTerritoire )
			{
				$oThesaurii = territoireDb::getThesaurusByTerritoire( $oTerritoire );
				foreach( $oThesaurii as $oThesaurus )
				{
					$thesaurii[ ] = $oThesaurus;
				}
				$thesaurii = array_merge($thesaurii,$oThesaurii);
			}
			// un thesaurus peut apparaitre plusieurs fois (plusieurs territoires)
			return array( 'thesaurii' => $thesaurii  );
		}


		/**
		 * Mise à jour du libellé et du codeThesaurus
		 *
		 * @param string $codeThesaurus : identifiant de thésaurus
		 * @param string $libelle       : libellé du thésaurus
		 *
		 * @access root
		 */
		protected function _updateThesaurus( $codeThesaurus , $libelle )
		{
			$this->restrictAccess( 'root' );
			$oThesaurus = thesaurusDb::getThesaurus( $codeThesaurus );
			$this->checkDroitThesaurus( $oThesaurus , DROIT_ADMIN );
			thesaurusDb::updateThesaurus( $oThesaurus , $codeThesaurus , $libelle );

			return array();
		}


		/**
		 * Retourne les entrées d'un thésaurus dans une langue
		 *
		 * @param string $codeThesaurus : identifiant de thésaurus
		 * @param string $codeLangue    : code langue ISO 639-1
		 *
		 * @return array entreeThesaurus : [] = array('cle', 'libelle', 'liste')
		 * @access root superadmin admin desk manager
		 */
		protected function _getEntreesThesaurus( $codeThesaurus , $codeLangue )
		{
			$oThesaurus = thesaurusDb::getThesaurus( $codeThesaurus );
			$this->checkDroitThesaurus( $oThesaurus , DROIT_GET );
			$entreesThesaurus = thesaurusDb::getEntreesThesaurus( $oThesaurus , $codeLangue );

			return array( 'entreesThesaurus' => $entreesThesaurus );
		}


		/**
		 * Retourne les entrées de plusieurs thésaurii dans une langue
		 *
		 * @param string $codesThesaurii : identifiants de thésaurii
		 * @param string $codeLangue     : code langue ISO 639-1
		 *
		 * @return array entreesThesaurii : [] = array('cle', 'libelle', 'liste')
		 * @access root superadmin admin desk manager
		 */
		protected function _getEntreesThesaurii( $codesThesaurii , $codeLangue )
		{
			$entreesThesaurii = array();
			foreach( $codesThesaurii as $codeThesaurus )
			{
				$oThesaurus = thesaurusDb::getThesaurus( $codeThesaurus );
				$this->checkDroitThesaurus( $oThesaurus , DROIT_GET );
				$entreesThesaurus = thesaurusDb::getEntreesThesaurus( $oThesaurus , $codeLangue );
				$entreesThesaurii = array_merge( $entreesThesaurii , $entreesThesaurus );
			}
			return array( 'entreesThesaurii' => $entreesThesaurii );
		}


		/**
		 * Retourne une entrée de thésaurus dans une langue
		 *
		 * @param string $codeTif    : code TIF de l'entrée
		 * @param string $codeLangue : code langue ISO 639-1
		 *
		 * @return object entreeThesaurus : array('cle', 'libelle', 'liste')
		 * @access root superadmin admin desk manager
		 */
		protected function _getEntreeThesaurus( $codeTif , $codeLangue )
		{
			$entreeThesaurus = thesaurusDb::getEntreeThesaurus( $codeTif , $codeLangue );

			return array( 'entreeThesaurus' => $entreeThesaurus );
		}


		/**
		 * Ajoute une entrée à un thésaurus local
		 *
		 * @param string $codeThesaurus : identifiant de thésaurus
		 * @param string $cleParente    : clé de la liste parente (MTH.NAT.TIFv30)
		 * @param string $libelle       : libellé à ajouter à la liste
		 * @param object $codeLangue    [optional] : code langue ISO 639-1 (fr par défaut)
		 *
		 * @return string codeTif : code Tourinfrance de l'entrée créée (101.02.01.02.01)
		 * @access root
		 */
		protected function _addEntreeThesaurus( $codeThesaurus , $cleParente , $libelle , $codeLangue = 'fr' )
		{
			$this->restrictAccess( 'root' );
			$oThesaurus = thesaurusDb::getThesaurus( $codeThesaurus );
			$this->checkDroitThesaurus( $oThesaurus , DROIT_GET );
			$codeTif = thesaurusDb::addEntreeThesaurus( $oThesaurus , $cleParente , $libelle , $codeLangue );

			return array( 'codeTif' => $codeTif );
		}


		/**
		 * Supprime une entrée d'un thésaurus local
		 *
		 * @param string $codeTIF : code Tourinfrance à supprimer
		 *
		 * @access root
		 */
		protected function _deleteEntreeThesaurus( $codeTIF )
		{
			// @todo : ajouter thésaurus pour effectuer le controle
			$this->restrictAccess( 'root' );
			thesaurusDb::deleteEntreeThesaurus( $codeTIF );

			return array();
		}


		/**
		 * Ajoute dans une nouvelle langue ou renomme dans une langue existante
		 * une entrée de thésaurus local
		 *
		 * @param string $codeTIF    : code Tourinfrance à supprimer
		 * @param string $codeLangue : code langue ISO 639-1
		 * @param string $libelle    : libellé dans la langue spécifiée
		 *
		 * @access root
		 */
		protected function _setEntreeThesaurus( $codeTIF , $codeLangue , $libelle )
		{
			// @todo : ajouter thésaurus pour effectuer le controle
			$this->restrictAccess( 'root' );
			thesaurusDb::setEntreeThesaurus( $codeTIF , $codeLangue , $libelle );

			return array();
		}


		protected function _translateEntreeThesaurus( $codeTIF , $codeLangue , $libelle )
		{
			$this->restrictAccess( 'root' );
			thesaurusDb::translateEntreeThesaurus( $codeTIF , $codeLangue , $libelle );

			return array();
		}


		/**
		 * Retourne une liste d'entrées de thésaurus
		 *
		 * @param string $liste : liste à retourner
		 * @param string $cle   : expression régulière de la clé (02.01.01.*)
		 *
		 * @access all
		 */
		protected function _getListeThesaurus( $liste , $cle , $pop )
		{
			$liste = thesaurusDb::getListeThesaurus( $liste , $cle , $pop );

			return array( 'liste' => $liste );
		}


		/**
		 * Retourne une partie du thésaurus sous forme d'un arbre
		 *
		 * @param string $cle : expression régulière de la clé (02.01.01.*)
		 *
		 * @access all
		 */
		protected function _getArbreThesaurus( $cle , $pop )
		{
			$arbre = thesaurusDb::getArbreThesaurus( $cle , $pop );

			return array( 'arbre' => $arbre );
		}


		private static $balisesListes = array(
			'tif:DetailClassement'      => 'LS_TypeClassement' ,
			'tif:DetailPrestation'      => 'LS_Prestation' ,
			'tif:Classement'            => 'LS_Classement' ,
			'tif:ControlledVocabulary'  => 'LS_ControlledVocabulary' ,
			'tif:Prestation'            => 'LS_Prestation' ,
			'tif:Distance'              => 'LS_Unite' ,
			'tif:DetailOffrePrestation' => 'LS_Prestation' ,
			'tif:OffresPrestations'     => 'LS_Prestation' ,
			'tif:DetailTarif'           => 'LS_Tarifs' ,
			'tif:ModePaiement'          => 'LS_ModePaiement' ,
			'tif:Civilite'              => 'LS_Civilite'
		);

		private static $listesBordereaux = array(
			'LS_Tarifs'  => array(
				'*'   => '13.04.01' ,
				'HPA' => '13.04.02' ,
				'HOT' => '13.04.03' ,
				'HLO' => '13.04.04' ,
				'RES' => '13.04.05' ,
				'VIL' => '13.04.06' ,
				'ASC' => '13.04.07'
			) ,
			'LS_Classement'     => array(
				'HPA' => '06.04.01.02' ,
				'HOT' => '06.04.01.03' ,
				'HLO' => '06.04.01.04' ,
				'RES' => '06.04.01.05' ,
				'VIL' => '06.04.01.06'
			) ,
			'LS_TypeClassement' => array(
				'HPA' => '06.03.01' ,
				'HLO' => '06.03.02' ,
				'HOT' => '06.03.03' ,
				'RES' => '06.03.04' ,
				'VIL' => '06.03.05'
			)
		);


		/**
		 * Retourne une liste d'entrées de thésaurus équivalentes au résultat du stemming de celle fournie en entrée
		 *
		 * @access all
		 */
		protected function _getEntreeThesaurusByLibelleViaStem( $libelle , $xpath , $bordereau )
		{
			// décodage du libellé pour le transmettre à la DB
			$libelle = utf8_decode( urldecode( $libelle ) );

			// stemmer
			$stemmer = new Stem();

			// sortir les mots séparés par un espace
			$stem = $stemmer->stem( $libelle , ' ' );

			// récupération des thesaurii
			$idGroupe     = tsDroits::getGroupeUtilisateur();
			$oGroupe      = groupeDb::getGroupe( $idGroupe );
			$oTerritoires = groupeDb::getGroupeTerritoires( $oGroupe );

			$thesaurii = array();
			foreach( $oTerritoires as $oTerritoire )
			{
				$oThesaurii = territoireDb::getThesaurusByTerritoire( $oTerritoire );
				foreach( $oThesaurii as $oThesaurus )
				{
					$thesaurii[ ] = $oThesaurus->codeThesaurus;
				}
			}
			$thesaurii = array_unique( $thesaurii );

			// extraction de toutes les "balises" une par une
			$xpathElements = explode( "/" , $xpath );

			// inversion du tableau pour lecture du XPath de droite à gauche
			$xpathElements = array_reverse( $xpathElements );

			// motif pour trouver un TIF
			$TifPattern = '/attribute::type=["\'](.*?)["\']/';

			// recherche de tous les codes TIF et de la liste
			$liste = null;
			$tifs  = array();
			foreach( $xpathElements as $elem )
			{
				// si la liste n'a pas encore été trouvée
				if( $liste === null )
				{
					// chercher dans la liste une valeur mappée
					foreach( self::$balisesListes as $tbalise => $tliste )
					{
						// recherche dans la liste des balises associées aux listes
						if( preg_match( '/(^|[^a-zA-Z0-9_])' . $tbalise . '([^a-zA-Z0-9_]|$)/' , $elem ) )
						{
							$liste = $tliste;
							break;
						}
					}
				}

				// recherche de TIF dans le morceau de xpath courant
				$matches = array();
				if( preg_match( $TifPattern , $elem , $matches ) >= 1 )
				{
					$tifs[ ] = $matches[ 1 ];
				}

			}

			// si la liste a été trouvée, on essaie d'ajouter le TIF lié au bordereau fourni en paramètre
			if( $liste !== null )
			{
				if( isset( self::$listesBordereaux[ $liste ] ) )
				{
					// si une liste est liée à ce bordereau, l'utiliser
					if( isset( self::$listesBordereaux[ $liste ][ $bordereau ] ) )
					{
						$tifs    = array(); // vider la liste
						$tifs[ ] = self::$listesBordereaux[ $liste ][ $bordereau ];
					}
					// si tous les bordereaux sont concernés par cette liste
					if( isset( self::$listesBordereaux[ $liste ][ '*' ] ) )
					{
						$tifs    = array(); // vider la liste
						$tifs[ ] = self::$listesBordereaux[ $liste ][ '*' ];
					}
				}
			}

			/**
			 * $liste : null ou String
			 * $tifs  : array des codes TIF trouvés dans le xpath + celui de la liste
			 */

			// liste des entrées de theso correspondant ET au stem ET aux theso accessibles ET à la liste
			$entrees = array();
			try
			{
				$entrees = thesaurusDb::getEntreeThesaurusByLibelleViaStem( $stem , $thesaurii , $liste );

				// sélection des entrées en fonction de leur correspondance aux codes TIF trouvés
				if( count( $tifs ) > 0 )
				{
					$entreesMatchTif = array();
					foreach( $entrees as $entree )
					{
						foreach( $tifs as $tif )
						{
							if( preg_match( '/^(99\.|[1-9][0-9]{2}\.)?' . $tif . '(\..*|$)/' , $entree[ 'cle' ] ) )
							{
								$entreesMatchTif[ ] = $entree;
							}
						}
					}
					// éliminer les TIF en doublon
					array_unique( $tifs );
					$entrees = $entreesMatchTif;
				}

			}
			catch( Exception $e )
			{
				throw $e;
			}

			if( count( $entrees ) == 0 )
			{
				throw new ApplicationException( "Aucune entrée de thésaurus trouvée." );
			}

			return array( 'liste' => $entrees );
		}

	}
