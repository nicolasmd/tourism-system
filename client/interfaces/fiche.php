<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	require_once('include/header.php');
	
	$pagination = new stdClass;
	if (is_array(PSession::$SESSION['fichesForPaging']) && count(PSession::$SESSION['fichesForPaging']) > 0) {
		foreach (PSession::$SESSION['fichesForPaging'] as $k => $fiche) {
			if ($fiche['idFiche'] == $_GET['idFiche']) {
				if (is_array(PSession::$SESSION['fichesForPaging'][$k-1])) {
					$pagination -> prev = PSession::$SESSION['fichesForPaging'][$k-1];
				}
				if (is_array(PSession::$SESSION['fichesForPaging'][$k+1])) {
					$pagination -> next = PSession::$SESSION['fichesForPaging'][$k+1];
				}
			}
		}
	}
?>

		<script type="text/javascript" src="include/langs/formEdition_<?php echo TS_LANG; ?>.js"></script>
		<script type="text/javascript" src="include/formEdition/MTH.js"></script>
		<script type="text/javascript" src="include/formEdition/formEdition.js"></script>
		<script type="text/javascript" src="include/formEdition/asc.js"></script>
		<script type="text/javascript" src="include/formEdition/deg.js"></script>
		<script type="text/javascript" src="include/formEdition/fma.js"></script>
		<script type="text/javascript" src="include/formEdition/hlo.js"></script>
		<script type="text/javascript" src="include/formEdition/hot.js"></script>
		<script type="text/javascript" src="include/formEdition/hpa.js"></script>
		<script type="text/javascript" src="include/formEdition/loi.js"></script>
		<script type="text/javascript" src="include/formEdition/org.js"></script>
		<script type="text/javascript" src="include/formEdition/pcu.js"></script>
		<script type="text/javascript" src="include/formEdition/pna.js"></script>
		<script type="text/javascript" src="include/formEdition/res.js"></script>
		<script type="text/javascript" src="include/formEdition/vil.js"></script>
		
		<script type="text/javascript" src="application/proxy/ts/champ/getChampsPrimaryKey/"></script>
		<script type="text/javascript" src="application/proxy/ts/thesaurus/getUserThesaurii/"></script>
		
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=drawing,places,geometry&language=fr"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
			google.load('visualization', '1', {packages: ['corechart']});
		</script>

		<script type="text/javascript" src="ressources/ficheHTML/gallery/gallery.js"></script>
		<script type="text/javascript">
			Ext.onReady(function(){
				
				Ext.ts.collapsibleCmp.push('content_details');
				
				// Permet d'effectuer le rendu après que tous les stores soient chargés.
				Ext.ts.onAllStoresLoaded = function() {
					var activeTab = Ext.getCmp('content_tab').getComponent(Ext.ts.getCookie('activeTabFiche'));
					Ext.getCmp('content_tab').setActiveTab(activeTab || 0);
					Ext.ts.myMask.hide();
					//refreshPreview();
					
					<?php if (tsDroits::getDroit('FICHE_VALIDATION')) { ?> 
					Ext.getCmp('gridChampsValidation').getStore().load();
					<?php } ?> 
				};
				
				// Redirection vers la liste des fiches si l'utilisateur n'a pas accès à la fiche
				Ext.ts.accessDenied = function() {
					Ext.ts.myMask.hide();
					Ext.Msg.show({
						title: Ext.ts.Lang.failureTitle,
						minWidth: 250,
						msg: Ext.ts.Lang.accessDenied,
						buttons: Ext.Msg.OK,
						icon: Ext.Msg.ERROR,
						fn: function (btn) {
							Ext.ts.open('fiches');
						}
					});
				};
				
				/** 
				 * PAGINATION
				 */
				Ext.ts.pagination = <?php echo json_encode($pagination) ?>;
				
				<?php if (tsDroits::getDroit('FICHE_VERSION') || isAuthorizedIP()) { ?> 
				/**
				 * HISTORIQUE DES VERSIONS
				 */
				var storeVersion = new Ext.ts.JsonStore({
					action: 'getFicheVersions',
					service: 'fiche',
					baseParams: {
						idFiche: Ext.ts.params.idFiche
					},
					autoLoad: true,
					fields: [
						{name: 'idFicheVersion', type: 'int'},
						{name: 'dateVersion', type: 'date', dateFormat: 'Y-m-d H:i:s'},
						{name: 'idUtilisateur', type: 'int'},
						{name: 'email', type: 'string'}
					],
					sortInfo: {field: 'idFicheVersion', direction: 'ASC'},
					remoteSort: true
				});
				var gridVersion = new Ext.grid.GridPanel({
					id: 'gridVersion',
					itemId: 'gridVersion',
					title: Ext.ts.Lang.historique,
					iconCls: 'time',
					store: storeVersion,
					columns: [{
						header: Ext.ts.Lang.version,
						dataIndex: 'idFicheVersion',
						sortable: true,
						width: 80
					},{
						xtype: 'datecolumn',
						header: Ext.ts.Lang.date,
						format: 'd F Y H:i:s',
						dataIndex: 'dateVersion',
						sortable: true,
						width: 150,
						filterable: true
					},{
						header: Ext.ts.Lang.utilisateur,
						dataIndex: 'email',
						sortable: true,
						width: 150
					},{
						xtype: 'actioncolumn',
						header: Ext.ts.Lang.outils,
						width: 80,
						items: [{
							iconCls: 'page_white_go',
							tooltip: Ext.ts.Lang.visualiser,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								Ext.ts.open('fiche', {
									idFiche: Ext.ts.params.idFiche,
									idFicheVersion: record.data.idFicheVersion
								});
							},
							scope: this
						}
						<?php if (tsDroits::getDroit('FICHE_XML') || isAuthorizedIP()) { ?> 
						,{
							iconCls: 'page_white_get',
							tooltip: Ext.ts.Lang.getXml,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								getFicheXml(Ext.ts.params.idFiche, record.data.idFicheVersion);
							},
							scope: this
						}
						<?php } ?> 
						<?php if (isAuthorizedIP()) { ?> 
						,{
							iconCls: 'delete',
							tooltip: Ext.ts.Lang.supprimer,
							handler: function(grid, rowIndex, colIndex) {
								var record = grid.getStore().getAt(rowIndex);
								Ext.MessageBox.confirm(
									Ext.ts.Lang.confirmTitle,
									Ext.ts.Lang.deleteFicheVersion,
									function (btn) {
										if (btn == 'yes') {
											Ext.ts.request({
												action: 'deleteFicheVersion',
												service: 'fiche',
												params: {
													idFiche: Ext.ts.params.idFiche,
													idFicheVersion: record.data.idFicheVersion
												},
												success: function(response) {
													storeVersion.reload();
												},
												scope: this
											});
										}
									},
									this
								);
							},
							scope: this
						}
						<?php } ?> 
						]
					}],
					plugins: new Ext.ts.GridFilters(),
					bbar: {
						xtype: 'autosizepaging',
						store: storeVersion,
						displayInfo: true,
						displayMsg: Ext.ts.Lang.pagingVersion,
						emptyMsg: Ext.ts.Lang.pagingVersionEmpty,
						reloadOnResize: true
					}
				});
				<?php } ?> 
				
				<?php if (tsDroits::getDroit('FICHE_VALIDATION')) { ?> 
				/**
				 * CHAMPS EN ATTENTE DE VALIDATION
				 */
				var storeChampsValidation = new Ext.ts.JsonStore({
					action: 'getChampsFicheAValider',
					service: 'ficheValidation',
					baseParams: {
						idFiche : Ext.ts.params.idFiche
					},
					fields: [
						{name: 'idValidationChamp', type: 'int'},
						{name: 'idChamp', type: 'int'},
						{name: 'identifiant', type: 'string'},
						{name: 'libelle', type: 'string'},
						{name: 'valeur', type:'auto'},
						{name: 'idUtilisateur', type: 'int'},
						{name: 'dateModification', type: 'date', dateFormat: 'Y-m-d H:i:s'},
						{name: 'email', type: 'string'}
					],
					sortInfo: {field: 'libelle', direction: 'ASC'},
					remoteSort: true,
					listeners: {
						load: initChampsValidation
					}
				});
				var gridChampsValidation = new Ext.grid.GridPanel({
					id: 'gridChampsValidation',
					itemId: 'gridChampsValidation',
					title: Ext.ts.Lang.validation,
					iconCls: 'error_gray',
					store: storeChampsValidation,
					columns: [{
						header: Ext.ts.Lang.champ,
						dataIndex: 'libelle',
						id: 'columnLibelleChamp'
					},{
						header: Ext.ts.Lang.modifiePar,
						dataIndex: 'email',
						width: 150
					},{
						xtype: 'datecolumn',
						header: Ext.ts.Lang.le,
						dataIndex: 'dateModification',
						format: 'd F Y H:i:s',
						width: 150
					}],
					autoExpandColumn: 'columnLibelleChamp',
					sm: new Ext.grid.RowSelectionModel({
						singleSelect: true,
						listeners: {
							rowselect: function(sm, rowIndex, record) {
								var field = Ext.getCmp(record.data.identifiant);
								if (!Ext.isEmpty(field)) {
									var onglet = field.findParentByType('ts_onglet');
									Ext.getCmp('content_tab').setActiveTab(onglet);
								}
							}
						}
					})
				});
				<?php } ?> 
				
				/**
				 * RACCOURCIS CLAVIER
				 */
				this.shortcutSaveFiche = new Ext.KeyMap(document, [{
					key: 's',
					ctrl: true,
					stopEvent: true,
					fn: sauvegardeFiche
				}]);
				
				if(Ext.isDefined(Ext.ts.params.idFicheVersion)) {
					this.shortcutSaveFiche.disable();
				}
			
				<?php if (tsDroits::getDroit('FICHE_XML') || isAuthorizedIP()) { ?> 
				new Ext.KeyMap(document, [{
					key: 'u',
					ctrl: true,
					stopEvent: true,
					fn: function() {
						getFicheXml(Ext.ts.params.idFiche, Ext.ts.params.idFicheVersion);
					}
				}]);
				<?php } ?> 
				<?php if (isAuthorizedIP()) { ?> 
				new Ext.KeyMap(document, [{
					key: 'r',
					ctrl: true,
					stopEvent: true,
					fn: function() {
						var page = Ext.ts.params.pg;
						delete Ext.ts.params.pg;
						Ext.ts.params.purgecache = '';
						Ext.ts.open(page, Ext.ts.params, false);
					}
				}]);
				<?php } ?> 
				
				/**
				 * MAIN TOOLBAR
				 */ 
				var mainTbar = [{
					text: Ext.ts.Lang.retourListeFiches,
					iconCls: 'resultset_previous',
					handler: function() {
						Ext.ts.open('fiches');
					}
				}];
				
				if (Ext.isDefined(Ext.ts.params.idFicheVersion)) {
					mainTbar.push(
						{
							xtype: 'spacer',
							width: 30
						},
						Ext.ts.Lang.visualisationVersion + ' ' + Ext.ts.params.idFicheVersion + ' : ',
						{
							text: Ext.ts.Lang.retourLastVersion,
							handler: function() {
								Ext.ts.open('fiche', {idFiche: Ext.ts.params.idFiche});
							}
						},'-',
						{
							text: Ext.ts.Lang.retourThisVersion,
							handler: restoreFicheVersion
						}
					);
				}
				
				mainTbar.push('->',{
					text: Ext.isDefined(Ext.ts.pagination.prev)
						? Ext.ts.pagination.prev.raisonSociale
						: Ext.ts.Lang.sansTitre,
					iconCls: 'resultset_previous',
					handler: function(e, tool) {
						Ext.ts.open('fiche', {idFiche: Ext.ts.pagination.prev.idFiche});
					},
					disabled: !Ext.isDefined(Ext.ts.pagination.prev)
				},'-',{
					text: Ext.isDefined(Ext.ts.pagination.next)
						? Ext.ts.pagination.next.raisonSociale
						: Ext.ts.Lang.sansTitre,
					iconCls: 'resultset_next',
					iconAlign: 'right',
					handler: function(e, tool) {
						Ext.ts.open('fiche', {idFiche: Ext.ts.pagination.next.idFiche});
					},
					disabled: !Ext.isDefined(Ext.ts.pagination.next)
				});
				
				/**
				 * FORM TOOLBAR
				 */
				var formTbar = ['->'/*,{
					text: Ext.ts.Lang.apercu,
					iconCls: 'page_white_magnify',
					itemId: 'panelPreview',
					handler: showDetails
				},{
					text: Ext.ts.Lang.guideSaisie,
					iconCls: 'information',
					itemId: 'guideSaisie',
					handler: showDetails
				}*/
				<?php if (tsDroits::getDroit('FICHE_VALIDATION') || isAuthorizedIP()) { ?> 
				/*,{
					id: 'btnValidation',
					text: Ext.ts.Lang.champsValidation + ' (0)',
					iconCls: 'error_gray',
					itemId: 'gridChampsValidation',
					handler: showDetails
				}*/
				<?php } ?> 
				<?php if (tsDroits::getDroit('FICHE_VERSION') || isAuthorizedIP()) { ?> 
				/*,{
					text: Ext.ts.Lang.historiqueVersions,
					iconCls: 'time',
					itemId: 'gridVersion',
					handler: showDetails
				}*/
				<?php } ?> 
				<?php if (tsDroits::getDroit('FICHE_XML') || isAuthorizedIP()) { ?> 
				/*,'-'*/,{
					text: Ext.ts.Lang.getXml,
					handler: function() {
						getFicheXml(Ext.ts.params.idFiche, Ext.ts.params.idFicheVersion);
					}
				},'-'
				<?php } ?> 
				,{
					id: 'btnPublication',
					text: Ext.ts.Lang.depublie,
					iconCls: 'tick_off',
					handler: setPublicationFiche,
					disabled: true
				},'-',{
					id: 'btnSauvegarde',
					text: Ext.ts.Lang.saveFiche,
					iconCls: 'disk',
					handler: sauvegardeFiche,
					disabled: Ext.isDefined(Ext.ts.params.idFicheVersion)
				}];
				
				/**
				 * CONTAINER
				 */
				var container = new Ext.ts.Container({
					title: Ext.ts.Lang.titleContainer,
					selMenu: 'fiches',
					content: new Ext.FormPanel({
						id: 'formulaireEdition',
						layout: 'border',
						items: [{
							xtype: 'tabpanel',
							id: 'content_tab',
							region: 'center',
							deferredRender: true,
							tbar: formTbar
						},{
							xtype: 'tabpanel',
							id: 'content_details',
							region: 'east',
							hideBorders: true,
							width: 500,
							minWidth: 500,
							split: true,
							headerCfg: {},
							collapsible: true,
							collapseMode: 'mini',
							activeTab: 0,
							items: [/*{
								xtype: 'panel',
								id: 'panelPreview',
								itemId: 'panelPreview',
								title: Ext.ts.Lang.apercu,
								iconCls: 'page_white_magnify',
								autoScroll: true
							},{
								xtype: 'panel',
								id: 'guideSaisie',
								itemId: 'guideSaisie',
								title: Ext.ts.Lang.guideSaisie,
								iconCls: 'information',
								autoLoad: 'include/aide/formEdition.php'
							}*/
							<?php if (tsDroits::getDroit('FICHE_VERSION') || isAuthorizedIP()) { ?> 
							gridVersion,
							<?php } ?>
							<?php if (tsDroits::getDroit('FICHE_VALIDATION')) { ?> 
							gridChampsValidation,
							<?php } ?>
							]
						}],
						tbar: new Ext.Toolbar({
							style: 'margin-bottom: 5px;',
							items: mainTbar
						}),
						listeners:  {
							afterrender: function(formulaireEdition) {
								formulaireEdition.addEvents('beforesubmit');
								formulaireEdition.addEvents('aftersubmit');
							},
							scope : this
						}
					})
				});
				
				/**
				 * LOAD -> FORMULAIRE D'EDITION
				 */
				Ext.ts.request({
					action: 'getFiche',
					service: 'fiche',
					params: {
						idFiche: Ext.ts.params.idFiche,
						idFicheVersion: Ext.ts.params.idFicheVersion
					},
					success: function(response) {
						var reponse = Ext.decode(response.responseText);
						
						Ext.ts.myMask = new Ext.LoadMask(Ext.getCmp('content_tab').getEl(), {
							msg: Ext.ts.Lang.ficheLoading,
							removeMask: true
						});
						Ext.ts.myMask.show();
						
						if (!Ext.isEmpty(reponse.objetFiche)) {
							Ext.ts.oFiche = reponse.objetFiche;
							
							Ext.getCmp('container').setTitle(
								Ext.ts.bordereaux[Ext.ts.oFiche.bordereau] + ' - ' +
								(!Ext.isEmpty(Ext.ts.oFiche.raisonSociale) ? Ext.ts.oFiche.raisonSociale : Ext.ts.Lang.sansTitre) +
								' (' + Ext.ts.oFiche.codeTIF + ')'
							);
							
							Ext.getCmp('btnPublication').setIconClass(Ext.ts.oFiche.publication == 'Y' ? 'tick' : 'tick_off');
							Ext.getCmp('btnPublication').setText(Ext.ts.oFiche.publication == 'Y' ? Ext.ts.Lang.publie : Ext.ts.Lang.depublie);
							Ext.getCmp('btnPublication').enable();
							
							var onglets = [];
							Ext.each(Ext.ts[Ext.ts.oFiche.bordereau], function(onglet) {
								var fieldsets = [];
								Ext.each(onglet.items, function(fieldset) {
									var fields = [];
									
									Ext.each(fieldset.items, function(field, i) {
										if (Ext.isDefined(field)) {
											var fieldName = field.tsName || field.hiddenName || field.name;
											if (Ext.isDefined(Ext.ts.oFiche.readable[fieldName])) {
												var cmp = new Ext.create(field);
												fields.push(cmp);
												cmp.setValue(Ext.ts.oFiche.readable[fieldName]);
												cmp.disable();
											}
											else if (Ext.isDefined(Ext.ts.oFiche.editable[fieldName])) {
												var cmp = new Ext.create(field);
												fields.push(cmp);
												cmp.setValue(Ext.ts.oFiche.editable[fieldName]);
												cmp.addClass('editable');
												cmp.on('change', onChange);
											}
										}
									});
									
									if (fields.length > 0) {
										fieldsets.push(Ext.applyIf(fieldset.fieldsetCfg || {}, {
											xtype: 'ts_fieldset',
											title: fieldset.fieldset,
											anchor: '100%',
											items: fields
										}));
									}
								});
								
								if (fieldsets.length > 0) {
									onglets.push(Ext.applyIf(onglet.ongletCfg || {}, {
										xtype: 'ts_onglet',
										itemId: onglet.onglet,
										title: onglet.onglet,
										layout: 'anchor',
										items: fieldsets
									}));
								}
							});
							
							if (onglets.length > 0) {
								Ext.getCmp('content_tab').add(onglets);
								Ext.getCmp('content_tab').doLayout();
								
								if (Ext.ts.storeToLoad === false) {
									Ext.ts.onAllStoresLoaded();
								}
							}
							else {
								Ext.ts.accessDenied.defer(500);
							}
						}
					},
					failure: function(response) {
						var result = Ext.decode(response.responseText);
						Ext.Msg.show({
							title: Ext.ts.Lang.failureTitle,
							minWidth: 250,
							msg: result.msg,
							buttons: Ext.Msg.OK,
							icon: Ext.Msg.ERROR,
							fn: function (btn) {
								Ext.ts.open('fiches');
							}
						});
					}
				});
			});
			
			function onChange() {
				Ext.getCmp('btnSauvegarde').setIconClass('disk_red');
			}
			
			<?php if (tsDroits::getDroit('FICHE_XML') || isAuthorizedIP()) { ?> 
			function getFicheXml(idFiche, idFicheVersion) {
				window.open(Ext.ts.url({
					action: 'getFicheXml',
					service: 'ficheExport',
					params: {
						idFiche: idFiche,
						idFicheVersion: idFicheVersion
					}
				}));
			}
			<?php } ?> 
			
			/*function showDetails(btn) {
				Ext.getCmp('content_details').expand();
				Ext.getCmp('content_details').getLayout().setActiveItem(btn.itemId);
			}*/
			
			/*function refreshPreview() {
				Ext.getCmp('panelPreview').getUpdater().update({
					url: Ext.ts.url({
						action: 'getFicheHtml',
						service: 'diffusion',
						plugin: 'diffusion',
						params: {
							idFiche: Ext.ts.params.idFiche
						}
					}),
					scripts: true
				});
			}*/
			
			<?php if (tsDroits::getDroit('FICHE_VALIDATION') || isAuthorizedIP()) { ?> 
			function initChampsValidation() {
				// Title de TabPanel de validation
				var count = Ext.getCmp('gridChampsValidation').getStore().getTotalCount();
				Ext.getCmp('gridChampsValidation').setTitle(Ext.ts.Lang.validation + (count > 0 ? ' (' + count + ')' : ''));
				Ext.getCmp('gridChampsValidation').setIconClass(count > 0 ? 'error' : 'error_gray');
				
				// Champs à valider/validés/refusés
				Ext.ts.validationFields = Ext.ts.validationFields || [];
				Ext.ts.validatedFields = Ext.ts.validatedFields || [];
				Ext.ts.rejectedFields = Ext.ts.rejectedFields || [];
				
				// Fonctionnalités de validation (Event / Button / ToolTip)
				Ext.ts.validationActions = Ext.ts.validationActions || new Ext.util.MixedCollection();
				//Ext.ts.validationToolTip = Ext.ts.validationToolTip || new Ext.util.MixedCollection();
				Ext.ts.validationEvents = Ext.ts.validationEvents || new Ext.util.MixedCollection();
				
				// RAZ des fonctionnalités de validation pour les champs validés/refusés
				Ext.each(Ext.ts.validatedFields, destroyChampValidation);
				Ext.ts.validatedFields = [];
				Ext.each(Ext.ts.rejectedFields, destroyChampValidation);
				Ext.ts.rejectedFields = [];
				
				// Construction des fonctionnalités de validation
				// Si l'onglet n'est pas encore rendu on diffère le rendu sur l'event activate
				Ext.getCmp('gridChampsValidation').getStore().each(function(record) {
					if (Ext.ts.validationFields.indexOf(record.data.identifiant) == -1) {
						var field = Ext.getCmp(record.data.identifiant);
						
						if (!Ext.isEmpty(field)) {
							var onglet = field.findParentByType('ts_onglet');
							
							if (onglet.rendered) {
								initChampValidation(record);
							}
							else {
								onglet.on('activate', function() {
									initChampValidation(record);
								}, this, {
									single: true,
									delay: 200
								});
							}
							
							Ext.ts.validationFields.push(record.data.identifiant);
						}
					}
				});
			}
			
			function destroyChampValidation(fieldName) {
				// Bouton d'action
				var container = Ext.ts.validationActions.get(fieldName);
				if (Ext.isDefined(container)) {
					container.destroy();
					Ext.ts.validationActions.removeKey(fieldName);
				}
				
				// ToolTip ancienne valeur
				/*var tooltip = Ext.ts.validationToolTip.get(fieldName);
				if (Ext.isDefined(tooltip)) {
					tooltip.destroy()
					Ext.ts.validationToolTip.removeKey(fieldName);
				}*/
				
				// Refus automatique si on modifie le champ
				var e = Ext.ts.validationEvents.get(fieldName);
				if (Ext.isDefined(e)) {
					e.cmp.un(e.event, e.fn);
					Ext.ts.validationEvents.removeKey(fieldName);
				}
			}
			
			function initChampValidation(record) {
				var field = Ext.getCmp(record.data.identifiant);
				var fieldEl = field.getEl();
				
				// Elément sur lequel les boutons seront rendus
				var wrapId = record.data.identifiant + 'ValidationWrap';
				if (Ext.isEmpty(Ext.getDom(wrapId))) {
					var fieldsetEl = field.findParentByType('ts_fieldset').getEl();
					var wrapEl = fieldsetEl.createChild({
						id: wrapId,
						tag: 'div',
						cls: 'champValidationWrap'
					});
					wrapEl.alignTo(fieldEl, 'tl-tr', [20, 0]);
				}
				
				// Boutons d'action
				if (!Ext.isDefined(Ext.ts.validationActions.get(record.data.identifiant))) {
					var validBtn = new Ext.Button({
						ref: 'validBtn',
						text: Ext.ts.Lang.valider,
						iconCls: 'button_ok',
						handler: function(btn) {
							validChamp(btn.fieldName);
						}
					});
					var rejectBtn = new Ext.Button({
						ref: 'rejectBtn',
						text: Ext.ts.Lang.refuser,
						iconCls: 'button_cancel',
						handler: function(btn) {
							rejectChamp(btn.fieldName);
						}
					});
					var statutValidation = new Ext.Toolbar.TextItem({
						ref: 'statutValidation',
						text: '',
						hidden: true
					});
					var cancelBtn = new Ext.Button({
						ref: 'cancelBtn',
						text: Ext.ts.Lang.annuler,
						handler: function(btn) {
							cancelValidation(btn.fieldName);
						},
						hidden: true
					});
					var container = new Ext.Toolbar({
						renderTo: wrapId,
						cls: 'validationTbar',
						defaults: {
							fieldName: record.data.identifiant
						},
						items: [
							statutValidation,
							rejectBtn,
							'-',
							validBtn,
							cancelBtn
						]
					});
					Ext.ts.validationActions.add(record.data.identifiant, container);
				}
				
				// ToolTip ancienne valeur => dans showDiff ?
				/*if (!Ext.isDefined(Ext.ts.validationToolTip.get(record.data.identifiant)) && Ext.isString(record.data.valeur)) {
					var value = Ext.ts.oFiche.editable[record.data.identifiant];
					if (field.getXType() == 'combomth') {
						var index = field.getStore().findExact('cle', value);
						var r = field.getStore().getAt(index);
						value = r.data.libelle;
					}
					var tooltip = new Ext.ToolTip({
						title: Ext.ts.Lang.ancienneValeur,
						target: fieldEl,
						anchor: 'left',
						padding: 2,
						html: '<span style="color:red;">' + value + '</span>'
					});
					Ext.ts.validationToolTip.add(record.data.identifiant, tooltip);
				}*/
				
				// Refus automatique si on modifie le champ
				if (!Ext.isDefined(Ext.ts.validationEvents.get(record.data.identifiant))) {
					var event = 'change';
					var fn = function() { rejectChamp(record.data.identifiant, true); };
					field.on(event, fn);
					Ext.ts.validationEvents.add(record.data.identifiant, {
						cmp: field,
						event: event,
						fn: fn
					});
				}
				
				// Valeur à valider
				field.setValue(record.data.valeur);
				
				// Mise en évidence des différences
				if (Ext.isFunction(field.showDiff)) {
					field.showDiff(Ext.ts.oFiche.editable[record.data.identifiant], record.data.valeur);
				}
			}
			
			function validChamp(fieldName) {
				Ext.ts.validationFields.remove(fieldName);
				Ext.ts.validatedFields.push(fieldName);
				Ext.ts.rejectedFields.remove(fieldName);
				
				var field = Ext.getCmp(fieldName);
				
				var record = getValidationRecord(fieldName);
				field.setValue(record.data.valeur);
				
				var panel = Ext.ts.validationActions.get(fieldName);
				panel.validBtn.hide();
				panel.rejectBtn.hide();
				panel.statutValidation.show();
				panel.statutValidation.setText(Ext.ts.Lang.valide);
				panel.cancelBtn.show();
				
				if (Ext.isFunction(field.hideDiff)) {
					field.hideDiff();
				}
			}
			
			function rejectChamp(fieldName, keepValue) {
				Ext.ts.validationFields.remove(fieldName);
				Ext.ts.validatedFields.remove(fieldName);
				Ext.ts.rejectedFields.push(fieldName);
				
				var field = Ext.getCmp(fieldName);
				
				if (!Ext.isDefined(keepValue)) {
					field.setValue(Ext.ts.oFiche.editable[fieldName]);
				}
				
				var panel = Ext.ts.validationActions.get(fieldName);
				panel.validBtn.hide();
				panel.rejectBtn.hide();
				panel.statutValidation.show();
				panel.statutValidation.setText(Ext.ts.Lang.refuse);
				panel.cancelBtn.show();
				
				if (Ext.isFunction(field.hideDiff)) {
					field.hideDiff();
				}
			}
			
			function cancelValidation(fieldName) {
				Ext.ts.validationFields.push(fieldName);
				Ext.ts.validatedFields.remove(fieldName);
				Ext.ts.rejectedFields.remove(fieldName);
				
				var field = Ext.getCmp(fieldName);
				
				var record = getValidationRecord(fieldName);
				field.setValue(record.data.valeur);
				
				var panel = Ext.ts.validationActions.get(fieldName);
				panel.validBtn.show();
				panel.rejectBtn.show();
				panel.statutValidation.hide();
				panel.cancelBtn.hide();
				
				if (Ext.isFunction(field.showDiff)) {
					field.showDiff(Ext.ts.oFiche.editable[fieldName], record.data.valeur);
				}
			}
			
			function getValidationRecord(identifiant) {
				var index = Ext.getCmp('gridChampsValidation').getStore().findExact('identifiant', identifiant);
				return index != -1 ? Ext.getCmp('gridChampsValidation').getStore().getAt(index) : false;
			}
			<?php } ?>
			
			<?php if (tsDroits::getDroit('FICHE_VERSION') || isAuthorizedIP()) { ?> 
			function restoreFicheVersion() {
				Ext.MessageBox.confirm(
					Ext.ts.Lang.confirmTitle,
					Ext.ts.Lang.restoreFiche,
					function (btn) {
						if (btn == 'yes') {
							Ext.ts.request({
								action: 'restoreFicheVersion',
								service: 'fiche',
								params: {
									idFiche: Ext.ts.params.idFiche,
									idFicheVersion: Ext.ts.params.idFicheVersion
								},
								success: function(response) {
									Ext.ts.open('fiche', {idFiche: Ext.ts.params.idFiche});
								},
								scope: this
							});
						}
					},
					this
				);
			}
			<?php } ?>
			
			function sauvegardeFiche() {
				var formulaireEdition = Ext.getCmp('formulaireEdition');
				
				if (formulaireEdition.getForm().isValid()) {
					if(formulaireEdition.fireEvent('beforesubmit')) {
						Ext.ts.submit({
							form: 'formulaireEdition',
							service: 'fiche',
							action: 'sauvegardeFiche',
							params: {
								idFiche: Ext.ts.params.idFiche,
								code_insee: Ext.ts.oFiche.codeInsee,
								commune: Ext.ts.oFiche.libelleCommune,

								champsAValider: Ext.encode(Ext.ts.validationFields),
								champsValide: Ext.encode(Ext.ts.validatedFields),
								champsRefuse: Ext.encode(Ext.ts.rejectedFields)
							},
							waitMsg: Ext.ts.Lang.saveWaitMsg,
							success: function(form, action) {
								var win = new Ext.ts.Notification({
									html: action.result.msg
								});
								win.show();

								Ext.getCmp('btnSauvegarde').setIconClass('disk');

								//refreshPreview();
								<?php if (tsDroits::getDroit('FICHE_VERSION') || isAuthorizedIP()) { ?> 
								Ext.getCmp('gridVersion').getStore().reload();
								<?php } ?> 
								<?php if (tsDroits::getDroit('FICHE_VALIDATION')) { ?> 
								Ext.getCmp('gridChampsValidation').getStore().reload();
								<?php } ?> 

								// Update les champs après la sauvegarde
								Ext.ts.request({
									action: 'getFiche',
									service: 'fiche',
									params: {
										idFiche: Ext.ts.params.idFiche,
										idFicheVersion: Ext.ts.params.idFicheVersion
									},
									success: function(response) {
										var reponse = Ext.decode(response.responseText);
										
										if (!Ext.isEmpty(reponse.objetFiche)) {
											Ext.ts.oFiche = reponse.objetFiche;
													
											Ext.getCmp('container').setTitle(
												Ext.ts.bordereaux[Ext.ts.oFiche.bordereau] + ' - ' +
												(!Ext.isEmpty(Ext.ts.oFiche.raisonSociale) ? Ext.ts.oFiche.raisonSociale : Ext.ts.Lang.sansTitre) +
												' (' + Ext.ts.oFiche.codeTIF + ')'
											);
											
											Ext.iterate(Ext.ts.oFiche.editable, function (field, value) {
												if (Ext.isDefined(Ext.getCmp(field))) {
													Ext.getCmp(field).setValue(value);
												}
											});
										}
									}
								});
							}
						});
					}
				}
				else {
					Ext.Msg.show({
						title: Ext.ts.Lang.failureTitle,
						minWidth: 250,
						msg: Ext.ts.Lang.saveError,
						buttons: Ext.Msg.OK,
						icon: Ext.Msg.ERROR
					});
				}
			}
			
			function setPublicationFiche(btn) {
				Ext.ts.request({
					action: 'setPublicationFiche',
					service: 'fiche',
					params: {
						idFiche: Ext.ts.params.idFiche,
						publication: !(Ext.ts.oFiche.publication == 'Y')
					},
					success: function(response) {
						Ext.ts.oFiche.publication = Ext.ts.oFiche.publication.toggle('Y', 'N');
						
						btn.setIconClass(Ext.ts.oFiche.publication == 'Y' ? 'tick' : 'tick_off');
						btn.setText(Ext.ts.oFiche.publication == 'Y' ? Ext.ts.Lang.publie : Ext.ts.Lang.depublie);
						
						var reponse = Ext.decode(response.responseText);
						var win = new Ext.ts.Notification({
							html: reponse.msg
						});
						win.show();
					},
					scope: this
				});
			}
		</script>

<?php
	require_once('include/footer.php');
?>