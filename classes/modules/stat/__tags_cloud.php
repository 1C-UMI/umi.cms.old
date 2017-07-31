<?php
	abstract class __tags_cloud_stat {

		public function tagsCloud($template = "default", $limit = 50, $max_font_size = 16) {
			if(!$template) $template = "default";
			if(!$limit) $limit = 50;
			if(!$max_font_size) $max_font_size = 16;


			list($template_block, $template_line, $template_separator) = def_module::loadTemplates("./tpls/stat/{$template}.tpl", "tags_block", "tags_block_line", "tags_separator");

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('allTags');
			$report = $factory->get('allTags');

			$report->setStart(time() - 3600*24*7);	//TODO: Fix to real dates
			$report->setFinish(time() + 3600*24);	//TODO: Fix to real dates


			$result = $report->get();
			$max = $result['max'];

			$lines = "";

			$i = 0;
			$sz = sizeof($result['labels']);
			for($i = 0; $i < $sz; $i++) {
				$label = $result['labels'][$i];
				$line_arr = Array();

				$tag = $label['tag'];
				$cnt = $label['cnt'];

				$line_arr['tag'] = $tag;
				$line_arr['cnt'] = $cnt;
				$line_arr['separator'] = ($i < $sz - 1) ? $template_separator : "";
				$line_arr['font_size'] = ceil($max_font_size * ($cnt / $max));

				$lines .= def_module::parseTemplate($template_line, $line_arr);
			}

			$block_arr = Array();
			$block_arr['lines'] = $lines;
			return def_module::parseTemplate($template_block, $block_arr);
		}
	};

?>