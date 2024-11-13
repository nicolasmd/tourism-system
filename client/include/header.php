<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Tourism System v2</title>
		<link rel="shortcut icon" href="images/<?php echo FAVICON_IMG; ?>" />
		
		<link rel="search" type="application/opensearchdescription+xml" title="Tourism System" href="include/opensearch.php" />
		
		<link rel="stylesheet" type="text/css" href="extjs/css/ext-all.css" />
		<link rel="stylesheet" type="text/css" href="extjs/css/ext-ux.css" />
		<link rel="stylesheet" type="text/css" href="extjs/css/xtheme-<?php echo THEME_DEFAULT; ?>.css" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<style type="text/css">
			#tsBandeauLeft { background-image: url('images/<?php echo BANDEAU_IMG; ?>'); }
		</style>
		
		<script type="text/javascript" src="extjs/ext-base.js"></script>
		<script type="text/javascript" src="extjs/ext-all-debug.js"></script>
		<script type="text/javascript" src="extjs/ext-ux.js"></script>
		<script type="text/javascript" src="extjs/ext-lang-fr.js"></script>
		<script type="text/javascript">
			Ext.ns('Ext.ts');
		</script>
		<script type="text/javascript" src="include/langs/general_<?php echo TS_LANG; ?>.js"></script>
		<script type="text/javascript" src="include/langs/<?php echo $_GET['pg']; ?>_<?php echo TS_LANG; ?>.js"></script>
		<script type="text/javascript">
			<?php if (PSession::$SESSION['tsSessionId'] != SESSION_ID_ROOT) { ?>
			Ext.ts.idUtilisateur = <?php echo PSession::$SESSION['idUtilisateur']; ?>;
			Ext.ts.idGroupe = <?php echo PSession::$SESSION['idGroupe']; ?>;
			<?php } ?> 
			Ext.ts.typeUtilisateur = '<?php echo PSession::$SESSION['typeUtilisateur']; ?>';
			Ext.ts.login = '<?php echo PSession::$SESSION['email']; ?>';
			Ext.ts.bordereaux = <?php echo json_encode($GLOBALS['bordereaux']); ?>;
			
			Ext.ts.params = <?php echo json_encode($_GET); ?>;
			
			Ext.ts.Menu = [{
				itemId: 'fiches',
				text: Ext.ts.Lang.menuFiches,
				iconCls: 'page_white_text',
				page: 'fiches',
				tsDroits: <?php tsDroits::printDroit('MENU_FICHE'); ?> 
			},{
				itemId: 'utilisateurs',
				text: Ext.ts.Lang.menuUtilisateurs,
				iconCls: 'user',
				page: 'utilisateurs',
				tsDroits: <?php tsDroits::printDroit('MENU_UTILISATEUR'); ?> 
			},{
				text: Ext.ts.Lang.menuProfils,
				iconCls: 'report_user',
				page: 'profils',
				tsDroits: <?php  tsDroits::printDroit('MENU_PROFIL'); ?> 
			},{
				text: Ext.ts.Lang.menuGroupes,
				iconCls: 'group',
				page: 'groupes',
				tsDroits: <?php  tsDroits::printDroit('MENU_GROUPE'); ?> 
			},{
				text: Ext.ts.Lang.menuTerritoires,
				iconCls: 'map',
				page: 'territoires',
				tsDroits: <?php  tsDroits::printDroit('MENU_TERRITOIRE'); ?> 
			},{
				text: Ext.ts.Lang.menuChamps,
				iconCls: 'application_form',
				page: 'champs',
				tsDroits: <?php  tsDroits::printDroit('MENU_CHAMP'); ?> 
			},{
				text: Ext.ts.Lang.menuThesaurii,
				iconCls: 'book_addresses',
				page: 'thesaurii',
				tsDroits: <?php  tsDroits::printDroit('MENU_THESAURUS'); ?> 
			},{
				text: Ext.ts.Lang.menuPlugins,
				iconCls: 'plugin',
				page: 'plugins',
				tsDroits: <?php  tsDroits::printDroit('MENU_PLUGIN'); ?> 
			}];
		</script>
		<script type="text/javascript" src="extjs/ext-user.js"></script>
