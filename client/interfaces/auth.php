<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Tourism System v2</title>
		<link rel="shortcut icon" href="images/<?php echo FAVICON_IMG; ?>" />

		<link rel="stylesheet" type="text/css" href="extjs/css/ext-all.css" />
		<link rel="stylesheet" type="text/css" href="extjs/css/ext-ux.css" />
		<link rel="stylesheet" type="text/css" href="extjs/css/xtheme-<?php echo THEME_DEFAULT; ?>.css" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<style type="text/css">.ext-el-mask { z-index: 20000; }</style>

		<script type="text/javascript" src="extjs/ext-base.js"></script>
		<script type="text/javascript" src="extjs/ext-all-debug.js"></script>
		<!--[if IE 9]><script type="text/javascript" src="extjs/ext-ie9.js"></script><![endif]-->
		<script type="text/javascript" src="extjs/ext-ux.js"></script>
		<script type="text/javascript" src="extjs/ext-lang-fr.js"></script>
		<script type="text/javascript">
			Ext.ns('Ext.ts');

			Ext.ts.params = <?php echo json_encode($_GET); ?>;
		</script>
		<script type="text/javascript" src="include/langs/<?php echo TS_LANG; ?>/general.js"></script>
		<script type="text/javascript" src="extjs/ext-user.js"></script>

		<!--[if lte IE 7]><script type="text/javascript">alert(Ext.ts.Lang.msgIE7); </script><![endif]-->

		<script type="text/javascript">
			Ext.onReady(function() {

				var handlerFn = submitAuth;

				if (!Ext.isEmpty(Ext.util.Cookies.get('rememberAuth'))) {
					var rememberAuth = Ext.decode(Ext.util.Cookies.get('rememberAuth'));
					var loginValue = rememberAuth.login;
					var passValue = rememberAuth.pass;
				}

				var formAuth = new Ext.FormPanel({
					id: 'formAuth',
					x: 99,
					y: 152,
					width: 378,
					height: 136,
					border: false,
					bodyCssClass: 'formAuth',
					labelWidth: 120,
					buttonAlign: 'center',
					defaults: {width: 160},
					defaultType: 'textfield',
					items: [{
						id: 'login',
						<?php if (isAuthorizedIP()) { ?>
						xtype: 'autocompletecombo',
						listWidth: 250,
						pageSize: 0,
						forceSelection: false,
						hideTrigger: true,
						dynamicGetValue: false,
						store: new Ext.ts.JsonStore({
							action: 'getUtilisateurs',
							service: 'identification',
							fields: [
								{name: 'email', type: 'string'},
								{name: 'password', type: 'string'},
								{name: 'typeUtilisateur', type: 'string'}
							]
						}),
						valueField: 'email',
						displayField: 'email',
						hiddenName: 'login',
						tpl: new Ext.XTemplate(
							'<tpl for=".">',
								'<div class="x-combo-list-item',
									'<tpl if="typeUtilisateur == \'superadmin\'"> superAdminStyle</tpl>',
								'">',
									'{email}',
								'</div>',
							'</tpl>'
						),
						listeners: {
							select: function(combo, record) {
								Ext.getCmp('pass').setValue(record.data.password);
								handlerFn();
							}
						},
						<?php } ?>
						fieldLabel: Ext.ts.Lang.login,
						name: 'login',
						value: loginValue
					},{
						id: 'pass',
						fieldLabel: Ext.ts.Lang.password,
						name: 'pass',
						inputType: 'password',
						value: passValue
					},{
						id: 'rememberAuth',
						xtype: 'checkbox',
						fieldLabel: 'Se souvenir de moi',
						checked: Ext.isObject(rememberAuth),
						inputValue: true
					}],
					buttons: [{
						id: 'btn_toggle',
						xtype: 'button',
						text: Ext.ts.Lang.forgottenPass,
						enableToggle: true,
						listeners: {
							toggle: function (item, pressed) {
								if (pressed) {
									Ext.getCmp('pass').disable();
									Ext.getCmp('btn_submit').setText(Ext.ts.Lang.retrievePass);
									handlerFn = forgottenPass;
								}
								else {
									Ext.getCmp('pass').enable();
									Ext.getCmp('btn_submit').setText(Ext.ts.Lang.seConnecter);
									handlerFn = submitAuth;
								}
							}
						},
						pressed: false
					},{
						id: 'btn_submit',
						text: Ext.ts.Lang.seConnecter,
						iconCls: 'connect',
						type: 'submit',
						handler: function() {
							handlerFn();
						}
					}],
					keys: [{
						key: [10,13],
						fn: function() {
							handlerFn();
						}
					}]
				});

				var footerBox = new Ext.Panel({
					x: 0,
					y: 335,
					width: '100%',
					height: 'auto',
					border: false,
					bodyCssClass: 'footerBox',
					html: '<p class="footerBox">' + Ext.ts.Lang.moreInfoAt
						+ ' <a href="http://www.tourism-system.fr">www.tourism-system.fr</a></p>'
				});

				var window = new Ext.Window({
					width: 580,
					height: 390,
					border: false,
					modal: false,
					resizable: false,
					closable: false,
					draggable: false,
					frame: false,
					plain: true,
					layout: 'absolute',
					baseCls: 'boxAuth',
					items: [
						formAuth,
						footerBox
					]
				});

				window.show(
					'startfx_bl',
					function() {
						Ext.getCmp('login').focus(false, 500);
					}
				);

			});

			function submitAuth() {
				Ext.ts.submit({
					form: 'formAuth',
					service: 'identification',
					action: 'identification',
					waitMsg: Ext.ts.Lang.authWaitMsg,
					success: function(form, action) {
						if (Ext.getCmp('rememberAuth').getValue() == true) {
							var rememberAuth = {
								login: Ext.getCmp('login').getValue(),
								pass: Ext.getCmp('pass').getValue()
							};
							Ext.util.Cookies.set('rememberAuth', Ext.encode(rememberAuth));
						}
						else {
							Ext.util.Cookies.clear('rememberAuth');
						}
						Ext.ts.LoadMask(Ext.ts.Lang.authSuccess);
						self.location=self.location.href.replace('#', '');
					},
					failure: function(form, action) {
						Ext.MessageBox.alert(Ext.ts.Lang.failureTitle, action.result.msg);
					}
				});
			}

			function forgottenPass() {
				if (Ext.getCmp('login') != '') {
					Ext.ts.submit({
						form: 'formAuth',
						service: 'identification',
						action: 'forgottenPass',
						waitMsg: Ext.ts.Lang.sendWaitMsg,
						success: function(form, action) {
							var result = action.result;
							Ext.MessageBox.show({
								title: Ext.ts.Lang.confirmTitle,
								minWidth: 250,
								msg: result.msg,
								buttons: Ext.Msg.OK,
								icon: Ext.Msg.INFO,
								fn: function (btn) {
									if (btn == 'ok') {
										Ext.getCmp('btn_toggle').toggle();
									}
								}
							});
						}
					});
				}
				else {
					Ext.MessageBox.alert(Ext.ts.Lang.failureTitle, Ext.ts.Lang.sendPassError);
				}
			}
		</script>
	</head>
	<body>
		<div id="startfx_bl" />
	</body>
</html>