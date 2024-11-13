<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	require_once('include/header.php');
?>

		<script type="text/javascript">
			Ext.onReady(function(){
				
				var store_profil = new Ext.ts.JsonStore({
					id: 'store_profil',
					action: 'getProfils',
					service: 'profilDroit',
					autoLoad: true,
					fields: [
						{name: 'idProfil', type: 'int'},
						{name: 'libelle', type: 'string'}
					],
					listeners: {
						load: function(store) {
							var Record = store.recordType;
							store.insert(0, new Record({
								idProfil: 0,
								libelle: Ext.ts.Lang.emptyProfil
							}));
						}
					},
					preLoad: true
				});
				
				/**
				 * PANEL DROITS FICHE
				 */
				var store_droitsFiche = new Ext.ts.JsonStore({
					id: 'store_droitsFiche',
					action: 'getDroitsFiche',
					service: 'utilisateurDroitFiche',
					baseParams: {idUtilisateur: Ext.ts.params.idUtilisateur},
					fields: [
						{name: 'idFiche', type: 'int'},
						{name: 'raisonSociale', type: 'string'},
						{name: 'visualisation', type: 'bool'},
						{name: 'modification', type: 'bool'},
						{name: 'validation', type: 'bool'},
						{name: 'suppressionFiches', type: 'bool'},
						{name: 'idProfil', type: 'int'}
					],
					sortInfo: {field: 'raisonSociale', direction: 'ASC'},
					listeners: {
						load: function (store, records) {
							panel_droitsFiche.setTitle(Ext.ts.Lang.droitsFiches + ' (' + records.length + ')');
						}
					}
				});
				
				var actionColItemsDF = [{
					getClass: function(value, meta, record) {
						return (value ? 'button_ok' : 'button_cancel')
							+ (record.data.idProfil != 0 ? '_gray' : '');
					},
					handler: function(grid, rowIndex, colIndex) {
						var dataIndex = grid.getColumnModel().getDataIndex(colIndex);
						var record = grid.getStore().getAt(rowIndex);
						
						if (record.data.idProfil == 0) {
							record.data[dataIndex] = !record.data[dataIndex];
							editDroitFiche(record.data);
						}
						else {
							Ext.Msg.alert(Ext.ts.Lang.failureTitle, Ext.ts.Lang.editDroitError);
						}
					}
				}];
				
				var grid_droitsFiche = new Ext.grid.EditorGridPanel({
					id: 'grid_droitsFiche',
					region: 'center',
					store: store_droitsFiche,
					colModel: new Ext.grid.ColumnModel({
						defaults: {
							xtype: 'actioncolumn',
							width: 90,
							sortable: true
						},
						columns: [{
							xtype: 'gridcolumn',
							header: Ext.ts.Lang.raisonSociale,
							dataIndex: 'raisonSociale',
							width: 250,
							renderer: function(value, metaData, record) {
								return value + ' (' + record.data.idFiche + ')';
							}
						},{
							xtype: 'gridcolumn',
							header: Ext.ts.Lang.profil,
							dataIndex: 'idProfil',
							width: 150,
							editor: new Ext.form.ComboBox({
								mode: 'local',
								store: store_profil,
								valueField: 'idProfil',
								displayField: 'libelle',
								hiddenName: 'idProfil',
								triggerAction: 'all',
								editable: false,
								listeners: {
									change: function(combo, value) {
										var selection = grid_droitsFiche.getSelectionModel().getSelected();
										Ext.ts.request({
											action: 'setDroitFicheProfil',
											service: 'utilisateurDroitFiche',
											params: {
												idUtilisateur: Ext.ts.params.idUtilisateur,
												idFiche: selection.data.idFiche,
												idProfil: value
											},
											success: function(response) {
												Ext.getCmp('grid_droitsFiche').getStore().reload();
											}
										});
									}
								}
							}),
							renderer: function(value, metaData) {
								var index = store_profil.findExact('idProfil', value);
								if (index == 0) {
									metaData.attr = 'style="color:gray;"';
								}
								return store_profil.getAt(index).data.libelle;
							}
						},{
							header: Ext.ts.Lang.visualisation,
							dataIndex: 'visualisation',
							items: actionColItemsDF
						},{
							header: Ext.ts.Lang.modification,
							dataIndex: 'modification',
							items: actionColItemsDF
						},{
							header: Ext.ts.Lang.validation,
							dataIndex: 'validation',
							items: actionColItemsDF
						},{
							header: Ext.ts.Lang.suppression,
							dataIndex: 'suppressionFiches',
							items: actionColItemsDF
						},{
							xtype: 'actioncolumn',
							header: Ext.ts.Lang.outils,
							width: 60,
							items: [{
								iconCls: 'delete',
								tooltip: Ext.ts.Lang.supprimer,
								handler: function(grid, rowIndex, colIndex) {
									var record = grid.getStore().getAt(rowIndex);
									deleteDroitFiche(record.data.idFiche);
								}
							}]
						}]
					}),
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: true,
						listeners: {
							selectionchange: function(sm) {
								if (sm.hasSelection()) {
									var record = sm.getSelected();
									store_droitsFicheChamp.setBaseParam('idFiche', record.data.idFiche);
									store_droitsFicheChamp.load();
								}
								else {
									store_droitsFicheChamp.removeAll();
								}
							}
						}
					}),
					tbar: ['->',{
						xtype: 'autocompletecombo',
						id: 'fiche',
						emptyText: Ext.ts.Lang.selectFiche,
						store: new Ext.ts.JsonStore({
							action: 'getFiches',
							service: 'fiche',
							fields: [
								{name: 'idFiche', type: 'int'},
								{name: 'raisonSociale', type: 'string'}
							],
							listeners: {
								load: function(store, record) {
									Ext.each(record, function(item) {
										item.data.displayField = item.data.raisonSociale + ' (' + item.data.idFiche + ')';
									});
								}
							}
						}),
						valueField: 'idFiche',
						displayField: 'displayField',
						tpl: '<tpl for="."><div class="x-combo-list-item">{raisonSociale} ({idFiche})</div></tpl>',
						enableKeyEvents: true,
						listeners: {
							keypress: function (field, e) {
								if (e.getKey() == e.ENTER) {
									e.stopEvent();
									addDroitFiche();
								}
							}
						}
					},{
						iconCls: 'add',
						handler: addDroitFiche
					}]
				});
				
				var store_droitsFicheChamp = new Ext.ts.JsonStore({
					action: 'getDroitFicheChamp',
					service: 'utilisateurDroitFiche',
					baseParams: {idUtilisateur: Ext.ts.params.idUtilisateur},
					fields: [
						{name: 'idChamp', type: 'int'},
						{name: 'libelle', type: 'string'},
						{name: 'visualisation', type: 'bool'},
						{name: 'modification', type: 'bool'},
						{name: 'validation', type: 'bool'}
					]
				});
				
				var actionColItemsDFC = [{
					getClass: function(value) {
						var idProfil = grid_droitsFiche.getSelectionModel().getSelected().data.idProfil;
						return (value ? 'button_ok' : 'button_cancel') + (idProfil != 0 ? '_gray' : '');
					},
					handler: function(grid, rowIndex, colIndex) {
						var dataIndex = grid.getColumnModel().getDataIndex(colIndex);
						var record = grid.getStore().getAt(rowIndex);
						var idProfil = grid_droitsFiche.getSelectionModel().getSelected().data.idProfil;
						
						if (idProfil == 0) {
							record.data[dataIndex] = !record.data[dataIndex];
							editDroitFicheChamp(record.data);
						}
						else {
							Ext.Msg.alert(Ext.ts.Lang.failureTitle, Ext.ts.Lang.editDroitError);
						}
					},
					scope: this
				}];
				
				var grid_droitsFicheChamp = new Ext.grid.GridPanel({
					id: 'grid_droitsFicheChamp',
					region: 'east',
					width: 520,
					minWidth: 520,
					header: false,
					collapsible: true,
					collapseMode: 'mini',
					collapsed: false,
					split: true,
					store: store_droitsFicheChamp,
					colModel: new Ext.grid.ColumnModel({
						defaults: {
							xtype: 'actioncolumn',
							width: 80,
							sortable: true
						},
						columns: [{
							xtype: 'gridcolumn',
							header: Ext.ts.Lang.champ,
							dataIndex: 'libelle',
							width: 200
						},{
							header: Ext.ts.Lang.visualisation,
							dataIndex: 'visualisation',
							items: actionColItemsDFC
						},{
							header: Ext.ts.Lang.modification,
							dataIndex: 'modification',
							items: actionColItemsDFC
						},{
							header: Ext.ts.Lang.validation,
							dataIndex: 'validation',
							items: actionColItemsDFC
						},{
							header: Ext.ts.Lang.outils,
							dataIndex: 'idChamp',
							width: 50,
							items: [{
								getClass: function(value) {
									var idProfil = grid_droitsFiche.getSelectionModel().getSelected().data.idProfil;
									return (idProfil == 0 ? 'delete' : 'no-img');
								},
								tooltip: Ext.ts.Lang.supprimer,
								handler: function(grid, rowIndex, colIndex) {
									var record = grid.getStore().getAt(rowIndex);
									deleteDroitFicheChamp(record.data.idChamp);
								},
								scope: this
							}]
						}]
					}),
					tbar: ['->',{
						xtype: 'autocompletecombo',
						id: 'idChampFiche',
						emptyText: Ext.ts.Lang.selectChamp,
						store: new Ext.ts.JsonStore({
							action: 'getChamps',
							service: 'champ',
							fields: [
								{name: 'idChamp', type: 'int'},
								{name: 'libelle', type: 'string'}
							]
						}),
						valueField: 'idChamp',
						displayField: 'libelle',
						enableKeyEvents: true,
						listeners: {
							keypress: function (field, e) {
								if (e.getKey() == e.ENTER) {
									e.stopEvent();
									createDroitFicheChamp();
								}
							}
						}
					},{
						iconCls: 'add',
						handler: createDroitFicheChamp
					}]
				});
				
				var panel_droitsFiche = new Ext.Panel({
					title: Ext.ts.Lang.droitsFiches,
					hideBorders: true,
					layout: 'border',
					items: [
						grid_droitsFiche,
						grid_droitsFicheChamp
					]
				});
				
				/**
				 * PANEL DROITS TERRITOIRE
				 */
				var store_droitsTerritoire = new Ext.ts.JsonStore({
					id: 'store_droitsTerritoire',
					action: 'getDroitsTerritoire',
					service: 'utilisateurDroitTerritoire',
					baseParams: {idUtilisateur: Ext.ts.params.idUtilisateur},
					fields: [
						{name: 'idTerritoire', type: 'int'},
						{name: 'libelleTerritoire', type: 'string'},
						{name: 'bordereau', type: 'string'},
						{name: 'visualisation', type: 'bool'},
						{name: 'modification', type: 'bool'},
						{name: 'validation', type: 'bool'},
						{name: 'suppressionFiches', type: 'bool'},
						{name: 'creationFiches', type: 'bool'},
						{name: 'administration', type: 'bool'},
						{name: 'idProfil', type: 'int'}
					],
					sortInfo: {field: 'libelleTerritoire', direction: 'ASC'},
					listeners: {
						load: function (store, records) {
							panel_droitsTerritoire.setTitle(Ext.ts.Lang.droitsTerritoires + ' (' + records.length + ')');
						}
					}
				});
				
				var actionColItemsDT = [{
					getClass: function(value, meta, record) {
						return (value ? 'button_ok' : 'button_cancel')
							+ (record.data.idProfil != 0 ? '_gray' : '');
					},
					handler: function(grid, rowIndex, colIndex) {
						var dataIndex = grid.getColumnModel().getDataIndex(colIndex);
						var record = grid.getStore().getAt(rowIndex);
						
						if (record.data.idProfil == 0) {
							record.data[dataIndex] = !record.data[dataIndex];
							editDroitTerritoire(record.data);
						}
						else {
							Ext.Msg.alert(Ext.ts.Lang.failureTitle, Ext.ts.Lang.editDroitError);
						}
					}
				}];
				
				var grid_droitsTerritoire = new Ext.grid.EditorGridPanel({
					id: 'grid_droitsTerritoire',
					region: 'center',
					store: store_droitsTerritoire,
					colModel: new Ext.grid.ColumnModel({
						defaults: {
							xtype: 'actioncolumn',
							width: 90,
							sortable: false
						},
						columns: [{
							xtype: 'gridcolumn',
							header: Ext.ts.Lang.territoire,
							dataIndex: 'libelleTerritoire',
							width: 250,
							renderer: function(value, metaData, record) {
								return value + ' (' + record.data.bordereau + ')';
							}
						},{
							xtype: 'gridcolumn',
							header: Ext.ts.Lang.profil,
							dataIndex: 'idProfil',
							width: 150,
							editor: new Ext.form.ComboBox({
								mode: 'local',
								store: store_profil,
								valueField: 'idProfil',
								displayField: 'libelle',
								hiddenName: 'idProfil',
								triggerAction: 'all',
								editable: false,
								listeners: {
									change: function(combo, value) {
										var selection = grid_droitsTerritoire.getSelectionModel().getSelected();
										Ext.ts.request({
											action: 'setDroitTerritoireProfil',
											service: 'utilisateurDroitTerritoire',
											params: {
												idUtilisateur: Ext.ts.params.idUtilisateur,
												idTerritoire: selection.data.idTerritoire,
												bordereau: selection.data.bordereau,
												idProfil: value
											},
											success: function(response) {
												Ext.getCmp('grid_droitsTerritoire').getStore().reload();
											}
										});
									}
								}
							}),
							renderer: function(value, metaData) {
								var index = store_profil.findExact('idProfil', value);
								if (index == 0) {
									metaData.attr = 'style="color:gray;"';
								}
								return store_profil.getAt(index).data.libelle;
							}
						},{
							header: Ext.ts.Lang.visualisation,
							dataIndex: 'visualisation',
							items: actionColItemsDT
						},{
							header: Ext.ts.Lang.modification,
							dataIndex: 'modification',
							items: actionColItemsDT
						},{
							header: Ext.ts.Lang.validation,
							dataIndex: 'validation',
							items: actionColItemsDT
						},{
							header: Ext.ts.Lang.suppression,
							dataIndex: 'suppressionFiches',
							items: actionColItemsDT
						},{
							header: Ext.ts.Lang.creation,
							dataIndex: 'creationFiches',
							items: actionColItemsDT
						},{
							header: Ext.ts.Lang.administration,
							dataIndex: 'administration',
							items: actionColItemsDT
						},{
							xtype: 'actioncolumn',
							header: Ext.ts.Lang.outils,
							width: 60,
							items: [{
								iconCls: 'delete',
								tooltip: Ext.ts.Lang.supprimer,
								handler: function(grid, rowIndex, colIndex) {
									var record = grid.getStore().getAt(rowIndex);
									deleteDroitTerritoire(record.data.idTerritoire, record.data.bordereau);
								}
							}]
						}]
					}),
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: true,
						listeners: {
							selectionchange: function(sm) {
								if (sm.hasSelection()) {
									var record = sm.getSelected();
									store_droitsTerritoireChamp.setBaseParam('bordereau', record.data.bordereau);
									store_droitsTerritoireChamp.setBaseParam('idTerritoire', record.data.idTerritoire);
									store_droitsTerritoireChamp.setBaseParam('idProfil', record.data.idProfil);
									store_droitsTerritoireChamp.load();
								}
								else {
									store_droitsTerritoireChamp.removeAll();
								}
							}
						}
					}),
					tbar: ['->',{
						xtype: 'autocompletecombo',
						id: 'territoire',
						emptyText: Ext.ts.Lang.selectTerritoire,
						store: new Ext.ts.JsonStore({
							action: 'getTerritoires',
							service: 'territoires',
							fields: [
								{name: 'idTerritoire', type: 'int'},
								{name: 'libelle', type: 'string'}
							]
						}),
						valueField: 'idTerritoire',
						displayField: 'libelle'
					},'-',{
						xtype: 'lovcombobordereau',
						id: 'bordereaux',
						emptyText: Ext.ts.Lang.selectBordereau
					},{
						iconCls: 'add',
						handler: addDroitTerritoire
					}]
				});
				
				var store_droitsTerritoireChamp = new Ext.ts.JsonStore({
					action: 'getDroitTerritoireChamp',
					service: 'utilisateurDroitTerritoire',
					baseParams: {idUtilisateur: Ext.ts.params.idUtilisateur},
					fields: [
						{name: 'idChamp', type: 'int'},
						{name: 'libelle', type: 'string'},
						{name: 'visualisation', type: 'bool'},
						{name: 'modification', type: 'bool'},
						{name: 'validation', type: 'bool'}
					]
				});
				
				var actionColItemsDTC = [{
					getClass: function(value) {
						var idProfil = grid_droitsTerritoire.getSelectionModel().getSelected().data.idProfil;
						return (value ? 'button_ok' : 'button_cancel') + (idProfil != 0 ? '_gray' : '');
					},
					handler: function(grid, rowIndex, colIndex) {
						var dataIndex = grid.getColumnModel().getDataIndex(colIndex);
						var record = grid.getStore().getAt(rowIndex);
						var idProfil = grid_droitsTerritoire.getSelectionModel().getSelected().data.idProfil;
						
						if (idProfil == 0) {
							record.data[dataIndex] = !record.data[dataIndex];
							editDroitTerritoireChamp(record.data);
						}
						else {
							Ext.Msg.alert(Ext.ts.Lang.failureTitle, Ext.ts.Lang.editDroitError);
						}
					},
					scope: this
				}];
				
				var grid_droitsTerritoireChamp = new Ext.grid.GridPanel({
					id: 'grid_droitsTerritoireChamp',
					region: 'east',
					width: 520,
					minWidth: 520,
					header: false,
					collapsible: true,
					collapseMode: 'mini',
					collapsed: false,
					split: true,
					store: store_droitsTerritoireChamp,
					colModel: new Ext.grid.ColumnModel({
						defaults: {
							xtype: 'actioncolumn',
							width: 80,
							sortable: true
						},
						columns: [{
							xtype: 'gridcolumn',
							header: Ext.ts.Lang.champ,
							dataIndex: 'libelle',
							width: 200
						},{
							header: Ext.ts.Lang.visualisation,
							dataIndex: 'visualisation',
							items: actionColItemsDTC
						},{
							header: Ext.ts.Lang.modification,
							dataIndex: 'modification',
							items: actionColItemsDTC
						},{
							header: Ext.ts.Lang.validation,
							dataIndex: 'validation',
							items: actionColItemsDTC
						},{
							header: Ext.ts.Lang.outils,
							dataIndex: 'idChamp',
							width: 50,
							items: [{
								getClass: function(value) {
									var idProfil = grid_droitsTerritoire.getSelectionModel().getSelected().data.idProfil;
									return (idProfil == 0 ? 'delete' : 'no-img');
								},
								tooltip: Ext.ts.Lang.supprimer,
								handler: function(grid, rowIndex, colIndex) {
									var record = grid.getStore().getAt(rowIndex);
									deleteDroitTerritoireChamp(record.data.idChamp);
								},
								scope: this
							}]
						}]
					}),
					tbar: ['->',{
						xtype: 'autocompletecombo',
						id: 'idChampTerritoire',
						emptyText: Ext.ts.Lang.selectChamp,
						store: new Ext.ts.JsonStore({
							action: 'getChamps',
							service: 'champ',
							fields: [
								{name: 'idChamp', type: 'int'},
								{name: 'libelle', type: 'string'}
							]
						}),
						valueField: 'idChamp',
						displayField: 'libelle',
						enableKeyEvents: true,
						listeners: {
							keypress: function (field, e) {
								if (e.getKey() == e.ENTER) {
									e.stopEvent();
									createDroitTerritoireChamp();
								}
							}
						}
					},{
						iconCls: 'add',
						handler: createDroitTerritoireChamp
					}]
				});
				
				var panel_droitsTerritoire = new Ext.Panel({
					title: Ext.ts.Lang.droitsTerritoires,
					hideBorders: true,
					layout: 'border',
					items: [
						grid_droitsTerritoire,
						grid_droitsTerritoireChamp
					]
				});
				
				/**
				 * CONTAINER
				 */
				var container = new Ext.ts.Container({
					title: Ext.ts.Lang.titleContainer,
					selMenu: 'utilisateurs',
					content: new Ext.Panel({
						hideBorders: true,
						layout: 'anchor',
						items: [{
							xtype: 'panel',
							id: 'header_utilisateur',
							height: 126,
							anchor: '100%',
							tpl: new Ext.XTemplate(
								'<div id="user_header">',
									'<div id="user_type"><img src="images/utilisateur.png" alt="{typeUtilisateur}"/>{[values.typeUtilisateur.toUpperCase()]}</div>',
									'<div id="user_identifiants">',
										'<div id="user_login">{email}</div>',
									'</div>',
									'<div><a href="#" onclick="sendPassword({idUtilisateur});">'+Ext.ts.Lang.sendIdentifiants+'</a></div>',
								'</div>'
							)
						},{
							xtype: 'tabpanel',
							id: 'tab_utilisateur',
							anchor: '100%, -126',
							style: 'margin: 5px;',
							border: true,
							resizeTabs: true,
							tabWidth: 175,
							activeTab: 0,
							items: [
								panel_droitsFiche,
								panel_droitsTerritoire
							]
						}]
					})
				});
				
				/**
				 * LOAD -> HEADER
				 */
				Ext.ts.request({
					action: 'getUtilisateur',
					service: 'utilisateur',
					params: {idUtilisateur: Ext.ts.params.idUtilisateur},
					success: function(response) {
						var utilisateur = Ext.decode(response.responseText);
						Ext.getCmp('header_utilisateur').update(utilisateur);
						Ext.getCmp('tab_utilisateur').setActiveTab(
							utilisateur.typeUtilisateur.indexOf('admin', 0) != -1 ? 1 : 0
						);
					}
				});
				
				Ext.ts.onAllStoresLoaded = function() {
					store_droitsFiche.load();
					store_droitsTerritoire.load();
				}
			});
			
			/**
			 * FUNCTION
			 */
			function sendPassword() {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.sendIdentifiantsConfirm,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'sendPassword',
								service: 'utilisateur',
								params: {
									idUtilisateur: Ext.ts.params.idUtilisateur
								},
								success: function(response) {
									var win = new Ext.ts.Notification({
										html: Ext.ts.Lang.identifiantsSent
									});
									win.show();
								}
							});
						}
					},
					this
				);
			}
			
			// FICHES
			
			function addDroitFiche() {
				var idFiche = Ext.getCmp('fiche').getValue();
				
				if (Ext.isEmpty(idFiche)) {
					Ext.Msg.show({
						title: Ext.ts.Lang.failureTitle,
						minWidth: 250,
						msg: Ext.ts.Lang.pleaseSelectFiche,
						buttons: Ext.Msg.OK,
						icon: Ext.Msg.ERROR,
						fn: function() {
							Ext.getCmp('fiche').focus();
						}
					});
					return false;
				}
				
				Ext.ts.request({
					action: 'setDroitFiche',
					service: 'utilisateurDroitFiche',
					params: {
						idUtilisateur: Ext.ts.params.idUtilisateur,
						idFiche: idFiche
					},
					success: function(response) {
						Ext.getCmp('fiche').reset();
						Ext.getCmp('fiche').focus();
						Ext.getCmp('grid_droitsFiche').getStore().reload();
					}
				});
			}

			function editDroitFiche(droits) {
				var params = droits
				params.idUtilisateur = Ext.ts.params.idUtilisateur;
				Ext.ts.request({
					action: 'setDroitFiche',
					service: 'utilisateurDroitFiche',
					params: params,
					success: function(response) {
						Ext.getCmp('grid_droitsFiche').getStore().reload();
					}
				});
			}
			
			function deleteDroitFiche(idFiche) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteDroitFiche,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'deleteDroitFiche',
								service: 'utilisateurDroitFiche',
								params: {
									idUtilisateur: Ext.ts.params.idUtilisateur,
									idFiche: idFiche
								},
								success: function(response) {
									Ext.getCmp('grid_droitsFiche').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function createDroitFicheChamp() {
				var idChamp = Ext.getCmp('idChampFiche').getValue();
				
				if (Ext.isEmpty(idChamp)) {
					Ext.Msg.show({
						title: Ext.ts.Lang.failureTitle,
						minWidth: 250,
						msg: Ext.ts.Lang.pleaseSelectChamp,
						buttons: Ext.Msg.OK,
						icon: Ext.Msg.ERROR,
						fn: function() {
							Ext.getCmp('idChampFiche').focus();
						}
					});
					return false;
				}
				
				var selection = Ext.getCmp('grid_droitsFiche').getSelectionModel().getSelected();
				Ext.ts.request({
					action: 'setDroitFicheChamp',
					service: 'utilisateurDroitFiche',
					params: {
						idUtilisateur: Ext.ts.params.idUtilisateur,
						idFiche: selection.data.idFiche,
						idChamp: idChamp
					},
					success: function(response) {
						Ext.getCmp('idChampFiche').reset();
						Ext.getCmp('idChampFiche').focus();
						Ext.getCmp('grid_droitsFicheChamp').getStore().reload();
					},
					scope: this
				});
			}
			
			function editDroitFicheChamp(droits) {
				var params = droits;
				params.idUtilisateur = Ext.ts.params.idUtilisateur;
				var selection = Ext.getCmp('grid_droitsFiche').getSelectionModel().getSelected();
				params.idFiche = selection.data.idFiche;
				Ext.ts.request({
					action: 'setDroitFicheChamp',
					service: 'utilisateurDroitFiche',
					params: params,
					success: function(response) {
						Ext.getCmp('grid_droitsFicheChamp').getStore().reload();
					}
				});
			}
			
			function deleteDroitFicheChamp(idChamp) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteDroitChamp,
					function (btn) {
						if (btn == 'yes') {
							var selection = Ext.getCmp('grid_droitsFiche').getSelectionModel().getSelected();
							Ext.ts.request({
								action: 'deleteDroitFicheChamp',
								service: 'utilisateurDroitFiche',
								params: {
									idUtilisateur: Ext.ts.params.idUtilisateur,
									idFiche: selection.data.idFiche,
									idChamp: idChamp
								},
								success: function(response) {
									Ext.getCmp('grid_droitsFicheChamp').getStore().reload();
								},
								scope: this
							});
						}
					},
					this
				);
			}
			
			// TERRITOIRES
			
			function addDroitTerritoire() {
				var idTerritoire = Ext.getCmp('territoire').getValue();
				var bordereaux = Ext.getCmp('bordereaux').getValue();
				
				if (Ext.isEmpty(idTerritoire)) {
					Ext.Msg.show({
						title: Ext.ts.Lang.failureTitle,
						minWidth: 250,
						msg: Ext.ts.Lang.pleaseSelectTerritoire,
						buttons: Ext.Msg.OK,
						icon: Ext.Msg.ERROR
					});
					return false;
				}
				if (Ext.isEmpty(bordereaux)) {
					Ext.Msg.show({
						title: Ext.ts.Lang.failureTitle,
						minWidth: 250,
						msg: Ext.ts.Lang.pleaseSelectBordereau,
						buttons: Ext.Msg.OK,
						icon: Ext.Msg.ERROR
					});
					return false;
				}
				
				Ext.ts.request({
					action: 'setDroitTerritoire',
					service: 'utilisateurDroitTerritoire',
					params: {
						idUtilisateur: Ext.ts.params.idUtilisateur,
						idTerritoire: idTerritoire,
						bordereaux: bordereaux
					},
					success: function(response) {
						Ext.getCmp('territoire').reset();
						Ext.getCmp('bordereaux').reset();
						Ext.getCmp('grid_droitsTerritoire').getStore().reload();
					}
				});
			}

			function editDroitTerritoire(droits) {
				var params = droits;
				params.idUtilisateur = Ext.ts.params.idUtilisateur;
				Ext.ts.request({
					action: 'setDroitTerritoire',
					service: 'utilisateurDroitTerritoire',
					params: params,
					success: function(response) {
						Ext.getCmp('grid_droitsTerritoire').getStore().reload();
					}
				});
			}
			
			function deleteDroitTerritoire(idTerritoire, bordereau) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteDroitTerritoire,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'deleteDroitTerritoire',
								service: 'utilisateurDroitTerritoire',
								params: {
									idUtilisateur: Ext.ts.params.idUtilisateur,
									idTerritoire: idTerritoire,
									bordereau: bordereau
								},
								success: function(response) {
									Ext.getCmp('grid_droitsTerritoire').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function createDroitTerritoireChamp() {
				var idChamp = Ext.getCmp('idChampTerritoire').getValue();
				
				if (Ext.isEmpty(idChamp)) {
					Ext.Msg.show({
						title: Ext.ts.Lang.failureTitle,
						minWidth: 250,
						msg: Ext.ts.Lang.pleaseSelectChamp,
						buttons: Ext.Msg.OK,
						icon: Ext.Msg.ERROR,
						fn: function () {
							Ext.getCmp('idChampTerritoire').focus();
						}
					});
					return false;
				}
				
				var selection = Ext.getCmp('grid_droitsTerritoire').getSelectionModel().getSelected();
				Ext.ts.request({
					action: 'setDroitTerritoireChamp',
					service: 'utilisateurDroitTerritoire',
					params: {
						idUtilisateur: Ext.ts.params.idUtilisateur,
						idTerritoire: selection.data.idTerritoire,
						bordereau: selection.data.bordereau,
						idChamp: idChamp
					},
					success: function(response) {
						Ext.getCmp('idChampTerritoire').reset();
						Ext.getCmp('idChampTerritoire').focus();
						Ext.getCmp('grid_droitsTerritoireChamp').getStore().reload();
					},
					scope: this
				});
			}
			
			function editDroitTerritoireChamp(droits) {
				var params = droits;
				params.idUtilisateur = Ext.ts.params.idUtilisateur;
				var selection = Ext.getCmp('grid_droitsTerritoire').getSelectionModel().getSelected();
				params.idTerritoire = selection.data.idTerritoire;
				params.bordereau = selection.data.bordereau;
				Ext.ts.request({
					action: 'setDroitTerritoireChamp',
					service: 'utilisateurDroitTerritoire',
					params: params,
					success: function(response) {
						Ext.getCmp('grid_droitsTerritoireChamp').getStore().reload();
					}
				});
			}
			
			function deleteDroitTerritoireChamp(idChamp) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteDroitChamp,
					function (btn) {
						if (btn == 'yes') {
							var selection = Ext.getCmp('grid_droitsTerritoire').getSelectionModel().getSelected();
							Ext.ts.request({
								action: 'deleteDroitTerritoireChamp',
								service: 'utilisateurDroitTerritoire',
								params: {
									idUtilisateur: Ext.ts.params.idUtilisateur,
									idTerritoire: selection.data.idTerritoire,
									bordereau: selection.data.bordereau,
									idChamp: idChamp
								},
								success: function(response) {
									Ext.getCmp('grid_droitsTerritoireChamp').getStore().reload();
								},
								scope: this
							});
						}
					},
					this
				);
			}
		</script>

<?php
	require_once('include/footer.php');
?>