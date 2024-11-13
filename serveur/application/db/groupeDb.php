<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/pluginDb.php');
	require_once('application/db/territoireDb.php');
	require_once('application/modele/groupeModele.php');
	require_once('application/modele/territoireModele.php');
	require_once('application/modele/utilisateurModele.php');
	
	final class groupeDb
	{
		
		const SQL_GROUPE = "SELECT idGroupe, idGroupeParent, nomGroupe, descriptionGroupe, idSuperAdmin FROM sitGroupe WHERE idGroupe='%d'";
		const SQL_GROUPES_ALL = "SELECT idGroupe, idGroupeParent, nomGroupe, descriptionGroupe, idSuperAdmin FROM sitGroupe WHERE idGroupe IN ('%s')";
		const SQL_GROUPES_ROOT = "SELECT idGroupe, idGroupeParent, nomGroupe, descriptionGroupe, idSuperAdmin FROM sitGroupe WHERE idGroupe IN ('%s') AND idGroupeParent IS NULL";
		const SQL_GROUPES_CHILDS = "SELECT idGroupe, idGroupeParent, nomGroupe, descriptionGroupe, idSuperAdmin FROM sitGroupe WHERE idGroupe IN ('%s') AND idGroupeParent='%d'";
		const SQL_CHILDS = "SELECT idGroupe FROM sitGroupe WHERE idGroupeParent='%d'";
		const SQL_CREATE_GROUPE_ROOT = "INSERT INTO sitGroupe (nomGroupe, descriptionGroupe) VALUES('%s', '%s')";
		const SQL_CREATE_GROUPE_CHILD = "INSERT INTO sitGroupe (nomGroupe, descriptionGroupe, idGroupeParent) VALUES('%s', '%s', '%d')";
		const SQL_UPDATE_GROUPE = "UPDATE sitGroupe SET nomGroupe='%s', descriptionGroupe='%s' WHERE idGroupe='%d'";
		const SQL_DELETE_GROUPE = "DELETE FROM sitGroupe WHERE idGroupe='%d'";
		const SQL_SET_SUPER_ADMIN_GROUPE = "UPDATE sitGroupe SET idSuperAdmin='%d' WHERE idGroupe='%d'";
		const SQL_UNSET_SUPER_ADMIN_GROUPE = "UPDATE sitGroupe SET idSuperAdmin=NULL WHERE idGroupe='%d'";
		const SQL_GROUPE_UTILISATEURS = "SELECT idUtilisateur, login, pass, typeUtilisateur FROM sitUtilisateur WHERE idGroupe='%d'";
		const SQL_GROUPE_TERRITOIRES = "SELECT idTerritoire FROM sitGroupeTerritoire WHERE idGroupe='%d'";
		const SQL_ADD_GROUPE_TERRITOIRE = "INSERT INTO sitGroupeTerritoire (idGroupe, idTerritoire) VALUES ('%d', '%d')";
		const SQL_DELETE_GROUPE_TERRITOIRE = "DELETE FROM sitGroupeTerritoire WHERE idGroupe='%d' AND idTerritoire='%d'";
		const SQL_GROUPE_PARTENAIRES = "SELECT idGroupePartenaire AS idGroupe, typePartenaire FROM sitGroupePartenaire WHERE idGroupe='%d'";
		const SQL_GROUPE_PARTENAIRE_TYPE = "SELECT typePartenaire FROM sitGroupePartenaire WHERE idGroupe='%d' AND idGroupePartenaire='%d'";
		const SQL_ADD_GROUPE_PARTENAIRE = "INSERT INTO sitGroupePartenaire (idGroupe, idGroupePartenaire, typePartenaire) VALUES ('%d', '%d', '%s')";
		const SQL_DELETE_GROUPE_PARTENAIRE = "DELETE FROM sitGroupePartenaire WHERE idGroupe='%d' AND idGroupePartenaire='%d'";
		const SQL_ADD_PARTENAIRE_FICHE_EXCLUDE = "INSERT INTO sitGroupePartenaireFicheExclude (idGroupe, idFiche) VALUES ('%d', '%d')";
		const SQL_DELETE_PARTENAIRE_FICHE_INCLUDE = "DELETE FROM sitGroupePartenaireFicheInclude WHERE idGroupe ='%d' AND idFiche ='%d'";
		const SQL_GROUPE_PLUGINS = "SELECT idPlugin FROM sitGroupePlugin WHERE idGroupe='%d'";
		const SQL_ADD_PLUGIN_GROUPE = "INSERT INTO sitGroupePlugin (idGroupe, idPlugin) VALUES ('%d', '%d')";
		const SQL_DELETE_PLUGIN_GROUPE = "DELETE FROM sitGroupePlugin WHERE idGroupe='%d' AND idPlugin='%d'";
		
		
		public static function getGroupe($idGroupe)
		{
			if (is_numeric($idGroupe) === false)
			{
				throw new ApplicationException("L'identifiant de groupe n'est pas numérique");
			}
			$groupe = tsDatabase::getObject(self::SQL_GROUPE, array($idGroupe), DB_FAIL_ON_ERROR);
			return groupeModele::getInstance($groupe, 'groupeModele');
		}
		
		
		public static function getGroupes($idGroupeParent = null)
		{
			$oGroupeCollection = new groupeCollection();
			if (is_null($idGroupeParent) === false)
			{
				$groupes = (is_numeric($idGroupeParent) && $idGroupeParent > 0)
					? tsDatabase::getObjects(self::SQL_GROUPES_CHILDS, array(tsDroits::getGroupesAdministrables(), $idGroupeParent))
					: tsDatabase::getObjects(self::SQL_GROUPES_ROOT, array(tsDroits::getGroupesAdministrables()));
			}
			else
			{
				$groupes = tsDatabase::getObjects(self::SQL_GROUPES_ALL, array(tsDroits::getGroupesAdministrables()));
			}
			foreach ($groupes as $groupe)
			{
				$oGroupeCollection[] = groupeModele::getInstance($groupe, 'groupeModele');
			}
			return $oGroupeCollection -> getCollection();
		}
		
		
		public static function getGroupesChilds(groupeModele $oGroupe)
		{
			$idGroupes = tsDatabase::getRecords(self::SQL_CHILDS, array($oGroupe -> idGroupe));
			
			$groupes = array();
			foreach ($idGroupes as $idGroupe)
			{
				$groupes[] = $idGroupe;
				$groupes = array_merge($groupes, self::getGroupesChilds($idGroupe));
			}
			
			return $groupes;
		}
		
		
		public static function createGroupe($nomGroupe, $descriptionGroupe, $idGroupeParent = null)
		{
			return (is_numeric($idGroupeParent) && $idGroupeParent > 0)
				? tsDatabase::insert(self::SQL_CREATE_GROUPE_CHILD, array($nomGroupe, $descriptionGroupe, $idGroupeParent))
				: tsDatabase::insert(self::SQL_CREATE_GROUPE_ROOT, array($nomGroupe, $descriptionGroupe));
		}
		
		
		public static function updateGroupe($oGroupe, $nomGroupe, $descriptionGroupe)
		{
			return tsDatabase::query(self::SQL_UPDATE_GROUPE, array($nomGroupe, $descriptionGroupe, $oGroupe -> idGroupe));
		}
		
		
		public static function deleteGroupe(groupeModele $oGroupe)
		{
			return tsDatabase::query(self::SQL_DELETE_GROUPE, array($oGroupe -> idGroupe));
		}
		
		
		public static function setSuperAdminGroupe(groupeModele $oGroupe, utilisateurModele $oUtilisateur)
		{
			if($oUtilisateur -> idGroupe != $oGroupe -> idGroupe)
			{
				throw new SecuriteException("L'utilisateur fait partie d'un autre groupe, il ne peut pas être administrateur");
			}
			
			if (in_array($oUtilisateur -> typeUtilisateur, array('admin', 'superadmin')) === false)
			{
				throw new SecuriteException("Seuls les utilisateurs de type admin peuvent devenir administrateur du groupe");
			}
			
			return tsDatabase::query(self::SQL_SET_SUPER_ADMIN_GROUPE, array($oUtilisateur -> idUtilisateur, $oGroupe -> idGroupe));
		}
		
		
		public static function unsetSuperAdminGroupe(groupeModele $oGroupe)
		{
			return tsDatabase::query(self::SQL_UNSET_SUPER_ADMIN_GROUPE, array($oGroupe -> idGroupe));;
		}
		
		
		public static function getUtilisateursGroupe(groupeModele $oGroupe)
		{
			return tsDatabase::getRows(self::SQL_GROUPE_UTILISATEURS, array($oGroupe -> idGroupe));
		}
		
		
		public static function getGroupeTerritoires(groupeModele $oGroupe)
		{
			$oTerritoireCollection = new TerritoireCollection();
			$idTerritoires = tsDatabase::getRecords(self::SQL_GROUPE_TERRITOIRES, array($oGroupe -> idGroupe));
			
			foreach($idTerritoires as $idTerritoire)
			{
				$oTerritoireCollection[] = territoireDb::getTerritoire($idTerritoire);
			}
			return $oTerritoireCollection -> getCollection();
		}

		
		public static function addGroupeTerritoire(groupeModele $oGroupe, territoireModele $oTerritoire)
		{
			return tsDatabase::insert(self::SQL_ADD_GROUPE_TERRITOIRE, array($oGroupe -> idGroupe, $oTerritoire -> idTerritoire));
		}
		
		
		public static function deleteGroupeTerritoire(groupeModele $oGroupe, territoireModele $oTerritoire)
		{
			return tsDatabase::query(self::SQL_DELETE_GROUPE_TERRITOIRE, array($oGroupe -> idGroupe, $oTerritoire -> idTerritoire));
		}
		
		
		public static function getGroupePartenaires(groupeModele $oGroupe)
		{
			return tsDatabase::getObjects(self::SQL_GROUPE_PARTENAIRES, array($oGroupe -> idGroupe));
		}

		
		public static function addGroupePartenaire(groupeModele $oGroupe, groupeModele $oGroupePartenaire, $typePartenaire)
		{
			return tsDatabase::insert(self::SQL_ADD_GROUPE_PARTENAIRE, array($oGroupe -> idGroupe, $oGroupePartenaire -> idGroupe, $typePartenaire));
		}
		
		
		public static function deleteGroupePartenaire(groupeModele $oGroupe, groupeModele $oGroupePartenaire)
		{
			return tsDatabase::query(self::SQL_DELETE_GROUPE_PARTENAIRE, array($oGroupe -> idGroupe, $oGroupePartenaire -> idGroupe));
		}
		
		
		public static function getGroupePartenaireType(groupeModele $oGroupe, groupeModele $oGroupePartenaire)
		{
			return tsDatabase::getRecord(self::SQL_GROUPE_PARTENAIRE_TYPE, array($oGroupe -> idGroupe, $oGroupePartenaire -> idGroupe));
		}
		
		
		public function addGroupePartenaireFicheExclude($oFiche)
		{
			return tsDatabase::query(self::SQL_ADD_PARTENAIRE_FICHE_EXCLUDE, array(tsDroits::getGroupeUtilisateur(), $oFiche -> idFiche));
		}
		
		
		public function deleteGroupePartenaireFicheInclude($oFiche)
		{
			return tsDatabase::query(self::SQL_DELETE_PARTENAIRE_FICHE_INCLUDE, array(tsDroits::getGroupeUtilisateur(), $oFiche -> idFiche));
		}
		
		
		public static function getGroupePlugins(groupeModele $oGroupe)
		{
			$oPluginCollection = new PluginCollection();
			$idPlugins = tsDatabase::getRecords(self::SQL_GROUPE_PLUGINS, array($oGroupe->idGroupe));
			foreach($idPlugins as $idPlugin)
			{
				$oPluginCollection[] = pluginDb::getPlugin($idPlugin);
			}
			return $oPluginCollection -> getCollection();
		}
		
		public static function addGroupePlugin($idGroupe, $oPlugin)
		{
			return tsDatabase::query(self::SQL_ADD_PLUGIN_GROUPE, array($idGroupe, $oPlugin->idPlugin));
		}
		
		public static function deleteGroupePlugin($idGroupe, $oPlugin)
		{
			return tsDatabase::query(self::SQL_DELETE_PLUGIN_GROUPE, array($idGroupe, $oPlugin->idPlugin));
		}
		
	}
