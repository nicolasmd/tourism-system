/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Client
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		GNU GPLv3 ; see LICENSE.txt
 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
 */



Ext.QuickTips.init();



/**
 * Fonctions permettant d'appeler une méthode en redéfinissant le scope
 */
bindWithArgs = function(objet, methode) {
	var myArgs = arguments;
	return function()
	{
		var args = [];
		for (var i in myArgs)
		{
			if (i > 1)
			{
				args.push(myArgs[i]);
			}
		}
		for (var i in arguments)
		{
			args.push(arguments[i]);
		}
		return methode.apply(objet, args);
	}
};



/**
 * CONNEXIONS AU PROXY
 */

/**
 * Construit l'url permettant d'appeler une action sur le proxy
 * @param {object} myparams : paramètres permettant de construire l'url (service, action, plugin, params)
 * @return {string} : url de l'action sur le proxy
 */
Ext.ts.url = function(myparams) {
	var url = 'application/proxy/'
			+ (Ext.isDefined(myparams.plugin) ? myparams.plugin : 'ts')
			+ '/' + myparams.service + '/' + myparams.action;

	var params = [];
	Ext.iterate(myparams.params, function(name, value) {
		params.push(name + '=' + value);
	});

	if (params.length > 0) {
		url += '?' + params.join('&');
	}

	return url;
};

/**
 * Surcharge de la méthode Ext.Ajax.request
 * Permet de gérer l'url, et les méthodes de callback par défaut (failure, callback)
 * @param {object} myparams : voir configs de Ext.Ajax.request et Ext.ts.url
 */
Ext.ts.request = function(myparams) {
	myparams.method = 'POST';

	myparams.url = Ext.ts.url({
		plugin: myparams.plugin,
		service: myparams.service,
		action: myparams.action
	});

	myparams.params = Ext.apply(myparams.params || {}, {
		method: 'request'
	});

	myparams.autoAbort = true;
	myparams.timeout = 60000;

	if (myparams.waitMsg !== false) {
		var msg	= Ext.isString(myparams.waitMsg) ? myparams.waitMsg : Ext.ts.Lang.waitMsg;
		Ext.Msg.wait(msg, Ext.ts.Lang.waitTitle);

		var hideMsg = function() {
			Ext.Msg.updateProgress(1);
			Ext.Msg.hide();
		};
	}

	if (!Ext.isDefined(myparams.success)) {
		myparams.success = Ext.emptyFn;
	}
	if (myparams.waitMsg !== false) {
		myparams.success = hideMsg.createSequence(myparams.success);
	}

	if (!Ext.isDefined(myparams.failure)) {
		myparams.failure = function(response) {
			if (Ext.isDefined(response.responseText)) {
				var result = Ext.decode(response.responseText);
				if (result.expired === true) {
					return Ext.ts.expiredSession();
				}
				var msg = result.msg || Ext.ts.Lang.failureMsg;
			}
			else if (Ext.isDefined(response.isTimeout) && response.isTimeout) {
				var msg = Ext.ts.Lang.timeoutMsg;
			}
			else {
				return false;
			}

			var result = Ext.decode(response.responseText);
			Ext.Msg.show({
				title: Ext.ts.Lang.failureTitle,
				minWidth: 250,
				msg: msg,
				buttons: Ext.Msg.OK,
				icon: Ext.Msg.ERROR
			});
		}
	}
	if (myparams.waitMsg !== false) {
		myparams.failure = hideMsg.createSequence(myparams.failure);
	}

	Ext.Ajax.request(myparams);
};

/**
 * Surcharge de la méthode Ext.form.BasicForm.submit
 * Permet de gérer l'url, et les méthodes de callback par défaut (failure)
 * @params {object} myparams : voir config de Ext.form.BasicForm.submit et Ext.ts.url
 */
Ext.ts.submit = function(myparams) {
	myparams.url = Ext.ts.url({
		plugin: myparams.plugin,
		service: myparams.service,
		action: myparams.action
	});

	myparams.params = Ext.apply(myparams.params || {}, {
		method: 'submit'
	});

	myparams.timeout = 60;

	if (!Ext.isDefined(myparams.waitMsg)) {
		myparams.waitMsg = Ext.ts.Lang.waitMsg;
	}

	if (!Ext.isDefined(myparams.failure)) {
		myparams.failure = function(form, action) {
			if (Ext.isDefined(action.response.responseText)) {
				var result = Ext.decode(action.response.responseText);
				if (result.expired == true) {
					return Ext.ts.expiredSession();
				}
				var msg = result.msg || Ext.ts.Lang.failureMsg;
			}
			else if (Ext.isDefined(action.response.isTimeout) && action.response.isTimeout) {
				var msg = Ext.ts.Lang.timeoutMsg;
			}
			else {
				return false;
			}

			Ext.Msg.show({
				title: Ext.ts.Lang.failureTitle,
				minWidth: 250,
				msg: msg,
				buttons: Ext.Msg.OK,
				icon: Ext.Msg.ERROR
			});
		}
	}

	if (Ext.isString(myparams.form)) {
		Ext.getCmp(myparams.form).getForm().submit(myparams);
	}
	else if (Ext.isObject(myparams.form)) {
		myparams.form.getForm().submit(myparams);
	}
};

/**
 * Surcharge de self.location utilisant Ext.ts.url pour construire l'url
 * @params {object} myparams : voir config de Ext.ts.url
 */
Ext.ts.location = function(myparams) {
	self.location = Ext.ts.url(myparams);
};

/**
 * Lorsque la session est expirée : affiche la box de login
 */
Ext.ts.expiredSession = function() {
	var boxLogin = Ext.getCmp('boxLogin');

	if (!Ext.isDefined(boxLogin)) {
		boxLogin = new Ext.ts.BoxLogin({
			id: 'boxLogin',
			title: Ext.ts.Lang.expiredMsg
		});
	}

	boxLogin.show();
};



/**
 * STORE
 */

/**
 * Initialisation de la variable indiquant le nombre de store à précharger
 */
Ext.ts.storeToLoad = false;

/**
 * Surcharge de Ext.data.JsonStore
 * Permet de gérer l'url, le retour d'erreur (session expirée), et le préchargement de store
 *
 * Préchargement de store :
 * Le paramètre preLoad à true, permet de préciser que le store doit être préchargé,
 * Dès que tous les stores à précharger sont loadés, une méthode de callback et exécutée :
 * => Ext.ts.onAllStoresLoaded
 */
Ext.ts.JsonStore = Ext.extend(Ext.data.JsonStore, {

	constructor: function(config) {
		config.proxy = new Ext.data.HttpProxy(new Ext.data.Connection({
			url: Ext.ts.url({
				plugin: config.plugin,
				service: config.service,
				action: config.action
			}),
			timeout: 60000,
			autoAbort: true
		}));

		config.root = config.root || 'dataRoot';
		config.totalProperty = config.totalProperty || 'dataCount';

		config.listeners = config.listeners || {};

		Ext.ts.JsonStore.superclass.constructor.call(this, config);

		this.on('exception', function(proxy, type, action, options, response) {
			if (Ext.isDefined(response.responseText)) {
				var result = Ext.decode(response.responseText);
				if (result.expired == true) {
					return Ext.ts.expiredSession();
				}
				var msg = result.msg || Ext.ts.Lang.failureMsg;
			}
			else if (Ext.isDefined(response.isTimeout) && response.isTimeout) {
				var msg = Ext.ts.Lang.timeoutMsg;
			}
			else {
				return false;
			}

			Ext.Msg.show({
				title: Ext.ts.Lang.failureTitle,
				minWidth: 250,
				msg: msg,
				buttons: Ext.Msg.OK,
				icon: Ext.Msg.ERROR
			});
		}, this);

		if (config.preLoad === true) {
			Ext.ts.storeToLoad = true;

			this.on('beforeload', function() {
				if (!Ext.isNumber(Ext.ts.storeToLoad)) {
					Ext.ts.storeToLoad = 0;
				}
				Ext.ts.storeToLoad++;
			}, this, {single: true});

			this.on('load', function() {
				Ext.ts.storeToLoad--;
				if (Ext.ts.storeToLoad == 0) {
					if (Ext.isFunction(Ext.ts.onAllStoresLoaded)) {
						Ext.ts.onAllStoresLoaded();
						Ext.ts.onAllStoresLoaded = false;
					}
				}
			}, this, {single: true});
		}
	}

});
Ext.reg('ts_jsonstore', Ext.ts.JsonStore);



/**
 * SURCHARGES JAVASCRIPT
 */

/**
 * Permet de tronquer la chaîne en fonction de la longueur passée en paramètre
 * @params {int} maxLength : Longueur max de la chaîne
 * @return {string} : La chaîne tronquée
 */
String.prototype.ellipse = function(maxLength) {
	if (this.length > maxLength) {
		return this.substr(0, maxLength-3) + '...';
	}
	return this;
};

/**
 * Permet de mettre en majuscule le premier caractère d'une chaîne
 * @return {string} : La chaîne modifiée
 */
String.prototype.ucfirst = function() {
	var newStr = this.charAt(0).toUpperCase();
	newStr += this.substr(1);
	return newStr;
};



/**
 * SURCHARGES EXT
 * Modifications de valeurs par défaut
 * Ajouts de méthodes
 */

/**
 * Permet d'annuler le rendu d'un composant
 * si la personne connectée ne possède pas les droits suffisants
 */
Ext.override(Ext.Component, {
	listeners: {
		beforerender: function(cmp) {
			if (Ext.isDefined(cmp.tsDroits)) {
				return cmp.tsDroits;
			}
		}
	}
});
Ext.form.Field.prototype.msgTarget = 'side';
Ext.override(Ext.Window, {
	modal: true,
	constrainHeader: true,
	buttonAlign: 'center',
	closeAction: 'close'
});
Ext.override(Ext.grid.GridPanel, {
	loadMask: true,
	viewConfig: {
		emptyText: Ext.ts.Lang.gridEmptyText
	}
});
Ext.override(Ext.grid.ActionColumn, {
	sortable: false
});
Ext.override(Ext.form.DateField, {
	format: 'Y-m-d'
});
if (Ext.form.BasicForm) {
	Ext.form.BasicForm.prototype.waitTitle = Ext.ts.Lang.waitTitle;
}
/**
 * getValueByKey : permet de récupérer le contenu du displayField d'un record via son valueField
 * getDisplayValue : similaire à getValue mais retourne le displayField au lieu du valueField
 */
Ext.override(Ext.form.ComboBox, {
	editable: false,
	getValueByKey: function(key) {
		var r = this.findRecord(this.valueField, key);
		if (Ext.isDefined(r)) {
			return r.data[this.displayField];
		}
		else {
			return key;
		}
	},
	getDisplayValue: function() {
		return this.getValueByKey(this.getValue());
	}
});

Ext.override(Ext.Window, {

	fixedCenter: true,

	scaleAnim: function(w, h, fn, scope) {
		var a = Ext.lib.Anim.motion(this.el, Ext.apply({
			height: {to: h},
			width: {to: w}
		}),.15,'easeNone');

		a.onTween.addListener(function(){
			if (this.fixedCenter) {
				this.center();
			}
			this.syncSize();
			this.syncShadow();
		}, this);

		a.animate();

		a.onComplete.addListener(fn, scope || this);
	}
});



/**
 * COOKIES
 */
Ext.ts.setCookie = function(key, value, timeout) {
	Ext.util.Cookies.set(key, value, new Date().add(Date.SECOND, timeout || 3600));
}
Ext.ts.getCookie = function(key) {
	return Ext.util.Cookies.get(key);
}



/**
 * RACCOURCIS CLAVIER
 */
Ext.ts.collapsibleCmp = [];
new Ext.KeyMap(document, [{
	key: Ext.EventObject.ENTER,
	ctrl: true,
	stopEvent: true,
	fn: function () {
		if (Ext.ts.collapsibleCmp.length > 0) {
			var collapsedState = Ext.getCmp(Ext.ts.collapsibleCmp[0]).collapsed;
			Ext.each(Ext.ts.collapsibleCmp, function(id) {
				if (!Ext.isDefined(Ext.getCmp(id))) {
					return true;
				}
				if (!Ext.isFunction(Ext.getCmp(id).collapse)
					|| !Ext.isFunction(Ext.getCmp(id).expand)) {
					return true;
				}

				if (collapsedState) {
					Ext.getCmp(id).expand();
				}
				else {
					Ext.getCmp(id).collapse();
				}
			});
		}
	}
}]);
new Ext.KeyMap(document, [{
	key: 'f',
	ctrl: true,
	shift: true,
	stopEvent: true,
	fn: function () {
		if (Ext.isDefined(Ext.getCmp('searchFicheField'))) {
			Ext.getCmp('searchFicheField').focus();
		}
	}
}]);



/**
 * MENU
 */

/**
 * Surcharge de Ext.Button pour construire les onglets de navigation
 */
Ext.ts.NavButton = Ext.extend(Ext.Button, {
	template: new Ext.Template(
		'<table id="{4}" cellspacing="0" class="ts-btn x-btn {3}">',
			'<tbody class="{1}">',
				'<tr>',
					'<td class="ts-btn-left"></td>',
					'<td class="ts-btn-center">',
						'<em unselectable="on">',
							'<button class="kwantza x-btn-text {2}" type="{1}">{0}</button>',
						'</em>',
					'</td>',
					'<td class="ts-btn-right"></td>',
				'</tr>',
			'</tbody>',
		'</table>'
	),

	initComponent: function() {
		Ext.ts.NavButton.superclass.initComponent.call(this);
	},

	handler: function() {
		if (Ext.isDefined(this.page)) {
			Ext.ts.open(this.page);
		}
	}

});
Ext.reg('navbutton', Ext.ts.NavButton);

/**
 * Surcharge de Ext.menu.Item pour construire les menu de navigation
 */
Ext.ts.NavItem = Ext.extend(Ext.menu.Item, {

	initComponent: function() {
		Ext.ts.NavItem.superclass.initComponent.call(this);
	},

	handler: function() {
		Ext.ts.open(this.page);
	}

});
Ext.reg('navitem', Ext.ts.NavItem);



/**
 * CONTAINER
 * Surcharge de Ext.Viewport pour construire le container de l'application
 * Contient le bandeau, le menu, le champ de recherche, la barre de titre
 */
Ext.ts.Container = Ext.extend(Ext.Viewport, {
	id: 'viewport',
	layout: 'border',

	initComponent: function() {
		var menu = [{
			xtype: 'spacer',
			width: 20
		}];

		if (hooks = Ext.ts.HookMgr.getMenu()) {
			Ext.each(hooks, function(hook) {
				Ext.ts.Menu.push(hook);
			});
		}

		Ext.each(Ext.ts.Menu, function(item) {
			if (!Ext.isDefined(item.tsDroits) || item.tsDroits === true) {
				if (item.page == Ext.ts.params.pg
					|| (Ext.isDefined(this.selMenu) && this.selMenu == item.itemId))
				{
					item.cls = 'x-btn-selected';
				}
				menu.push(item);
				menu.push({
					xtype: 'spacer',
					width: 5
				});
			}
		}, this);

		menu.push({
			text: Ext.ts.Lang.menuHelp,
			iconCls: 'help',
			handler: function() {
				if (!Ext.getCmp('Ext_ts_Help')) {
					var win = new Ext.ts.Help();
				}
				Ext.getCmp('Ext_ts_Help').show();
				Ext.getCmp('Ext_ts_Help').expand();
			}
		});
		menu.push('->');
		menu.push({
			xtype: 'autocompletecombo',
			id: 'searchFicheField',
			width: 175,
			pageSize: 0,
			listAlign: 'tr-br?',
			listWidth: 'auto',
			emptyText: Ext.ts.Lang.autocompleteFiches,
			store: new Ext.ts.JsonStore({
				action: 'getFiches',
				service: 'fiche',
				root: 'dataRoot',
				totalProperty: 'dataCount',
				fields: [
					{name: 'idFiche', type: 'int'},
					{name: 'raisonSociale', type: 'string'}
				]
			}),
			valueField: 'idFiche',
			displayField: 'raisonSociale',
			autoSelect: false,
			forceSelection: false,
			resizable: false,
			dynamicGetValue: false,
			hideTrigger: !Ext.isDefined(Ext.ts.params.query),
			triggerClass: 'x-form-clear-trigger',
			onTriggerClick: function() {
				this.setValue('');
				this.setHideTrigger(true);
				var store = Ext.StoreMgr.get('storeFiche');
				store.setBaseParam('query', '');
				store.setBaseParam('start', 0);
				store.load();
			},
			value: Ext.isDefined(Ext.ts.params.query)
				? Ext.ts.params.query
				: undefined
			,
			enableKeyEvents: true,
			listeners: {
				keydown: function(field, e) {
					if (e.getKey() == e.RETURN && field.getRawValue() != '') {
						e.stopEvent();
						if (Ext.ts.params.pg == 'fiches') {
							this.setHideTrigger(false);
							var store = Ext.StoreMgr.get('storeFiche');
							store.setBaseParam('query', field.getRawValue());
							store.setBaseParam('start', 0);
							store.load();
						}
						else {
							self.location = 'fiches.php?query='+field.getRawValue();
						}
					}
				},
				select: function(combo, record) {
					Ext.ts.open('fiche', {idFiche: record.data.idFiche});
				}
			}
		});
		menu.push({
			xtype: 'spacer',
			width: 20
		});

		var bandeauCollapsed = (Ext.ts.getCookie('bandeauCollapsed') == 'true');

		var items = this.content || this.items;
		var tools = this.tools || [];
		tools.push({
			id: 'up',
			handler: function(e, tool) {
				if (Ext.getCmp('bandeau').collapsed) {
					Ext.getCmp('bandeau').expand();
					Ext.ts.setCookie('bandeauCollapsed', false);
				}
				else {
					Ext.getCmp('bandeau').collapse();
					Ext.ts.setCookie('bandeauCollapsed', true);
				}
			}
		});

		this.items = [{
			xtype: 'panel',
			id: 'bandeau',
			region: 'north',
			height: 90,
			border: false,
			hideBorders: true,
			split: false,
			header: false,
			collapsible: false,
			collapsed: bandeauCollapsed,
			floatable: false,
			collapseMode: 'mini',
			items: [{
				xtype: 'panel',
				width: '100%',
				height: 52,
				html: '<div id="tsBandeau">'
					+ '<div id="tsBandeauLeft"></div>'
					+ '<div id="tsBandeauRight">'
					+ '<span id="loginUtilisateur">'+Ext.ts.login+'</span> | <a href="deconnexion.php">'+Ext.ts.Lang.menuDeconnexion+'</a>'
					+ '</div>'
					+ '</div>'
			},{
				xtype: 'toolbar',
				cls: 'ts-toolbar',
				defaultType: 'navbutton',
				items: menu
			}],
			listeners: {
				render: function() {
					Ext.ts.collapsibleCmp.push('bandeau');
				},
				expand: function() {
					Ext.getCmp('container').getTool('up').replaceClass('x-tool-down', 'x-tool-up');
				},
				collapse: function() {
					Ext.getCmp('container').getTool('up').replaceClass('x-tool-up', 'x-tool-down');
				}
			}
		},{
			xtype: 'panel',
			id: 'container',
			region: 'center',
			title: this.title,
			hideBorders: true,
			layout: 'fit',
			items: items,
			tools: tools,
			listeners: {
				titlechange: this.updateTitle
			}
		}];

		Ext.ts.Container.superclass.initComponent.call(this);
	},

	updateTitle: function(p, title) {
		document.title = title + ' - ' + Ext.ts.Lang.titleAppli;
	}

});
Ext.reg('ts_container', Ext.ts.Container);



/**
 * BOX LOGIN
 * Surcharge de Ext.Window permettant de construire une box de login
 */
Ext.ts.BoxLogin = Ext.extend(Ext.Window, {
	title: Ext.ts.Lang.authMsg,
	width: 350,
	height: 160,
	border: false,
	modal: true,
	resizable: false,
	closable: false,
	draggable: false,
	layout: 'fit',

	initComponent: function() {
		this.buildLoginField();
		this.buildPassField();
		this.buildButtons();

		this.items = {
			xtype: 'form',
			frame: true,
			border: false,
			bodyStyle: 'padding:10px',
			labelWidth: 120,
			buttonAlign: 'center',
			defaults: {width: 160},
			items: [
				this.loginField,
				this.passField
			],
			buttons: this.formButtons,
			keys: [{
				key: [10,13],
				fn: this.authenticate,
				scope: this
			}]
		};

		Ext.ts.BoxLogin.superclass.initComponent.call(this);

		this.on('show', this.focusField, this);
	},

	buildLoginField: function() {
		this.loginField = new Ext.form.TextField({
			fieldLabel: Ext.ts.Lang.login,
			readOnly: true,
			value: Ext.ts.login
		});
	},

	buildPassField: function() {
		this.passField = new Ext.form.TextField({
			fieldLabel: Ext.ts.Lang.password,
			inputType:  'password'
		});
	},

	buildButtons: function() {
		this.formButtons = [{
			text: Ext.ts.Lang.seConnecter,
			iconCls: 'connect',
			handler: this.authenticate,
			scope: this
		}];
	},

	authenticate: function() {
		Ext.ts.request({
			service: 'identification',
			action: 'identification',
			params: {
				login: this.loginField.getValue(),
				pass: this.passField.getValue()
			},
			waitMsg: Ext.ts.Lang.authWaitMsg,
			success: this.successFn,
			failure: this.failureFn,
			scope: this
		});
	},

	successFn: function(response) {
		this.close();
	},

	failureFn: function(response) {
		var result = Ext.decode(response.responseText);
		Ext.MessageBox.alert(Ext.ts.Lang.failureTitle, result.msg);
	},

	focusField: function() {
		this.passField.focus(false, 500);
	}

});



/**
 * HOOK MANAGER
 */

/**
 * Permet de gérer des hooks :
 * Répertorie des hooks pour les interfaces/menus
 * La méthode findCmp permet d'accéder facilement à des composants
 * dans une HookableInterface dans le but de les enrichir
 */
Ext.ts.HookGroup = Ext.extend(Ext.util.Observable, {
	toExplore: [
		'items',
		'tbar',
		'bbar',
		'menu'
	],

	constructor: function(config) {
		this.hooks = [];
		this.menus = [];

		Ext.apply(this, config);
		Ext.ts.HookGroup.superclass.constructor.call(this);
	},

	add: function(hookName, hookObj) {
		if (!Ext.isDefined(this.hooks[hookName])) {
			this.hooks[hookName] = [];
		}

		this.hooks[hookName].push(hookObj);
	},

	get: function(hookName) {
		return Ext.isDefined(this.hooks[hookName]) && !Ext.isEmpty(this.hooks[hookName])
			? this.hooks[hookName]
			: false;
	},

	addMenu: function(menu) {
		if (Ext.isObject(menu)) {
			this.menus.push(menu);
		}
		else if (Ext.isArray(menu)) {
			Ext.each(menu, function(item) {
				this.menus.push(item);
			}, this);
		}
	},

	getMenu: function() {
		return this.menus;
	},

	findCmp: function(obj, itemId) {
		if (Ext.isObject(obj)) {
			if (obj.itemId == itemId) {
				return obj;
			}
			else {
				var result = false;
				Ext.iterate(obj, function(key, item) {
					if (this.toExplore.indexOf(key) != -1) {
						result = this.findCmp(item, itemId);
						return result === false;
					}
				}, this);
				return result;
			}
			/*else if (Ext.isDefined(obj.items)) {
				return this.findCmp(obj.items, itemId);
			}
			else {
				return false;
			}*/
		}
		else if (Ext.isArray(obj)) {
			var result = false;
			Ext.each(obj, function(item) {
				result = this.findCmp(item, itemId);
				return result === false;
			}, this);
			return result;
		}
		else {
			return false;
		}
	}

});

/**
 * Singleton : HookGroup par défaut
 */
Ext.ts.HookMgr = new Ext.ts.HookGroup();



/**
 * HOOKABLE INTERFACE
 * Extension de Ext.ts.Container
 * Permet de créer une interface qui pourra être enrichie par les plugins
 * en modifiant les items et en ajoutant des méthodes
 */
Ext.ts.HookableInterface = Ext.extend(Ext.ts.Container, {

	initComponent: function() {

		this.buildItems();

		if (hooks = Ext.ts.HookMgr.get(this.id)) {
			Ext.each(hooks, function(hook) {
				var extendItemsFn = Ext.isFunction(hook.extendItems)
					? hook.extendItems
					: Ext.emptyFn;
				Ext.destroyMembers(hook, 'extendItems');

				Ext.apply(this, hook);

				extendItemsFn.call(this);
			}, this);
		}

		Ext.ts.HookableInterface.superclass.initComponent.call(this);
	}

});



/**
 * OPEN INTERFACE
 */

/**
 * Permet de savoir si la touche Ctrl est enfoncée ou pas
 */
Ext.ts.isCtrl = false;
Ext.ts.setCtrl = new Ext.KeyMap(document, {
	key: Ext.EventObject.CTRL,
	fn: function() {
		Ext.ts.isCtrl = true;
		Ext.ts.setCtrl.disable();
	}
}, 'keydown');
Ext.ts.unsetCtrl = new Ext.KeyMap(document, {
	key: Ext.EventObject.CTRL,
	fn: function() {
		Ext.ts.isCtrl = false;
		Ext.ts.setCtrl.enable();
	}
}, 'keyup');

/**
 * Ouvre une page, dans un nouvel onglet si la touche Ctrl est enfoncée,
 * sinon dans la fenêtre courante
 * @params {string} page : la page à ouvrir
 * @params {array} params [optional] : paramètres à passer dans l'url
 * @params {bool} params [optional] : si différent de undefined
 * 		force l'ouverture dans la page courante (false), ou un nouvel onglet (true)
 */
Ext.ts.open = function(page, params, newTab) {
	var arrPath = self.location.pathname.split('/');
	arrPath.pop();
	var basePath = arrPath.join('/');

	var url = 'http://' + self.location.hostname + (!Ext.isEmpty( self.location.port ) ? ':' + self.location.port : '') + basePath + '/' + page + '.php';

	var arrParams = [];
	Ext.iterate(params, function(name, value) {
		arrParams.push(name + (value != '' ? '=' + value : ''));
	});

	if (arrParams.length > 0) {
		url += '?' + arrParams.join('&');
	}

	newTab = Ext.isDefined(newTab) ? newTab : Ext.ts.isCtrl;

	if (newTab) {
		// Fix: on force dans le cas où on relache la touche
		//		après l'ouverture du nouvel onglet
		Ext.ts.isCtrl = false;
		Ext.ts.setCtrl.enable();
		window.open(url);
	}
	else {
		self.location = url;
	}
}



/**
 * LOAD MESSAGE
 * Affiche un message par dessus un masque
 * @params {string} message : le message à afficher
 * @params {mixed} element : element sur lequel on pose le masque, par défaut le body
 * @return {object} : le masque créé
 */
Ext.ts.LoadMask = function (message, element) {
	if (!Ext.isDefined(element)) {
		element = Ext.getBody();
	}

	var myMask = new Ext.LoadMask(element, {
		msg: message,
		msgCls: 'loadingmessage',
		removeMask: true
	});
	myMask.show();

	return myMask;
};



/**
 * NOTIFICATIONS
 * Affiche une notification dans un coin de l'écran
 */
Ext.ts.NotificationMgr = {
	positions: []
};
Ext.ts.Notification = Ext.extend(Ext.Window, {
	width: 250,
	autoHeight: true,
	title: Ext.ts.Lang.confirmTitle,
	iconCls: 'information',
	plain: false,
	modal: false,
	shadow: false,
	closable: true,
	draggable: false,
	resizable: false,
	bodyStyle: 'text-align:left;padding:10px;',
	animateTarget: 'startfx_br',
	animateFrom: 'bottom',
	autoDestroy: true,
	hideDelay : 5000,

	initComponent: function() {
		if(this.autoDestroy) {
			this.task = new Ext.util.DelayedTask(this.hide, this);
		}
		else {
			this.closable = true;
		}
		Ext.ts.Notification.superclass.initComponent.call(this);
	},

	setMessage: function(msg) {
		this.body.update(msg);
	},

	setTitle: function(title, iconCls) {
		Ext.ts.Notification.superclass.setTitle.call(this, title, iconCls||this.iconCls);
	},

	onRender: function(ct, position) {
		Ext.ts.Notification.superclass.onRender.call(this, ct, position);
	},

	onDestroy: function() {
		Ext.ts.NotificationMgr.positions.remove(this.pos);
		Ext.ts.Notification.superclass.onDestroy.call(this);
	},

	afterShow: function(){
		Ext.ts.Notification.superclass.afterShow.call(this);
		this.on('move', function() {
			Ext.ts.NotificationMgr.positions.remove(this.pos);
			if(this.autoDestroy) {
				this.task.cancel();
			}
		}, this);
		if (this.autoDestroy) {
			this.task.delay(this.hideDelay);
		}
	},

	animShow: function(){
		this.pos = 0;
		while(Ext.ts.NotificationMgr.positions.indexOf(this.pos)>-1) {
			if (this.animateFrom === 'top') {
				this.pos--;
			}
			else {
				this.pos++;
			}
		}
		Ext.ts.NotificationMgr.positions.push(this.pos);
		this.el.alignTo(this.animateTarget || document, (this.animateFrom === 'top' ? 'tr-br' : 'br-tr'), [ -1, -1-((this.getSize().height+10)*this.pos) ]);
		this.el.slideIn((this.animateFrom === 'top' ? 't' : 'b'), {
			duration: .7,
			callback: this.afterShow,
			scope: this
		});
	},

	animHide: function(){
		Ext.ts.NotificationMgr.positions.remove(this.pos);
		this.el.ghost((this.animateFrom === 'top' ? 't' : 'b'), {
			duration: 1,
			remove: true
		});
	}

});



/**
 * BORDEREAU COLUMN
 * GridColumn permettant d'afficher le libellé d'un bordereau via son code
 */
Ext.ts.BordereauColumn = Ext.extend(Ext.grid.Column, {
	header: Ext.ts.Lang.bordereau,
	dataIndex: 'bordereau',
	sortable: true,

	initComponent: function() {
		Ext.lrp.BordereauColumn.superclass.initComponent.call(this);
	},

	renderer: function(value) {
		return Ext.ts.bordereaux[value];
	}

});
Ext.grid.Column.types.bordereaucolumn = Ext.ts.BordereauColumn;



/**
 * AUTO SIZE PAGING
 * Extension de Ext.PagingToolbar permettant d'avoir un pageSize dynamique
 * en fonction de la hauteur de la grid
 */
Ext.ts.AutoSizePaging = Ext.extend(Ext.PagingToolbar, {

	// Hauteur d'une ligne
	lineHeight : 24,

	sizeFieldText: Ext.ts.Lang.autosizepagingSizeField,

	// true pour recalculer le pageSize dès que la grid est redimensionnée
	reloadOnResize: false,

	onRenderDelay: 0,

	initComponent: function() {
		// Pour gagner en performance
		this.reloadOnResize = false;

		this.pageSize = 0;
		this.deferredLoad = false;

		var userItems = this.items || [];
		this.items = this.buildSizeField();
		if (userItems.length > 0) {
			this.items.push('-');
			this.items = this.items.concat(userItems);
		}

		Ext.ts.AutoSizePaging.superclass.initComponent.call(this);

		this.store.on('beforeload', this.deferLoad, this, {single: true});
		this.on('render', this.setGridEvent, this, {delay: this.onRenderDelay});
	},

	deferLoad: function() {
		if (this.pageSize == 0) {
			this.deferredLoad = true;
			return false;
		}
	},

	setGridEvent: function() {
		this.grid = this.grid
			|| this.findParentByType('grid')
			|| this.findParentByType('editorgrid');

		this.grid.on('afterlayout', this.initPageSize, this);
	},

	initPageSize: function() {
		var pageSize = this.getPageSize();
		if (pageSize == 0) {
			return;
		}

		this.pageSize = pageSize;
		this.setPageSizeField();
		this.setPageSizeStore();

		this.store.setBaseParam('start', 0);

		if (this.reloadOnResize === true) {
			this.grid.on('resize', this.updatePageSize, this);
		}

		if (this.deferredLoad === true) {
			this.store.load();
		}

		this.un('afterlayout', this.initPageSize, this);
	},

	updatePageSize: function() {
		var oldPageSize = this.pageSize;
		var newPageSize = this.getPageSize();

		var totalCount = this.store.getTotalCount();
		var nbPages = Math.ceil(totalCount / oldPageSize);

		if (newPageSize > 0 && newPageSize != this.pageSize) {
			this.pageSize = newPageSize;
			this.setPageSizeField();
			this.setPageSizeStore();

			if (nbPages > 1 || totalCount > this.pageSize) {
				this.store.reload({
					params: {
						start: this.calculateNewStart(totalCount)
					}
				});
			}
		}
	},

	getPageSize: function() {
		var pageSize = parseInt(this.grid.getView().scroller.getHeight() / this.lineHeight);
		return pageSize >= 0 ? pageSize : 0;
	},

	setPageSizeStore: function() {
		this.store.setBaseParam('limit', this.pageSize);
	},

	setPageSizeField: function() {
		this.sizeField.setValue(this.pageSize);
	},

	calculateNewStart: function(totalCount) {
		return Math.min(Math.round(this.cursor / this.pageSize) * this.pageSize, Math.floor((totalCount - 1) / this.pageSize) * this.pageSize);
	},

	buildSizeField: function() {
		this.sizeField = new Ext.form.NumberField({
			cls: 'x-tbar-page-number',
			allowDecimals: false,
			allowNegative: false,
			enableKeyEvents: true,
			selectOnFocus: true,
			listeners: {
				scope: this,
				keydown: this.onSizeFieldKeyDown,
				blur: this.onSizeFieldBlur
			}
		});

		return ['-', this.sizeFieldText, this.sizeField];
	},

	onSizeFieldBlur : function(field){
		field.setValue(this.pageSize);
	},

	onSizeFieldKeyDown : function(field, e){
		var k = e.getKey();
		var pageSizeTmp = field.getValue();
		var totalCount = this.store.getTotalCount();
		if (k == e.RETURN) {
			e.stopEvent();

			if (pageSizeTmp != this.pageSize) {
				this.pageSize = pageSizeTmp;
				this.store.reload({
					params: {
						start: this.calculateNewStart(totalCount),
						limit: this.pageSize
					}
				});
				this.grid.un('resize', this.updatePageSize, this);
			}
		}else if (k == e.UP || k == e.PAGEUP || k == e.DOWN || k == e.PAGEDOWN) {
			e.stopEvent();
			var increment = e.shiftKey ? 10 : 1;
			if(k == e.DOWN || k == e.PAGEDOWN){
				increment *= -1;
			}
			pageSizeTmp += increment;
			if(pageSizeTmp >= 1) {
				field.setValue(pageSizeTmp);
			}
		}
	}

});
Ext.reg('autosizepaging', Ext.ts.AutoSizePaging);



/**
 * GRID KEY SEARCH
 * Plugin pour grid, permet de rechercher une entrée dans la grid
 * Au fur et à mesure ou l'on saisie une recherche au clavier, la première ligne correspondante est sélectionnée
 * Gère également une action au moment ou l'on presse la touche entrée si une ligne est sélectionnée
 */
Ext.ts.gridKeySearch = Ext.extend(Ext.util.Observable, {
	timeout: 1500,

	constructor: function(config) {
        Ext.apply(this, config);

		Ext.ts.gridKeySearch.superclass.constructor.call(this);
	},

	init: function(grid) {
		this.grid = grid;
		this.currentSearch = '';
		this.lastTimeIndex = false;

		this.grid.on('keydown', this.onKeyDown, this)
	},

	onKeyDown: function(e) {
		if (e.getKey() == e.RETURN && Ext.isFunction(this.enterHandler)) {
			e.stopEvent();
			var selection = this.grid.getSelectionModel().getSelections();
			if (selection.length == 1) {
				this.enterHandler.call(this.scope || this, selection[0]);
			}
		}

		if (this.lastTimeIndex != false) {
			clearTimeout(this.lastTimeIndex);
		}
		this.lastTimeIndex = this.resetSearch.defer(this.timeout, this);

		if (e.isSpecialKey()) {
			return false;
		}

		var store = this.grid.getStore();
		this.currentSearch += String.fromCharCode(e.getKey()).toLowerCase();

		var index = store.find(this.dataIndex, this.currentSearch);
		if (index != -1) {
			this.grid.getSelectionModel().selectRow(index);
			this.grid.getView().focusRow(index);
		}
	},

	resetSearch: function() {
		this.currentSearch = '';
	}

});



/**
 * GRID FILTERS
 */
Ext.ts.GridFilters = Ext.extend(Ext.ux.grid.GridFilters, {
	paramPrefix: 'gridfilters',
	encode: true
});
// Fix : l'ajout de listeners dans la config écrasait le listeners par défaut qui contient keyup
Ext.override(Ext.ux.grid.filter.StringFilter, {
	init : function (config) {
        Ext.applyIf(config, {
            enableKeyEvents: true,
            iconCls: this.iconCls
        });

        this.inputItem = new Ext.form.TextField(config);
		this.inputItem.on('keyup', this.onInputKeyUp, this);
        this.menu.add(this.inputItem);
        this.updateTask = new Ext.util.DelayedTask(this.fireUpdate, this);
    }
});
Ext.preg('gridfilters', Ext.ts.GridFilters);



/**
 * MANAGEMENT WINDOW
 * Extension de Ext.Window, simplifie la création de formulaires
 * permettant de créer/modifier des données sur le serveur
 * L'url de l'action à appeler sur le proxy est gérée par Ext.ts.url
 * Une fois la sauvegarde effectuée, il est possible :
 * 	- d'afficher une notification
 * 	- de reload une grid
 * 	- d'exécuter une fonction de callback
 */
Ext.ts.ManagementWindow = Ext.extend(Ext.Window, {
	width: 450,
	resizable: false,
	layout: 'fit',
	remote: true,
	closeWin: true,
	resetForm: true,
	showMsg: true,

	initComponent: function() {
		var formHeight = this.height;
		this.height = 'auto';

		var formItems = this.items;

		this.form = new Ext.form.FormPanel({
			height: formHeight,
			fileUpload: this.fileUpload || false,
			headers: this.headers || false,
			border: false,
			style: 'padding:10px;background-color:#FFFFFF;',
			labelWidth: this.labelWidth || 120,
			items: formItems,
			keys: [{
				key: [10,13],
				fn: this.submitForm,
				scope: this
			}]
		});

		this.items = this.form;

		this.buttons = [{
			text: Ext.ts.Lang.valider,
			handler: this.submitForm,
			scope: this
		},{
			text: Ext.ts.Lang.annuler,
			handler: function() {
				this.destroy();
			},
			scope: this
		}];

		Ext.ts.ManagementWindow.superclass.initComponent.call(this);

		this.on('show', this.focusFirstChild, this);
	},

	focusFirstChild: function() {
		var fields = this.form.items.items;
		Ext.each(fields, function(field) {
			if (
				Ext.isFunction(field.focus) &&
				field.getXType() != 'hidden' &&
				field.isVisible() == true &&
				field.disabled != true
			) {
				field.focus(false, 500);
				return false;
			}
		});
	},

	submitForm: function() {
		if (this.form.getForm().isValid() === false) {
			return false;
		}

		if (this.remote) {
			Ext.ts.submit({
				form: this.form,
				plugin: this.plugin,
				service: this.service,
				action: this.action,
				params: this.params || {},
				success: this.onSuccessRemote,
				scope: this
			});
		}
		else {
			this.onSuccessLocal();
		}
	},

	onSuccessRemote: function(form, action) {
		if (this.showMsg) {
			var win = new Ext.ts.Notification({
				html: action.result.msg
			});
			win.show();
		}

		if (this.closeWin) {
			this.destroy();
		}
		else if (this.resetForm) {
			this.form.getForm().reset();
			this.focusFirstChild();
		}

		if (Ext.isDefined(this.gridToReload)) {
			this.gridToReload.getStore().reload();
		}

		if (Ext.isDefined(this.treeToReload)) {
			var nodeToReload = Ext.isDefined(this.nodeToReload)
				? this.nodeToReload
				: this.treeToReload.getRootNode();

			this.treeToReload.getLoader().load(nodeToReload, function(node) {
				node.expand();
			});
		}

		if (Ext.isFunction(this.callback)) {
			this.callback.call(this.scope || this, form, action);
		}
	},

	onSuccessLocal: function() {
		if (Ext.isFunction(this.callback)) {
			this.callback.call(this.scope || this, this, this.form.getForm().getValues());
		}

		if (this.closeWin) {
			this.destroy();
		}
		else {
			this.form.getForm().reset();
			this.focusFirstChild();
		}
	}

});
Ext.reg('managementwindow', Ext.ts.ManagementWindow);



/**
 * AUTO COMPLETE COMBO
 * Extension de Ext.form.ComboBox, constitue un champ texte
 * avec une autocomplétion à partir de 4 caractères
 */
Ext.ts.AutoCompleteCombo = Ext.extend(Ext.form.ComboBox, {
	width: 250,
	mode: 'remote',
	pageSize: 10,
	editable: true,
	forceSelection: true,
	hideTrigger: false,
	triggerAction: 'all',
	minChars: 3,
	resizable: true,
	dynamicGetValue: true,
	listEmptyText: Ext.ts.Lang.autocompleteEmptyText,

	initComponent: function() {
		if (Ext.isString(this.store)) {
			this.store = Ext.StoreMgr.get(this.store);
		}

		this.hasLoop = false;

		Ext.ts.AutoCompleteCombo.superclass.initComponent.call(this);
	},

	setNewValue: function(v) {
		this.store.on('load', function(store, records) {
			this.hasLoop = true;
			this.setValue(v);
			this.hasLoop = false;
		}, this, {single: true});

		this.store.load({
			params: {
				queryField: this.valueField,
				query: v
			}
		});
	},

	setValue: function(v) {
		if (this.dynamicGetValue === true
			&& this.hasLoop === false
			&& v != ''
			&& this.store.findExact(this.valueField, v) == -1)
		{
			this.setNewValue(v);
		}
		else {
			Ext.ts.AutoCompleteCombo.superclass.setValue.call(this, v);
		}
	}

});
Ext.reg('autocompletecombo', Ext.ts.AutoCompleteCombo);



/**
 * AUTOCOMPLETECOMBO COMMUNES
 * AutoCompleteCombo des communes
 * différentes valeurs de service et action sont possible :
 * territoires/getCommunes => toutes les communes
 * utilisateurDroitTerritoire/getCommunesUtilisateur => communes via les droits sur territoire
 * groupes/getGroupeCommunes => communes via le groupe
 */
Ext.ts.AutoCompleteComboCommune = Ext.extend(Ext.ts.AutoCompleteCombo, {
	listEmptyText: Ext.ts.Lang.rechercheCommuneEmpty,
	valueField: 'codeInsee',
	displayField: 'libelle',
	tpl: '<tpl for="."><div class="x-combo-list-item">{libelle} ({codePostal})</div></tpl>',

	service: 'territoires',
	action: 'getCommunes',

	initComponent: function() {
		this.store = new Ext.ts.JsonStore({
			service: this.service,
			action: this.action,
			fields: [
				{name: 'codeInsee', type: 'string'},
				{name: 'codePostal', type: 'string'},
				{name: 'libelle', type: 'string'}
			],
			sortInfo: {field: 'libelle', direction: 'ASC'}
		});

		Ext.ts.AutoCompleteComboCommune.superclass.initComponent.call(this);
	}
});
Ext.reg('autocompletecombocommune', Ext.ts.AutoCompleteComboCommune);



/**
 * COMBO BORDEREAUX
 * ComboBox des bordereaux
 * Le mode local récupère tous les bordereaux
 * Le mode remote récupère uniquement les bordereaux sur lesquels
 * l'utilisateur connecté a les droits
 */
Ext.ts.ComboBordereau = Ext.extend(Ext.form.ComboBox, {
	width: 250,
	mode: 'local',
	triggerAction: 'all',
	valueField: 'bordereau',
	displayField: 'libelleBordereau',
	editable: false,

	initComponent: function() {
		if (this.mode == 'remote') {
			this.store = new Ext.ts.JsonStore({
				service: 'utilisateurDroitTerritoire',
				action: 'getBordereauxUtilisateur',
				autoLoad: true,
				root: 'dataRoot',
				dataCount: 'dataCount',
				fields: [
					{name: 'bordereau', type: 'string'},
					{name: 'libelleBordereau', type: 'string'}
				],
				sortInfo: {field: 'libelleBordereau', direction: 'ASC'},
				listeners: {
					load: function() {
						if (!Ext.isEmpty(this.value)) {
							this.setValue(this.value);
						}
					},
					scope: this
				}
			});
			this.mode = 'local';
		}
		else {
			var data = [];

			for (var i in Ext.ts.bordereaux) {
				data.push([i, Ext.ts.bordereaux[i]]);
			}

			this.store = new Ext.data.ArrayStore({
				fields: ['bordereau', 'libelleBordereau'],
				sortInfo: {field: 'libelleBordereau', direction: 'ASC'},
				data: data
			});
		}

		Ext.ts.ComboBordereau.superclass.initComponent.call(this);
	}
});
Ext.reg('combobordereau', Ext.ts.ComboBordereau);



/**
 * LOVCOMBO BORDEREAUX
 * LovCombo des bordereaux
 * Le mode local récupère tous les bordereaux
 * Le mode remote récupère uniquement les bordereaux sur lesquels
 * l'utilisateur connecté a les droits
 */
Ext.ts.LovComboBordereau = Ext.extend(Ext.ux.form.LovCombo, {
	width: 250,
	mode: 'local',
	triggerAction: 'all',
	valueField: 'bordereau',
	displayField: 'libelleBordereau',
	editable: false,

	initComponent: function() {
		if (this.mode == 'remote') {
			this.store = new Ext.ts.JsonStore({
				service: 'utilisateurDroitTerritoire',
				action: 'getBordereauxUtilisateur',
				autoLoad: true,
				root: 'dataRoot',
				dataCount: 'dataCount',
				fields: [
					{name: 'bordereau', type: 'string'},
					{name: 'libelleBordereau', type: 'string'}
				],
				sortInfo: {field: 'libelleBordereau', direction: 'ASC'},
				listeners: {
					load: {
						fn: this.deferredSetValue,
						single: true,
						scope: this
					}
				}
			});
			this.mode = 'local';
		}
		else {
			var data = [];

			for (var i in Ext.ts.bordereaux) {
				data.push([i, Ext.ts.bordereaux[i]]);
			}

			this.store = new Ext.data.ArrayStore({
				fields: ['bordereau', 'libelleBordereau'],
				sortInfo: {field: 'libelleBordereau', direction: 'ASC'},
				data: data
			});
		}

		Ext.ts.LovComboBordereau.superclass.initComponent.call(this);
	},

	deferredSetValue: function() {
		if (!Ext.isEmpty(this.initialConfig.value)
			&& this.initialConfig.value != this.value) {
			this.setValue(this.initialConfig.value);
		}
	}
});
Ext.reg('lovcombobordereau', Ext.ts.LovComboBordereau);



/**
 * SEARCH ENGINE
 * Panel du moteur de recherche
 * Filtre les stores passés en config (storeId ou array de storeId),
 * en fonction des critères renseignés
 */
Ext.ts.SearchLoader = Ext.extend(Ext.ux.tree.XmlTreeLoader, {
	dataUrl: Ext.ts.url({
		action: 'getCriteresSearchEngine',
		service: 'thesaurus'
	}),
	nodeParameter: 'cle',
	processAttributes: function(attr) {
		switch (attr.tagName)
		{
			case 'Item':
				attr.text = attr.value;
				attr.iconCls = 'no-img';
				attr.leaf = true;
				attr.checked = false;
				break;
			case 'Critere':
				attr.id = attr.cle;
				attr.text = attr.title;
				attr.singleClickExpand = true;
				attr.loaded = false;
				break;
			default:
				attr.text = attr.title;
				attr.singleClickExpand = true;
				attr.loaded = true;
				break;
		}
	},
	listeners: {
		beforeload: function(loader, node) {
			if (node.attributes.tagName == 'Critere') {
				loader.baseParams.liste = node.attributes.liste;
			}
		}
	}
});
Ext.ts.SearchEngine = Ext.extend(Ext.Panel, {
	autoScroll: true,
	layout: 'anchor',
	defaults: {
		style: 'margin: 10px;'
	},

	baseUrl: 'include/searchEngine/',

	initComponent: function() {
		// STORES A FILTRER
		this.storeMgr = new Ext.util.MixedCollection();
		if (Ext.isArray(this.store)) {
			Ext.each(this.store, function(item) {
				this.storeMgr.add(item, Ext.StoreMgr.get(item));
			}, this);
		}
		else {
			this.storeMgr.add(this.store, Ext.StoreMgr.get(this.store));
		}

		// COMMUNES
		this.communeCombo = new Ext.ts.AutoCompleteComboCommune({
			emptyText: Ext.ts.Lang.rechercheCommune,
			hiddenName: 'codeInsee',
				service: 'groupes',
			action: 'getGroupeCommunes',
			listeners: {
				select: this.addCommune,
				scope: this
			}
		});
		this.communeGrid = new Ext.grid.GridPanel({
			store: new Ext.ts.JsonStore({
				fields: [
					{name: 'libelle'},
					{name: 'codeInsee'},
					{name: 'codePostal'}
				],
				sortInfo: {field: 'libelle', direction: 'ASC'}
			}),
			columns: [{
				id: 'expand',
				header: Ext.ts.Lang.commune,
				dataIndex: 'libelle',
				sortable: true,
				width: 200
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
			}],
			autoExpandColumn: 'expand',
			tbar: ['->',this.communeCombo],
			listeners: {
				keydown: this.deleteCommune,
				scope: this
			}
		});
		this.communePanel = new Ext.form.FieldSet({
			height: 200,
			anchor: '100%',
			title: Ext.ts.Lang.filterByCommune,
			collapsible: true,
			layout: 'fit',
			items: this.communeGrid,
			listeners: {
				expand: function(cmp) {
					cmp.doLayout(true, true);
				}
			}
		});

		// BORDEREAUX / CRITERES
		this.bordereauCombo = new Ext.ts.ComboBordereau({
			//mode: 'remote',
			anchor: '100%',
			emptyText: Ext.ts.Lang.chooseBordereau,
			style: 'margin-bottom: 5px;',
			listeners: {
				select: this.selectBordereau,
				scope: this
			}
		});
		this.treeCritere = new Ext.tree.TreePanel({
			anchor: '100% -28',
			autoScroll: true,
			useArrows:true,
			rootVisible: false,
			root: new Ext.tree.AsyncTreeNode({
				id: 'root'
			}),
			loader: new Ext.ts.SearchLoader(),
			contextMenu: new Ext.menu.Menu({
				items: [{
					id: 'or',
					text: Ext.ts.Lang.critereOr,
					handler: this.switchOperator,
					scope: this
				},{
					id: 'and',
					text: Ext.ts.Lang.critereAnd,
					handler: this.switchOperator,
					scope: this
				}]
			}),
			tbar: ['->',{
				text: Ext.ts.Lang.annuler,
				handler: this.unSelectAll,
				scope: this
			},/*'-',{
				text: Ext.ts.Lang.addCritere,
				iconCls: 'add',
				handler: this.addCritereAction,
				scope: this
			},*/'-',{
				tooltip: Ext.ts.Lang.expandAll,
				iconCls: 'arrow-down-double',
				handler: function() {
					this.treeCritere.expandAll();
				},
				scope: this
			},'-',{
				tooltip: Ext.ts.Lang.collapseAll,
				iconCls: 'arrow-up-double',
				handler: function() {
					this.treeCritere.collapseAll();
				},
				scope: this
			}],
			listeners: {
				click: function(node, e) {
					if (Ext.isDefined(node.attributes.checked)) {
						node.getUI().toggleCheck();
					}
				},
				contextmenu: function(node, e) {
					if (Ext.isDefined(node.attributes.operator)) {
						node.select();
						var c = node.getOwnerTree().contextMenu;
						c.contextNode = node;
						c.showAt(e.getXY());
					}
				}
			},
			disabled: true
		});
		this.storeCritere = new Ext.data.ArrayStore({
			url: this.baseUrl + 'critereIntDate.php',
			fields: [
				{name: 'typeCritere', type: 'string'},
				{name: 'libelleCritere', type: 'string'},
				{name: 'codeCritere', type: 'string'},
				{name: 'uniteCritere', type: 'string'}
			],
			sortInfo: {field: 'libelleCritere', direction: 'ASC'}
		});
		this.bordereauPanel = new Ext.form.FieldSet({
			anchor: '100% -210',
			title: Ext.ts.Lang.filterByBordereau,
			collapsible: true,
			layout: 'anchor',
			items: [
				this.bordereauCombo,
				this.treeCritere
			],
			listeners: {
				afterlayout: function(cmp) {
					if (cmp.getHeight() < 400) {
						cmp.setHeight(400);
					}
				}
			}
		});

		// TOOLBAR
		this.filterButton = new Ext.Button({
			text: Ext.ts.Lang.appliquer,
			iconCls: 'bullet_go',
			handler: this.filterAction,
			scope: this,
			disabled: true
		});
		var resetButton = new Ext.Button({
			text: Ext.ts.Lang.reinitialiser,
			iconCls: 'arrow_rotate_anticlockwise',
			handler: this.resetAction,
			scope: this
		});
		this.tbar = ['->', resetButton, '-', this.filterButton];

		// CONTAINER
		this.items = [
			this.bordereauPanel,
			this.communePanel
		];

		Ext.ts.SearchEngine.superclass.initComponent.call(this);
	},

	// COMMUNES
	addCommune: function(combo, record) {
		var store = this.communeGrid.getStore();
		if (store.findExact('codeInsee', record.data.codeInsee) == -1) {
			store.add(record);
			store.sort('libelle', 'ASC');
			this.communeCombo.reset();
			this.filterButton.enable();
		}
	},

	deleteCommune: function(e) {
		if (e.getKey() == e.DELETE) {
			e.stopEvent();
			var selection = this.communeGrid.getSelectionModel().getSelections();
			if (selection.length > 0) {
				this.communeGrid.getStore().remove(selection);
			}
		}
	},

	// BORDEREAUX
	onBeforeLoadTree: function(loader, node) {
		// Stop le load du tree la première fois
		return false;
	},

	selectBordereau: function(combo, record) {
		var bordereau = record.data.bordereau;
		if (!Ext.isEmpty(bordereau)) {
			// Load le store de critère en fonction du bordereau
			this.storeCritere.load({
				params: {bordereau: bordereau}
			});

			// Load le tree en fonction du bordereau
			this.treeCritere.getLoader().baseParams = {
				bordereau: bordereau.toLowerCase()
			};
			this.treeCritere.getLoader().load(this.treeCritere.getRootNode());

			// Filtre la grid des fiches en fonction du bordereau
			this.storeMgr.each(function(store) {
				Ext.destroyMembers(store.baseParams, 'bordereau');
				store.setBaseParam('bordereau', bordereau);
				store.reload({params: {start: 0}});
			}, this);

			this.filterButton.enable();
			this.treeCritere.enable();
		}
	},

	switchOperator: function(item) {
		var node = item.parentMenu.contextNode;
		var operator = item.id;
		var iconEl = Ext.get(node.getUI().getIconEl());
		node.attributes.operator = operator;
		if (operator == 'and') {
			iconEl.addClass('folder-and');
		}
		else {
			iconEl.removeClass('folder-and');
		}
	},

	unSelectAll: function() {
		var selection = this.treeCritere.getChecked();
		Ext.each(selection, function(item) {
			item.getUI().toggleCheck(false);
		});
	},

	addCritereAction: function() {
		// CRITERE
		this.critereField = new Ext.form.ComboBox({
			fieldLabel: Ext.ts.Lang.critere,
			width: 250,
			mode: 'local',
			triggerAction: 'all',
			store: this.storeCritere,
			valueField: 'codeCritere',
			displayField: 'libelleCritere',
			editable: false,
			listeners: {
				select: {
					fn: function(combo, record, index) {
						this.fieldsetValeur.removeAll();
						this.typeCritere = record.data.typeCritere;
						this.uniteCritere = Ext.isDefined(record.data.uniteCritere) ? record.data.uniteCritere : null;
						switch (this.typeCritere) {
							case 'int':
								this.fieldsetValeur.add(this.getItemsInt());
							break;
							case 'date':
								this.fieldsetValeur.add(this.getItemsDate());
							break;
						}
						this.fieldsetValeur.expand();
						this.fieldsetValeur.doLayout();
					},
					scope: this
				}
			}
		});
		// FORMULAIRE
		this.fieldsetValeur = new Ext.form.FieldSet({
			xtype: 'fieldset',
			title: Ext.ts.Lang.entrerValeur,
			defaults: {
				width: 250,
				allowBlank: false
			},
			collapsed: true
		});
		this.form = new Ext.FormPanel({
			height: 200,
			border: false,
			style: 'padding:10px;background-color:#FFFFFF;',
			items: [{
				xtype: 'fieldset',
				title: Ext.ts.Lang.selectCritere,
				defaults: {
					allowBlank: false
				},
				items: this.critereField
			},this.fieldsetValeur]
		});
		var win = new Ext.Window({
			title: Ext.ts.Lang.addCritere,
			width: 450,
			height: 'auto',
			resizable: false,
			modal: true,
			closeAction: 'close',
			buttonAlign: 'center',
			layout: 'fit',
			items: this.form,
			buttons: [{
				text: Ext.ts.Lang.valider,
				handler: function() {
					if (this.form.getForm().isValid()) {
						var root = this.treeCritere.getRootNode();
						var parentNode = root.findChild('ref', 'int_date');
						if (Ext.isEmpty(parentNode)) {
							parentNode = root.appendChild(new Ext.tree.TreeNode({
								ref: 'int_date',
								text: Ext.ts.Lang.autres,
								loaded: true
							}));
						}
						switch (this.typeCritere) {
							case 'int':
								parentNode.appendChild(this.buildNodeInt());
							break;
							case 'date':
								parentNode.appendChild(this.buildNodeDate());
							break;
						}
						parentNode.expand();
						win.destroy();
					}
				},
				scope: this
			},{
				text: Ext.ts.Lang.annuler,
				handler: function() {
					win.destroy();
				}
			}]
		});
		win.show();
	},

	filterAction: function() {
		// Récupération des critères
		var selection = this.treeCritere.getChecked();

		var filters = [];
		var filterBool = [];
		Ext.each(selection, function(item) {
			// Critères INT/DATE
			if (item.parentNode.attributes.ref == 'int_date') {
				switch (item.attributes.type) {
					case 'int':
						filters.push(
							item.attributes.key +
							item.attributes.operator +
							item.attributes.value + (
								Ext.isDefined(item.attributes.unit)
								? '-' + item.attributes.unit
								: ''
							)
						);
					break;
					case 'date':
						filters.push(
							item.attributes.key +
							'(' + item.attributes.value1 + ',' +
							item.attributes.value2 + ')'
						);
					break;
				}
			}
			// Critères BOOL
			else {
				var parentId = item.parentNode.id;
				if (!Ext.isDefined(filterBool[parentId])) {
					filterBool[parentId] = [];
				}
				filterBool[parentId].push(item.attributes.key);
			}
		});
		for (var i in filterBool) {
			if (!Ext.isFunction(filterBool[i])) {
				var parentNode = this.treeCritere.getNodeById(i);
				filters.push(filterBool[i].join(parentNode.attributes.operator));
			}
		}

		// Récupération des communes
		var communes = []
		this.communeGrid.getStore().each(function(commune) {
			communes.push(commune.data.codeInsee);
		});

		// Reload la grid en prenant en compte les critères de recherches
		this.storeMgr.each(function(store) {
			if (!Ext.isEmpty(filters)) {
				store.setBaseParam('filters[]', filters);
			}
			else {
				Ext.destroyMembers(store.baseParams, 'filters[]');
			}

			if (!Ext.isEmpty(communes)) {
				store.setBaseParam('communes[]', communes);
			}
			else {
				Ext.destroyMembers(store.baseParams, 'communes[]');
			}

			store.reload({params: {start: 0}});
		}, this);
	},

	resetAction: function() {
		// Vide la grid des communes
		this.communeGrid.getStore().removeAll();

		// Vide l'arbre
		this.treeCritere.getLoader().baseParams = {};
		this.treeCritere.getLoader().load(this.treeCritere.getRootNode());

		// Reset la liste déroulante des bordereaux
		this.bordereauCombo.reset();

		// Reload la grid en supprimant les critères de recherches
		this.storeMgr.each(function(store) {
			Ext.destroyMembers(store.baseParams, 'bordereau', 'communes[]', 'filters[]', 'query');
			store.reload({params: {start: 0}});
		}, this);

		// Désactive les fonctionnalités de recherche
		this.filterButton.disable();
		this.treeCritere.disable();
	},

	/*
	 * CRITERE INT
	 */
	getItemsInt: function() {
		this.operateurField = new Ext.form.ComboBox({
			fieldLabel: Ext.ts.Lang.operateur,
			width: 150,
			mode: 'local',
			triggerAction: 'all',
			store: new Ext.data.ArrayStore({
				data: [
					['<', Ext.ts.Lang.inferieur],
					['<=', Ext.ts.Lang.inferieurEgal],
					['=', Ext.ts.Lang.egal],
					['>=', Ext.ts.Lang.superieurEgal],
					['>', Ext.ts.Lang.superieur]
				],
				fields: [
					{name: 'k_operateur'},
					{name: 'v_operateur'}
				]
			}),
			valueField: 'k_operateur',
			displayField: 'v_operateur',
			editable: false
		});
		this.valeurField = new Ext.form.TextField({
			fieldLabel: Ext.ts.Lang.valeur,
			width: 150
		});
		return [this.operateurField, this.valeurField];
	},

	buildNodeInt: function() {
		return new Ext.tree.TreeNode({
			type: 'int',
			text:
				this.critereField.getDisplayValue() + ' ' +
				this.operateurField.getDisplayValue() + ' ' +
				this.valeurField.getValue(),
			key: this.critereField.getValue(),
			operator: this.operateurField.getValue(),
			value: this.valeurField.getValue(),
			unit: this.uniteCritere,
			leaf: true,
			checked: true
		});
	},

	/*
	 * CRITERE DATE
	 */
	getItemsDate: function() {
		this.valeurField1 = new Ext.form.DateField({
			fieldLabel: Ext.ts.Lang.duUc,
			width: 150,
			format: 'd/m/Y'
		});
		this.valeurField2 = new Ext.form.DateField({
			fieldLabel: Ext.ts.Lang.auUc,
			width: 150,
			format: 'd/m/Y'
		});
		return [this.valeurField1, this.valeurField2];
	},

	buildNodeDate: function() {
		return new Ext.tree.TreeNode({
			type: 'date',
			text:
				this.critereField.getDisplayValue() +
				' ' + Ext.ts.Lang.duLc + ' ' +
				this.valeurField1.getValue().format('d/m/Y') +
				' ' + Ext.ts.Lang.auLc + ' ' +
				this.valeurField2.getValue().format('d/m/Y'),
			key: this.critereField.getValue(),
			value1: this.valeurField1.getValue().format('Y-m-d'),
			value2: this.valeurField2.getValue().format('Y-m-d'),
			leaf: true,
			checked: true
		});
	}

});
Ext.reg('searchengine', Ext.ts.SearchEngine);



/**
 * AIDE
 * Fenêtre pour l'aide
 * Le sommaire est dynamique en fonction des droits
 * de l'utilisateur connecté
 */
Ext.ts.HelpLoader = Ext.extend(Ext.ux.tree.XmlTreeLoader, {
	processAttributes: function(attr) {
		attr.cls = 'helpNodes';
		attr.text = attr.title;
		if (attr.tagName == 'Chapitre') {
			attr.iconCls = 'book';
			attr.loaded = true;
			if (attr.ref == Ext.ts.params.pg) {
				attr.expanded = true;
				attr.display = true;
			}
		}
		else {
			attr.iconCls = 'page_white';
			attr.leaf = true;
		}
	}
});
Ext.ts.Help = Ext.extend(Ext.Window, {
	id: 'Ext_ts_Help',
	title: Ext.ts.Lang.helpTitle,
	width: 800,
	height: 600,
	modal: false,
	maximizable: true,
	collapsible: true,
	layout: 'border',

	initComponent: function() {
		var emptyText = '<div class="helpContent">'+Ext.ts.Lang.helpEmpty+'</div>';

		this.tree = new Ext.tree.TreePanel({
			region: 'west',
			width: 200,
			minWidth: 100,
			maxWidth: 400,
			header: false,
			collapsible: true,
			collapseMode: 'mini',
			split: true,
			margins: '2 0 2 2',
			cmargins: '2 2 2 2',
			autoScroll: true,
			useArrows:true,
			rootVisible: false,
			root: new Ext.tree.AsyncTreeNode(),
			loader: new Ext.ts.HelpLoader({
				dataUrl: Ext.ts.url({
					action: 'getAide',
					service: 'aide'
				}),
				listeners: {
					load: {
						fn: this.displayFirstPage,
						scope: this
					}
				}
			}),
			selModel: new Ext.tree.DefaultSelectionModel({
				listeners: {
					selectionchange: {
						fn: this.displayContent,
						scope: this
					}
				}
			})
		});

		this.panelDetail = new Ext.Panel({
			region: 'center',
			autoScroll: true,
			margins: '2 2 2 0',
			html: emptyText
		});

		this.tbar = [{
			text: Ext.ts.Lang.precedent,
			iconCls: 'resultset_previous',
			handler: this.previousAction,
			scope: this
		},'-',{
			text: Ext.ts.Lang.suivant,
			iconCls: 'resultset_next',
			iconAlign: 'right',
			handler: this.nextAction,
			scope: this
		},'->',{
			text: Ext.ts.Lang.accueil,
			iconCls: 'house',
			handler: this.homeAction,
			scope: this
		}];

		this.items = [this.tree, this.panelDetail];

		Ext.ts.Help.superclass.initComponent.call(this);

		this.on('show', this.adjustWindowSize, this);
	},

	displayFirstPage: function(loader, treeNode, response) {
		Ext.each(treeNode.childNodes, function(node) {
			if (Ext.isDefined(node.attributes.display)) {
				node.ensureVisible(function() {
					this.tree.getSelectionModel().select(node);
				}, this);
			}
		}, this);
	},

	displayContent: function(sm, node){
		var tplPage = new Ext.Template(
			'<div class="helpContent">',
				'<h2>{text}</h2>',
				'<div class="helpPage">{innerText}</div>',
			'</div>'
		);
		tplPage.compile();

		var tplChapitre = new Ext.XTemplate(
			'<div class="helpContent">',
				'<h2>'+Ext.ts.Lang.sommaire+'</h2>',
				'<div class="helpSommaire">',
					'<tpl for=".">',
						'<p><a href="#" id="node_{idNode}">{text}</a><tpl if="resume"> : {resume}</tpl></p>',
					'</tpl>',
				'</div>',
			'</div>'
		);
		tplChapitre.compile();

		var el = this.panelDetail.body;

		if (Ext.isDefined(node)) {
			if (node.leaf) {
				tplPage.overwrite(el, node.attributes);
			}
			else {
				var data = [];
				Ext.each(node.childNodes, function(item) {
					data.push({
						idNode: item.attributes.id,
						text: item.attributes.text,
						resume: item.attributes.resume,
						node: item
					});
				});
				tplChapitre.overwrite(el, data);
				Ext.each(data, function(item) {
					Ext.get('node_'+item.idNode).on('click', function() {
						item.node.ensureVisible(function(node) {
							sm.select(node);
						});
					}, this);
				}, this);
			}
		}
	},

	previousAction: function() {
		this.tree.getSelectionModel().selectPrevious();
	},

	nextAction: function() {
		this.tree.getSelectionModel().selectNext();
	},

	homeAction: function() {
		this.tree.getSelectionModel().select(this.tree.getRootNode());
	},

	adjustWindowSize: function() {
		var bodySize = Ext.getBody().getSize();
		if (bodySize.width < this.getWidth()) {
			this.setWidth(bodySize.width - 10);
		}
		if (bodySize.height < this.getHeight()) {
			this.setHeight(bodySize.height - 10);
		}
		this.center();
	}

});
Ext.reg('ts_help', Ext.ts.Help);



/**
 * CHECKCOLUMN
 * Définition d'un type de column contenant une checkbox
 */
Ext.ts.CheckColumn = Ext.extend(Ext.grid.Column, {
	constructor: function(cfg){
		Ext.ts.CheckColumn.superclass.constructor.call(this, cfg);

		this.on('mousedown', this.toggleCheck, this);
	},

	allowOnlyOneSelected:false,
	paintSelectedBackground:false,

	toggleCheck: function(column, grid, rowIndex, e) {
		if(this.allowOnlyOneSelected)
		{
			for(var i=0;i<grid.store.data.length;i++)
			{
				var aux = grid.store.getAt(i);
				if(i!=this.dataIndex)
				{
					aux.set(this.dataIndex, false);
				}
			}
		}

		var record = grid.store.getAt(rowIndex);
		record.set(this.dataIndex, !record.data[this.dataIndex]);

		if(this.paintSelectedBackground)
		{
			if(record.data[this.dataIndex])
			{
				for( var i=0; i<grid.getColumnModel().getColumnCount(false);i++)
				{
					grid.getView().getCell(rowIndex,i).style.backgroundColor = '#DDD';
				}
			}
			else
			{
				for( var i=0; i<grid.getColumnModel().getColumnCount(false);i++)
				{
					grid.getView().getCell(rowIndex,i).style.backgroundColor = '#FFF';
				}
			}
		}
	},

	renderer : function(v, p, record){
		p.css += ' x-grid3-check-col-td';
		return String.format('<div class="x-grid3-check-col{0}">&#160;</div>', v ? '-on' : '');
	},

	// Deprecate use as a plugin. Remove in 4.0
	init: Ext.emptyFn
});

// register Column xtype
Ext.grid.Column.types.checkcolumn = Ext.ts.CheckColumn;



/**
 * BOUTON GOOGLE
 * Surcharge de Ext.Button permettant de construire un bouton stylisé à la Google
 */
Ext.ts.GoogleButton = Ext.extend(Ext.Button, {
	color: 'blue',
	fontSize: 11,

	initComponent: function(){
		this.customizeTemplate();

		Ext.ts.GoogleButton.superclass.initComponent.call(this);
	},

	/* private */
	customizeTemplate: function() {
		this.template = new Ext.Template(
			'<table id="{4}" cellspacing="0" class="x-btn {3}">',
				'<tbody class="{1}">',
					'<tr>',
						'<td class="' + this.color + '-google-button">',
							'<em class="{2} x-unselectable" unselectable="on">',
								'<button type="{1}" style="font-family: arial; font-size: ' + this.fontSize + 'px; width:"' + this.width  + 'px;" height:"' + this.height  + 'px;">{0}</button>',
							'</em>',
						'</td>',
					'</tr>',
				'</tbody>',
			'</table>'
		);
	}
});
Ext.reg('googlebutton', Ext.ts.GoogleButton);



/**
 * LINKBUTTON
 * Surcharge de Ext.Button permettant de construire un bouton ressemblant à un lien
 */
Ext.ts.LinkButton = Ext.extend(Ext.Button, {
	underline: false,
	fontSize: 11,
	bold: false,

	initComponent: function(){
		this.customizeTemplate();

		Ext.ts.GoogleButton.superclass.initComponent.call(this);
	},

	/* private */
	customizeTemplate: function() {
		var style = 'font-size: ' + this.fontSize + 'px;';

		if(this.underline) {
			style += 'text-decoration: underline;';
		}

		if(this.bold) {
			style += 'font-weight: bold;';
		}

		this.template = new Ext.Template(
			'<table id="{4}" cellspacing="0" class="x-btn {3}">',
				'<tbody class="{1}">',
					'<tr>',
						'<td class="link-button">',
							'<em class="{2} x-unselectable" unselectable="on">',
								'<button type="{1}" style="' + style + '">{0}</button>',
							'</em>',
						'</td>',
					'</tr>',
				'</tbody>',
			'</table>'
		);
	}
});
Ext.reg('linkbutton', Ext.ts.LinkButton);
