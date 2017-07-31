<?php
	abstract class __rate_vote {
		public function json_rate($template = "default") {
			if(!$template) $template = "default";

			$element_id = (int) $_REQUEST['param0'];
			$bid = ((bool) $_REQUEST['param1']) ? 1 : -1;


			list($template_ok, $template_not_found, $template_rated) = def_module::loadTemplates("tpls/vote/rate/{$template}.tpl", "rate_ok", "rate_not_found", "rate_rated");

			$block_arr = Array();
			
			$block_arr['request_id'] = $_REQUEST['requestId'];


			if($element = umiHierarchy::getInstance()->getElement($element_id)) {
				if(self::getIsRated($element_id)) {
					$rate_voters = $element->getValue("rate_voters");
					$rate_sum = $element->getValue("rate_sum");

					$res = $template_rated;
				} else {
					$rate_voters = $element->getValue("rate_voters");
					$rate_sum = $element->getValue("rate_sum") + $bid;

					$element->setValue("rate_voters", ++$rate_voters);
					$element->setValue("rate_sum", $rate_sum);

					$element->commit();

					$res = $template_ok;


					self::setIsRated($element_id);
				}


				$block_arr['current_rating'] = $rate_sum / $rate_voters;
			} else {
				$res = $template_not_found;
			}

			$res = def_module::parseTemplate($res, $block_arr, $element_id);

			header("Content-type: text/javascript");
			$this->flush($res);
		}


		public static function getIsRated($element_id) {
			return in_array($element_id, $_SESSION['rated']);
		}


		public static function setIsRated($element_id) {
			if(!is_array($_SESSION['rated'])) {
				$_SESSION['rated'] = Array();
			}
			$_SESSION['rated'][] = (int) $element_id;
		}
	};
?>