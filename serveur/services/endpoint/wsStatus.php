<?

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	class WSStatus
	{
		// Objet commun pour le retour du service
		public function __construct($success, $message, $errorCode = 0, $errorInfos = array())
		{
			$this -> success = $success;
			$this -> message = (is_array($message)) ? implode('<br />', $message) : $message;
			$this -> errorCode = $errorCode;
			$this -> errorInfos = $errorInfos;
		}
	
	}
