<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	/**
	 * Classe parente des services
	 * Initialise l'application
	 * Gère les erreurs et les retours pour la conformité au wsdl
	 */
	abstract class wsEndpoint
	{
		private static $loaded = false;

		/**
		 * Méthode d'appel, chaque méthode de service passe par ici
		 * Initialise l'application, charge les droits de l'utilisateur, puis appelle la méthode demandée
		 * Permet de centraliser la gestion des retours d'erreur (wsStatus) pour garder la conformité au wsdl
		 * @param string $method : méthode appelée
		 * @param array $arguments : tableau d'arguments passés
		 * @return : ce qui est retourné par la méthode appelée
		 */
		public function __call($method, $arguments)
		{
			// Appel entre services en interne
			if (self::$loaded == true)
			{
				return call_user_func_array(array($this, '_' . $method), $arguments);
			}

			try
			{
				// Load de l'application
				self::loadApplication();

				self::$loaded = true;

				// Chargement des plugins, load les hooks
				tsPlugins::loadPlugins();

				$className = get_class($this);

				// Hook Before Session
				tsPlugins::registerVar('arguments', $arguments);
				tsPlugins::callHook($className, $method, 'beforeSession');

				// Vérification et renouvellement de la session, load des droits
				// Dépile $arguments pour enlever le sessionId
				tsDroits::restore(array_shift($arguments));
				tsDroits::load();

				if (method_exists($this, '_' . $method))
				{
					// Hook Params
					tsPlugins::setHookParams((array) array_pop($arguments));

					// Hook Before
					foreach (getParamsName($className, '_' . $method) as $k => $paramName)
					{
						tsPlugins::registerVar($paramName, $arguments[$k]);
					}
					tsPlugins::callHook($className, $method, 'before');

					$retour = call_user_func_array(array($this, '_' . $method), $arguments);
					$retour = (is_array($retour) === false) ? array() : $retour;

					// Hook After
					tsPlugins::registerVar('retour', $retour);
					tsPlugins::callHook($className, $method, 'after');

					// Hook Responses
					$retour['hookResponses'] = tsPlugins::getHookResponses();
				}
				else
				{
					throw new ApplicationException("La méthode demandée n'existe pas");
				}



				if (count(Logger::$errors) > 0)
				{
					$success = false;
					$errors = Logger::$errors;
					$errorCode = 0;
					$errorInfos = array();
				}
				elseif (count(Logger::$notices) > 0)
				{
					$success = false;
					$errors = Logger::$notices;
					$errorCode = 0;
					$errorInfos = array();
				}
				else
				{
					$success = true;
					$errors = null;
					$errorCode = 0;
					$errorInfos = array();
				}

			}
			catch(SessionException $e)
			{
				$success = false;
				$errors = $e -> getMessage();
				$errorCode = 510;
				$errorInfos = $e -> getInfos();

				Logger::file("Erreur de session");
			}
			catch(SecuriteException $e)
			{
				$success = false;
				$errors = $e -> getMessage();
				$errorCode = $e -> getCode();
				$errorInfos = $e -> getInfos();

				Logger::file($e -> getMessage());
			}
			catch(ImportException $e)
			{
				$success = false;
				$errors = $e -> getMessage();
				$errorCode = $e -> getCode();
				$errorInfos = $e -> getInfos();

				Logger::file($e -> getMessage());
			}
			catch(ApplicationException $e)
			{
				$success = false;
				$errors = $e -> getMessage();
				$errorCode = $e -> getCode();
				$errorInfos = $e -> getInfos();

				Logger::file($e -> getMessage());
			}
			catch(Exception $e)
			{
				$success = false;
				$errors = $e -> getMessage();
				$errorCode = $e -> getCode();
				$errorInfos = array();

				Logger::file($e -> getMessage());
			}

			return array_merge(array('status' => new wsStatus($success, $errors, $errorCode, $errorInfos)), (array) $retour);
		}




		/**
		 * Initialisation de l'application (config, bdd, cache)
		 * @void
		 */
		final protected function loadApplication()
		{
			Logger::init(array(
				'email' => tsConfig::get('TS_EMAIL_LOGS'),
				'application' => 'Tourism System',
				'error_reporting' => 4,
				'user_reporting' => 'E_USER_ERROR,E_USER_WARNING',
				'verbose' => false,
				'encoding' => 'UTF-8'
			));

			tsDatabase::load(tsConfig::get('TS_BDD_TYPE'));
			tsDatabase::connect(
				tsConfig::get('TS_BDD_SERVER'),
				tsConfig::get('TS_BDD_USER'),
				tsConfig::get('TS_BDD_PASSWORD'));
			tsDatabase::selectDatabase(tsConfig::get('TS_BDD_NAME'));

			tsCache::load(tsConfig::get('TS_CACHE'));
		}




		/**
		 * Restriction des accès aux services
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function restrictAccess()
		{
			$authorizedUsers = func_get_args();

			if (in_array(tsDroits::getTypeUtilisateur(), $authorizedUsers) === false)
			{
				throw new SecuriteException("Droits insuffisants : ce service n'est pas disponible");
			}
		}


		/**
		 *
		 * @param ficheModele $oFiche :
		 * @param object   $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkAccesFiche(ficheModele $oFiche)
		{
			if (tsDroits::isFicheAdministrable($oFiche) === false)
			{
				throw new SecuriteException("Vous n'avez pas accès à cette fiche.", 516, array('idFiche' => $oFiche -> idFiche));
			}
		}


		/**
		 *
		 * @param ficheModele $oFiche :
		 * @param object   $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitFiche(ficheModele $oFiche, $droit)
		{
			$droitsFiche = tsDroits::getDroitFiche($oFiche);

			if (($droitsFiche & $droit) === 0)
			{
				throw new SecuriteException("Droits insuffisants : vous n'avez pas les droits sur cette fiche", 517, array('idFiche' => $oFiche->idFiche));
			}
		}


		/**
		 *
		 * @param ficheModele $oFiche :
		 * @param object   $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitFicheChamp(ficheModele $oFiche, champModele $oChamp, $droit)
		{
			$droitsFiche = tsDroits::getDroitFiche($oFiche);
			$droitsFicheChamp = tsDroits::getDroitFicheChamp($oFiche, $oChamp, $droitFiche);

			if (($droitsFicheChamp & $droit) === 0)
			{
				throw new SecuriteException("Droits insuffisants : vous n'avez pas les droits sur le champ cette fiche", 518,
					array('idFiche' => $oFiche->idFiche, 'idChamp' => $oChamp -> idChamp));
			}
		}

		/**
		 *
		 * @param groupeModele $oGroupe
		 * @param object    $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitGroupe(groupeModele $oGroupe, $droit)
		{

		}

		/**
		 *
		 * @param champModele $oChamp
		 * @param object   $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitChamp(champModele $oChamp, $droit)
		{

		}

		/**
		 *
		 * @param utilisateurModele $oUtilisateur
		 * @param object         $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitUtilisateur(utilisateurModele $oUtilisateur, $droit)
		{

		}

		/**
		 *
		 * @param profilDroitModele $oProfil
		 * @param object    $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitProfil(profilDroitModele $oProfil, $droit)
		{
			if (tsDroits::isRoot() === false)
			{
				if ($oProfil -> idGroupe != tsDroits::getGroupeUtilisateur() && $droit == DROIT_ADMIN)
				{
					throw new SecuriteException("Droits insuffisants : vous n'avez pas les droits sur ce profil", 520, array('idFiche' => $oProfil->idProfil));
				}
			}
		}

		/**
		 *
		 * @param territoireModele $oTerritoire
		 * @param object        $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitTerritoire(territoireModele $oTerritoire, $droit)
		{

		}

		/**
		 *
		 * @param thesaurusModele $oThesaurus
		 * @param object       $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitThesaurus(thesaurusModele $oThesaurus, $droit)
		{

		}

		/**
		 *
		 * @param bordereauModele $oBordereau
		 * @param communeModele   $oCommune
		 * @param object       $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitBordereauCommune(bordereauModele $oBordereau, communeModele $oCommune, $droit)
		{
			$droitsBordereauCommune = tsDroits::getDroitBordereauCommune($oBordereau, $oCommune);

			if (($droitsBordereauCommune & $droit) === 0)
			{
				throw new SecuriteException("Droits insuffisants : vous n'avez pas les droits sur ce bordereau/commune");
			}
		}

		/**
		 *
		 * @param bordereauModele  $oBordereau
		 * @param territoireModele $oTerritoire
		 * @param object        $droit
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitBordereauTerritoire(bordereauModele $oBordereau, territoireModele $oTerritoire, $droit)
		{

		}


		/**
		 *
		 * @param communeModele $oCommune :
		 * @throws SecuriteException : l'utilisateur n'a pas accès au service
		 */
		protected function checkDroitCommune(communeModele $oCommune)
		{

		}

	}
