<pre>
<?php
	error_reporting(~E_ALL);

	ini_set('include_path', dirname(__FILE__) . '/');

	include "classes/umiDistr/umiDistrWriter.php";
	include "classes/umiDistr/umiDistrInstallItem.php";
	include "classes/umiDistr/umiDistrFile.php";
	include "classes/umiDistr/umiDistrFolder.php";
	include "classes/umiDistr/umiDistrMySql.php";

	$distr = new umiDistrWriter("test.autoconf", true);
	$distr->author = "lyxsus";
	$distr->comment = "UMI.CMS Pro Business UTF8";
	$distr->generatePackage("output/umicms_pro_business_utf8.ucp");

	echo "Generated package {$distr->comment}\n";
?>
</pre>