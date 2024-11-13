<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	tsDroits::checkDroit('MENU_PLUGIN');
	
	require_once('include/header.php');
?>

		<script type="text/javascript">
			Ext.onReady(function(){
				
				/**
				 * GRID - PLUGIN
				 */
				var store_plugin = new Ext.ts.JsonStore({
					action: 'getPlugins',
					service: 'plugin',
					fields: [
						{name: 'nomPlugin', type: 'string'},
						{name: 'version', type: 'string'},
						{name: 'actif', type: 'string'},
						{name: 'cle', type: 'bool'},
						{name: 'serveurMaj', type: 'string'},
						{name: 'dateMaj', type: 'date', dateFormat: 'Y-m-d H:i:s'},
						{name: 'newVersion', type: 'boolean'}
					],
					sortInfo: {field: 'nomPlugin', direction: 'ASC'},
					remoteSort: true
				});
				
				var grid_plugin = new Ext.grid.GridPanel({
					id: 'grid_plugin',
					store: store_plugin,
					columns: [{
						header: Ext.ts.Lang.plugin,
						dataIndex: 'nomPlugin',
						sortable: true,
						width: 200
					},{
						header: Ext.ts.Lang.version,
						dataIndex: 'version',
						sortable: true,
						width: 80
					},{
						header: Ext.ts.Lang.acces,
						dataIndex: 'cle',
						sortable: true,
						width: 50,
						renderer: function(value) {
							return '<img src="images/'+(value ? 'key.png' : 'group.png')+'" title="'+(value ? Ext.ts.Lang.restreint : Ext.ts.Lang.publik)+'" />';
						}
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.etat,
						width: 50,
						items: [{
							getClass: function(value, medaData, record) {
								return 	record.data.actif == 'N' ? 'lightbulb_off' :
										record.data.actif == 'Y' ? 'lightbulb' :
										'no-img';
							},
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								if (record.data.actif == 'N') {
									enablePlugin(record.data.nomPlugin);
								}
								else if (record.data.actif == 'Y') {
									disablePlugin(record.data.nomPlugin);
								}
							}
						}]
					},{	
						xtype: 'datecolumn',
						header: Ext.ts.Lang.dateMaj,
						dataIndex: 'dateMaj',
						format: 'd F Y H:i:s',
						sortable: true,
						width: 150
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 70,
						items: [{
							getClass: function(value, medaData, record) {
								return record.data.actif == '' ? 'application_add' : 'application_delete';
							},
							tooltip: Ext.ts.Lang.installUninstall,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								if (record.data.actif == '') {
									installPlugin(record.data);
								}
								else {
									uninstallPlugin(record.data.nomPlugin);
								}
							}
						},{
							getClass: function(value, medaData, record) {
								return record.data.actif != '' && record.data.newVersion
									? 'arrow_refresh' : 'no-img';
							},
							tooltip: Ext.ts.Lang.update,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								if (record.data.actif != '' && record.data.newVersion) {
									updatePlugin(record.data.nomPlugin);
								}
							}
						}]
					}],
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: false,
						listeners: {
							rowselect: function(selModel, rowId, record) {
								store_groupe.setBaseParam('nomPlugin', record.data.nomPlugin);
								store_groupe.load();
							},
							selectionchange: function(sm) {
								Ext.getCmp('field_searchGroupe').setDisabled(!sm.hasSelection());
								Ext.getCmp('btn_addAllGroupes').setDisabled(sm.getCount() != 1);
								Ext.getCmp('btn_deleteAllGroupes').setDisabled(sm.getCount() != 1);
								if (!sm.hasSelection()) { store_groupe.removeAll(); }
							}
						}
					}),
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							store: store_plugin,
							width: 200
						})
					],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_plugin,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingPlugin,
						emptyMsg: Ext.ts.Lang.pagingPluginEmpty,
						reloadOnResize: true
					})
				});
				
				/**
				 * GRID - GROUPE
				 */
				var store_groupe = new Ext.ts.JsonStore({
					action: 'getPluginGroupes',
					service: 'plugin',
					fields: [
						{name: 'idGroupe', type: 'int'},
						{name: 'nomGroupe', type: 'string'},
						{name: 'actif', type: 'boolean'},
					],
					sortInfo: {field: 'nomGroupe', direction: 'ASC'},
					remoteSort: true
				});
				
				var grid_groupe = new Ext.grid.GridPanel({
					id: 'grid_groupe',
					store: store_groupe,
					columns: [{
						header: Ext.ts.Lang.groupe,
						dataIndex: 'nomGroupe',
						sortable: true,
						width: 300
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.etat,
						dataIndex: 'actif',
						width: 50,
						items: [{
							getClass: function(value, medaData, record) {
								return value ? 'tick' : 'tick_off';
							},
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								setGroupePlugin(record.data.idGroupe, record.data.actif);
							}
						}],
						filterable: true
					}],
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							id: 'field_searchGroupe',
							store: store_groupe,
							width: 200,
							disabled: true
						}),'->',{
						id: 'btn_addAllGroupes',
						text: Ext.ts.Lang.addAllGroupes,
						iconCls: 'tick',
						disabled: true,
						handler: addAllGroupes
					},{
						id: 'btn_deleteAllGroupes',
						text: Ext.ts.Lang.deleteAllGroupes,
						iconCls: 'tick_off',
						disabled: true,
						handler: deleteAllGroupes
					}],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_groupe,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingGroupe,
						emptyMsg: Ext.ts.Lang.pagingGroupeEmpty,
						reloadOnResize: true
					}),
					plugins: [
						new Ext.ts.GridFilters()
					]
				});
				
				/**
				 * CONTAINER
				 */
				var container = new Ext.ts.Container({
					title: Ext.ts.Lang.titleContainer,
					selMenu: 'plugins',
					content: new Ext.Panel({
						hideBorders: true,
						layout: 'border',
						items: [{
							xtype: 'panel',
							region: 'center',
							layout: 'fit',
							margins: '5 0 5 5',
							items: grid_plugin
						},{
							xtype: 'panel',
							region: 'east',
							layout: 'fit',
							width: 600,
							minWidth: 250,
							maxWidth: 600,
							margins: '5 5 5 0',
							cmargins: '5 5 5 0',
							header: false,
							collapsible: true,
							collapseMode: 'mini',
							split: true,
							items: grid_groupe
						}]
					})
				});
				
				store_plugin.load();
				
			});
			
			/**
			 * FUNCTION - PLUGIN
			 */
			function installPlugin(data) {
				if (data.cle === true) {
					Ext.MessageBox.prompt(
						Ext.ts.Lang.confirmTitle,
						Ext.ts.Lang.installProtectedPlugin,
						function (btn, cle) {
							if (btn == 'ok') {
								Ext.ts.request({
									action: 'installPlugin',
									service: 'plugin',
									params: {
										nomPlugin: data.nomPlugin,
										cle: cle
									},
									success: function(response) {
										Ext.getCmp('grid_plugin').getStore().reload();
									}
								});
							}
						}
					);
				}
				else {
					Ext.MessageBox.confirm(
						Ext.ts.Lang.confirmTitle,
						Ext.ts.Lang.installPlugin,
						function (btn) {
							if (btn == 'yes') {
								Ext.ts.request({
									action: 'installPlugin',
									service: 'plugin',
									params: {nomPlugin: data.nomPlugin},
									success: function(response) {
										Ext.getCmp('grid_plugin').getStore().reload();
									}
								});
							}
						}
					);
				}
			}
			
			function uninstallPlugin(nomPlugin) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.uninstallPlugin,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'uninstallPlugin',
								service: 'plugin',
								params: {nomPlugin: nomPlugin},
								success: function(response) {
									Ext.getCmp('grid_plugin').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function enablePlugin(nomPlugin) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.enablePlugin,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'enablePlugin',
								service: 'plugin',
								params: {nomPlugin: nomPlugin},
								success: function(response) {
									Ext.getCmp('grid_plugin').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function disablePlugin(nomPlugin) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.disablePlugin,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'disablePlugin',
								service: 'plugin',
								params: {nomPlugin: nomPlugin},
								success: function(response) {
									Ext.getCmp('grid_plugin').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function updatePlugin(nomPlugin) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.updatePlugin,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'updatePlugin',
								service: 'plugin',
								params: {nomPlugin: nomPlugin},
								success: function(response) {
									Ext.getCmp('grid_plugin').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			/**
			 * FUNCTION - GROUPE
			 */
			function setGroupePlugin(idGroupe, etat) {
				var selection = Ext.getCmp('grid_plugin').getSelectionModel().getSelected();
				var nomPlugin = selection.data.nomPlugin;
				
				Ext.ts.request({
					action: etat ? 'deleteGroupePlugin' : 'addGroupePlugin',
					service: 'groupes',
					params: {
						nomPlugin: nomPlugin,
						idGroupe: idGroupe
					},
					success: function(response) {
						Ext.getCmp('grid_groupe').getStore().reload();
					},
					scope: this
				});
			}
			
			function addAllGroupes() {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.addAllGroupesConfirm,
					function (btn) {
						if (btn == 'yes') {
							var selection = Ext.getCmp('grid_plugin').getSelectionModel().getSelected();
							var nomPlugin = selection.data.nomPlugin;
							
							Ext.ts.request({
								action: 'addGroupesPlugin',
								service: 'groupes',
								params: {
									nomPlugin: nomPlugin
								},
								success: function(response) {
									Ext.getCmp('grid_groupe').getStore().reload();
								},
								scope: this
							});
						}
					},
					this
				);
			}
			
			function deleteAllGroupes() {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteAllGroupesConfirm,
					function (btn) {
						if (btn == 'yes') {
							var selection = Ext.getCmp('grid_plugin').getSelectionModel().getSelected();
							var nomPlugin = selection.data.nomPlugin;
							
							Ext.ts.request({
								action: 'deleteGroupesPlugin',
								service: 'groupes',
								params: {
									nomPlugin: nomPlugin
								},
								success: function(response) {
									Ext.getCmp('grid_groupe').getStore().reload();
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