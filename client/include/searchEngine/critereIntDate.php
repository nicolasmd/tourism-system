<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	header('Content-Type: text/html; charset=utf-8');
	
	switch ($_REQUEST['bordereau'])
	{
		case 'ASC':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'DEG':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'FMA':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'HLO':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'HOT':
			echo json_encode(array(
				array('int', 'Tarifs', '13.04.03'),
				array('int', 'Capacité', '01.03.01.09'),
				array('int', 'Distance à la route (en m)', '08.02.06.08', '01.03.02.01'),
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'HPA':
			echo json_encode(array(
				array('int', 'Tarifs', '13.04.02'),
				array('int', 'Capacité', '01.03.01.09'),
				array('int', 'Distance à la route (en m)', '08.02.06.08', '01.03.02.01'),
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'ITI':
			echo json_encode(array(
				
			));
		break;
		case 'LOI':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'ORG':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'PCU':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'PNA':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'PRD':
			echo json_encode(array(
				
			));
		break;
		case 'RES':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		case 'VIL':
			echo json_encode(array(
				array('date', "Date d'ouverture", '09.01.01')
			));
		break;
		default:
			echo json_encode(array());
		break;
	}
	
?>