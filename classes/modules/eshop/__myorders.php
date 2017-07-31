<?php
	abstract class __myorders_eshop {
		public function my_orders($template = "default") {
			if(!$template) $template = "default";

			list($template_block, $template_line, $template_block_empty) = def_module::loadTemplates("./tpls/eshop/orders/{$template}.tpl", "orders_block", "orders_block_line", "orders_block_empty");


			$block_arr = Array();


			$lines = "";

			$orders = $this->orders;
			foreach($orders as $order_id) {
				$order = umiObjectsCollection::getInstance()->getObject($order_id);
				list($status_id) = $order->getValue("status");
				$status = $this->idToStatus($status_id);
				$status_name = $status->getValue("id_name");
				if($status_name == "cart") continue;

				$line_arr = Array();
				$line_arr['id']		= $order_id;
				$line_arr['status']	= $status->getName();
				$line_arr['time']	= $order->getValue("order_time")->getFormattedDate("U");
				$line_arr['total']	= $this->calculateOrderPrice($order_id);
				$line_arr['link']	= $this->pre_lang . "/eshop/order/" . $order_id . "/";

				$lines .= def_module::parseTemplate($template_line, $line_arr);
			}

			$block_arr['lines'] = $lines;

			$template = $template_block;	//TEMP
			return def_module::parseTemplate($template, $block_arr);
		}

		public function order($template = "default") {
			if(!$template) $template = "default";

			//TODO: Check, if this order belongs to current user
			$order_id = (int) $_REQUEST['param0'];

			list($template_block, $template_line) = def_module::loadTemplates("./tpls/eshop/orders/{$template}.tpl", "order_block", "order_block_line");
			$block_arr = Array();

			$order = umiObjectsCollection::getInstance()->getObject($order_id);

			$block_arr['id'] = $order_id;

			$lines = "";
			$price_total = 0;
			$items = $order->getValue("items");

			foreach($items as $item_id) {
				$item = umiObjectsCollection::getInstance()->getObject($item_id);

				$line_arr = Array();

				$price_total += $item->getValue("price_total");
				$spec = "&nbsp;" . '<img src="/images/mt_special_price.gif" border="0" alt="Специальная цена" title="Специальная цена" width="38" height="25" />';

				$element_id = $item->getValue("catalog_relation");
				$element = umiHierarchy::getInstance()->getElement($element_id);

				$line_arr['title'] = $item->getName();
				$line_arr['price'] = $item->getValue("price_item");
				$line_arr['num'] = $item->getValue("count");
				$line_arr['order_item_id'] = $item->getId();
				$line_arr['price_total'] = $item->getValue("price_total");

				$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);

				$lines .= def_module::parseTemplate($template_line, $line_arr);
			}

			$block_arr['lines'] = $lines;
			$block_arr['order_price'] = $price_total;
			$block_arr['delivery_address_id'] = $order->getValue("delivery_address");
			return def_module::parseTemplate($template_block, $block_arr, false, $order_id);
		}


		public function order_cancel($template = "default") {
			if(!$template) $template = "default";

			//TODO: Check, if this order belongs to current user
			$order_id = (int) $_REQUEST['param0'];

			$order = umiObjectsCollection::getInstance()->delObject($order_id);

			$this->redirect($this->pre_lang . "/eshop/personal/");
		}
	};
?>