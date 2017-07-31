<?php
	$dom = new DOMDocument;

	$dom->Load('source.xml');
//	$dom->schemaValidate("umicmsDump.xsd");
//	if($dom->validate()) {
	if($dom->schemaValidate("umicmsDump.xsd")) {

		echo "This document is valid!\n";
	} else {
		echo "This document is not valid!\n";
	}


?>