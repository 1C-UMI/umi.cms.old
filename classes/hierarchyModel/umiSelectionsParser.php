<?php
	class umiSelectionsParser implements iUmiSelectionsParser {
		public static function parseSelection(umiSelection $selection) {
			if($limit_cond = $selection->getLimitConds()) {
				$limit_sql = " LIMIT " . (int) ($limit_cond[0] * $limit_cond[1]) . ", " . (int) $limit_cond[0];
			} else {
				$limit_sql = "";
			}


			$need_hierarchy = $selection->getForceCond();
			$hierarchy_cond = $selection->getHierarchyConds();
			$condition_mode_or = $selection->getConditionModeOr();

			if(!empty($hierarchy_cond)) {
				$hierarchy_sql = "";
				$sz = sizeof($hierarchy_cond);
				for($i = 0; $i < $sz; $i++) {
					if($i == 0) {
						$hierarchy_sql .= " AND (";
					}

					$hierarchy_sql .= "h.rel = '{$hierarchy_cond[$i]}'";

					if($i == ($sz - 1)) {
						$hierarchy_sql .= ")";
					} else {
						$hierarchy_sql .= " OR ";
					}
				}
				$need_hierarchy = true;
			} else {
				$hierarchy_sql = "";
			}

			if($element_type_cond = $selection->getElementTypeConds()) {
				$element_type_sql = "";
				$sz = sizeof($element_type_cond);
				for($i = 0; $i < $sz; $i++) {
					if($i == 0) {
						$element_type_sql .= " AND (";
					}

					$element_type_sql .= "h.type_id = '{$element_type_cond[$i]}'";

					if($i == ($sz - 1)) {
						$element_type_sql .= ")";
					} else {
						$element_type_sql .= " OR ";
					}
				}
				$need_hierarchy = true;
			} else {
				$element_type_sql = "";
			}

			$need_content = false;

			$content_tables_loaded = 0;
			$content_tables = "";

			if($perms_cond = $selection->getPermissionsConds()) {
				$need_hierarchy = true;

				$perms_tables = ",\r\n\t\tcms3_permissions c3p";

				$perms_sql = "";
				$sz = sizeof($perms_cond);
				for($i = 0; $i < $sz; $i++) {
					if($i == 0) {
						$perms_sql .= " AND (";
					}

					$perms_sql .= "(c3p.owner_id = '{$perms_cond[$i]}' AND c3p.rel_id = h.id AND level >= 1)";

					if($i == ($sz - 1)) {
						$perms_sql .= ")";
					} else {
						$perms_sql .= " OR ";
					}
				}
			} else {
				$perms_tables = "";
			}

			if($arr_propconds = $selection->getPropertyConds()) {
				$prop_sql = "";
				
				$condition_mode_or = $selection->getConditionModeOr();

				$prop_cond = array();
				foreach ($arr_propconds as $arr_cond) {
					if ($arr_cond['type'] !== false) $prop_cond[] = $arr_cond;
				}
				unset($arr_propconds);

				$sz = sizeof($prop_cond);
				for($i = 0; $i < $sz; $i++) {

					if($i == 0) {
						$prop_sql .= " AND (";
					}
					
					if(!$condition_mode_or || $content_tables_loaded == 0) {
						$cname = "c" . (++$content_tables_loaded);
						$content_tables .= ",\r\n\t\tcms3_object_content {$cname}";
					}
					

					switch($prop_cond[$i]['filter_type']) {
						case "equal":
							if(is_array($prop_cond[$i]['value']) && sizeof($prop_cond[$i]['value']) > 0) {
								$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$prop_cond[$i]['field_id']}' AND (";

								foreach($prop_cond[$i]['value'] as $cval) {
									$cval = mysql_escape_string($cval);
									$prop_sql .= "{$cname}.{$prop_cond[$i]['type']} = '{$cval}' OR ";
								}

								$prop_sql = substr($prop_sql, 0, strlen($prop_sql) - 4);
								$prop_sql .= "))";
							} else {
								$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$prop_cond[$i]['field_id']}' AND {$cname}.{$prop_cond[$i]['type']} = '" . mysql_escape_string($prop_cond[$i]['value']) . "')";
							}
							break;


						case "not_equal":
							if(is_array($prop_cond[$i]['value'])) {
								$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$prop_cond[$i]['field_id']}' AND (";

								foreach($prop_cond[$i]['value'] as $cval) {
									$cval = mysql_escape_string($cval);
									$prop_sql .= "{$cname}.{$prop_cond[$i]['type']} != '{$cval}' AND ";
								}

								$prop_sql = substr($prop_sql, 0, strlen($prop_sql) - 4);
								$prop_sql .= "))";
							} else {
								$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$prop_cond[$i]['field_id']}' AND {$cname}.{$prop_cond[$i]['type']} = '" . mysql_escape_string($prop_cond[$i]['value']) . "')";
							}
							break;


						case "like":
							$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$prop_cond[$i]['field_id']}' AND {$cname}.{$prop_cond[$i]['type']} LIKE '%" . mysql_escape_string($prop_cond[$i]['value']) . "%')";
							break;

						case "between":
							$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$prop_cond[$i]['field_id']}' AND {$cname}.{$prop_cond[$i]['type']} BETWEEN '" . ((int) $prop_cond[$i]['min']) . "' AND '" . ((int) $prop_cond[$i]['max']) . "')";
							break;

						case "more":
							$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$prop_cond[$i]['field_id']}' AND {$cname}.{$prop_cond[$i]['type']} > '" . ((int) $prop_cond[$i]['value']) . "')";
							break;

						case "less":
							$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$prop_cond[$i]['field_id']}' AND {$cname}.{$prop_cond[$i]['type']} < '" . ((int) $prop_cond[$i]['value']) . "')";
							break;

						case "null":
							$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$prop_cond[$i]['field_id']}' AND {$cname}.{$prop_cond[$i]['type']} IS NULL)";
							break;

						default:
							break;
					}

					if($i == ($sz - 1)) {
						$prop_sql .= ")";
					} else {
						if($condition_mode_or) {
							$prop_sql .= " OR ";
						} else {
							$prop_sql .= " AND ";
						}
					}

				}
				$need_content = true;
			}


			$object_type_sql = "";
			if($object_type_cond = $selection->getObjectTypeConds()) {
				$object_type_sql = "";
				$sz = sizeof($object_type_cond);
				for($i = 0; $i < $sz; $i++) {
					if($i == 0) {
						$object_type_sql .= " AND (";
					}

					$object_type_sql .= "o.type_id = '{$object_type_cond[$i]}'";

					if($i == ($sz - 1)) {
						$object_type_sql .= ")";
					} else {
						$object_type_sql .= " OR ";
					}
				}
			} else {
				$object_type_sql = "";
			}

			$order_sql = "";
			if($order_cond = $selection->getOrderConds()) {


				$is_prop_updated = false;

				$order_sql = " ORDER BY ";
				$sz = sizeof($order_cond);
				

				for($i = 0; $i < $sz; $i++) {

					if($native_field = $order_cond[$i]['native_field']) {
						switch($native_field) {
							case "name": {
								$order_sql .= "o.name " . (($order_cond[$i]['asc']) ? "ASC" : "DESC");
								break;
							}
							
							case "rand": {
								$order_sql .= "RAND()";
								break;
							}
						}

						if($i == ($sz - 1)) {
						} else {
							$order_sql .= ", ";
						}

						if($i == ($sz - 1)) {
							if(!$is_prop_updated && !$prop_sql) {
								$prop_sql = substr($prop_sql, 0, strlen($prop_sql) - strlen(" AND ("));
							}
						}

					} else {
						$need_content = true;
						$prop_sql .= " AND (";
						$is_prop_updated = true;

						$cname = "c" . (++$content_tables_loaded);
						$content_tables .= ",\r\n\t\tcms3_object_content {$cname}";

						$prop_sql .= "({$cname}.obj_id = o.id AND {$cname}.field_id = '{$order_cond[$i]['field_id']}')";

						if($i == ($sz - 1)) {
							$prop_sql .= ")";
						} else {
							$prop_sql .= " AND ";
						}

						$order_sql .= "{$cname}.{$order_cond[$i]['type']} " . (($order_cond[$i]['asc']) ? "ASC" : "DESC");
						if($i == ($sz - 1)) {
						} else {
							$order_sql .= ", ";
						}
					}
				}
				
				if($order_sql == " ORDER BY ") {
					$order_sql = "";
				}
			} else {
				if($need_hierarchy == true) {
					$order_sql = " ORDER BY h.ord";
				} else {
					$order_sql = "";
				}
			}


			if($names_cond = $selection->getNameConds()) {
				$names_sql = " AND (";

				$sz = sizeof($names_cond);
				for($i = 0; $i < $sz; $i++) {
					$cname = mysql_escape_string($names_cond[$i]['value']);
					
					if($names_cond[$i]['type'] == "exact") {
						$names_sql .= "o.name = '{$cname}'";
					} else {
						$names_sql .= "o.name LIKE '%{$cname}%'";
					}

					if($i == ($sz - 1)) {
					} else {
						$names_sql .= " OR ";
					}
				}
				$names_sql .= ") ";

			} else {
				$names_sql = "";
			}
			
			if($condition_mode_or) {
				if($names_sql && $prop_sql) {
					if(substr($prop_sql, 0, 6) == " AND (") {
						$prop_sql = substr($prop_sql, 6, strlen($prop_sql) - 6);
					}
					
					if(substr($names_sql, 0, 5) == " AND ") {
						$names_sql = substr($names_sql, 5, strlen($names_sql) - 5);
					}
					
					$prop_sql = " AND ((" . $prop_sql;
					$names_sql = " OR " . $names_sql . ")";
				}
			}






			if($need_hierarchy == true) {
				$lang_cond = " AND h.lang_id = '".cmsController::getInstance()->getCurrentLang()->getId()."' ";
				$domain_cond = " AND h.domain_id = '".(int) cmsController::getInstance()->getCurrentDomain()->getId()."' ";
				if ($active_cond = $selection->getActiveConds()) {
					$is_active = (isset($active_cond[0]) && (bool) $active_cond[0])? 1 : 0;
					$unactive_cond = " AND h.is_active = '".$is_active."' ";
				} else {
					$unactive_cond = (cmsController::getInstance()->getCurrentMode() == "") ? " AND h.is_active = '1' " : "";
				}
				

				if($need_content == false) {
					$sql = <<<SQL

SELECT SQL_CACHE h.id
	FROM cms3_hierarchy h, cms3_objects o{$perms_tables}
		WHERE	h.obj_id = o.id AND h.is_deleted = '0'
			{$object_type_sql}
			{$hierarchy_sql}
			{$unactive_cond}
			{$element_type_sql}
			{$names_sql}
			{$perms_sql}
			{$lang_cond}
			{$domain_cond}

				{$order_sql}
					{$limit_sql}

SQL;


					$sql_count = <<<SQL

SELECT SQL_CACHE COUNT(h.id)
	FROM cms3_hierarchy h, cms3_objects o{$perms_tables}
		WHERE	h.obj_id = o.id AND h.is_deleted = '0'
			{$object_type_sql}
			{$hierarchy_sql}
			{$unactive_cond}
			{$element_type_sql}
			{$names_sql}
			{$perms_sql}
			{$lang_cond}
			{$domain_cond}

				ORDER BY h.ord

SQL;

				} else {
					$sql = <<<SQL

SELECT DISTINCT SQL_CACHE h.id
	FROM	cms3_hierarchy h, cms3_objects o{$content_tables}{$perms_tables}

			WHERE	o.id = h.obj_id AND h.is_deleted = '0'
				{$object_type_sql}
				{$hierarchy_sql}
				{$unactive_cond}
				{$element_type_sql}
				{$prop_sql}
				{$names_sql}
				{$perms_sql}
				{$lang_cond}
				{$domain_cond}

					{$order_sql}
						{$limit_sql}

SQL;

					$sql_count = <<<SQL

SELECT SQL_CACHE COUNT(DISTINCT h.id)
	FROM	cms3_hierarchy h, cms3_objects o{$content_tables}{$perms_tables}

			WHERE	o.id = h.obj_id AND h.is_deleted = '0'
				{$object_type_sql}
				{$hierarchy_sql}
				{$unactive_cond}
				{$element_type_sql}
				{$prop_sql}
				{$names_sql}
				{$perms_sql}
				{$lang_cond}
				{$domain_cond}

					ORDER BY h.ord

SQL;


				}
			} else {
				if($need_content == false) {
					$sql = <<<SQL

SELECT SQL_CACHE o.id
	FROM cms3_objects o
		WHERE	1
			{$object_type_sql}
			{$names_sql}
					{$order_sql}
						{$limit_sql}
SQL;


					$sql_count = <<<SQL

SELECT SQL_CACHE COUNT(o.id)
	FROM cms3_objects o
		WHERE	1
			{$object_type_sql}
			{$names_sql}

SQL;
				} else {
					$sql = <<<SQL

SELECT SQL_CACHE DISTINCT o.id
	FROM	cms3_objects o{$content_tables}

			WHERE	1
				{$object_type_sql}
				{$prop_sql}
				{$names_sql}
					{$order_sql}
						{$limit_sql}
SQL;

					$sql_count = <<<SQL

SELECT SQL_CACHE COUNT(DISTINCT o.id)
	FROM	cms3_objects o{$content_tables}

			WHERE	1
				{$object_type_sql}
				{$prop_sql}
				{$names_sql}
SQL;


				}
			}

//if(!$prop_sql && !$object_type_sql && !$content_tables && !$limit_sql) return false;

			return Array(
					"result"	=> $sql,
					"count"		=> $sql_count
			);
		}


		public static function runSelection(umiSelection $selection) {
			$sqls = self::parseSelection($selection);
//var_dump($sqls['result']);

			if($result = memcachedController::getInstance()->loadSql($sqls['result'])) {
			} else {
				$result = mysql_unbuffered_query($sqls['result']);
				memcachedController::getInstance()->saveSql($sqls['result'], $result);
			}

			if($err = mysql_error()) {
				var_dump($sqls);
				exit($err);
			}

			$res = Array();
			while(list($element_id) = mysql_fetch_row($result)) {
				$res[] = (int) $element_id;
			}
			return $res;
		}

		public static function runSelectionCounts(umiSelection $selection) {
			$sqls = self::parseSelection($selection);

			if($result = memcachedController::getInstance()->loadSql($sqls['count'])) {
			} else {
				$result = mysql_query($sqls['count']);
				memcachedController::getInstance()->saveSql($sqls['count'], $result);
			}

			if($err = mysql_error()) {
//				exit($err);
			}

			if(list($count) = mysql_fetch_row($result)) {
				return (int) $count;
			} else {
				return false;
			}
		}

	}
?>