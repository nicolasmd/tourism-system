<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	tsDroits::checkDroit('MENU_UTILISATEUR');
	
	require_once('include/header.php');
?>

		<script type="text/javascript">
			Ext.onReady(function(){
				
				/**
				 * GRID - UTILISATEUR
				 */
				var store_utilisateur = new Ext.ts.JsonStore({
					action: 'getUtilisateurs',
					service: 'utilisateur',
					fields: [
						{name: 'idUtilisateur', type: 'int'},
						{name: 'email', type: 'string'},
						<?php if (tsDroits::getDroit('UTILISATEUR_PASSWORD') || isAuthorizedIP()) { ?> 
						{name: 'password', type: 'string'},
						<?php } ?> 
						{name: 'typeUtilisateur', type: 'string'},
						<?php if (tsDroits::getDroit('UTILISATEUR_GROUPE')) { ?> 
						{name: 'idGroupe', type: 'int'},
						{name: 'nomGroupe', type: 'string'}
						<?php } ?> 
					],
					sortInfo: {field: 'email', direction: 'ASC'},
					remoteSort: true
				});
				
				var grid_utilisateur = new Ext.grid.EditorGridPanel({
					id: 'grid_utilisateur',
					store: store_utilisateur,
					columns: [{
						header: Ext.ts.Lang.email,
						dataIndex: 'email',
						sortable: true,
						width: 200,
						editor: new Ext.form.TextField({
							style: 'margin-top: 2px',
							readOnly: true,
							selectOnFocus: true
						})
					},
					<?php if (tsDroits::getDroit('UTILISATEUR_PASSWORD') || isAuthorizedIP()) { ?> 
					{
						header: Ext.ts.Lang.password,
						dataIndex: 'password',
						sortable: true,
						width: 100,
						editor: new Ext.form.TextField({
							style: 'margin-top: 2px',
							readOnly: true,
							selectOnFocus: true
						})
					},
					<?php } ?> 
					{
						header: Ext.ts.Lang.typeUtilisateur,
						dataIndex: 'typeUtilisateur',
						sortable: true,
						width: 200,
						filter: {
							type: 'list',
							options: [
								{id: 'desk',text: 'desk'},
								{id: 'manager',text: 'manager'},
								{id: 'admin',text: 'admin'},
								{id: 'superadmin',text: 'superadmin'}
							]
						}
					}
					<?php if (tsDroits::getDroit('UTILISATEUR_GROUPE')) { ?> 
					,{
						header: Ext.ts.Lang.groupe,
						dataIndex: 'nomGroupe',
						sortable: true,
						width: 200,
						filterable: true
					}
					<?php } ?> 
					,{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 100,
						items: [{
							iconCls: 'time',
							tooltip: Ext.ts.Lang.voirHistoriqueSessions,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								getSessions(record.data);
							}
						},{
							iconCls: 'brick_edit',
							tooltip: Ext.ts.Lang.configurerUtilisateur,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								Ext.ts.open('utilisateur', {idUtilisateur: record.data.idUtilisateur});
							}
						},{
							iconCls: 'edit',
							tooltip: Ext.ts.Lang.modifier,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								updateUtilisateur(record.data);
							}
						},{
							iconCls: 'delete',
							tooltip: Ext.ts.Lang.supprimer,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								deleteUtilisateur(record.data.idUtilisateur);
							}
						}]
					}],
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: true
					}),
					trackMouseOver: true,
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							store: store_utilisateur,
							width: 200
						}),'->',{
							text: Ext.ts.Lang.createUtilisateur,
							iconCls: 'add',
							handler: createUtilisateur
						}
					],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_utilisateur,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingUtilisateur,
						emptyMsg: Ext.ts.Lang.pagingUtilisateurEmpty,
						reloadOnResize: true
					}),
					plugins: new Ext.ts.GridFilters()
				});
				
				/**
				 * CONTAINER
				 */
				var container = new Ext.ts.Container({
					title: Ext.ts.Lang.titleContainer,
					content: new Ext.Panel({
						hideBorders: true,
						layout: 'border',
						items: [{
							xtype: 'panel',
							region: 'center',
							layout: 'fit',
							margins: '5 5 5 5',
							items: grid_utilisateur
						}]
					})
				});
				
				store_utilisateur.load();
				
			});
			
			/**
			 * FUNCTION - UTILISATEUR
			 */
			function getSessions(utilisateur) {
				var store_session = new Ext.ts.JsonStore({
					action: 'getSessions',
					service: 'utilisateur',
					baseParams: {
						idUtilisateur: utilisateur.idUtilisateur
					},
					autoLoad: true,
					fields: [
						{name: 'sessionStart', type: 'date', dateFormat: 'Y-m-d H:i:s'},
						{name: 'sessionEnd', type: 'date', dateFormat: 'Y-m-d H:i:s'}
					],
					sortInfo: {field: 'sessionStart', direction: 'DESC'},
					remoteSort: true
				});
				var grid_session = new Ext.grid.GridPanel({
					id: 'grid_session',
					height: 400,
					store: store_session,
					colModel: new Ext.grid.ColumnModel({
						defaults: {
							xtype: 'datecolumn',
							format: 'd/m/Y H:i:s',
							sortable: true,
							width: 200
						},
						columns: [{
							header: Ext.ts.Lang.debut,
							dataIndex: 'sessionStart'
						},{
							header: Ext.ts.Lang.fin,
							dataIndex: 'sessionEnd'
						}]
					}),
					bbar: new Ext.ts.AutoSizePaging({
						store: store_session,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingSession,
						emptyMsg: Ext.ts.Lang.pagingSessionEmpty
					})
				});
				var win = new Ext.Window({
					title: Ext.ts.Lang.historiqueSessions
						+ ' - ' + utilisateur.email,
					width: 600,
					height: 'auto',
					hideBorders: true,
					resizable: true,
					maximizable: true,
					modal: true,
					closeAction: 'close',
					layout: 'fit',
					items: grid_session
				});
				win.show();
			}
			
			function createUtilisateur() {
				var items = fieldsUtilisateur();
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.createUtilisateur,
					height: 100,
					labelWidth: 140,
					items: items,
					action: 'createUtilisateur',
					service: 'utilisateur',
					callback: function(form, action) {
						self.location = 'utilisateur.php?idUtilisateur='+action.result.reponse;
					}
				});
				win.show();
			}
			
			function updateUtilisateur(data) {
				var items = fieldsUtilisateur(data);
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.updateUtilisateur,
					height: 130,
					labelWidth: 140,
					items: items,
					action: 'updateUtilisateur',
					service: 'utilisateur',
					params: {idUtilisateur: data.idUtilisateur},
					gridToReload: Ext.getCmp('grid_utilisateur')
				});
				win.show();
			}
			
			function deleteUtilisateur(idUtilisateur) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteUtilisateur,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'deleteUtilisateur',
								service: 'utilisateur',
								params: {idUtilisateur: idUtilisateur},
								success: function(response) {
									Ext.getCmp('grid_utilisateur').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function fieldsUtilisateur(values) {
				var values = Ext.isDefined(values) ? values : {};
				
				var fields = [{
					xtype: 'textfield',
					name: 'email',
					fieldLabel: Ext.ts.Lang.email,
					width: 250,
					vtype: 'email',
					readOnly: Ext.isDefined(values.email),
					allowBlank: false,
					value: values.email
				}];
				
				if (Ext.isDefined(values.password)) {
					fields.push({
						xtype: 'textfield',
						name: 'oldPassword',
						fieldLabel: Ext.ts.Lang.ancien,
						width: 250,
						inputType: 'password'
						<?php if (tsDroits::getDroit('UTILISATEUR_PASSWORD') || isAuthorizedIP()) { ?> 
						,value: values.password
						<?php } ?>
					},{
						xtype: 'textfield',
						name: 'password',
						fieldLabel: Ext.ts.Lang.nouveau,
						width: 250,
						inputType: 'password'
					});
				}
				
				if (!Ext.isDefined(values.typeUtilisateur)) {
					fields.push({
						xtype: 'combo',
						hiddenName: 'typeUtilisateur',
						fieldLabel: Ext.ts.Lang.typeUtilisateur,
						width: 250,
						mode: 'local',
						triggerAction: 'all',
						store: [
							Ext.ts.Lang.desk,
							Ext.ts.Lang.manager,
							Ext.ts.Lang.admin
						],
						allowBlank: false,
						value: values.typeUtilisateur
					});
				}
				
				<?php if (tsDroits::getDroit('UTILISATEUR_GROUPE')) { ?> 
				fields.push({
					xtype: 'autocompletecombo',
					id: 'f_idGroupe',
					hiddenName: 'idGroupe',
					fieldLabel: Ext.ts.Lang.groupe,
					store: new Ext.ts.JsonStore({
						action: 'getGroupes',
						service: 'groupes',
						fields: [
							{name: 'idGroupe', type: 'int'},
							{name: 'nomGroupe', type: 'string'}
						]
					}),
					valueField: 'idGroupe',
					displayField: 'nomGroupe',
					allowBlank: false,
					listeners: {
						added: function() {
							if (Ext.isDefined(values.idGroupe)) {
								Ext.getCmp('f_idGroupe').setValue(values.idGroupe);
							}
						}
					}
				});
				<?php } ?> 
				
				return fields;
			}
		</script>

<?php
	require_once('include/footer.php');
?>