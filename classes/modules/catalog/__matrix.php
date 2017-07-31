<?php

	abstract class __catalog_matrix {

		public function __catalog_matrix_onInit () {
		}

		public function matrix() {
			$params = Array();
			$this->load_forms();

			$sql = "SELECT * FROM cms_catalog_matrix ORDER BY name ASC";
			$result = mysql_query($sql);

			$rows = "";
			while($row = mysql_fetch_assoc($result)) {
				$id = $row['id'];
				$name = $row['name'];

				list($count_questions) = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM cms_catalog_matrix_questions WHERE rel='$id'"));
				list($count_items) = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM cms_catalog_matrix_rels WHERE rel='$id'"));

				$rows .= <<<ROW

<row>
	<col>
		<a href="%pre_lang%/admin/catalog/matrix_matrix/$id/">$name</a>
	</col>

	<col style="text-align: center;">
		$count_items
	</col>

	<col style="text-align: center;">
		$count_questions
	</col>

	<col style="text-align: center;">
		<a href="%pre_lang%/admin/catalog/matrix_edit/$id/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" border="0" /></a>
	</col>


	<col style="text-align: center;">
		<a href="%pre_lang%/admin/catalog/matrix_del/$id/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" border="0" /></a>
	</col>
</row>

ROW;
			}

			$params['rows'] = $rows;
			return $this->parse_form("matrix", $params);
		}

	};

?>