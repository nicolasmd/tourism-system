<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	tsDroits::checkDroit('MENU_GROUPE');
	
	require_once('include/header.php');
?>

		<script type="text/javascript">
			Ext.onReady(function(){
				
				/**
				 * GRID - GROUPE
				 */
				/*var store_groupe = new Ext.ts.JsonStore({
					action: 'getGroupes',
					service: 'groupes',
					fields: [
						{name: 'idSuperAdmin', type: 'int'},
						{name: 'email', type: 'string'},
						{name: 'idGroupe', type: 'int'},
						{name: 'nomGroupe', type: 'string'}
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
						width: 200
					},{
						header: Ext.ts.Lang.superAdmin,
						dataIndex: 'email',
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
								editGroupe(record.data);
							}
						},{
							iconCls: 'delete',
							tooltip: Ext.ts.Lang.supprimer,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								deleteGroupe(record.data.idGroupe);
							}
						}]
					}],
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: true,
						listeners: {
							rowselect: function(selModel, rowId, record) {
								store_territoire.setBaseParam('idGroupe', record.data.idGroupe);
								store_territoire.load();
							},
							selectionchange: function(sm) {
								Ext.getCmp('btn_assocTerritoire').setDisabled(!sm.hasSelection());
								if (!sm.hasSelection()) { store_territoire.removeAll(); }
							}
						}
					}),
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							store: store_groupe,
							width: 200
						}),'->',{
							text: Ext.ts.Lang.createGroupe,
							iconCls: 'add',
							handler: createGroupe
						}
					],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_groupe,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingGroupe,
						emptyMsg: Ext.ts.Lang.pagingGroupeEmpty,
						reloadOnResize: true
					})
				});*/
				
				var tree_groupe = new Ext.ux.tree.TreeGrid({
					id: 'tree_groupe',
					useArrows: true,
					rootVisible: false,
					root: new Ext.tree.AsyncTreeNode({ id: '0' }),
					loader: new Ext.tree.TreeLoader({
						dataUrl: Ext.ts.url({
							action: 'getGroupesTree',
							service: 'groupes',
						}),
						nodeParameter: 'idGroupe',
						createNode: function(attr) {
							attr.id = attr.idGroupe;
							attr.iconCls = 'no-img';
							return Ext.tree.TreeLoader.prototype.createNode.call(this, attr);
						}
					}),
					columns:[{
						header: Ext.ts.Lang.groupe,
						dataIndex: 'nomGroupe',
						width: 300
					},{
						header: Ext.ts.Lang.descriptionGroupe,
						dataIndex: 'descriptionGroupe',
						width: 300,
						tpl: new Ext.XTemplate('{[this.renderer(values)]}', {
							renderer: function(values) {
								return !Ext.isEmpty(values.descriptionGroupe) ? values.descriptionGroupe : '';
							}
						})
					},{
						header: Ext.ts.Lang.superAdmin,
						dataIndex: 'email',
						width: 200,
						tpl: new Ext.XTemplate('{[this.renderer(values)]}', {
							renderer: function(values) {
								return !Ext.isEmpty(values.email) ? values.email : '';
							}
						})
					},{
						header: Ext.ts.Lang.outils,
						width: 80,
						dataIndex: 'idGroupe',
						tpl: new Ext.XTemplate('{[this.renderer(values)]}', {
							renderer: function(values) {
								return '<img class="x-action-col-icon add-action" title="Ajouter" src="images/add.png" />'
									+ '<img class="x-action-col-icon edit-action" title="Modifier" src="images/edit.png" />'
									+ '<img class="x-action-col-icon delete-action" title="Supprimer" src="images/trash.png" />';
							}
						})
					}],
					selModel: new Ext.tree.DefaultSelectionModel({
						listeners: {
							selectionchange: function(sm, node) {
								if (!Ext.isEmpty(node)) {
									store_territoire.setBaseParam('idGroupe', node.attributes.idGroupe);
									store_territoire.load();
									store_partenaire.setBaseParam('idGroupe', node.attributes.idGroupe);
									store_partenaire.load();
								}
								else {
									store_territoire.removeAll();
									store_partenaire.removeAll();
								}
								Ext.getCmp('btn_assocTerritoire').setDisabled(Ext.isEmpty(node));
								Ext.getCmp('btn_assocPartenaire').setDisabled(Ext.isEmpty(node));
							}
						}
					}),
					tbar: ['->',{
						text: Ext.ts.Lang.createGroupe,
						iconCls: 'add',
						handler: function() {
							createGroupe(0, Ext.getCmp('tree_groupe').getRootNode());
						}
					}],
					listeners: {
						click: function(node, e) {
							var element = new Ext.Element(e.getTarget());
							if (element.hasClass('add-action')) {
								createGroupe(node.attributes.idGroupe, node);
							}
							else if (element.hasClass('edit-action')) {
								editGroupe(node.attributes, node.parentNode);
							}
							else if (element.hasClass('delete-action')) {
								deleteGroupe(node.attributes.idGroupe, node.parentNode);
							}
						}
					}
				});
				
				/**
				 * GRID - TERRITOIRE
				 */
				var store_territoire = new Ext.ts.JsonStore({
					action: 'getGroupeTerritoires',
					service: 'groupes',
					fields: [
						{name: 'idTerritoire', type: 'int'},
						{name: 'libelle', type: 'string'}
					],
					sortInfo: {field: 'libelle', direction: 'ASC'},
					remoteSort: true
				});
				
				var grid_territoire = new Ext.grid.GridPanel({
					id: 'grid_territoire',
					forceLayout: true,
					store: store_territoire,
					columns: [{
						header: Ext.ts.Lang.territoire,
						dataIndex: 'libelle',
						sortable: true,
						width: 200
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 50,
						items: [{
							iconCls: 'link_break',
							tooltip: Ext.ts.Lang.desassocier,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								disassociateTerritoireGroupe(record.data.idTerritoire);
							}
						}]
					}],
					tbar: ['->',{
						id: 'btn_assocTerritoire',
						text: Ext.ts.Lang.addTerritoireGroupe,
						iconCls: 'add',
						disabled: true,
						handler: associateTerritoireGroupe
					}],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_territoire,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingTerritoire,
						emptyMsg: Ext.ts.Lang.pagingTerritoireEmpty,
						reloadOnResize: true
					})
				});
				
				
				
				/**
				 * GRID - PARTENAIRE
				 */
				var store_partenaire = new Ext.ts.JsonStore({
					action: 'getGroupePartenaires',
					service: 'groupes',
					fields: [
						{name: 'idGroupe', type: 'int'},
						{name: 'nomGroupe', type: 'string'},
						{name: 'typePartenaire', type: 'string'}
					],
					sortInfo: {field: 'nomGroupe', direction: 'ASC'},
					remoteSort: true
				});
				
				var grid_partenaire = new Ext.grid.GridPanel({
					id: 'grid_partenaire',
					forceLayout: true,
					store: store_partenaire,
					columns: [{
						header: Ext.ts.Lang.partenaire,
						dataIndex: 'nomGroupe',
						sortable: true,
						width: 200
					},{
						header: Ext.ts.Lang.typePartenaire,
						dataIndex: 'typePartenaire',
						sortable: true,
						width: 150
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 50,
						items: [{
							iconCls: 'link_break',
							tooltip: Ext.ts.Lang.desassocier,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								disassociatePartenaireGroupe(record.data.idGroupe);
							}
						}]
					}],
					tbar: ['->',{
						id: 'btn_assocPartenaire',
						text: Ext.ts.Lang.addPartenaireGroupe,
						iconCls: 'add',
						disabled: true,
						handler: associatePartenaireGroupe
					}],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_partenaire,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingPartenaire,
						emptyMsg: Ext.ts.Lang.pagingPartenaireEmpty,
						reloadOnResize: true
					})
				});
				
				/**
				 * CONTAINER
				 */
				var container = new Ext.ts.Container({
					title: Ext.ts.Lang.titleContainer,
					selMenu: 'groupes',
					content: new Ext.Panel({
						hideBorders: true,
						layout: 'border',
						items: [{
							xtype: 'panel',
							region: 'center',
							layout: 'fit',
							margins: '5 0 5 5',
							items: tree_groupe
						},{
							xtype: 'panel',
							region: 'east',
							layout: 'vbox',
							width: 600,
							minWidth: 600,
							maxWidth: 600,
							margins: '5 5 5 0',
							cmargins: '5 5 5 0',
							header: false,
							collapsible: true,
							collapseMode: 'mini',
							split: true,
							defaults: {
								flex: 1,
								align: 'stretch'
							},
							items: [
								grid_territoire,
								grid_partenaire
							]
						}]
					})
				});
				
			});
			
			/**
			 * FUNCTION - GROUPE
			 */
			function createGroupe(idGroupe, node) {
				var items = fieldsGroupe();
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.createGroupe,
					width: 490,
					height: 80,
					labelWidth: 160,
					items: items,
					action: 'createGroupe',
					service: 'groupes',
					params: {idGroupe: idGroupe},
					treeToReload: Ext.getCmp('tree_groupe'),
					nodeToReload: node
				});
				win.show();
			}
			
			function editGroupe(data, node) {
				var items = fieldsGroupe(data);
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.updateGroupe,
					width: 490,
					height: 100,
					labelWidth: 160,
					items: items,
					action: 'editGroupe',
					service: 'groupes',
					params: {idGroupe: data.idGroupe},
					treeToReload: Ext.getCmp('tree_groupe'),
					nodeToReload: node
				});
				win.show();
			}
			
			function deleteGroupe(idGroupe, node) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteGroupeMsg,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'deleteGroupe',
								service: 'groupes',
								params: {idGroupe: idGroupe},
								success: function(response) {
									Ext.getCmp('tree_groupe').getLoader().load(node);
								}
							});
						}
					}
				);
			}
			
			function fieldsGroupe(values) {
				var values = Ext.isDefined(values) ? values : {};
				
				var fields = [{
					xtype: 'textfield',
					id: 'nomGroupe',
					fieldLabel: Ext.ts.Lang.groupe,
					width: 250,
					allowBlank: false,
					value: values.nomGroupe
				},{
					xtype: 'textfield',
					id: 'descriptionGroupe',
					fieldLabel: Ext.ts.Lang.descriptionGroupe,
					width: 250,
					allowBlank: true,
					value: values.descriptionGroupe
				}];
				
				if (Ext.isDefined(values.idGroupe)) {
					fields.push({
						xtype: 'autocompletecombo',
						id: 'f_idSuperAdmin',
						fieldLabel: Ext.ts.Lang.superAdmin,
						store: new Ext.ts.JsonStore({
							action: 'getAdmins',
							service: 'utilisateur',
							fields: [
								{name: 'idUtilisateur', type: 'int'},
								{name: 'email', type: 'string'}
							]
						}),
						valueField: 'idUtilisateur',
						displayField: 'email',
						hiddenName: 'idSuperAdmin',
						allowBlank: true,
						listeners: {
							added: function() {
								if (values.idSuperAdmin != 0) {
									Ext.getCmp('f_idSuperAdmin').setValue(values.idSuperAdmin);
								}
							}
						}
					});
				}
				
				return fields;
			}
			
			/**
			 * FUNCTION - TERRITOIRE
			 */
			function associateTerritoireGroupe() {
				var idGroupe = Ext.getCmp('tree_groupe').getSelectionModel().getSelectedNode().attributes.idGroupe;
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.addTerritoireGroupeTitle,
					height: 60,
					items: [{
						xtype: 'autocompletecombo',
						id: 'f_territoire',
						fieldLabel: Ext.ts.Lang.territoire,
						store: new Ext.ts.JsonStore({
							action: 'getTerritoires',
							service: 'territoires',
							autoLoad: true,
							fields: [
								{name: 'idTerritoire'},
								{name: 'libelle'}
							]
						}),
						valueField: 'idTerritoire',
						displayField: 'libelle',
						hiddenName: 'idTerritoire',
						allowBlank: false
					}],
					action: 'addGroupeTerritoire',
					service: 'groupes',
					params: {
						idGroupe: idGroupe
					},
					gridToReload: Ext.getCmp('grid_territoire')
				});
				win.show();
			}
			
			function disassociateTerritoireGroupe(idTerritoire) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteTerritoireGroupeMsg,
					function (btn) {
						if (btn == 'yes') {
							var idGroupe = Ext.getCmp('tree_groupe').getSelectionModel().getSelectedNode().attributes.idGroupe;
							Ext.ts.request({
								action: 'deleteGroupeTerritoire',
								service: 'groupes',
								params: {idTerritoire: idTerritoire, idGroupe: idGroupe},
								success: function(response) {
									Ext.getCmp('grid_territoire').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			/**
			 * FUNCTION - PARTENAIRE
			 */
			function associatePartenaireGroupe() {
				var idGroupe = Ext.getCmp('tree_groupe').getSelectionModel().getSelectedNode().attributes.idGroupe;
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.addPartenaireGroupeTitle,
					height: 80,
					items: [{
						xtype: 'autocompletecombo',
						id: 'f_partenaire',
						fieldLabel: Ext.ts.Lang.groupe,
						store: new Ext.ts.JsonStore({
							action: 'getGroupes',
							service: 'groupes',
							autoLoad: true,
							fields: [
								{name: 'idGroupe'},
								{name: 'nomGroupe'}
							]
						}),
						valueField: 'idGroupe',
						displayField: 'nomGroupe',
						hiddenName: 'idGroupePartenaire',
						allowBlank: false
					},{
						xtype: 'combo',
						hiddenName: 'typePartenaire',
						fieldLabel: Ext.ts.Lang.typePartenaire,
						width: 250,
						mode: 'local',
						triggerAction: 'all',
						store: [
							'exclude',
							'include'
						],
						allowBlank: false
					}],
					action: 'addGroupePartenaire',
					service: 'groupes',
					params: {
						idGroupe: idGroupe
					},
					gridToReload: Ext.getCmp('grid_partenaire')
				});
				win.show();
			}
			
			function disassociatePartenaireGroupe(idGroupePartenaire) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deletePartenaireGroupeMsg,
					function (btn) {
						if (btn == 'yes') {
							var idGroupe = Ext.getCmp('tree_groupe').getSelectionModel().getSelectedNode().attributes.idGroupe;
							Ext.ts.request({
								action: 'deleteGroupePartenaire',
								service: 'groupes',
								params: {idGroupe: idGroupe, idGroupePartenaire: idGroupePartenaire},
								success: function(response) {
									Ext.getCmp('grid_partenaire').getStore().reload();
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