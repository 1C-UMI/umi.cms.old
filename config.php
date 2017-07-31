<?php
	function mysql_fatal() {
		include "./errors/mysql_failed.html";
		exit();
	}


	include "./mysql.php";

	mysql_query("SET FOREIGN_KEY_CHECKS 1");

	include "./errors.php";		//хэндлер ошибок. пока UC

//error_reporting(E_ALL);

	include "./classes/patterns/iSingletone.php";
	include "./classes/patterns/singletone.php";

	include "./classes/patterns/iUmiEntinty.php";
	include "./classes/patterns/umiEntinty.php";


	include './classes/systemCore/regedit/iRegedit.php';
	include './classes/systemCore/regedit/regedit.php';	//реестр!!!

	include 'security.php';
	include 'system.php';		//системные !!функции !! установка, подготовка и инициализация системы. подключение модулей.
	include 'def_macroses.php';	//пре-обработчик базовых макросов (title, content, ...)
	include 'morph.php';		//простая баблиотека морфологии русского. выделяет основу слов.
	include 'utf8.php';		//utf8 & win1251...
	include 'lib.php';		//libs


	include './classes/modules/def_module.php';	//базовый модуль!!!

	include './classes/cifi/iCifi.php';
	include './classes/cifi/cifi.php';		//cifi user-interface

	include './classes/ranges/ranges.php';		//ranges iface

	include "./classes/translit/iTranslit.php";
	include "./classes/translit/translit.php";

	include './classes/systemCore/templater/iTemplater.php';
	include './classes/systemCore/templater/templater.php';

	include './classes/systemCore/cmsController/iCmsController.php';
	include './classes/systemCore/cmsController/cmsController.php';

	include './classes/umiXmlExporter/iUmiXmlExporter.php';
	include './classes/umiXmlExporter/umiXmlExporter.php';

	include './classes/umiXmlImporter/iUmiXmlImporter.php';
	include './classes/umiXmlImporter/umiXmlImporter.php';


	include './classes/languageMorph/iLanguageMorph.php';
	include './classes/languageMorph/language_morph.php';


	include "./classes/umiDate/iUmiDate.php";
	include "./classes/umiDate/umiDate.php";

	include "./classes/umiFile/iUmiFile.php";
	include "./classes/umiFile/umiFile.php";

	include "./classes/umiFile/iUmiImageFile.php";
	include "./classes/umiFile/umiImageFile.php";

	include "./classes/umiFilter/iUmiFilter.php";
	include "./classes/umiFilter/umiFilter.php";

	include "./classes/umiFilter/iUmiFilterProcessor.php";
	include "./classes/umiFilter/umiFilterProcessor.php";

	include "./classes/umiDirectory/iUmiDirectory.php";
	include "./classes/umiDirectory/umiDirectory.php";

	include "./classes/umiMail/iUmiMail.php";
	include "./classes/umiMail/umiMail.php";


	include "./classes/umiPagenum/iPagenum.php";
	include "./classes/umiPagenum/pagenum.php";

	include "./classes/umiCaptcha/iUmiCaptcha.php";
	include "./classes/umiCaptcha/umiCaptcha.php";

	include "./classes/hierarchyModel/iLang.php";
	include "./classes/hierarchyModel/lang.php";

	include "./classes/hierarchyModel/iLangsCollection.php";
	include "./classes/hierarchyModel/langsCollection.php";

	include "./classes/hierarchyModel/iDomainMirrow.php";
	include "./classes/hierarchyModel/domainMirrow.php";

	include "./classes/hierarchyModel/iDomain.php";
	include "./classes/hierarchyModel/domain.php";

	include "./classes/hierarchyModel/iDomainsCollection.php";
	include "./classes/hierarchyModel/domainsCollection.php";

	include "./classes/hierarchyModel/iTemplate.php";
	include "./classes/hierarchyModel/template.php";

	include "./classes/hierarchyModel/iTemplatesCollection.php";
	include "./classes/hierarchyModel/templatesCollection.php";

	include "./classes/hierarchyModel/iUmiHierarchyType.php";
	include "./classes/hierarchyModel/umiHierarchyType.php";

	include "./classes/hierarchyModel/iUmiHierarchyTypesCollection.php";
	include "./classes/hierarchyModel/umiHierarchyTypesCollection.php";

	include "./classes/hierarchyModel/iUmiHierarchyElement.php";
	include "./classes/hierarchyModel/umiHierarchyElement.php";

	include "./classes/hierarchyModel/iUmiHierarchy.php";
	include "./classes/hierarchyModel/umiHierarchy.php";


	include "./classes/hierarchyModel/iUmiSelection.php";
	include "./classes/hierarchyModel/umiSelection.php";

	include "./classes/hierarchyModel/iUmiSelectionsParser.php";
	include "./classes/hierarchyModel/umiSelectionsParser.php";



	include "./classes/dataModel/iUmiFieldType.php";
	include "./classes/dataModel/umiFieldType.php";

	include "./classes/dataModel/iUmiField.php";
	include "./classes/dataModel/umiField.php";

	include "./classes/dataModel/iUmiFieldsGroup.php";
	include "./classes/dataModel/umiFieldsGroup.php";

	include "./classes/dataModel/iUmiObjectType.php";
	include "./classes/dataModel/umiObjectType.php";

	include "./classes/dataModel/iUmiObjectProperty.php";
	include "./classes/dataModel/umiObjectProperty.php";

	include "./classes/dataModel/iUmiObject.php";
	include "./classes/dataModel/umiObject.php";

	include "./classes/dataModel/iUmiFieldTypesCollection.php";
	include "./classes/dataModel/umiFieldTypesCollection.php";

	include "./classes/dataModel/iUmiFieldsCollection.php";
	include "./classes/dataModel/umiFieldsCollection.php";

	include "./classes/dataModel/iUmiObjectTypesCollection.php";
	include "./classes/dataModel/umiObjectTypesCollection.php";

	include "./classes/dataModel/iUmiObjectsCollection.php";
	include "./classes/dataModel/umiObjectsCollection.php";

	include "./classes/memcachedController/iMemcachedController.php";
	include "./classes/memcachedController/memcachedController.php";

?>