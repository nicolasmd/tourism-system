<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/ficheModele.php');
	require_once('application/modele/ficheFichierModele.php');
	
	// @todo : en verifiant la classe : penser à s'occuper de l'image principale 
	final class ficheFichierDb
	{
	
		const SQL_CREATE_FICHE_FICHIER = "INSERT INTO sitFicheFichier (idFiche) VALUES ('%d')";
		const SQL_UPDATE_FICHE_FICHIER = "UPDATE sitFicheFichier SET md5='%s', nomFichier='%s', path='%s', url='%s', type='%s', extension='%s', principal='%s' WHERE idFichier='%d'";
		const SQL_FICHE_FICHIER = "SELECT idFiche, md5, nomFichier, path, url, type, extension, principal FROM sitFicheFichier WHERE idFichier='%d'";
		const SQL_FICHE_FICHIERS = "SELECT idFichier FROM sitFicheFichier WHERE idFiche='%d'";
		const SQL_DELETE_FICHE_FICHIER = "DELETE FROM sitFicheFichier WHERE idFichier='%d'";		
		
		private static $normalizeChars = array(
			'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
			'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
			'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
			'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
			'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
			'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
			'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
		);
		
		
		public static function getFicheFichier($idFichier)
		{
			$fichier = tsDatabase::getRow(self::SQL_FICHE_FICHIER, array($idFichier), DB_FAIL_ON_ERROR);
			
			$oFicheFichier = new ficheFichierModele();
			$oFicheFichier -> setIdFiche($fichier['idFiche']);
			$oFicheFichier -> setIdFichier($idFichier);
			$oFicheFichier -> setMd5($fichier['md5']);
			$oFicheFichier -> setNomFichier($fichier['nomFichier']);
			$oFicheFichier -> setType($fichier['type']);
			$oFicheFichier -> setUrl($fichier['url']);
			$oFicheFichier -> setPath($fichier['path']);
			$oFicheFichier -> setExtension($fichier['extension']);
			$oFicheFichier -> setPrincipal($fichier['principal']);
			
			return $oFicheFichier;
		}
		
		

		public static function getFicheFichiers(ficheModele $oFiche)
		{
			$oFicheFichierCollection = new FicheFichierCollection();
			$idsFichier = tsDatabase::getRecords(self::SQL_FICHE_FICHIERS, array($oFiche -> idFiche));
			foreach($idsFichier as $idFichier)
			{
				$oFichier = self::getFicheFichier($idFichier);
				$oFicheFichierCollection[] = $oFichier -> getObject();
			}
			return $oFicheFichierCollection -> getCollection();
		}
		
		
		
		/**
		 * 
		 * @param ficheModele $oFiche
		 * @param string   $nomFichier
		 * @param string   $principal
		 * @param string   $contentBase64
		 * @return 
		 */
		public static function addFicheFichier(ficheModele $oFiche, $nomFichier, $principal, $url)
		{
			$principalYN = ($principal === true) ? 'Y' : 'N';
			
			$content = file_get_contents(str_replace(' ', '%20', $url));
			if ($content === false)
			{
				throw new ApplicationException("Le fichier envoyé n'est pas acccessible");
			}
			
			$parts = self::explodeFilename($nomFichier);
			$filename = self::cleanUploadedFilename($parts[0]);
			if ($filename == '')
			{
				throw new ApplicationException("Le nom du fichier n'est pas correct");
			}
			$extension = strtolower($parts[1]);
			$type = self::getType($extension);
			if (is_null($type))
			{
				throw new ApplicationException("Le type de fichier n'est pas correct");
			}
			
			$md5 = md5($content);

			$idFichier = tsDatabase::insert(self::SQL_CREATE_FICHE_FICHIER, array($oFiche -> idFiche));
			
			$pathMedias = tsConfig::get('TS_PATH_MEDIAS');
			$subFolders = str_split(substr($md5, 0, tsConfig::get('TS_SUBFOLDERS_DEPTH_MEDIAS')));
			foreach ($subFolders as $subFolder)
			{
				$pathMedias .= $subFolder . '/';
				if (is_dir($pathMedias) === false)
				{
					mkdir($pathMedias);
				}
			}
			$subFolder = implode('/', $subFolders) . '/';
			
			$pathFichier = tsConfig::get('TS_PATH_MEDIAS') . $subFolder . $idFichier . '_' . $filename . '.' . $extension;
			$urlFichier = tsConfig::get('TS_URL_MEDIAS') . $subFolder . $idFichier . '_' . $filename . '.' . $extension;
			
			$res = file_put_contents($pathFichier, $content);
			if ($res === false)
			{
				throw new ApplicationException("Le fichier n'a pas pu être créé");
			}
			
			tsDatabase::query(self::SQL_UPDATE_FICHE_FICHIER, 
					array($md5, $filename, $pathFichier, $urlFichier, $type, $extension, $principalYN, $idFichier));
			
			return $idFichier;
		}
		
		
		
		
		public static function deleteFicheFichier(ficheFichierModele $oFicheFichier)
		{
			if (file_exists($oFicheFichier -> path))
			{
				unlink($oFicheFichier -> path);
			}
			return tsDatabase::query(self::SQL_DELETE_FICHE_FICHIER, array($oFicheFichier -> idFichier));
		}
		
		
		
		
		
		/**
		 * Scinde un nom de fichier en Nom / Extension
		 * @return Array : tableau[nom_du_fichier, extension]
		 * @param $filename String : Nom du fichier à analyser
		 */
		private static function explodeFilename($filename)
		{
			$parts = explode('.', $filename);
			$extension = array_pop($parts);
			$name = implode('.', $parts);
			return(array($name, $extension));
		}
		
		
		
		/**
		 * Nettoie le nom de fichier uploadé par l'utilisateur
		 * @return string : le nom du fichier nettoyé
		 * @param $filename string : Nom du fichier sans l'extension
		 * @param $replacedBy string[optional] : caractère de remplacement (underscore par défaut)
		 */
		private static function cleanUploadedFilename($filename, $replacedBy = '_')
		{
			return preg_replace('/[^a-z0-9\_\-]/', '', strtolower(strtr(
				str_replace(array(' ', '%20'), array($replacedBy, $replacedBy), trim($filename)), self::$normalizeChars)));
		}
		
		
		
		private static function getType($extension)
		{
			$type = null;
			
			switch (strtolower($extension))
			{
				case 'mp3': case 'wav':
					$type = 'audio';
				break;
				
				case 'doc': case 'txt': case 'xls':
				case 'odt': case 'csv':
					$type = 'doc';
				break;
				
				case 'jpg': case 'jpeg': case 'png': case 'gif':
					$type = 'image';
				break;
				
				case 'pdf':
					$type = 'pdf';
				break;
				
				case 'flv': case 'mpg': case 'mpeg':
				case 'avi': case 'mov': case 'mp4':
					$type = 'video';
				break;
				
				case 'xml':
					$type = 'xml';
				break;
			}
			
			return $type;
		} 
		
		
		
	}
