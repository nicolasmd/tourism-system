<?php

/**
 * @version		1.0 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	/**
	 * Classe wsIdentification - endpoint du webservice Identification
	 * Permet de gérer l'identification (et la déconnexion)
	 * @todo : déconnexion
	 */
	final class wsIdentification extends wsEndpoint
	{

		/**
		 * Méthode d'authentification aux webservices.
		 * L'identification ouvre une session pour l'accès aux autres méthodes
		 * La session dure 20 minutes et est renouvelée à chaque appel d'une méthode nécessitant l'identifiant de session.
		 * @param $email string : identifiant de l'utilisateur
		 * @param $password string : mot de passe
		 * @return string idSession : identifiant de session à garder pour l'appel aux autres services
		 */
		public static function identification($email, $password)
		{
			try
			{
				self::loadApplication();

				if ($email == '')
				{
					throw new ApplicationException('L\'identifiant fourni ne peut pas être vide');
				}

				if ($password == '')
				{
					throw new ApplicationException('Le mot de passe fourni ne peut pas être vide');
				}

				return(array(new wsStatus(true), tsDroits::connect($email, $password)));
			}
			catch(ApplicationException $e)
			{
				return array(new wsStatus(false, $e -> getMessage(), 4));
			}
			catch(Exception $e)
			{
				return array(new wsStatus(false, $e -> getMessage(), 3));
			}
		}



	}
