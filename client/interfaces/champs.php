<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	tsDroits::checkDroit('MENU_CHAMP');
	
	require_once('include/header.php');
?>

		<script type="text/javascript">
			Ext.onReady(function(){
				
				/**
				 * GRID - CHAMPS
				 */
				var store_champ = new Ext.ts.JsonStore({
					action: 'getChamps',
					service: 'champ',
					fields: [
						{name: 'idChamp', type: 'int'},
						{name: 'libelle', type: 'string'},
						{name: 'identifiant', type: 'string'},
						{name: 'liste', type: 'string'},
						{name: 'xPath', type: 'string'},
						{name: 'champs', type: 'auto'},
						{name: 'bordereau', type: 'auto'}
					],
					sortInfo: {field: 'libelle', direction: 'ASC'},
					remoteSort: true
				});
				
				var grid_champ = new Ext.grid.GridPanel({
					id: 'grid_champ',
					store: store_champ,
					autoExpandColumn: 'expandcol2',
					columns: [{
						header: Ext.ts.Lang.libelle,
						dataIndex: 'libelle',
						sortable: true,
						width: 200
					},{
						header: Ext.ts.Lang.identifiant,
						dataIndex: 'identifiant',
						sortable: true,
						width: 200
					},{
						header: Ext.ts.Lang.liste,
						dataIndex: 'liste',
						sortable: true,
						width: 150
					},{
						header: Ext.ts.Lang.bordereau,
						dataIndex: 'bordereau',
						sortable: true,
						width: 150
					},{
						id: 'expandcol2',
						header: Ext.ts.Lang.requeteXpath,
						dataIndex: 'xPath',
						sortable: true,
						width: 200
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 70,
						items: [{
							iconCls: 'add',
							tooltip: Ext.ts.Lang.ajouter,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								addSousChamp(record.data.idChamp);
							}
						},{
							iconCls: 'edit',
							tooltip: Ext.ts.Lang.modifier,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								editChamp(record.data);
							}
						},{
							iconCls: 'delete',
							tooltip: Ext.ts.Lang.supprimer,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								deleteChamp(record.data.idChamp);
							}
						}]
					}],
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: true,
						listeners: {
							rowselect: function(selModel, rowId, record) {
								store_champ2.loadData(record.data);
							},
							selectionchange: function(sm) {
								if (!sm.hasSelection()) { store_champ2.removeAll(); }
							}
						}
					}),
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							store: store_champ,
							width: 200
						}),'->',{
							text: Ext.ts.Lang.createChamp,
							iconCls: 'add',
							handler: createChamp
						}
					],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_champ,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingChamp,
						emptyMsg: Ext.ts.Lang.pagingChampEmpty,
						reloadOnResize: true
					})
				});
				
				/**
				 * GRID - CHAMPS
				 */
				var store_champ2_record = Ext.data.Record.create([
					{name: 'idChamp', type: 'int'},
					{name: 'libelle', type: 'string'},
					{name: 'identifiant', type: 'string'},
					{name: 'liste', type: 'string'},
					{name: 'xPath', type: 'string'}
				]);
				var store_champ2_reader = new Ext.data.JsonReader({
					root: 'champs'
				}, store_champ2_record);
				var store_champ2 = new Ext.data.Store({
					reader: store_champ2_reader,
					sortInfo: {field: 'libelle', direction: 'ASC'}
				});
				
				var grid_champ2 = new Ext.grid.GridPanel({
					id: 'grid_champ2',
					store: store_champ2,
					autoExpandColumn: 'expandcol2',
					columns: [{
						header: Ext.ts.Lang.libelle,
						dataIndex: 'libelle',
						sortable: true,
						width: 200
					},{
						header: Ext.ts.Lang.identifiant,
						dataIndex: 'identifiant',
						sortable: true,
						width: 200
					},{
						header: Ext.ts.Lang.liste,
						dataIndex: 'liste',
						sortable: true,
						width: 200
					},{
						id: 'expandcol2',
						header: Ext.ts.Lang.requeteXpath,
						dataIndex: 'xPath',
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
								editSousChamp(record.data);
							}
						},{
							iconCls: 'delete',
							tooltip: Ext.ts.Lang.supprimer,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								deleteSousChamp(record.data.idChamp);
							}
						}]
					}]
				});
				
				/**
				 * CONTAINER
				 */
				var container = new Ext.ts.Container({
					title: Ext.ts.Lang.titleContainer,
					selMenu: 'champs',
					content: new Ext.Panel({
						hideBorders: true,
						layout: 'border',
						items: [{
							xtype: 'panel',
							region: 'center',
							layout: 'fit',
							margins: '5 5 0 5',
							items: grid_champ
						},{
							xtype: 'panel',
							region: 'south',
							layout: 'fit',
							height: 300,
							minHeight: 100,
							maxHeight: 500,
							margins: '0 5 5 5',
							cmargins: '0 5 5 5',
							header: false,
							collapsible: true,
							collapseMode: 'mini',
							split: true,
							items: grid_champ2
						}]
					})
				});
				
				store_champ.load();
				
			});
			
			/**
			 * FUNCTION - CHAMPS
			 */
			function createChamp() {
				var items = fieldsChamp();
				items.push(checkboxBordereau());
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.createChamp,
					width: 550,
					height: 380,
					items: items,
					action: 'createChamp',
					service: 'champ',
					gridToReload: Ext.getCmp('grid_champ')
				});
				win.show();
			}
			
			function editChamp(data) {
				var items = fieldsChamp(data);
				items.push(checkboxBordereau(data));
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.updateChamp,
					width: 550,
					height: 380,
					items: items,
					action: 'updateChamp',
					service: 'champ',
					params: {idChamp: data.idChamp},
					gridToReload: Ext.getCmp('grid_champ')
				});
				win.show();
			}
			
			function deleteChamp(idChamp) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteChamp,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'deleteChamp',
								service: 'champ',
								params: {idChamp: idChamp},
								success: function(response) {
									Ext.getCmp('grid_champ').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function addSousChamp(idChamp) {
				var items = fieldsChamp();
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.createChamp,
					width: 550,
					height: 220,
					items: items,
					action: 'createChamp',
					service: 'champ',
					params: {idChamp: idChamp},
					gridToReload: Ext.getCmp('grid_champ')
				});
				win.show();
			}
			
			function editSousChamp(data) {
				var items = fieldsChamp(data);
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.updateChamp,
					width: 550,
					height: 220,
					items: items,
					action: 'updateChamp',
					service: 'champ',
					params: {idChamp: data.idChamp},
					gridToReload: Ext.getCmp('grid_champ')
				});
				win.show();
			}
			
			function deleteSousChamp(idChamp) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteChamp,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'deleteChamp',
								service: 'champ',
								params: {idChamp: idChamp},
								success: function(response) {
									Ext.getCmp('grid_champ').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function fieldsChamp(values) {
				var values = Ext.isDefined(values) ? values : {};
				
				return [{
					xtype: 'textfield',
					id: 'libelle',
					fieldLabel: Ext.ts.Lang.libelle,
					width: 250,
					allowBlank: false,
					value: values.libelle
				},{
					xtype: 'textfield',
					id: 'identifiant',
					fieldLabel: Ext.ts.Lang.identifiant,
					width: 250,
					allowBlank: false,
					value: values.identifiant
				},{
					xtype: 'textfield',
					id: 'liste',
					fieldLabel: Ext.ts.Lang.liste,
					width: 250,
					allowBlank: true,
					value: values.liste
				},{
					xtype: 'textarea',
					id: 'xPath',
					fieldLabel: Ext.ts.Lang.requeteXpath,
					width: 370,
					height: 100,
					allowBlank: false,
					value: values.xPath
				}];
			}
			
			function checkboxBordereau(values) {
				var values = Ext.isDefined(values) ? values : {};
				
				var bordereaux = Ext.isDefined(values.bordereau) && !Ext.isEmpty(values.bordereau)
					? values.bordereau.split(',') 
					: [];
				
				var checkboxgroup = [];
				for (var i in Ext.ts.bordereaux) {
					checkboxgroup.push({
						name: 'bordereaux[]',
						boxLabel: Ext.ts.bordereaux[i],
						inputValue: i,
						checked: (bordereaux.indexOf(i) != -1)
					});
				}
				return {
					xtype: 'checkboxgroup',
					id: 'bordereaux',
					fieldLabel: Ext.ts.Lang.bordereaux,
					columns: 2,
					style: 'margin-top: 10px;',
					items: checkboxgroup
				};
			}
		</script>

<?php
	require_once('include/footer.php');
?>