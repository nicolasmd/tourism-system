<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsSession
	{
		const SQL_INIT_SESSION = "INSERT INTO sitSessions (idUtilisateur, sessionId, sessionStart, sessionEnd) VALUES ('%d', '%s', NOW(), DATE_ADD(NOW(), INTERVAL 89 MINUTE))";
		const SQL_SESSION_ACTIVE = "SELECT sessionId FROM sitSessions WHERE idUtilisateur='%d' AND TIMESTAMPDIFF(SECOND, NOW(), sessionEnd) > 0";
		const SQL_REFRESH_SESSION = "UPDATE sitSessions SET sessionEnd=DATE_ADD(NOW(), INTERVAL 89 MINUTE) WHERE sessionId='%s'";
		const SQL_UTILISATEUR = "SELECT idUtilisateur FROM sitSessions WHERE sessionId='%s' AND TIMESTAMPDIFF(SECOND, NOW(), sessionEnd) > 0";

		public static function initSession($idUtilisateur)
		{
			$sessionActive = self::hasSessionActive($idUtilisateur);
			if ($sessionActive !== false)
			{
				self::refreshSession($sessionActive);
				return $sessionActive;
			}
			$idSession = md5(uniqid(mt_rand(), true));
			tsDatabase::query(self::SQL_INIT_SESSION, array($idUtilisateur, $idSession));
			return $idSession;
		}

		private static function hasSessionActive($idUtilisateur)
		{
			return tsDatabase::getRecord(self::SQL_SESSION_ACTIVE, array($idUtilisateur));
		}

		private static function refreshSession($idSession)
		{
			return tsDatabase::query(self::SQL_REFRESH_SESSION, array($idSession));
		}

		public static function getIdUtilisateur($idSession)
		{
			$idUtilisateur = tsDatabase::getRecord(self::SQL_UTILISATEUR, array($idSession));
			if ($idUtilisateur === false)
			{
				throw new SessionException("Cette session n'existe pas ou est expirée");
			}
			self::refreshSession($idSession);
			return $idUtilisateur;
		}
	}
