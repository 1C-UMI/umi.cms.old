<?php
	abstract class __search_search {
		public function runSearch($str) {
			$words_temp = split(" ", $str);	//TODO
			$words = Array();

			foreach($words_temp as $word) {
				if(strlen($word) >= 3) {
					$words[] = $word;
				}
			}

			$elements = __search_search::buildQueries($words);

			return $elements;
		}

		public static function buildQueries($words) {
			$lang_id = cmsController::getInstance()->getCurrentLang()->getId();
			$domain_id = cmsController::getInstance()->getCurrentDomain()->getId();

			$words_cond = "";
			foreach($words as $word) {
				$word = mysql_real_escape_string($word);
				$word = str_replace(Array("%", "_"), Array("\\%", "\\_"), $word);
//				$words_cond .= "siw.word = '{$word}' OR ";
				$word_base = language_morph::get_word_base($word);
				$words_cond .= "siw.word LIKE '{$word}%' OR ";
				$words_cond .= "siw.word LIKE '{$word_base}%' OR ";
			}

			$words_cond = substr($words_cond, 0, strlen($words_cond) - 4);

			$sql = <<<SQL

SELECT SQL_SMALL_RESULT HIGH_PRIORITY SQL_CACHE s.rel_id

	FROM	cms3_search_index_words siw,
		cms3_search_index si,
		cms3_search s,
		cms3_hierarchy h

			WHERE	({$words_cond}) AND
				si.word_id = siw.id AND
				s.rel_id = si.rel_id AND
				s.domain_id = '{$domain_id}' AND
				s.lang_id = '{$lang_id}' AND
				h.id = s.rel_id AND
				h.is_deleted = '0' AND
				h.is_active = '1'

					GROUP BY si.rel_id
						ORDER BY si.weight DESC


SQL;


			$res = Array();

			$result = mysql_query($sql);
			while(list($element_id) = mysql_fetch_row($result)) {

				$res[] = $element_id;
			}

			return $res;
		}

		public function prepareContext($element_id) {
			if(!($element = umiHierarchy::getInstance()->getElement($element_id))) {
				return false;
			}

			if($element->getValue("is_unindexed")) return false;

			$context = "";

			$type_id = $element->getObject()->getTypeId();
			$type = umiObjectTypesCollection::getInstance()->getType($type_id);

			$field_groups = $type->getFieldsGroupsList();
			foreach($field_groups as $field_group_id => $field_group) {
				foreach($field_group->getFields() as $field_id => $field) {
					if($field->getIsInSearch() == false) continue;

					$field_name = $field->getName();

					$val = $element->getValue($field_name);
					if(is_null($val) || !$val) continue;

					$context .= $val . "\n\n";
				}
			}
			return $context;
		}


		public function getContext($element_id, $search_string) {
			$content = $this->prepareContext($element_id);

			$pattern_sentence = "/[A-ZА-Я]*([^\.^\?^!.])*$word([^\.^\?^!.])*[[!\.\?]+[ ]+]/im";
			$content = preg_replace("/%content redirect\((.*)\)%/im", "::CONTENT_REDIRECT::\\1::", $content);
			$content = preg_replace("/%[A-z]+ [A-z]+\((.*)\)%/im", "", $content);

			$bt = "<b>";
			$et = "</b>";


			$words_arr = split(" ", $search_string);


			$content = preg_replace("/([A-zА-я0-9])\.([A-zА-я0-9])/im", "\\1&#46;\\2", $content);

			$context = str_replace(">", "> ", $content);
			$context = str_replace("<br>", " ", $context);
			$context = str_replace("&nbsp;", " ", $context);
			$context = str_replace("\n", " ", $context);
			$context = strip_tags($context);


			if(preg_match_all("/::CONTENT_REDIRECT::(.*)::/i", $context, $temp)) {
				$sz = sizeof($temp[1]);

				for($i = 0; $i < $sz; $i++) {
					if(is_numeric($temp[1][$i])) {
						$turl = cmsController::getInstance()->getModule('content')->get_page_url($temp[1][$i]);
						$turl = umiHierarchy::getInstance()->getPathById($temp[1][$i]);
						$turl = trim($turl, "'");
						$res = str_replace($temp[0][$i], "<p>%search_redirect_text% \"<a href='$turl'>$turl</a>\"</p>", $context);
					} else {
						$turl = strip_tags($temp[1][$i]);
						$turl = trim($turl, "'");
						$context = str_replace($temp[0][$i], "<p>%search_redirect_text% <a href=\"" . $turl . "\">" . $turl . "</a></p>", $context);
					}
				}
			}

			$context .= "\n";


			$res_out = "";

			$lines = Array();
			foreach($words_arr as $cword) {
				if(strlen($cword) <= 1)	continue;

				$tres = $context;
				$sword = morph_get_root($cword);

				$pattern_sentence = "/([^\.^\?^!^<^>.]*)$sword([^\.^\?^!^<^>.]*)[!\.\?\n]/im";
				$pattern_word = "/([^ ^[\.[ ]*]^!^\?^\(^\).]*)($sword)([^ ^\.^!^\?^\(^\).]*)/im";

				preg_match($pattern_sentence, $tres, $tres);
				$lines[] = $tres[0];
			}

			$lines = array_unique($lines);

			$res_out = "";
			foreach($lines as $line) {
				foreach($words_arr as $cword) {
					$sword = morph_get_root($cword);
					$pattern_word = "/([^ ^.^!^\?.]*)($sword)([^ ^.^!^\?.]*)/im";
					$line = preg_replace($pattern_word, $bt . "\\1\\2\\3" . $et, $line);
				}

				if($line) {
					$res_out .= "<p>" . $line . "</p>";
				}
			}

			if(!$res_out) {
				preg_match("/([^\.^!^\?.]*)([\.!\?]*)/im", $context, $res_out);
				$res_out = $res_out[0];
				$res_out = "<p></p>";
			}
			return $res_out;
		}
	};
?>