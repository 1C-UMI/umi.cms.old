<?php

abstract class __eshop {

	public function onInit() {
	}

	public function total() {
		$params = Array();
		$this->load_forms();
		
		$today = strtotime(date("Y-m-d"));
		$tomorrow = $today + 3600*24;
		$yesterday = $today - 3600*24;


		$type_id = umiObjectTypesCollection::getInstance()->getBaseType("eshop", "order");
		$type = umiObjectTypesCollection::getInstance()->getType($type_id);
		$status_field_id = $type->getFieldId("status");
		$order_time_field_id = $type->getFieldId("order_time");

		$sel = new umiSelection;

		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);

		$sel->setPropertyFilter();
		$sel->addPropertyFilterEqual($status_field_id, $this->statusToId("wait"));

		$orders_waiting_check = umiSelectionsParser::runSelectionCounts($sel);

		$params['orders_waiting_check'] = $orders_waiting_check;


		$sel = new umiSelection;

		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);

		$sel->setPropertyFilter();

		$sel->addPropertyFilterMore($order_time_field_id, strtotime(date("Y-m-d")));

		$orders_today = umiSelectionsParser::runSelectionCounts($sel);
		$params['orders_today'] = $orders_today;


		$sel = new umiSelection;

		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);

		$sel->setPropertyFilter();
//		$sel->addPropertyFilterNotEqual($status_field_id, $this->statusToId("cart"));
		$sel->addPropertyFilterLess($order_time_field_id, strtotime(date("Y-m-d")));
		$sel->addPropertyFilterMore($order_time_field_id, strtotime(date("Y-m-d")) - 3600*24);

		$orders_yesterday = umiSelectionsParser::runSelectionCounts($sel);

		$params['orders_yesterday'] = $orders_yesterday;


		$sel = new umiSelection;

		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);

		$sel->setPropertyFilter();
		$sel->addPropertyFilterNotEqual($status_field_id, $this->statusToId("cart"));

		$orders_before = umiSelectionsParser::runSelectionCounts($sel);

		$params['orders_before'] = $orders_before;


		$sql = "SELECT COUNT(*) FROM cms_eshop_orders WHERE status = 'waiting_credit_form'";
		$result = mysql_query($sql);
		list($credit_need_form) = mysql_fetch_row($result);
		$params['credit_need_form'] = $credit_need_form;


		$sql = "SELECT COUNT(*) FROM cms_eshop_orders WHERE status = 'wating_bank'";
		$result = mysql_query($sql);
		list($credit_waiting_bank) = mysql_fetch_row($result);
		$params['credit_waiting_bank'] = $credit_waiting_bank;


		$sql = "SELECT COUNT(*) FROM cms_eshop_orders WHERE status = 'chanceled'";
		$result = mysql_query($sql);
		list($cancel_total) = mysql_fetch_row($result);
		$params['cancel_total'] = $cancel_total;

		$sql = "SELECT COUNT(*) FROM cms_eshop_orders WHERE status = 'chanceled' AND posttime > $today";
		$result = mysql_query($sql);
		list($cancel_today) = mysql_fetch_row($result);
		$params['cancel_today'] = $cancel_today;

		$sql = "SELECT COUNT(*) FROM cms_eshop_orders WHERE status = 'chanceled' AND posttime > $yesterday";
		$result = mysql_query($sql);
		list($cancel_yesterday) = mysql_fetch_row($result);
		$params['cancel_yesterday'] = $cancel_yesterday;

		$sql = "SELECT COUNT(*) FROM cms_eshop_orders WHERE status = 'chanceled' AND posttime < $yesterday";
		$result = mysql_query($sql);
		list($cancel_before) = mysql_fetch_row($result);
		$params['cancel_before'] = $cancel_before;

		$sql = "SELECT SUM(eoi.num) FROM cms_eshop_orders eo, cms_eshop_orders_items eoi WHERE (eo.status != 'not_formed' AND eo.status != 'chanceled' AND eo.status != 'denied' AND eo.status != 'ready') AND eoi.order_id = eo.id";
		$result = mysql_query($sql);
		list($goods_to_send) = mysql_fetch_row($result);
		$params['goods_to_send'] = $goods_to_send;

/*
SELECT SUM(eoi.num), SUM(es.num) FROM cms_eshop_orders eo, cms_eshop_orders_items eoi, cms_eshop_stores es
	WHERE	(eo.status != 'not_formed' AND eo.status != 'chanceled' AND eo.status != 'denied' AND eo.status != 'ready') AND
		eoi.order_id = eo.id AND
		es.object_id = eoi.object_id
			GROUP BY eoi.num
*/

		mysql_query("DROP TEMPORARY TABLE __ord_sum_1");
		mysql_query("DROP TEMPORARY TABLE __ord_sum_2");
		mysql_query("DROP TEMPORARY TABLE __ord_sum_3");

		$sql = <<<END
CREATE TEMPORARY TABLE __ord_sum_1
	SELECT object_id, SUM(num) as num_sum
		FROM cms_eshop_orders eo, cms_eshop_orders_items eoi 
			WHERE	(eo.status != 'not_formed' AND eo.status != 'chanceled' AND eo.status != 'denied' AND eo.status != 'ready') 
				AND eoi.order_id = eo.id
					GROUP BY eoi.object_id;
END;
		mysql_query($sql);

		$sql = <<<END
CREATE TEMPORARY TABLE __ord_sum_2
	SELECT object_id, SUM(num) as num_sum
		FROM cms_eshop_stores
			GROUP BY object_id;
END;
		mysql_query($sql);

		$sql = <<<END
CREATE TEMPORARY TABLE __ord_sum_3
		SELECT IF(SUM(s1.num_sum) - SUM(s2.num_sum) > 0, SUM(s1.num_sum) - SUM(s2.num_sum), 0) as needle_sum
			FROM __ord_sum_1 s1, __ord_sum_2 s2
				WHERE s1.object_id = s2.object_id
					GROUP BY s1.object_id;
END;
		mysql_query($sql);

		$sql = "SELECT SUM(needle_sum) FROM __ord_sum_3";
		$result = mysql_query($sql);
		list($goods_not_enought) = mysql_fetch_row($result);
		$params['goods_not_enought'] = $goods_not_enought;

		$sql = "SELECT COUNT(*) FROM cms_eshop_subs WHERE reason = 'price' GROUP BY user_id";
		$result = mysql_query($sql);

//		list($price_wait_down) = array(1);//(int) mysql_fetch_row($result);
		list($price_wait_down) = mysql_fetch_row($result);
		$params['price_wait_down'] = (int) $price_wait_down;

		return $this->parse_form("total", $params);
	}

	public function goods_not_enought() {
		$params = Array();
		$this->sheets_set_active("total");
		$this->load_forms();

		$sql = <<<END
CREATE TEMPORARY TABLE __ord_sum_1
	SELECT object_id, SUM(num) as num_sum
		FROM cms_eshop_orders eo, cms_eshop_orders_items eoi 
			WHERE	(eo.status != 'not_formed' AND eo.status != 'chanceled' AND eo.status != 'denied' AND eo.status != 'ready') 
				AND eoi.order_id = eo.id
					GROUP BY eoi.object_id;
END;
		mysql_query($sql);

		$sql = <<<END
CREATE TEMPORARY TABLE __ord_sum_2
	SELECT object_id, SUM(num) as num_sum
		FROM cms_eshop_stores
			GROUP BY object_id;
END;
		mysql_query($sql);

		$sql = <<<END
CREATE TEMPORARY TABLE __ord_sum_3
		SELECT IF(SUM(s1.num_sum) - SUM(s2.num_sum) > 0, SUM(s1.num_sum) - SUM(s2.num_sum), 0) as needle_sum, s1.object_id
			FROM __ord_sum_1 s1, __ord_sum_2 s2
				WHERE s1.object_id = s2.object_id
					GROUP BY s1.object_id;
END;
		mysql_query($sql);

		$sql = "SELECT ct.name, ct.rel, s3.object_id, s1.num_sum, s2.num_sum FROM __ord_sum_3 s3, __ord_sum_1 s1, __ord_sum_2 s2, cms_catalog_tree ct WHERE s3.needle_sum > 0 AND s1.object_id = s3.object_id AND ct.object_id = s3.object_id AND s2.object_id = s3.object_id;";
		$result = mysql_query($sql);
		$rows = "";
		while(list($object_title, $object_rel, $object_id, $obj_need, $obj_have) = mysql_fetch_row($result)) {
			$rows .= <<<ROW
	<row>
		<col><a href="%pre_lang%/admin/catalog/tree_object_edit/$object_id/$object_rel/">$object_title</a></col>
		<col>$obj_need</col>
		<col>$obj_have</col>
	</row>
ROW;
		}

		$params['rows'] = $rows;
		return $this->parse_form("goods_not_enought", $params);
	}

	public function goods_to_send() {
		$params = Array();
		$this->sheets_set_active("total");
		$this->load_forms();

		$sql = <<<END
CREATE TEMPORARY TABLE __ord_sum_1
	SELECT object_id, SUM(num) as num_sum
		FROM cms_eshop_orders eo, cms_eshop_orders_items eoi 
			WHERE	(eo.status != 'not_formed' AND eo.status != 'chanceled' AND eo.status != 'denied' AND eo.status != 'ready') 
				AND eoi.order_id = eo.id
					GROUP BY eoi.object_id;
END;
		mysql_query($sql);

		$sql = "SELECT s1.object_id, ct.name, ct.rel, s1.num_sum FROM __ord_sum_1 s1, cms_catalog_tree ct WHERE ct.object_id = s1.object_id";
		$result = mysql_query($sql);
		$row = "";
		while(list($object_id, $object_title, $object_rel, $obj_need) = mysql_fetch_row($result)) {
			$rows .= <<<ROW
	<row>
		<col><a href="%pre_lang%/admin/catalog/tree_object_edit/$object_id/$object_rel/">$object_title</a></col>
		<col>$obj_need</col>
	</row>
ROW;
		}

		$params['rows'] = $rows;
		return $this->parse_form("goods_to_send", $params);
	}

	public function blacklist_user_add($user_id) {
		$sql = "INSERT INTO cms_eshop_blacklist (user_id) VALUES('$user_id')";
		mysql_query($sql);
	}

	public function blacklist_user_del($user_id) {
		$sql = "DELETE FROM cms_eshop_blacklist WHERE user_id = '$user_id'";
		mysql_query($sql);
	}

	public function config() {
		$this->sheets_reset();
		$this->load_forms();
		$params = Array();

		$regedit = regedit::getInstance();

		$params['shop_email'] = $regedit->getVal("//modules/eshop/shop_email");
		$params['from_email'] = $regedit->getVal("//modules/eshop/from_email");
		$params['related_discount'] = (int) $regedit->getVal("//modules/eshop/related_discount");

		return $this->parse_form("config", $params);
	}

	public function config_do() {
		$shop_email = utf8_1251($_REQUEST['shop_email']);
		$from_email = utf8_1251($_REQUEST['from_email']); 
		$related_discount = (int) utf8_1251($_REQUEST['related_discount']);

		$regedit = regedit::getInstance();
		$regedit->setVar("//modules/eshop/from_email", $from_email);
		$regedit->setVar("//modules/eshop/shop_email", $shop_email);
		$regedit->setVar("//modules/eshop/related_discount", $related_discount);

		$this->redirect($this->pre_lang . "/admin/eshop/config/");
	}
}

?>