<?php
	
	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	
	tsDroits::checkDroit('MENU_FICHE');
	
	require_once('include/header.php');
	
	$autoLoad = PSession::$SESSION['tsSessionId'] == SESSION_ID_ROOT ? 'false' : 'true';
?>

		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&language=fr"></script>
		<script type="text/javascript" src="include/maps/markerclusterer.js"></script>
		<script type="text/javascript" src="ressources/ficheHTML/gallery/gallery.js"></script>
		<script type="text/javascript">
			Ext.onReady(function(){
				
				Ext.ts.collapsibleCmp.push('westFiches');
				
				new Ext.ts.HookableInterface({
					id: 'fiches',
					selMenu: 'fiches',
					title: Ext.ts.Lang.titleContainer,
					
					buildItems: function() {
						this.storeFiche = new Ext.ts.JsonStore({
							storeId: 'storeFiche',
							action: 'getFiches',
							service: 'fiche',
							baseParams: {
								query: Ext.isDefined(Ext.ts.params.query)
									? Ext.ts.params.query
									: undefined
							},
							autoLoad: <?php echo $autoLoad; ?>,
							fields: [
								{name: 'idFiche', type: 'int'},
								{name: 'raisonSociale', type: 'string'},
								{name: 'codeTIF', type: 'string'},
								{name: 'referenceExterne', type: 'string'},
								{name: 'codeInsee', type: 'string'},
								{name: 'commune', type: 'string'},
								{name: 'bordereau', type: 'string'},
								{name: 'gpsLat', type: 'float'},
								{name: 'gpsLng', type: 'float'},
								{name: 'idGroupe', type: 'int'},
								{name: 'nomGroupe', type: 'string'},
								{name: 'publication', type: 'string'},
								{name: 'dateCreation', type: 'date', dateFormat: 'Y-m-d H:i:s'},
								{name: 'dateVersion', type: 'date', dateFormat: 'Y-m-d H:i:s'}
							],
							sortInfo: {field: 'raisonSociale', direction: 'ASC'},
							remoteSort: true,
							listeners: {
								load: {
									fn: this.loadMap,
									scope: this
								}
							}
						});

						this.storeFiche.on('load', this.updateButtonExport, this);

						this.smFiche = new Ext.grid.CheckboxSelectionModel({
							singleSelect: false,
							listeners: {
								selectionchange: {
									fn: function(sm) {
										this.btnPublierFiches.setDisabled(sm.getCount() < 1);
										this.btnDepublierFiches.setDisabled(sm.getCount() < 1);
										this.btnDeleteFiches.setDisabled(sm.getCount() < 1);
										this.btnAddToPanier.setDisabled(sm.getCount() < 1);
									},
									scope: this
								}
							}
						});
						
						this.items = {
							itemId: 'borderFiches',
							layout: 'border',
							items: [{
								itemId: 'centerFiches',
								ref: '../../centerFiches',
								xtype: 'tabpanel',
								region: 'center',
								hideBorders: true,
								margins: '5 5 5 0',
								resizeTabs: true,
								tabWidth: 120,
								activeTab: 0,
								items: [{
									id: 'gridFiche',
									itemId: 'gridFiche',
									xtype: 'grid',
									title: Ext.ts.Lang.modeListe,
									store: this.storeFiche,
									sm: this.smFiche,
									columns: [this.smFiche,{
										xtype: 'actioncolumn',
										header: Ext.ts.Lang.publie,
										dataIndex: 'publication',
										width: 60,
										items: [{
											getClass: function(value, metaData, record) {
												return value == 'Y' ? 'tick' : 'tick_off';
											},
											tooltip: Ext.ts.Lang.publierDepublier,
											handler: function(grid, rowIndex, colIndex) {
												var record = grid.getStore().getAt(rowIndex);
												this.setPublicationFiche(record.data.idFiche, record.data.publication == 'N');
											},
											scope: this
										}],
										filter: {
											type: 'list',
											options: [{
												id: 'Y', 
												text: 'Publié'
											},{
												id: 'N', 
												text: 'Non publié'
											}]
										}
									},{
										header: Ext.ts.Lang.idFiche,
										dataIndex: 'idFiche',
										sortable: true,
										width: 80,
										hidden: true
									},{
										header: Ext.ts.Lang.codeTif,
										dataIndex: 'codeTIF',
										sortable: true,
										width: 130,
										filterable: true,
										hidden: true
									},{
										header: Ext.ts.Lang.referenceExterne,
										dataIndex: 'referenceExterne',
										sortable: true,
										width: 130,
										filterable: true,
										hidden: true
									},{
										header: Ext.ts.Lang.raisonSociale,
										dataIndex: 'raisonSociale',
										sortable: true,
										width: 250,
										filterable: true
									},{
										header: Ext.ts.Lang.commune,
										dataIndex: 'commune',
										sortable: true,
										width: 150,
										filterable: true
									},{
										header: Ext.ts.Lang.codeInsee,
										dataIndex: 'codeInsee',
										sortable: true,
										width: 80,
										filter: {
											type: 'string',
											listeners: {
												serialize: function(args) {
													args.comparison = 'start';
												}
											}
										},
										hidden: true
									},{
										xtype: 'bordereaucolumn',
										sortable: true,
										width: 150
									},{
										header: Ext.ts.Lang.proprietaire,
										dataIndex: 'nomGroupe',
										sortable: true,
										width: 200,
										filterable: true
									},{
										xtype: 'datecolumn',
										header: Ext.ts.Lang.dateCreation,
										format: 'd F Y H:i:s',
										dataIndex: 'dateCreation',
										sortable: true,
										width: 150,
										filterable: true
									},{
										xtype: 'datecolumn',
										header: Ext.ts.Lang.dateVersion,
										format: 'd F Y H:i:s',
										dataIndex: 'dateVersion',
										sortable: true,
										width: 150,
										filterable: true,
										hidden: true
									},{
										xtype: 'actioncolumn',
										header: Ext.ts.Lang.outils,
										dataIndex: 'idFiche',
										width: 100,
										items: [/*{
											iconCls: 'page_white_go',
											tooltip: Ext.ts.Lang.visualiser,
											handler: function(grid, rowIndex, colIndex) {
												var record = grid.getStore().getAt(rowIndex);
												this.displayFiche(record.data.idFiche);
											},
											scope: this
										},*/{
											iconCls: 'page_white_acrobat',
											tooltip: Ext.ts.Lang.exporterPdf,
											handler: function(grid, rowIndex, colIndex) {
												var record = grid.getStore().getAt(rowIndex);
												this.getFichePdf(record.data.idFiche);
											},
											scope: this
										},{
											iconCls: 'page_white_copy',
											tooltip: Ext.ts.Lang.dupliquer,
											handler: function(grid, rowIndex, colIndex) {
												var record = grid.getStore().getAt(rowIndex);
												this.duplicateFiche(record.data);
											},
											scope: this
										},{
											iconCls: 'edit',
											tooltip: Ext.ts.Lang.modifier,
											handler: function(grid, rowIndex, colIndex) {
												var record = grid.getStore().getAt(rowIndex);
												Ext.ts.open('fiche', {idFiche: record.data.idFiche});
											},
											scope: this
										},{
											getClass: function(value, metaData, record) {
												return record.data.idGroupe == Ext.ts.idGroupe ? 'delete' : 'link_break';
											},
											tooltip: Ext.ts.Lang.supprimer,
											handler: function(grid, rowIndex, colIndex) {
												var record = grid.getStore().getAt(rowIndex);
												if (record.data.idGroupe == Ext.ts.idGroupe) {
													this.deleteFiche(record.data.idFiche);
												}
												else {
													this.deleteGroupePartenaireFiche(record.data);
												}
											},
											scope: this
										}]
									}],
									viewConfig: {
										getRowClass: function(record) {
											return record.data.idGroupe != Ext.ts.idGroupe ? 'fichePartenaireCls' : '';
										}
									},
									tbar: ['->',{
										ref: '../../../../../btnExporterFiches',
										text: Ext.ts.Lang.exporterSelectionEnCSV,
										iconCls: 'page_white_excel',
										handler: this.exportSelection,
										scope: this,
										disabled: false
									},'-',{
										ref: '../../../../../btnAddToPanier',
										text: Ext.ts.Lang.addToPanier,
										iconCls: 'basket_put',
										handler: function() {
											var selection = this.smFiche.getSelections();
											this.addToPanier(selection);
										},
										scope: this,
										disabled: true
									},'-',{
										ref: '../../../../../btnPublierFiches',
										text: Ext.ts.Lang.publierFiches,
										iconCls: 'tick',
										handler: function() {
											var idFiches = this.getSelectionFiches();
											this.setPublicationFiches(idFiches, true);
										},
										scope: this,
										disabled: true
									},{
										ref: '../../../../../btnDepublierFiches',
										text: Ext.ts.Lang.depublierFiches,
										iconCls: 'tick_off',
										handler: function() {
											var idFiches = this.getSelectionFiches();
											this.setPublicationFiches(idFiches, false);
										},
										scope: this,
										disabled: true
									},{
										ref: '../../../../../btnDeleteFiches',
										text: Ext.ts.Lang.deleteFiches,
										iconCls: 'delete',
										handler: function() {
											var idFiches = this.getSelectionFiches();
											this.deleteFiches(idFiches);
										},
										scope: this,
										disabled: true
									}
									<?php if (tsDroits::getDroit('FICHE_CREATE')) { ?> 
									,'-',{
										text: Ext.ts.Lang.createFiche,
										iconCls: 'add',
										autoHeight: true,
										handler: this.createFiche,
										scope: this
									}
									<?php } ?>
									],
									bbar: {
										xtype: 'autosizepaging',
										store: this.storeFiche,
										displayInfo: true,
										displayMsg: Ext.ts.Lang.pagingFiche,
										emptyMsg: Ext.ts.Lang.pagingFicheEmpty,
										reloadOnResize: true
									},
									plugins: [
										new Ext.ts.GridFilters(),
										new Ext.ts.gridKeySearch({
											dataIndex: 'raisonSociale',
											enterHandler: function(record) {
												Ext.ts.open('fiche', {idFiche: record.data.idFiche});
											},
											scope: this
										})
									],
									listeners: {
										rowdblclick: {
											fn: function(grid) {
												var idFiche = grid.getSelectionModel().getSelected().data.idFiche;
												Ext.ts.open('fiche', {idFiche: idFiche});
											},
											scope: this
										}
									}
								},{
									itemId: 'mapFiche',
									title: Ext.ts.Lang.modeCarte,
									html: '<div id="mapContainer" />',
									tbar: ['->',{
										xtype: 'tbtext',
										ref: '../../../../../nbGeoloc',
										text: Ext.ts.Lang.rechercheFicheEmpty
									}],
									listeners: {
										activate: {
											fn: this.loadMap,
											scope: this
										}
									}
								}]
							},{
								id: 'westFiches',
								itemId: 'westFiches',
								region: 'west',
								hideBorders: true,
								width: 450,
								minWidth: 450,
								maxWidth: 450,
								margins: '5 0 5 5',
								cmargins: '5 0 5 5',
								header: false,
								collapsible: true,
								collapseMode: 'mini',
								collapsed: false,
								split: true,
								layout: 'accordion',
								layoutConfig: {
									titleCollapse: true,
									animate: true
								},
								items: [{
									itemId: 'searchEngine',
									xtype: 'searchengine',
									title: Ext.ts.Lang.moteurDeRecherche,
									iconCls: 'find',
									store: 'storeFiche'
								},{
									itemId: 'gridPanier',
									xtype: 'grid',
									ref: '../../../gridPanier',
									title: Ext.ts.Lang.monPanier,
									iconCls: 'basket',
									layout: 'fit',
									store: new Ext.data.JsonStore({
										fields: [
											{name: 'idFiche', type: 'int'},
											{name: 'raisonSociale', type: 'string'},
											{name: 'bordereau', type: 'string'}
										],
										sortInfo: {field: 'raisonSociale', direction: 'ASC'},
										listeners: {
											add: {fn: this.storePanierChange, scope: this},
											remove: {fn: this.storePanierChange, scope :this}
										}
									}),
									columns: [{
										header: Ext.ts.Lang.raisonSociale,
										dataIndex: 'raisonSociale',
										sortable: true,
										width: 200
									},{
										xtype: 'bordereaucolumn',
										sortable: true,
										width: 150
									},{
										xtype: 'actioncolumn',
										header: Ext.ts.Lang.outils,
										dataIndex: 'idFiche',
										width: 60,
										items: [{
											iconCls: 'delete',
											tooltip: Ext.ts.Lang.supprimer,
											handler: this.deleteFromPanier,
											scope: this
										}]
									}],
									tbar: ['->',{
										ref: '../../../../../btnExport',
										text: Ext.ts.Lang.exporter,
										iconCls: 'page_white_get',
										menu: {
											items: [{
												text: Ext.ts.Lang.formatXml,
												iconCls: 'page_white_code',
												handler: this.exportXml,
												scope: this
											},{
												text: Ext.ts.Lang.exporterSelectionEnCSV,
												iconCls: 'page_white_excel',
												handler: this.exportPanier,
												scope: this
											},{
												text: Ext.ts.Lang.formatPdf,
												iconCls: 'page_white_acrobat',
												handler: this.exportPdf,
												scope: this
											}]
										},
										disabled: true
									}],
									bbar: ['->',{
										xtype: 'tbtext',
										ref: '../../../../../countFichesPanier',
										text: '0 fiches'
									}]
									//,listeners: {
									//	afterrender: function(grid) {
									//		var panierDropTarget = new Ext.dd.DropTarget(grid.getView().scroller.dom, {
									//			ddGroup: 'GridDD',
									//			notifyDrop: function(ddSource, e, data){
									//				var selection = ddSource.dragData.selections;
									//				this.addToPanier(selection);
									//				return true;
									//			}
									//		});
									//	}
									//}
								}]
							}]
						};
					},
					
					initMap: function() {
						var myMask = new Ext.LoadMask('mapContainer', {
							msg: Ext.ts.Lang.waitMsg,
							store: this.storeFiche
						});
						
						this.map = new google.maps.Map(document.getElementById('mapContainer'), {
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});
						
						this.markers = [];
						
						this.clustersStyles = [];
						for (var i=0 ; i<5 ; i++) {
							this.clustersStyles.push({
								url: 'images/cluster.gif',
								height: 32,
								width: 32,
								opt_anchor: [16, 0],
								opt_textColor: '#fff',
								opt_textSize: 10
							});
						}
					},
					
					loadMap: function() {
						var activeItem = this.centerFiches.getActiveTab().itemId;
						if (activeItem != 'mapFiche') {
							return true;
						}
						
						if (!Ext.isDefined(this.map)) {
							this.initMap();
						}
						else {
							this.clearMap();
						}
						
						Ext.ts.request({
							action: 'getFichesForMap',
							service: 'fiche',
							waitMsg: false,
							success: function(response) {
								var fiches = Ext.decode(response.responseText);
								var me = this;
								
								if (!Ext.isArray(fiches) || fiches.length == 0) {
									this.nbGeoloc.setText(Ext.ts.Lang.rechercheFicheEmpty);
									return false;
								}
								
								var minLat, maxLat, minLng, maxLng;
								
								Ext.each(fiches, function(fiche) {
									var gpsLat = parseFloat(fiche.gpsLat);
									var gpsLng = parseFloat(fiche.gpsLng);
									
									if (Ext.isEmpty(gpsLat) || gpsLat == 0 || Ext.isEmpty(gpsLng) || gpsLng == 0) {
										return true;
									}
									
									minLat = (!Ext.isDefined(minLat) || gpsLat < minLat) ? gpsLat : minLat;
									maxLat = (!Ext.isDefined(maxLat) || gpsLat > maxLat) ? gpsLat : maxLat;
									minLng = (!Ext.isDefined(minLng) || gpsLng < minLng) ? gpsLng : minLng;
									maxLng = (!Ext.isDefined(maxLng) || gpsLng > maxLng) ? gpsLng : maxLng;
									
									var marker = new google.maps.Marker({
										idFiche: fiche.idFiche,
										title: fiche.raisonSociale,
										icon: 'images/marker_generic.png',
										position: new google.maps.LatLng(gpsLat, gpsLng),
										draggable: false,
										clickable: true
									});
									this.markers.push(marker);
									
									var html = '<div class="gMapsBubble">'
										+ '<p class="gMapsBubbleTitle">'+(!Ext.isEmpty(fiche.raisonSociale) ? fiche.raisonSociale : Ext.ts.Lang.sansTitre)+'</p>'
										+ '<div class="gMapsBubbleBtn">'
										//+ '<input type="button" value="'+Ext.ts.Lang.visualiser+'" class="visualiserBtn" id="displayFiche'+fiche.idFiche+'" />'
										+ '<input type="button" value="'+Ext.ts.Lang.modifier+'" class="modifierBtn" id="openFiche'+fiche.idFiche+'" />'
										+ '</div>'
										+ '</div>';
									
									google.maps.event.addListener(marker, 'click', function(e) {
										var infobox = new google.maps.InfoWindow({
											content: html,
											position: marker.getPosition()
										});
										google.maps.event.addListener(infobox, 'domready', function() {
											/*var node = Ext.getDom('displayFiche'+fiche.idFiche);
											var element = Ext.get(node);
											element.on('click', function() { this.displayFiche(fiche.idFiche); }, me);*/
											var node = Ext.getDom('openFiche'+fiche.idFiche);
											var element = Ext.get(node);
											element.on('click', function() { Ext.ts.open('fiche', {idFiche: fiche.idFiche}); }, me);
										});
										infobox.open(me.map, marker);
									});
									
								}, this);
								
								var bounds = new google.maps.LatLngBounds(
									new google.maps.LatLng(minLat, minLng),
									new google.maps.LatLng(maxLat, maxLng)
								);
								this.map.setCenter(new google.maps.LatLng(bounds.getCenter().lat(), bounds.getCenter().lng()));
								this.map.fitBounds(bounds);
								
								this.markerClusterer = new MarkerClusterer(this.map, this.markers, {
									styles: this.clustersStyles,
									zoomOnClick: true
								});
								google.maps.event.addListener(this.markerClusterer, 'clusterclick', function(cluster) {
									var markers = cluster[0].markers_;
									if (markers.length <= 20) {
										var idFiches = [];
										Ext.each(markers, function(marker) {
											idFiches.push(marker.idFiche);
										});
										me.displayFiches(idFiches);
										me.markerClusterer.zoomOnClick_ = false;
									}
									else {
										me.markerClusterer.zoomOnClick_ = true;
									}
								});
								this.nbGeoloc.setText(
									this.markers.length+' '+(
										this.markers.length > 1 
										? Ext.ts.Lang.objetsGeolocalises
										: Ext.ts.Lang.objetGeolocalise
									)
								);
							},
							scope: this
						});
					},
					
					clearMap: function() {
						if (Ext.isDefined(this.markerClusterer)) {
							this.markerClusterer.clearMarkers();
						}
						Ext.each(this.markers, function(marker) {
							marker.setMap(null);
						});
						this.markers = [];
					},
					
					getWinApercuFiche: function() {
						var win = Ext.getCmp('winApercuFiche');
						if (!Ext.isDefined(win)) {
							win = new Ext.Window({
								id: 'winApercuFiche',
								width: 800,
								height: 600,
								title: Ext.ts.Lang.apercuFiche,
								hideBorders: true,
								modal: false,
								maximizable: true,
								collapsible: true,
								closeAction: 'hide',
								layout: 'fit',
								items: new Ext.TabPanel({
									ref: 'tab'
								})
							});
						}
						return win;
					},
					
					/*displayFiche: function(idFiche) {
						var win = this.getWinApercuFiche();
						
						var index = this.storeFiche.findExact('idFiche', parseInt(idFiche));
						var fiche = this.storeFiche.getAt(index).data;
						
						var panelFiche = win.tab.getComponent(idFiche);
						if (!Ext.isDefined(panelFiche)) {
							panelFiche = new Ext.Panel({
								itemId: idFiche,
								title: !Ext.isEmpty(fiche.raisonSociale) ? fiche.raisonSociale : Ext.ts.Lang.sansTitre,
								autoScroll: true,
								autoLoad: {
									url: Ext.ts.url({
										action: 'getFicheHtml',
										service: 'diffusion',
										plugin: 'diffusion',
										params: {
											idFiche: idFiche
										}
									}),
									scripts: true
								},
								closable: true
							});
							win.tab.add(panelFiche);
						}
						win.tab.setActiveTab(idFiche);
						
						win.show();
						win.expand();
					},*/
					
					displayFiches: function(idFiches) {
						var win = this.getWinApercuFiche();
						
						var panelFiches = win.tab.getComponent('apercuFiches');
						if (!Ext.isDefined(panelFiches)) {
							panelFiches = new Ext.Panel({
								itemId: 'apercuFiches',
								title: idFiches.length + ' ' + (idFiches.length > 1 ? Ext.ts.Lang.fiches : Ext.ts.Lang.fiche),
								closable: true,
								layout: 'fit',
								items: new Ext.DataView({
									ref: 'dataviewFiches',
									store: new Ext.ts.JsonStore({
										action: 'getListeFiches',
										service: 'fiche',
										fields: [
											{name: 'idFiche', type: 'int'},
											{name: 'raisonSociale', type: 'string'},
											{name: 'adresse1', type: 'string'},
											{name: 'codePostal', type: 'string'},
											{name: 'commune', type: 'string'},
											{name: 'telephone1', type: 'string'},
											{name: 'email', type: 'string'},
											{name: 'photo', type: 'string'}
										],
										sortInfo: {field: 'raisonSociale', direction: 'ASC'},
										remoteSort: true,
										listeners: {
											load: function() {
												panelFiches.dataviewFiches.fireEvent('resize');
											}
										}
									}),
									tpl: new Ext.XTemplate(
										'<tpl for=".">',
											'<div class="containerFicheListe">',
												'<h2>{raisonSociale}</h2>',
												'<tpl if="photo != \'\'">',
													'<div><img alt="{raisonSociale}" src="application/proxy/proxy.php?service=ficheFichier&action=resizeImage&u={photo}&w=200&h=150" /></div>',
												'</tpl>',
												'<div>',
													'<p>{adresse1}</p>',
													'<p>{codePostal} {commune}</p>',
												'</div>',
												'<div>',
													'<tpl if="telephone1 != \'\'"><p>Tel : {telephone1}</p></tpl>',
													'<tpl if="email != \'\'"><p>Email : {email}</p></tpl>',
												'</div>',
												/*'<div class="containerLienDetail">',
													'<p id="{idFiche}" class="lienDetail">En savoir +</p>',
												'</div>',*/
											'</div>',
										'</tpl>'
									),
									autoScroll: true,
									emptyText: 'Aucune fiche à afficher',
									listeners: {
										resize: {
											fn: function() {
												var dataviewSize = this.getWidth() - Ext.getScrollBarWidth();
												var nodes = Ext.query('.containerFicheListe');
												if (!Ext.isEmpty(nodes)) {
													var blockSize = Ext.get(nodes[0]).getWidth() + 15;
													var blockPerLine = Math.floor(dataviewSize/blockSize);
													var nbLine = Math.ceil(nodes.length/blockPerLine);
													for (var i=0 ; i<nbLine ; i++) {
														var maxHeight = 0;
														var elements = [];
														for (var j=0 ; j<blockPerLine ; j++) {
															var index = i*blockPerLine + j;
															if (Ext.isDefined(nodes[index])) {
																var element = Ext.get(nodes[index]);
																elements.push(element);
																element.setHeight('auto');
																maxHeight = element.getHeight() > maxHeight ? element.getHeight() : maxHeight;
															}
														}
														Ext.each(elements, function(element) {
															element.setHeight(maxHeight);
														});
													}
												}
											},
											delay: 500
										}/*,
										click: {
											fn: function(dw, index, node) {
												var element = Ext.get(node);
												if (element.hasClass('lienDetail')) {
													this.displayFiche(element.id);
												}
											},
											scope: this
										}*/
									}
								})
							});
							win.tab.add(panelFiches);
						}
						
						win.tab.setActiveTab('apercuFiches');
						win.show();
						win.expand();
						
						var myMask = new Ext.LoadMask(panelFiches.dataviewFiches.getEl(), {
							msg: Ext.ts.Lang.waitMsg,
							store: panelFiches.dataviewFiches.getStore(),
							removeMask: true
						});
						
						panelFiches.dataviewFiches.getStore().load({
							params: {
								idFiches: idFiches.join(',')
							}
						});
					},
					
					getFichePdf: function(idFiche) {
						Ext.ts.request({
							action: 'getFichePdf',
							service: 'ficheExport',
							params: {
								idFiche: idFiche
							},
							success: function(response) {
								var result = Ext.decode(response.responseText);
								window.open(result.reponse);
							}
						});
					},
					
					createFiche: function() {
						var win = new Ext.ts.ManagementWindow({
							title: Ext.ts.Lang.createFiche,
							height: 80,
							items: [{
								xtype: 'combobordereau',
								fieldLabel: Ext.ts.Lang.bordereau,
								hiddenName: 'bordereau',
								mode: 'remote',
								allowBlank: false
							},{
								xtype: 'autocompletecombocommune',
								fieldLabel: Ext.ts.Lang.commune,
								hiddenName: 'codeInsee',
								service: 'utilisateurDroitTerritoire',
								action: 'getCommunesUtilisateur',
								allowBlank: false
							}],
							action: 'createFiche',
							service: 'fiche',
							callback: function(form, action) {
								Ext.ts.open('fiche', {idFiche: action.result.reponse});
							},
							scope: this
						});
						win.show();
					},
					
					duplicateFiche: function(data) {
						var newRaisonSociale = (!Ext.isEmpty(data.raisonSociale) ? data.raisonSociale : Ext.ts.Lang.sansTitre) + ' (' + Ext.ts.Lang.copie + ')';
						var win = new Ext.ts.ManagementWindow({
							title: Ext.ts.Lang.duplicateFiche,
							height: 110,
							items: [{
								xtype: 'combobordereau',
								fieldLabel: Ext.ts.Lang.bordereau,
								hiddenName: 'bordereau',
								mode: 'remote',
								allowBlank: false,
								value: data.bordereau
							},{
								xtype: 'autocompletecombo',
								fieldLabel: Ext.ts.Lang.commune,
								listEmptyText: Ext.ts.Lang.rechercheCommuneEmpty,
								store: new Ext.ts.JsonStore({
									action: 'getCommunesUtilisateur',
									service: 'utilisateurDroitTerritoire',
									fields: [
										{name: 'codeInsee'},
										{name: 'libelle'}
									]
								}),
								valueField: 'codeInsee',
								displayField: 'libelle',
								hiddenName: 'codeInsee',
								allowBlank: false,
								value: data.codeInsee
							},{
								xtype: 'hidden',
								name: 'commune',
								value:data.commune
							},{
								xtype: 'textfield',
								width: 250,
								fieldLabel: Ext.ts.Lang.raisonSociale,
								name: 'raisonSociale',
								allowBlank: false,
								value: newRaisonSociale
							}],
							action: 'duplicateFiche',
							service: 'fiche',
							params: {idFiche: data.idFiche},
							callback: function(form, action) {
								Ext.ts.open('fiche', {idFiche: action.result.reponse});
							},
							scope: this
						});
						win.show();
					},
					
					setPublicationFiche: function(idFiche, publication) {
						Ext.ts.request({
							action: 'setPublicationFiche',
							service: 'fiche',
							params: {
								idFiche: idFiche,
								publication: publication
							},
							success: function(response) {
								this.storeFiche.reload();
							},
							scope: this
						});
					},
					
					setPublicationFiches: function(idFiches, publication) {
						Ext.ts.request({
							action: 'setPublicationFiches',
							service: 'fiche',
							params: {
								idFiches: idFiches,
								publication: publication
							},
							success: function(response) {
								this.storeFiche.reload();
							},
							scope: this
						});
					},
					
					deleteFiche: function(idFiche) {
						Ext.MessageBox.confirm(
							Ext.ts.Lang.confirmTitle,
							Ext.ts.Lang.deleteFiche,
							function (btn) {
								if (btn == 'yes') {
									Ext.ts.request({
										action: 'deleteFiche',
										service: 'fiche',
										params: {idFiche: idFiche},
										success: function(response) {
											this.storeFiche.reload();
										},
										scope: this
									});
								}
							},
							this
						);
					},
					
					deleteFiches: function(idFiches) {
						Ext.MessageBox.confirm(
							Ext.ts.Lang.confirmTitle,
							Ext.ts.Lang.deleteFichesConfirm,
							function (btn) {
								if (btn == 'yes') {
									Ext.ts.request({
										action: 'deleteFiches',
										service: 'fiche',
										params: {idFiches: idFiches},
										success: function(response) {
											this.storeFiche.reload();
										},
										scope: this
									});
								}
							},
							this
						);
					},
					
					deleteGroupePartenaireFiche: function(fiche) {
						Ext.MessageBox.confirm(
							Ext.ts.Lang.confirmTitle,
							Ext.ts.Lang.deleteFichePartenaire,
							function (btn) {
								if (btn == 'yes') {
									Ext.ts.request({
										action: 'deleteGroupePartenaireFiche',
										service: 'groupes',
										params: {idFiche: fiche.idFiche},
										success: function(response) {
											this.storeFiche.reload();
										},
										scope: this
									});
								}
							},
							this
						);
					},
					
					getSelectionFiches: function() {
						var selection = this.smFiche.getSelections();
						var idFiches = [];
						Ext.each(selection, function(record) {
							idFiches.push(record.data.idFiche);
						});
						return idFiches.join(',');
					},
					
					addToPanier: function(selection) {
						var store = this.gridPanier.getStore();
						var Record = store.recordType;
						Ext.each(selection, function(item) {
							var index = store.findExact('idFiche', item.data.idFiche);
							
							if (index != -1) {
								return;
							}
							
							store.add(new Record({
								idFiche: item.data.idFiche,
								raisonSociale: item.data.raisonSociale,
								bordereau: item.data.bordereau
							}));
						});
						this.smFiche.clearSelections();
						this.gridPanier.expand();
					},
					
					deleteFromPanier: function(grid, rowIndex, colIndex) {
						var record = grid.getStore().getAt(rowIndex);
						this.gridPanier.getStore().remove(record);
					},
					
					storePanierChange: function(store) {
						this.btnSaveAsPlaylist.setDisabled(store.getCount() == 0);
						this.btnExport.setDisabled(store.getCount() == 0);
						this.countFichesPanier.setText(store.getCount() + ' fiches');
					},
					
					exportXml: function() {
						var idFiches = [];
						this.gridPanier.getStore().each(function(record) {
							idFiches.push(record.data.idFiche);
						});
						
						Ext.ts.request({
							action: 'exportXml',
							service: 'ficheExport',
							params: {idFiches: idFiches.join(',')},
							success: function(response) {
								var result = Ext.decode(response.responseText);
								self.location = result.url;
							}
						});
					},

					exportPanier: function() {
						var idFiches = [];
						this.gridPanier.getStore().each(function(record) {
							idFiches.push(record.data.idFiche);
						});
						
						Ext.ts.request({
							plugin: 'exportCSV',
							action: 'exportPanier',
							service: 'exportCSV',
							params: {idFiches: idFiches.join(',')},
							success: function(response) {
								var result = Ext.decode(response.responseText);
								self.location = result.reponse;
							}
						});
					},

					exportSelection: function() {
							Ext.ts.request({
							plugin: 'exportCSV',
							action: 'exportSelection',
							service: 'exportCSV',
							params: this.storeFiche.baseParams,
							success: function(response) {
								var result = Ext.decode(response.responseText);
								self.location = result.reponse;
							}
						});
					},

					updateButtonExport: function(store) {
						this.btnExporterFiches.setText(Ext.ts.Lang.exporterSelectionEnCSV + ' (' + store.getTotalCount() + ' fiches)');
					},

					exportPdf: function(idFiche) {
						var idFiches = [];
						this.gridPanier.getStore().each(function(record) {
							idFiches.push(record.data.idFiche);
						});
						
						if (idFiches.length > 15)
						{
							Ext.Msg.show({
								title: Ext.ts.Lang.failureTitle,
								minWidth: 250,
								msg: Ext.ts.Lang.limiteExportPdf,
								buttons: Ext.Msg.OK,
								icon: Ext.Msg.WARNING
							});
							return false;
						}
						
						Ext.ts.request({
							action: 'exportPdf',
							service: 'ficheExport',
							params: {idFiches: idFiches.join(',')},
							success: function(response) {
								var result = Ext.decode(response.responseText);
								self.location = result.url;
							}
						});
					}
					
				});
				
			});
		</script>

<?php
	require_once('include/footer.php');
?>