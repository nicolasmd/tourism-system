/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Client
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		GNU GPLv3 ; see LICENSE.txt
 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
 */

Ext.ts.MTH = {
	getFilterKey: function(bordereau, name) {
		switch (bordereau) {
			case 'ASC':
				switch (name) {
					case 'label': return {key: '06.06.07.*'}; break;
					case 'type_tarif': return {key: '13.04.(01|07).*'}; break;
					case 'tarif': return {key: '13.04.(01|07).*'}; break;
					default: return false; break;
				}
				break;
			case 'DEG':
				switch (name) {
					case 'label': return {key: '06.06.07.*'}; break;
					case 'type_tarif': return {key: '13.04.01.*'}; break;
					case 'tarif': return {key: '13.04.01.*'}; break;
					default: return false; break;
				}
				break;
			case 'FMA':
				switch (name) {
					case 'label': return {key: '06.06.07.*'}; break;
					case 'type_tarif': return {key: '13.04.01.*'}; break;
					case 'tarif': return {key: '13.04.01.*'}; break;
					default: return false; break;
				}
				break;
			case 'HLO':
				switch (name) {
					case 'type_etablissement': return {key: '02.01.04.01.*'}; break;
					case 'classement': return {key: '06.04.01.04.*'}; break;
					case 'label': return {key: '06.06.(02|06).*'}; break;
					case 'gites_de_france': return {key: '06.03.02.01.*'}; break;
					case 'chaine': return {key: '06.02.03.*'}; break;
					case 'type_tarif': return {key: '13.04.04.*'}; break;
					case 'tarif': return {key: '13.04.04.*'}; break;
					case 'prestation_produit': return {key: '15.07.*'}; break;
					default: return false; break;
				}
				break;
			case 'HOT':
				switch (name) {
					case 'type_etablissement': return {key: '02.01.05.01.*'}; break;
					case 'classement': return {key: '06.04.01.03.*'}; break;
					case 'label': return {key: '06.06.(03|06).*'}; break;
					case 'chaine': return {key: '06.02.02.*'}; break;
					case 'type_tarif': return {key: '13.04.03.*'}; break;
					case 'tarif': return {key: '13.04.03.*'}; break;
					case 'prestation_produit': return {key: '15.09.*'}; break;
					default: return false; break;
				}
				break;
			case 'HPA':
				switch (name) {
					case 'type_etablissement': return {key: '02.01.06.01.*'}; break;
					case 'classement': return {key: '06.04.01.02.*', pop: '06.04.01.02.05,06.04.01.02.06,06.04.01.02.09,06.04.01.02.10'}; break;
					case 'label': return {key: '06.06.01.*'}; break;
					case 'gites_de_france': return {key: '06.03.01.03.*'}; break;
					case 'chaine': return {key: '06.02.01.*'}; break;
					case 'type_tarif': return {key: '13.04.02.*'}; break;
					case 'tarif': return {key: '13.04.02.*'}; break;
					case 'prestation_produit': return {key: '15.08.*'}; break;
					default: return false; break;
				}
				break;
			case 'LOI':
				switch (name) {
					case 'label': return {key: '06.06.07.*'}; break;
					case 'type_tarif': return {key: '13.04.01.*'}; break;
					case 'tarif': return {key: '13.04.01.*'}; break;
					default: return false; break;
				}
				break;
			case 'ORG':
				switch (name) {
					case 'classement': return {key: '06.04.01.01.*'}; break;
					case 'label': return {key: '06.06.07.*'}; break;
					case 'prestation_produit': return {key: '15.11.*'}; break;
					default: return false; break;
				}
				break;
			case 'PCU':
				switch (name) {
					case 'label': return {key: '06.06.07.*'}; break;
					case 'type_tarif': return {key: '13.04.01.*'}; break;
					case 'tarif': return {key: '13.04.01.*'}; break;
					default: return false; break;
				}
				break;
			case 'PNA':
				switch (name) {
					case 'type_etablissement': return {key: '02.01.12.01.*'}; break;
					case 'label': return {key: '06.06.07.*'}; break;
					case 'type_tarif': return {key: '13.04.01.*'}; break;
					case 'tarif': return {key: '13.04.01.*'}; break;
					default: return false; break;
				}
				break;
			case 'RES':
				switch (name) {
					case 'classement': return {key: '06.04.01.05.*'}; break;
					case 'label': return {key: '06.06.(04|06).*'}; break;
					case 'type_tarif': return {key: '13.04.05.*'}; break;
					case 'tarif': return {key: '13.04.05.*'}; break;
					default: return false; break;
				}
				break;
			case 'VIL':
				switch (name) {
					case 'type_etablissement': return {key: '02.01.14.*'}; break;
					case 'classement': return {key: '06.04.01.06.*'}; break;
					case 'label': return {key: '06.06.05.*'}; break;
					case 'chaine': return {key: '06.02.05.*'}; break;
					case 'type_tarif': return {key: '13.04.06.*'}; break;
					case 'tarif': return {key: '13.04.06.*'}; break;
					case 'prestation_produit': return {key: '15.10.*'}; break;
					default: return false; break;
				}
				break;
			case 'PRD':
				switch (name) {
					case 'type_tarif': return {key: '13.04.01.*'}; break;
					case 'tarif': return {key: '13.04.01.*'}; break;
					default: return false; break;
				}
				break;
			default: return false; break;
		}
	}
};