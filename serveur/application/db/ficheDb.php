<?php

	/**
	 * @version        0.4 alpha-test - 2013-06-03
	 * @package        Tourism System Server
	 * @copyright      Copyright (C) 2010 Raccourci Interactive
	 * @license        Qt Public License; see LICENSE.txt
	 * @author         Nicolas Marchand <nicolas.raccourci@gmail.com>
	 */

	require_once('application/db/champDb.php');
	require_once('application/db/ficheValidationDb.php');
	require_once('application/db/groupeDb.php');
	require_once('application/db/thesaurusDb.php');
	require_once('application/db/utilisateurDb.php');
	require_once('application/modele/bordereauModele.php');
	require_once('application/modele/communeModele.php');
	require_once('application/modele/ficheModele.php');
	require_once('application/modele/ficheSimpleModele.php');
	require_once('application/modele/versionModele.php');
	require_once('application/utils/tifTools.php');

	final class ficheDb
	{

		const SQL_FICHE_PUB2 = "SELECT f.idFiche, IF(g.idGroupe IS NULL,'N','Y') AS publication, f.raisonSociale, f.codeTIF, f.codeInsee, f.bordereau, f.gpsLat, f.gpsLng, f.idGroupe, f.referenceExterne, f.dateCreation FROM sitFiche f LEFT OUTER JOIN sitFichePublication g ON ( f.idFiche=g.idFiche AND g.idGroupe = %2\$d ) WHERE f.idFiche='%1\$d' ORDER BY publication DESC LIMIT 1";
		const SQL_FICHE_PUB3 = "SELECT f.idFiche, IF(g.idGroupe IS NULL,'N','Y') AS publication, f.raisonSociale, f.codeTIF, f.codeInsee, f.bordereau, f.gpsLat, f.gpsLng, f.idGroupe, f.referenceExterne, f.dateCreation FROM sitFiche f LEFT OUTER JOIN sitFichePublication g ON ( f.idFiche=g.idFiche AND g.idGroupe = %2\$d ) WHERE f.idFiche IN ('%1\$s') ORDER BY publication DESC";
		const SQL_FICHE_CODETIF = "SELECT idFiche FROM sitFiche WHERE codeTIF='%s'";
		const SQL_FICHE_REFEXTERNE = "SELECT idFiche FROM sitFiche WHERE referenceExterne='%s'";
		const SQL_FICHES2 = "SELECT f.idFiche, f.raisonSociale, f.codeTIF, f.codeInsee, f.bordereau, f.gpsLat, f.gpsLng, f.idGroupe, IF(g.idGroupe IS NULL,'N','Y') AS publication, f.referenceExterne, f.dateCreation FROM sitFiche f LEFT OUTER JOIN sitFichePublication g ON ( f.idFiche=g.idFiche AND g.idGroupe = %2\$d ) WHERE f.idFiche IN ('%1\$s')";
                const SQL_FICHES3 = "SELECT f.idFiche, f.raisonSociale, f.codeTIF, f.codeInsee, f.bordereau, f.gpsLat, f.gpsLng, f.idGroupe, IF(g.idGroupe IS NULL,'N','Y') AS publication, f.referenceExterne, f.dateCreation FROM sitFiche f LEFT OUTER JOIN sitFichePublication g ON ( f.idFiche=g.idFiche AND g.idGroupe = %2\$d ) WHERE f.idFiche NOT IN ('%3\$s') AND f.idFiche IN ('%1\$s')";
		const SQL_FICHES_FROM_CLES = 'SELECT DISTINCT idFiche FROM sitFicheChamps WHERE cle IN (\'%1$s\') AND idFiche IN (\'%2$s\')';
		const SQL_FICHES_BORDEREAU2 = "SELECT f.idFiche, f.raisonSociale, f.codeTIF, f.codeInsee, f.bordereau, f.gpsLat, f.gpsLng, f.idGroupe, IF(g.idGroupe IS NULL,'N','Y') AS publication, f.referenceExterne, f.dateCreation FROM sitFiche f LEFT OUTER JOIN sitFichePublication g ON ( f.idFiche=g.idFiche AND g.idGroupe = %2\$d ) WHERE f.idFiche IN ('%1\$s') AND  bordereau = '%3\$s'";
		const SQL_CREATE_FICHE = "INSERT INTO sitFiche (codeInsee, bordereau, dateCreation) VALUES ('%s', '%s', NOW())";
		const SQL_CREATE_FICHE_REFEXTERNE = "UPDATE sitFiche SET referenceExterne='%s' WHERE idFiche='%d'";
		const SQL_CREATE_FICHE_CODETIF = "UPDATE sitFiche SET codeTIF='%s' WHERE idFiche='%d'";
		const SQL_CREATE_FICHE_GROUPE = "UPDATE sitFiche SET idGroupe='%d' WHERE idFiche='%d'";
		const SQL_UPDATE_RAISON_SOCIALE = "UPDATE sitFiche SET raisonSociale='%s' WHERE idFiche='%d'";
		const SQL_UPDATE_GPS = "UPDATE sitFiche SET gpsLat='%s', gpsLng='%s' WHERE idFiche='%d'";
		const SQL_SET_PUBLICATION = "REPLACE INTO sitFichePublication (idFiche, idGroupe) VALUES ('%d', '%d')";
		const SQL_UNSET_PUBLICATION = "DELETE FROM sitFichePublication WHERE idFiche='%d' AND idGroupe='%d'";
		const SQL_ARCHIVE_FICHE = "INSERT INTO sitFicheSupprime (idFiche, codeTIF, codeInsee, bordereau, raisonSociale, idUtilisateur, referenceExterne, idFicheVersion, dateCreation, dateSuppression) VALUES ('%d', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', NOW())";
		const SQL_DELETE_FICHE = "DELETE FROM sitFiche WHERE idFiche='%d'";
		const SQL_VERSIONS_FICHE = "SELECT idFicheVersion, dateVersion, idUtilisateur, etat, dateValidation FROM sitFicheVersion WHERE idFiche = '%d'";

		//const SQL_FICHE_VERSION  = "SELECT idFicheVersion, dateVersion, etat, xmlTIF FROM sitFicheVersion WHERE idFiche='%d'";
		const SQL_FICHE_VERSION2 = "SELECT idFiche, idFicheVersion, dateVersion, etat FROM sitFicheVersion WHERE idFiche='%d'";
		const SQL_FICHE_VERSION_ETAT = " AND etat='%s'";
		const SQL_FICHE_VERSION_IDVERSION = " AND idFicheVersion='%d'";
		const SQL_FICHE_VERSION_LASTVERSION = " ORDER BY idFicheVersion DESC LIMIT 0,1";
		const SQL_UPDATE_FICHE_VERSION = "INSERT INTO sitFicheVersion (idFicheVersion, idFiche, dateVersion, etat, dateValidation) VALUES ('%d', '%d', NOW(), '%s', NOW())";
		const SQL_UPDATE_FICHE_UTILISATEUR = "UPDATE sitFicheVersion SET idUtilisateur='%d' WHERE idFiche='%d' AND idFicheVersion='%d'";
		const SQL_UPDATE_FICHE_NOMVERSION = "UPDATE sitFicheVersion SET nomVersion='%s' WHERE idFiche='%d' AND idFicheVersion='%d'";
		const SQL_UPDATE_FICHE_VERSIONPRINCIPALE = "UPDATE sitFicheVersion SET versionPrincipale='%s' WHERE idFiche='%d' AND idFicheVersion='%d'";
		const SQL_DELETE_FICHE_VERSION = "DELETE FROM sitFicheVersion WHERE idFiche='%d' AND idFicheVersion = '%d'";

		// @todo : Récupération des champs spécifiques via droits champs sur bordereau
		const SQL_FICHE_OBJET = "SELECT * FROM sitChamp WHERE FIND_IN_SET('%s', bordereau)>0"; // AND xpath NOT LIKE '%%tif:ChampsSpecifiques%%'";
		const SQL_INSERT_FICHE_TIF_LINK = 'INSERT IGNORE INTO sitFicheChamps VALUES (\'%1$s\',\'%2$s\')';
		const SQL_DELETE_FICHE_LINK = "DELETE FROM sitFicheChamps WHERE idFiche = '%s'";

		public static function createFiche(bordereauModele $oBordereau, communeModele $oCommune, $referenceExterne = null)
		{
			$idFiche = tsDatabase::insert(self::SQL_CREATE_FICHE, array($oCommune->codeInsee, $oBordereau->bordereau));

			if (is_null($referenceExterne) === false)
			{
				tsDatabase::query(self::SQL_CREATE_FICHE_REFEXTERNE, array($referenceExterne, $idFiche));
			}

			$codeTIF = self::getCodeTif($idFiche, $oBordereau->bordereau, $oCommune);
			tsDatabase::query(self::SQL_CREATE_FICHE_CODETIF, array($codeTIF, $idFiche));

			if (tsDroits::isRoot() === false)
			{
				tsDatabase::query(self::SQL_CREATE_FICHE_GROUPE, array(tsDroits::getGroupeUtilisateur(), $idFiche));
			}

			// Création du xml
			$strXml = file_get_contents(tsConfig::get('TS_PATH_EMPTYXML'));

			$strXml = str_replace('{identifier}', $codeTIF, $strXml);
			$strXml = str_replace('{created}', date("Y-m-d"), $strXml);
			$strXml = str_replace('{modified}', date("Y-m-d"), $strXml);
			$strXml = str_replace('{code_postal}', $oCommune->codePostal, $strXml);
			$strXml = str_replace('{code_commune}', $oCommune->codeInsee, $strXml);
			$strXml = str_replace('{commune}', $oCommune->libelle, $strXml);

			$infosBordereau = tifTools::getInfosBordereau($oBordereau->bordereau);
			$strXml = str_replace('{cleClassification}', $infosBordereau['cle'], $strXml);
			$strXml = str_replace('{libelleClassification}', $infosBordereau['libelle'], $strXml);

			self::createFicheVersion($idFiche, $strXml);

			return $idFiche;
		}

		private static function getCodeTif($idFiche, $bordereau, $oCommune)
		{
			if ($oCommune->codePays == 'FR')
			{
				$codeTif = (strtoupper($bordereau) . tifTools::getCodeRegionByCodeInsee($oCommune->codeInsee) .
					'0' . mb_substr($oCommune->codeInsee, 0, 2, 'UTF-8') . tsConfig::get('TS_NUMERO_BASE') .
					str_repeat('0', 6 - strlen($idFiche)) . $idFiche);
			}
			else
			{
				$codeTif = (strtoupper($bordereau) . $oCommune->codePays . tsConfig::get('TS_NUMERO_BASE') .
					str_repeat('0', 10 - strlen($idFiche)) . $idFiche);
			}

			return $codeTif;
		}

		public static function createFicheVersion($idFiche, $xml)
		{
			$lastVersion = self::getFicheVersion($idFiche);
			$newIdVersion = (is_null($lastVersion) === false ? $lastVersion['idFicheVersion'] + 1 : 1);

			// Ne pas sauvegarder si l'ancienne version est la même
			if ($newIdVersion > 2)
			{
				$oldXml = str_replace(">\r\n", '>', $lastVersion['xmlTIF']);
				$newXml = str_replace(">\r\n", '>', $xml);

				if ($oldXml == $newXml)
				{
					return true;
				}
			}

			$path = self::getPathByIdFiche($idFiche);

			$xmlFile = $path . $idFiche . '-' . $newIdVersion . '.xml';
			$result = file_put_contents($xmlFile, $xml);
			if ($result === false)
			{
				throw new Exception("Impossible de créer la nouvelle version de la fiche : $idFiche");
			}

			$xmlFile = $path . $idFiche . '.xml';
			$result = file_put_contents($xmlFile, $xml);
			if ($result === false)
			{
				throw new Exception("Impossible de créer la nouvelle version de la fiche : $idFiche");
			}

			$result = tsDatabase::query(self::SQL_UPDATE_FICHE_VERSION, array($newIdVersion, $idFiche, 'accepte'));

			if (tsDroits::isRoot() === false)
			{
				tsDatabase::query(self::SQL_UPDATE_FICHE_UTILISATEUR, array(tsDroits::getIdUtilisateur(), $idFiche, $newIdVersion));
			}

			self::updateSitFiche($idFiche);

			return $result;
		}

		private static function updateSitFiche($idFiche)
		{
			$version = self::getFicheVersion($idFiche);
			$oFicheSimple = ficheSimpleModele::loadByXml($version['xmlTIF']);

			if ($oFicheSimple->raisonSociale)
			{
				tsDatabase::query(self::SQL_UPDATE_RAISON_SOCIALE, array($oFicheSimple->raisonSociale, $idFiche));
			}

			if ($oFicheSimple->gpsLat && $oFicheSimple->gpsLng)
			{
				tsDatabase::query(self::SQL_UPDATE_GPS, array($oFicheSimple->gpsLat, $oFicheSimple->gpsLng, $idFiche));
			}
			self::processTIFCodes($version);
		}

		public static function &getFichesIds()
		{
			return tsDroits::getFichesAdministrables();
		}

		public static function &getFichesWithout(&$idsToExclude, &$idsUniverse = null)
		{
			$oFicheSimpleCollection = new ficheSimpleCollection();
			$idsFichesAdministrables = ($idsUniverse !== null ? $idsUniverse : tsDroits::getFichesAdministrables());
			$fiches = array();
			if (is_array($idsToExclude) && count($idsToExclude) > 0)
			{
				$fiches = tsDatabase::getObjects(self::SQL_FICHES3, array($idsFichesAdministrables, tsDroits::getGroupeUtilisateur(), $idsToExclude));
			}
			else
			{
				$fiches = tsDatabase::getObjects(self::SQL_FICHES2, array($idsFichesAdministrables, tsDroits::getGroupeUtilisateur()));
			}

			foreach ($fiches as $fiche)
			{
				$oFicheSimpleCollection[] = ficheSimpleModele::getInstance($fiche, 'ficheSimpleModele');
			}

			return $oFicheSimpleCollection->getCollection();
		}

		public static function &getFichesCles($cles, $fichesAdministrables)
		{
			try
			{
				if (count($cles) == 0)
				{
					$cles = '*';
				}

				return tsDatabase::getRecords(self::SQL_FICHES_FROM_CLES, array($cles, $fichesAdministrables));
			}
			catch (Exception $ex)
			{
				return array();
			}
		}

		public static function &getFichesBordereau($bordereau = null)
		{
			if ($bordereau == null)
			{
				return ficheDb::getFiches();
			}
			$oFicheSimpleCollection = new ficheSimpleCollection();
			$fiches = tsDatabase::getObjects(self::SQL_FICHES_BORDEREAU2, array(tsDroits::getFichesAdministrables(), tsDroits::getGroupeUtilisateur(), $bordereau));
			foreach ($fiches as $fiche)
			{
				$oFicheSimpleCollection[] = ficheSimpleModele::getInstance($fiche, 'ficheSimpleModele');
			}

			return $oFicheSimpleCollection->getCollection();
		}
                


                public static function &listFiches($parameters = array())
                {
                        $fichesAdministrables = tsDroits::getFichesAdministrables();
                        $groupUtilisateur = tsDroits::getGroupeUtilisateur();
                                
                        $where = '';
                    
                        //TODO sql injection
                        if($parameters['bordereau'])
                        {
                            $where .= ' AND f.bordereau = "'.$parameters['bordereau'].'"';
                        }
                        
                        if($parameters['filtreCommune'])
                        {
                            $where .= ' AND f.codeInsee IN ("'.implode('","',$parameters['filtreCommune']).'")';
                        }
                        
                        //query [idFiche, codeTif, referenceExterne, raisonSociale]
                        if($parameters['query'])
                        {
                            $where .= ' AND (
                                f.idFiche LIKE "%%'.$parameters['query'].'%%"
                                OR f.codeTIF LIKE "%%'.$parameters['query'].'%%"
                                OR f.referenceExterne LIKE "%%'.$parameters['query'].'%%"
                                OR f.raisonSociale LIKE "%%'.$parameters['query'].'%%"
                             )';
                        }
                        
                        //filters
                        //throw new Exception(var_export($parameters['filters'],true).':'.var_export($parameters['filtersType'],true));
                        if($parameters['filters'] && $parameters['filtersType'] == 'or')
                        {
                            $where .= ' AND sitFicheChamps.cle IN ("'.implode('","',$parameters['filters']).'")';
                        }

                        if($parameters['filters'] && ( $parameters['filtersType'] == 'and' || $parameters['filtersType'] == false))
                        {
                            foreach($parameters['filters'] as $filter)
                            {
                                $where .= ' AND sitFicheChamps.cle = "'.$filter.'"';
                            }
                            
                        }
                        
                        
                        //gridfilters
                        if($parameters['gridfilters'])
                        {
                            foreach($parameters['gridfilters'] as $gridFilter)
                            {
                                if($gridFilter->field == 'commune')
                                {
                                    $where .= ' AND sitCommune.libelle LIKE  "%%'.$gridFilter->value.'%%"';
                                }
                                else if($gridFilter->field == 'nomGroupe')
                                {
                                    $where .= ' AND sitGroupe.nomGroupe LIKE  "%%'.$gridFilter->value.'%%"';
                                }
                                else if($gridFilter->field == 'publication')
                                {
                                    if(is_array($gridFilter->value) && sizeof($gridFilter->value) == 1 && $gridFilter->value[0] == 'Y'){
                                        $where .= ' AND g.idGroupe IS NOT NULL';
                                    }else if(is_array($gridFilter->value) && sizeof($gridFilter->value) == 1 && $gridFilter->value[0] == 'N'){
                                        $where .= ' AND g.idGroupe IS NULL';
                                    }
                                }
                                else if($gridFilter->field == 'dateCreation')
                                {
                                    if($gridFilter->comparison == 'eq')
                                    {
                                        $where .= ' AND f.dateCreation LIKE  "'.$gridFilter->value.'%%"';
                                    }
                                    else if($gridFilter->comparison == 'lt')
                                    {
                                        $where .= ' AND f.dateCreation <=  "'.$gridFilter->value.'"';
                                    }       
                                    else if($gridFilter->comparison == 'gt')
                                    {
                                        $where .= ' AND f.dateCreation >=  "'.$gridFilter->value.'"';
                                    }       
                                    
                                }

                                else
                                    //codeTIF
                                    //raisonSociale
                                    //codeInsee

                                {
                                    $where .= ' AND f.'.$gridFilter->field.' LIKE  "%%'.$gridFilter->value.'%%"';
                                }
                                
                                
                                
                            }
                        }
                        

                        $sqlCount = "SELECT 
                                        count(distinct f.idFiche) as nombre
                                    FROM sitFiche f
                                    LEFT OUTER JOIN sitFichePublication g ON ( f.idFiche=g.idFiche AND g.idGroupe = %2\$d ) 
                                    LEFT JOIN sitGroupe ON (f.idGroupe = sitGroupe.idGroupe)
                                    LEFT JOIN sitCommune ON (f.codeInsee = sitCommune.codeInsee)
                                    LEFT JOIN sitFicheChamps ON (f.idFiche = sitFicheChamps.idFiche)
                                    
                                    WHERE f.idFiche IN ('%1\$s') 
                                    ".$where."
                                ";
                        
                        
                        $sql = "SELECT 
                                        f.idFiche, 
                                        f.raisonSociale, 
                                        f.codeTIF, 
                                        f.codeInsee, 
                                        f.bordereau, 
                                        f.gpsLat, 
                                        f.gpsLng, 
                                        f.idGroupe, 
                                        IF(g.idGroupe IS NULL,'N','Y') AS publication, 
                                        f.referenceExterne, 
                                        f.dateCreation,
                                        sitGroupe.nomGroupe as nomGroupe,
                                        sitCommune.libelle as commune
                                    FROM sitFiche f
                                    LEFT OUTER JOIN sitFichePublication g ON ( f.idFiche=g.idFiche AND g.idGroupe = %2\$d ) 
                                    LEFT JOIN sitGroupe ON (f.idGroupe = sitGroupe.idGroupe)
                                    LEFT JOIN sitCommune ON (f.codeInsee = sitCommune.codeInsee)
                                    LEFT JOIN sitFicheChamps ON (f.idFiche = sitFicheChamps.idFiche)
                                    
                                    WHERE f.idFiche IN ('%1\$s') 
                                    ".$where."
                                    GROUP BY f.idFiche
                                    ORDER BY %5\$s %6\$s
                                    LIMIT %3\$d, %4\$d";
                        
                       
                         
                        
                        $fichesCount = tsDatabase::getObject($sqlCount, array(
                            $fichesAdministrables,
                            $groupUtilisateur,
                        ));
                        

                        
                        
                        $fiches = tsDatabase::getObjects($sql, array(
                            $fichesAdministrables,
                            $groupUtilisateur,
                            $parameters['start'],
                            $parameters['limit'],
                            $parameters['sort'],
                            $parameters['dir']
                        ));

                        
			$oFicheSimpleCollection = new ficheSimpleCollection();
                        foreach ($fiches as $fiche)
			{
				$oFicheSimpleCollection[] = ficheSimpleModele::getInstance($fiche, 'ficheSimpleModele');
			}
                        
			return array(
                            'dataCount' => $fichesCount->nombre,
                            'dataRoot' => $oFicheSimpleCollection->getCollection()
                        );
                }

                
                
                public static function &getFiches()
		{
			$oFicheSimpleCollection = new ficheSimpleCollection();
			$fiches = tsDatabase::getObjects(self::SQL_FICHES2, array(tsDroits::getFichesAdministrables(), tsDroits::getGroupeUtilisateur()));
			foreach ($fiches as $fiche)
			{
				$oFicheSimpleCollection[] = ficheSimpleModele::getInstance($fiche, 'ficheSimpleModele');
			}

			return $oFicheSimpleCollection->getCollection();
		}

		public static function &getFicheByIdFiche($idFiche, $idFicheVersion = null, $simple = false)
		{
			$fiche = tsDatabase::getObject(self::SQL_FICHE_PUB2, array($idFiche, tsDroits::getGroupeUtilisateur()), DB_FAIL_ON_ERROR);
			$oFiche = ficheModele::getInstance($fiche, 'ficheModele');
			$version = self::getFicheVersion($idFiche, $idFicheVersion, $simple);

			$oFiche->setIdVersion($version['idFicheVersion']);
			$oFiche->setDateVersion($version['dateVersion']);
			$oFiche->setEtatVersion($version['etat']);
			if ($simple !== true)
			{
				$oFiche->setXml($version['xmlTIF']);
			}

			// @hook getFiche
			// tsHook::hookObject('getFiche', $oFiche);

			$retour = $oFiche->getObject();

			return $retour;
		}

		public static function &getFicheCompleteByFicheSimple($oFiche)
		{
			$version = self::getFicheVersion($oFiche->idFiche);

			$oFiche->setIdVersion($version['idFicheVersion']);
			$oFiche->setDateVersion($version['dateVersion']);
			$oFiche->setEtatVersion($version['etat']);
			$oFiche->setXml($version['xmlTIF']);
			$retour = $oFiche->getObject();

			return $retour;
		}

		public static function &getFichesByIdsFiches($idsFiches, $simple = false)
		{
			$fiches = tsDatabase::getObjects(self::SQL_FICHE_PUB3, array($idsFiches, tsDroits::getGroupeUtilisateur()), DB_FAIL_ON_ERROR);

			$retour = array();
			foreach ($fiches as $fiche)
			{
				$oFiche = ficheModele::getInstance($fiche, 'ficheModele');
				$version = self::getFicheVersion($fiche->idFiche);
				$oFiche->setIdVersion($version['idFicheVersion']);
				$oFiche->setDateVersion($version['dateVersion']);
				$oFiche->setEtatVersion($version['etat']);
				if ($simple !== true)
				{
					$oFiche->setXml($version['xmlTIF']);
				}
				$retour[] = $oFiche->getObject();
			}

			return $retour;
		}

		public static function &getFicheSimpleByIdFiche($idFiche)
		{
			$fiche = tsDatabase::getObject(self::SQL_FICHE_PUB2, array($idFiche, tsDroits::getGroupeUtilisateur()), DB_FAIL_ON_ERROR);
			$oFiche = ficheSimpleModele::getInstance($fiche, 'ficheSimpleModele');

			$version = self::getFicheVersion($idFiche);
			$oFiche->setIdVersion($version['idFicheVersion']);
			$oFiche->setDateVersion($version['dateVersion']);
			$oFiche->setEtatVersion($version['etat']);

			return $oFiche->getObject();
		}

		public static function &getFicheVersion($idFiche, $idFicheVersion = null, $simple = false)
		{
			$sqlFicheVersion = self::SQL_FICHE_VERSION2 . (is_null($idFicheVersion) ? self::SQL_FICHE_VERSION_LASTVERSION : self::SQL_FICHE_VERSION_IDVERSION);
			$version = tsDatabase::getRow($sqlFicheVersion, array($idFiche, $idFicheVersion));

			if (is_null($version) === false && !$simple)
			{
				$path = self::getPathByIdFiche($idFiche);
				$xmlFile = $path . $idFiche . '-' . $version['idFicheVersion'] . '.xml';

				if (file_exists($xmlFile) === false || filesize($xmlFile) === 0)
				{
					throw new ApplicationException("Impossible de charger le xml de la version " . $version['idFicheVersion'] . " : $idFiche");
				}

				$version['xmlTIF'] = file_get_contents($xmlFile);
			}

			return $version;
		}

		private function getPathByIdFiche($idFiche)
		{
			while (strlen($idFiche) < (tsConfig::get('TS_SUBFOLDERS_DEPTH_XML') * 2))
			{
				$idFiche = '0' . $idFiche;
			}

			$pathXml = tsConfig::get('TS_PATH_XML');
			$subFolders = str_split($idFiche, 2);
			foreach ($subFolders as $subFolder)
			{
				$pathXml .= $subFolder . '/';
				if (is_dir($pathXml) === false)
				{
					mkdir($pathXml);
				}
			}

			return $pathXml;
		}

		public static function &getFiche(ficheModele $oFiche, $droitFiche)
		{
			$champs = tsDatabase::getRows(self::SQL_FICHE_OBJET, array($oFiche->bordereau));

			$oFiche->editable = array();
			$oFiche->readable = array();
			$oFiche->validation = array();

			$champsValidation = ficheValidationDb::getChampsFicheAValider($oFiche);

			foreach ($champs as $champ)
			{
				$oChamp = champDb::getChamp($champ['idChamp']);
				$droitChamp = tsDroits::getDroitFicheChamp($oFiche, $oChamp, $droitFiche);

				if ($droitChamp & DROIT_MODIFICATION)
				{
					$oFiche->editable[$champ['identifiant']] = champDb::getFicheValueChamp($oFiche, $oChamp);
				}
				elseif ($droitChamp & DROIT_VISUALISATION)
				{
					$oFiche->readable[$champ['identifiant']] = champDb::getFicheValueChamp($oFiche, $oChamp);
				}
				if ($droitChamp & DROIT_VALIDATION)
				{
					$oChampFicheValidation = ficheValidationDb::getChampFicheAValider($oFiche, $oChamp);
					if ($oChampFicheValidation !== false)
					{
						$oFiche->validation[$champ['identifiant']] = $oChampFicheValidation->valeur;
					}
				}
			}

			return $oFiche;
		}

		public static function getIdFicheByCodeTIF($codeTIF)
		{
			return tsDatabase::getRecord(self::SQL_FICHE_CODETIF, array($codeTIF));
		}

		public static function getIdFicheByRefExterne($refExterne)
		{
			return tsDatabase::getRecord(self::SQL_FICHE_REFEXTERNE, array($refExterne));
		}

		public static function sauvegardeFicheBrouillon($oFiche, $stdFiche)
		{
			return self::sauvegardeFiche($oFiche, $stdFiche, true);
		}

		public static function sauvegardeFiche(ficheModele $oFiche, $stdFiche, $droitFiche, $champsValide = array(), $champsRefuse = array())
		{
			$champs   = tsDatabase::getRows( self::SQL_FICHE_OBJET, array( $oFiche->bordereau ) );

			$champsAValider = array();
			foreach ($champs as $champ)
			{
				if (isset($stdFiche[$champ['identifiant']]))
				{
					$oChamp = champDb::getChamp($champ['idChamp']);

					$droitChamp = tsDroits::getDroitFicheChamp($oFiche, $oChamp, $droitFiche);
					if ($droitChamp & DROIT_MODIFICATION)
					{
						$oldValue = champDb::getFicheValueChamp($oFiche, $oChamp);
						$newValue = $stdFiche[$champ['identifiant']];
						// VALIDATION
						if (in_array($oChamp->identifiant, $champsValide) && ($droitChamp & DROIT_VALIDATION))
						{
							$oChampFicheValidation = ficheValidationDb::getChampFicheAValider($oFiche, $oChamp);
							if ($oChampFicheValidation !== false)
							{
								$newValue = (is_array($oChamp->champs) && count($oChamp->champs) > 0)
									? json_encode($oChampFicheValidation->valeur)
									: $oChampFicheValidation->valeur;
								ficheValidationDb::accepteChampFiche($oChampFicheValidation);
							}
						}
						if (in_array($oChamp->identifiant, $champsRefuse) && ($droitChamp & DROIT_VALIDATION))
						{
							$oChampFicheValidation = ficheValidationDb::getChampFicheAValider($oFiche, $oChamp);
							if ($oChampFicheValidation !== false)
							{
								ficheValidationDb::refuseChampFiche($oChampFicheValidation);
							}
						}

						// Noeud complexe
						if (is_array($oChamp->champs) && count($oChamp->champs) > 0)
						{
							$oldValue = storePrepare($oldValue, $oChamp->identifiant);

							if (is_array($newValue) === false)
							{
								$newValue = json_decode($newValue, true);
							}
							$newValue = storePrepare($newValue, $oChamp->identifiant);

							$toAdd = $newValue;
							$toDelete = $oldValue;

							storeCompare($toDelete, $toAdd);

							if (count($toDelete) > 0 || count($toAdd) > 0)
							{
								// VALIDATION
								if (!($droitChamp & DROIT_VALIDATION))
								{
									ficheValidationDb::setChampFicheAValider($oFiche, $oChamp, $newValue);
									$champsAValider[] = $oChamp->libelle;
									continue;
								}

								champDb::setFicheValueChamp($oFiche, $oChamp, $newValue);
							}
						}
						// Noeud simple
						else
						{
							if ($oldValue != $newValue)
							{
								// VALIDATION
								if (!($droitChamp & DROIT_VALIDATION))
								{
									ficheValidationDb::setChampFicheAValider($oFiche, $oChamp, $newValue);
									$champsAValider[] = $oChamp->libelle;
									continue;
								}

								champDb::setFicheValueChamp($oFiche, $oChamp, $newValue);
							}
						}
					}

				}

			}

			$xml = tsXml::getXmlFiche($oFiche);

			return self::createFicheVersion($oFiche->idFiche, $xml);
		}

		public static function setPublicationFiche($oFiche, $publication)
		{
			$sql = ($publication === true ? self::SQL_SET_PUBLICATION : self::SQL_UNSET_PUBLICATION);

			return tsDatabase::query($sql, array($oFiche->idFiche, tsDroits::getGroupeUtilisateur()));
		}

		public static function deleteFiche($oFiche)
		{
			$path = self::getPathByIdFiche($oFiche->idFiche);
			$pathArchives = tsConfig::get('TS_PATH_ARCHIVES_XML');

			// Archivage
			$lastVersion = self::getFicheVersion($oFiche->idFiche);
			$result = copy($path . $oFiche->idFiche . '-' . $lastVersion['idFicheVersion'] . '.xml', $pathArchives . $oFiche->idFiche . '.xml');
			if ($result === false)
			{
				throw new Exception("Impossible d'archiver la fiche : " . $oFiche->idFiche);
			}
			tsDatabase::query(self::SQL_ARCHIVE_FICHE, array($oFiche->idFiche, $oFiche->codeTIF, $oFiche->codeInsee, $oFiche->bordereau, $oFiche->raisonSociale,
				tsDroits::getIdUtilisateur(), $oFiche->referenceExterne, $lastVersion['idFicheVersion'], $oFiche->dateCreation));

			$dir = opendir($path);
			if ($dir !== false)
			{
				while ($entry = readdir($dir))
				{
					if (!is_dir($path . $entry) && $entry != '.' && $entry != '..')
					{
						unlink($path . $entry);
					}
				}
				closedir($dir);
			}
			rmdir($path);

			return tsDatabase::query(self::SQL_DELETE_FICHE, array($oFiche->idFiche));
		}

		public static function &getFicheVersions(ficheModele $oFiche)
		{
			$oVersionCollection = new versionCollection();
			$versions = tsDatabase::getRows(self::SQL_VERSIONS_FICHE, array($oFiche->idFiche));
			foreach ($versions as $version)
			{
				$oVersion = new versionModele();
				$oVersion->setIdFicheVersion($version['idFicheVersion']);
				$oVersion->setIdFiche($oFiche->idFiche);
				$oVersion->setDateVersion($version['dateVersion']);
				$oVersion->setIdUtilisateur($version['idUtilisateur']);
				$oVersion->setEtat($version['etat']);
				$oVersion->setDateValidation($version['dateValidation']);
				$oVersionCollection[] = $oVersion->getObject();
			}

			return $oVersionCollection->getCollection();
		}

		public static function deleteFicheVersion($oFiche, $idFicheVersion = null)
		{
			$path = self::getPathByIdFiche($oFiche->idFiche);

			if (is_null($idFicheVersion))
			{
				$lastVersion = self::getFicheVersion($oFiche->idFiche);
				$idFicheVersion = $lastVersion['idFicheVersion'];
			}

			if ($idFicheVersion == 1)
			{
				throw new Exception("La première version d'une fiche ne peut pas être supprimée.");
			}

			$result = unlink($path . $oFiche->idFiche . '-' . $idFicheVersion . '.xml');
			if ($result === false)
			{
				throw new Exception("Impossible de supprimer la version $idFicheVersion de la fiche : " . $oFiche->idFiche);
			}

			$result = tsDatabase::query(self::SQL_DELETE_FICHE_VERSION, array($oFiche->idFiche, $idFicheVersion));

			$lastVersion = self::getFicheVersion($oFiche->idFiche);

			$result = file_put_contents($path . $oFiche->idFiche . '.xml', $lastVersion['xmlTIF']);
			if ($result === false)
			{
				throw new Exception("Impossible de mettre à jour la version de la fiche : " . $oFiche->idFiche);
			}

			self::updateSitFiche($oFiche->idFiche);
		}

		public static function processTIFCodesALL()
		{
			$fiches = self::getFiches();
			foreach ($fiches as $fiche)
			{
				$version = self::getFicheVersion($fiche->idFiche);
				self::processTIFCodes($version);
			}
		}

		public static function processTIFCodes($ficheVersion)
		{
			// nettoyage de la map fiche-TIF
			tsDatabase::query(self::SQL_DELETE_FICHE_LINK, array($ficheVersion['idFiche']));

			// recuperation des codes TIF de la fiche
			$oDoc = new DOMDocument();
			$oDoc->loadXML($ficheVersion['xmlTIF']);
			$xpath = new DOMXPath($oDoc);
			$nodeList = $xpath->query("//*[@type or @code]/@*[name()='type' or name()='code']");

			// s'il y a des codes TIF
			if ($nodeList->length > 0)
			{
				// valeur du code
				$codes = array();
				foreach ($nodeList as $node)
				{
					$codes[] = $node->nodeValue; // $node->item(0)->
				}

				// ne garder qu'une occurrence de chaque TIF
				$codes = array_unique($codes);

				//				$listeCodes = array();
				foreach ($codes as $code)
				{
					$codeOK = false;

					if (!empty($code) && preg_match("!^[0-9]{2,3}\.!", $code)) // debut de code TIF ou DMS
					{
						try // @TODO faire un test pour ne prendre que les codes de + de 5 octets (?)
						{
							// récupérer l'entrée theso correspondant au code TIF
							// renvoie une exception en cas d'échec (entrée non trouvée)
							$entree = thesaurusDb::getEntreeThesaurus($code, 'fr');
							// si le code est dans une liste
							if($entree->liste != null)
							{
								$codeOK = true;
							}
						}
						catch (Exception $e)
						{
							if( preg_match( "!^[0-9]{2}\.[0-9]{3,4}$!" , $code ) ) // code DMS
							{
								$codeOK = true;
							}
						}
					}

					if($codeOK===true)
					{
						self::addFicheCleLink( $ficheVersion[ 'idFiche' ] , $code );
					}
				}
			}
		}

		public static function addFicheCleLink($idFiche, $code)
		{
			tsDatabase::query(self::SQL_INSERT_FICHE_TIF_LINK, array($idFiche, $code));
		}

		public static function isXMLValide($xml)
		{
			// @todo
		}

		private static function getFicheVersionByEtat($idFiche, $etat = 'accepte')
		{
			$version = tsDatabase::getRow(self::SQL_FICHE_VERSION . self::SQL_FICHE_VERSION_ETAT . self::SQL_FICHE_VERSION_LASTVERSION, array($idFiche, $etat));

			return self::getFicheVersion($idFiche, $version['idFicheVersion']);
		}
	}
