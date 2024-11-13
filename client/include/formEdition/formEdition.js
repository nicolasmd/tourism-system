/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Client
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		GNU GPLv3 ; see LICENSE.txt
 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
 */

 /**
 * OBJET FICHE
 * Contiendra l'ensemble des informations concernant la fiche en cours d'édition
 */
Ext.ts.oFiche = {};



/**
 * ONGLETS
 * Un cookie permet de retenir l'onglet courant, celui ci restera
 * l'onglet affiché par défaut pendant une heure
 * Permet également d'initialiser le guide de saisie
 */
Ext.ts.Onglet = Ext.extend(Ext.Panel, {
	border: false,
	autoScroll: true,
	initComponent: function() {
		Ext.ts.Onglet.superclass.initComponent.call(this);

		this.on('activate', this.setActiveTabFiche);
		this.on('activate', this.initGuideDeSaisie);
	},

	setActiveTabFiche: function(p) {
		Ext.ts.setCookie('activeTabFiche', p.itemId, 3600);
	},

	initGuideDeSaisie: function() {
		var nodes = Ext.query('.editable');
		Ext.each(nodes, function(node) {
			var element = Ext.get(node);
			element.addListener('mouseover', function() {
				var cmp = Ext.getCmp(node.id);
				var name = cmp.name || cmp.tsName;

				if (Ext.isDefined(Ext.getCmp('guideSaisie')))
				{
					var blocks = Ext.getCmp('guideSaisie').getEl().query('.fieldHelp');
					Ext.each(blocks, function(block) {
						var el = Ext.get(block);

						if (el.getAttribute('rel') == name) {
							el.addClass('visible');
						}
						else {
							el.removeClass('visible');
						}
					});
				}
			})
		});
	}

});
Ext.reg('ts_onglet', Ext.ts.Onglet);



/**
 * FIELDSETS
 */
Ext.ts.FieldSet = Ext.extend(Ext.form.FieldSet, {
	labelWidth: 150,
	style: 'margin: 10px;',
	defaults: {
		minLength: 1
	},
	initComponent: function() {
		Ext.ts.FieldSet.superclass.initComponent.call(this);
	}
});
Ext.reg('ts_fieldset', Ext.ts.FieldSet);



/**
 * FIELD CONTAINER
 */
/*Ext.ts.FieldContainer = Ext.extend(Ext.Panel, {
	layout: 'column',
	border: false,

	initComponent: function() {
		var field = this.items;
		this.items = [{
			layout: 'form',
			border: false,
			labelWidth: 150,
			items: field
		},{
			border: false,
			html: 'test'
		}];

		Ext.ts.FieldContainer.superclass.initComponent.call(this);
	}
});
Ext.reg('ts_fieldcontainer', Ext.ts.FieldContainer);*/



/**
 * TEXTFIELD
 * Surcharge de TextField ajoutant quelques fonctionnalités
 */
Ext.ts.TextField = Ext.extend(Ext.form.TextField, {
	width: 250,

	initComponent: function() {
		Ext.ts.TextField.superclass.initComponent.call(this);
	},

	showDiff: function(oldValue, newValue) {
		if (oldValue != newValue) {
			this.addClass('fieldDiffAdded');

			this.tooltipDiff = new Ext.ToolTip({
				title: Ext.ts.Lang.ancienneValeur,
				target: this.getEl(),
				anchor: 'left',
				padding: 2,
				html: !Ext.isEmpty(oldValue)
					? '<span style="color:red;">' + oldValue + '<span>'
					: '<span style="font-style:italic;">Non renseignée<span>'
			});
		}
	},

	hideDiff: function() {
		this.removeClass('fieldDiffAdded');
		if (Ext.isDefined(this.tooltipDiff)) {
			this.tooltipDiff.destroy();
		}
	}

});
Ext.reg('ts_textfield', Ext.ts.TextField);



/**
 * DATEFIELD
 * Surcharge de DateField ajoutant quelques fonctionnalités
 */
Ext.ts.DateField = Ext.extend(Ext.form.DateField, {
	width: 100,

	initComponent: function() {
		Ext.ts.DateField.superclass.initComponent.call(this);
	},

	showDiff: function(oldValue, newValue) {
		if (oldValue != newValue) {
			this.addClass('fieldDiffAdded');

			this.tooltipDiff = new Ext.ToolTip({
				title: Ext.ts.Lang.ancienneValeur,
				target: this.getEl(),
				anchor: 'left',
				padding: 2,
				cls: 'fieldDiffDeleted',
				html: !Ext.isEmpty(oldValue)
					? '<span style="color:red;">' + oldValue + '<span>'
					: '<span style="font-style:italic;">Non renseignée<span>'
			});
		}
	},

	hideDiff: function() {
		this.removeClass('fieldDiffAdded');
		if (Ext.isDefined(this.tooltipDiff)) {
			this.tooltipDiff.destroy();
		}
	}

});
Ext.reg('ts_datefield', Ext.ts.DateField);



/**
 * TEXTAREA
 * Surcharge de TextArea ajoutant quelques fonctionnalités
 */
Ext.ts.TextArea = Ext.extend(Ext.form.TextArea, {
	width: 600,
	height: 120,

	initComponent: function() {
		Ext.ts.TextArea.superclass.initComponent.call(this);
	},

	showDiff: function(oldValue, newValue) {
		if (oldValue != newValue) {
			this.addClass('fieldDiffAdded');

			this.tooltipDiff = new Ext.ToolTip({
				title: Ext.ts.Lang.ancienneValeur,
				target: this.getEl(),
				anchor: 'left',
				padding: 2,
				cls: 'fieldDiffDeleted',
				html: !Ext.isEmpty(oldValue)
					? '<span style="color:red;">' + oldValue + '<span>'
					: '<span style="font-style:italic;">Non renseignée<span>'
			});
		}
	},

	hideDiff: function() {
		this.removeClass('fieldDiffAdded');
		if (Ext.isDefined(this.tooltipDiff)) {
			this.tooltipDiff.destroy();
		}
	}

});
Ext.reg('ts_textarea', Ext.ts.TextArea);



/**
 * TEXTAREA MULTI LANGUES
 * Composant permettant de gérer la traduction dans un textarea
 */
Ext.ts.MlTextArea = Ext.extend(Ext.TabPanel, {
	width: 600,
	height: 150,
	activeTab: 0,
	style: 'margin-top: 10px;',
	border: false,
	plain: true,
	deferredRender: false,
	langs: [
		{code: 'fr', libelle: Ext.ts.Lang.francais},
		{code: 'en', libelle: Ext.ts.Lang.anglais},
		{code: 'de', libelle: Ext.ts.Lang.allemand},
		{code: 'es', libelle: Ext.ts.Lang.espagnol},
		{code: 'nl', libelle: Ext.ts.Lang.neerlandais},
		{code: 'it', libelle: Ext.ts.Lang.italien}
	],

	initComponent: function() {
		this.addEvents('change');

		this.firstShow = false;
		this.items = [];
		this.fields = {};

		Ext.each(this.langs, function(lang) {
			var fieldName = Ext.isDefined(this.tsName)
				? this.tsName + '_' + lang.code
				: undefined;

			this.fields[lang.code] = new Ext.form.TextArea({
				id: fieldName,
				name: fieldName,
				maxLength: this.maxLength,
				listeners: {
					change: this.onChange,
					scope: this
				}
			});

			this.items.push({
				xtype: 'panel',
				title: lang.libelle,
				layout: 'fit',
				items: this.fields[lang.code],
				listeners: {
					activate: {
						fn: function(p) {
							if (this.firstShow) {
								p.getComponent(0).focus();
							}
							this.firstShow = true;
						},
						scope: this
					}
				}
			});
		}, this);

		Ext.ts.MlTextArea.superclass.initComponent.call(this);
	},

	setValue: function(values) {
		Ext.iterate(values, function(key, item) {
			this.fields[key].setValue(item);
		}, this);
	},

	getValue: function() {
		var value = undefined;
		Ext.iterate(this.fields, function(lang, field) {
			if (!Ext.isEmpty(field.getValue())) {
				if (!Ext.isDefined(value)) {
					value = {};
				}
				value[lang] = field.getValue();
			}
		}, this);
		return value;
	},

	reset: function() {
		for (var i in this.fields) {
			if (!Ext.isFunction(this.fields[i])) {
				this.fields[i].reset();
			}
		}
	},

	onChange: function() {
		this.fireEvent('change');
	}

});
Ext.reg('mltextarea', Ext.ts.MlTextArea);



/**
 * OBJECT GRID
 * Composant permettant de gérer les champs complexe sous forme d'une grid
 */
Ext.ts.ObjectGrid = Ext.extend(Ext.Panel, {
	width: 600,
	height: 350,
	border: true,
	hideBorders: true,
	activeItem: 0,
	layout: 'card',

	initComponent: function() {
		this.addEvents('visualisation', 'edition', 'beforesave', 'change');

		this.hiddenField = new Ext.form.Hidden({
			name: this.tsName
		});

		// Permet de différer le rendu
		this.hidden = false;
		this.storeToLoad = 0;

		// BUTTONS
		this.buttonAdd = new Ext.Button({
			text: Ext.ts.Lang.ajouter,
			iconCls: 'add',
			handler: function() {
				this.setModeEdition();
			},
			scope: this
		});
		this.buttonSave = new Ext.Button({
			text: Ext.ts.Lang.valider,
			iconCls: 'disk',
			handler: this.saveItem,
			scope: this,
			hidden: true
		});
		this.buttonCancel = new Ext.Button({
			text: Ext.ts.Lang.annuler,
			iconCls: 'cancel',
			handler: this.setModeVisualisation,
			scope: this,
			hidden: true
		});

		// FORMULAIRE
		this.buildFormulaire();

		// GRILLE
		this.buildGrid();

		// CONTAINER
		this.items = [
			this.grid,
			this.form,
			this.hiddenField
		];
		this.tbar = new Ext.Toolbar({
			defaults: {
				width: 80
			},
			items: [
				'->',
				this.buttonAdd,
				this.buttonSave,
				this.buttonCancel
			]
		});

		Ext.ts.ObjectGrid.superclass.initComponent.call(this);

		this.on('change', this.setHiddenValue, this);
	},

	buildFormulaire: function() {
		var items = [];
		Ext.each(this.formItems, function(fieldset) {
			var fieldsetItems = [];
			Ext.each(fieldset.items, function(field) {
				this.fields[field].submitValue = false;
				this.fields[field] = new Ext.create(this.fields[field]);

				// A la fin de chaque load, on vérifie si le store est le dernier.
				// Si c'est le cas on peut afficher le composant.
				if (this.fields[field].getXType() == 'combomth') {
					this.storeToLoad++;
					this.hidden = true;
					this.fields[field].getStore().on('load', this.deferRender, this);
				}

				fieldsetItems.push(this.fields[field]);
			}, this);
			items.push({
				xtype: 'fieldset',
				title: fieldset.fieldset,
				labelWidth: 150,
				style: 'margin: 10px',
				items: fieldsetItems
			});
		}, this);
		this.form = new Ext.Panel({
			items: items
		});
	},

	buildGrid: function() {
		var readerFields = [];
		var columns = [];
		Ext.iterate(this.fields, function(fieldName, field) {
			// Champs du reader
			var readerField = {
				name: fieldName,
				type: 'auto'
			};
			if (field.xtype == 'datefield') {
				readerField.type = 'date';
				readerField.dateFormat = 'Y-m-d';
			}
			readerFields.push(readerField);

			// Colonnes de la grille
			var column = {
				header: field.fieldLabel,
				dataIndex: fieldName,
				hidden: !Ext.isDefined(this.gridColumns[fieldName])
			};
			if (field.xtype.indexOf('combo', 0) != -1) {
				column.renderer = {
					fn: function(key, metaData, record, rowIndex, colIndex, store) {
						return this.fields[fieldName].getValueByKey(key);
					},
					scope: this
				}
			}
			Ext.apply(column, this.gridColumns[fieldName]);
			columns.push(column);
		}, this);
		columns.push({
			xtype: 'actioncolumn',
			header: Ext.ts.Lang.outils,
			width: 60,
			fixed: true,
			items: [{
				iconCls: 'edit',
				tooltip: Ext.ts.Lang.modifier,
				handler: function(grid, rowIndex, colIndex) {
					var record = grid.getStore().getAt(rowIndex);
					this.setModeEdition(record);
				},
				scope: this
			},{
				iconCls: 'delete',
				tooltip: Ext.ts.Lang.supprimer,
				handler: function(grid, rowIndex, colIndex) {
					var record = grid.getStore().getAt(rowIndex);
					this.deleteItem(record);
				},
				scope: this
			}]
		});
		this.grid = new Ext.grid.GridPanel({
			border: false,
			store: new Ext.data.JsonStore({
				fields: readerFields,
				listeners: {
					add: {
						fn: this.onChange,
						scope: this
					},
					update: {
						fn: this.onChange,
						scope: this
					},
					remove: {
						fn: this.onChange,
						scope: this
					}
				}
			}),
			colModel: new Ext.grid.ColumnModel({
				defaults: {
					sortable: false,
					width: 150
				},
				columns: columns
			}),
			viewConfig: {
				forceFit: true
			}
		});
	},

	setValue: function(data) {
		if (Ext.isDefined(data) && Ext.isArray(data)) {
			this.grid.getStore().loadData(data);
			this.setHiddenValue();
		}
	},

	deferRender: function() {
		this.storeToLoad--;
		if (this.storeToLoad == 0) {
			this.show();
		}
	},

	setModeEdition: function(record) {
		this.getLayout().setActiveItem(1);

		this.buttonAdd.setVisible(false);
		this.buttonSave.setVisible(true);
		this.buttonCancel.setVisible(true);

		if (Ext.isDefined(record)) {
			this.recordEdited = record;

			Ext.iterate(this.recordEdited.data, function(k, v) {
				if (!Ext.isEmpty(v)) {
					if (this.fields[k].xtype == 'datefield' && !Ext.isDate(v)) {
						v = new Date(v);
					}
					this.fields[k].setValue(v);
				}
			}, this);
		}

		this.fireEvent('edition', this.recordEdited);
	},

	setModeVisualisation: function() {
		Ext.iterate(this.fields, function(k, v) {
			this.fields[k].reset();
		}, this);

		delete this.recordEdited;

		this.getLayout().setActiveItem(0);

		this.buttonSave.setVisible(false);
		this.buttonCancel.setVisible(false);
		this.buttonAdd.setVisible(true);

		this.fireEvent('visualisation');
	},

	saveItem: function() {
		var error = false;

		Ext.iterate(this.fields, function(fieldName, field) {
			error = this.checkField(field) || error;
		}, this);

		if (error) {
			Ext.MessageBox.alert(Ext.ts.Lang.failureTitle, Ext.ts.Lang.objectgridError);
			return false;
		}

		if (!this.fireEvent('beforesave', this.recordEdited)) {
			return false;
		}

		if (Ext.isDefined(this.recordEdited)) {
			Ext.iterate(this.fields, function(fieldName, field) {
				this.recordEdited.set(fieldName, field.getValue());
			}, this);
		}
		else {
			var data = {};
			Ext.iterate(this.fields, function(fieldName, field) {
				data[fieldName] = field.getValue();
			}, this);
			this.grid.getStore().add(new Ext.data.Record(data));
		}

		this.setModeVisualisation();
	},

	deleteItem: function(record) {
		Ext.MessageBox.confirm(
			Ext.ts.Lang.confirmTitle,
			Ext.ts.Lang.objectgridDelete,
			function (btn) {
				if (btn == 'yes') {
					this.grid.getStore().remove(record);
				}
			},
			this
		);
	},

	checkField: function(field) {
		if (field.required === true && Ext.isEmpty(field.getValue())) {
			field.markInvalid();
			return true;
		}
		return false;
	},

	onChange: function() {
		this.fireEvent('change');
	},

	setHiddenValue: function() {
		var data = [];
		this.grid.getStore().each(function(record) {
			var dataTmp = {};
			Ext.iterate(record.data, function(k, v) {
				if (Ext.isDate(v)) {
					v = v.format('Y-m-d');
				}
				dataTmp[k] = v;
			});
			data.push(dataTmp);
		});
		this.hiddenField.setValue(Ext.encode(data));
	}

});
Ext.reg('objectgrid', Ext.ts.ObjectGrid);



/**
 * OBJECT FORM
 * Composant permettant de gérer les champs complexe sous forme d'un formulaire
 */
Ext.ts.ObjectForm = Ext.extend(Ext.Container, {
	width: 600,

	initComponent: function() {
		this.built = false;
		this.addEvents('built', 'change');

		this.hiddenField = new Ext.form.Hidden({
			name: this.tsName
		});

		this.primaryField = Ext.ts.complexeFields[this.tsName];

		var filter = Ext.ts.MTH.getFilterKey(Ext.ts.oFiche.bordereau, this.tsName);

		var baseParams = {};
		if (filter != false) {
			baseParams = filter;
		}
		if (Ext.isDefined(this.LS)) {
			baseParams.ls = this.LS;
		}
		if (Ext.isDefined(this.key)) {
			baseParams.key = this.key;
		}
		if (Ext.isDefined(this.pop)) {
			baseParams.pop = this.pop;
		}

		this.store = new Ext.ts.JsonStore({
			action: 'getListeThesaurus',
			service: 'thesaurus',
			baseParams: baseParams,
			autoLoad: true,
			root: 'dataRoot',
			totalProperty: 'dataCount',
			sortInfo: {
				field: 'cle',
				direction: 'ASC'
			},
			fields: [
				{name: 'cle'},
				{name: 'libelle'},
				{name: 'list'}
			],
			preLoad: true
		});

		this.items = this.hiddenField;

		Ext.ts.ObjectForm.superclass.initComponent.call(this);

		this.store.on('load', this.buildFields, this);
		this.on('change', this.setHiddenValue, this);
	},

	setValue: function(data) {
		if (Ext.isDefined(data)) {
			if (!this.built) {
				this.on('built', function() { this.setValueFields(data); }, this, {single: true});
			}
			else {
				this.setValueFields(data);
			}
		}
	},

	setValueFields: function(data) {
		Ext.each(data, function(item, key) {
			Ext.iterate(item, function(identifiant, value) {
				if (Ext.isDefined(this.fields[item[this.primaryField]])) {
					if (Ext.isDefined(this.fields[item[this.primaryField]][identifiant])) {
						this.fields[item[this.primaryField]][identifiant].setValue(value);
					}
				}
			}, this);
		}, this);
		this.setHiddenValue();
	},

	buildFields: function() {
		this.fields = {};

		var items = [];
		this.store.each(function(record) {
			var fieldsetItems = [];
			Ext.iterate(this.fieldsCfg, function(identifiant, fieldCfg) {
				if (!Ext.isDefined(this.fields[record.data.cle])) {
					this.fields[record.data.cle] = {};
				}

				fieldCfg.submitValue = false;
				var field = new Ext.create(fieldCfg);
				field.on('change', this.onChange, this);
				fieldsetItems.push(field);

				this.fields[record.data.cle][identifiant] = field;
			}, this);
			items.push({
				xtype: 'fieldset',
				title: record.data.libelle,
				width: this.width,
				labelWidth: 150,
				items: fieldsetItems
			});
		}, this);

		this.add(items);

		this.built = true;
		this.fireEvent('built');
	},

	onChange: function() {
		this.fireEvent('change');
	},

	setHiddenValue: function() {
		var data = [];
		Ext.iterate(this.fields, function(cleValue, fields) {
			var empty = true;
			var dataTmp = {};
			dataTmp[this.primaryField] = cleValue;
			Ext.iterate(fields, function(identifiant, field) {
				var value = field.getValue();
				if (!Ext.isEmpty(value)) {
					empty = false;
					if (Ext.isDate(value)) {
						value = value.format('Y-m-d');
					}
					dataTmp[identifiant] = value;
				}
			}, this);
			if (empty === false) {
				data.push(dataTmp);
			}
		}, this);
		this.hiddenField.setValue(Ext.encode(data));
	},

	showDiff: function(oldValue, newValue) {
		// Prepare
		var oldArr = {};
		Ext.each(oldValue, function(item) {
			oldArr[item[this.primaryField]] = item;
		}, this);
		var newArr = {};
		Ext.each(newValue, function(item) {
			newArr[item[this.primaryField]] = item;
		}, this);

		// Compare
		Ext.iterate(this.fields, function(cleValue, fields) {
			Ext.iterate(fields, function(identifiant, field) {
				var fieldOldValue = Ext.isDefined(oldArr[cleValue]) ? Ext.value(oldArr[cleValue][identifiant], '') : '';
				var fieldNewValue = Ext.isDefined(newArr[cleValue]) ? Ext.value(newArr[cleValue][identifiant], '') : '';
				if (Ext.isFunction(field.showDiff)) {
					field.showDiff(fieldOldValue, fieldNewValue);
				}
			}, this);
		}, this);
	},

	hideDiff: function() {
		Ext.iterate(this.fields, function(cleValue, fields) {
			Ext.iterate(fields, function(identifiant, field) {
				if (Ext.isFunction(field.hideDiff)) {
					field.hideDiff();
				}
			}, this);
		}, this);
	}

});
Ext.reg('objectform', Ext.ts.ObjectForm);



/**
 * LISTE DEROULANTE - THESAURUS
 * ComboBox proposant une liste venant du thésaurus
 */
Ext.ts.ComboMTH = Ext.extend(Ext.form.ComboBox, {
	width: 250,
	mode: 'local',
	valueField: 'cle',
	displayField: 'libelle',
	triggerAction: 'all',
	editable: false,
	resizable: true,
	emptyText: Ext.ts.Lang.combomthEmptyText,

	storeCfg: {},
	emptyValue: true,

	initComponent: function() {
		var filter = Ext.ts.MTH.getFilterKey(Ext.ts.oFiche.bordereau, this.tsName);

		var baseParams = {};
		if (filter != false) {
			baseParams = filter;
		}
		if (Ext.isDefined(this.LS)) {
			baseParams.ls = this.LS;
		}
		if (Ext.isDefined(this.key)) {
			baseParams.key = this.key;
		}
		if (Ext.isDefined(this.pop)) {
			baseParams.pop = this.pop;
		}

		var storeCfg = {
			action: 'getListeThesaurus',
			service: 'thesaurus',
			baseParams: baseParams,
			autoLoad: true,
			root: 'dataRoot',
			totalProperty: 'dataCount',
			sortInfo: {
				field: 'libelle',
				direction: 'ASC'
			},
			fields: [
				{name: 'cle'},
				{name: 'libelle'}
			],
			listeners: {
				load: {
					fn: this.onStoreLoad,
					scope: this
				}
			},
			preLoad: true
		};
		storeCfg = Ext.apply(storeCfg, this.storeCfg);

		this.store = new Ext.ts.JsonStore(storeCfg);

		if (Ext.isDefined(this.tsName)) {
			this.hiddenName = this.tsName;
		}

		Ext.ts.ComboMTH.superclass.initComponent.call(this);
	},

	onStoreLoad: function(store, record) {
		if (record.length > 0 && this.emptyValue)
		{
			var Record = store.recordType;
			store.insert(0, [new Record({
				cle: '',
				libelle: Ext.ts.Lang.combomthEmptyText
			})]);
		}

		if (record.length == 0)
		{
			this.hide();
		}
	},

	showDiff: function(oldValue, newValue) {
		if (oldValue != newValue) {
			this.addClass('fieldDiffAdded');

			if (!Ext.isEmpty(oldValue)) {
				var index = this.store.findExact('cle', oldValue);
				if (index != -1) {
					var record = this.store.getAt(index);
					oldValue = record.data.libelle;
				}
			}

			this.tooltipDiff = new Ext.ToolTip({
				title: Ext.ts.Lang.ancienneValeur,
				target: this.getEl(),
				anchor: 'left',
				padding: 2,
				cls: 'fieldDiffDeleted',
				html: (Ext.isDefined(index) && index != -1)
					? '<span style="color:red;">' + oldValue + '<span>'
					: '<span style="font-style:italic;">Non renseignée<span>'
			});
		}
	},

	hideDiff: function() {
		this.removeClass('fieldDiffAdded');
		if (Ext.isDefined(this.tooltipDiff)) {
			this.tooltipDiff.destroy();
		}
	}

});
Ext.reg('combomth', Ext.ts.ComboMTH);


/**
 * LISTE DE CHECKBOX - THESAURUS
 * CheckboxGroup proposant une liste venant du thésaurus
 */
Ext.ts.ListMTH = Ext.extend(Ext.Container, {
	border: false,
	groupColumns: 4,

	initComponent: function () {
		this.built = false;
		this.addEvents('built' , 'change');

		this.hiddenField = new Ext.form.Hidden({
			name: this.tsName
		});
		this.primaryField = Ext.ts.complexeFields[this.tsName] || 'cle';

		var filter = Ext.ts.MTH.getFilterKey(Ext.ts.oFiche.bordereau, this.tsName);

		var baseParams = {};
		if (filter != false) {
			baseParams = filter;
		}
		if (Ext.isDefined(this.LS)) {
			baseParams.ls = this.LS;
		}
		if (Ext.isDefined(this.key)) {
			baseParams.key = this.key;
		}
		if (Ext.isDefined(this.pop)) {
			baseParams.pop = this.pop;
		}

		this.store = new Ext.ts.JsonStore({
			action: 'getListeThesaurus',
			service: 'thesaurus',
			baseParams: baseParams,
			autoLoad: true,
			root: 'dataRoot',
			totalProperty: 'dataCount',
			sortInfo: {
				field: 'libelle',
				direction: 'ASC'
			},
			fields: [
				{name: 'cle'},
				{name: 'libelle'},
				{name: 'list'}
			],
			preLoad: true
		});

		this.width = this.groupColumns * 250;
		this.items = this.hiddenField;

		Ext.ts.ListMTH.superclass.initComponent.call(this);

		this.store.on('load', this.buildFields, this);
		this.on('change', this.setHiddenValue, this);
	},

	setValue: function (data) {
		if (Ext.isDefined(data)) {
			if (!this.built) {
				this.on('built', function() { this.setValueFields(data); }, this, {single: true});
			}
			else {
				this.setValueFields(data);
			}
		}
	},

	setValueFields: function (data) {
		// Pour éviter que l'event change soit lancé
		this.suspendEvents();
		
		this.reset();
		Ext.each(data , function (item) {
			var cle = Ext.isObject(item) ? item[this.primaryField] : item;
			if (Ext.isDefined(this.fields[cle])) {
				this.fields[cle].setValue(true);
			}
		}, this);
		this.setHiddenValue();
		
		this.resumeEvents();
	},

	getValue: function () {
		var data = [];
		Ext.iterate(this.fields, function (cle, field) {
			if (field.getValue() === true) {
				dataTmp = {};
				dataTmp[this.primaryField] = cle;
				data.push(dataTmp);
			}
		}, this);
		return data;
	},

	reset: function () {
		Ext.iterate(this.fields, function (cle, field) {
			field.setValue(false);
		}, this);
	},

	buildFields: function () {
		var checkboxes = [];
		this.fields = {};

		this.store.each(function (item) {
			// Provenance du code
			var match = item.data.cle.match(/^([0-9]{2,})\./);
			var prefixe = (!Ext.isEmpty(match) && parseInt(match[1]) >= 99) ? parseInt(match[1]) : 0;
			var thesaurus = Ext.ts.thesaurii[prefixe];

			if (Ext.isArray(item.data.list)) {
				// Combo : jamais utilisé
				this.fields[item.data.cle] = new Ext.form.ComboBox({
					fieldLabel: item.data.libelle ,
					mode: 'local',
					valueField: 'cle',
					displayField: 'libelle',
					triggerAction: 'all',
					editable: false,
					resizable: true,
					store: new Ext.data.JsonStore({
						fields: [
							{name: 'cle'},
							{name: 'libelle'}
						],
						sortInfo: {
							field: 'libelle',
							direction: 'ASC'
						},
						data: item.data.list
					}),
					listeners: {
						change: this.onChange,
						scope: this
					}
				});
				this.findParentByType('ts_fieldset').add(this.fields[item.data.cle]);
			}
			else {
				// Checkbox
				this.fields[item.data.cle] = new Ext.form.Checkbox({
					id: item.data.cle,
					hideLabel: true,
					boxLabel: item.data.libelle,
					inputValue: item.data.cle,
					listeners: {
						render: function(c) {
							Ext.QuickTips.register({
								target: c.getEl(),
								text: thesaurus
							});
						},
						// L'event change ne fonctionne pas avec Chrome
						check: this.onChange,
						scope: this
					}
				});
				checkboxes.push(this.fields[item.data.cle]);
			}
		}, this);
		
		if (checkboxes.length > 0) {
			this.checkboxContainer = new Ext.form.CheckboxGroup({
				columns: this.groupColumns,
				items: checkboxes
			});

			this.add(this.checkboxContainer);
		}

		this.built = true;
		this.fireEvent('built');
	},

	onChange: function () {
		this.fireEvent('change');
	},

	setHiddenValue: function () {
		var data = this.getValue();
		this.hiddenField.setValue(Ext.encode(data));
	},

	showDiff: function (oldValue, newValue) {
		if (!Ext.isArray(oldValue) || !Ext.isArray(newValue)) {
			return false;
		}

		var arrOld = [];
		Ext.each(oldValue, function (item, index) {
			arrOld.push(item[this.primaryField]);
		}, this);
		var arrNew = [];
		Ext.each(newValue, function (item, index) {
			arrNew.push(item[this.primaryField]);
		}, this);

		Ext.each(arrOld, function (item, index) {
			if (arrNew.indexOf(item) == -1) {
				this.fields[item].addClass('fieldDiffDeleted');
			}
		}, this);
		Ext.each(arrNew, function (item, index) {
			if (arrOld.indexOf(item) == -1) {
				this.fields[item].addClass('fieldDiffAdded');
			}
		}, this);
	},

	hideDiff: function () {
		Ext.iterate(this.fields, function (cle, field) {
			field.removeClass('fieldDiffAdded');
			field.removeClass('fieldDiffDeleted');
		}, this);
	}
} );
Ext.reg('listmth', Ext.ts.ListMTH);



/**
 * LISTE D'ITEMS DRAGGABLE
 * Grid proposant une liste venant du thésaurus
 */
Ext.ts.DDGrids = Ext.extend(Ext.Panel, {
	width: 1000,
	height: 300,
	border: false,
	layout: 'hbox',
	defaults: {flex: 1},
	layoutConfig: {align: 'stretch'},

	titleLeft: Ext.ts.Lang.ddgridsTitleLeft,
	titleRight: Ext.ts.Lang.ddgridsTitleRight,
	useDistance: false,

	initComponent: function() {
		this.addEvents('change');

		this.items = [];

		this.hiddenField = new Ext.form.Hidden({
			name: this.tsName
		});

		var filter = Ext.ts.MTH.getFilterKey(Ext.ts.oFiche.bordereau, this.tsName);

		var baseParams = {};
		if (filter != false) {
			baseParams = filter;
		}
		if (Ext.isDefined(this.LS)) {
			baseParams.ls = this.LS;
		}
		if (Ext.isDefined(this.key)) {
			baseParams.key = this.key;
		}
		if (Ext.isDefined(this.pop)) {
			baseParams.pop = this.pop;
		}

		var columns = [{
			id: 'libelle',
			header: this.titleRight,
			dataIndex: 'libelle',
			sortable: true,
			editable: false
		}];
		var fields = [
			{name: 'cle', type: 'string'},
			{name: 'libelle', type: 'string'}
		];

		if (this.useDistance) {
			columns.push({
				header: Ext.ts.Lang.distance,
				dataIndex: 'distance',
				sortable: true,
				width: 60,
				editor: new Ext.form.NumberField({
					allowNegative: false
				})
			},{
				header: Ext.ts.Lang.unite,
				dataIndex: 'unite',
				sortable: true,
				width: 60,
				editor: new Ext.ts.ComboMTH({
					LS: 'LS_DistanceUnite'
				}),
				renderer: function(value) {
					return this.editor.getValueByKey(value);
				}
			});

			fields.push(
				{name: 'distance', type: 'int'},
				{name: 'unite', type: 'string'},
				{name: 'added', type: 'boolean'},
				{name: 'deleted', type: 'boolean'}
			);
		}

		this.gridL = new Ext.grid.GridPanel({
			itemId: 'gridL',
			ddGroup: this.tsName+'GridRDDGroup',
			store: new Ext.ts.JsonStore({
				action: 'getListeThesaurus',
				service: 'thesaurus',
				baseParams: baseParams,
				autoLoad: true,
				root: 'dataRoot',
				totalProperty: 'dataCount',
				sortInfo: {
					field: 'libelle',
					direction: 'ASC'
				},
				fields: fields,
				preLoad: true
			}),
			columns: [{
				id: 'libelle',
				header: this.titleLeft,
				dataIndex: 'libelle',
				sortable: true
			}],
			viewConfig: {
				getRowClass: function(record) {
					return record.data.deleted ? 'rowDiffDeleted' : '';
				}
			},
			plugins: new Ext.ts.gridKeySearch({
				dataIndex: 'libelle',
				enterHandler: function(record) {
					this.transferItems([record], this.gridL, this.gridR);
					this.onChange();
				},
				scope: this
			}),
			autoExpandColumn : 'libelle',
			enableDragDrop: true,
			cls: 'ddgrids'
		});

		this.gridR = new Ext.grid.EditorGridPanel({
			itemId: 'gridR',
			ddGroup: this.tsName+'GridLDDGroup',
			store: new Ext.data.JsonStore({
				sortInfo: {
					field: 'libelle',
					direction: 'ASC'
				},
				fields: fields
			}),
			columns: columns,
			viewConfig: {
				getRowClass: function(record) {
					return record.data.added ? 'rowDiffAdded' : '';
				}
			},
			sm: new Ext.grid.RowSelectionModel(),
			autoExpandColumn : 'libelle',
			enableDragDrop: true,
			cls: 'ddgrids',
			clicksToEdit: 1,
			trackMouseOver: true,
			listeners: {
				afteredit: this.onChange,
				scope: this
			}
		});

		this.items = [this.gridL, this.gridR, this.hiddenField];

		Ext.ts.DDGrids.superclass.initComponent.call(this);

		// Permet de différer le traitement de setValue
		this.loaded = false;
		this.gridL.getStore().on('load', function() { this.loaded = true }, this, {single: true});

		this.on('afterlayout', this.initDD, this);
		this.on('change', this.setHiddenValue, this);
	},

	initDD: function() {
		var myApp = this;
		var gridL = this.gridL;
		var gridR = this.gridR;

		// Right to Left
		var GridLDropTargetEl = this.gridL.getView().scroller.dom;
		var GridLDropTarget = new Ext.dd.DropTarget(GridLDropTargetEl, {
			ddGroup: this.tsName+'GridLDDGroup',
			notifyDrop: function(ddSource, e, data){
				var records = ddSource.dragData.selections;
				myApp.transferItems(records, gridR, gridL);
				myApp.onChange();
				return true;
			}
		});

		// Left to Right
		var GridRDropTargetEl = this.gridR.getView().scroller.dom;
		var GridRDropTarget = new Ext.dd.DropTarget(GridRDropTargetEl, {
			ddGroup: this.tsName+'GridRDDGroup',
			notifyDrop: function(ddSource, e, data){
				var records = ddSource.dragData.selections;
				myApp.transferItems(records, gridL, gridR);
				myApp.onChange();
				return true;
			}
		});
	},

	setValue: function(data) {
		if (Ext.isDefined(data) && !Ext.isEmpty(data)) {
			this.data = data;
			if (!this.loaded) {
				this.gridL.getStore().on('load', this.loadData, this, {single: true});
			}
			else {
				this.loadData();
			}
		}
	},

	loadData: function() {
		var records = [];
		this.gridR.getStore().each(function(record) {
			records.push(record);
		}, this);
		this.transferItems(records, this.gridR, this.gridL);

		var records = [];
		Ext.each(this.data, function(item) {
			var record = this.gridL.getStore().getAt(this.gridL.getStore().findExact('cle', item.cle));
			if (Ext.isDefined(record)) {
				if (this.useDistance) {
					record.set('distance', item.distance);
					record.set('unite', item.unite);
					record.commit();
				}
				records.push(record);
			}
		}, this);
		this.transferItems(records, this.gridL, this.gridR);

		this.setHiddenValue();
	},

	transferItems: function(records, src, target) {
		Ext.each(records, function(record) {
			src.getStore().remove(record);
			target.getStore().add(record);
		}, this);
		target.getStore().sort('libelle', 'ASC');
	},

	onChange: function() {
		this.fireEvent('change');
	},

	setHiddenValue: function() {
		var data = [];
		this.gridR.getStore().each(function(rec) {
			var dataTmp = {
				cle: rec.data.cle
			};
			if (this.useDistance) {
				dataTmp.distance = rec.data.distance || '';
				dataTmp.unite = rec.data.unite || '';
			}
			data.push(dataTmp);
		}, this);
		this.hiddenField.setValue(Ext.encode(data));
	},

	showDiff: function(oldValue, newValue) {
		if (!Ext.isArray(oldValue) || !Ext.isArray(newValue)) {
			return false;
		}

		var arrOld = [];
		Ext.each(oldValue, function(item, index) {
			arrOld.push(item.cle);
		});
		var arrNew = [];
		Ext.each(newValue, function(item, index) {
			arrNew.push(item.cle);
		});

		Ext.each(arrOld, function(item, index) {
			if (arrNew.indexOf(item) == -1) {
				var record = this.gridL.getStore().getAt(this.gridL.getStore().findExact('cle', item));
				record.set('deleted', true);
			}
		}, this);
		Ext.each(arrNew, function(item, index) {
			if (arrOld.indexOf(item) == -1) {
				var record = this.gridR.getStore().getAt(this.gridR.getStore().findExact('cle', item));
				record.set('added', true);
			}
		}, this);
	},

	hideDiff: function() {
		this.gridL.getStore().each(function(record) {
			record.set('added', false);
			record.set('deleted', false);
		});
		this.gridR.getStore().each(function(record) {
			record.set('added', false);
			record.set('deleted', false);
		});
	}

});
Ext.reg('ddgrids', Ext.ts.DDGrids);



/**
 * GEOLOCALISATION GOOGLE MAPS
 * Composant permettant de gérer la localisation de la fiche
 */
Ext.ts.GeolocPanel = Ext.extend(Ext.Panel, {
	width: 600,
	height: 400,
	html: '<div id="geolocContainer" style="width:100%;height:100%;" />',
	latEmpty: 0,
	lngEmpty: 0,
	zoomEmpty: 1,
	zoomDefault: 13,

	initComponent: function() {
		this.addEvents('change');

		this.searchField = new Ext.form.TextField({
			width: 200,
			emptyText: Ext.ts.Lang.recherche,
			submitValue: false,
			selectOnFocus: true,
			enableKeyEvents: true,
			listeners: {
				scope: this,
				keydown: function(field, e) {
					var k = e.getKey();

					if (k == e.RETURN) {
						e.stopEvent();
						this.searchAction(field.getValue());
					}
				}
			}
		});
		this.searchButton = new Ext.Button({
			iconCls: 'find',
			tooltip: Ext.ts.Lang.rechercher,
			listeners: {
				scope: this,
				click: function() {
					this.searchAction(this.searchField.getValue());
				}
			}
		});

		this.gpsLat = new Ext.form.NumberField({
			name: 'gps_lat',
			width: 100,
			decimalPrecision: 8,
			decimalSeparator: '.',
			value: this.latEmpty,
			listeners: {
				scope: this,
				change: function() {
					this.zoom = this.zoomDefault;
					this.refreshMap();
					this.fireEvent('change');
				}
			}
		});

		this.gpsLng = new Ext.form.NumberField({
			name: 'gps_lng',
			width: 100,
			decimalPrecision: 8,
			decimalSeparator: '.',
			value: this.lngEmpty,
			listeners: {
				scope: this,
				change: function() {
					this.zoom = this.zoomDefault;
					this.refreshMap();
					this.fireEvent('change');
				}
			}
		});

		this.ficheAdresse = new Ext.Toolbar.TextItem({
			text: Ext.ts.oFiche.codePostal + ' ' + Ext.ts.oFiche.libelleCommune
		});

		this.geolocByAdresseButton = new Ext.Button({
			text: Ext.ts.Lang.geolocByFicheAdresse,
			listeners: {
				scope: this,
				click: function() {
					this.searchAction(this.ficheAdresse.el.dom.innerHTML);
				}
			}
		});

		this.zoom = this.zoomEmpty;

		this.tbar = [
			this.searchField,
			'-',
			this.searchButton,
			'->',
			Ext.ts.Lang.lat+': ', this.gpsLat,
			'-',
			Ext.ts.Lang.lng+': ', this.gpsLng
		];

		this.bbar = new Ext.Toolbar({
			items: [
				this.ficheAdresse,
				'->',
				this.geolocByAdresseButton
			]
		});

		Ext.ts.GeolocPanel.superclass.initComponent.call(this);

		this.on('afterrender', this.initMap, this);
	},

	initMap: function() {
		this.map = new google.maps.Map(document.getElementById('geolocContainer'), {
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		var adresse = '';
		if (Ext.isDefined(Ext.getCmp('adresse1')))
		{
			adresse = Ext.getCmp('adresse1').getValue();
			if (!Ext.isEmpty(adresse))
			{
				this.ficheAdresse.setText(adresse + ', ' + Ext.ts.oFiche.codePostal + ' ' + Ext.ts.oFiche.libelleCommune);
			}
		}

		// Si aucune géolocalisation existe, on essaye de l'affiner via Google
		if (this.gpsLat.getValue() == this.latEmpty && this.gpsLng.getValue() == this.lngEmpty) {
			this.searchAction(this.ficheAdresse.el.dom.innerHTML, true);
		}
		else {
			this.refreshMap();
		}
	},

	refreshMap: function() {
		var myApp = this;

		if (Ext.isDefined(this.marker)) {
			this.marker.setMap(null);
			delete this.marker;
		}

		this.map.setCenter(new google.maps.LatLng(this.gpsLat.getValue(), this.gpsLng.getValue()));
		this.map.setZoom(this.zoom);

		this.marker = new google.maps.Marker({
			position: new google.maps.LatLng(this.gpsLat.getValue(), this.gpsLng.getValue()),
			draggable: true
		});
		google.maps.event.addListener(this.marker, 'dragend', function(e) {
			myApp.gpsLat.setValue(e.latLng.lat());
			myApp.gpsLng.setValue(e.latLng.lng());
			myApp.fireEvent('change');
		});
		this.marker.setMap(this.map);
	},

	setValue: function(data) {
		if (!Ext.isEmpty(data.gpsLat) && !Ext.isEmpty(data.gpsLng)) {
			this.gpsLat.setValue(this.formatNumber(data.gpsLat));
			this.gpsLng.setValue(this.formatNumber(data.gpsLng));
			this.zoom = this.zoomDefault;
		}
	},

	searchAction: function(adresse, auto) {
		var myApp = this;

		var myMask = new Ext.LoadMask(Ext.get('geolocContainer'), {
			msg: Ext.ts.Lang.geolocWaitMsg,
			removeMask: true
		});
		myMask.show();

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({address: adresse}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				myApp.gpsLat.setValue(results[0].geometry.location.lat());
				myApp.gpsLng.setValue(results[0].geometry.location.lng());
				myApp.zoom = myApp.zoomDefault;
				myApp.fireEvent('change');

				if (auto === true) {
					Ext.MessageBox.show({
						title: Ext.ts.Lang.confirmTitle,
						msg: Ext.ts.Lang.geolocAuto,
						icon: Ext.MessageBox.INFO,
						buttons: Ext.MessageBox.OK
					});
				}
			} else {
				var win = new Ext.ts.Notification({
					html: Ext.ts.Lang.geolocError+' : "'+adresse+'"',
					title: Ext.ts.Lang.failureTitle,
					iconCls: 'error'
				});
				win.show();
			}
			myApp.refreshMap();
			myMask.hide();
		});
	},

	formatNumber: function(number) {
		return Ext.util.Format.number(number, '0.00000000')
	}

});
Ext.reg('geolocpanel', Ext.ts.GeolocPanel);



/**
 * PERIODES OUVERTURES
 * Composant permettant de gérer les périodes et horaires d'ouverture
 */
Ext.ts.PeriodeOuverture = Ext.extend(Ext.Panel, {
	width: 1400,
	border: true,
	hideBorders: true,
	layout:'anchor',

	initComponent: function() {
		this.addEvents('edition', 'visualisation', 'beforesave');
		this.typeToutAnnee = 'Tout';
		this.typePeriodes = 'Periodes';
		this.typeUnJour = 'Journee';

		this.readyState=false;

		this.hiddenField = new Ext.form.Hidden({
			name: this.tsName
		});

		//Permet de différer le rendu
		this.hidden = false;
		this.storeToLoad = 0;

		this.firstRender = false;
		this.tableauEnregistrer = []; //array used to save the data in a know format.
		this.auxUnJour = []; //Auxiliar data to save in the memory the information insered in the period of one day
		this.auxPeriodeCollection = []; //Auxiliar data to save in the memory the information insered in the multiple periods
		this.auxPeriodeAnnuel = []; //Auxiliar data to save in the memory the information insered in the annual period

		this.days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

		//BUTTONS
		this.buttonAdd = new Ext.Button({
			text: 'Ajouter une période',
			iconCls: 'add',
			style: 'padding: 0px 20px 0px 20px;',
			handler: function() {
				this.ajouterPeriode();
			},
			scope: this
		});

		//FORMULAIRE
		this.buildFormulaire();

		//CONTAINER
		this.items = [
			this.form,
			this.panelDispos,
			this.hiddenField
		];

		Ext.ts.PeriodeOuverture.superclass.initComponent.call(this);
	},

	buildFormulaire: function() {

		//FIELDS
		this.fields = {};

		//RadioButton to annual period
		this.rbnAllYear = new Ext.form.Radio({
			boxLabel: 'Toute l\'année',
			name: 'rb-auto1',
			inputValue: 1,
			id:'rbnAllYear',
			width: 100,
			listeners:{
				check: function(cbx, checked){
					if(checked){
						this.clearFields();
						this.grid.setDisabled(true);
						//Compare schedule's type to save temporary data
						if(this.typePeriode == this.typeUnJour)
						{
							this.auxUnJour = [];
							this.auxUnJour = this.periodeCollection;
						}
						else
						{
							this.auxPeriodeCollection = [];
							this.auxPeriodeCollection = this.periodeCollection;
						}
						//Create a start record to annual information
						if(this.auxPeriodeAnnuel.length<1)
						{
							var periode={};
							var now = new Date();
							var dateDebut = Date.parseDate(now.getFullYear() + '-01-01','Y-m-d');
							var dateFin = Date.parseDate(now.getFullYear() + '-12-31','Y-m-d');
							var vtext =  'Du ' + dateDebut.format('l j F Y') + ' au '+ dateFin.format('l j F Y');
							periode.datedebut = dateDebut;
							periode.datefin = dateFin;
							periode.text = vtext;
							periode.jours=[
								{jour: 'Tous les jours', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''}
							];
							this.auxPeriodeAnnuel.push(periode);
						}
						this.grid.getStore().loadData(this.auxPeriodeAnnuel);
						this.periodeCollection = this.auxPeriodeAnnuel;
						this.currentSelectedIndex = 0;
						record = this.grid.getStore().getAt(0);
						this.chargerData(record);
						this.fsPeriodes.setVisible(false);

						this.fsHoraires.setVisible(true);

						this.panelTypeHoraire.setVisible(true);
						this.rbnAll.setDisabled(false);
						this.rbnDays.setDisabled(false);
						this.gridHoraires.setVisible(true);
						this.onBeforeSave();


						this.typePeriode = this.typeToutAnnee;
						this.paintBackground();
						if(this.readyState)
							this.afficherTableaux(1,2013,12,'',131359,'A', null);
					}
				},
				scope:this
			}
		});

		//RadioButton to periods
		this.rbnPeriodes = new Ext.form.Radio({
			boxLabel:'Périodes',
			name: 'rb-auto1',
			inputValue: 2,
			id:'rbnPeriodes',
			listeners:{
				check: function(cbx, checked){
					if(checked){
						this.clearFields();
						this.grid.setDisabled(false);
						if(this.typePeriode == this.typeToutAnnee)
						{
							this.auxPeriodeAnnuel = [];
							this.auxPeriodeAnnuel = this.periodeCollection;
						}
						else
						{
							this.auxUnJour = [];
							this.auxUnJour = this.periodeCollection;
						}
						this.grid.getStore().loadData(this.auxPeriodeCollection);
						this.periodeCollection = this.auxPeriodeCollection;
						this.fsPeriodes.setVisible(true);

						this.fsHoraires.setVisible(true);
						this.grid.setVisible(true);
						this.dfJour.setVisible(false);

						this.panelTypeHoraire.setVisible(true);
						this.rbnAll.setDisabled(true);
						this.rbnDays.setDisabled(true);

						this.gridHoraires.setVisible(false);
						this.onBeforeSave();
						this.paintBackground();
						this.typePeriode = this.typePeriodes;
						if(this.readyState)
							this.afficherTableaux(1,2013,12,'',131359,'A', null);
					}
				},
				scope:this
			}
		});

		//RadioButton to day program
		this.rbnUnJour = new Ext.form.Radio({
			boxLabel:'Une journée',
			name: 'rb-auto1',
			inputValue: 3,
			id:'rbnUnJour',
			listeners:{
				check: function(cbx, checked){
					if(checked){
						this.clearFields();
						this.grid.setDisabled(false);
						if(this.typePeriode == this.typeToutAnnee)
						{
							this.auxPeriodeAnnuel = [];
							this.auxPeriodeAnnuel = this.periodeCollection;
						}
						else{
							this.auxPeriodeCollection = [];
							this.auxPeriodeCollection = this.periodeCollection;
						}
						this.grid.getStore().loadData(this.auxUnJour);
						if(Ext.isDefined(this.auxUnJour[0]))
						{
							this.dfJour.setValue(this.auxUnJour[0]['datedebut']);
						}
						this.periodeCollection = this.auxUnJour;
						this.currentSelectedIndex = 0;

						record = this.grid.getStore().getAt(0);
						this.chargerData(record);

						this.fsPeriodes.setVisible(true);

						this.grid.setVisible(false);
						this.dfJour.setVisible(true);

						this.panelTypeHoraire.setVisible(false);
						this.rbnAll.setDisabled(true);
						this.rbnDays.setDisabled(true);

						if(this.periodeCollection.length > 0)
						{
							this.fsHoraires.setVisible(true);
							this.gridHoraires.setVisible(true);
						}
						else
						{
							this.fsHoraires.setVisible(false);
							this.gridHoraires.setVisible(false);
						}

						this.onBeforeSave();

						this.paintBackground();
						this.typePeriode = this.typeUnJour;
						if(this.readyState)
							this.afficherTableaux(1,2013,12,'',131359,'A', null);
					}
				},
				scope:this
			}
		});

		this.radioGroupPeriodes = new Ext.form.RadioGroup({
			width: 500,
			height: 20,
			fieldLabel: 'Dates',
			labelStyle: 'width: 130px;',
			hideLabel: false,
			style: 'margin : 5px 0px 5px 15px;',
			items:[
				this.rbnUnJour,
				this.rbnPeriodes,
				this.rbnAllYear
			]
		});

		this.panelTypePeriode = new Ext.Panel({
			layout: 'form',
			border: false,
			bodyStyle: 'padding : 0px 0px 0px 10px;',
			items: [
				this.radioGroupPeriodes
			]
		});


		this.fields.datedebut = new Ext.form.DateField({
			xtype: 'datefield',
			width: 100,
			format: 'd/m/Y',
			required: true,
			listeners:{
				change:function(field,newValue,oldValue){
					if((this.fields.datefin.getValue() == ''|| field.getValue().format('U') > this.fields.datefin.getValue().format('U')))
					{
						this.fields.datefin.setValue(field.getValue());
					}
					this.fields.datefin.setMinValue(field.getValue());
				},
				scope:this
			}
		});
		this.fields.datefin = new Ext.form.DateField({
			xtype: 'datefield',
			width: 100,
			format: 'd/m/Y',
			required: true,
		});

		//FORMULAIRE
		this.rbnAll = new Ext.form.Radio({
			checked: true,
			boxLabel:'Tous les jours',
			name: 'rb-auto',
			id:'rbnAll',
			inputValue: 1,
			listeners: {
				check: {
					fn: function(checkbox, checked) {
						if(checkbox.checked){
							record = this.grid.getStore().getAt(this.currentSelectedIndex);
							idx=this.findPeriode(record);
							var aux = [];
							if(!Ext.isDefined(this.vToujours)  && this.periodeCollection[idx].jours.length == 0)
							{
								this.vToujours= this.initializeToujours();
							}
							else
							{
								if(this.periodeCollection[idx].jours[0].jour!='Tous les jours')
								{
									this.vJours= this.periodeCollection[idx].jours;
								}
								else
								{
									if(this.periodeCollection[idx].jours[0].jour=='Tous les jours')
									{
										this.vToujours= this.periodeCollection[idx].jours;
									}
								}
							}
							this.periodeCollection[idx].jours = this.vToujours;

							this.gridHoraires.getColumnModel().getColumnById('ccFerme').hidden = true;
							this.gridHoraires.getStore().loadData(this.periodeCollection[idx].jours);

							this.onBeforeSave();
						}
					},
					scope: this
				}
			}
		});

		this.rbnDays = new Ext.form.Radio({
			checked: false,
			boxLabel:'Certains jours',
			name: 'rb-auto',
			inputValue: 2,
			id:'rbnDays',
			style: 'margin : 0px 0px 0px 0px;',
			listeners: {
				check: {
					fn: function(checkbox, checked) {
						if(checkbox.checked){
							if (Ext.isDefined(this.currentSelectedIndex))
							{
								this.copySchedule();
								record = this.grid.getStore().getAt(this.currentSelectedIndex);
								idx=this.findPeriode(record);
								var aux = [];
								if(!Ext.isDefined(this.vJours) && this.periodeCollection[idx].jours.length == 0)
								{
									this.vJours= this.initializeVJours();
								}
								else
								{
									if(this.periodeCollection[idx].jours.length ==1 && this.periodeCollection[idx].jours[0].jour=='Tous les jours')
									{
										this.vToujours= this.periodeCollection[idx].jours;
									}
									else
									{
										if(this.periodeCollection[idx].jours[0].jour!='Tous les jours')
										{
											this.vJours= this.periodeCollection[idx].jours;
										}
									}
								}
								this.periodeCollection[idx].jours = this.vJours;
								this.gridHoraires.getColumnModel().getColumnById('ccFerme').hidden = false;
								this.gridHoraires.getStore().loadData(this.periodeCollection[idx].jours);
								this.onBeforeSave();
								this.paintBackground();
							}
						}
					},
					scope: this
				}
			}
		});

		this.radioGroup = new Ext.form.RadioGroup({
			height: 20,
			labelStyle: 'width: 100px;',
			style: 'margin : 5px 0px 15px 15px;',
			id:'radioGroupTypeHoraire',
			items:[
				this.rbnAll,
				this.rbnDays
			]
		});

		this.panelTypeHoraire = new Ext.Panel({
			layout: 'form',
			border: false,
			bodyStyle: 'padding : 0px 0px 0px 10px;',
			hidden: true,
			id:'panelTypeHoraire',
			items: [
				this.radioGroup
			],
			listeners: {
				afterlayout: {
					fn: function() {
						//On force le bon positionement des radio buttons CODE SALE!!!! car quand on a
						//une fiche sans horaires saisis les radiobuton ont une mauvaise positionement
						if(document.getElementById('radioGroupTypeHoraire').style.width == '')
						{
							var divbase=document.getElementById('rbnDays');
							divbase.style.marginLeft = '172px';
						}
					},
					scope: this
				}
			}
		});

		//GRILLE
		this.buildGrid();
		this.buildGridHoraires();
		
		var dfJourEvent = function(field) {
			this.fsHoraires.setVisible(true);
			this.gridHoraires.setVisible(true);
			this.gridHoraires.focus();

			var periode={};
			var dateDebut = field.getValue();
			var dateFin = field.getValue();
			var vtext =  'Du ' + dateDebut.format('l j F Y') + ' au '+ dateFin.format('l j F Y');
			periode.datedebut = dateDebut;
			periode.datefin = dateFin;
			periode.text = vtext;
			periode.jours=[
				{jour: 'Tous les jours', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''}
			];
			if(this.auxUnJour.length<1)
			{
				this.auxUnJour.push(periode);
			}else
			{
				this.auxUnJour[0] = periode;
			}
			this.grid.getStore().loadData(this.auxUnJour);
			this.periodeCollection = this.auxUnJour;

			if(!Ext.isDefined(this.vToujours)  && this.periodeCollection[0].jours.length == 0)
				{
					this.vToujours= this.initializeToujours();
				}
				else
				{
					if(this.periodeCollection[0].jours[0].jour!='Tous les jours')
					{
						this.vJours= this.periodeCollection[0].jours;
					}
					else
					{
						if(this.periodeCollection[0].jours[0].jour=='Tous les jours')
						{
							this.vToujours= this.periodeCollection[0].jours;
						}
					}
				}
				this.periodeCollection[0].jours = this.vToujours;

				this.gridHoraires.getStore().loadData(this.periodeCollection[0].jours);

				this.onBeforeSave();
		};
		
		this.dfJour = new Ext.form.DateField({
			fieldLabel: 'Jour ',
			id:'dfJour',
			format: 'd/m/Y',
			listeners: {
				select: {
					fn: dfJourEvent,
					scope: this
				},
				change:{
					fn: dfJourEvent,
					scope: this
				}
			}
		});

		this.fsPeriodes = new Ext.form.FieldSet ({
			autoHeight:true,
			autoWidth: true,
			hideBorders: true,
			id: 'fsPeriodes',
			bodyStyle: 'padding:0px; border:1px; border-color:#cccccc;',
			autoHeight:true,
			hidden: true,
			items: [
				this.dfJour,
				this.grid
			]
		});

		this.panelDispos = new Ext.Panel({
			border: true,
			activeItem: 0,
			anchor: '50%',
			style: 'float: right; margin: 10px 0px; border:1px; border-color:#cccccc;',
			id:'dispos',
			defaults: {
				style: 'padding:0px 0px 0px;',
				border: true
			}
		});

		this.fsHoraires = new Ext.form.FieldSet ({
			autoHeight:true,
			width: 680,
			hideBorders: true,
			id: 'fsHoraires',
			bodyStyle: 'padding:0px; border:1px; border-color:#cccccc;',
			title: 'Horaires',
			autoHeight:true,
			items :[
				this.panelTypeHoraire,
				this.gridHoraires
			],
			hidden: true
		});

		this.form = new Ext.Panel({
			anchor: '50%',
			style: 'float: left;',
			border: false,
			defaults: {
				xtype: 'fieldset',
				labelWidth: 50,
				style: 'margin: 10px; border-color:#cccccc;'
			},
			items: [this.panelTypePeriode,
				this.fsPeriodes,
				this.fsHoraires
			],
			listeners: {
				afterlayout: {
					fn: function() {
						if(!this.firstRender && (this.rbnAllYear.checked||this.rbnUnJour.checked)){
							this.currentSelectedIndex = 0;
							record = this.grid.getStore().getAt(0);
							this.chargerData(record);
							this.panelTypeHoraire.doLayout();
						}
						var dispos=''

						this.afficherTableaux(1,2013,12,dispos,131359,'A', null);
					},
					scope: this
				}
			}
		});
	},

	buildGrid: function() {
		var readerFields = [];
		var columns = [];

		this.periodeCollection = [];
		this.auxPeriodeCollection = [];
		this.auxPeriodeAnnuel = [];
		this.auxUnJour = [];
		var readerFields = [];

		this.storePeriode = new Ext.data.JsonStore({
			fields: [
				{name: 'datedebut',      	type: 'string' },
				{name: 'datefin',		type: 'string'},
			],
			listeners: {
				remove: {
					fn: this.setHiddenValue,
					scope: this
				}
			}
		});

		this.storeperiodeAnnuel = new Ext.data.JsonStore({
			fields: readerFields,
		});

		var smPeriode = new Ext.grid.RowSelectionModel({
			singleSelect: true,
			listeners: {
				rowselect: {
					fn: this.onGridPeriodeRowSelect,
					scope: this
				}
			}
		});

		// GET THE TOP BORDER OF THE FIRST ROW
		var view = new Ext.grid.GridView({
			getRowClass: function(record, index) {
				if (index == 0) {
					return 'border-top-first-row';
				}
			}
		});

		var fm = Ext.form;

		this.grid = new Ext.grid.EditorGridPanel({
			cls: 'extra-alt',
			border: false,
			store: this.storePeriode,
			columnLines: true,
			stripeRows: true,
			height:120,
			clicksToEdit: 2,
			columnDebut: 0,
			columnFin: 1,
			selModel: smPeriode,
			colModel: new Ext.grid.ColumnModel({
				defaults: {
					sortable: false,
					width: 120
				},
				columns: [{
					xtype: 'datecolumn',
					id: 'gcDateDebut',
					header: 'Du',
					dataIndex: 'datedebut',
					format: 'l j F Y',
					editor: new fm.DateField({
						xtype: 'datefield',
						format: 'd/m/Y'
					}),
					width: 300
				},{
					xtype: 'datecolumn',
					id: 'gcDateFin',
					header: 'Au',
					dataIndex: 'datefin',
					format: 'l j F Y',
					editor: new fm.DateField({
						xtype: 'datefield',
						format: 'd/m/Y'
					}),
					width: 300
				},{
					xtype: 'gridcolumn',
					id: 'gcTextPeriode',
					header: 'Periode',
					dataIndex: 'text',
					hidden: true,
					width: 300
				},{
					xtype: 'actioncolumn',
					header: 'Outils',
					width: 55,
					items: [{
						iconCls: 'delete',
						tooltip: Ext.ts.Lang.supprimer,
						handler: function(grid, rowIndex, colIndex) {
							var record = grid.getStore().getAt(rowIndex);
							this.deletePeriode(record);
						},
						scope: this
					}]
				}]
			}),
			tbar: new Ext.Toolbar({
				defaults: {
					enableToggle: false,
					groupId: 'typeButtons',
					allowDepress: true,
					scope: this
				},
				items: [
					'->',
					this.buttonAdd
				]
			}),
			viewConfig: {
				forceFit: true
			},
			listeners: {
				afteredit: {
					fn: function(e) {
						if(e.column == 0)
						{
							e.grid.getColumnModel().getCellEditor(e.column + 1,e.row).field.setMinValue(e.value);
						}
						this.validerPeriode(e);
					},
					scope: this
				}
			}
		});
	},

	// Validation method, valide the new period to make sure no date's superposition
	validerPeriode: function(periode){
		if(this.grid.getStore().getAt(periode.row).data.datedebut != '')
		{
			var debutToSave =  periode.grid.getColumnModel().getCellEditor(periode.grid.columnDebut,periode.row).field.getValue().format('U');
		}
		else
		{
			periode.grid.startEditing(periode.row,periode.grid.columnDebut);
			return false;
		}
		if(this.grid.getStore().getAt(periode.row).data.datefin != '')
		{
			var finToSave =  periode.grid.getColumnModel().getCellEditor(periode.grid.columnFin,periode.row).field.getValue().format('U');
		}
		else
		{
			periode.grid.startEditing(periode.row,periode.grid.columnFin);
			return false;
		}


		var skip = false;
		var contRecord = -1;
		var idxRecord = 0;

		this.grid.getStore().each(function(record) {
			contRecord++;
			if (periode.record.id == record.id) {
				idxRecord = contRecord;
				return true;
			}
			var recordDateDebut = new Date(record.data.datedebut);
			var recordDateFin = new Date(record.data.datefin);
			if (!Ext.isDate(recordDateDebut) || !Ext.isDate(recordDateFin)) {
				return true;
			}
			var debut = recordDateDebut.format('U');
			var fin = recordDateFin.format('U');

			if (!(finToSave < debut) && !(debutToSave > fin)) {
				skip = true;
				return false;
			}
		}, this);

		if (skip) {
			Ext.MessageBox.alert(Ext.ts.Lang.failureTitle, Ext.ts.Lang.periodeouvertureError);
			if(periode.column == 0)
			{
				periode.grid.startEditing(periode.row,periode.grid.columnFin);
			}
			else
			{
				periode.grid.startEditing(periode.row,periode.grid.columnDebut);
			}
			this.cacherHoraires();
			return false;
		}
		else
		{
			this.periodeCollection[idxRecord].datedebut = periode.grid.getColumnModel().getCellEditor(periode.grid.columnDebut,periode.row).field.getValue();
			this.periodeCollection[idxRecord].datefin = periode.grid.getColumnModel().getCellEditor(periode.grid.columnFin,periode.row).field.getValue();
			this.montrerHoraires();
			if(this.periodeCollection[idxRecord].datedebut.format('U')==this.periodeCollection[idxRecord].datefin.format('U'))
			{
				this.rbnAll.setDisabled(true);
				this.rbnDays.setDisabled(true);
			}
			periode.record.commit();
			this.chargerData(periode.record);
			this.onBeforeSave();
			this.afficherTableaux(1,2013,12,'',131359,'A', null);
		}
		return true;
	},

	//Schedules grid's creation method
	buildGridHoraires: function() {
		var readerFields = [];
		var columns = [];
		var readerFields = [];

		this.storeHoraires = new Ext.data.JsonStore({
			fields: [
			   {name: 'jour',      	type: 'string' },
			   {name: 'ferme',		type: 'boolean'},
			   {name: 'debut1',	type: 'string'},
			   {name: 'fin1',	type: 'string'},
			   {name: 'debut2',	type: 'string'},
			   {name: 'fin2',		type: 'string'}
			]
		});

		this.clearFields();

		var smHoraires = new Ext.grid.RowSelectionModel({
			singleSelect: true,
		});

		var fm = Ext.form;

		this.gridHoraires = new Ext.grid.EditorGridPanel({
			border:true,
			store: this.storeHoraires,
			style:'margin : 0px 0px 0px 00px;',
			columnLines: true,
			clicksToEdit: 1,
			height: 200,
			columnFerme: 5,
			colModel: new Ext.grid.ColumnModel({
				columns: [{
					xtype: 'gridcolumn',
					id: 'jour',
					header: 'Jours',
					dataIndex: 'jour'
				},{
					id: 'gcdebut1',
					header: 'Début matin',
					dataIndex: 'debut1',
					editor: new fm.TimeField({
						format: 'H:i:s',
						increment: 5,
						hideTrigger: true,
						editable: true,
						forceSelection: true,
						emptyText: '00:00:00'
					})
				},{
					id: 'gcfin1',
					header: 'Fin matin',
					dataIndex: 'fin1',
					editor: new fm.TimeField({
						format: 'H:i:s',
						increment: 5,
						hideTrigger: true,
						editable: true,
						forceSelection: true,
						emptyText: '00:00:00'
					})
				},{
					xtype: 'gridcolumn',
					id: 'gcdebut2',
					header: 'Début après midi',
					dataIndex: 'debut2',
					editor: new fm.TimeField({
						format: 'H:i:s',
						increment: 5,
						hideTrigger: true,
						editable: true,
						forceSelection: true,
						emptyText: '00:00:00'
					})
				},{
					xtype: 'gridcolumn',
					id: 'gcfin2',
					header: 'Fin après midi',
					dataIndex: 'fin2',
					editor: new fm.TimeField({
						format: 'H:i:s',
						increment: 5,
						hideTrigger: true,
						editable: true,
						forceSelection: true,
						emptyText: '00:00:00'
					})
				},{
					xtype: 'actioncolumn',
					id: 'ccFerme',
					header: Ext.ts.Lang.outils,
					dataIndex: 'type',
					width: 70,
					items: [{
						iconCls: 'page_white_copy',
						tooltip: Ext.ts.Lang.copieraudessous,
						//Method to copy data to row below
						handler: function(grid, rowIndex, colIndex) {
							if(rowIndex<6)
							{
								var idx = this.findPeriode(this.grid.getStore().getAt(this.currentSelectedIndex));
								if(this.periodeCollection[idx].jours[rowIndex+1]['debut1']=='')
									this.periodeCollection[idx].jours[rowIndex+1]['debut1']=this.periodeCollection[idx].jours[rowIndex]['debut1'];
								if(this.periodeCollection[idx].jours[rowIndex+1]['fin1']=='')
									this.periodeCollection[idx].jours[rowIndex+1]['fin1']=this.periodeCollection[idx].jours[rowIndex]['fin1'];
								if(this.periodeCollection[idx].jours[rowIndex+1]['debut2']=='')
									this.periodeCollection[idx].jours[rowIndex+1]['debut2']=this.periodeCollection[idx].jours[rowIndex]['debut2'];
								if(this.periodeCollection[idx].jours[rowIndex+1]['fin2']=='')
									this.periodeCollection[idx].jours[rowIndex+1]['fin2']=this.periodeCollection[idx].jours[rowIndex]['fin2'];
								this.verifierFermes(idx);
								this.gridHoraires.getStore().loadData(this.periodeCollection[idx].jours);
								this.onBeforeSave();
								this.paintBackground();
							}
						},
						scope: this
					},{
						iconCls: 'cross',
						tooltip: Ext.ts.Lang.fermer,
						//Method to clear row's information
						handler: function(grid, rowIndex, colIndex) {
							var idx = this.findPeriode(this.grid.getStore().getAt(this.currentSelectedIndex));
							this.periodeCollection[idx].jours[rowIndex]['debut1']='';
							this.periodeCollection[idx].jours[rowIndex]['fin1']='';
							this.periodeCollection[idx].jours[rowIndex]['debut2']='';
							this.periodeCollection[idx].jours[rowIndex]['fin2']='';
							this.verifierFermes(idx);
							this.gridHoraires.getStore().loadData(this.periodeCollection[idx].jours);
							this.onBeforeSave();
							this.paintBackground();
						},
						scope: this
					}]
				}]
			}),
			viewConfig: {
				forceFit: true
			},
			listeners: {
				afteredit: {
					fn: function(e) {
						if(e.column < this.gridHoraires.columnFerme)
						{
							this.validerValeur(e);
						}
					},
					scope: this
				},
				beforeedit: {
					fn: function(e) {
						var idx= this.findPeriode(this.grid.getStore().getAt(this.currentSelectedIndex));
						this.verifierFermes(idx);
					},
					scope: this
				},
				viewready: {
					fn: function(e) {
						if (Ext.isDefined(this.currentSelectedIndex))
						{
							var idx= this.findPeriode(this.grid.getStore().getAt(this.currentSelectedIndex));
							this.verifierFermes(idx);
							this.gridHoraires.getStore().loadData(this.periodeCollection[idx].jours);
						}
						else
						{
							for(var idx=0;idx<this.periodeCollection.length;idx++)
							{
								this.verifierFermes(idx);
							}
						}
						this.paintBackground();
						this.afficherTableaux(1,2013,12,'',131359,'A', null);
						this.readyState = true;
					},
					scope: this
				}
			}
		});
	},

	//get inserted value of the schedule grid to insert them in the temporary array of periods "periodCollection"
	validerValeur: function(objEdition) {
		if(objEdition.column <= objEdition.grid.columnFerme)
		{
			var action='';
			var record = this.grid.getStore().getAt(this.currentSelectedIndex);
			var idx=this.findPeriode(record);
			if(objEdition.column%2!=0)
			{
				action='debut'+(parseInt((objEdition.column/2))+1);
			}
			else
			{
				action='fin'+parseInt((objEdition.column/2));
			}
			this.periodeCollection[idx].jours[objEdition.row][action] = objEdition.value;
			if(objEdition.column + 1 < 5)
			{
				objEdition.grid.getColumnModel().getCellEditor(objEdition.column + 1,objEdition.row).field.setMinValue(objEdition.value);
			}
			this.onBeforeSave();
			this.verifierFermes(idx);
			this.gridHoraires.getStore().loadData(this.periodeCollection[idx].jours);
			this.paintBackground();
			this.afficherTableaux(1,2013,12,'',131359,'A', null);
			objEdition.grid.startEditing(objEdition.row,objEdition.column + 1);
		}
	},

	//get information of closed days of the periodCollection to paint the background of the grid.
	paintBackground: function() {
		if(this.gridHoraires.getStore().data.items.length>1)
		{
			for(var j=0; j<7; j++)
			{
				if(this.gridHoraires.getStore().data.items[j].data.ferme)
				{
					for( var i=0; i<this.gridHoraires.getColumnModel().getColumnCount(false);i++)
					{
						this.gridHoraires.getView().getCell(j,i).style.backgroundColor = '#DDD';
					}
				}
			}
		}
	},

	//Change the CurrentRowSelect to obtains the selected period's schedule.
	onGridPeriodeRowSelect: function(sm, rowIndex, record) {
		this.currentSelectedIndex = rowIndex;
		if(record.data.datedebut == '' || record.data.datedefin == '')
		{
			return false;
		}
		this.montrerHoraires();

		if(record.data.datedebut==record.data.datefin)
		{
			this.rbnAll.setDisabled(true);
			this.rbnDays.setDisabled(true);
		}

		this.chargerData(record);
	},

	//Show the fieldset of schedules "fsHoraires"
	montrerHoraires: function(){
		this.clearFields();

		this.fsHoraires.setVisible(true);
		this.panelTypeHoraire.setVisible(true);
		this.rbnAll.setDisabled(false);
		this.rbnDays.setDisabled(false);

		//EN TRAVAILLANT AVEC LA GRILLE
		this.gridHoraires.setVisible(true);
	},

	//Hide the fieldset of schedules "fsHoraires"
	cacherHoraires: function(){
		this.fsHoraires.setVisible(false);
		this.panelTypeHoraire.setVisible(false);
		this.rbnAll.setDisabled(true);
		this.rbnDays.setDisabled(true);

		//EN TRAVAILLANT AVEC LA GRILLE
		this.gridHoraires.setVisible(false);
	},

	//Load the selected period data, use the "record" found in the temporary array of periods 'periodCollection' by the method 'findPeriode'
	chargerData: function(record) {
		if (Ext.isDefined(record)) {
			var cont = 0;
			this.recordEdited = record;
			var idx=this.findPeriode(record);
			this.clearFields();
			var flag = false;
			if(idx!=-1)
			{
				if(this.periodeCollection[idx].jours.length ==1 && this.periodeCollection[idx].jours[0].jour=='Tous les jours')
				{
					this.rbnAll.setValue(true);
				}
				else{
					this.rbnDays.setValue(true);
					this.verifierFermes(idx);
				}
				this.gridHoraires.getStore().loadData(this.periodeCollection[idx].jours);
				this.paintBackground();
			}
		}

		this.fireEvent('edition', this.recordEdited);
	},

	//Verification of closure information, if in a day we have not a opening hour we mark the row as closed "fermé=true"
	//if all week is closed so it's open allways
	verifierFermes:function(idx){
		var actions = ['debut', 'fin'];
		var contjours = 0;
		var contjoursFermes = 0;
		if(this.periodeCollection[idx].jours[0].jour != 'Tous les jours' )
		{
			Ext.each(this.days, function(day) {
				if(Ext.isEmpty(this.periodeCollection[idx].jours[contjours]['debut1']) && Ext.isEmpty(this.periodeCollection[idx].jours[contjours]['fin1']) &&
				Ext.isEmpty(this.periodeCollection[idx].jours[contjours]['debut2']) && Ext.isEmpty(this.periodeCollection[idx].jours[contjours]['fin2']))
				{
					this.periodeCollection[idx].jours[contjours]['ferme'] = true;
					contjoursFermes++;
				}
				else
				{
					this.periodeCollection[idx].jours[contjours]['ferme'] = false;
				}
				contjours++;
			}, this);
			if(contjoursFermes==7)
			{
				contjours = 0;
				Ext.each(this.days, function(day) {
					this.periodeCollection[idx].jours[contjours]['ferme'] = false;
					contjours++;
				}, this);
			}
		}
	},

	//Copy the opening and closing hours of allways (tous les jours) to the weekly schedule in the empty cells
	copySchedule: function(){
		var actions = ['debut', 'fin'];
		var contjours;
		if (Ext.isDefined(this.currentSelectedIndex))
		{
			var idx = this.findPeriode(this.grid.getStore().getAt(this.currentSelectedIndex));
			Ext.each(actions, function(action) {
				contjours = 0;
				Ext.each(this.days, function(day) {
					for(var i=1; i<=2; i++)
					{
						if(this.vJours[contjours][action+i] == '' && this.periodeCollection[idx].jours[0][action+i] !='')
						{
							this.vJours[contjours][action+i] = this.periodeCollection[idx].jours[0][action+i];
						}
					}
					contjours++;
				}, this);
			}, this);
			this.onBeforeSave();
		}
	},

	clearFields:function(){
		this.vJours = this.initializeVJours();
		this.vToujours= this.initializeToujours();
	},

	//Initialize the week days
	initializeVJours: function(){
		var vJours = [
			{jour: 'Lundi', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''},
			{jour: 'Mardi', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''},
			{jour: 'Mercredi', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''},
			{jour: 'Jeudi', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''},
			{jour: 'Vendredi', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''},
			{jour: 'Samedi', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''},
			{jour: 'Dimanche', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''}
		];
		return vJours;
	},

	//Initialize the empty record of "tous les jours"
	initializeToujours: function(){
		var vToujours= [
			{jour: 'Tous les jours', ferme: false, debut1:'', fin1:'', debut2:'', fin2:''}
		];
		return vToujours;
	},

	//Add a new record in the periods grid
	ajouterPeriode: function() {
		this.clearFields();
		this.cacherHoraires();
		var periode = {
			datedebut: '',
			datefin: '',
			text: '',
			jours: this.vToujours
		};
		this.grid.stopEditing();
		this.periodeCollection.push(periode);
		this.grid.getStore().loadData(this.periodeCollection);
		this.grid.startEditing(this.grid.getStore().data.items.length-1, 0);
		return true;
	},

	//Delete the selected record in the periods grid and deleted that in the periodeCollection
	deletePeriode: function(rec) {
		Ext.Msg.show({
			title: Ext.ts.Lang.confirmTitle,
			msg: Ext.ts.Lang.objectgridDelete,
			buttons: Ext.Msg.YESNO,
			icon: Ext.MessageBox.QUESTION,
			fn: function(btn) {
				if (btn == 'yes') {
					if(typeof rec == 'object' && Ext.isDefined(rec.data))
					{
						idx = this.grid.store.indexOf(rec);
						this.currentSelectedIndex=idx;
						var idx2= this.findPeriode(rec);
						this.periodeCollection.splice(idx2,1);
						this.tableauEnregistrer.splice(idx2,1)
						this.storePeriode.removeAt(idx);
						this.clearFields();
						this.fsPeriodes.setVisible(true);

						this.fsHoraires.setVisible(true);

						this.panelTypeHoraire.setVisible(true);
						this.rbnAll.setDisabled(true);
						this.rbnDays.setDisabled(true);
						//this.afficherTableaux(1,2013,12,'',131359,'A', null);
						this.onBeforeSave();
					}
				}
			},
			scope: this
		});
	},

	// Find a record in periodCollection with the currentSelectedindex as parameter.
	findPeriode: function(rec){
		var idx=-1;
		for(var i in this.periodeCollection)
		{
			if((new Date(this.periodeCollection[i].datedebut)).format('U')==(new Date(rec.data.datedebut)).format('U') && (new Date(this.periodeCollection[i].datefin)).format('U')==(new Date(rec.data.datefin)).format('U'))
			{
				idx=i;
			}
		}
		return idx;
	},

	onModeEdition: function(record) {
		if (!this.firstRender) {
			this.firstRender = true;
		}

		if (Ext.isDefined(record)) {
			var allDay = false;
			var byDay = false;

			Ext.iterate(record.data, function(k, v) {
				allDay = (k.indexOf('heure', 0) != -1 && k.indexOf('all', 0) != -1 && v != '') || allDay;
				byDay = (k.indexOf('heure', 0) != -1 && k.indexOf('all', 0) == -1 && v != '') || byDay;
			});

			if (allDay || !byDay) {
				this.radioGroup.onSetValue('rbnAll', true);
			}
			else {
				this.radioGroup.onSetValue('rbnDays', true);
			}
		}
		else {
			this.fieldsetAll.expand();
		}
	},

	switchHoraires: function(c) {
		if (c.itemId == 'fieldsetAll') {
			this.radioGroup.onSetValue('rbnAll', true);
		}
		if (c.itemId == 'fieldsetDays') {
			this.radioGroup.onSetValue('rbnDays', true);
		}
	},

	//Method to save temporally the information, we save in the variable "tableauEnregistrer" having the standar to save.
	//We transfor the periodCollection data in a know format to the tourismSystem standar.
	onBeforeSave: function() {
		record = this.grid.getStore().getAt(this.currentSelectedIndex);
		//on s'assure de enregistrer le dernier donné existant dans le tableau
		if(Ext.isDefined(this.periodeCollection[0]) && (this.periodeCollection[0]['datedebut'].format('dm') != '0101' && this.periodeCollection[0]['datefin'].format('dm') != '3112'))
		{
			for(var i=0;i<this.periodeCollection.length;i++)
			{
				if(!Ext.isDefined(this.tableauEnregistrer[i]))
				{
					this.tableauEnregistrer.push({});
				}
				//this.tableauEnregistrer[i] = {};
				this.tableauEnregistrer[i]['datedebut'] = this.periodeCollection[i]['datedebut'].format('Y-m-d')
				this.tableauEnregistrer[i]['datefin'] = this.periodeCollection[i]['datefin'].format('Y-m-d')
				this.tableauEnregistrer[i]['type'] = this.type;
			}
		}
		else
		{
			var lengthTabEn=this.tableauEnregistrer.length;
			for(var i=1;i<lengthTabEn;i++)
			{
				this.tableauEnregistrer.splice(1,1)
			}
		}
		//We save all the information in the grid to save all changes
		for(var k=0;k<this.grid.getStore().data.items.length;k++)
		{
			record = this.grid.getStore().getAt(k);
			var idx = this.findPeriode(record)
			var actions = ['debut', 'fin'];
			var contjours;
			var dayLower = '';
			if(!Ext.isDefined(this.tableauEnregistrer[idx]))
			{
				this.tableauEnregistrer.push({});
			}
			this.tableauEnregistrer[idx] = {};
			this.tableauEnregistrer[idx]['datedebut'] = this.periodeCollection[idx]['datedebut'].format('Y-m-d')
			this.tableauEnregistrer[idx]['datefin'] = this.periodeCollection[idx]['datefin'].format('Y-m-d')
			this.tableauEnregistrer[idx]['type'] = this.type;

			if(/*Ext.isDefined(this.periodeCollection[idx].jours[0]) && */this.periodeCollection[idx].jours[0].jour=='Tous les jours')
			{
				this.tableauEnregistrer[idx]['heuredebut_all1'] = this.periodeCollection[idx].jours[0]['debut1'];
				this.tableauEnregistrer[idx]['heuredebut_all2'] = this.periodeCollection[idx].jours[0]['debut2'];
				this.tableauEnregistrer[idx]['heurefin_all1'] = this.periodeCollection[idx].jours[0]['fin1'];
				this.tableauEnregistrer[idx]['heurefin_all2'] = this.periodeCollection[idx].jours[0]['fin2'];
			}
			else
			{
				this.tableauEnregistrer[idx]['heuredebut_all1'] = '';
				this.tableauEnregistrer[idx]['heuredebut_all2'] = '';
				this.tableauEnregistrer[idx]['heurefin_all1'] = '';
				this.tableauEnregistrer[idx]['heurefin_all2'] = '';
			}

			Ext.each(actions, function(action) {
				contjours = 0;
				Ext.each(this.days, function(day) {
					dayLower = day.toLowerCase();
					for(var i=1; i<=2; i++)
					{
						this.tableauEnregistrer[idx]['heure'+action+'_'+ dayLower+i]=  (Ext.isDefined(this.periodeCollection[idx].jours[contjours]) && this.periodeCollection[idx].jours[contjours].jour!='Tous les jours' && !this.periodeCollection[idx].jours[contjours].ferme) ?  (Ext.isDefined(this.periodeCollection[idx].jours[contjours][action+i]) ? this.periodeCollection[idx].jours[contjours][action+i]:''):'';
					}
					contjours++;
				}, this);
			}, this);

			this.setHiddenValue();
			if(this.readyState)
				this.afficherTableaux(1,2013,12,'',131359,'A', null);
		}
		return true;
	},

	//Method to load the information of the "fiche", load the schedule by jour.
	remplirJours:function(data){
		var vJours = [];
		if(!Ext.isEmpty(data['heuredebut_all1']) || !Ext.isEmpty(data['heuredebut_all2']))
		{
			var vJours= this.initializeToujours();
			this.rbnAll.setValue(true);
			vJours[0]['debut1'] = data['heuredebut_all1'];
			vJours[0]['debut2'] = data['heuredebut_all2'];
			vJours[0]['fin1'] = data['heurefin_all1'];
			vJours[0]['fin2'] = data['heurefin_all2'];
		}
		else
		{
			var vJours= this.initializeVJours();
			this.rbnDays.setValue(true);
			var actions = ['debut', 'fin'];
			var contjours;
			Ext.each(actions, function(action) {
				contjours = 0;
				Ext.each(this.days, function(day) {
					for(var i=1; i<=2; i++)
					{
						vJours[contjours][action+i] = data['heure'+action+'_' + day.toLowerCase() +i];
					}
					contjours++;
				}, this);
			}, this);
		}
		return vJours;
	},

	//Load the information from the server.
	setValue: function(data) {
		if (Ext.isDefined(data) && Ext.isArray(data)) {
			var periodes = [];
			Ext.iterate(data, function(v) {
				var debut = Date.parseDate(v['datedebut'],'Y-m-d');
				var fin = Date.parseDate(v['datefin'],'Y-m-d');
				var vtext =  'Du ' + debut.format('l j F Y') + ' au '+ fin.format('l j F Y');
				v['text'] = vtext;
			});
			if(Ext.isArray(data) && data.length ==1)
			{
				var periode = {
					datedebut: '',
					datefin: '',
					text: '',
					jours: []
				};
				var cont=0;
				var debut = Date.parseDate(data[0]['datedebut'],'Y-m-d');
				var fin = Date.parseDate(data[0]['datefin'],'Y-m-d');
				periode.datedebut = debut;
				periode.datefin = fin;
				if(debut.format('dm') == '0101' && fin.format('dm') == '3112')
				{
					this.rbnAllYear.setValue(true);
					periode.jours = this.remplirJours(data[0]);
				}
				else
				{
					if(debut.format('U') == fin.format('U') )
					{
						this.rbnUnJour.setValue(true);
						//periode.jours = this.remplirJours(data[0]);
						var vJours= this.initializeToujours();
						this.rbnAll.setValue(true);
						if(!Ext.isEmpty(data[0]['heuredebut_all1']) || !Ext.isEmpty(data[0]['heuredebut_all2']))
						{
							vJours[0]['debut1'] = data[0]['heuredebut_all1'];
							vJours[0]['debut2'] = data[0]['heuredebut_all2'];
							vJours[0]['fin1'] = data[0]['heurefin_all1'];
							vJours[0]['fin2'] = data[0]['heurefin_all2'];
						}
						periode.jours = vJours;
					}
					else
					{
						this.rbnPeriodes.setValue(true);
						periode.jours = this.remplirJours(data[0]);
					}
				}

				periodes.push(periode);
			}
			else
			{
				if(Ext.isArray(data) && data.length >1)
				{
					var cont=0;
					//Ext.iterate(data[cont], function(k, v) { // k=field ; v=valeur
					Ext.iterate(data, function(k, v) { // k=field ; v=valeur
						var periode = {
							datedebut: '',
							datefin: '',
							text: '',
							jours: []
						};
						var debut = Date.parseDate(data[cont]['datedebut'],'Y-m-d');
						var fin = Date.parseDate(data[cont]['datefin'],'Y-m-d');
						periode.datedebut = debut;
						periode.datefin = fin;
						periode.jours = this.remplirJours(data[cont]);
						cont++;

						periodes.push(periode);
					}, this);
					this.rbnPeriodes.setValue(true);
				}
			}
			this.periodeCollection = periodes;
			this.grid.getStore().loadData(this.periodeCollection);

			if(this.periodeCollection.length >= 1)
			{
				//if(this.periodeCollection[0].jours.length ==1 && this.periodeCollection[0].jours[0].jour=='Tous les jours')
				if(this.rbnAllYear.checked)
				{
					//this.rbnAll.setValue(true);
					this.gridHoraires.getStore().loadData(this.periodeCollection[this.currentSelectedIndex].jours);
				}
				if(this.rbnUnJour.checked)
				{
					//this.rbnAll.setValue(true);
					this.dfJour.setValue(this.periodeCollection[this.currentSelectedIndex]['datedebut']);
					this.gridHoraires.getStore().loadData(this.periodeCollection[this.currentSelectedIndex].jours);
					this.fsHoraires.setVisible(true);
					this.gridHoraires.setVisible(true);
				}
			}

			this.onBeforeSave();
			this.setHiddenValue();
		}
		this.firstRender = true;
	},


	setHiddenValue: function() {
		var datas = [];
		Ext.each(this.tableauEnregistrer, function(record) {
			var data = {};
			Ext.iterate(record, function(k, v) {
				if (Ext.isDate(v)) {
					v = v.format('Y-m-d');
				}
				data[k] = v;
			});
			datas.push(data);
		});
		this.hiddenField.setValue(Ext.encode(datas));
	},

	/*
	* DISPONIBILITES
	*
	*/
	nbrJourAnnee: function(aaaa, mm, jj)
	{
		var MaDate  = new Date(aaaa,mm,jj);//date a traiter
		var annee = MaDate.getFullYear();//année de la date à traiter
		var NumSemaine = 0,//numéro de la semaine

		// calcul du nombre de jours écoulés entre le 1er janvier et la date à traiter.
		// ----------------------------------------------------------------------------
		// initialisation d'un tableau avec le nombre de jours pour chaque mois
		ListeMois = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
		// si l'année est bissextile alors le mois de février vaut 29 jours
		if (annee %4 == 0 && annee %100 !=0 || annee %400 == 0){
			ListeMois[1] = 29
		};
		// on parcours tous les mois précédants le mois à traiter
		// et on calcul le nombre de jour écoulé depuis le 1er janvier dans TotalJour
		var TotalJour = 0;
		for(cpt = 0; cpt < mm; cpt++){
			TotalJour += ListeMois[cpt];
		}
		TotalJour += jj;
		return TotalJour;
	},

	jourDeLaSemaine: function(aaaa,mm,jj)
	{
		//calcul du nombre de jour depuis le début de l'année
		totalJour = this.nbrJourAnnee(aaaa,mm,jj);

		//définition du premier jour de chaque annee

		var jour1 = (new Date(aaaa, 0, 1, 0, 0, 0, 0)).getDay();
		var jourFinal = (jour1+totalJour-1)%7;
		jourFinal = (jourFinal>1 ? jourFinal-2:jourFinal+5);
		return jourFinal;
	},

	DefSemaineNum: function(aaaa, mm, jj)
	{

		//initialisation des variables
		//----------------------------
		var MaDate  = new Date(aaaa,mm,jj);//date a traiter
		var annee = MaDate.getFullYear();//année de la date à traiter
		var NumSemaine = 0,//numéro de la semaine

		// calcul du nombre de jours écoulés entre le 1er janvier et la date à traiter.
		// ----------------------------------------------------------------------------
		TotalJour = this.nbrJourAnnee(aaaa,mm,jj);

		jourPremierAn = this.jourDeLaSemaine(aaaa,0,1)+2;
		if (jourPremierAn==8) jourPremierAn=1;

		nbrJourASupprimer = 8-jourPremierAn+1;
		if (nbrJourASupprimer==8) nbrJourASupprimer=1;

		TotalJour2 = TotalJour - nbrJourASupprimer;

		NumSemaine = Math.floor(TotalJour2/7)+1;

		// if (NumSemaine==0) NumSemaine=52;

		return(NumSemaine + 1);
	},

	afficherTableaux: function(mois,annee,nbr,dispos,idFiche,zone, produit)
	{
		var divbase=document.getElementById('dispos');
		while(divbase.firstChild!=null)
		{
			divbase.removeChild(divbase.firstChild);
		}

		var moisSuivant = mois+1;
		var moisPrecedent = mois-1;
		var anneeSuivante = annee;
		var anneePrecedente = annee;
		var moisSelect = mois;
		var anneeSelect = annee;
		if (moisSuivant==13)
		{
			moisSuivant=1;
			anneeSuivante++;
		}
		if (moisPrecedent==0)
		{
			moisPrecedent=12;
			anneePrecedente--;
		}


		for(it=0;it<nbr;it++)
		{
			//extraire le premier mois
			var pos = dispos.indexOf('-',0);
			var dispo = dispos.substring(0,pos);
			dispos = dispos.substring(pos+1,dispos.length);
			var classdiv = "mois";
			if (it%3==2)
			{
				classdiv = "mois last";
			}
			this.afficherTableau(mois,annee,null,zone,classdiv);
			mois++;
			if (mois==13)
			{
				mois=1;
				annee++;
			}
		}

		// IE 7, pour forcer le rendu
		//divPag.innerHTML = divPag.innerHTML;
	},

	afficherTableau: function(mois,annee,dispo,zone,classdiv)
	{
		var intituleMois;
		if (mois== 1) intituleMois="Janvier";
		if (mois== 2) intituleMois="Février";
		if (mois== 3) intituleMois="Mars";
		if (mois== 4) intituleMois="Avril";
		if (mois== 5) intituleMois="Mai";
		if (mois== 6) intituleMois="Juin";
		if (mois== 7) intituleMois="Juillet";
		if (mois== 8) intituleMois="Août";
		if (mois== 9) intituleMois="Septembre";
		if (mois==10) intituleMois="Octobre";
		if (mois==11) intituleMois="Novembre";
		if (mois==12) intituleMois="Décembre";

		var divbase=document.getElementById('dispos');

		var divMois = document.createElement('div');
		divMois.setAttribute('class',classdiv);

		var divTable = document.createElement('table');

		var divTr = document.createElement('tr');

		var tmpDiv = document.createElement('th');
		tmpDiv.setAttribute('colspan','8');

		var tmpTxt = document.createTextNode(intituleMois+' '+annee);

		tmpDiv.appendChild(tmpTxt);
		divTr.appendChild(tmpDiv);
		divTable.appendChild(divTr);

		divTr = document.createElement('tr');

		this.ajoutTD(divTr,'Sem','',false);
		this.ajoutTD(divTr,'L','',false);
		this.ajoutTD(divTr,'M','',false);
		this.ajoutTD(divTr,'M','',false);
		this.ajoutTD(divTr,'J','',false);
		this.ajoutTD(divTr,'V','',false);
		this.ajoutTD(divTr,'S','',false);
		this.ajoutTD(divTr,'D','',false);

		divTable.appendChild(divTr);

		//Connaitre le premier jour
		var laDate = new Date(annee,mois,1);
		var jour = this.jourDeLaSemaine(annee,mois-1,1);
		jour++;
		if (jour==7) jour=0;


		var bissextile = 0;
		//Obtenir le nombre max de jours
		var nbrJours = 0;
		if (annee%4==0)
		{
			bissextile=1;
		}

		if((mois%2!=0&&mois<8) || (mois%2==0&&mois>7))
		{
			nbrJours=31;
		}

		if((mois%2==0&&mois<8) || (mois%2!=0&&mois>7))
		{
			nbrJours=30;
		}

		if(mois==2 && bissextile==1)
		{
			nbrJours=29;
		}

		if(mois==2 && bissextile==0)
		{
			nbrJours=28;
		}
		//Obtenir le numéro de semaine
		var semaine = this.DefSemaineNum(annee,mois-1,1);

		//Iteration num 1 début du tableau + jours du dernier mois

		divTr = document.createElement('tr',false);
		this.ajoutTD(divTr,semaine,'');
		for(it2=0;it2<jour;it2++)
		{
			this.ajoutTD(divTr,'','',false);
		}

		//Iteration num 2 sur chaque jour, dès qu'on arrive à 7 on change de ligne
		for(it3=0;it3<nbrJours;it3++)
		{
			var laDispo='ferme';
			var idx=-1;
			var vDate = annee+'-'+(mois.toString().length > 1 ? mois:'0'+mois)+'-'+((it3+1).toString().length > 1 ? (it3+1):'0'+(it3+1)) ;
			var vAuxDate = Date.parseDate(vDate,'Y-m-d').format('U');
			this.grid.getStore().each(function(record) {
				var recordDateDebut = new Date(record.data.datedebut);
				var recordDateFin = new Date(record.data.datefin);
				if (!Ext.isDate(recordDateDebut) || !Ext.isDate(recordDateFin)) {
					return true;
				}
				var debut = recordDateDebut.format('U');
				var fin = recordDateFin.format('U');

				if ((vAuxDate >= debut) && (vAuxDate <= fin)) {
					laDispo='dispo';
					idx = this.findPeriode(record);
				}
			}, this);
			if(idx!=-1 && this.periodeCollection[idx].jours[0].jour != 'Tous les jours' )
			{
				//if(this.periodeCollection[idx].jours[(jour>1 ? jour-2:jour+5)].ferme)
				if((this.periodeCollection[idx].jours[jour].ferme)/* || (Ext.isEmpty(this.periodeCollection[idx].jours[jour]['debut1']) && Ext.isEmpty(this.periodeCollection[idx].jours[jour]['fin1']) &&
					Ext.isEmpty(this.periodeCollection[idx].jours[jour]['debut2']) && Ext.isEmpty(this.periodeCollection[idx].jours[jour]['fin2']))*/)
				{
					laDispo='ferme';
				}
			}
			this.ajoutTD(divTr,it3+1,laDispo,true);
			jour++;
			if (jour==7 && it3+1<nbrJours)
			{
				jour=0;
				divTable.appendChild(divTr);
				divTr = document.createElement('tr');
				this.ajoutTD(divTr,this.DefSemaineNum(annee,mois-1,it3+2),'',false);
			}
		}

		//Iteration num 3 terminer le tableau
		for(it4=jour;it4<7;it4++)
		{
			this.ajoutTD(divTr,'','',false);
		}
		divTable.appendChild(divTr);

		divMois.appendChild(divTable);

		divbase.appendChild(divMois);

		//IE 7, pour forcer le rendu
		divbase.innerHTML = divbase.innerHTML;

	},

	ajoutTD: function(leTR,libelle,classe,divVide)
	{
		var tmpDiv = document.createElement('td');
		if (classe!='')
		{
			tmpDiv.setAttribute('class',classe);
		}
		var tmpTxt = document.createTextNode(libelle);
		tmpDiv.appendChild(tmpTxt);

		if (divVide)
		{
			var tmpDiv2 = document.createElement('div');
			tmpDiv.appendChild(tmpDiv2);
		}

		leTR.appendChild(tmpDiv);
	}


});
Ext.reg('periodeouverture', Ext.ts.PeriodeOuverture);



/**
 * BIBLIOTHEQUE DE FICHIERS
 * Composant permettant de gérer une bibliothèque de fichiers
 */
Ext.ts.ImageChooser = Ext.extend(Ext.Panel, {
	cls: 'img-chooser-dlg',
	width: '100%',
	height: 600,
	layout: 'border',
	border: false,

	initComponent: function() {
		this.addEvents('change');

		this.hiddenField = new Ext.form.Hidden({
			name: this.tsName
		});

		this.idFichier = 1;
		this.countPhoto = 0;
		this.initTemplates();

		// ITEMS
		this.view = new Ext.DataView({
			tpl: this.thumbTemplate,
			singleSelect: true,
			overClass: 'x-view-over',
			itemSelector: 'div.thumb-wrap',
			emptyText: '<div style="padding:10px;">'+Ext.ts.Lang.bibliothequeFichierEmpty+'</div>',
			store: new Ext.data.JsonStore({
				root: 'fichiers',
				fields: [
					{name:'idFichier', type: 'int'},
					{name:'nom_fichier', type: 'string'},
					{name:'url_fichier', type: 'string'},
					{name:'type_fichier', type: 'string'},
					{name:'description_fichier', type: 'string'},
					{name:'ordre', type: 'int'}
				],
				sortInfo: {field: 'ordre', direction: 'ASC'}
			}),
			listeners: {
				selectionchange: {
					fn: this.onSelectionChange,
					scope: this,
					buffer: 100
				},
				beforeselect: function(view) {
					return view.getStore().getRange().length > 0;
				}
			},
			prepareData: this.formatData.createDelegate(this)
		});

		this.fieldSearch = new Ext.form.TextField({
			width: 200,
			selectOnFocus: true,
			listeners: {
				render: {
					fn: function(cmp){
						cmp.getEl().on('keyup', this.filter, this, {buffer:500});
					},
					scope: this
				}
			}
		});

		this.btnUp = new Ext.Button({
			iconCls: 'resultset_previous',
			handler: function() {
				this.sortImage(-1);
			},
			scope: this,
			disabled: true
		});

		this.btnDown = new Ext.Button({
			iconCls: 'resultset_next',
			handler: function() {
				this.sortImage(1);
			},
			scope: this,
			disabled: true
		});

		this.btnDelete = new Ext.Button({
			text: Ext.ts.Lang.supprimer,
			iconCls: 'delete',
			handler: this.deleteFichier,
			scope: this,
			disabled: true
		});

		var centerPanel = new Ext.Panel({
			cls: 'img-chooser-view',
			region: 'center',
			autoScroll: true,
			items: this.view,
			tbar:[
				Ext.ts.Lang.rechercher+' : ',
				this.fieldSearch, '->',
				this.btnUp,
				'Ordonner les images',
				this.btnDown, '-',
				this.btnDelete
			]
		});

		this.eastPanel = new Ext.Panel({
			cls: 'img-chooser-view',
			region: 'east',
			split: true,
			width: 250,
			minWidth: 200,
			maxWidth: 350
		});

		this.items = [centerPanel, this.eastPanel, this.hiddenField];

		// BUTTONS
		this.btnAdd = new Ext.Button({
			text: Ext.ts.Lang.addFichier,
			iconCls: 'add',
			handler: this.uploadFichier,
			scope: this
		});

		this.btnAddVideos = new Ext.Button({
			text: Ext.ts.Lang.addVideo,
			iconCls: 'add',
			handler: this.uploadVideo,
			scope: this
		});

		this.buttons = [this.btnAdd, this.btnAddVideos];

		Ext.ts.ImageChooser.superclass.initComponent.call(this);

		this.on('change', this.setHiddenValue, this);
	},

	formatData: function(data) {
		data.nom_fichier = Ext.isEmpty(data.nom_fichier) ? 'Sans titre' : data.nom_fichier;
		return data;
	},

	isImage: function(type) {
		return type == "03.01.01";
	},

	isTexte: function(type) {
		return type == "03.01.02";
	},

	isSon: function(type) {
		return type == "03.01.03";
	},

	isVideo: function(type) {
		return type == "03.01.04";
	},

	initTemplates : function() {
		this.thumbTemplate = new Ext.XTemplate(
			'<div class="img-chooser-types">',
				'<h2 class="img-chooser-title picture">' + Ext.ts.Lang.images + ' ('+Ext.ts.Lang.typesSupportes+' : jpg, jpeg)</h2>',
				'<tpl for=".">',
					'<tpl if="this.isImage(type_fichier)">',
						'<div class="thumb-wrap" id="idFichier{idFichier}">',
							'<div class="thumb"><img src="{[this.urlThumbImage(values.url_fichier, 80, 60)]}" title="{nom_fichier}" alt="{nom_fichier}" /></div>',
							'<span>{[this.shortName(values.nom_fichier)]}</span>',
						'</div>',
					'</tpl>',
				'</tpl>',
			'</div>',
			'<div class="img-chooser-types">',
				'<h2 class="img-chooser-title page_white_acrobat">' + Ext.ts.Lang.documents + ' ('+Ext.ts.Lang.typesSupportes+' : pdf)</h2>',
				'<tpl for=".">',
					'<tpl if="this.isTexte(type_fichier)">',
						'<div class="thumb-wrap" id="idFichier{idFichier}">',
							'<div class="thumb"><img src="images/fichierTexte.png" title="{nom_fichier}" alt="{nom_fichier}" /></div>',
							'<span>{[this.shortName(values.nom_fichier)]}</span>',
						'</div>',
					'</tpl>',
				'</tpl>',
			'</div>',
			/*'<div class="img-chooser-types">',
				'<h2 class="img-chooser-title music">' + Ext.ts.Lang.sons + '</h2>',
				'<tpl for=".">',
					'<tpl if="this.isSon(type_fichier)">',
						'<div class="thumb-wrap" id="idFichier{idFichier}">',
							'<div class="thumb"><img src="images/fichierSon.png" title="{nom_fichier}" alt="{nom_fichier}" /></div>',
							'<span>{[this.shortName(values.nom_fichier)]}</span>',
						'</div>',
					'</tpl>',
				'</tpl>',
			'</div>',*/
			'<div class="img-chooser-types">',
				'<h2 class="img-chooser-title film">' + Ext.ts.Lang.videos + ' ('+Ext.ts.Lang.sitesSupportes+' : '+  Ext.ts.Lang.descSitesSupportes +')</h2>',
				'<tpl for=".">',
					'<tpl if="this.isVideo(type_fichier)">',
						'<div class="thumb-wrap" id="idFichier{idFichier}">',
							'<div class="thumb"><img src="{[this.urlThumbVideo(values.description_fichier)]}" title="{nom_fichier}" alt="{nom_fichier}" /></div>',
							'<span>{[this.shortName(values.nom_fichier)]}</span>',
						'</div>',
					'</tpl>',
				'</tpl>',
			'</div>',{
				isImage: this.isImage,
				isTexte: this.isTexte,
				isSon: this.isSon,
				isVideo: this.isVideo,
				shortName: this.shortName,
				urlThumbImage: this.urlThumbImage,
				urlThumbVideo: this.urlThumbVideo
			}
		);
		this.thumbTemplate.compile();

		this.detailsTemplate = new Ext.XTemplate(
			'<div class="details">',
				'<tpl for=".">',

					'<tpl if="this.isImage(type_fichier)">',
						'<img src="{[this.urlThumbImage(values.url_fichier, 200, 130)]}" alt="{nom_fichier}" /><div class="details-info" />',
						'<b>'+Ext.ts.Lang.nomImage+' :</b>',
						'<span>{nom_fichier}</span><br />',
						'<a href="{url_fichier}" target="_blank">'+Ext.ts.Lang.ouvrir+'</a><br />',
					'</tpl>',

					'<tpl if="this.isTexte(type_fichier)">',
						'<img src="images/fichierTexte.png" alt="{nom_fichier}" /><div class="details-info" />',
						'<b>'+Ext.ts.Lang.nomFichier+' :</b>',
						'<span>{nom_fichier}</span><br />',
						'<a href="{url_fichier}" target="_blank">'+Ext.ts.Lang.ouvrir+'</a><br />',
					'</tpl>',

					/*'<tpl if="this.isSon(type_fichier)">',
						'<img src="images/fichierSon.png" alt="{nom_fichier}" /><div class="details-info" />',
						'<b>'+Ext.ts.Lang.nomFichier+' :</b>',
						'<span>{nom_fichier}</span><br />',
						'<a href="#" class="showFichier">'+Ext.ts.Lang.ouvrir+'</a><br />',
					'</tpl>',*/

					'<tpl if="this.isVideo(type_fichier)">',
						'<div class="thumb"><a href="#" class="showFichier"><img src="{[this.urlThumbVideo(values.description_fichier)]}" alt="{nom_fichier}" /></a></div><div class="details-info" />',
						'<b>'+Ext.ts.Lang.nomFichier+' :</b>',
						'<span>{nom_fichier}</span><br />',
						'<a href="#" class="showFichier">'+Ext.ts.Lang.ouvrir+'</a><br />',
					'</tpl>',

				'</tpl>',
			'</div>',{
				isImage: this.isImage,
				isTexte: this.isTexte,
				isSon: this.isSon,
				isVideo: this.isVideo,
				urlThumbImage: this.urlThumbImage,
				urlThumbVideo: this.urlThumbVideo
			}
		);
		this.detailsTemplate.compile();
	},

	initEvents: function() {
		var nodes = Ext.query('.showFichier');
		Ext.each(nodes, function(node) {
			var element = Ext.get(node);
			element.addListener('click', this.showFichier, this);
		}, this);
	},

	filter: function(){
		this.view.getStore().filter('nom_fichier', this.fieldSearch.getValue());
	},

	sortStore: function() {
		// Il semblerait que le Store mette le trie en cache...
		// Ceci le force à le regénérer
		// Du coup, la méthode this.view.getSelectedRecords n'est pas fiable
		// D'où l'utilisation d'un idFichier temporaire et de getSelectedNodes
		this.view.getStore().sort('nom_fichier', 'desc');
		this.view.getStore().sort('ordre', 'asc');
	},

	getRecord: function(nodeId) {
		var index = this.view.getStore().findExact('idFichier', parseInt(nodeId.substr(9)));
		return this.view.getStore().getAt(index);
	},

	onSelectionChange : function(){
		var selNodes = this.view.getSelectedNodes();
		var detailEl = this.eastPanel.body;

		if (selNodes.length == 1) {
			var record = this.getRecord(selNodes[0].id);
			this.btnUp.setDisabled(!this.isImage(record.data.type_fichier) || record.data.ordre == 0);
			this.btnDown.setDisabled(!this.isImage(record.data.type_fichier) || record.data.ordre == this.countPhoto - 1);
			this.btnDelete.enable();

			detailEl.hide();
			this.detailsTemplate.overwrite(detailEl, record.data);
			detailEl.slideIn('l', {stopFx: true, duration: .2});

			this.initEvents();
		}
		else {
			this.btnUp.disable();
			this.btnDown.disable();
			this.btnDelete.disable();

			detailEl.update('');
		}
	},
	
	shortName: function(string) {
		return string.ellipse(15);
	},

	urlThumbImage: function(url, width, height) {
		url = url.replace(' ', '%20');
		url = url.replace('&', '%26');
		
		return Ext.ts.url({
			service: 'ficheFichier',
			action: 'resizeImage',
			params: {
				u: url,
				w: width,
				h: height
			}
		});
	},

	urlThumbVideo: function(description) {
		if(Ext.isEmpty(description)) {
			return "images/fichierVideo.png";
		}

		var sources = {
			vimeo: "images/fichierVideo.png",
			dailymotion: "http://www.dailymotion.com/thumbnail/video/{idVideo}",
			youtube: "http://img.youtube.com/vi/{idVideo}/2.jpg"
		};

		var arrDescription = description.split(':');
		var source = arrDescription[0];
		var idVideo = arrDescription[1];

		return sources[source].replace('{idVideo}', idVideo);
	},

	showFichier: function() {
		var selNodes = this.view.getSelectedNodes();

		if (selNodes.length == 1) {
			var record = this.getRecord(selNodes[0].id);

			if (this.isSon(record.data.type_fichier)) {
				var tpl = 	'<div class="img-chooser-preview">' +
								'<embed src="{url_fichier}" height="400" width="600" />' +
							'</div>';
			}
			else if (this.isVideo(record.data.type_fichier)) {
				var tpl = '<iframe src="{url_fichier}" width="100%" height="100%" style="border:0;" />';
			}
			else {
				return false;
			}

			var win = new Ext.Window({
				title: record.data.nom_fichier,
				border: false,
				width: 640,
				height: 480,
				modal: true,
				resizable: false,
				maximizable: false,
				closeAction: 'close',
				layout: 'fit',
				items: new Ext.Panel({
					autoScroll: true,
					data: record.data,
					tpl: new Ext.XTemplate(tpl)
				})
			});
			win.show();
		}
	},

	sortImage: function(value) {
		var selNodes = this.view.getSelectedNodes();
		var selIndexes = this.view.getSelectedIndexes();

		if (selNodes.length == 1) {
			var recordToMove = this.getRecord(selNodes[0].id);
			var recordToSwitch = this.view.getStore().getAt(this.view.getStore().findExact('ordre', recordToMove.data.ordre + value));

			recordToMove.set('ordre', recordToMove.data.ordre + value);
			recordToSwitch.set('ordre', recordToMove.data.ordre - value);

			this.sortStore();

			this.view.select(selIndexes[0] + value);

			this.fireEvent('change');
		}
	},

	uploadFichier: function(data) {
		var dialog = new Ext.ux.UploadDialog.Dialog({
			url: Ext.ts.url({
				service: 'ficheFichier',
				action: 'uploadFile'
			}),
			reset_on_hide: false,
			allow_close_on_upload: false,
			upload_autostart: false,
			permitted_extensions: ["jpg", "jpeg", "pdf"/*, "mp3"*/],
			listeners: {
				uploadsuccess: {
					fn: function (win, name, response) {
						this.addFichier(response);
					},
					scope: this
				}
			}
		});
		dialog.show('ok-btn');
	},

	uploadVideo: function(data) {
		var addVideoWin = new Ext.ts.ManagementWindow({
			title: "Ajouter une vidéo",
			width: 550,
			height: 80,
			remote: false,
			closeWin: false,
			items: [{
				xtype: 'textfield',
				name: 'nom',
				fieldLabel: Ext.ts.Lang.nomVideo,
				width: 350,
				allowBlank: false
			},{
				xtype: 'textfield',
				name: 'url',
				fieldLabel: Ext.ts.Lang.lienVideo,
				width: 350,
				allowBlank: false
			}],
			callback: function(win, values) {
				var regExps = [{
					regExp: /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/,
					match: 5,
					url: "http://player.vimeo.com/video/{idVideo}",
					source: 'vimeo'
				},{
					regExp: /video\/([^_]+)/,
					match: 1,
					url: "http://www.dailymotion.com/embed/video/{idVideo}",
					source: 'dailymotion'
				},{
					regExp: /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/,
					match: 7,
					url: "http://www.youtube.com/embed/{idVideo}",
					source: 'youtube'
				}];

				var parsed = false;
				Ext.each(regExps, function(regExp) {
					var match = values.url.match(regExp.regExp);
					if (!Ext.isEmpty(match)) {
						parsed = true;
						url = regExp.url.replace('{idVideo}', match[regExp.match]);
						source = regExp.source;
						idVideo = match[regExp.match];
						return false;
					}
				})

				if (!parsed) {
					Ext.Msg.show({
						title: Ext.ts.Lang.failureTitle,
						minWidth: 250,
						msg: Ext.ts.Lang.urlInvalide,
						buttons: Ext.Msg.OK,
						icon: Ext.Msg.ERROR
					});
					
					return false;
				}

				var file = {
					type: "03.01.04",
					name: values.nom,
					url: url,
					description: source + ':' + idVideo
				};
				this.addFichier(file);

				win.destroy();
			},
			scope: this
		});
		addVideoWin.show();
	},

	addFichier: function(file) {
		var Record = this.view.getStore().recordType;
		this.view.getStore().addSorted(new Record({
			idFichier: this.idFichier++,
			nom_fichier: file.name,
			url_fichier: file.url,
			type_fichier: file.type,
			description_fichier: file.description,
			ordre: (this.isImage(file.type)) ? this.countPhoto++ : -1
		}));
		this.sortStore();
		this.fireEvent('change');
	},

	deleteFichier: function() {
		var selNodes = this.view.getSelectedNodes();

		if (selNodes.length == 1) {
			Ext.MessageBox.confirm(
				Ext.ts.Lang.confirmTitle,
				Ext.ts.Lang.deleteFichier,
				function (btn) {
					if (btn == 'yes') {
						var record = this.getRecord(selNodes[0].id);

						this.view.getStore().remove(record);
						this.countPhoto--;
						if (this.isImage(record.data.type_fichier)) {
							this.view.getStore().each(function(rec) {
								rec.data.ordre -= (rec.data.ordre > record.data.ordre) ? 1 : 0;
							});
						}
						this.sortStore();
						this.fireEvent('change');
					}
				},
				this
			);
		}
	},

	setValue: function(data) {
		Ext.each(data, function(item) {
			item.idFichier = this.idFichier++;
			item.ordre = this.isImage(item.type_fichier) ? this.countPhoto++ : -1;
		}, this);
		this.view.store.loadData({
			fichiers: data
		});
		this.setHiddenValue();
	},

	setHiddenValue: function() {
		var data = [];
		this.view.store.each(function(record) {
			data.push(record.data);
		});
		this.hiddenField.setValue(Ext.encode(data));
	}

});
Ext.reg('imgchooser', Ext.ts.ImageChooser);



/**
 * FABRIQUE A CHAMPS
 * Componsant permettant de gérer les champs spécifiques dynamiquement
 */
Ext.ts.FieldFactory = Ext.extend(Ext.Panel, {
	border: false,
	height: 'auto',
	width: 'auto',
	layout: 'form',

	initComponent: function() {
		this.addEvents('change');

		Ext.ts.FieldFactory.superclass.initComponent.call(this);
	},

	setValue: function(data) {
		if (!Ext.isEmpty(data)) {
			Ext.each(data, this.buildField, this);
		}
	},

	buildField: function(field) {
		switch (field.type) {
			case 'text':
				this.buildFieldText(field);
				break;
			case 'textarea':
				this.buildFieldTextArea(field);
				break;
			case 'select':
				this.buildFieldSelect(field);
				break;
			case 'multiple':
				this.buildFieldMultiple(field);
				break;
		}
	},

	buildFieldText: function(field) {
		var fieldText = new Ext.ts.TextField({
			fieldLabel: field.libelle,
			id: field.name,
			name: field.name,
			value: field.value,
			listeners: {
				change: this.onChange,
				scope: this
			},
			disabled: field.disabled
		});
		this.add(fieldText);
	},

	buildFieldTextArea: function(field) {
		var fieldText = new Ext.ts.TextArea({
			fieldLabel: field.libelle,
			id: field.name,
			name: field.name,
			value: field.value,
			listeners: {
				change: this.onChange,
				scope: this
			},
			disabled: field.disabled
		});
		this.add(fieldText);
	},

	buildFieldSelect: function(field) {
		var fieldSelect = new Ext.ts.ComboMTH({
			fieldLabel: field.libelle,
			id: field.name,
			tsName: field.name,
			LS: field.list,
			key: field.key,
			value: field.value,
			listeners: {
				change: this.onChange,
				scope: this
			},
			disabled: field.disabled
		});
		this.add(fieldSelect);
	},

	buildFieldMultiple: function(field) {
		var fieldMultiple = new Ext.ts.ListMTH({
			fieldLabel: field.libelle,
			id: field.name,
			tsName: field.name,
			LS: field.list,
			key: field.key,
			listeners: {
				change: this.onChange,
				scope: this
			},
			disabled: field.disabled
		});
		fieldMultiple.setValue(field.value);
		this.add(fieldMultiple);
	},

	onChange: function() {
		this.fireEvent('change');
	}

});
Ext.reg('fieldfactory', Ext.ts.FieldFactory);



/**
 * LISTE DES CHAMPS
 */
Ext.ts.formEdition = {
	type_etablissement: {
		xtype: 'listmth',
		id: 'type_etablissement',
		tsName: 'type_etablissement',
		fieldLabel: Ext.ts.Lang.typeEtablissement,
		groupColumns: 1,
		LS: 'LS_ControlledVocabulary'
	},
	raison_sociale: {
		xtype: 'ts_textfield',
		id: 'raison_sociale',
		name: 'raison_sociale',
		fieldLabel: Ext.ts.Lang.raisonSociale,
		maxLength: 160
	},
	adresse1: {
		xtype: 'ts_textfield',
		id: 'adresse1',
		name: 'adresse1',
		fieldLabel: 'Adresse 1',
		maxLength: 80
	},
	adresse2: {
		xtype: 'ts_textfield',
		id: 'adresse2',
		name: 'adresse2',
		fieldLabel: 'Adresse 2',
		maxLength: 80
	},
	adresse3: {
		xtype: 'ts_textfield',
		id: 'adresse3',
		name: 'adresse3',
		fieldLabel: 'Adresse 3',
		maxLength: 80
	},
	code_postal: {
		xtype: 'ts_textfield',
		id: 'code_postal',
		name: 'code_postal',
		fieldLabel: 'Code postal',
		width: 125,
		maxLength: 10
	},
	cedex: {
		xtype: 'ts_textfield',
		id: 'cedex',
		name: 'cedex',
		fieldLabel: 'Cedex',
		width: 125,
		maxLength: 10
	},
	bureau_distributeur: {
		xtype: 'ts_textfield',
		id: 'bureau_distributeur',
		name: 'bureau_distributeur',
		fieldLabel: 'Bureau distributeur',
		width: 125
	},
	province_etat: {
		xtype: 'ts_textfield',
		id: 'province_etat',
		name: 'province_etat',
		fieldLabel: 'Province/Etat',
		maxLength: 10
	},
	commune: {
		xtype: 'ts_textfield',
		id: 'commune',
		name: 'commune',
		fieldLabel: 'Commune',
		disabled: true
	},
	telephone1: {
		xtype: 'ts_textfield',
		id: 'telephone1',
		name: 'telephone1',
		fieldLabel: 'Téléphone 1',
		width: 125,
		maxLength: 800
	},
	telephone2: {
		xtype: 'ts_textfield',
		id: 'telephone2',
		name: 'telephone2',
		fieldLabel: 'Téléphone 2',
		width: 125,
		maxLength: 800
	},
	fax: {
		xtype: 'ts_textfield',
		id: 'fax',
		name: 'fax',
		fieldLabel: 'Fax',
		width: 125,
		maxLength: 800
	},
	site_web: {
		xtype: 'ts_textfield',
		id: 'site_web',
		name: 'site_web',
		fieldLabel: 'Site Web',
		maxLength: 800,
		vtype: 'url'
	},
	email: {
		xtype: 'ts_textfield',
		id: 'email',
		name: 'email',
		fieldLabel: 'Email',
		maxLength: 800,
		vtype: 'email'
	},
	siret: {
		xtype: 'ts_textfield',
		id: 'siret',
		name: 'siret',
		fieldLabel: 'SIRET',
		width: 125,
		maxLength: 14
	},
	ape_naf: {
		xtype: 'ts_textfield',
		id: 'ape_naf',
		name: 'ape_naf',
		fieldLabel: 'APE/NAF',
		width: 125,
		maxLength: 5
	},
	rcs: {
		xtype: 'ts_textfield',
		id: 'rcs',
		name: 'rcs',
		fieldLabel: 'RCS',
		width: 125,
		maxLength: 12
	},


	description_commerciale: {
		xtype: 'mltextarea',
		id: 'description_commerciale',
		tsName: 'description_commerciale'
	},
	description_commerciale_fr: {
		xtype: 'ts_textarea',
		id: 'description_commerciale_fr',
		name: 'description_commerciale_fr',
		hideLabel: true
	},
	description_commerciale_en: {
		xtype: 'ts_textarea',
		id: 'description_commerciale_en',
		name: 'description_commerciale_en',
		hideLabel: true
	},
	description_commerciale_es: {
		xtype: 'ts_textarea',
		id: 'description_commerciale_es',
		name: 'description_commerciale_es',
		hideLabel: true
	},
	description_commerciale_de: {
		xtype: 'ts_textarea',
		id: 'description_commerciale_de',
		name: 'description_commerciale_de',
		hideLabel: true
	},
	description_commerciale_it: {
		xtype: 'ts_textarea',
		id: 'description_commerciale_it',
		name: 'description_commerciale_it',
		hideLabel: true
	},
	description_commerciale_nl: {
		xtype: 'ts_textarea',
		id: 'description_commerciale_nl',
		name: 'description_commerciale_nl',
		hideLabel: true
	},


	slogan: {
		xtype: 'mltextarea',
		id: 'slogan',
		tsName: 'slogan'
	},
	slogan_fr: {
		xtype: 'ts_textarea',
		id: 'slogan_fr',
		name: 'slogan_fr',
		hideLabel: true
	},
	slogan_en: {
		xtype: 'ts_textarea',
		id: 'slogan_en',
		name: 'slogan_en',
		hideLabel: true
	},
	slogan_es: {
		xtype: 'ts_textarea',
		id: 'slogan_es',
		name: 'slogan_es',
		hideLabel: true
	},
	slogan_de: {
		xtype: 'ts_textarea',
		id: 'slogan_de',
		name: 'slogan_de',
		hideLabel: true
	},
	slogan_it: {
		xtype: 'ts_textarea',
		id: 'slogan_it',
		name: 'slogan_it',
		hideLabel: true
	},
	slogan_nl: {
		xtype: 'ts_textarea',
		id: 'slogan_nl',
		name: 'slogan_nl',
		hideLabel: true
	},
	
	
	langues_parlees: {
		xtype: 'objectgrid',
		id: 'langues_parlees',
		tsName: 'langues_parlees',
		height: 250,
		fields: {
			langue: {
				xtype: 'combomth',
				fieldLabel: 'Langue',
				LS: 'LS_LANGUES',
				required: true
			},
			usage: {
				xtype: 'listmth',
				fieldLabel: 'Usage',
				groupColumns: 1,
				LS: 'LS_Usage',
				pop: '11.01.02,11.01.03,11.01.04,11.01.05,11.01.08'
			}
		},
		formItems: [{
			fieldset: 'Détail',
			items: ['langue', 'usage']
		}],
		gridColumns: {
			langue: {width: 150}
		}
	},
	langues_parlees_accueil: {
		xtype: 'listmth',
		id: 'langues_parlees_accueil',
		tsName: 'langues_parlees_accueil',
		LS: 'LS_LANGUES'
	},
	capacite_personne: {
		xtype: 'ts_textfield',
		id: 'capacite_personne',
		name: 'capacite_personne',
		fieldLabel: 'Nombre de personnes',
		width: 125
	},
	capacite_chambre: {
		xtype: 'ts_textfield',
		id: 'capacite_chambre',
		name: 'capacite_chambre',
		fieldLabel: 'Nombre de chambres',
		width: 125
	},
	capacite_superficie: {
		xtype: 'ts_textfield',
		id: 'capacite_superficie',
		name: 'capacite_superficie',
		fieldLabel: 'Superficie totale (m2)',
		width: 125
	},
	capacite: {
		xtype: 'objectgrid',
		id: 'capacite',
		tsName: 'capacite',
		height: 450,
		fields: {
			type: {
				xtype: 'combomth',
				fieldLabel: 'Type de capacité',
				LS: 'LS_Unite',
				key: '14.03.02.*',
				pop: '14.03.02.14,14.03.02.15,14.03.02.16',
				required: true
			},
			batiment: {
				xtype: 'combomth',
				fieldLabel: 'Type de bâtiment',
				LS: 'LS_TypeBatiment',
				key: '14.03.01.*'
			},
			nbunites: {
				xtype: 'textfield',
				fieldLabel: 'Nombre',
				width: 250
			},
			surface: {
				xtype: 'textfield',
				fieldLabel: 'Surface (en m2)',
				width: 250
			},
			nom: {
				xtype: 'textfield',
				fieldLabel: 'Nom',
				width: 250
			},
			description: {
				xtype: 'mltextarea',
				fieldLabel: 'Description',
				width: 400
			},
			personnesmin: {
				xtype: 'textfield',
				fieldLabel: 'Min',
				width: 250
			},
			personnesmax: {
				xtype: 'textfield',
				fieldLabel: 'Max',
				width: 250
			}
		},
		formItems: [{
			fieldset: 'Détail',
			items: ['type', 'batiment', 'nbunites', 'surface', 'nom', 'description']
		},{
			fieldset: 'Nombre de personnes',
			items: ['personnesmin', 'personnesmax']
		}],
		gridColumns: {
			type: {width: 200},
			surface: {width: 90},
			nbunites: {width: 90}
		}
	},
	classement: {
		xtype: 'combomth',
		id: 'classement',
		tsName: 'classement',
		fieldLabel: 'Classement préfectoral',
		LS: 'LS_Classement'
	},
	date_classement: {
		xtype: 'ts_datefield',
		id: 'date_classement',
		name: 'date_classement',
		fieldLabel: 'Date du classement'
	},
	numero_classement: {
		xtype: 'ts_textfield',
		id: 'numero_classement',
		name: 'numero_classement',
		fieldLabel: 'Numéro du classement',
		width: 125
	},
	label: {
		xtype: 'listmth',
		id: 'label',
		tsName: 'label',
		LS: 'LS_TypeClassement'
	},
	gites_de_france: {
		xtype: 'combomth',
		id: 'gites_de_france',
		tsName: 'gites_de_france',
		fieldLabel: 'Gîtes de France',
		LS: 'LS_Classement'
	},
	michelin: {
		xtype: 'combomth',
		id: 'michelin',
		tsName: 'michelin',
		fieldLabel: 'Michelin',
		LS: 'LS_Classement',
		key: '06.03.06.12'
	},
	handicap: {
		xtype: 'listmth',
		id: 'handicap',
		tsName: 'handicap',
		LS: 'LS_Classement',
		key: '06.05.*',
		groupColumns: 1
	},
	chaine: {
		xtype: 'listmth',
		id: 'chaine',
		tsName: 'chaine',
		LS: 'LS_TypeClassement'
	},
	interet: {
		xtype: 'combomth',
		id: 'interet',
		tsName: 'interet',
		fieldLabel: 'Intérêt de la fiche',
		LS: 'LS_Classement',
		key: '99.06.07.*'
	},



	contact: {
		xtype: 'objectgrid',
		id: 'contact',
		tsName: 'contact',
		height: 620,
		fields: {
			type_contact: {
				xtype: 'combomth',
				fieldLabel: 'Type de contact',
				LS: 'LS_Contact',
				pop: '04.03.13',
				required: true
			},
			raison_sociale: {
				xtype: 'textfield',
				fieldLabel: 'Raison sociale',
				width: 250,
				required: true
			},
			nom: {
				xtype: 'textfield',
				fieldLabel: 'Nom',
				width: 250,
				required: true
			},
			prenom: {
				xtype: 'textfield',
				fieldLabel: 'Prénom',
				width: 250
			},
			fonction: {
				xtype: 'textfield',
				fieldLabel: 'Fonction',
				width: 250
			},
			adresse1: {
				xtype: 'textfield',
				fieldLabel: 'Adresse 1',
				width: 250
			},
			adresse2: {
				xtype: 'textfield',
				fieldLabel: 'Adresse 2',
				width: 250
			},
			adresse3: {
				xtype: 'textfield',
				fieldLabel: 'Adresse 3',
				width: 250
			},
			code_postal: {
				xtype: 'textfield',
				fieldLabel: 'Code postal',
				width: 125
			},
			code_insee: {
				xtype: 'autocompletecombocommune',
				fieldLabel: 'Commune',
					service: 'territoires',
				action: 'getCommunes'
			},
			cedex: {
				xtype: 'textfield',
				fieldLabel: 'Cedex',
				width: 125
			},
			pays: {
				xtype: 'combomth',
				fieldLabel: 'Pays',
				LS: 'LS_Pays'
			},
			telephone1: {
				xtype: 'textfield',
				fieldLabel: 'Téléphone 1',
				width: 125
			},
			telephone2: {
				xtype: 'textfield',
				fieldLabel: 'Téléphone 2',
				width: 125
			},
			fax: {
				xtype: 'textfield',
				fieldLabel: 'Fax',
				width: 125
			},
			site_web: {
				xtype: 'textfield',
				fieldLabel: 'Site web',
				width: 250,
				vtype: 'url'
			},
			email: {
				xtype: 'textfield',
				fieldLabel: 'Email',
				width: 250,
				vtype: 'email'
			}
		},
		formItems: [{
			fieldset: 'Description du contact',
			items: [
				'type_contact', 'raison_sociale', 'nom', 'prenom', 'fonction',
				'adresse1', 'adresse2', 'adresse3', 'code_postal',
				'cedex', 'code_insee', 'pays'
			]
		},{
			fieldset: 'Moyens de communication',
			items: [
				'telephone1', 'telephone2', 'fax', 'site_web', 'email'
			]
		}],
		gridColumns: {
			raison_sociale: {width: 150},
			nom: {width: 150},
			type_contact: {width: 150}
		}
	},



	proprietaire_raison_sociale: {
		xtype: 'ts_textfield',
		id: 'proprietaire_raison_sociale',
		name: 'proprietaire_raison_sociale',
		fieldLabel: Ext.ts.Lang.raisonSociale,
		maxLength: 160
	},
	proprietaire_nom: {
		xtype: 'ts_textfield',
		id: 'proprietaire_nom',
		name: 'proprietaire_nom',
		fieldLabel: 'Nom'
	},
	proprietaire_prenom: {
		xtype: 'ts_textfield',
		id: 'proprietaire_prenom',
		name: 'proprietaire_prenom',
		fieldLabel: 'Prénom'
	},
	proprietaire_adresse1: {
		xtype: 'ts_textfield',
		id: 'proprietaire_adresse1',
		name: 'proprietaire_adresse1',
		fieldLabel: 'Adresse 1',
		maxLength: 80
	},
	proprietaire_adresse2: {
		xtype: 'ts_textfield',
		id: 'proprietaire_adresse2',
		name: 'proprietaire_adresse2',
		fieldLabel: 'Adresse 2',
		maxLength: 80
	},
	proprietaire_adresse3: {
		xtype: 'ts_textfield',
		id: 'proprietaire_adresse3',
		name: 'proprietaire_adresse3',
		fieldLabel: 'Adresse 3',
		maxLength: 80
	},
	proprietaire_code_postal: {
		xtype: 'ts_textfield',
		id: 'proprietaire_code_postal',
		name: 'proprietaire_code_postal',
		fieldLabel: 'Code postal',
		width: 125,
		maxLength: 10
	},
	proprietaire_cedex: {
		xtype: 'ts_textfield',
		id: 'proprietaire_cedex',
		name: 'proprietaire_cedex',
		fieldLabel: 'Cedex',
		width: 125,
		maxLength: 10
	},
	proprietaire_code_insee: {
		xtype: 'autocompletecombocommune',
		id: 'proprietaire_code_insee',
		name: 'proprietaire_commune',
		hiddenName: 'proprietaire_code_insee',
		submitValue: true,
		fieldLabel: 'Commune',
		service: 'territoires',
		action: 'getCommunes'
	},
	proprietaire_pays: {
		xtype: 'combomth',
		id: 'proprietaire_pays',
		name: 'proprietaire_pays',
		fieldLabel: 'Pays',
		LS: 'LS_Pays'
	},
	proprietaire_telephone1: {
		xtype: 'ts_textfield',
		id: 'proprietaire_telephone1',
		name: 'proprietaire_telephone1',
		fieldLabel: 'Téléphone 1',
		width: 125,
		maxLength: 800
	},
	proprietaire_telephone2: {
		xtype: 'ts_textfield',
		id: 'proprietaire_telephone2',
		name: 'proprietaire_telephone2',
		fieldLabel: 'Téléphone 2',
		width: 125,
		maxLength: 800
	},
	proprietaire_fax: {
		xtype: 'ts_textfield',
		id: 'proprietaire_fax',
		name: 'proprietaire_fax',
		fieldLabel: 'Fax',
		width: 125,
		maxLength: 800
	},
	proprietaire_site_web: {
		xtype: 'ts_textfield',
		id: 'proprietaire_site_web',
		name: 'proprietaire_site_web',
		fieldLabel: 'Site Web',
		maxLength: 800,
		vtype: 'url'
	},
	proprietaire_email: {
		xtype: 'ts_textfield',
		id: 'proprietaire_email',
		name: 'proprietaire_email',
		fieldLabel: 'Email',
		maxLength: 800,
		vtype: 'email'
	},



	reservation_raison_sociale: {
		xtype: 'ts_textfield',
		id: 'reservation_raison_sociale',
		name: 'reservation_raison_sociale',
		fieldLabel: Ext.ts.Lang.raisonSociale,
		maxLength: 160
	},
	reservation_nom: {
		xtype: 'ts_textfield',
		id: 'reservation_nom',
		name: 'reservation_nom',
		fieldLabel: 'Nom'
	},
	reservation_prenom: {
		xtype: 'ts_textfield',
		id: 'reservation_prenom',
		name: 'reservation_prenom',
		fieldLabel: 'Prénom'
	},
	reservation_adresse1: {
		xtype: 'ts_textfield',
		id: 'reservation_adresse1',
		name: 'reservation_adresse1',
		fieldLabel: 'Adresse 1',
		maxLength: 80
	},
	reservation_adresse2: {
		xtype: 'ts_textfield',
		id: 'reservation_adresse2',
		name: 'reservation_adresse2',
		fieldLabel: 'Adresse 2',
		maxLength: 80
	},
	reservation_adresse3: {
		xtype: 'ts_textfield',
		id: 'reservation_adresse3',
		name: 'reservation_adresse3',
		fieldLabel: 'Adresse 3',
		maxLength: 80
	},
	reservation_code_postal: {
		xtype: 'ts_textfield',
		id: 'reservation_code_postal',
		name: 'reservation_code_postal',
		fieldLabel: 'Code postal',
		width: 125,
		maxLength: 10
	},
	reservation_cedex: {
		xtype: 'ts_textfield',
		id: 'reservation_cedex',
		name: 'reservation_cedex',
		fieldLabel: 'Cedex',
		width: 125,
		maxLength: 10
	},
	reservation_code_insee: {
		xtype: 'autocompletecombocommune',
		id: 'reservation_code_insee',
		name: 'reservation_commune',
		hiddenName: 'reservation_code_insee',
		submitValue: true,
		fieldLabel: 'Commune',
		service: 'territoires',
		action: 'getCommunes'
	},
	reservation_pays: {
		xtype: 'combomth',
		id: 'reservation_pays',
		name: 'reservation_pays',
		fieldLabel: 'Pays',
		LS: 'LS_Pays'
	},
	reservation_telephone1: {
		xtype: 'ts_textfield',
		id: 'reservation_telephone1',
		name: 'reservation_telephone1',
		fieldLabel: 'Téléphone 1',
		width: 125,
		maxLength: 800
	},
	reservation_telephone2: {
		xtype: 'ts_textfield',
		id: 'reservation_telephone2',
		name: 'reservation_telephone2',
		fieldLabel: 'Téléphone 2',
		width: 125,
		maxLength: 800
	},
	reservation_fax: {
		xtype: 'ts_textfield',
		id: 'reservation_fax',
		name: 'reservation_fax',
		fieldLabel: 'Fax',
		width: 125,
		maxLength: 800
	},
	reservation_site_web: {
		xtype: 'ts_textfield',
		id: 'reservation_site_web',
		name: 'reservation_site_web',
		fieldLabel: 'Site Web',
		maxLength: 800,
		vtype: 'url'
	},
	reservation_email: {
		xtype: 'ts_textfield',
		id: 'reservation_email',
		name: 'reservation_email',
		fieldLabel: 'Email',
		maxLength: 800,
		vtype: 'email'
	},



	environnement: {
		xtype: 'objectgrid',
		id: 'environnement',
		tsName: 'environnement',
		height: 220,
		fields: {
			type: {
				xtype: 'combomth',
				fieldLabel: 'Type d\'environnement',
				LS: 'LS_Environnement',
				required: true
			},
			nom: {
				xtype: 'textfield',
				fieldLabel: 'Nom',
				width: 250
			},
			distance: {
				xtype: 'textfield',
				fieldLabel: 'Distance',
				width: 125
			},
			unite_distance: {
				xtype: 'combomth',
				fieldLabel: 'Unité',
				LS: 'LS_DistanceUnite',
				width: 125
			}
		},
		formItems: [{
			fieldset: 'Détail',
			items: ['type', 'nom', 'distance', 'unite_distance']
		}],
		gridColumns: {
			type: {width: 150}
		}
	},
	points_acces: {
		xtype: 'objectgrid',
		id: 'points_acces',
		tsName: 'points_acces',
		height: 220,
		fields: {
			type: {
				xtype: 'combomth',
				fieldLabel: 'Type d\'accès',
				LS: 'LS_Acces',
				required: true
			},
			nom: {
				xtype: 'textfield',
				fieldLabel: 'Nom',
				width: 250
			},
			distance: {
				xtype: 'textfield',
				fieldLabel: 'Distance',
				width: 125
			},
			unite_distance: {
				xtype: 'combomth',
				fieldLabel: 'Unité',
				LS: 'LS_DistanceUnite',
				width: 125
			}
		},
		formItems: [{
			fieldset: 'Détail',
			items: ['type', 'nom', 'distance', 'unite_distance']
		}],
		gridColumns: {
			type: {width: 150}
		}
	},
	accessibilite: {
		xtype: 'listmth',
		id: 'accessibilite',
		tsName: 'accessibilite',
		LS: 'LS_Prestation',
		key: '15.01.*'
	},
	activite: {
		xtype: 'ddgrids',
		id: 'activite',
		tsName: 'activite',
		LS: 'LS_Prestation',
		key: '15.02',
		titleLeft: 'Liste des activités',
		titleRight: 'Activités de l\'établissement',
		useDistance: true
	},
	confort: {
		xtype: 'ddgrids',
		id: 'confort',
		tsName: 'confort',
		LS: 'LS_Prestation',
		key: '15.03',
		titleLeft: 'Liste des conforts',
		titleRight: 'Conforts de l\'établissement'
	},
	equipement: {
		xtype: 'ddgrids',
		id: 'equipement',
		tsName: 'equipement',
		LS: 'LS_Prestation',
		key: '15.05',
		titleLeft: 'Liste des équipements',
		titleRight: 'Equipements de l\'établissement'
	},
	service: {
		xtype: 'ddgrids',
		id: 'service',
		tsName: 'service',
		LS: 'LS_Prestation',
		key: '15.06',
		titleLeft: 'Liste des services',
		titleRight: 'Services de l\'établissement'
	},
	mode_paiement: {
		xtype: 'listmth',
		id: 'mode_paiement',
		tsName: 'mode_paiement',
		LS: 'LS_ModePaiement',
		key: '13.02'
	},
	tarif: {
		xtype: 'objectgrid',
		id: 'tarif',
		tsName: 'tarif',
		height: 550,
		fields: {
			type_tarif: {
				xtype: 'combomth',
				tsName: 'type_tarif',
				fieldLabel: 'Type de tarif',
				LS: 'LS_Tarifs',
				required: true
			},
			tarifstandard: {
				xtype: 'textfield',
				fieldLabel: 'Tarif unique',
				width: 250,
				regex: /^[0-9]{0,}(\.[0-9]{1,})?$/,
				regexText: Ext.ts.Lang.tarifRegex
			},
			tarifmin: {
				xtype: 'textfield',
				fieldLabel: 'Tarif min',
				width: 250,
				regex: /^[0-9]{0,}(\.[0-9]{1,})?$/,
				regexText: Ext.ts.Lang.tarifRegex
			},
			tarifmax: {
				xtype: 'textfield',
				fieldLabel: 'Tarif max',
				width: 250,
				regex: /^[0-9]{0,}(\.[0-9]{1,})?$/,
				regexText: Ext.ts.Lang.tarifRegex
			},
			nom: {
				xtype: 'textfield',
				fieldLabel: 'Nom',
				width: 250
			},
			description: {
				xtype: 'mltextarea',
				fieldLabel: 'Description',
				width: 400
			},
			datedebut: {
				xtype: 'datefield',
				format: 'd/m/Y',
				fieldLabel: 'Du',
				width: 100
			},
			datefin: {
				xtype: 'datefield',
				format: 'd/m/Y',
				fieldLabel: 'Au',
				width: 100
			},
			personnesmin: {
				xtype: 'textfield',
				fieldLabel: 'Min',
				width: 250
			},
			personnesmax: {
				xtype: 'textfield',
				fieldLabel: 'Max',
				width: 250
			}
		},
		formItems: [{
			fieldset: 'Détail',
			items: ['type_tarif', 'tarifstandard', 'tarifmin', 'tarifmax', 'nom', 'description']
		},{
			fieldset: 'Période d\'application du tarif',
			items: ['datedebut', 'datefin']
		},{
			fieldset: 'Nombre de personnes',
			items: ['personnesmin', 'personnesmax']
		}],
		gridColumns: {
			type_tarif: {width: 200},
			tarifstandard: {width: 80},
			tarifmin: {width: 80},
			tarifmax: {width: 80}
		}
	},
	ouverture: {
		xtype: 'objectgrid',
		id: 'ouverture',
		tsName: 'ouverture',
		height: 250,
		fields: {
			type: {
				xtype: 'combomth',
				fieldLabel: 'Type de période',
				LS: 'LS_Periode',
				pop: '09.01.02,09.01.03,09.01.04,09.01.06,09.01.08',
				required: true
			},
			datedebut: {
				xtype: 'datefield',
				fieldLabel: 'Du',
				width: 100,
				format: 'd/m/Y',
				required: true
			},
			datefin: {
				xtype: 'datefield',
				fieldLabel: 'Au',
				width: 100,
				format: 'd/m/Y',
				required: true
			},
			description: {
				xtype: 'textarea',
				fieldLabel: 'Description',
				width: 250
			}
		},
		formItems: [{
			fieldset: 'Détail',
			items: ['type', 'datedebut', 'datefin', 'description']
		}],
		gridColumns: {
			type: {width: 150},
			datedebut: {xtype: 'datecolumn', format: 'd F Y', width: 60},
			datefin: {xtype: 'datecolumn', format: 'd F Y', width: 60}
		}
	},
	periode_ouverture: {
		xtype: 'periodeouverture',
		id: 'periode_ouverture',
		tsName: 'periode_ouverture',
		type: '09.01.05'
	},
	coordonnees_gps: {
		xtype: 'geolocpanel',
		id: 'coordonnees_gps',
		tsName: 'coordonnees_gps'
	},
	photos_fichiers: {
		xtype: 'imgchooser',
		id: 'photos_fichiers',
		tsName: 'photos_fichiers'
	},
	champs_specifiques: {
		xtype: 'fieldfactory',
		id: 'champs_specifiques',
		tsName: 'champs_specifiques'
	}
};
