<?php
	abstract class __search_catalog {
		public function parseSearchRelation(umiField $field, $template) {
			$block_arr = Array();

			$name = $field->getName();
			$title = $field->getTitle();

			$guide_id = $field->getGuideId();
			$guide_items = umiObjectsCollection::getGuidedItems($guide_id);


			$value = $_REQUEST['fields_filter'][$name];

			$items = "";
			foreach($guide_items as $object_id => $object_name) {
				$selected = ($object_id == $value) ? "selected" : "";

				$items .= <<<ITEM
	<option value="{$object_id}" {$selected}>{$object_name}</option>

ITEM;
			}


			$block_arr['name'] = $name;
			$block_arr['title'] = $title;
			$block_arr['items'] = $items;
			return def_module::parseTemplate($template, $block_arr);
		}

		public function parseSearchText(umiField $field, $template) {
			$block_arr = Array();

			$name = $field->getName();
			$title = $field->getTitle();

			$value = (string) $_REQUEST['fields_filter'][$name];


			$block_arr['name'] = $name;
			$block_arr['title'] = $title;
			$block_arr['value'] = self::protectStringVariable($value);
			return def_module::parseTemplate($template, $block_arr);
		}


		public function parseSearchPrice(umiField $field, $template) {
			$block_arr = Array();

			$name = $field->getName();
			$title = $field->getTitle();

			$value = (array) $_REQUEST['fields_filter'][$name];


			$block_arr['name'] = $name;
			$block_arr['title'] = $title;
			$block_arr['value_from'] = self::protectStringVariable($value[0]);
			$block_arr['value_to'] = self::protectStringVariable($value[1]);
			return def_module::parseTemplate($template, $block_arr);
		}


		public function parseSearchBoolean(umiField $field, $template) {
			$block_arr = Array();

			$name = $field->getName();
			$title = $field->getTitle();

			$value = (array) $_REQUEST['fields_filter'][$name];


			$block_arr['name'] = $name;
			$block_arr['title'] = $title;
			$block_arr['checked'] = ((bool) $value[0]) ? " checked" : "";
			return def_module::parseTemplate($template, $block_arr);
		}








		public function applyFilterText(umiSelection $sel, umiField $field, $value) {
			if(empty($value)) return false;

			$sel->addPropertyFilterLike($field->getId(), $value);
		}

		public function applyFilterInt(umiSelection $sel, umiField $field, $value) {
			if(empty($value)) return false;

			$sel->addPropertyFilterEqual($field->getId(), $value);
		}

		public function applyFilterRelation(umiSelection $sel, umiField $field, $value) {
			if(empty($value)) return false;

			$sel->addPropertyFilterEqual($field->getId(), $value);
		}

		public function applyFilterPrice(umiSelection $sel, umiField $field, $value) {
			if(empty($value)) return false;

			if($value[1]) {
				$sel->addPropertyFilterBetween($field->getId(), $value[0], $value[1]);
			} else {
				if($value[0]) {
					$sel->addPropertyFilterMore($field->getId(), $value[0]);
				}
			}
		}

		public function applyFilterBoolean(umiSelection $sel, umiField $field, $value) {
			if(empty($value)) return false;

			if($value) {
				$sel->addPropertyFilterEqual($field->getId(), $value);
			}
		}


		public static function protectStringVariable($stringVariable = "") {
			$stringVariable = htmlspecialchars($stringVariable);
			return $stringVariable;
		}
	};
?>