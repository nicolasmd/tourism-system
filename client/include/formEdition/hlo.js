/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Client
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		GNU GPLv3 ; see LICENSE.txt
 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
 */

Ext.ts.HLO = [{
	onglet: 'Etablissement',
	items: [{
		fieldset: 'Coordonnées établissement',
		items: [
			Ext.ts.formEdition.type_etablissement,
			Ext.ts.formEdition.raison_sociale,
			Ext.ts.formEdition.adresse1,
			Ext.ts.formEdition.adresse2,
			Ext.ts.formEdition.adresse3,
			Ext.ts.formEdition.code_postal,
			Ext.ts.formEdition.cedex,
			Ext.ts.formEdition.bureau_distributeur,
			Ext.ts.formEdition.commune,
			Ext.ts.formEdition.province_etat,
			Ext.ts.formEdition.telephone1,
			Ext.ts.formEdition.telephone2,
			Ext.ts.formEdition.fax,
			Ext.ts.formEdition.site_web,
			Ext.ts.formEdition.email
		]
	},{
		fieldset: 'Informations légales',
		items: [
			Ext.ts.formEdition.siret,
			Ext.ts.formEdition.ape_naf,
			Ext.ts.formEdition.rcs
		]
	},/*{
		fieldset: 'Description commerciale',
		items: [
			Ext.ts.formEdition.description_commerciale
		]
	},*//*{
		fieldset: 'Slogan',
		items: [
			Ext.ts.formEdition.slogan
		]
	},*/{
		fieldset: 'Langues parlées',
		items: [
			//Ext.ts.formEdition.langues_parlees
			Ext.ts.formEdition.langues_parlees_accueil
		]
	},{
		fieldset: 'Capacités',
		items: [
			Ext.ts.formEdition.capacite_chambre,
			Ext.ts.formEdition.capacite_personne
		]
	}]
},{
	onglet: 'Descriptions',
	items: [{
		fieldset: 'Français',
		items: [
			Ext.ts.formEdition.description_commerciale_fr
		]
	},{
		fieldset: 'Anglais',
		items: [
			Ext.ts.formEdition.description_commerciale_en
		]
	},{
		fieldset: 'Espagnol',
		items: [
			Ext.ts.formEdition.description_commerciale_es
		]
	},{
		fieldset: 'Allemand',
		items: [
			Ext.ts.formEdition.description_commerciale_de
		]
	},{
		fieldset: 'Italien',
		items: [
			Ext.ts.formEdition.description_commerciale_it
		]
	},{
		fieldset: 'Néerlandais',
		items: [
			Ext.ts.formEdition.description_commerciale_nl
		]
	}]
},{
	onglet: 'Slogans',
	items: [{
		fieldset: 'Français',
		items: [
			Ext.ts.formEdition.slogan_fr
		]
	},{
		fieldset: 'Anglais',
		items: [
			Ext.ts.formEdition.slogan_en
		]
	},{
		fieldset: 'Espagnol',
		items: [
			Ext.ts.formEdition.slogan_es
		]
	},{
		fieldset: 'Allemand',
		items: [
			Ext.ts.formEdition.slogan_de
		]
	},{
		fieldset: 'Italien',
		items: [
			Ext.ts.formEdition.slogan_it
		]
	},{
		fieldset: 'Néerlandais',
		items: [
			Ext.ts.formEdition.slogan_nl
		]
	}]
},{
	onglet: 'Capacités',
	items: [{
		fieldset: 'Détails capacités',
		items: [
			Ext.ts.formEdition.capacite
		]
	}]
},{
	onglet: 'Classements',
	items: [{
		fieldset: 'Classement officiel',
		items: [
			Ext.ts.formEdition.classement,
			Ext.ts.formEdition.date_classement,
			Ext.ts.formEdition.numero_classement
		]
	},{
		fieldset: 'Labels',
		items: [
			Ext.ts.formEdition.label,
			Ext.ts.formEdition.gites_de_france,
			{
				xtype: 'combomth',
				id: 'gites_de_france_charmance',
				tsName: 'gites_de_france_charmance',
				fieldLabel: 'Gîtes de France - Charmance',
				LS: 'LS_Classement',
				key: '99.06.03.02.22'
			},{
				xtype: 'combomth',
				id: 'locations_clevacances',
				tsName: 'locations_clevacances',
				fieldLabel: 'Locations CléVacances',
				LS: 'LS_Classement',
				key: '06.03.02.02'
			},{
				xtype: 'combomth',
				id: 'bed_and_breakfast',
				tsName: 'bed_and_breakfast',
				fieldLabel: 'Bed & Breakfast France',
				LS: 'LS_Classement',
				key: '99.06.03.02.03'
			},{
				xtype: 'combomth',
				id: 'cles_d_or',
				tsName: 'cles_d_or',
				fieldLabel: "Clés d'or",
				LS: 'LS_Classement',
				key: '99.06.03.02.09'
			},{
				xtype: 'combomth',
				id: 'ecolabel_europeen',
				tsName: 'ecolabel_europeen',
				fieldLabel: 'EcoLabel Européen',
				LS: 'LS_Classement',
				key: '100.06.03.02.01'
			},{
				xtype: 'combomth',
				id: 'clef_verte',
				tsName: 'clef_verte',
				fieldLabel: 'Clef verte',
				LS: 'LS_Classement',
				key: '100.06.03.02.02'
			},{
				xtype: 'combomth',
				id: 'fleurs_de_soleil',
				tsName: 'fleurs_de_soleil',
				fieldLabel: 'Fleurs de soleil',
				LS: 'LS_Classement',
				key: '99.06.03.02.18'
			}
		]
	},{
		fieldset: 'Chaînes',
		items: [
			Ext.ts.formEdition.chaine
		]
	},{
		fieldset: 'Tourisme et handicap',
		items: [
			Ext.ts.formEdition.handicap
		]
	}]
},/*{
	onglet: 'Contacts',
	items: [{
		fieldset: 'Détails contact',
		items: [
			Ext.ts.formEdition.contact
		]
	}]
},*/{
	onglet: 'Contacts',
	items: [{
		fieldset: 'Propriétaire',
		items: [
			Ext.ts.formEdition.proprietaire_raison_sociale,
			Ext.ts.formEdition.proprietaire_nom,
			Ext.ts.formEdition.proprietaire_prenom,
			Ext.ts.formEdition.proprietaire_adresse1,
			Ext.ts.formEdition.proprietaire_adresse2,
			Ext.ts.formEdition.proprietaire_adresse3,
			Ext.ts.formEdition.proprietaire_code_postal,
			Ext.ts.formEdition.proprietaire_cedex,
			Ext.ts.formEdition.proprietaire_code_insee,
			Ext.ts.formEdition.proprietaire_pays,
			Ext.ts.formEdition.proprietaire_telephone1,
			Ext.ts.formEdition.proprietaire_telephone2,
			Ext.ts.formEdition.proprietaire_fax,
			Ext.ts.formEdition.proprietaire_site_web,
			Ext.ts.formEdition.proprietaire_email
		]
	},{
		fieldset: 'Réservation',
		items: [
			Ext.ts.formEdition.reservation_raison_sociale,
			Ext.ts.formEdition.reservation_nom,
			Ext.ts.formEdition.reservation_prenom,
			Ext.ts.formEdition.reservation_adresse1,
			Ext.ts.formEdition.reservation_adresse2,
			Ext.ts.formEdition.reservation_adresse3,
			Ext.ts.formEdition.reservation_code_postal,
			Ext.ts.formEdition.reservation_cedex,
			Ext.ts.formEdition.reservation_code_insee,
			Ext.ts.formEdition.reservation_pays,
			Ext.ts.formEdition.reservation_telephone1,
			Ext.ts.formEdition.reservation_telephone2,
			Ext.ts.formEdition.reservation_fax,
			Ext.ts.formEdition.reservation_site_web,
			Ext.ts.formEdition.reservation_email
		]
	}]
},{
	onglet: 'Localisation',
	items: [{
		fieldset: 'Géolocalisation',
		items: [
			Ext.ts.formEdition.coordonnees_gps
		]
	},{
		fieldset: 'Environnement',
		items: [
			Ext.ts.formEdition.environnement
		]
	},{
		fieldset: 'Points d\'accès',
		items: [
			Ext.ts.formEdition.points_acces
		]
	}]
},{
	onglet: 'Prestations',
	items: [{
		fieldset: 'Accessibilités',
		items: [
			Ext.ts.formEdition.accessibilite
		]
	},{
		fieldset: 'Activités',
		items: [
			Ext.ts.formEdition.activite
		]
	},{
		fieldset: 'Conforts',
		items: [
			Ext.ts.formEdition.confort
		]
	},{
		fieldset: 'Equipements',
		items: [
			Ext.ts.formEdition.equipement
		]
	},{
		fieldset: 'Services',
		items: [
			Ext.ts.formEdition.service
		]
	}]
},{
	onglet: 'Tarifs',
	items: [{
		fieldset: 'Modes de paiement',
		items: [
			Ext.ts.formEdition.mode_paiement
		]
	},{
		fieldset: 'Tarifs',
		items: [
			Ext.ts.formEdition.tarif
		]
	}]
},{
	onglet: 'Médias',
	ongletCfg: {
		layout: 'anchor'
	},
	items: [{
		fieldset: 'Bibliothèque de fichiers',
		fieldsetCfg: {
			anchor: '100% 100%',
			layout: 'fit'
		},
		items: [
			Ext.ts.formEdition.photos_fichiers
		]
	}]
},{
	onglet: "Périodes d'ouverture",
	items: [{
		fieldset: "Périodes d'ouverture",
		items: [
			Ext.ts.formEdition.ouverture
		]
	},{
		fieldset: 'Disponibilités',
		items: [
			Ext.ts.formEdition.disponibilites
		]
	}]
},{
	onglet: 'Champs spécifiques',
	items: [{
		fieldset: 'Champs spécifiques',
		items: [
			Ext.ts.formEdition.champs_specifiques
		]
	}]
}];