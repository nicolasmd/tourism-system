<?php
	
/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/sessionCollection.php');
	
	final class sessionModele extends baseModele implements WSDLable
	{
		
		protected $idUtilisateur;
		protected $sessionId;
		protected $sessionStart;
		protected $sessionEnd;
		protected $ip;


		public function __toString()
		{
			$str = '<h3>Session</h3>';
			$str .= '<h5>idUtilisateur : ' . $this -> idUtilisateur . '</h5>';
			$str .= '<h5>SessionId : ' . $this -> sessionId . '</h5>';
			$str .= '<h5>sessionStart : ' . $this -> sessionStart . '</h5>';
			$str .= '<h5>sessionEnd : ' . $this -> sessionEnd . '</h5>';
			$str .= '<h5>IP : ' . $this -> ip . '</h5>';
			return $str;
		}
		
	}
