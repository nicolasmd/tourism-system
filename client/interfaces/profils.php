<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	tsDroits::checkDroit('MENU_PROFIL');
	
	require_once('include/header.php');
?>

		<script type="text/javascript">
			Ext.onReady(function(){
				
				/**
				 * GRID - PROFIL
				 */
				var store_profil = new Ext.ts.JsonStore({
					action: 'getProfils',
					service: 'profilDroit',
					fields: [
						{name: 'idProfil', type: 'int'},
						{name: 'libelle', type: 'string'},
						{name: 'idGroupe', type: 'int'},
						{name: 'nomGroupe', type: 'string'},
						{name: 'visualisation', type: 'bool'},
						{name: 'modification', type: 'bool'},
						{name: 'validation', type: 'bool'},
						{name: 'creationFiches', type: 'bool'},
						{name: 'suppressionFiches', type: 'bool'},
						{name: 'administration', type: 'bool'}
					],
					sortInfo: {field: 'libelle', direction: 'ASC'},
					remoteSort: true
				});
				
				var actionColItems = [{
					getClass: function(value, metaData, record) {
						if (record.data.idGroupe == 0 && Ext.ts.typeUtilisateur != 'root') {
							metaData.css += ' no-actioncolumn';
						}
						return (value ? 'button_ok' : 'button_cancel')
							+ ((record.data.idGroupe == 0 && Ext.ts.typeUtilisateur != 'root') ? '_gray' : '');
					},
					handler: function(grid, rowIndex, colIndex) {
						var record = grid.getStore().getAt(rowIndex);
						if (record.data.idGroupe > 0 || Ext.ts.typeUtilisateur == 'root') {
							var dataIndex = grid.getColumnModel().getDataIndex(colIndex);
							record.data[dataIndex] = !record.data[dataIndex];
							editProfil(record.data);
						}
					},
					scope: this
				}];
				
				var grid_profil = new Ext.grid.GridPanel({
					id: 'grid_profil',
					store: store_profil,
					colModel: new Ext.grid.ColumnModel({
						defaults: {
							xtype: 'actioncolumn',
							width: 90,
							sortable: true
						},
						columns: [{
							xtype: 'gridcolumn',
							header: Ext.ts.Lang.profil,
							dataIndex: 'libelle',
							sortable: true,
							width: 200
						}
						<?php if (PSession::$SESSION['typeUtilisateur'] == 'root') { ?>
						,{
							xtype: 'gridcolumn',
							header: Ext.ts.Lang.groupe,
							dataIndex: 'nomGroupe',
							sortable: true,
							width: 200,
							filterable: true
						}
						<?php } ?> 
						,{
							header: Ext.ts.Lang.visualisation,
							dataIndex: 'visualisation',
							items: actionColItems
						},{
							header: Ext.ts.Lang.modification,
							dataIndex: 'modification',
							items: actionColItems
						},{
							header: Ext.ts.Lang.validation,
							dataIndex: 'validation',
							items: actionColItems
						},{
							header: Ext.ts.Lang.creation,
							dataIndex: 'creationFiches',
							items: actionColItems
						},{
							header: Ext.ts.Lang.suppression,
							dataIndex: 'suppressionFiches',
							items: actionColItems
						},{
							header: Ext.ts.Lang.administration,
							dataIndex: 'administration',
							items: actionColItems
						},{
							xtype: 'actioncolumn',
							header: Ext.ts.Lang.outils,
							width: 60,
							items: [{
								tooltip: Ext.ts.Lang.supprimer,
								getClass: function (value, metaData, record) {
									return (record.data.idGroupe > 0 || Ext.ts.typeUtilisateur == 'root') ? 'delete' : 'no-img';
								},
								handler: function(grid, rowIndex, colIndex) {
									var record = grid.getStore().getAt(rowIndex);
									deleteProfil(record.data.idProfil);
								}
							}]
						}]
					}),
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: true,
						listeners: {
							selectionchange: function(sm) {
								if (sm.hasSelection()) {
									var idProfil = sm.getSelected().data.idProfil;
									store_droitChamp.setBaseParam('idProfil', idProfil);
									store_droitChamp.load();
								}
								else {
									store_droitChamp.removeAll();
								}
								Ext.getCmp('btn_addDroitChamp').setDisabled(
									!sm.hasSelection() || (sm.getSelected().data.idGroupe == 0 && Ext.ts.typeUtilisateur != 'root')
								);
							}
						}
					}),
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							store: store_profil,
							width: 200
						}),'->',{
							text: Ext.ts.Lang.createProfil,
							iconCls: 'add',
							handler: createProfil
						}
					],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_profil,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingProfil,
						emptyMsg: Ext.ts.Lang.pagingProfilEmpty,
						reloadOnResize: true
					}),
					plugins: [
						new Ext.ts.GridFilters()
					]
				});
				
				/**
				 * GRID - DROIT CHAMP
				 */
				var store_droitChamp = new Ext.ts.JsonStore({
					action: 'getProfilDroitChamp',
					service: 'profilDroit',
					fields: [
						{name: 'idChamp', type: 'int'},
						{name: 'libelle', type: 'string'},
						{name: 'visualisation', type: 'bool'},
						{name: 'modification', type: 'bool'},
						{name: 'validation', type: 'bool'}
					],
					sortInfo: {field: 'libelle', direction: 'ASC'},
					remoteSort: true
				});
				
				var actionColItemsDC = [{
					getClass: function(value, metaData) {
						var idGroupe = Ext.getCmp('grid_profil').getSelectionModel().getSelected().data.idGroupe;
						if (idGroupe == 0 && Ext.ts.typeUtilisateur != 'root') {
							metaData.css += ' no-actioncolumn';
						}
						return (value ? 'button_ok' : 'button_cancel')
							+ ((idGroupe == 0 && Ext.ts.typeUtilisateur != 'root') ? '_gray' : '');
					},
					handler: function(grid, rowIndex, colIndex) {
						var profil = Ext.getCmp('grid_profil').getSelectionModel().getSelected();
						if (profil.data.idGroupe > 0 || Ext.ts.typeUtilisateur == 'root') {
							var record = grid.getStore().getAt(rowIndex);
							var dataIndex = grid.getColumnModel().getDataIndex(colIndex);
							record.data[dataIndex] = !record.data[dataIndex];
							record.data.idProfil = profil.data.idProfil;
							editDroitChamp(record.data);
						}
					},
					scope: this
				}];
				
				var grid_droitChamp = new Ext.grid.GridPanel({
					id: 'grid_droitChamp',
					store: store_droitChamp,
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
							items: actionColItemsDC
						},{
							header: Ext.ts.Lang.modification,
							dataIndex: 'modification',
							items: actionColItemsDC
						},{
							header: Ext.ts.Lang.validation,
							dataIndex: 'validation',
							items: actionColItemsDC
						},{
							xtype: 'actioncolumn',
							header: Ext.ts.Lang.outils,
							width: 50,
							items: [{
								getClass: function () {
									var idGroupe = Ext.getCmp('grid_profil').getSelectionModel().getSelected().data.idGroupe;
									return (idGroupe > 0 || Ext.ts.typeUtilisateur == 'root') ? 'delete' : 'no-img';
								},
								tooltip: Ext.ts.Lang.supprimer,
								handler: function(grid, rowIndex, colIndex) {
									var record = grid.getStore().getAt(rowIndex);
									deleteDroitChamp(record.data.idChamp);
								}
							}]
						}]
					}),
					tbar: ['->', {
						id: 'btn_addDroitChamp',
						text: Ext.ts.Lang.addDroitChamp,
						iconCls: 'add',
						handler: addDroitsChamps,
						disabled: true
					}]
				});
				
				/**
				 * CONTAINER
				 */
				var container = new Ext.ts.Container({
					title: Ext.ts.Lang.titleContainer,
					selMenu: 'profils',
					content: new Ext.Panel({
						hideBorders: true,
						layout: 'border',
						items: [{
							xtype: 'panel',
							region: 'center',
							layout: 'fit',
							margins: '5 0 5 5',
							items: grid_profil
						},{
							xtype: 'panel',
							region: 'east',
							layout: 'fit',
							width: 520,
							minWidth: 520,
							margins: '5 5 5 0',
							cmargins: '5 5 5 0',
							header: false,
							collapsible: true,
							collapseMode: 'mini',
							split: true,
							items: grid_droitChamp
						}]
					})
				});
				
				store_profil.load();
				
			});
			
			/**
			 * FUNCTION - PROFIL
			 */
			function createProfil() {
				var items = fieldsProfil();
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.createProfil,
					height: 230,
					items: items,
					action: 'createProfil',
					service: 'profilDroit',
					gridToReload: Ext.getCmp('grid_profil')
				});
				win.show();
			}
			
			function editProfil(droits) {
				Ext.ts.request({
					action: 'updateProfil',
					service: 'profilDroit',
					params: droits,
					success: function(response) {
						Ext.getCmp('grid_profil').getStore().reload();
					}
				});
			}
			
			function deleteProfil(idProfil) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteProfil,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'deleteProfil',
								service: 'profilDroit',
								params: {idProfil: idProfil},
								success: function(response) {
									Ext.getCmp('grid_profil').getStore().reload();
								}
							});
						}
					}
				);
			}
			
			function fieldsProfil(values) {
				var values = Ext.isDefined(values) ? values : {};
				
				var visualisation = new Ext.form.Checkbox({
					fieldLabel: Ext.ts.Lang.droitsParDefaut,
					name: 'visualisation',
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitVisualisation
				});
				visualisation.setValue(values.visualisation);
				var modification = new Ext.form.Checkbox({
					name: 'modification',
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitModification,
					handler: function (cb, checked) {
						visualisation.setDisabled(checked);
						if (checked) {
							visualisation.setValue(checked);
						}
					}
				});
				modification.setValue(values.modification);
				var validation = new Ext.form.Checkbox({
					name: 'validation',
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitValidation
				});
				validation.setValue(values.validation);
				var creationFiches = new Ext.form.Checkbox({
					name: 'creationFiches',
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitCreationFiches
				});
				creationFiches.setValue(values.creationFiches);
				var suppressionFiches = new Ext.form.Checkbox({
					name: 'suppressionFiches',
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitSuppressionFiches
				});
				suppressionFiches.setValue(values.suppressionFiches);
				var administration = new Ext.form.Checkbox({
					name: 'administration',
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitAdministration,
					handler: function (cb, checked) {
						visualisation.setDisabled(checked);
						modification.setDisabled(checked);
						validation.setDisabled(checked);
						creationFiches.setDisabled(checked);
						suppressionFiches.setDisabled(checked);
						if (checked) {
							visualisation.setValue(checked);
							modification.setValue(checked);
							validation.setValue(checked);
							creationFiches.setValue(checked);
							suppressionFiches.setValue(checked);
						}
					}
				});
				administration.setValue(values.administration);
				
				return [{
					xtype: 'textfield',
					id: 'libelle',
					fieldLabel: Ext.ts.Lang.profil,
					width: 250,
					allowBlank: false,
					value: values.libelle
				}
				<?php if (PSession::$SESSION['typeUtilisateur'] == 'root') { ?>
				,{
					xtype: 'autocompletecombo',
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
					hiddenName: 'idGroupe',
					allowBlank: true,
					readOnly: Ext.isDefined(values.idGroupe),
					listeners: {
						render: function(combo) {
							if (Ext.isDefined(values.idGroupe)) {
								combo.setValue(values.idGroupe);
							}
						}
					}
				}
				<?php } ?> 
				,
					visualisation,
					modification,
					validation,
					creationFiches,
					suppressionFiches,
					administration
				];
			}
			
			/**
			 * FUNCTION - DROITS CHAMPS
			 */
			function addDroitChamp() {
				var selection = Ext.getCmp('grid_profil').getSelectionModel().getSelected();
				var items = fieldsDroitChamp();
				
				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.addDroitChamp,
					height: 130,
					items: items,
					action: 'setProfilDroitChamp',
					service: 'profilDroit',
					params: {
						idProfil: selection.data.idProfil
					},
					gridToReload: Ext.getCmp('grid_droitChamp'),
					closeWin: false
				});
				win.show();
			}
			
			function addDroitsChamps() {
				var idProfil = Ext.getCmp('grid_profil').getSelectionModel().getSelected().data.idProfil;
				
				var visualisation = new Ext.form.Checkbox({
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitVisualisation
				});
				var modification = new Ext.form.Checkbox({
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitModification,
					handler: function (cb, checked) {
						visualisation.setDisabled(checked);
						if (checked) {
							visualisation.setValue(checked);
						}
					}
				});
				var validation = new Ext.form.Checkbox({
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitValidation
				});
				
				var gridL = new Ext.grid.GridPanel({
					ddGroup: 'GridRDDGroup',
					store: new Ext.ts.JsonStore({
						action: 'getChamps',
						service: 'champ',
						autoLoad: true,
						root: 'dataRoot',
						totalProperty: 'dataCount',
						fields: [
							{name: 'idChamp', type: 'int'},
							{name: 'libelle', type: 'string'},
							{name: 'bordereau', type: 'string'}
						],
						sortInfo: {field: 'libelle', direction: 'ASC'},
						listeners: {
							load: function (store) {
								Ext.ts.request({
									action: 'getProfilDroitChamp',
									service: 'profilDroit',
									params: {idProfil: idProfil},
									success: function(response) {
										var champs = Ext.decode(response.responseText);
										var toTransfer = [];
										Ext.each(champs.dataRoot, function(champ) {
											var r = store.getAt(store.findExact('idChamp', champ.idChamp));
											if (Ext.isDefined(r)) {
												toTransfer.push(r);
											}
										});
										transferItems(toTransfer, gridL, gridR);
									}
								});
							}
						}
					}),
					columns: [{
						id: 'expand',
						header: Ext.ts.Lang.listeChamps,
						dataIndex: 'libelle',
						sortable: true
					},{
						header: Ext.ts.Lang.bordereau,
						dataIndex: 'bordereau',
						sortable: true,
						hidden: true
					}],
					autoExpandColumn : 'expand',
					enableDragDrop: true,
					plugins: new Ext.ts.gridKeySearch({
						dataIndex: 'libelle',
						enterHandler: function(record) {
							transferItems([record], gridL, gridR);
						}
					})
				});
				
				var gridR = new Ext.grid.EditorGridPanel({
					ddGroup: 'GridLDDGroup',
					store: new Ext.data.JsonStore({
						fields: [
							{name: 'idChamp', type: 'int'},
							{name: 'libelle', type: 'string'}
						],
						sortInfo: {field: 'libelle', direction: 'ASC'}
					}),
					sm: new Ext.grid.RowSelectionModel(),
					columns: [{
						id: 'expand',
						header: Ext.ts.Lang.profilDroitChamps,
						dataIndex: 'libelle',
						sortable: true
					}],
					autoExpandColumn : 'expand',
					enableDragDrop: true
				});
				
				var transferItems = function(records, src, target) {
					Ext.each(records, function(record) {
						src.getStore().remove(record);
						target.getStore().add(record);
					});
					target.getStore().sort('libelle', 'ASC');
				};
				
				var win = new Ext.Window({
					title: Ext.ts.Lang.addDroitChamp,
					width: 640,
					height: 480,
					hideBorders: true,
					layout: 'anchor',
					items: [{
						height: 50,
						anchor: '100%',
						layout: 'form',
						padding: '10px',
						items: [{
							xtype: 'checkboxgroup',
							fieldLabel: Ext.ts.Lang.droits,
							items: [
								visualisation,
								modification,
								validation
							]
						}]
					},{
						anchor: '100% -50',
						layout: 'hbox',
						defaults: {flex: 1},
						layoutConfig: {align: 'stretch'},
						items: [gridL, gridR]
					}],
					buttons: [{
						text: Ext.ts.Lang.valider,
						handler: function() {
							var champs = [];
							gridR.getStore().each(function(record) {
								champs.push(record.data.idChamp)
							});
							Ext.ts.request({
								action: 'setProfilDroitChamps',
								service: 'profilDroit',
								params: {
									idProfil: idProfil,
									champs: champs.join(','),
									visualisation: visualisation.getValue(),
									modification: modification.getValue(),
									validation: validation.getValue()
								},
								success: function(response) {
									win.destroy();
									Ext.getCmp('grid_droitChamp').getStore().reload();
								}
							});
						}
					},{
						text: Ext.ts.Lang.annuler,
						handler: function() {
							win.destroy();
						}
					}],
					listeners: {
						show: function () {
							// <=
							var GridLDropTargetEl = gridL.getView().scroller.dom;
							var GridLDropTarget = new Ext.dd.DropTarget(GridLDropTargetEl, {
								ddGroup: 'GridLDDGroup',
								notifyDrop: function(ddSource, e, data){
									var records = ddSource.dragData.selections;
									transferItems(records, gridR, gridL);
									return true;
								}
							});
							// =>
							var GridRDropTargetEl = gridR.getView().scroller.dom;
							var GridRDropTarget = new Ext.dd.DropTarget(GridRDropTargetEl, {
								ddGroup: 'GridRDDGroup',
								notifyDrop: function(ddSource, e, data){
									var records = ddSource.dragData.selections;
									transferItems(records, gridL, gridR);
									return true;
								}
							});
						}
					}
				});
				win.show();
			}
			
			function editDroitChamp(droits) {
				Ext.ts.request({
					action: 'setProfilDroitChamp',
					service: 'profilDroit',
					params: droits,
					success: function(response) {
						Ext.getCmp('grid_droitChamp').getStore().reload();
					}
				});
			}
			
			function deleteDroitChamp(idChamp) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteDroitChamp,
					function (btn) {
						if (btn == 'yes') {
							var idProfil = Ext.getCmp('grid_profil').getSelectionModel().getSelected().data.idProfil;
							Ext.ts.request({
								action: 'deleteProfilDroitChamp',
								service: 'profilDroit',
								params: {
									idProfil: idProfil,
									idChamp: idChamp
								},
								success: function(response) {
									Ext.getCmp('grid_droitChamp').getStore().removeAll();
								}
							});
						}
					}
				);
			}
			
			function fieldsDroitChamp(values) {
				var values = Ext.isDefined(values) ? values : {};
				
				var visualisation = new Ext.form.Checkbox({
					fieldLabel: Ext.ts.Lang.droits,
					name: 'visualisation',
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitVisualisation
				});
				visualisation.setValue(values.visualisation);
				var modification = new Ext.form.Checkbox({
					name: 'modification',
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitModification,
					handler: function (cb, checked) {
						visualisation.setDisabled(checked);
						if (checked) {
							visualisation.setValue(checked);
						}
					}
				});
				modification.setValue(values.modification);
				var validation = new Ext.form.Checkbox({
					name: 'validation',
					inputValue: 'true',
					boxLabel: Ext.ts.Lang.droitValidation
				});
				validation.setValue(values.validation);
				
				return [{
					xtype: 'autocompletecombo',
					fieldLabel: Ext.ts.Lang.champ,
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
					hiddenName: 'idChamp',
					allowBlank: false,
					listeners: {
						render: function(combo) {
							if (Ext.isDefined(values.idChamp)) {
								combo.setValue(values.idChamp);
							}
						}
					}
				},
					visualisation,
					modification,
					validation
				];
			}
		</script>

<?php
	require_once('include/footer.php');
?>