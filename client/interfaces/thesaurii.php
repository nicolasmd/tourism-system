<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	tsDroits::checkDroit('MENU_THESAURUS');

	require_once('include/header.php');
?>

		<script type="text/javascript">
			Ext.onReady(function(){

				/**
				 * GRID - THESAURUS
				 */
				var store_thesaurus = new Ext.ts.JsonStore({
					action: 'getThesaurii',
					service: 'thesaurus',
					fields: [
						{name: 'codeThesaurus', type: 'string'},
						{name: 'libelle', type: 'string'},
						{name: 'prefixe', type: 'string'}
					],
					sortInfo: {field: 'prefixe', direction: 'ASC'},
					remoteSort: true
				});

				var grid_thesaurus = new Ext.grid.GridPanel({
					id: 'grid_thesaurus',
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
						width: 60,
						items: [{
							iconCls: 'edit',
							tooltip: Ext.ts.Lang.modifier,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								editThesaurus(record.data);
							}
						},{
							iconCls: 'page_white_get',
							tooltip: Ext.ts.Lang.exporter,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								exportThesaurus(record.data.codeThesaurus);
							}
						}]
					}],
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: false,
						listeners: {
							selectionchange: function(sm) {
								Ext.getCmp('field_searchEntree').setDisabled(!sm.hasSelection());
								Ext.getCmp('btn_addEntree').setDisabled(sm.getCount() != 1);
								if (sm.hasSelection()) {
									var codesThesaurii = [];
									Ext.each(sm.getSelections(), function(record) {
										codesThesaurii.push(record.data.codeThesaurus);
									});
									store_entree.setBaseParam('codesThesaurii', codesThesaurii.join(','));
									store_entree.load();
								}
								else {
									store_entree.removeAll();
								}
							}
						}
					}),
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							store: store_thesaurus,
							width: 200
						}),'->',{
							text: Ext.ts.Lang.createThesaurus,
							iconCls: 'add',
							handler: createThesaurus
						}
					],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_thesaurus,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingThesaurus,
						emptyMsg: Ext.ts.Lang.pagingThesaurusEmpty,
						reloadOnResize: true
					})
				});

				/**
				 * GRID - ENTREE
				 */
				var store_entree = new Ext.ts.JsonStore({
					action: 'getEntreesThesaurii',
					service: 'thesaurus',
					fields: [
						{name: 'cle', type: 'string'},
						{name: 'liste', type: 'string'},
						{name: 'lang', type: 'string'},
						{name: 'libelle', type: 'string'}
					],
					sortInfo: {field: 'cle', direction: 'ASC'},
					remoteSort: true
				});

				var grid_entree = new Ext.grid.EditorGridPanel({
					id: 'grid_entree',
					forceLayout: true,
					store: store_entree,
					columns: [{
						header: Ext.ts.Lang.cle,
						dataIndex: 'cle',
						sortable: true,
						width: 200,
						filter: {
							type: 'string',
							listeners: {
								serialize: function(args) {
									args.comparison = 'regex';
									args.value = '/^((99|[0-9]{3})\.)?'+args.value+'/';
								}
							}
						},
						editor: new Ext.form.TextField({
							style: 'margin-top: 2px',
							readOnly: true,
							selectOnFocus: true
						})
					},{
						header: Ext.ts.Lang.liste,
						dataIndex: 'liste',
						sortable: true,
						width: 150,
						filterable: true,
						editor: new Ext.form.TextField({
							style: 'margin-top: 2px',
							readOnly: true,
							selectOnFocus: true
						})
					},{
						header: Ext.ts.Lang.langue,
						dataIndex: 'lang',
						sortable: true,
                        filterable : true ,
                        filter : {
                            type    : 'list' ,
                            active : true,
							value : 'fr',
                            options : [
                                {
                                    id   : 'fr' ,
                                    text : 'fr'
                                },
                                {
                                    id   : 'en' ,
                                    text : 'en'
                                },
                                {
                                    id   : 'de' ,
                                    text : 'de'
                                },
                                {
                                    id   : 'es' ,
                                    text : 'es'
                                },
                                {
                                    id   : 'it' ,
                                    text : 'it'
                                },
                                {
                                    id   : 'nl' ,
                                    text : 'nl'
                                }
                            ]
                        },
						width: 60
					},{
						header: Ext.ts.Lang.libelle,
						dataIndex: 'libelle',
						sortable: true,
						width: 200,
						filterable: true
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 90,
						items: [{
							iconCls: 'edit',
							tooltip: Ext.ts.Lang.modifier,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								editEntreeThesaurus(record.data);
							}
						},{
								iconCls : 'add' ,
								tooltip : Ext.ts.Lang.traduire ,
								handler : function ( grid , rowIndex , colIndex ) {
									var record = grid.getStore().getAt( rowIndex );
									translateEntreeThesaurus( record.data );
								}
						},{
							iconCls : 'delete' ,
							tooltip : Ext.ts.Lang.supprimer ,
							handler : function ( grid , rowIndex , colIndex ) {
								var record = grid.getStore().getAt( rowIndex );
								deleteEntreeThesaurus( record.data.cle );
							}
						}]
					}],
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: true
					}),
					trackMouseOver: true,
					tbar: [Ext.ts.Lang.recherche+' : ',
						new Ext.ux.form.SearchField({
							id: 'field_searchEntree',
							store: store_entree,
							width: 200,
							disabled: true
						}),'->',{
						id: 'btn_addEntree',
						text: Ext.ts.Lang.addEntree,
						iconCls: 'add',
						disabled: true,
						handler: addEntreeThesaurus
					}],
					bbar: new Ext.ts.AutoSizePaging({
						store: store_entree,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingEntree,
						emptyMsg: Ext.ts.Lang.pagingEntreeEmpty,
						reloadOnResize: true
					}),
					plugins: [
						new Ext.ts.GridFilters(),
						new Ext.ts.gridKeySearch({
							dataIndex: 'libelle'
						})
					]
				});

				/**
				 * CONTAINER
				 */
				var container = new Ext.ts.Container({
					title: Ext.ts.Lang.titleContainer,
					selMenu: 'thesaurii',
					content: new Ext.Panel({
						hideBorders: true,
						layout: 'border',
						items: [{
							xtype: 'panel',
							region: 'center',
							layout: 'fit',
							margins: '5 0 5 5',
							items: grid_thesaurus
						},{
							xtype: 'panel',
							region: 'east',
							layout: 'fit',
							width: 750,
							minWidth: 250,
							maxWidth: 750,
							margins: '5 5 5 0',
							cmargins: '5 5 5 0',
							header: false,
							collapsible: true,
							collapseMode: 'mini',
							split: true,
							items: grid_entree
						}]
					})
				});

				store_thesaurus.load();

			});

			/**
			 * FUNCTION - THESAURUS
			 */
			function createThesaurus() {
				var items = fieldsThesaurus();

				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.createThesaurus,
					height: 80,
					items: items,
					action: 'createThesaurus',
					service: 'thesaurus',
					gridToReload: Ext.getCmp('grid_thesaurus')
				});
				win.show();
			}

			function editThesaurus(data) {
				var items = fieldsThesaurus(data);

				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.updateThesaurus,
					height: 80,
					items: items,
					action: 'editThesaurus',
					service: 'thesaurus',
					gridToReload: Ext.getCmp('grid_thesaurus')
				});
				win.show();
			}

			function exportThesaurus(codeThesaurus) {
				var combo = new Ext.form.ComboBox({
					width: 250,
					fieldLabel: Ext.ts.Lang.langue,
					mode: 'local',
					triggerAction: 'all',
					valueField: 'k_langue',
					displayField: 'v_langue',
					editable: false,
					store: new Ext.data.ArrayStore({
						fields: ['k_langue', 'v_langue'],
						sortInfo: {field: 'v_langue', direction: 'ASC'},
						data: [
							['fr', Ext.ts.Lang.francais],
							['en', Ext.ts.Lang.anglais],
							['es', Ext.ts.Lang.espagnol],
							['de', Ext.ts.Lang.allemand],
							['it', Ext.ts.Lang.italien],
							['nl', Ext.ts.Lang.neerlandais]
						]
					}),
					allowBlank: false
				});

				var form = new Ext.form.FormPanel({
					height: 60,
					border: false,
					style: 'padding:10px;background-color:#FFFFFF;',
					items: combo
				});

				var win = new Ext.Window({
					title: Ext.ts.Lang.chooseLang,
					width: 450,
					resizable: false,
					layout: 'form',
					items: form,
					buttons: [{
						text: Ext.ts.Lang.valider,
						handler: function() {
							if (form.getForm().isValid()) {
								var codeLangue = combo.getValue();
								win.destroy();
								Ext.ts.location({
									action: 'exportThesaurus',
									service: 'thesaurus',
									params: {
										codeThesaurus: codeThesaurus,
										codeLangue: codeLangue
									}
								});
							}
						}
					},{
						text: Ext.ts.Lang.annuler,
						handler: function() {
							win.destroy();
						}
					}]
				});
				win.show();
			}

			function fieldsThesaurus(values) {
				var values = Ext.isDefined(values) ? values : {};

				return [{
					xtype: 'textfield',
					id: 'codeThesaurus',
					fieldLabel: Ext.ts.Lang.codeThesaurus,
					width: 250,
					allowBlank: false,
					value: values.codeThesaurus,
					readOnly: Ext.isDefined(values.codeThesaurus)
				},{
					xtype: 'textfield',
					id: 'libelle',
					fieldLabel: Ext.ts.Lang.nomThesaurus,
					width: 250,
					allowBlank: false,
					value: values.libelle
				}];
			}

			/**
			 * FUNCTION - ENTREE
			 */
			function addEntreeThesaurus() {
				var selection = Ext.getCmp('grid_thesaurus').getSelectionModel().getSelected();
				var codeThesaurus = selection.data.codeThesaurus;
				var items = fieldsEntreeThesaurus();

				var win = new Ext.ts.ManagementWindow({
					title: Ext.ts.Lang.createEntree,
					height: 100,
					items: items,
					action: 'addEntreeThesaurus',
					service: 'thesaurus',
					params: {
						codeThesaurus: codeThesaurus
					},
					gridToReload: Ext.getCmp('grid_entree')
				});
				win.show();
			}

			function editEntreeThesaurus( data ) {
				var items = fieldsEntreeThesaurus( data );

				var win = new Ext.ts.ManagementWindow( {
					title        : Ext.ts.Lang.updateEntree ,
					height       : 100 ,
					items        : items ,
					action       : 'editEntreeThesaurus' ,
					service      : 'thesaurus' ,
					gridToReload : Ext.getCmp( 'grid_entree' )
				} );
				win.show();
			}

			function translateEntreeThesaurus( data ) {
				var items = fieldsEntreeThesaurus( data );

				var win = new Ext.ts.ManagementWindow( {
					title        : Ext.ts.Lang.translateEntree ,
					height       : 100 ,
					items        : items ,
					action       : 'translateEntreeThesaurus' ,
					service      : 'thesaurus' ,
					gridToReload : Ext.getCmp( 'grid_entree' )
				} );
				win.show();
			}

			function deleteEntreeThesaurus(cle) {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.deleteEntree,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'deleteEntreeThesaurus',
								service: 'thesaurus',
								params: {cle: cle},
								success: function(response) {
									Ext.getCmp('grid_entree').getStore().reload();
								}
							});
						}
					}
				);
			}

			function fieldsEntreeThesaurus(values) {
				var values = Ext.isDefined(values) ? values : {};

				return [{
					xtype: 'textfield',
					id: 'cle',
					fieldLabel: Ext.ts.Lang.cleParente,
					width: 250,
					allowBlank: false,
					value: values.cle,
					readOnly: Ext.isDefined(values.cle)
				},{
					xtype: 'combo',
					hiddenName: 'codeLangue',
					fieldLabel: Ext.ts.Lang.langue,
					width: 250,
					mode: 'local',
					triggerAction: 'all',
					valueField: 'k_langue',
					displayField: 'v_langue',
					editable: false,
					store: new Ext.data.ArrayStore({
						fields: ['k_langue', 'v_langue'],
						sortInfo: {field: 'v_langue', direction: 'ASC'},
						data: [
							['fr', Ext.ts.Lang.francais],
							['en', Ext.ts.Lang.anglais],
							['es', Ext.ts.Lang.espagnol],
							['de', Ext.ts.Lang.allemand],
							['it', Ext.ts.Lang.italien],
							['nl', Ext.ts.Lang.neerlandais]
						]
					}),
					allowBlank: false,
					value: values.lang
				},{
					xtype: 'textfield',
					id: 'libelle',
					fieldLabel: Ext.ts.Lang.libelle,
					width: 250,
					allowBlank: false,
					value: values.libelle
				}];
			}
		</script>

<?php
	require_once('include/footer.php');
?>
