<?php

	/**
	 * @version        0.4 alpha-test - 2013-06-03
	 * @package        Tourism System Server
	 * @copyright    Copyright (C) 2010 Raccourci Interactive
	 * @license        Qt Public License; see LICENSE.txt
	 * @author        Nicolas Marchand <nicolas.raccourci@gmail.com>
	 */

	require_once('application/modele/bordereauModele.php');
	require_once('application/modele/champModele.php');

	final class champDb
	{
		const SQL_CHAMP = "SELECT idChamp, identifiant, libelle, stockage, xPath, scope, versioning, plugin, bordereau, liste, cle FROM sitChamp WHERE idChamp='%d'";
		const SQL_CHAMP_IDENTIFIANT = "SELECT idChamp FROM sitChamp WHERE identifiant='%s' AND idChampParent IS NULL";
		const SQL_CHAMP_XPATH = "SELECT idChamp FROM sitChamp WHERE xpath='%s' AND idChampParent IS NULL";
		const SQL_CHAMP_XPATH_MULTI = "SELECT idChamp FROM sitChamp WHERE xpath='%s'";
		const SQL_CHAMP_ENFANTS = "SELECT idChamp FROM sitChamp WHERE idChampParent='%d'";
		const SQL_CREATE_CHAMP = "INSERT INTO sitChamp (identifiant, libelle, xPath) VALUES ('%s', '%s', '%s')";
		const SQL_UPDATE_CHAMP = "UPDATE sitChamp SET identifiant='%s', libelle='%s', xPath='%s' WHERE idChamp='%d'";
		const SQL_UPDATE_CHAMP_NULL = "UPDATE sitChamp SET %s=NULL WHERE idChamp='%d'";
		const SQL_UPDATE_CHAMP_VALUE = "UPDATE sitChamp SET %s='%s' WHERE idChamp='%d'";
		const SQL_CHAMPS = "SELECT idChamp FROM sitChamp WHERE idChampParent IS NULL";
		const SQL_CHAMPS_BORDEREAU = " AND (FIND_IN_SET('%s', bordereau)>0 OR bordereau IS NULL)";
		const SQL_DELETE_CHAMP = "DELETE FROM sitChamp WHERE idChamp='%d'";

		const SQL_GET_VALUE_CHAMP = "SELECT valeur FROM sitFicheValeurChamp WHERE idFiche='%d' AND idChamp='%d' AND idGroupe='%d' ORDER BY idFicheVersion DESC LIMIT 0,1";
		const SQL_SET_VALUE_CHAMP = "REPLACE INTO sitFicheValeurChamp (idFiche, idFicheVersion, idChamp, idGroupe, valeur) VALUES ('%d', '%d', '%d', '%d', '%s')";

		private static $domFiches = array();

		// Lecture
		public static function getFicheValueChamp($oFiche, $oChamp)
		{
			$methodName = 'get' . ucfirst($oChamp -> stockage) . 'Value';
			return self::$methodName($oFiche, $oChamp);
		}

		private static function getXmlValue($oFiche, $oChamp)
		{
			return tsXml::getValueChamp($oFiche, $oChamp);
		}

		private static function getDbValue($oFiche, $oChamp)
		{
			$idGroupe = ($oChamp -> scope == 'groupe' ? tsDroits::getGroupeUtilisateur() : 0);
			$valeur = tsDatabase::getRecord(self::SQL_GET_VALUE_CHAMP, array($oFiche -> idFiche, $oChamp -> idChamp, $idGroupe));
			return $valeur !== false ? $valeur : '';
		}

		private static function getPluginValue($oFiche, $oChamp)
		{
			$value = '';
			
			tsPlugins::registerVar('oFiche', $oFiche);
			tsPlugins::registerVar('oChamp', $oChamp);
			tsPlugins::registerVar('value', $value);
			tsPlugins::callHook('champDb', 'getFicheValueChamp', $oChamp -> identifiant);
			
			return $value;
		}

		// Ecriture
		public static function setFicheValueChamp($oFiche, $oChamp, $value)
		{
			$methodName = 'set' . ucfirst($oChamp -> stockage) . 'Value';
			self::$methodName($oFiche, $oChamp, $value);
		}

		private static function setXmlValue($oFiche, $oChamp, $value)
		{
			tsXml::setValueChamp($oFiche, $oChamp, $value);
		}

		private static function setDbValue($oFiche, $oChamp, $value)
		{
			$idGroupe = ($oChamp -> scope == 'groupe' ? tsDroits::getGroupeUtilisateur() : 0);
			$idFicheVersion = ($oChamp -> versioning == 'Y' ? $oFiche -> idVersion : 0);
			return tsDatabase::query(self::SQL_SET_VALUE_CHAMP, array($oFiche -> idFiche, $idFicheVersion, $oChamp -> idChamp, $idGroupe, $value));
		}

		private static function setPluginValue($oFiche, $oChamp, $value)
		{
			tsPlugins::registerVar('oFiche', $oFiche);
			tsPlugins::registerVar('oChamp', $oChamp);
			tsPlugins::registerVar('value', $value);
			tsPlugins::callHook('champDb', 'setFicheValueChamp', $oChamp -> identifiant);
		}




		public static function getChamp($idChamp)
		{
			if (is_numeric($idChamp) === false)
			{
				throw new ApplicationException("L'identifiant de champ n'est pas numérique");
			}
			$result = tsDatabase::getObject(self::SQL_CHAMP, array($idChamp), DB_FAIL_ON_ERROR);
			$oChamp = champModele::getInstance($result, 'champModele');

			if (is_null($result->idChampParent))
			{
				// Enfants pour champ tif complexe
				$oChampCollection = new ChampCollection();
				$idChamps = tsDatabase::getRecords(self::SQL_CHAMP_ENFANTS, array($idChamp));
				foreach ($idChamps as $idChamp)
				{
					$oChampCollection[] = self::getChamp($idChamp);
				}
				$oChamp->setChamps($oChampCollection->getCollection());
				$oChamp = $oChamp->getObject();
			}
			else
			{
				$oChamp->setIdChampParent($result->idChampParent);
				$oChamp->setListe($result['liste']);
			}

			return $oChamp;
		}


		public static function getChampByIdentifiant($identifiant)
		{
			$idChamp = tsDatabase::getRecord(self::SQL_CHAMP_IDENTIFIANT, array($identifiant), DB_FAIL_ON_ERROR);

			return self::getChamp($idChamp);
		}


		public static function getChampByXPath($xpath)
		{
			$idChamp = tsDatabase::getRecord(self::SQL_CHAMP_XPATH, array($xpath), DB_FAIL_ON_ERROR);

			return self::getChamp($idChamp);
		}


		public static function getChampsByXPath($xpath)
		{
			$idChamps = tsDatabase::getRecords(self::SQL_CHAMP_XPATH_MULTI, array($xpath), DB_FAIL_ON_ERROR);

			$retour = array();
			foreach ($idChamps as $idChamp)
			{
				$retour[] = self::getChamp($idChamp);
			}

			return $retour;
		}


		public static function createChamp($identifiant, $libelle, $xPath, $liste, $bordereaux, $oChampParent)
		{
			$idChamp = tsDatabase::insert(self::SQL_CREATE_CHAMP, array($identifiant, $libelle, $xPath));
			if (is_null($liste) === false)
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('liste', $liste, $idChamp));
			}
			if (is_null($bordereaux) === false)
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('bordereau', $bordereaux, $idChamp));
			}
			if (is_null($oChampParent) === false)
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('idChampParent', $oChampParent->idChamp, $idChamp));
			}

			return $idChamp;
		}


		public static function updateChamp(champModele $oChamp, $identifiant, $libelle, $xPath, $liste, $bordereaux)
		{
			tsDatabase::query(self::SQL_UPDATE_CHAMP, array($identifiant, $libelle, $xPath, $oChamp->idChamp));

			if (is_null($liste))
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_NULL, array('liste', $oChamp->idChamp));
			}
			else
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('liste', $liste, $oChamp->idChamp));
			}

			if (is_null($bordereaux))
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_NULL, array('bordereau', $oChamp->idChamp));
			}
			else
			{
				tsDatabase::query(self::SQL_UPDATE_CHAMP_VALUE, array('bordereau', $bordereaux, $oChamp->idChamp));
			}
		}


		public static function getChamps($bordereau)
		{
			$oChampCollection = new ChampCollection();
			if (is_null($bordereau))
			{
				$idChamps = tsDatabase::getRecords(self::SQL_CHAMPS, array());
			}
			else
			{
				$oBordereau = new bordereauModele();
				$oBordereau->setBordereau($bordereau);
				$idChamps = tsDatabase::getRecords(self::SQL_CHAMPS . self::SQL_CHAMPS_BORDEREAU, array($bordereau));
			}

			foreach ($idChamps as $idChamp)
			{
				$oChampCollection[] = self::getChamp($idChamp);
			}

			return $oChampCollection->getCollection();
		}


		public static function deleteChamp(champModele $oChamp)
		{
			return tsDatabase::query(self::SQL_DELETE_CHAMP, array($oChamp->idChamp));
		}

	}
