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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Tourism System v2</title>
		<link rel="shortcut icon" href="images/favicon.png" />
		
		<link rel="stylesheet" type="text/css" href="extjs/css/ext-all.css" />
		<link rel="stylesheet" type="text/css" href="extjs/css/ext-ux.css" />
		<link rel="stylesheet" type="text/css" href="extjs/css/xtheme-<?php echo THEME_DEFAULT; ?>.css" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		
		<script type="text/javascript" src="extjs/ext-base.js"></script>
		<script type="text/javascript" src="extjs/ext-all-debug.js"></script>
		<script type="text/javascript" src="extjs/ext-ux.js"></script>
		<script type="text/javascript" src="extjs/ext-lang-fr.js"></script>
		<script type="text/javascript">
			Ext.ns('Ext.ts');
			
			Ext.ts.params = <?php echo json_encode($_GET); ?>;
		</script>
		<script type="text/javascript" src="include/langs/<?php echo TS_LANG; ?>/general.js"></script>
		<script type="text/javascript" src="extjs/ext-user.js"></script>
		
		<script type="text/javascript">
			Ext.onReady(function() {
				
				var msgMaintenance = new Ext.Panel({
					x: 99,
					y: 152,
					width: 378,
					height: 136,
					border: false,
					bodyCssClass: 'msgMaintenance',
					html: "<?php echo MAINTENANCE_MSG; ?>"
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
						msgMaintenance,
						footerBox
					]
				});
				
				window.show('startfx_bl');
				
			});
		</script>
	</head>
	<body>
		<div id="startfx_bl" />
	</body>
</html>