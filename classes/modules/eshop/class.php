<?php

class eshop extends def_module {
	public $orders = Array(), $cart = false;
	public $is_auth = false;
	public $user_id = 0;

	public function __construct() {
                parent::__construct($CMS_ENV);

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$this->__loadLib("__admin.php");
			$this->__implement("__eshop");

			$this->__loadLib("__orders.php");
			$this->__implement("__orders_eshop");

			$this->__loadLib("__csv_import.php");
			$this->__implement("__csv_import_eshop");


			$this->sheets_add("Сводные данные", "total");
			$this->sheets_add("Заказы", "orders");
			$this->sheets_add("Импорт из CSV", "csv_import");
		} else {
			$this->__loadLib("__custom.php");
			$this->__implement("__custom_eshop");

			$this->__loadLib("__personal.php");
			$this->__implement("__personal_eshop");

			$this->__loadLib("__myorders.php");
			$this->__implement("__myorders_eshop");

			$this->__loadLib("__delivery.php");
			$this->__implement("__delivery_eshop");

			$this->prepareInit();
		}
	}

	public function __destruct() {
		if($this->is_auth) {
			$user = umiObjectsCollection::getInstance()->getObject($this->user_id);
			$user->getPropByName("orders_refs")->setValue($this->orders);
			$user->commit();
		} else {
			$_SESSION['orders'] = $this->orders;
		}
	}




	private function prepareInit() {
		cmsController::getInstance()->getModule('users');

		if($users_inst = cmsController::getInstance()->getModule("users")) {
			if($users_inst->is_auth()) {
				$user_id = cmsController::getInstance()->getModule("users")->user_id;

				$this->is_auth = true;
				$this->user_id = $user_id;

				$user = umiObjectsCollection::getInstance()->getObject($user_id);

				$this->orders = $user->getValue("orders_refs");
			}
		} else {
			$this->is_auth = false;
			$this->user_id = false;

			if(!is_array($_SESSION['orders'])) {
				$_SESSION['orders'] = Array();
			}

			$this->orders = $_SESSION['orders'];
		}

		$this->getCartObject();

	}

	private function getCartObject() {
		if($this->cart) return $this->cart;

		foreach($this->orders as $order_id) {
			$order = umiObjectsCollection::getInstance()->getObject($order_id);
			if(!$order) continue;

			list($status) = $order->getValue("status");
			$status = $this->idToStatus($status);
			if(!$status) continue;

			$id_name = $status->getValue("id_name");

			if($id_name == "cart") {
				return $this->cart = $order;
			}
		}
		$this->createCartObject();
	}

	private function createCartObject() {
		$type_id = umiObjectTypesCollection::getInstance()->getBaseType("eshop", "order");
		$order_id = umiObjectsCollection::getInstance()->addObject("tmp", $type_id);

		$order = umiObjectsCollection::getInstance()->getObject($order_id);
		$order->getPropByName("status")->setValue(Array($this->statusToId("cart")));


		$order->commit();

		$this->orders[] = $order_id;
	}


	public function statusToId($status_name) {
		$type_id = umiObjectTypesCollection::getInstance()->getBaseType("eshop", "order_status");


		$sel = new umiSelection;

		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);

		$result = umiSelectionsParser::runSelection($sel);

		foreach($result as $status_id) {
			$status = umiObjectsCollection::getInstance()->getObject($status_id);

			if($status->getPropByName("id_name")->getValue() == $status_name) {
				return $status_id;
			}
		}
		return false;
	}

	public function idToStatus($status_id) {
		return umiObjectsCollection::getInstance()->getObject($status_id);
	}




	public function json_add_to_cart() {
		$element_id = (int) $_REQUEST['param0'];

		return $this->addToCart($element_id);
	}


	public function addToCart($element_id) {
		$res = "";


		$order_id = $this->cart->getId();
		$res = $this->addItemToOrder($order_id, $element_id);
		$res = $this->basket_refresh();

		$this->flush($res);
	}

	private function getOrderItem($order_id, $element_id) {
		$order = umiObjectsCollection::getInstance()->getObject($order_id);
		$items = $order->getPropByName("items")->getValue();

		foreach($items as $item_id) {
			$item = umiObjectsCollection::getInstance()->getObject($item_id);
			$rel = $item->getPropByName("catalog_relation")->getValue();

			if($rel == $element_id) {
				return $item;
			}
		}


		$element = umiHierarchy::getInstance()->getElement($element_id);

		$item_name = $element->getName();

		$type_id = umiObjectTypesCollection::getInstance()->getBaseType("eshop", "order_item");
		$item_id = umiObjectsCollection::getInstance()->addObject($item_name, $type_id);
		$item = umiObjectsCollection::getInstance()->getObject($item_id);

		$item->setValue("price_item", $element->getValue("cena"));
		$item->setValue("count", 0);
		$item->setValue("price_total", 0);
		$item->setValue("discount_size", 0);
		$item->setValue("catalog_relation", Array($element_id));

		$item->commit();


		$items[] = $item;

		$order->setValue("items", $items);
		$order->commit();

		return $item;
	}

	private function recountOrderItem($item) {
		$price = $item->getPropByName("price_item")->getValue();
		$count = $item->getPropByName("count")->getValue();

		$price_total = $price * $count;

		$item->getPropByName("price_total")->setValue($price_total);

		$item->commit();
	}


	public function addItemToOrder($order_id, $element_id) {
		$res = "";

		$item = $this->getOrderItem($order_id, $element_id);
		$item_id = $item->getId();

		$count = $item->getPropByName("count")->getValue();
		$item->getPropByName("count")->setValue(++$count);
		$this->recountOrderItem($item);
		$item->commit();

		return $res;
	}


	public function basket($template = "default") {
		if(!$template) $template = "default";
		list($template_basket) = def_module::loadTemplates("tpls/eshop/{$template}.tpl", "basket");


		$block_arr = Array();

		$price_total = 0;
		$items_total = 0;


		if(!$this->cart) {
			$this->getCartObject();
		}

		$this->cart->update();
		$cart = $this->cart;
		$items = $cart->getPropByName("items")->getValue();
		foreach($items as $item_id) {
			$item = umiObjectsCollection::getInstance()->getObject($item_id);

			$price_total += $item->getValue("price_total");
			$items_total += $item->getValue("count");
		}

		$block_arr['items_num'] = $items_total;
		$block_arr['total_price'] = $price_total;
		$block_arr['link'] = $this->pre_lang . "/eshop/my_cart/";

		return self::parseTemplate($template_basket, $block_arr);
	}

	public function basket_refresh() {
		$res = mysql_escape_string($this->basket('short'));
		$res = "catalog_refreshBasket(\"$res\");";
		$this->flush($res);
	}




	public function my_cart($template = "default") {
		if(!$template) $template = "default";
		list($template_my_cart, $template_orderlist_block, $template_orderlist_item, $template_my_cart_noitems, $template_credit_link, $template_discount_card) = def_module::loadTemplates("tpls/eshop/{$template}.tpl", "my_cart", "orderlist_block", "orderlist_item", "my_cart_noitems", "credit_link", "discount_card");


		$cardnum = $this->getCardNum();
		$dcard_discount = (int) $this->calcCardDiscount($cardnum);


		$price_order = 0;
		$price_total = 0;
		$items_str = "";

		$items = $this->cart->getPropByName("items")->getValue();
		foreach($items as $item_id) {
			$spec = "&nbsp;" . '<img src="/images/mt_special_price.gif" border="0" alt="Специальная цена" title="Специальная цена" width="38" height="25" />';

			$line_arr = Array();

			$item = umiObjectsCollection::getInstance()->getObject($item_id);

			$element_id = $item->getValue("catalog_relation");

			$element = umiHierarchy::getInstance()->getElement($element_id);

			if(!$element) continue;

			$line_arr['title'] = $item->getName();
			$line_arr['price'] = $item->getValue("price_item");
			$price_total += $line_arr['price_total'] = $item->getValue("price_total");
			$line_arr['num'] = $item->getValue("count");
			$line_arr['order_item_id'] = $item->getId();

			$line_arr['spec'] = ($element->getValue("fiksirovannaya_cena")) ? $spec : "";
			$line_arr['link'] = umiHierarchy::getInstance()->getPathById($element_id);

			$line_arr['status'] = "В корзине";

			$items_str .= self::parseTemplate($template_orderlist_item, $line_arr);
		}

		$price_deliver = 0;


		$price_order = $price_total + $price_deliver;

		$credit_link = ($this->hasAllowedCredits($price_total)) ? $template_credit_link : "";

		$from = Array('%items%', '%credit_link%');
		$to = Array($items_str, $credit_link);
		$orderlist = str_replace($from, $to, $template_orderlist_block);


		$block_arr = Array();
		$block_arr['orderlist'] = $orderlist;
		$block_arr['discount_card'] = $discount_card;
		$block_arr['price_order'] = $price_order;
		$block_arr['price_deliver'] = $price_deliver;
		$block_arr['price_total'] = $price_total;
		$block_arr['cardnum'] = $cardnum;
		$block_arr['dcard_discount'] = $dcard_discount . "%";
		$block_arr['order_link_disabled'] = $order_link_disabled;

		$res = self::parseTemplate($template_my_cart, $block_arr);
		return $res;
	}


	public function json_basket_del() {
		$order_item_id = (int) $_REQUEST['param0'];


		umiObjectsCollection::getInstance()->delObject($order_item_id);

		$res = $this->json_recount_cart();
		$this->flush($res);
	}

	public function json_update_cart() {
		$order_item_id = (int) $_REQUEST['param0'];
		$num = (int) $_REQUEST['param1'];
		if($num < 0) $num = 0;

		$item = umiObjectsCollection::getInstance()->getObject($order_item_id);
		$count = $item->getPropByName("count")->getValue();
		$item->getPropByName("count")->setValue($num);
		$this->recountOrderItem($item);
		$item->commit();

		$res = $this->json_recount_cart();
		$this->flush($res);
	}


	public function json_recount_cart() {
		$res = "var res = Array();\r\n";

		$price_total = 0;
		$order_total = 0;

		$items = $this->cart->getValue("items");
		foreach($items as $item_id) {
			$item = umiObjectsCollection::getInstance()->getObject($item_id);

			if(!$item) continue;

			$price_total += $item->getValue("price_total");	//TODO ???
			$order_total += $item->getValue("price_total");

			$res .= "res[" . $item->getId() . "] = Array('" . $item->getValue("count") . "', '" . $item->getValue("price_total") . "');\r\n";
		}


		$res .= "res['order_total'] = {$order_total};\r\n";
		$res .= "res['total'] = {$price_total};\r\n";
		$res .= "res['dcard_discount'] = '{$dcard_discount}';\r\n";

		$res .= "callback_cartUpdate(res);";
		return $res;
	}


	public function order_do($template = "default") {
		if(!$template) $template = "default";
		$template = "mails_order"; //TEMP

		$delivery_address_id = $_REQUEST['delivery_address'];
		$customer_comment = $_REQUEST['customer_comment'];

		$this->my_cart_save(false);

		if(!$this->is_auth) return "%users auth_only()%";

		$user = umiObjectsCollection::getInstance()->getObject($this->user_id);

		$order_time = time();

		$cart = $this->cart;
		$order_id = $cart->getId();

		//Sending e-mail to customer
		list($template_block) = def_module::loadTemplates("tpls/eshop/{$template}.tpl", "customer_order_do_block");
		$block_arr = Array();
		$block_arr['user_id'] = $this->user_id;
		$block_arr['order_id'] = $order_id;
		$block_arr['order_time'] = $order_time;

		$block_arr['domain'] = $domain = $_SERVER['HTTP_HOST'];

		$mail_content = self::parseTemplate($template_block, $block_arr);



		$fio = $user->getValue("lname") . " " . $user->getValue("fname") . " " . $user->getValue("father_name");
		$email = $user->getValue("e-mail");

		$email_from = regedit::getInstance()->getVal("//settings/email_from");
		$fio_from = regedit::getInstance()->getVal("//settings/fio_from");


		$some_img = new umiImageFile("./images/mt_logo.gif");

		$someMail = new umiMail();
		$someMail->addRecipient($email, $fio);
		$someMail->setFrom($email_from, $fio_from);
		$someMail->setSubject("Ваш заказ #" . $order_id);
		$someMail->setPriorityLevel("hight");
		$someMail->attachFile($some_img);
		$someMail->setContent($mail_content);
		$someMail->commit();
		$someMail->send();

		//Sending e-mail to administrators
		//TODO: write


		//Changing order status
		//TODO: uncomment
		$cart->setValue("status", Array($this->statusToId("wait")));
		$cart->setValue("order_time", $order_time);
		$cart->setValue("delivery_address", $delivery_address_id);
		$cart->setValue("customer_comments", $customer_comment);
		$cart->commit();
		$this->cart = NULL;
		$this->getCartObject();

		$this->redirect($this->pre_lang . "/eshop/my_orders/");
	}


	public function getUserIdByOrderId($order_id) {
		$type_id = umiObjectTypesCollection::getInstance()->getBaseType("users", "user");
		$type = umiObjectTypesCollection::getInstance()->getType($type_id);
		$orders_refs_field_id = $type->getFieldId("orders_refs");

		$sel = new umiSelection;

		$sel->setObjectTypeFilter();
		$sel->addObjectType($type_id);

		$sel->setPropertyFilter();
		$sel->addPropertyFilterEqual($orders_refs_field_id, $order_id);

		$sel->setLimitFilter();
		$sel->addLimit(1);

		$result = umiSelectionsParser::runSelection($sel);

		return (int) current($result);
	}


	public function calculateOrderPrice($order_id) {
		$order = umiObjectsCollection::getInstance()->getObject($order_id);
		if(!$order) return false;

		$items = $order->getValue("items");
		foreach($items as $item_id) {
			$item = umiObjectsCollection::getInstance()->getObject($item_id);

			if(!$item) continue;

			$price_total += $item->getValue("price_total");	//TODO ???
			$order_total += $item->getValue("price_total");
		}
		return $order_total;
	}
	
	public function config() {
		if(class_exists("__eshop")) return __eshop::config();
	}
}
?>