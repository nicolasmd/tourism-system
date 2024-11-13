<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	/**
	 * Classe permettant le debuggage et le log d'évènements
	 */
	class Logger
	{

		private static $instance;

		// Configuration par défaut du logger

		private static $mode = 'email';
		private static $email = TS_EMAIL_LOGS;
		private static $application = 'Application';
		private static $user_reporting = 'E_USER_ERROR,E_USER_WARNING'; // E_USER_NOTICE
		private static $verbose = false;
		private static $error_reporting = 4;

		/**
		 * Ne logge plus les évènements
		 */
		public static function __sleep()
		{
			restore_error_handler();
		}

		/**
		 * Logge à nouveau les évènements
		 */
		public static function __wakeup()
		{
			self::init(array());
		}

		/**
		 * Gestion des erreurs utilisateur lancées par trigger_error
		 * Traitement spécifique selon leur niveau
		 * @return true
		 * @param $errno integer : niveau d'erreur
		 * @param $errstr string : message d'erreur
		 * @param $errfile : fichier sur lequel l'erreur a été identifiée
		 * @param $errline : ligne à laquelle l'erreur a été identifiée
		 */
		public static function userErrorHandler($errno, $errstr, $errfile, $errline)
		{
			switch ($errno)
			{
				// Erreur
				case E_USER_ERROR:
					//echo "[$errno] $errstr<br />\n";
					//echo "Erreur fatale à la ligne $errline dans le fichier $errfile à la ligne $errline";
					Logger::file("[$errno] $errstr<br />$errfile à la ligne $errline", 'SIT E_USER_ERROR');
					exit(1);
					break;
				// Avertissement
				case E_USER_WARNING:
					//echo "<b>ALERTE</b> [$errno] $errstr<br />\n";
					//echo "Avertissement dans le fichier $errfile à la ligne $errline";
					Logger::file("[$errno] $errstr<br />$errfile à la ligne $errline", 'SIT E_USER_WARNING');
					break;
				// Notice
				case E_USER_NOTICE:
					//echo "<b>AVERTISSEMENT</b> [$errno] $errstr<br />\n";
					//echo "Notice dans le fichier $errfile à la ligne $errline";
					Logger::file("[$errno] $errstr<br />$errfile à la ligne $errline", 'SIT E_USER_NOTICE');
					break;
				// Autres erreurs
				default:
					if (self::$error_reporting >= $errno)
					{
						$id = uniqid('SIT');
						//echo "Erreur ($errno) dans le fichier $errfile à la ligne $errline : <i>$errstr</i><br />";
						Logger::file("[$errno] $errstr<br />$errfile à la ligne $errline", 'SIT ERROR : ' . $id);
						//Logger::email($_REQUEST, 'REQUEST SIT ERROR : ' . $id);
					}
					break;
			}

			return true;
		}


		/**
		 * Initialise le logger
		 * @param $params array : tableau associatif de paramêtres
		 * 			verbose -> mode verbeux
		 * 			mode -> ne sert pas
		 * 			email -> email d'envoi pour la réception des erreurs
		 * 			application -> nom de l'application
		 * 			user_reporting -> E_USER_ERROR,E_USER_WARNING,E_USER_NOTICE
		 * 			error_reporting -> niveau de rapport d'erreurs
		 */
		public static function init(array $params)
		{
			//ini_set('error_prepend_string', '<font style="font-family:Tahoma; font-size: 12px; color: #c00;">');
			//ini_set('error_append_string', '</font>');

			set_error_handler(array(__CLASS__, "userErrorHandler"));
			self::$verbose = (isset($params['verbose'])) ?
				$params['verbose'] : self::$verbose;
			self::$mode = (isset($params['mode'])) ?
				$params['mode'] : self::$mode;
			self::$email = (isset($params['email'])) ?
				$params['email'] : self::$email;
			self::$application = (isset($params['application'])) ?
				$params['application'] : self::$application;
			self::$user_reporting = (isset($params['user_reporting'])) ?
				$params['user_reporting'] : self::$user_reporting;
			self::$error_reporting = (isset($params['error_reporting'])) ?
				$params['error_reporting'] : self::$error_reporting;

			//error_reporting(self::$error_reporting);
		}

		/**
		 * Logge dans un fichier le message passé en paramêtre
		 * Les fichiers sont stockés sur l'arborescence dans le répertoire logs
		 * @param $message : N'importe quel type (objet, tableau, string)
		 */
		public static function file($message)
		{
			if (is_array($message) || is_object($message))
			{
				$message = var_export($message, true);
			}
			$filename = LOGS_PATH . date('Y-m-d') . '_log_' . date('H') . 'h.txt';
			$message = date('H:i:s') . ' -> ' . $message . PHP_EOL;
			file_put_contents($filename, $message, FILE_APPEND);
			chmod($filename, 0777);
		}

		/**
		 * Envoie le message demandé par email
		 * Les fichiers sont stockés sur l'arborescence dans le réportoire logs
		 * @param $message : N'importe quel type (objet, tableau, string)
		 * @param $objet string [optional] : L'objet du message
		 */
		public static function email($message, $objet = null)
		{
			if (is_null($objet))
			{
				$objet = self::$application . ' Logger_' . date('Y-m-d H:i:s');
			}

			if (is_array($message) || is_object($message))
			{
				$message = var_export($message, true);
			}
			mail(self::$email, $objet, $message);
		}

		/**
		 * echo le message passé en paramêtre
		 * @param $message : N'importe quel type (objet, tableau, string)
		 */
		public static function debug($message)
		{
			if (is_array($message) || is_object($message))
			{
				echo '<pre>';
				var_dump($message);
				echo '</pre>';
			}
			else
			{
				echo $message . '<br />';
			}
		}

		/**
		 * echo le message passé en paramêtre seulement en mode verbeux
		 * @param $message : N'importe quel type (objet, tableau, string)
		 */
		public static function log($message)
		{
			if (self::$verbose === true)
			{
				if (is_array($message) || is_object($message))
				{
					echo '<pre>';
					print_r($message);
					echo '</pre>';
				}
				else
				{
					echo $message . '<br />';
				}
			}
		}

		private static $sbenchactif = true;

		public static function benchState($actif)
		{
			Logger::$sbenchactif = $actif;
		}

		/**
		 * Logge tout et n'importe quoi avec des timestamps
		 * @param $message : N'importe quel type (objet, tableau, string)
		 */
		public static function bench($_METHOD_NAME_HERE_, $object)
		{
			if (Logger::$sbenchactif)
			{
				$now = microtime(true);
				// ne rien mettre avant

				if ($_METHOD_NAME_HERE_ !== 0)
				{
					@session_id(666);
					@session_start();

					$past = $_SESSION[$_METHOD_NAME_HERE_]['lastTime'];
					$delta = ($now - $past); // seconds with microseconds

					if ($delta > 60) // si le dernier log date de plus d'une minute on reprend a zero
					{
						$delta = 0;
					}

					$contentToAdd = '';

					$numArgs = func_num_args();
					for ($i = 2; $i < $numArgs; $i++)
					{
						$message = func_get_arg($i);

						if (is_array($message) || is_object($message))
						{
							$message = var_export($message, true);
						}

						$contentToAdd .= $message;

						if (($i + 1) < $numArgs)
						{
							$contentToAdd .= "\n\n\n\n    --++--++--++-->    " . $i . "    <--++--++--++--\n\n\n\n";
						}
					}

					tsDatabase::query("insert into dLog (t,d,m,k,v) values('%s','%s','%s','%s','%s')", array($now, $delta, $_METHOD_NAME_HERE_, $object, $contentToAdd));

					// ne pas modifier la fin
					$_SESSION[$_METHOD_NAME_HERE_]['lastTime'] = microtime(true); // reprise du time pour ne pas tenir compte du temps mis par le log
				}
			}
		}

		public static function benchReset()
		{
			@session_id(666);
			@session_start();
			$_SESSION = array();
		}

	}

?>
