<?php
	abstract class __subjects_news {
		public function subjects() {
			$params = Array();
			$this->load_forms();

			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("news", "subject");
			$hierarchy_type_id = $hierarchy_type->getId();
			$subjects_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type_id);
			$subjects_type_id = key($subjects_types);

			$subjects = umiObjectsCollection::getInstance()->getGuidedItems($subjects_type_id);

			$rows = "";
			foreach($subjects as $subject_id => $subject_name) {
				$rows .= <<<ROW

		<row>
			<col>
				<input type="text" quant="no" style="width: 97%;">
					<name><![CDATA[subjects[{$subject_id}]]]></name>
					<value><![CDATA[{$subject_name}]]></value>
				</input>
			</col>

			<col style="text-align: center;">
				<checkbox value="{$subject_id}">
							<name><![CDATA[subject_del[]]]></name>
							<value><![CDATA[{$subject_id}]]></value>
					</checkbox>

			</col>
		</row>

ROW;
			}

			$params['rows'] = $rows;
			return $this->parse_form("subjects", $params);
		}

		public function subjects_do() {
			$subject_new = $_REQUEST['subject_new'];
			$subjects = $_REQUEST['subjects'];
			$subject_del = $_REQUEST['subject_del'];

			foreach($subjects as $subject_id => $subject_name) {
				$subject = umiObjectsCollection::getInstance()->getObject($subject_id);
				$subject->setName($subject_name);
				$subject->commit();
			}

			foreach($subject_del as $subject_id) {
				umiObjectsCollection::getInstance()->delObject($subject_id);
			}


			if($subject_new) {
				$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getTypeByName("news", "subject");
				$hierarchy_type_id = $hierarchy_type->getId();

				$subjects_types = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($hierarchy_type_id);
				$subjects_type_id = key($subjects_types);

				umiObjectsCollection::getInstance()->addObject($subject_new, $subjects_type_id);
			}

			$this->redirect($this->pre_lang . "/admin/news/subjects/");
		}
	}
?>