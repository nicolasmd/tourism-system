<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	tsDroits::checkDroit('MENU_TERRITOIRE');
	
	require_once('include/header.php');
?>

		<script type="text/javascript">
			Ext.onReady(function(){
				
				/**
				 * GRID - TERRITOIRE
				 */
				var store_territoire = new Ext.ts.JsonStore({
					action: 'getTerritoires',
					service: 'territoires',
					fields: [
						{name: 'idTerritoire', type: 'int'},
						{name: 'libelle', type: 'string'}
					],
					sortInfo: {field: 'libelle', direction: 'ASC'},
					remoteSort: true
				});
				
				var grid_territoire = new Ext.grid.GridPanel({
					id: 'grid_territoire',
					store: store_territoire,
					columns: [{
						header: Ext.ts.Lang.territoire,
						dataIndex: 'libelle',
						sortable: true,
						width: 200
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 60,
						items: [{
							iconCls: 'edit',
							tooltip: Ext.ts.Lang.modifier,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								editTerritoire(record.data);
							}
						},{
							iconCls: 'delete',
							tooltip: Ext.ts.Lang.supprimer,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								deleteTerritoire(record.data.idTerritoire);
							}
						}]
					}],
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: true,
						listeners: {
							rowselect: function(selModel, rowId, record) {
								store_commune.setBaseParam('idTerritoire', record.data.idTerritoire);
								store_commune.load();
								store_thesaurus.setBaseParam('idTerritoire', record.data.idTerritoire);
								store_thesaurus.load();
							},
							selectionchange: function(sm) {
								Ext.getCmp('field_searchCommune').setDisabled(!sm.hasSelection());
								Ext.getCmp('btn_assocCommune').setDisabled(!sm.hasSelection());
								Ext.getCmp('btn_assocThesaurus').setDisabled(!sm.hasSelection());
								if (!sm.hasSelection()) { store_commune.removeAll(); }
								if (!sm.hasSelection()) { store_thesaurus.removeAll(); }
							}
						}
					}),
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							store: store_territoire,
							width: 200
						}),'->',{
							text: Ext.ts.Lang.createTerritoire,
							iconCls: 'add',
							handler: createTerritoire
						}
					],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_territoire,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingTerritoire,
						emptyMsg: Ext.ts.Lang.pagingTerritoireEmpty,
						reloadOnResize: true
					})
				});
				
				/**
				 * GRID - COMMUNE
				 */
				var store_commune = new Ext.ts.JsonStore({
					action: 'getCommunesByTerritoire',
					service: 'territoires',
					fields: [
						{name: 'codeInsee', type: 'string'},
						{name: 'codePostal', type: 'string'},
						{name: 'libelle', type: 'string'},
						{name: 'gpsLat', type: 'float'},
						{name: 'gpsLng', type: 'float'},
						{name: 'prive', type: 'boolean'}
					],
					sortInfo: {field: 'libelle', direction: 'ASC'},
					remoteSort: true
				});
				
				var grid_commune = new Ext.grid.GridPanel({
					id: 'grid_commune',
					forceLayout: true,
					flex: 3,
					store: store_commune,
					columns: [{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.prive,
						dataIndex: 'prive',
						width: 50,
						items: [{
							getClass: function(value) {
								return value ? 'key' : 'key_gray';
							},
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								setCommuneTerritoirePrive(record.data.codeInsee, !record.data.prive);
							}
						}]
					},{
						id: 'expand',
						header: Ext.ts.Lang.commune,
						dataIndex: 'libelle',
						sortable: true,
						width: 150
					},{
						header: Ext.ts.Lang.codeInsee,
						dataIndex: 'codeInsee',
						sortable: true,
						width: 80
					},{
						header: Ext.ts.Lang.codePostal,
						dataIndex: 'codePostal',
						sortable: true,
						width: 80
					},{
						header: Ext.ts.Lang.latitude,
						dataIndex: 'gpsLat',
						sortable: true,
						width: 100
					},{
						header: Ext.ts.Lang.longitude,
						dataIndex: 'gpsLng',
						sortable: true,
						width: 100
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 50,
						items: [{
							iconCls: 'link_break',
							tooltip: Ext.ts.Lang.desassocier,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								disassociateCommuneTerritoire(record.data.codeInsee);
							}
						}]
					}],
					autoExpandColumn: 'expand',
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							id: 'field_searchCommune',
							store: store_commune,
							width: 200,
							disabled: true
						}),'->',{
						id: 'btn_assocCommune',
						text: Ext.ts.Lang.addTerritoireCommune,
						iconCls: 'add',
						disabled: true,
						handler: associateCommuneTerritoire
					}],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_commune,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingCommune,
						emptyMsg: Ext.ts.Lang.pagingCommuneEmpty,
						reloadOnResize: true
					})
				});
				
				/**
				 * GRID - THESAURUS
				 */
				var store_thesaurus = new Ext.ts.JsonStore({
					action: 'getThesaurusByTerritoire',
					service: 'territoires',
					fields: [
						{name: 'codeThesaurus', type: 'string'},
						{name: 'libelle', type: 'string'},
						{name: 'prefixe', type: 'int'}
					],
					sortInfo: {field: 'libelle', direction: 'ASC'},
					remoteSort: true
				});
				
				var grid_thesaurus = new Ext.grid.GridPanel({
					id: 'grid_thesaurus',
					forceLayout: true,
					flex: 1,
					store: store_thesaurus,
					columns: [{
						header: Ext.ts.Lang.code,
						dataIndex: 'codeThesaurus',
						sortable: true,
						width: 150
					},{
						header: Ext.ts.Lang.thesaurus,
						dataIndex: 'libelle',
						sortable: true,
						width: 250
					},{
						header: Ext.ts.Lang.prefixe,
						dataIndex: 'prefixe',
						sortable: true,
						width: 50
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 50,
						items: [{
							iconCls: 'link_break',
							tooltip: Ext.ts.Lang.desassocier,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								disassociateThesaurusTerritoire(record.data.codeThesaurus);
							}
						}]
					}],
					tbar: ['->',{
						id: 'btn_assocThesaurus',
						text: Ext.ts.Lang.addTerritoireThesaurus,
						iconCls: 'add',
						disabled: true,
						handler: associateThesaurusTerritoire
					}],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_thesaurus,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingThesaurus,
						emptyMsg: Ext.ts.Lang.pagingThesaurusEmpty,
						reloadOnResize: true
					})
				});
				
				/**
				 * CONTAINER
				 */
				var container = new Ext.ts.Container({
					title: Ext.ts.Lang.titleContainer,
					selMenu: 'territoires',
					content: new Ext.Panel({
						hideBorders: true,
						layout: 'border',
						items: [{
							xtype: 'panel',
							region: 'center',
							layout: 'fit',
							margins: '5 0 5 5',
							items: grid_territoire
						},{
							xtype: 'panel',
							region: 'east',
							layout: 'vbox',
							width: 650,
							minWidth: 650,
							maxWidth: 650,
							margins: '5 5 5 0',
							cmargins: '5 5 5 0',
							header: false,
							collapsible: true,
							collapseMode: 'mini',
							split: true,
							items: [
								grid_commune,
								grid_thesaurus
							]
						}]
					})
				});
				
				store_territoire.load();
				
			});
			
			/**
			 * FUNCTION - TERRITOIRE
			 */
			function createTerritoire() {
				var items = fieldsTerritoire();
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.createTerritoire,
					height: 60,
					items: items,
					action: 'createTerritoire',
					service: 'territoires',
					gridToReload: Ext.getCmp('grid_territoire')
				});
				win.show();
			}
			
			function editTerritoire(data) {
				var items = fieldsTerritoire(data);
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.updateTerritoire,
					height: 60,
					items: items,
					action: 'updateTerritoire',
					service: 'territoires',
					params: {idTerritoire: data.idTerritoire},
					gridToReload: Ext.getCmp('grid_territoire')
				});
				win.show();
			}
			
			function deleteTerritoire(idTerritoire) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteTerritoire,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'deleteTerritoire',
								service: 'territoires',
								params: {idTerritoire: idTerritoire},
								success: function(response) {
									Ext.getCmp('grid_territoire').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function fieldsTerritoire(values) {
				var values = Ext.isDefined(values) ? values : {};
				
				return [{
					xtype: 'textfield',
					id: 'libelle',
					fieldLabel: Ext.ts.Lang.territoire,
					width: 250,
					allowBlank: false,
					value: values.libelle
				}];
			}
			
			/**
			 * FUNCTION - COMMUNE
			 */
			function associateCommuneTerritoire() {
				var idTerritoire = Ext.getCmp('grid_territoire').getSelectionModel().getSelected().data.idTerritoire;
				
				var store = new Ext.ts.JsonStore({
					action: 'getCommunes',
					service: 'territoires',
					fields: [
						{name: 'codeInsee', type: 'string'},
						{name: 'codePostal', type: 'string'},
						{name: 'libelle', type: 'string'}
					]
				});
				var sm = new Ext.grid.CheckboxSelectionModel();
				var grid = new Ext.grid.GridPanel({
					height: 450,
					store: store,
					columns: [sm,{
						header: Ext.ts.Lang.commune,
						dataIndex: 'libelle',
						sortable: true,
						width: 250
					},{
						header: Ext.ts.Lang.codePostal,
						dataIndex: 'codePostal',
						sortable: true,
						width: 100,
						filter: {
							type: 'string',
							listeners: {
								serialize: function(args) {
									args.comparison = 'start';
								}
							}
						}
					},{
						header: Ext.ts.Lang.codeInsee,
						dataIndex: 'codeInsee',
						sortable: true,
						width: 100,
						filter: {
							type: 'string',
							listeners: {
								serialize: function(args) {
									args.comparison = 'start';
								}
							}
						}
					}],
					sm: sm,
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							store: store,
							width: 200
						})
					],
					bbar: new Ext.ts.AutoSizePaging({
						store: store,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingCommune,
						emptyMsg: Ext.ts.Lang.pagingCommuneEmpty
					}),
					plugins: new Ext.ts.GridFilters()
				});
				var win = new Ext.Window({
					title: Ext.ts.Lang.addTerritoireCommune,
					width: 600,
					height: 'auto',
					border: false,
					resizable: true,
					maximizable: true,
					modal: true,
					closeAction: 'close',
					layout: 'fit',
					buttonAlign: 'center',
					items: grid,
					buttons: [{
						text: Ext.ts.Lang.ajouter,
						handler: function() {
							var sm = grid.getSelectionModel();
							if (sm.getCount() > 0) {
								var selection = sm.getSelections();
								var arrCodeInsee = [];
								Ext.each(selection, function(item) {
									arrCodeInsee.push(item.data.codeInsee);
								});
								
								Ext.ts.request({
									action: 'addCommuneTerritoire',
									service: 'territoires',
									params: {
										idTerritoire: idTerritoire,
										codeInsee: arrCodeInsee.join(',')
									},
									success: function(response) {
										Ext.getCmp('grid_commune').getStore().reload();
									}
								});
							}
						}
					},{
						text: Ext.ts.Lang.fermer,
						handler: function() {
							win.destroy();
						}
					}]
				});
				win.show();
			}
			
			function disassociateCommuneTerritoire(codeInsee) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteTerritoireCommune,
					function (btn) {
						if (btn == 'yes') {
							var idTerritoire = Ext.getCmp('grid_territoire').getSelectionModel().getSelected().data.idTerritoire;
							Ext.ts.request({
								action: 'deleteCommuneTerritoire',
								service: 'territoires',
								params: {codeInsee: codeInsee, idTerritoire: idTerritoire},
								success: function(response) {
									Ext.getCmp('grid_commune').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function setCommuneTerritoirePrive(codeInsee, prive) {
				var idTerritoire = Ext.getCmp('grid_territoire').getSelectionModel().getSelected().data.idTerritoire;
				Ext.ts.request({
					action: 'setCommuneTerritoirePrive',
					service: 'territoires',
					params: {
						codeInsee: codeInsee,
						idTerritoire: idTerritoire,
						prive: prive
					},
					success: function(response) {
						Ext.getCmp('grid_commune').getStore().reload();
					}
				});
			}
			
			/**
			 * FUNCTION - THESAURUS
			 */
			function associateThesaurusTerritoire() {
				var idTerritoire = Ext.getCmp('grid_territoire').getSelectionModel().getSelected().data.idTerritoire;
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.addTerritoireThesaurus,
					height: 60,
					items: [{
						xtype: 'autocompletecombo',
						id: 'f_thesaurus',
						fieldLabel: Ext.ts.Lang.thesaurus,
						store: new Ext.ts.JsonStore({
							action: 'getThesaurii',
							service: 'thesaurus',
							fields: [
								{name: 'codeThesaurus'},
								{name: 'libelle'}
							]
						}),
						valueField: 'codeThesaurus',
						displayField: 'libelle',
						hiddenName: 'codeThesaurus',
						allowBlank: false
					}],
					action: 'addThesaurusTerritoire',
					service: 'territoires',
					params: {
						idTerritoire: idTerritoire
					},
					gridToReload: Ext.getCmp('grid_thesaurus')
				});
				win.show();
			}
			
			function disassociateThesaurusTerritoire(codeThesaurus) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteTerritoireThesaurus,
					function (btn) {
						if (btn == 'yes') {
							var idTerritoire = Ext.getCmp('grid_territoire').getSelectionModel().getSelected().data.idTerritoire;
							Ext.ts.request({
								action: 'deleteThesaurusTerritoire',
								service: 'territoires',
								params: {codeThesaurus: codeThesaurus, idTerritoire: idTerritoire},
								success: function(response) {
									Ext.getCmp('grid_thesaurus').getStore().reload();
								}
							});
						}
					}
				);
			}
		</script>

<?php
	require_once('include/footer.php');
?>