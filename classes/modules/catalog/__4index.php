<?php

abstract class __4index_catalog {
	public function get4index() {
		$res = Array();

		if(class_exists("Console_ProgressBar")) {
			echo "Scanning catalog...\r\n";

			$sql = "SELECT COUNT(*) FROM cms_catalog_tree WHERE is_active = 1";
			$result = mysql_query($sql);
			list($total) = mysql_fetch_row($result);

			$progressBar = new Console_ProgressBar('[%bar%] %percent%', '=>', '-', 100, $total);

		}

		$sql = "SELECT id, name, title, h1, object_id FROM cms_catalog_tree WHERE is_active = 1 ORDER BY id";
		$result = mysql_query($sql);

		$i = 0;
		while($row = mysql_fetch_assoc($result)) {

			if($object_id = $row['object_id']) {
			} else {
				$cont = "";
			}
			$cont = "";

			if(class_exists("Console_ProgressBar")) {
				$progressBar->update(++$i);
			}

			$res[] = Array($row['id'], Array("name" => $row['name'], "content" => $cont, "title" => $row['name'], "lang" => 'ru', "domain" => $_REQUEST['domain'], "modifytime" => 1));
		}

		return $res;
	}
}
?>
