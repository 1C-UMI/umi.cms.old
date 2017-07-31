<?php
	function createUMIDump($input, $target_element_id) {
		$timestamp = time();

		$elements = createUMIDumpElements($input);

		$element = umiHierarchy::getInstance()->getElement($target_element_id);

		$title = $element->getName();

		$res = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<umicmsDump>
	<siteName><![CDATA[adfasdasd]]></siteName>
	<domain>local</domain>
	<sourceId><![CDATA[SOURCE-FOR-{$target_element_id}]]></sourceId>

	<generateTime>
			<timestamp><![CDATA[{$timestamp}]]></timestamp>
	</generateTime>


	<element id="22800" parentId="0" objectId="22800" is_visible="1">
		<name><![CDATA[{$title}]]></name>

		<behaviour>
			<module><![CDATA[catalog]]></module>
			<method><![CDATA[category]]></method>
		</behaviour>
	</element>

	<object id="22800" typeId="22" isLocked="0">
		<name><![CDATA[{$title}]]></name>
	</object>


	{$elements}

</umicmsDump>
XML;

		return $res;
	}


	function createUMIDumpElements($input) {
		$fields = $input[0];
		$elements = $input[1];

		$res = "";

		for($i = 0; $i < sizeof($elements); $i++) {
			$element = $elements[$i];

			if(!$element['id']) continue;

			$props = createUMIDumpElementProps($fields, $element);

			$res .= <<<XML

	<element id="{$element['id']}" parentId="22800" objectId="{$element['id']}" is_visible="1">
		<name><![CDATA[{$element['name']}]]></name>

		<templatePath><![CDATA[inner.tpl]]></templatePath>
		<lang prefix="ru"><![CDATA[Русский]]></lang>
		<domain><![CDATA[production-business.umi-cms.ru]]></domain>

		<behaviour>
			<module><![CDATA[catalog]]></module>
			<method><![CDATA[object]]></method>
		</behaviour>
	</element>

	<object id="{$element['id']}" typeId="23" isLocked="0">
		<name><![CDATA[{$element['name']}]]></name>

		<propertiesBlock isLocked="1" isPublic="0">
			<name><![CDATA[imported]]></name>
			<title><![CDATA[Импортированные свойства]]></title>

			{$props}
		</propertiesBlock>
	</object>



XML;
		}


		return $res;
	}


	function createUMIDumpElementProps($fields, $element) {
		$res = "";

		foreach($fields as $field) {
			$val = $element[$field['name']];
			if($val) {
				$values = <<<XML
					<value><![CDATA[{$val}]]></value>
XML;
			} else {
				$values = "";
			}


			if(!$field['name']) $field['name'] = translit::convert($field['title']);

			if($field['data_type'] == "native") continue;

			$res .= <<<XML

			<property isLocked="0" isPublic="0">
				<name><![CDATA[{$field['name']}]]></name>
				<title><![CDATA[{$field['title']}]]></title>

				<fieldType><![CDATA[{$field['data_type']}]]></fieldType>
				<isMultiple>0</isMultiple>
				<isIndexed>0</isIndexed>
				<isFilterable>0</isFilterable>

				<guideId>0</guideId>

				<tip><![CDATA[]]></tip>

				<values>
{$values}

				</values>
			</property>


XML;
		}


		return $res;
	}



function getCSVImage($filename) {
	$cont = file_get_contents($filename);

	$rows = split("\n", $cont);
	unset($cont);

	$result = Array();
	$fields = Array();

	$j = 0;
	foreach($rows as $row) {
		$cols = split(";", $row);

		if($j == 0) {
			foreach($cols as $col) {
				$col = trim($col);
				$fields[] = Array("name" => $col);
			}
		}

		if($j == 1) {
			$i = 0;
			foreach($cols as $col) {
				$col = trim($col);
				$fields[$i++]['title'] = $col;
			}
		}


		if($j == 2) {
			$i = 0;
			foreach($cols as $col) {
				$col = trim($col);

				$fields[$i++]['data_type'] = $col;
			}
		}


		if($j >= 3) {
			$subresult = Array();

			for($i = 0; $i < sizeof($fields); $i++) {
				if(!($field_name = $fields[$i]['name'])) continue;


				$cols[$i] = trim($cols[$i]);
				if($field_name == "id") {
					$cols[$i] = (int) substr($cols[$i], 6, strlen($cols[$i]) - 6);
				}

				$subresult[$field_name] = trim($cols[$i]);
			}

			$result[] = $subresult;
		}

		++$j;
	}

	return Array($fields, $result);
}
?>