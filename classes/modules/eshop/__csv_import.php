<?php
	abstract class __csv_import_eshop {
		public function csv_import () {
			$params = Array();
			$this->load_forms();

			$cifi_csvfile = new cifi("csvfile", "./files/", false);
			$params['cifi_csvfile'] = $cifi_csvfile->make_div() . $cifi_csvfile->make_element();

			return $this->parse_form("csv_import", $params);
		}


		public function csv_import_do () {
			$select_csvfile = $_REQUEST['select_csvfile'];
			if(!($csvfile = umiFile::upload("pics", "csvfile", "./files/"))) $csvfile = new umiFile("./files/" . $select_csvfile);

			$target_element_id = (int) $_REQUEST['data_values'][404][0];

			if($element = umiHierarchy::getInstance()->getElement($target_element_id)) {
				$this->__loadLib("csv_convert.php");

				$arr = getCSVImage($csvfile->getFilepath());

				$res = createUMIDump($arr, $target_element_id);
				$res = iconv("CP1251", "UTF-8//TRANSLIT", $res);

				$xmlpath = $csvfile->getFilepath() . ".xml";

				file_put_contents($xmlpath, $res);
				chmod($xmlpath, 0777);


				include "./classes/umiImportRelations/iUmiImportRelations.php";
				include "./classes/umiImportRelations/umiImportRelations.php";


				umiImportRelations::getInstance()->addNewSource("SOURCE-FOR-{$target_element_id}");
				$source_id = umiImportRelations::getInstance()->getSourceId("SOURCE-FOR-{$target_element_id}");

				umiImportRelations::getInstance()->setIdRelation($source_id, "22800", $target_element_id);

				$xmlImporter = new umiXmlImporter();
				$xmlImporter->loadXmlFile($xmlpath);
				$xmlImporter->analyzeXml();
				$xmlImporter->importXml();

				$this->redirect($this->pre_lang . "/admin/eshop/csv_import/");
			}
		}
	};
?>