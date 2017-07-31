<?php
	/* UMI.CMS 2.0 - скрипт для генерации robots.txt */

	include "config.php";

	header("Content-type: text/plain");

	echo "User-Agent: *\r\n";
	echo "Disallow: \r\n";

	$type_id = umiObjectTypesCollection::getInstance()->getBaseType("content");
	$type = umiObjectTypesCollection::getInstance()->getType($type_id);
	$robots_deny_field_id = $type->getFieldId("robots_deny");

	$sel = new umiSelection;

	$sel->setPropertyFilter();
	$sel->addPropertyFilterEqual($robots_deny_field_id, 1);

	$sel->forceHierarchyTable();

	$result = umiSelectionsParser::runSelection($sel);
	foreach($result as $element_id) {
		$element_path = umiHierarchy::getInstance()->getPathById($element_id);
		echo "Disallow: ", $element_path, "\r\n";
	}
?>