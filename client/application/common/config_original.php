<?php

	// Client
	define('TS_CLIENT_URL', '{TS_CLIENT_URL}');
	define('TS_CLIENT_PATH', '{TS_CLIENT_PATH}');

	// Serveur
	define('TS_BASE_URL', '{TS_BASE_URL}');

	define('TS_WSCHAMP_URL', TS_BASE_URL . 'services/wsdl/champ.wsdl');
	define('TS_WSFICHE_URL', TS_BASE_URL . 'services/wsdl/fiche.wsdl');
	define('TS_WSFICHEFICHIER_URL', TS_BASE_URL . 'services/wsdl/ficheFichier.wsdl');
	define('TS_WSFICHEEXPORT_URL', TS_BASE_URL . 'services/wsdl/ficheExport.wsdl');
	define('TS_WSFICHEVALIDATION_URL', TS_BASE_URL . 'services/wsdl/ficheValidation.wsdl');
	define('TS_WSGROUPE_URL', TS_BASE_URL . 'services/wsdl/groupe.wsdl');
	define('TS_WSIDENTIFICATION_URL', TS_BASE_URL . 'services/wsdl/identification.wsdl');
	define('TS_WSPROFILDROIT_URL', TS_BASE_URL . 'services/wsdl/profilDroit.wsdl');
	define('TS_WSTERRITOIRES_URL', TS_BASE_URL . 'services/wsdl/territoires.wsdl');
	define('TS_WSTHESAURUS_URL', TS_BASE_URL . 'services/wsdl/thesaurus.wsdl');
	define('TS_WSUTILISATEUR_URL', TS_BASE_URL . 'services/wsdl/utilisateur.wsdl');
	define('TS_WSUTILISATEURDROITFICHE_URL', TS_BASE_URL . 'services/wsdl/utilisateurDroitFiche.wsdl');
	define('TS_WSUTILISATEURDROITTERRITOIRE_URL', TS_BASE_URL . 'services/wsdl/utilisateurDroitTerritoire.wsdl');
	define('TS_WSPLUGIN_URL', TS_BASE_URL . 'services/wsdl/plugin.wsdl');

	// Urls des fichiers XML
	define('TS_URL_XML', '{TS_URL_XML}');
	
	// Logger
	define('TS_EMAIL_LOGS', '{TS_EMAIL_LOGS}');

	// Cache
	define('TS_CACHE', 'nocache');
	define('TS_CACHE_PREFIXE', 'cacheClient_');
	define('TS_MEMCACHE_SERVER', 'localhost');
	define('TS_MEMCACHE_PORT', '11211');

	// Répertoires
	define('PLUGINS_PATH', TS_CLIENT_PATH . 'plugins/');
	define('TMP_PATH', '{TMP_PATH}');
	define('TMP_URL', '{TMP_URL}');
	define('LOGS_PATH', '{LOGS_PATH}');

	// Accès Root
	define('LOGIN_ROOT', '{LOGIN_ROOT}');
	define('PASS_ROOT', '{PASS_ROOT}');
	define('SESSION_ID_ROOT', '{SESSION_ID_ROOT}');
	
	// Interfaces
	define('TS_LANG', 'fr');
	define('THEME_DEFAULT', 'gray');
	define('PAGE_DEFAULT', 'fiches');
	define('FAVICON_IMG', 'favicon.png');
	define('BANDEAU_IMG', 'bandeau_left.gif');

	// Taille max des fichiers pour l'upload (en ko)
	define('MAX_SIZE_FILE', 5000000);
	define('MAX_SIZE_IMAGE', 2000000);
	define('MAX_SIZE_AUDIO', 5000000);

	// Maintenance
	define('PRE_MAINTENANCE', false);
	define('PRE_MAINTENANCE_MSG', "");
	define('MAINTENANCE', false);
	define('MAINTENANCE_MSG', "");
	define('MAINTENANCE_AUTHORIZED_IP', false);

	// IPs autorisée
	$GLOBALS['authorizedIP'] = array(
		
	);

	// Bordereaux
	$GLOBALS['bordereaux'] = array(
		'ASC' => 'Activités sportives / culturelles',
		'DEG' => 'Dégustation',
		'FMA' => 'Fêtes et Manifestations',
		'HLO' => 'Hébergement locatif',
		'HOT' => 'Hôtellerie',
		'HPA' => 'Hôtellerie de plein air',
		'LOI' => 'Loisirs',
		'ORG' => 'Organismes',
		'PCU' => 'Patrimoine culturel',
		'PNA' => 'Patrimoine naturel',
		'RES' => 'Restauration',
		'VIL' => 'Village de vacances'
	);

?>