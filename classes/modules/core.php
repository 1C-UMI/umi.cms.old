<?php
class core {

	public function cms_callMethod($method_name, $args) {
		$res = call_user_method_array($method_name, $this, $args);
		return $res;	//no need anymore to check if method exists. (look "__call" function)
	}

	public function __toString() {
		return "umi.__core";
	}

	# Вставка правильного урла для вызова явовского скрипта
	public function insert_correct_url($func="", $param="", $url="/", $name_url="ссылка"){
		
		$arr=array();
		
		$arr=split("\|", $param);
		$soa=sizeof($arr);
		for($i=0; $i<$soa; $i++){
			$arr[$i]=str_replace("!", "'", $arr[$i]);
			$arg.=$arr[$i];
			if($i<$soa-1){
				$arg.=",";
			}
		}
		
		if(!empty($func)){
			$res="<a href=\"".$url."\" onclick=\"javascript: ".$func."(".$arg."); return false;\">".$name_url."</a>";
		}else{
			$res="";
		}
		return $res;
	}

	public function navibar($tpl_name = "default", $full = true, $offsetLeft = 0, $offsetRight = 0) {
		if (!strlen($tpl_name)) $tpl_name = "default";
		$arr = Array();

		if($content = cmsController::getInstance()->getModule("content")) {}
		else
			return "";


		$templates = $content->get_navibar_template($tpl_name);

		$tpl_block = $templates['navibar'];
		$tpl_block_empty = $templates['navibar_empty'];
		$tpl_element = $templates['element'];
		$tpl_element_active = $templates['element_active'];
		$tpl_quant = $templates['quantificator'];


		$element_id = cmsController::getInstance()->getCurrentElementId();


		$parents = umiHierarchy::getInstance()->getAllParents($element_id);
		$parents[] = $element_id;

		foreach($parents as $celement_id) {
			if(!$celement_id) continue;

			if($element = umiHierarchy::getInstance()->getElement($celement_id)) {
				$name = $element->getObject()->getName();
				$path = umiHierarchy::getInstance()->getPathById($celement_id);

				$arr[] = Array($name, $path);
			} else {
				break;
			}
		}

		$res = "";
		$sz = sizeof($arr) - $offsetRight;
		for($i = $offsetLeft; $i < $sz; $i++) {
			$ctpl = $tpl_element;
			$from = Array('%text%', '%link%');
			$to = Array($arr[$i][0], $arr[$i][1]);

			if($i != ($sz-1)) {
				$res .= str_replace($from, $to, $ctpl) . $tpl_quant;
			} else {
				if(!$full)
					$ctpl = $tpl_element_active;
				$res .= str_replace($from, $to, $ctpl);
			}
		}

		$template = $sz > 0 ? $tpl_block : $tpl_block_empty;

		return str_replace("%elements%", $res, $template);
	}


	public function insertCut($template = "default") {
		if(!$template) $template = "default";

		$pages = $_REQUEST['cut_pages'];
		$curr_page = ((int) $_REQUEST['cut_curr_page']) + 1;

		if($pages > 1) {
			return "%system numpages('{$pages}', '1', '{$template}', 'cut')%";
		} else {
			return "";
		}
	}

	public function curr_module() {
		if(cmsController::getInstance()->getCurrentModule() == "config" && cmsController::getInstance()->getCurrentMethod() == "mainpage") {
			return "";
		}

		if(cmsController::getInstance()->getCurrentModule() == "data" && cmsController::getInstance()->getCurrentMethod() == "trash") {
			return "trash";
		}

		return cmsController::getInstance()->getCurrentModule();
	}


	public function insertPopup($text = "", $src = "") {
		$res = $text;

		$path = (substr($src, 0, 1) == "/") ? "." . $src : $src;
		if(is_file($path)) {
			$isz = getimagesize($path);
			if(is_array($isz)) {
				list($width, $height) = $isz;
				$res = "<a href=\"$src\" onclick=\"javascript: return gen_popup('$src', '$width', '$height');\" class=\"umi_popup\">" . $text . "</a>";
			}
		}
		return $res;
	}

	public function insertThumb($src1 = "", $src2 = "", $alt = "") {
		$path2 = (substr($src2, 0, 1) == "/") ? "." . $src2 : $src2;

		$thumb = "<img src=\"$src1\" border=\"0\" class=\"umi_thumb\" alt=\"{$alt}\" title=\"{$alt}\" />";

		if(is_file($path2)) {
			$isz = getimagesize($path2);
			if(is_array($isz)) {
				list($width, $height) = $isz;
				$res = "<a href=\"$src2\" onclick=\"javascript: return gen_popup('$src2', '$width', '$height');\">" . $thumb . "</a>";
			}
		}
		return $res;
	}
	
	
	public function getTypeEditLink($type_id) {
		if(system_is_allowed("data", "type_edit")) {
			if($type = umiObjectTypesCollection::getInstance()->getType($type_id)) {
				$type_name = $type->getName();
				return "<i>(<![CDATA[{$type_name}]]>, <a href='%pre_lang%/admin/data/type_edit/{$type_id}'><![CDATA[редактировать тип]]></a>)</i>";
			} else {
				return "";
			}
		} else {
			return "";
		}
	}

};

?>