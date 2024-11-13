
<!-- Inclusion des fichiers de plugins -->
<?php tsPlugins::hookInterfaces($_GET['pg']); ?> 
		
		<?php if (PRE_MAINTENANCE === true) { ?> 
		<script type="text/javascript">
			Ext.onReady(function(){
				<?php
					if (!isset(PSession::$SESSION['preMaintenanceWarned'])) {
						PSession::$SESSION['preMaintenanceWarned'] = true;
				?> 
				Ext.Msg.show({
					title: Ext.ts.Lang.attention,
					minWidth: 250,
					msg: "<?php echo PRE_MAINTENANCE_MSG; ?>",
					buttons: Ext.Msg.OK,
					icon: Ext.Msg.WARNING
				});
				<?php } else { ?> 
				var win = new Ext.ts.Notification({
					html: "<?php echo PRE_MAINTENANCE_MSG; ?>",
					title: Ext.ts.Lang.attention,
					iconCls: 'error',
					closable: false,
					autoDestroy: false
				});
				win.show();
				<?php } ?> 
			});
		</script>
		<?php } ?> 
		<?php if (isAuthorizedIP()) { ?> 
		<script type="text/javascript">
			Ext.override(Ext.ts.BoxLogin, {
				buildLoginField: function() {
					this.loginField = new Ext.ts.AutoCompleteCombo({
						fieldLabel: Ext.ts.Lang.login,
						listWidth: 250,
						pageSize: 0,
						forceSelection: false,
						hideTrigger: true,
						dynamicGetValue: false,
						store: new Ext.ts.JsonStore({
							action: 'getUtilisateurs',
							service: 'identification',
							root: 'dataRoot',
							totalProperty: 'dataCount',
							fields: [
								{name: 'email', type: 'string'},
								{name: 'password', type: 'string'},
								{name: 'typeUtilisateur', type: 'string'}
							]
						}),
						valueField: 'email',
						displayField: 'email',
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
							select: {
								fn: function(combo, record) {
									this.passField.setValue(record.data.password);
									this.authenticate();
								},
								scope: this
							}
						}
					});
				},
				
				buildButtons: function() {
					this.formButtons = [{
						text: Ext.ts.Lang.root,
						iconCls: 'user_gray',
						handler: function() {
							this.loginField.setValue('<?php echo LOGIN_ROOT; ?>');
							this.passField.setValue('<?php echo PASS_ROOT; ?>');
							this.authenticate();
						},
						scope: this
					},{
						text: Ext.ts.Lang.seConnecter,
						iconCls: 'connect',
						handler: this.authenticate,
						scope: this
					}];
				},
				
				focusField: function() {
					this.loginField.focus(false, 500);
				}
				
			});
			
			Ext.override(Ext.ts.Container, {
				listeners: {
					afterrender: function() {
						var element = Ext.get('loginUtilisateur');
						element.applyStyles('cursor:pointer;');
						element.on('click', this.showBoxLogin, this);
						
						new Ext.KeyMap(document, [{
							key: 'l',
							ctrl: true,
							stopEvent: true,
							fn: this.showBoxLogin,
							scope: this
						}]);
					}
				},
				
				showBoxLogin: function() {
					this.boxLogin = Ext.getCmp('boxLogin');
					
					if (!Ext.isDefined(this.boxLogin)) {
						this.boxLogin = new Ext.ts.BoxLogin({
							id: 'boxLogin',
							title: Ext.ts.Lang.switchUser,
							modal: false,
							closable: true,
							draggable: true,
							successFn: function() {
								Ext.ts.LoadMask(Ext.ts.Lang.authSuccess);
								self.location=self.location.href.replace('#', '');
							}
						});
					}
					
					this.boxLogin.show('loginUtilisateur');
				}
			});
		</script>
		<?php } ?> 
	</head>
	<body>
		<div id="startfx_br"></div>
	</body>
</html>