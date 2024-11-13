<?php
	header("Content-type: text/html; charset=UTF-8;");
	$error = false;
?> 

<!DOCTYPE>
<html>
	<head>
		<title>Bienvenue dans l'interface de configuration</title>
		<style type="text/css">
			body
			{
				font-family: arial;
				font-size: 11px;
				padding: 10px;
			}

			h1
			{	
				width: 630px;
				margin: 0 auto;
				text-indent: 230px;	
				font-size: 15px;
				padding: 20px 0;
			}

			p
			{
				margin: auto;
				margin-bottom: 8px;
				width:630px;
				position: relative;
			}

			label
			{
				width : 230px;
				font-size: 11px;
				padding: 6px 0;
				float: left;
			}
			
			input
			{
				width: 400px;
			}

			.header
			{
				text-align: center;
				margin-bottom: 20px;
			}

			.bouton
			{
				padding-top: 20px;
				text-align: center;
			}

			.bouton input
			{
				width: auto;
			}

			.error
			{
				width: 250px;
				position: absolute;
				top: 4px;
				left: 630px;
				color: red;
				font-size: 11px;
				padding-left: 10px;
			}

		</style>
	</head> 
	<body>
		<form method="POST" action="">
			<p class="header"><img src="images/logo.png" alt="Tourism System" /></p>
			
			<h1>Configuration de Tourism System Client</h1>
			<p><label for="CLIENT_URL">URL du Client : </label><input type="text" id="CLIENT_URL" name="CLIENT_URL" placeholder="http://www.url-to-your-client.fr/" <?php echo isset($_POST['CLIENT_URL']) ? 'value="' . $_POST['CLIENT_URL'] . '"' : '';?> />
<?php 
			if (isset($_POST['CLIENT_URL']) && empty($_POST['CLIENT_URL']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}

?>
			</p>

			<p><label for="CLIENT_PATH">Répertoire du Client : </label><input type="text" id ="CLIENT_PATH" name="CLIENT_PATH" placeholder="/path/to/your/client/" <?php echo isset($_POST['CLIENT_PATH']) ? 'value="' . $_POST['CLIENT_PATH'] . '"' : ''; ?> />
<?php
			if (isset($_POST['CLIENT_PATH']))
			{
				if (empty($_POST['CLIENT_PATH']))
				{
					$error = true;
?>
					<span class="error">Vous devez renseigner ce champ</span>
<?php
				}
				elseif (!is_dir($_POST['CLIENT_PATH']) || !is_readable($_POST['CLIENT_PATH']))
				{
					$error = true;
?>
					<span class="error">Ce chemin est inaccessible</span>
<?php	
				}
			}
?>
			</p>

			<p><label for="EMAIL_LOGS_CLIENT">Adresse mail des logs : </label><input type="text" id ="EMAIL_LOGS_CLIENT" name="EMAIL_LOGS_CLIENT" placeholder="admin@domain.fr" <?php echo isset($_POST['EMAIL_LOGS_CLIENT']) ? 'value="' . $_POST['EMAIL_LOGS_CLIENT'] . '"' : ''; ?> />
<?php
			
			if (isset($_POST['EMAIL_LOGS_CLIENT']) && empty($_POST['EMAIL_LOGS_CLIENT']))
			{
				$error=true;
?>
				<span  class="error"> Vous devez renseigner ce champ </span>
<?php
						
			}
			
?>
			</p>

			<p><label for="LOGS_PATH_CLIENT">Répertoire des logs du client : </label> <input type="text" id="LOGS_PATH_CLIENT" name="LOGS_PATH_CLIENT" placeholder="/path/to/your/logs/"<?php echo isset($_POST['LOGS_PATH_CLIENT']) ? 'value="' . $_POST['LOGS_PATH_CLIENT'] . '"' : '';?> />
<?php 		
			if(isset($_POST['LOGS_PATH_CLIENT']) && empty($_POST['LOGS_PATH_CLIENT']))
			{
				$error=true;
?>
				<span class="error"> Vous devez renseignez ce champ<span/>
<?php
			}
			
?>
			</p>

			<p><label for="TMP_URL">URL des fichiers temporaires : </label> <input type="text"  id= "TMP_URL" name="TMP_URL" placeholder="http://url-to-your-tmp-files.fr/"<?php echo isset($_POST['TMP_URL']) ? 'value="' . $_POST['TMP_URL'] . '"' : ''; ?> />
<?php
			if (isset($_POST['TMP_URL']) && empty($_POST['TMP_URL']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
					
			}
			
?>
			</p>
			
			<p><label for="TMP_PATH">Répertoire des fichiers temporaires : </label> <input type="text"  id= "TMP_PATH" name="TMP_PATH" placeholder="/path/to/your/tmp/files/" <?php echo isset($_POST['TMP_PATH']) ? 'value="' . $_POST['TMP_PATH'] . '"' : ''; ?> />					<!-- repertoire vers les tmp -->
<?php
			if (isset($_POST['TMP_PATH']) && empty($_POST['TMP_PATH']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
			
?>
			</p>

			<h1>Configuration de Tourism System Serveur</h1>
			
			<p><label for="BASE_URL">URL du Serveur : </label> <input type="text" id="BASE_URL" name="BASE_URL" placeholder="http://www.url-to-your-server.fr/"<?php echo isset($_POST['BASE_URL']) ? 'value="' . $_POST['BASE_URL'] . '"' : '';?> />
<?php
			if (isset($_POST['BASE_URL']) && empty($_POST['BASE_URL']))
			{
				$error=true;
?>
			<span class="error"> Vous devez renseignez ce champ<span/>
<?php
			}
			
?>
			</p>

			<p><label for="BASE_PATH">Répertoire du Serveur :</label> <input type="text" id= "BASE_PATH" name="BASE_PATH"  placeholder="/path/to/your/serveur/"  <?php echo isset($_POST['BASE_PATH']) ? 'value="' . $_POST['BASE_PATH'] . '"' : ''; ?> />
<?php
			if (isset($_POST['BASE_PATH']))
			{
				if (empty($_POST['BASE_PATH']))
				{
					$error = true;
?>
					<span class="error">Vous devez renseigner ce champ</span>
<?php
				}
				elseif (!is_dir($_POST['BASE_PATH']) || !is_readable($_POST['BASE_PATH']))
				{
					$error = true;
?>
					<span class="error">Ce chemin est inaccessible</span>
<?php	
				}
			}
?>
			</p>

			<p><label for="EMAIL_LOGS_SERVEUR">Adresse mail des logs : </label> <input type="text"  id= "EMAIL_LOGS_SERVEUR" name="EMAIL_LOGS_SERVEUR" placeholder="admin@domain.fr" <?php echo isset($_POST['EMAIL_LOGS_SERVEUR']) ? 'value="' . $_POST['EMAIL_LOGS_SERVEUR'] . '"' : ''; ?> />
<?php
			if (isset($_POST['EMAIL_LOGS_SERVEUR']) && empty($_POST['EMAIL_LOGS_SERVEUR']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php	
			}
					
?>
			</p>

			<p><label for="LOGS_PATH_SERVEUR">Répertoire des logs du serveur : </label> <input type="text" id= "LOGS_PATH_SERVEUR"  name="LOGS_PATH_SERVEUR" placeholder="/path/to/your/logs/"<?php echo isset($_POST['LOGS_PATH_SERVEUR']) ? 'value="' . $_POST['LOGS_PATH_SERVEUR'] . '"' : ''; ?> />
<?php
			if (isset($_POST['LOGS_PATH_SERVEUR']) && empty($_POST['LOGS_PATH_SERVEUR']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
			
?>
			</p>

			<p><label for="TS_URL_TMP">URL des fichiers temporaires : </label> <input type="text"  id= "TS_URL_TMP" name="TS_URL_TMP" placeholder="http://url-to-your-tmp-files.fr/"<?php echo isset($_POST['TMP_URL']) ? 'value="' . $_POST['TMP_URL'] . '"' : ''; ?> />
<?php
			if (isset($_POST['TS_URL_TMP']) && empty($_POST['TS_URL_TMP']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
					
			}
			
?>
			</p>
			
			<p><label for="TS_PATH_TMP">Répertoire des fichiers temporaires : </label> <input type="text"  id= "TS_PATH_TMP" name="TS_PATH_TMP" placeholder="/path/to/your/tmp/files/" <?php echo isset($_POST['TMP_PATH']) ? 'value="' . $_POST['TMP_PATH'] . '"' : ''; ?> />					<!-- repertoire vers les tmp -->
<?php
			if (isset($_POST['TS_PATH_TMP']) && empty($_POST['TS_PATH_TMP']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
			
?>
			</p>	

			<p><label for="URL_XML">URL vers les XML :</label><input type="text"  id= "URL_XML" name="URL_XML" placeholder="http://www.url-to-your-xml.fr/" <?php echo isset($_POST['URL_XML']) ? 'value="' . $_POST['URL_XML'] . '"' : ''; ?>  />
<?php
			if (isset($_POST['URL_XML']) && empty($_POST['URL_XML']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
			
?>
			</p>
			<p><label for="PATH_XML">Répertoire des XML :</label><input type="text"  id= "PATH_XML" name="PATH_XML" placeholder="/path/to/your/xml/" <?php echo isset($_POST['PATH_XML']) ? 'value="' . $_POST['PATH_XML'] . '"' : ''; ?> />
<?php
			if (isset($_POST['PATH_XML']) && empty($_POST['PATH_XML']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
		
?>
			</p>

			<p><label for="URL_MEDIAS">URL des médias :</label> <input type="text"  id= "URL_MEDIAS" name="URL_MEDIAS" placeholder="http://www.url-to-your-medias.fr/"<?php echo isset($_POST['URL_MEDIAS']) ? 'value="' . $_POST['URL_MEDIAS'] . '"' : ''; ?> />
<?php
			if (isset($_POST['URL_MEDIAS']) && empty($_POST['URL_MEDIAS']))
			{
				$error= true;
?>
			<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
?>
			</p>

			<p><label for="PATH_MEDIAS">Répertoire des médias :</label> <input type="text"  id= "PATH_MEDIAS" name="PATH_MEDIAS" placeholder="/path/to/your/medias/"<?php echo isset($_POST['PATH_MEDIAS']) ? 'value="' . $_POST['PATH_MEDIAS'] . '"' : ''; ?> />
<?php
			if (isset($_POST['PATH_MEDIAS']) && empty($_POST['PATH_MEDIAS']))
			{
				$error= true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
		
?>

			<!-- Accès Root -->
			<h1>Accès administrateur</h1>

			<p><label for="LOGIN_ROOT">Login Root :</label> <input type="text"  id= "LOGIN_ROOT" name="LOGIN_ROOT" placeholder="root" <?php echo isset($_POST['LOGIN_ROOT']) ? 'value="' . $_POST['LOGIN_ROOT'] . '"' : ''; ?> />
<?php
			if (isset($_POST['LOGIN_ROOT']) && empty($_POST['LOGIN_ROOT']))
			{
				$error = true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
		
?>
			</p>
			
			<p><label for="PASS_ROOT">Mot de passe Root :</label> <input type="text"  id= "PASS_ROOT" name="PASS_ROOT" placeholder="password" <?php echo isset($_POST['PASS_ROOT']) ? 'value="' . $_POST['PASS_ROOT'] . '"' : ''; ?>/>
<?php
			if (isset($_POST['PASS_ROOT']) && empty($_POST['PASS_ROOT']))
			{
				$error= true;
?>
				<span class="error">Vous devez renseigner ce champ<span>
<?php
			}
		
?>
			</p>

			<p><label for="SESSION_ID_ROOT">Identifiant de session Root :</label> <input type="text"  id= "SESSION_ID_ROOT" name="SESSION_ID_ROOT" placeholder="sessionidroot" <?php echo isset($_POST['SESSION_ID_ROOT']) ? 'value="' . $_POST['SESSION_ID_ROOT'] . '"' : ''; ?>/>
<?php
			if (isset($_POST['SESSION_ID_ROOT']) && empty($_POST['SESSION_ID_ROOT']))
			{
				$error= true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
	
?>
			</p>

			 <!-- Base de données -->
			<h1>Configuration de la BD</h1>

			<p><label for="BDD_SERVER">Hôte : </label> <input type="text"  id= "BDD_SERVER" name="BDD_SERVER"  placeholder="localhost" <?php echo isset($_POST['BDD_SERVER']) ? 'value="' . $_POST['BDD_SERVER'] . '"' : ''; ?>/>
<?php
			if (isset($_POST['BDD_SERVER']) && empty($_POST['BDD_SERVER']))
			{
				$error= true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}

?>
			</p>
			<p><label for="BDD_NAME">Nom de la BD : </label> <input type="text" id= "BDD_NAME"  name="BDD_NAME"  placeholder="mydb" <?php echo isset($_POST['BDD_NAME']) ? 'value="' . $_POST['BDD_NAME'] . '"' : ''; ?>/> 
<?php
			if (isset($_POST['BDD_NAME']) && empty($_POST['BDD_NAME']))
			{
				$error= true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
	
?>
			</p>
			<p><label for="BDD_USER">Utilisateur : </label> <input type="text" id= "BDD_USER"  name="BDD_USER"  placeholder="admin" <?php echo isset($_POST['BDD_USER']) ? 'value="' . $_POST['BDD_USER'] . '"' : ''; ?>/>			
<?php
			if (isset($_POST['BDD_USER']) && empty($_POST['BDD_USER']))
			{
				$error= true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}
?>
			</p>
			<p><label for="BDD_PASSWORD">Mot de passe : </label> <input type="text"  id= "BDD_PASSWORD" name="BDD_PASSWORD"  placeholder="password"<?php echo isset($_POST['BDD_PASSWORD']) ? 'value="' . $_POST['BDD_PASSWORD'] . '"' : ''; ?> />
<?php
			if (isset($_POST['BDD_PASSWORD']) && empty($_POST['BDD_PASSWORD']))
			{
				$error= true;
?>
				<span class="error">Vous devez renseigner ce champ</span>
<?php
			}

			if(isset($_POST['BDD_SERVER'], $_POST['BDD_NAME'], $_POST['BDD_USER'], $_POST['BDD_PASSWORD']) && !empty($_POST['BDD_SERVER']) && !empty($_POST['BDD_NAME']) && !empty($_POST['BDD_USER']) && !empty($_POST['BDD_PASSWORD'])) 
			{
				$db = @mysql_connect($_POST['BDD_SERVER'], $_POST['BDD_USER'], $_POST['BDD_PASSWORD']);
				
				if ($db == 0)
				{
					$error = true;
?>
					<span class="error">Impossible de se connecter au serveur de base de données</span>
<?php
				}
				else
				{
		 
					$result = mysql_select_db($_POST['BDD_NAME']);
				
					if ($result == 0)
  					{
  						$error = true;
?>
						<span class="error">La base de donnée n'existe pas</span>
<?php
  					}
				}
			}
?>
			</p>

			<h1>Configuration de la machine</h1>
			<p>Les fichiers htaccess doivent être pris en compte (directive AllowOverride à All)
<?php
				if (isset($_POST['CLIENT_URL']) && !empty($_POST['CLIENT_URL']))
				{
					$htacces = @file_get_contents($_POST['CLIENT_URL'] . '/application/proxy/ts/identification/identification');
					
					// on vérifie le htaccess
					if ($htacces == false)
					{
						$error = true;
						echo '<span class="error">Les htaccess ne sont pas pris en compte</span>';
					}
				}
?>
			</p>

			<p class="bouton"><input type="submit" name="Submit" value="Installer" /></p> <!-- bouton de validation -->
			
		</form>		
	</body>
</html>

<?php
//-------------------------------------------------------------------------------------------------

	if (isset($_POST['Submit']) && $error === false)
	{
		// Récupérer les valeurs saisies
		$CLIENT_URL = $_POST["CLIENT_URL"];
		$CLIENT_PATH = $_POST['CLIENT_PATH'];
		$EMAIL_LOGS_CLIENT = $_POST['EMAIL_LOGS_CLIENT'];
		$LOGS_PATH_CLIENT = $_POST['LOGS_PATH_CLIENT'];

		$BASE_URL = $_POST['BASE_URL'];
		$BASE_PATH = $_POST['BASE_PATH'];
		$URL_XML = $_POST['URL_XML'];
		$PATH_XML = $_POST['PATH_XML'];
		
		$EMAIL_LOGS_SERVEUR = $_POST['EMAIL_LOGS_SERVEUR'];
		$LOGS_PATH_SERVEUR = $_POST['LOGS_PATH_SERVEUR'];
		
		$TMP_PATH = $_POST['TMP_PATH'];
		$TMP_URL = $_POST['TMP_URL'];
		$TS_PATH_TMP = $_POST['TS_PATH_TMP'];
		$TS_URL_TMP = $_POST['TS_URL_TMP'];
		$URL_MEDIAS = $_POST['URL_MEDIAS'];
		$PATH_MEDIAS = $_POST['PATH_MEDIAS'];
		
		$LOGIN_ROOT = $_POST['LOGIN_ROOT'];
		$PASS_ROOT = $_POST['PASS_ROOT'];
		$SESSION_ID_ROOT = $_POST['SESSION_ID_ROOT'];
		
		$BDD_SERVER = $_POST['BDD_SERVER'];
		$BDD_NAME = $_POST['BDD_NAME']; 
		$BDD_USER = $_POST['BDD_USER']; 
		$BDD_PASSWORD = $_POST['BDD_PASSWORD']; 

//-------------------------------------------------------------------------------------------------

		$dumpsSql = array(
			'structure.sql',
			'sitThesaurus.sql',
			'sitEntreesThesaurus.sql',
			'sitCommune.sql',
			'sitChamp.sql'
		);

		$pathSql = $_POST['BASE_PATH'] . '_sql/';
		
		foreach ($dumpsSql as $dump)
		{
			shell_exec("mysql -h $BDD_SERVER -u $BDD_USER --password=$BDD_PASSWORD $BDD_NAME < $pathSql$dump");
		}

//-------------------------------------------------------------------------------------------

		// création répertoires xml et medias

		if(!is_dir($PATH_MEDIAS))
		{
			mkdir($PATH_MEDIAS, 0777);
		}

		if(!is_dir($PATH_XML))
		{
			mkdir($PATH_XML, 0777);
		}

		if(!is_dir($TMP_PATH))
		{
			mkdir($TMP_PATH, 0777);
		}

		if(!is_dir($TS_PATH_TMP))
		{
			mkdir($TS_PATH_TMP, 0777);
		}


		if(!is_dir($LOGS_PATH_CLIENT))
		{
			mkdir($LOGS_PATH_CLIENT, 0777);
		}
		

		if(!is_dir($LOGS_PATH_SERVEUR))
		{
			mkdir($LOGS_PATH_SERVEUR, 0777);
		}

//-------------------------------------------------------------------------------------------------

		// ouverture en lecture et modification 
		$handle = fopen($CLIENT_PATH."/application/common/config_original.php", "c+");
		$contenu = fread($handle,filesize($CLIENT_PATH."/application/common/config_original.php"));
		fclose($handle);

		$handle = fopen($CLIENT_PATH."/application/common/config_original.php", "w");

		$contenu = str_ireplace("{TS_CLIENT_URL}", $CLIENT_URL, $contenu);
		$contenu = str_ireplace("{TS_CLIENT_PATH}", $CLIENT_PATH, $contenu);
		$contenu = str_ireplace("{TS_EMAIL_LOGS}", $EMAIL_LOGS_CLIENT, $contenu);
		$contenu = str_ireplace("{LOGS_PATH}", $LOGS_PATH_CLIENT, $contenu);
		$contenu = str_ireplace("{TS_BASE_URL}", $BASE_URL, $contenu);
		$contenu = str_ireplace("{TS_URL_XML}", $URL_XML, $contenu);
		$contenu = str_ireplace("{TMP_PATH}", $TMP_PATH, $contenu);
		$contenu = str_ireplace("{TMP_URL}", $TMP_URL, $contenu);
		$contenu = str_ireplace("{LOGIN_ROOT}", $LOGIN_ROOT, $contenu);
		$contenu = str_ireplace("{PASS_ROOT}", $PASS_ROOT, $contenu);
		$contenu = str_ireplace("{SESSION_ID_ROOT}", $SESSION_ID_ROOT, $contenu);
	
		fwrite($handle, $contenu);

		fclose($handle);

//--------------------------------------------------------------------------------------

		$handle = null;
		$contenu = null;
		$handle = fopen($BASE_PATH."/application/common/config_original.php", "c+");
		$contenu = fread($handle,filesize($BASE_PATH."/application/common/config_original.php"));
		fclose($handle);

		$handle = fopen($BASE_PATH."/application/common/config_original.php", "w");
		
		$contenu = str_ireplace("{BASE_URL}", $BASE_URL, $contenu);
		$contenu = str_ireplace("{BASE_PATH}", $BASE_PATH, $contenu);
		$contenu = str_ireplace("{TS_PATH_XML}", $PATH_XML, $contenu);
		$contenu = str_ireplace("{TS_PATH_TMP}", $TS_PATH_TMP, $contenu);
		$contenu = str_ireplace("{TS_URL_TMP}", $TS_URL_TMP, $contenu);
		$contenu = str_ireplace("{TS_EMAIL_LOGS}", $EMAIL_LOGS_SERVEUR, $contenu);
		$contenu = str_ireplace("{TS_URL_MEDIAS}", $URL_MEDIAS, $contenu);
		$contenu = str_ireplace("{TS_PATH_MEDIAS}", $PATH_MEDIAS, $contenu);

		$contenu = str_ireplace("{TS_BDD_SERVER}", $BDD_SERVER, $contenu);
		$contenu = str_ireplace("{TS_BDD_NAME}", $BDD_NAME, $contenu);
		$contenu = str_ireplace("{TS_BDD_USER}", $BDD_USER, $contenu);
		$contenu = str_ireplace("{TS_BDD_PASSWORD}", $BDD_PASSWORD, $contenu);
		$contenu = str_ireplace("{TS_ROOT_LOGIN}", $LOGIN_ROOT, $contenu);
		$contenu = str_ireplace("{TS_ROOT_PASS}", $PASS_ROOT, $contenu);
		$contenu = str_ireplace("{TS_ROOT_SESSIONID}", $SESSION_ID_ROOT, $contenu);
		$contenu = str_ireplace("{TS_PATH_LOGS}", $LOGS_PATH_SERVEUR, $contenu);

		fwrite($handle, $contenu);
		fclose($handle);

//----------------------------------------------------------------------------------------------

		$oldname_client = $CLIENT_PATH.'/application/common/config_original.php';
		$newname_client = $CLIENT_PATH.'/application/common/config.php';
		rename($oldname_client , $newname_client);

		$oldname_serveur = $BASE_PATH.'/application/common/config_original.php';
		$newname_serveur = $BASE_PATH.'/application/common/config.php';
		rename($oldname_serveur , $newname_serveur);

		if ($error == false)
		{
?>
			<script type="text/javascript">
			location.assign(location.href);
			</script>
<?php

		}
	}
?>