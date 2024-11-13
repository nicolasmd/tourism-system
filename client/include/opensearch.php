<?php
	
	header('Content-type: text/xml');
	
	require_once('../application/common/config.php');
	
	echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>

<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
	<ShortName>Tourism System</ShortName>
	<Description>Tourism System</Description>
	<Language>fr</Language>
	<InputEncoding>UTF-8</InputEncoding>
	<Image width="16" height="16" type="image/x-icon"><?php echo TS_CLIENT_URL; ?>images/favicon.png</Image>
	<Url type="text/html" method="GET" template="<?php echo TS_CLIENT_URL; ?>fiches.php?query={searchTerms}" />
</OpenSearchDescription>