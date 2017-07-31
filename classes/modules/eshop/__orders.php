<?php
	abstract class __orders_eshop {

		public function onInit() {
		}


		public function orders() {
			$this->load_forms();
			$params = Array();

			$per_page = 10;
			$curr_page = (int) $_REQUEST['p'];


			//Preparing statuses list
			$order_status_id = umiObjectTypesCollection::getInstance()->getBaseType("eshop", "order_status");
			$status_arr = umiObjectsCollection::getInstance()->getGuidedItems($order_status_id);

			$order_status_filter = (int) $_REQUEST['order_status_filter'];

			if(!$order_status_filter) {
				list(,,,,, $order_status_filter) = array_keys($status_arr);
			}

			$params['order_status_filter_list'] = putSelectBox_assoc($status_arr, $order_status_filter);

			//Selection orders

			$order_type_id = umiObjectTypesCollection::getInstance()->getBaseType("eshop", "order");
			$order_type = umiObjectTypesCollection::getInstance()->getType($order_type_id);
			$order_time_field_id = $order_type->getFieldId("order_time");
			$status_field_id = $order_type->getFieldId("status");

			$sel = new umiSelection;

			$sel->setObjectTypeFilter();
			$sel->addObjectType($order_type_id);

			$sel->setLimitFilter();
			$sel->addLimit($per_page, $curr_page);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($status_field_id, $order_status_filter);

			$sel->setOrderFilter();
			$sel->setOrderByProperty($order_time_field_id, false);


			//Rendering numpage

			$result = umiSelectionsParser::runSelection($sel);
			$total = umiSelectionsParser::runSelectionCounts($sel);
			$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);


			//Rendering orders

			$sz = sizeof($result);
			$rows = "";
			for($i = 0; $i < $sz; $i++) {
				//Getting object
				$order_id = $result[$i];
				$order = umiObjectsCollection::getInstance()->getObject($order_id);

				//Getting order post time
				$order_time = $order->getValue("order_time");
				$order_date = is_object($order_time) ? $order_time->getFormattedDate("d.m.Y H:i") : $order_time;

				//Getting users info
				$user_id = $this->getUserIdByOrderId($order_id);
				$user_info = cmsController::getInstance()->getModule('users')->get_user_info($user_id, "<a href=\"%pre_lang%/admin/users/user_edit/{$user_id}/all/\">%lname% %fname% %mname% (%login%)</a>");

				//Getting "status"
				list($status) = $order->getValue("status");
				$statuses = putSelectBox_assoc($status_arr, $status);

				$order_link = $this->pre_lang . "/admin/eshop/orders_order/" . $order_id . "/";

				//Getting items
				$items = $order->getValue("items");

				$price_total = 0;
				$items_count = 0;
				$items_str = "";
				$isz = sizeof($items);
				for($j = 0; $j < $isz; $j++) {
					$item_id = $items[$j];
					$item = umiObjectsCollection::getInstance()->getObject($item_id);

					$items_count += $item->getValue("count");
					$price_total += $item->getValue("price_total");

					$element_id = $item->getValue("catalog_relation");

					$element = umiHierarchy::getInstance()->getElement($element_id);
                    if(($element = umiHierarchy::getInstance()->getElement($element_id)) === false) continue;
					$element_name = $element->getName();

					$element_link = umiHierarchy::getInstance()->getPathById($element_id);

					$items_str .= <<<END
<a href="{$element_link}">{$element_name}</a><br />
END;
				}




				$rows .= <<<END
	<row>
		<col>
			№{$order_id}
		</col>

		<col>
			{$order_date}
		</col>

		<col>
			{$user_info}
		</col>

		<col>
			<select quant="no" br="no">
				<name><![CDATA[statuses[{$order_id}]]]></name>
				{$statuses}
			</select>

		</col>

		<col>
			<a href="{$order_link}"><b>{$items_count}</b> товаров на сумму <b>{$price_total} руб</b></a>
		</col>

		<col>
			{$items_str}
		</col>
	</row>

END;
			}

			$params['rows'] = $rows;
			return $this->parse_form("orders", $params);
		}


		public function orders_do() {
			$statuses = $_REQUEST['statuses'];

			foreach($statuses as $order_id => $status_id) {
				if(cmsController::getInstance()->getModule("users")->isOwnerOfObject($order_id)) {
					continue;
				}

				$order = umiObjectsCollection::getInstance()->getObject($order_id);
				$order->setValue("status", $status_id);
				$order->commit();
			}
			$this->redirect($this->pre_lang . "/admin/eshop/orders/");
		}




		public function orders_order() {
			$this->load_forms();
			$params = Array();

			$this->sheets_set_active("orders");

			$order_id = (int) $_REQUEST['param0'];


			$order = umiObjectsCollection::getInstance()->getObject($order_id);

			//Getting order post time
			$order_time = $order->getValue("order_time");
			$params['posttime'] = is_object($order_time) ? $order_time->getFormattedDate("d.m.Y H:i") : $order_time;

			//Getting users info
			$user_id = $this->getUserIdByOrderId($order_id);
			$params['user_info'] = cmsController::getInstance()->getModule('users')->get_user_info($user_id, "<a href=\"%pre_lang%/admin/users/user_edit/{$user_id}/all/\">%lname% %fname% %mname% (%login%)</a>");


			//Preparing statuses list
			$order_status_id = umiObjectTypesCollection::getInstance()->getBaseType("eshop", "order_status");
			$status_arr = umiObjectsCollection::getInstance()->getGuidedItems($order_status_id);

			//Getting "status"
			list($status) = $order->getValue("status");
			$params['statuses'] = putSelectBox_assoc($status_arr, $status);

			$order_link = $this->pre_lang . "/admin/eshop/orders_order/" . $order_id . "/";

			$params['comment'] = $order->getValue("customer_comments");
			$params['admin_comment'] = $order->getValue("admin_comments");

			$params['discount_card_number'] = "Дисконтная карта не задействована";

			$delivery_address_id = $order->getValue("delivery_address");

			$delivery_address = umiObjectsCollection::getInstance()->getObject($delivery_address_id);

			if($delivery_address) {
				$delivery_country	= $delivery_address->getValue("country");
				$delivery_city		= $delivery_address->getValue("city");
				$delivery_post_index	= $delivery_address->getValue("post_index");
				$delivery_address_str	= $delivery_address->getValue("address");
				$delivery_phone		= $delivery_address->getValue("phone");

				$params['delivery_address'] = 	$delivery_country . ", " .
								$delivery_city . ", " .
								$delivery_post_index . ", " .
								$delivery_address_str . ", " .
								$delivery_phone;
			}

			//Getting items
			$items = $order->getValue("items");

			$price_total = 0;
			$items_count = 0;
			$rows = "";
			$isz = sizeof($items);
			for($j = 0; $j < $isz; $j++) {
				$item_id = $items[$j];
				$item = umiObjectsCollection::getInstance()->getObject($item_id);

				$items_count += $item_count = $item->getValue("count");
				$price_total += $item_price_total = $item->getValue("price_total");
				$item_price = $item->getValue("price_item");

				$element_id = $item->getValue("catalog_relation");

				$element = umiHierarchy::getInstance()->getElement($element_id, true, true);

				if(!$element) continue;
				
				$element_name = $element->getName();
				$element_link = umiHierarchy::getInstance()->getPathById($element_id);

				$rows .= <<<ROW

	<row>
		<col>
			<a href="{$element_link}">{$element_name}</a>
		</col>

		<col>
			{$item_count} шт.
		</col>

		<col>
			{$item_price} руб.
		</col>

		<col>
			{$item_price_total} руб.
		</col>
	</row>

ROW;
			}

			$rows .= <<<ROW
	<row>
		<col>
			<b>Итого</b>
		</col>

		<col colspan="2">
			<b>{$items_count} шт.</b>
		</col>

		<col>
			<b>{$price_total} руб.</b>
		</col>
	</row>
ROW;


			$params['rows'] = $rows;
			$params['order_id'] = $order_id;
			$params['save_n_save'] = '<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';

			return $this->parse_form("orders_order", $params);
		}


		public function orders_order_do() {
			$order_id = (int) $_REQUEST['param0'];
			$status = $_REQUEST['status'];
			$comment = $_REQUEST['comment'];
			$admin_comment = $_REQUEST['admin_comment'];


			$order = umiObjectsCollection::getInstance()->getObject($order_id);
			$order->setValue("status", $status);
			$order->setValue("customer_comments", $comment);
			$order->setValue("admin_comments", $admin_comment);
			$order->commit();


			$exit_after_save = $_REQUEST['exit_after_save'];
			if($exit_after_save) {
				$this->redirect($_REQUEST['pre_lang'] . "/admin/eshop/orders/");
			} else {
				$this->redirect($_REQUEST['pre_lang'] . "/admin/eshop/orders_order/" . $order_id . "/");
			}
		}

	};
?>