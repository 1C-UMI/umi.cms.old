<?php
	abstract class __tickets_content {

		public function json_add_ticket () {

			$requestId = (int) $_REQUEST['requestId'];

			$x = (int) $_REQUEST['x'];
			$y = (int) $_REQUEST['y'];
			$width = (int) $_REQUEST['width'];
			$height = (int) $_REQUEST['height'];

			$url = $_SERVER['HTTP_REFERER'];

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("content", "ticket");

			$user_id = cmsController::getInstance()->getModule("users")->user_id;

			$object_id = umiObjectsCollection::getInstance()->addObject("Ticket", $object_type_id);

			$object = umiObjectsCollection::getInstance()->getObject($object_id);

			$object->setValue("x", $x);
			$object->setValue("y", $y);
			$object->setValue("width", $width);
			$object->setValue("height", $height);

			$object->setValue("url", $url);
			$object->setValue("user_id", $user_id);
			$object->setValue("message", "Новая заметка");
			$object->commit();

			$res = <<<END

var response = new lLibResponse({$requestId});
response.ticketId = '{$object_id}';
lLib.getInstance().makeResponse(response);

END;

			$this->flush($res);
		}

		public function json_del_ticket () {
			$ticket_id = (int) $_REQUEST['ticket_id'];

			umiObjectsCollection::getInstance()->delObject($ticket_id);
			$this->flush();
		}


		public function json_get_tickets () {
			$requestId = (int) $_REQUEST['requestId'];
			$url = $_SERVER['HTTP_REFERER'];

			if(!system_is_allowed("content", "tickets")) {
				exit();
			}

			$object_type_id = umiObjectTypesCollection::getInstance()->getBaseType("content", "ticket");

			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);
			$url_field_id = $object_type->getFieldId("url");

			$sel = new umiSelection;

			$sel->setObjectTypeFilter();
			$sel->addObjectType($object_type_id);

			$sel->setPropertyFilter();
			$sel->addPropertyFilterEqual($url_field_id, $url);

			$tickets = umiSelectionsParser::runSelection($sel);

			$user_id = cmsController::getInstance()->getModule("users")->user_id;;
			$user = umiObjectsCollection::getInstance()->getObject($user_id);
			$user_login = $user->getValue("login");
			$user_fio = $user->getValue("lname") . " " . $user->getValue("fname") . " " . $user->getValue("father_name");

			$res = <<<END

var response = new lLibResponse({$requestId});
response.tickets = new Array();
response.userId = '{$user_id}';
response.userLogin = '{$user_login}';
response.userFIO = '{$user_fio}';

END;

			foreach($tickets as $ticket_id) {
				$ticket = umiObjectsCollection::getInstance()->getObject($ticket_id);

				$message = mysql_real_escape_string($ticket->getValue("message"));
				$message = str_replace("\n", "\\n", $message);

				$x = $ticket->getValue("x");
				$y = $ticket->getValue("y");
				$width = $ticket->getValue("width");
				$height = $ticket->getValue("height");


				$user_id = $ticket->getValue("user_id");

				if(!$user_id) {
					umiObjectsCollection::getInstance()->delObject($ticket_id);
					continue;
				}


				$user = umiObjectsCollection::getInstance()->getObject($user_id);
				$user_login = $user->getValue("login");
				$user_fio = $user->getValue("lname") . " " . $user->getValue("fname") . " " . $user->getValue("father_name");


				$res .= <<<END
response.tickets[response.tickets.length] = new Array('{$ticket_id}', '{$user_id}', '{$user_login}', '{$user_fio}', '{$x}', '{$y}', '{$width}', '{$height}', '{$message}');

END;
			}


			$res .= <<<END

lLib.getInstance().makeResponse(response);

END;



			$this->flush($res);
		}

		public function json_update_ticket () {
			$ticket_id = (int) $_REQUEST['ticket_id'];
			$message = (string) $_REQUEST['message'];

			$message = str_replace("\\n", "\n", $message);

			$ticket = umiObjectsCollection::getInstance()->getObject($ticket_id);
			$ticket->setValue("message", $message);
			$ticket->commit();

			$this->flush();
		}

	};
?>