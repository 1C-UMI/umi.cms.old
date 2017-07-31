<?php

include "../mysql.php";

include "../errors.php";		//С…СЌРЅРґР»РµСЂ РѕС€РёР±РѕРє. РїРѕРєР° UC

include '../security.php';

include "../classes/patterns/iSingletone.php";
include "../classes/patterns/singletone.php";

include "../classes/patterns/iUmiEntinty.php";
include "../classes/patterns/umiEntinty.php";

include '../classes/systemCore/regedit/iRegedit.php';
include '../classes/systemCore/regedit/regedit.php';	//СЂРµРµСЃС‚СЂ!!!

include "../classes/memcachedController/iMemcachedController.php";
include "../classes/memcachedController/memcachedController.php";

include "../classes/hierarchyModel/iLang.php";
include "../classes/hierarchyModel/lang.php";

include "../classes/hierarchyModel/iLangsCollection.php";
include "../classes/hierarchyModel/langsCollection.php";


$s_referer = trim($_SERVER['HTTP_REFERER'], "/");

$o_lang = langsCollection::getInstance()->getDefaultLang();

$arr_referer = explode("/", $s_referer);

if (isset($arr_referer[3])) {
	$s_lang = $arr_referer[3];
	if ($i_lang_id = langsCollection::getInstance()->getLangId($s_lang)) {
		$o_lang = langsCollection::getInstance()->getLang($i_lang_id);
	}
}
echo "\nvar pre_lang = '".$o_lang->getPrefix()."';\n";

?>



var is_ie = !(navigator.appName.indexOf("Netscape") != -1);

function includeJS(src) {
	if(document.getElementsByTagName && document.createElement) {
		var head = document.getElementsByTagName('head')[0];

		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = src;

		head.appendChild(script);

		return true;
	} else {
		return false;
	}
}

includeJS("/js/lLib.js");
includeJS("/js/custom.js");
includeJS("/js/client/cookie.js");
includeJS("/js/client/catalog.js");
includeJS("/js/client/stat.js");
includeJS("/js/client/vote.js");
includeJS("/js/client/users.js");
includeJS("/js/client/eshop.js");
includeJS("/js/client/forum.js");
includeJS("/js/client/mouse.js");
includeJS("/js/client/quickEdit.js");
includeJS("/js/client/qPanel.js");
includeJS("/js/client/umiTicket.js");
includeJS("/js/client/umiTickets.js");
includeJS("/js/client/floatReferers.js");



document.onkeydown = function(e) {
        var is_ie = !(navigator.appName.indexOf("Netscape") != -1);

	if(!is_ie)
		event = e;

	if(event.keyCode == 27) {
		quickEdit.getInstance().hide();
	}

	if (event.shiftKey && event.keyCode == 68) {
		quickEdit.getInstance().show();
	}

	if (event.shiftKey && event.keyCode == 67) {
		umiTickets.getInstance().beginCreatingTicket();
	}


	if(event.ctrlKey) {
		if(event.keyCode == 37) {
			var obj = document.getElementById('toprev');
			if(obj) {
				document.location = obj.toString();
			}
		}

		if(event.keyCode == 39) {
			var obj = document.getElementById('tonext');
			if(obj) {
				document.location = obj.toString();
			}
		}

		if(event.keyCode == 36) {
			var obj = document.getElementById('tobegin');
			if(obj) {
				document.location = obj.toString();
			}
		}

		if(event.keyCode == 35) {
			var obj = document.getElementById('toend');
			if(obj) {
				document.location = obj.toString();
			}
		}
	}
}