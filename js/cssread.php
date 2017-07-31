<?php
$css_path = "./style.css";
error_reporting(0);
function parse_css($css_path) {
	global $g_css_content;
	$styles = Array();

	if(!is_file($css_path)) return false;
	$css_content = file_get_contents($css_path);
	$g_css_content = $css_content;

	$pattern = "/\/\*(.*)\*\//";
	preg_match_all($pattern, $css_content, $ss);
	$ss = $ss[1];
	foreach($ss as $style_element) {
		$style_element = trim($style_element);

		$type = "";
		list($type, $element, $alias) = split("->", $style_element);


		if($type == "style") {
			list($stag, $sclass)  = split("\.", $element);

			$styles[] = Array(
						"alias" => $alias,
						"tag" => $stag,
						"class" => $sclass
					);
		}
	}
	
	$g_css_content .= <<<CSS
virtual-property {
	color:	#5A5A5A;
	font-style: italic;
}
CSS;
	return $styles;
}

?>