<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/ficheDb.php');
	require_once('application/db/ficheFichierDb.php');

	/**
	 * Classe wsFicheFichier - endpoint du webservice FicheFichier
	 * Gestion des fichiers attachés à une fiche
	 * Accessible aux utilisateurs root, superadmin, admin, desk, manager
	 */
	final class wsFicheFichier extends wsEndpoint
	{
		
		/**
		 * Attache un fichier à une fiche
		 * @param string $nomFichier : nom du fichier
		 * @param bool $principal : si true, fichier principal de la fiche
		 * @param binary $content : contenu binaire du fichier "base64_encodé"
		 * @return int idFichier : identifiant du fichier sitFicheFichier.idFichier
		 */
		protected function _addFicheFichier($idFiche, $nomFichier, $principal, $url)
		{
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkAccesFiche($oFiche);
			$idFichier = ficheFichierDb::addFicheFichier($oFiche, $nomFichier, $principal, $url);
			return array('idFichier' => $idFichier);
		}
		
		
		/**
		 * Supprime un fichier d'une fiche
		 * @param int idFichier : identifiant du fichier sitFicheFichier.idFichier
		 */
		protected function _deleteFicheFichier($idFichier)
		{
			$oFicheFichier = ficheFichierDb::getFicheFichier($idFichier);
			$idFiche = $oFicheFichier -> idFiche;
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkAccesFiche($oFiche);
			ficheFichierDb::deleteFicheFichier($oFicheFichier);
			return array();
		}
		
		
		/**
		 * Retourne la liste des fichiers d'une fiche
		 * @param int idFiche : identifiant de la fiche sitFiche.idFiche
		 * @return FicheFichiersCollection fichiers : collection de ficheFichierModele 
		 */
		protected function _getFicheFichiers($idFiche)
		{
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkAccesFiche($oFiche);
			$fichiers = ficheFichierDb::getFicheFichiers($oFiche);
			return array('fichiers' => $fichiers);
		}
		
		
		/**
		 * Retourne un fichier d'une fiche
		 * @param int idFichier : identifiant du fichier sitFicheFichier.idFichier
		 * @return ficheFichierModele fichier 
		 */
		protected function _getFicheFichier($idFichier)
		{
			$oFichier = ficheFichierDb::getFicheFichier($idFichier);
			// Vérification des droits sur fiche après traitement
			$idFiche = $oFichier -> idFiche;
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkAccesFiche($oFiche);
			return array('fichier' => $oFichier);
		}
		
		
		
	}
