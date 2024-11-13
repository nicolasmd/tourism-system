<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/champDb.php');
	require_once('application/db/communeDb.php');
	require_once('application/db/ficheDb.php');
	require_once('application/modele/bordereauModele.php');
	require_once('application/utils/tifTools.php');

	/**
	 * Classe wsFicheImport - endpoint du webservice FicheImport
	 * 
	 */
	final class wsFicheImport extends wsEndpoint
	{
		
		/**
		 * Import d'une fiche dans Tourism System
		 * @param string $xmlTif : source XML Tourinfrance de la fiche
		 * @return int idFiche : identifiant numérique de la fiche sitFiche.idFiche
		 * @access root superadmin admin
		 */
		protected function _importFiche($xmlTif)
		{
			$this -> restrictAccess('root', 'superadmin', 'admin');
			
			$dcIdentifier = tsXml::getValueXpath($xmlTif, '//tif:DublinCore/dc:identifier');
			if (empty($dcIdentifier))
			{
				throw new ImportException("Aucun dcIdentifier", 511);
			}
			elseif (is_array($dcIdentifier))
			{
				$dcIdentifier = $dcIdentifier[0];
			}
			
			$idFiche = ficheDb::getIdFicheByRefExterne($dcIdentifier);
			if (empty($idFiche))
			{
				$idFiche = ficheDb::getIdFicheByCodeTIF($dcIdentifier);
			}
			
			$raisonSociale = tsXml::getValueXpath($xmlTif, '//tif:Contacts/tif:DetailContact[attribute::type="04.03.13"][1]/tif:RaisonSociale');
			if (empty($raisonSociale))
			{
				throw new ImportException("Aucune raison sociale", 512, array('idFiche' => $idFiche));
			}
			
			/*$dcTitle = tsXml::getValueXpath($xmlTif, '//tif:DublinCore/dc:title');
			if (empty($dcTitle))
			{
				throw new ImportException("Aucun dcTitle", 513, array('idFiche' => $idFiche));
			}*/
			
			$classification = tsXml::getValueXpath($xmlTif, '//tif:DublinCore/tif:Classification/@code');
			try
			{
				$bordereau = tifTools::getBordereau($classification);
			}
			catch(Exception $e)
			{
				throw new ImportException($e -> getMessage(), 514, array('idFiche' => $idFiche));
			}
			
			$codeInsee = tsXml::getValueXpath($xmlTif, '//tif:Contacts/tif:DetailContact[@type="04.03.13"][1]//tif:Commune/@code');
			if (empty($codeInsee))
			{
				throw new ImportException("Aucun codeInsee", 515, array('idFiche' => $idFiche));
			}
			elseif (is_array($codeInsee))
			{
				$codeInsee = $codeInsee[0];
			}
			
			$newFiche = false;
			
			if(empty($idFiche))
			{
				$oBordereau = new bordereauModele();
				$oBordereau -> setBordereau($bordereau);
				$oCommune = communeDb::getCommune($codeInsee);
				$this -> checkDroitBordereauCommune($oBordereau, $oCommune, DROIT_CREATION_FICHES);
				
				$idFiche = ficheDb::createFiche($oBordereau, $oCommune, $dcIdentifier);
				$oFiche = ficheDb::getFicheSimpleByIdFiche($idFiche);
				$newFiche = true;
			}
			else
			{
				$oFiche = ficheDb::getFicheSimpleByIdFiche($idFiche);
				$this -> checkDroitFiche($oFiche, DROIT_MODIFICATION);
			}
			
			// Hook beforeCreateFicheVersion
			tsPlugins::registerVar('oFiche', $oFiche);
			tsPlugins::registerVar('xmlTif', $xmlTif);
			tsPlugins::callHook('wsFicheImport', 'importFiche', 'beforeCreateFicheVersion');
			
			ficheDb::createFicheVersion($idFiche, $xmlTif);
			
			// Fix : en attendant que la plateforme envoie le contrat hors du XML
			$oChamp = champDb::getChamp(200);
			$contrat = tsXml::getValueXpath($xmlTif, $oChamp -> xPath);

			if (!empty($contrat))
			{
				champDb::setFicheValueChamp($oFiche, $oChamp, $contrat);
			}

			$oFiche -> newFiche = $newFiche;
			
			return array('fiche' => $oFiche);
		}
		
		
		/**
		 * Supprime les dernières versions de chaque fiche si ces dernières ont été mises à jour durant un intervalle de temps
		 * @param string $dateDebut : date de début de l'import
		 * @param string $dateFin : date de fin de l'import
		 * @access root superadmin admin
		 */
		protected function _restoreFiche($dateDebut, $dateFin)
		{
			if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $dateDebut)
				|| !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $dateFin))
			{
				throw new Exception("Dates invalides");
			}
			
			$timestampDebut = strtotime($dateDebut);
			$timestampFin = strtotime($dateFin);
			
			$deletedVersions = array();
			
			foreach (tsDroits::getFichesAdministrables() as $idFiche)
			{
				$oFiche = ficheDb::getFicheByIdFiche($idFiche);
				
				if ($oFiche -> idVersion > 2 // Cas d'une fiche créée pendant l'import, ne peut être restaurée
					&& strtotime($oFiche -> dateVersion) > $timestampDebut
					&& strtotime($oFiche -> dateVersion) < $timestampFin)
				{
					$deletedVersions[] = array(
						'idFiche' => $oFiche -> idFiche,
						'idFicheVersion' => $oFiche -> idVersion
					);
					ficheDb::deleteFicheVersion($oFiche, $oFiche -> idVersion);
				}
			}
			
			return array('versions' => $deletedVersions);
		}
		
		
		/**
		 * Construit une fiche xml via un tableau de pairs xpath/valeur
		 * @param array $champs : tableau de champs xpath/valeur
		 * @param string $xml (optional) : xml de base
		 * @param bool $sort (optional) : tri les champs
		 * @return string xml : xml de la fiche
		 * @access root superadmin admin
		 */
		protected function _buildFicheXpath($champs, $xml, $sort)
		{
			// Quand ça provient de la plateforme Java
			if (isset($champs -> item))
			{
				$champs = $champs -> item;
			}
			
			$baseXml = !empty($xml) ? $xml : file_get_contents(tsConfig::get('TS_PATH_EMPTYXML'));
			
			$domFiche = new tsDOMDocument('1.0');
			$domFiche -> loadXML($baseXml);
			$domXpath = new DOMXpath($domFiche);
			
			if (is_null($sort) || $sort === true)
			{
				usort ($champs, array($this, 'sortXpath'));
			}
			
			foreach($champs as $champ)
			{
				try
				{
					$domFiche -> setValueFromXPath($champ -> xPath, $champ -> value);
					$domFiche -> saveXML();
				}
				catch(Exception $ex)
				{
					//Annulation de l'enregistrement de ce xPath
				}
			}
			
			return array('xml' => $domFiche -> saveXML());
		}
		
		
		private function sortXpath($a, $b)
		{
			$cmp = strcmp($a -> xPath, $b -> xPath);
			return ($cmp>0) ? 1 : -1;
		}
		
		
	}
