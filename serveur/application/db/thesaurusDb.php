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
	require_once('application/modele/entreeThesaurusModele.php');
	require_once('application/modele/thesaurusModele.php');
	require_once('application/utils/stemming/stem.php');
	require_once( 'application/utils/fonctions.php' );

// TODO : changer la clé primaire vers codeThesaurus
	final class thesaurusDb
	{

		const SQL_THESAURUS                      = "SELECT codeThesaurus, libelle, prefixe, idThesaurus FROM sitThesaurus WHERE codeThesaurus='%s'";
		const SQL_THESAURII                      = "SELECT codeThesaurus FROM sitThesaurus";
		const SQL_UPDATE_THESAURUS               = 'UPDATE sitThesaurus SET codeThesaurus=\'%1$s\', libelle=\'%2$s\' WHERE codeThesaurus=\'%1$s\'';
		const SQL_CREATE_THESAURUS               = "INSERT INTO sitThesaurus(codeThesaurus, libelle, prefixe) VALUES ('%s', '%s', '%s')";

		const SQL_ENTREES_THESAURUS = "SELECT cle, libelle, liste, '%s' as lang FROM sitEntreesThesaurus WHERE codeThesaurus='%s' AND lang='%s'";

		const SQL_ENTREES_THESAURUS_ROOT_ALL_LNG = "SELECT cle, libelle, liste, lang FROM sitEntreesThesaurus WHERE codeThesaurus='%s'";

		const SQL_ENTREE_THESAURUS               = "SELECT cle, libelle, liste FROM sitEntreesThesaurus WHERE cle='%s' AND lang='%s'";

		const SQL_ENTREES_THESAURUS_MASQUE       = "SELECT DISTINCT cle FROM sitEntreesThesaurusMasque WHERE idThesaurus IN ('%s')";
		const SQL_ADD_ENTREE_THESAURUS           = "INSERT INTO sitEntreesThesaurus (codeThesaurus, cle, liste, lang, libelle) VALUES ('%s', '%s', '%s', '%s', '%s')";

		const SQL_LISTE_FROM_CLE          = "SELECT liste FROM sitEntreesThesaurus WHERE cle='%s' AND lang='fr'";
		const SQL_NUMERO_TIF              = "SELECT IF (MAX(SUBSTRING_INDEX(cle, '.', -1) + 1) < 10,
														CONCAT(SUBSTRING(cle, 1, LENGTH(cle) - LOCATE('.', REVERSE(cle))), '.', '0', MAX(SUBSTRING_INDEX(cle, '.', -1) + 1)),
														CONCAT(SUBSTRING(cle, 1, LENGTH(cle) - LOCATE('.', REVERSE(cle))), '.', MAX(SUBSTRING_INDEX(cle, '.', -1) + 1)))
												AS codeTIF FROM sitEntreesThesaurus WHERE codeThesaurus='%s' AND (LOCATE('%s',cle)=4 OR LOCATE('%s',cle)=5)";
		const SQL_CODE_PARENT             = "SELECT SUBSTRING_INDEX(cle, '.', -1)) AS numero FROM sitEntreesThesaurus WHERE codeThesaurus='%s' AND liste='%s'";
		const SQL_PREFIXE_THESAURUS       = "SELECT MAX(prefixe) FROM sitThesaurus WHERE prefixe IS NOT NULL";
		const SQL_DELETE_ENTREE_THESAURUS = "DELETE FROM sitEntreesThesaurus WHERE cle='%s' AND codeThesaurus<>'MTH.NAT.TIFV30'";

		const SQL_SET_ENTREE_THESAURUS    = "REPLACE INTO sitEntreesThesaurus(libelle, cle, lang, codeThesaurus,liste) VALUES ('%s', '%s', '%s','%s','%s')";
		const SQL_VALUE_BY_KEY            = "SELECT libelle FROM sitEntreesThesaurus WHERE cle='%s' AND lang='%s'";
		const SQL_THESAURUS_BY_KEY        = "SELECT codeThesaurus FROM sitEntreesThesaurus WHERE cle='%s' LIMIT 0,1";
		const SQL_LISTE_THESAURUS         = "SELECT 0 AS idNorme, cle, libelle FROM sitEntreesThesaurus WHERE liste = '%s' AND cle REGEXP('^%s$') AND codeThesaurus = '%s' AND lang = 'fr'";
		const SQL_LISTE_THESAURUS_ALL_LNG = "SELECT cle, libelle FROM sitEntreesThesaurus WHERE liste = '%s'
									AND cle REGEXP('^%s$') AND codeThesaurus = '%s'";

		const SQL_ARBRE_THESAURUS = "SELECT 1 AS idNorme, cle, libelle FROM sitEntreesThesaurus WHERE cle REGEXP('^((99|[0-9]{3}).)?%s(.[0-9]{2,3})*$') AND codeThesaurus = '%s' AND lang = 'fr'";

		const SQL_KEY_THESAURUS_BY_STEM        = "SELECT t.cle, t.libelle , t.codeThesaurus FROM sitEntreesThesaurus t INNER JOIN sitEntreesThesaurusStems s ON (t.cle = s.cle AND t.lang = s.lang) WHERE s.stem LIKE '%s' AND s.lang = 'fr' AND t.codeThesaurus IN ( 'MTH.NAT.TIFV30' , 'MTH.LOC.RAC' , '%s' ) AND t.liste = '%s'";
		const SQL_ADD_ENTREE_THESAURUS_STEM    = 'INSERT INTO sitEntreesThesaurusStems (cle, lang, stem) VALUES (\'%1$s\', \'%2$s\', \'%3$s\')';
		const SQL_UPDATE_ENTREE_THESAURUS_STEM = 'UPDATE sitEntreesThesaurusStems SET stem = \'%1$s\' WHERE cle = \'%2$s\' AND lang = \'%3$s\'';
		const SQL_DELETE_ENTREE_THESAURUS_STEM = 'DELETE FROM sitEntreesThesaurusStems WHERE cle = \'%s\' ';


		private static $pop;
		private static $entrees;
		private static $entreesByKey;
		private static $entreesKeys;


		public static function getThesaurus( $codeThesaurus )
		{
			$result = tsDatabase::getObject( self::SQL_THESAURUS , array( $codeThesaurus , DB_FAIL_ON_ERROR ) );

			return thesaurusModele::getInstance( $result , 'thesaurusModele' );
		}


		public static function createThesaurus( $codeThesaurus , $libelle)
		{
			if( self::isCodeThesaurusValide( $codeThesaurus ) === false )
			{
				throw new ApplicationException( "Le code thésaurus n'est pas valide" );
			}
			$prefixe = self::getNextPrefixeThesaurus();
			tsDatabase::insert( self::SQL_CREATE_THESAURUS , array( $codeThesaurus , $libelle , $prefixe ) );

			return $prefixe;
		}


		public static function updateThesaurus( $oThesaurus , $codeThesaurus , $libelle )
		{
			if( self::isCodeThesaurusValide( $codeThesaurus ) === false )
			{
				throw new ApplicationException( "Le code thésaurus $codeThesaurus n'est pas valide" );
			}

			return tsDatabase::query( self::SQL_UPDATE_THESAURUS , array( $codeThesaurus , $libelle ) );
		}


		public static function getThesaurii()
		{
			$oThesaurusCollection = new ThesaurusCollection();
			$thesaurii            = tsDatabase::getRecords( self::SQL_THESAURII , array() );
			foreach( $thesaurii as $thesaurus )
			{
				$oThesaurusCollection[ ] = self::getThesaurus( $thesaurus )->getObject();
			}

			return $oThesaurusCollection->getCollection();
		}


		public static function getEntreesThesaurus( thesaurusModele $oThesaurus , $codeLangue )
		{
			if( self::isCodeLangueValide( $codeLangue ) === false )
			{
				throw new ApplicationException( "Le code langue n'est pas valide" );
			}

			$oEntreeThesaurusCollection = new entreeThesaurusCollection();
			if( tsDroits::isRoot() ) // toutes les langues
			{

				// Hook request refactor
				$sql = self::SQL_ENTREES_THESAURUS_ROOT_ALL_LNG;
				tsPlugins::registerVar( 'sqlRequest' , $sql );
				tsPlugins::callHook( 'thesaurusDb' , 'getEntreesThesaurusRoot' , 'requestRefactor' );

				$entreesThesaurus = tsDatabase::getRows( $sql , array( $oThesaurus->codeThesaurus ) );
			}
			else // uniquement la langue demandée
			{
				// Hook request refactor
				$sql = self::SQL_ENTREES_THESAURUS;
				tsPlugins::registerVar( 'sqlRequest' , $sql );
				tsPlugins::callHook( 'thesaurusDb' , 'getEntreesThesaurus' , 'requestRefactor' );

				$entreesThesaurus = tsDatabase::getRows( $sql , array( $oThesaurus->codeThesaurus , $codeLangue ) );
			}

			// eliminer les clés masquées
			$clesMasque = tsDatabase::getRecords( self::SQL_ENTREES_THESAURUS_MASQUE , array( $idGroupe = tsDroits::getGroupeUtilisateur() ) );

			foreach( $entreesThesaurus as &$entreeThesaurus )
			{
				foreach( $clesMasque as &$masque )
				{
					if( preg_match( '/' . $masque . '/' , $entreeThesaurus[ 'cle' ] ) )
					{
						continue 2;
					}
				}

				$oEntreeThesaurus = new entreeThesaurusModele();
				$oEntreeThesaurus->setCle( $entreeThesaurus[ 'cle' ] );
				$oEntreeThesaurus->setLibelle( $entreeThesaurus[ 'libelle' ] );
				$oEntreeThesaurus->setListe( $entreeThesaurus[ 'liste' ] );

				if( !empty( $norme ) )
				{
					$oEntreeThesaurus->setLibellesExternes( $entreeThesaurus[ 'libellesExternes' ] );
				}

				$oEntreeThesaurus->setLang( $entreeThesaurus[ 'lang' ] );
				$oEntreeThesaurusCollection[ ] = $oEntreeThesaurus->getObject();
			}

			return $oEntreeThesaurusCollection->getCollection();
		}




		public static function &getEntreesThesaurii( $codesThesaurii , $codeLangue )
		{
			$entreesThesaurii = array();
			foreach( $codesThesaurii as $codeThesaurus )
			{
				$oThesaurus       = thesaurusDb::getThesaurus( $codeThesaurus );
				$entreesThesaurus = thesaurusDb::getEntreesThesaurus( $oThesaurus , $codeLangue );
				$entreesThesaurii = array_merge( $entreesThesaurii , $entreesThesaurus );
			}

			return $entreesThesaurii;
		}




		public static function getEntreeThesaurus( $codeTif , $codeLangue )
		{
			if( self::isCodeLangueValide( $codeLangue ) === false )
			{
				throw new ApplicationException( "Le code langue n'est pas valide" );
			}

			// Hook request refactor
			$sql = self::SQL_ENTREE_THESAURUS;
			tsPlugins::registerVar( 'sqlRequest' , $sql );
			tsPlugins::callHook( 'thesaurusDb' , 'getEntreeThesaurus' , 'requestRefactor' );

			$result = tsDatabase::getObject( $sql , array( $codeTif , $codeLangue ) , DB_FAIL_ON_ERROR );

			return entreeThesaurusModele::getInstance( $result , 'entreeThesaurusModele' );
		}


		public static function addEntreeThesaurus( thesaurusModele $oThesaurus , $cleParente , $libelle , $codeLangue )
		{

			if( self::isCodeLangueValide( $codeLangue ) === false )
			{
				throw new ApplicationException( "Le code langue n'est pas valide" );
			}

			$liste = self::getListeFromCle( $cleParente );
			//$nextCode = tsDatabase::getRecord(self::SQL_NUMERO_TIF, array($oThesaurus -> codeThesaurus, $liste));
			$nextCode = tsDatabase::getRecord( self::SQL_NUMERO_TIF , array( $oThesaurus->codeThesaurus , $cleParente , $cleParente ) );
			if( is_null( $nextCode ) )
			{
				$nextCode = $oThesaurus->prefixe . '.' . $cleParente . '.01';
			}

			tsDatabase::insert( self::SQL_ADD_ENTREE_THESAURUS , array( $oThesaurus->codeThesaurus , $nextCode , $liste , $codeLangue , $libelle ) );

			$stemmer = new Stem();
			$stem    = $stemmer->stem( utf8_decode( urldecode($libelle)) , ' ' , $codeLangue );
			tsDatabase::insert( self::SQL_ADD_ENTREE_THESAURUS_STEM , array( $nextCode , $codeLangue , $stem ) );

			return $nextCode;
		}


		public static function deleteEntreeThesaurus( $codeTIF )
		{
			tsDatabase::query( self::SQL_DELETE_ENTREE_THESAURUS_STEM , array( $codeTIF ) );

			return tsDatabase::query( self::SQL_DELETE_ENTREE_THESAURUS , array( $codeTIF ) );
		}



		public static function setEntreeThesaurus( $codeTIF , $codeLangue , $libelle )
		{
			if( self::isCodeLangueValide( $codeLangue ) === false )
			{
				throw new ApplicationException( "Le code langue n'est pas valide" );
			}
			$codeThesaurus = self::getThesaurusByKey( $codeTIF );
			$liste         = self::getListeFromCle( $codeTIF , false );

			$stemmer = new Stem();
			$stem    = $stemmer->stem( utf8_decode( urldecode( $libelle ) ) , ' ' , $codeLangue );
			tsDatabase::insert( self::SQL_UPDATE_ENTREE_THESAURUS_STEM , array( $stem , $codeTIF , $codeLangue ) );

			return tsDatabase::query( self::SQL_SET_ENTREE_THESAURUS , array( $libelle , $codeTIF , $codeLangue , $codeThesaurus , $liste ) );
		}


		public static function translateEntreeThesaurus( $codeTIF , $codeLangue , $libelle )
		{
			if( self::isCodeLangueValide( $codeLangue ) === false )
			{
				throw new ApplicationException( "Le code langue n'est pas valide" );
			}
			$codeThesaurus = self::getThesaurusByKey( $codeTIF );
			$liste         = self::getListeFromCle( $codeTIF , false );

			/* // TODO: Laisser ce bout de code pour l'activer quand le stemmer prendra en charge plusieurs langues
			$stemmer = new Stem();
			$stem    = $stemmer->stem( $libelle , ' ' , $codeLangue );
			tsDatabase::insert( self::SQL_ADD_ENTREE_THESAURUS_STEM , array( $codeTIF , $codeLangue , $stem ) );
			*/

			return tsDatabase::query( self::SQL_SET_ENTREE_THESAURUS , array( $libelle , $codeTIF , $codeLangue , $codeThesaurus , $liste ) );
		}


		public static function getListeFromCle( $cle , $cleParente = true )
		{
			$liste = tsDatabase::getRecord( self::SQL_LISTE_FROM_CLE , array( $cle . ( $cleParente ? '.01' : '' ) ) );
			if( is_null( $liste ) )
			{
				throw new ApplicationException( "Le code TIF fourni n'est pas valide" );
			}

			return $liste;
		}


		public static function getValueByKey( $cle , $codeLangue = 'fr' )
		{
			if( self::isCodeLangueValide( $codeLangue ) === false )
			{
				throw new ApplicationException( "Le code langue n'est pas valide" );
			}
			$value = tsDatabase::getRecord( self::SQL_VALUE_BY_KEY , array( $cle , $codeLangue ) , DB_FAIL_ON_ERROR );

			if( is_null( $value ) )
			{
				throw new ApplicationException( "Le code TIF fourni n'est pas valide" );
			}

			return $value;
		}



		public static function getListeThesaurus( $liste , $cle , $pop = null )
		{
			$cle = str_replace( '.*' , '' , $cle );
			$cle = ( $cle == '' ? '.*' : "((99|[0-9]{3}).)?$cle(.[0-9]{2,3})+" );

			self::$pop = ( !is_null($pop) && $pop != '' ? explode( ',' , $pop ) : array() );

			$entrees = tsDatabase::getRows( self::SQL_LISTE_THESAURUS , array( $liste , $cle , 'MTH.NAT.TIFV30' ) , DB_FAIL_ON_ERROR );
			$entrees = array_filter( $entrees , array( 'self' , 'popListeThesaurus' ) );
			$entrees = array_values( $entrees );

			if( tsDroits::isRoot() === false )
			{
				$idGroupe     = tsDroits::getGroupeUtilisateur();
				$oGroupe      = groupeDb::getGroupe( $idGroupe );
				$oTerritoires = groupeDb::getGroupeTerritoires( $oGroupe );

				$thesaurii    = array();
				$thesauriiIds = array();
				foreach( $oTerritoires as $oTerritoire )
				{
					$oThesaurii = territoireDb::getThesaurusByTerritoire( $oTerritoire );
					foreach( $oThesaurii as $oThesaurus )
					{
						$thesaurii[ ]    = $oThesaurus->codeThesaurus;
						$thesauriiIds[ ] = $oThesaurus->idThesaurus;
					}
				}
				$thesaurii = array_unique( $thesaurii );

				foreach( $thesaurii as $thesaurus )
				{
					$entreesAjout = tsDatabase::getRows( self::SQL_LISTE_THESAURUS , array( $liste , $cle , $thesaurus ) , DB_FAIL_ON_ERROR );
					$entrees      = array_merge( $entrees , $entreesAjout );
				}


				// eliminer les clés masquées
				$entreesMasque = array();
				$clesMasque = tsDatabase::getRecords( self::SQL_ENTREES_THESAURUS_MASQUE , array( $thesauriiIds ) );
				if( count( $clesMasque ) > 0 )
				{
					foreach( $entrees as $k => $entreeThesaurus )
					{
						$delete = false;
						foreach( $clesMasque as &$masque )
						{
							if( $masque == $entreeThesaurus[ 'cle' ] )
							{
								// exclure cette entrée
								$delete = true;
								break;
							}
						}
						if( $delete === false ) // si l'entrée est à exclure
						{
							$entreesMasque[ ] = $entreeThesaurus;
						}
					}
					$entrees = $entreesMasque;
				}
			}

			return $entrees;
		}


		public static function getArbreThesaurus( $cle , $pop )
		{
			$cle = str_replace( '.*' , '' , $cle );

			if( $cle == '' )
			{
				throw new ApplicationException( "Code TIF invalide" );
			}

			self::$pop = ( $pop != '' ? explode( ',' , $pop ) : array() );

			// Hook request refactor
			$sql = self::SQL_ARBRE_THESAURUS;
			tsPlugins::registerVar( 'sqlRequest' , $sql );
			tsPlugins::callHook( 'thesaurusDb' , 'getArbreThesaurus' , 'requestRefactor' );

			$entrees = tsDatabase::getRows( $sql , array( $cle , 'MTH.NAT.TIFV30' ) , DB_FAIL_ON_ERROR );

			$entrees = array_filter( $entrees , array( 'self' , 'popListeThesaurus' ) );
			$entrees = array_values( $entrees );

			$entreesParCle = array(); // entrees de thesaurus sans doublon
			foreach( $entrees as &$e )
			{
				$entreesParCle[$e['cle']] = $e;
			}
			$entrees = array();

			if( tsDroits::isRoot() === false )
			{
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

				foreach( $thesaurii as $thesaurus )
				{
					// $entrees = array_merge( $entrees , tsDatabase::getRows( $sql , array( $cle , $thesaurus ) , DB_FAIL_ON_ERROR ) );
					$entreesAutre = tsDatabase::getRows( $sql , array( $cle , $thesaurus ) , DB_FAIL_ON_ERROR );
					foreach( $entreesAutre as &$e )
					{
						$entreesParCle[ $e[ 'cle' ] ] = $e;
					}
				}
			}

			foreach( $entreesParCle as &$e )
			{
				$entrees[] = $e;
			}

			self::$entrees = $entrees;

			self::$entreesByKey = $entreesParCle;
			self::$entreesKeys = array_keys( $entreesParCle);

			return self::getEntreesRecursive( $cle );
		}


		private static function getEntreesRecursive( $cle )
		{
			$arbre = array();
			foreach( self::$entreesKeys as $k )
			{
				if( preg_match( '/^([0-9]{2,3}.)?' . $cle . '.[0-9]{2,3}$/' , $k ) )
				{
					$entree = self::$entreesByKey[$k];
					$children = self::getEntreesRecursive( $entree['cle'] );

					if( count( $children ) > 0 )
					{
						$entree[ 'children' ] = $children;
					}

					$arbre[ ] = $entree;
				}
			}
			return $arbre;
		}


/*		private static function getEntreesRecursive( $cle )
		{
			$arbre = array();
			foreach( self::$entrees as $entree )
			{
				if( preg_match( '/^([0-9]{2,3}.)?' . $cle . '.[0-9]{2,3}$/' , $entree[ 'cle' ] ) )
				{
					$resultTmp = $entree;
					$children = self::getEntreesRecursive( $resultTmp[ 'cle' ] );

					if( count( $children ) > 0 )
					{
						$resultTmp[ 'children' ] = $children;
					}
					$arbre[ ] = $resultTmp;
				}
			}

			return $arbre;
		}*/


		private static function popListeThesaurus( $item )
		{
			return !in_array( $item[ 'cle' ] , self::$pop );
		}


		private static function getNextPrefixeThesaurus()
		{
			$prefixe = tsDatabase::getRecord( self::SQL_PREFIXE_THESAURUS , array() );

			return ( is_null( $prefixe ) ? tsConfig::get( 'TS_THESAURUS_PREFIXE' ) : intval( $prefixe ) + 1 );
		}


		private static function isCodeThesaurusValide( $codeThesaurus )
		{
			return ( preg_match( "/^MTH\.LOC\.[A-Z]{3}([A-Z0-9]{1,3})?$/i" , $codeThesaurus ) == 1 );
		}


		private static function isCodeLangueValide( $codeLangue )
		{
			// @TODO : à déplacer dans un futur proche
			return in_array( $codeLangue , array( 'fr' , 'en' , 'de' , 'es' , 'nl' , 'it' ) );
		}


		private static function getThesaurusByKey( $cle )
		{
			//Aller chercher le thésaurus dans la base
			$value = tsDatabase::getRecord( self::SQL_THESAURUS_BY_KEY , array( $cle ) , DB_FAIL_ON_ERROR );

			return $value;
		}




		public static function getEntreeThesaurusByLibelleViaStem( $stem , $thesaurii , $liste )
		{
			// liste des entrées de theso correspondant ET au stem ET aux theso accessibles ET à la liste
			$entrees = array();
			try
			{
				$entrees = tsDatabase::getRows( self::SQL_KEY_THESAURUS_BY_STEM , array( $stem , $thesaurii , $liste ) , DB_FAIL_ON_ERROR );
			}
			catch( Exception $e )
			{
				throw $e;
			}

			return $entrees;
		}

	}
