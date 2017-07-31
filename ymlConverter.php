<?php
//	header("Content-type: text/xml");

	include "./config.php";

//	include "./classes/umiXmlExporter/iUmiXmlExporter.php";
//	include "./classes/umiXmlExporter/umiXmlExporter.php";

	$sel = new umiSelection();

	$rubric_id = umiHierarchy::getInstance()->getIdByPath("/mini_market/");

	$sel->setHierarchyFilter();
	$sel->addHierarchyFilter($rubric_id, 3);

	$result = umiSelectionsParser::runSelection($sel);

	$result[] = $rubric_id;

	$t = new umiXmlExporter();
	$t->setElements($result);
	$t->run();
	$src = $t->getResultFile();
	$xmldata = simplexml_load_string($src);

	file_put_contents("./xsl/yml_source.xml", $src);
	chmod("./xsl/yml_source.xml", 0777);


	$xslt = new xsltProcessor;
	$xslt->importStyleSheet(DomDocument::load('./xsl/dump2yml.xsl'));
	$resultXml = $xslt->transformToXML($xmldata);

	file_put_contents("./xsl/yml_result.xml", $resultXml);
	chmod("./xsl/yml_result.xml", 0777);

	echo "ok";
?>