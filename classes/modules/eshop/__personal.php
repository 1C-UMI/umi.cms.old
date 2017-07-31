<?php
	abstract class __personal_eshop {
		public function personal ($template = "default") {
			if(!$template) $template = "default";

			list($template_block, $personal_cart_block, $personal_cart_block_empty, $template_orders_block, $template_orders_block_empty, $template_delivery_block, $template_delivery_block_empty) = def_module::loadTemplates("./tpls/eshop/personal/{$template}.tpl", "personal_block", "personal_cart_block", "personal_cart_block_empty", "personal_orders_block", "personal_orders_block_empty", "delivery_block", "delivery_block_empty");

			$cart_has_items = (bool) sizeof($this->cart->getValue("items"));

			$user_id = $this->user_id;
			$user = umiObjectsCollection::getInstance()->getObject($user_id);
			$has_delivery_list = (bool) sizeof($user->getValue("delivery_addresses"));

			$block_arr = Array();

			$block_arr['cart'] = ($cart_has_items) ? $personal_cart_block : $personal_cart_empty_block;
			$block_arr['orders'] = $template_orders_block;
			$block_arr['delivery_address'] = ($has_delivery_list) ? $template_delivery_block : $template_delivery_block_empty;
			//TODO

			$block_arr['template'] = $template;
			return def_module::parseTemplate($template_block, $block_arr);
		}
	};
?>