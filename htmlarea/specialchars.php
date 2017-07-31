<?php

function gen_chars() {
	$chars = Array();
	$chars[] = "&nbsp;";
	$chars[] = "&#150;";
	$chars[] = "&#151;";
	$chars[] = "&#036;";
	$chars[] = "&#128;";
	$chars[] = "&#132;";
	$chars[] = "&#147;";
	$chars[] = "&#148;";
	$chars[] = "&#171;";
	$chars[] = "&#187;";
	$chars[] = "&#035;";
	$chars[] = "&#038;";
	$chars[] = "&#037;";
	$chars[] = "&#133;";
	$chars[] = "&#169;";
	$chars[] = "&#174;";
	$chars[] = "&#153;";
	$chars[] = "&#167;";
	$chars[] = "&#162;";

	$chars[] = "&#176;";


	$res = "\t<tr>\r\n";

	$sz = sizeof($chars);
	for($i = 0; $i < $sz; $i++) {
		$line = "";
		$char = $chars[$i];

		$char = "<a href='#'>" . $char . "</a>";


		$line = "\t\t<td onclick=\"javacsript: returnChar('" . str_replace("&", "&amp;", $chars[$i]) . "')\">\r\n\t\t\t" . $char . "\r\n\t\t</td>\r\n";

		if(($i % 5) == 4) {


			$line = $line . "\t</tr>\r\n" . "\t<tr>\r\n";

		}
		

		$res .= $line;
	}

	$l = $i%5;
	for(; $l < 5; $l++) {
		$res .= "\t\t<td></td>\r\n";
		$t = true;
	}
	if($t)
		$res.= "\t</tr>\r\n";

$d1 = "\t<tr>\r\n\t\t<td></td>\r\n\t\t<td></td>\r\n\t\t<td></td>\r\n\t\t<td></td>\r\n\t\t<td></td>\r\n\t</tr>";

	$res = str_replace($d1, "", $res);

	return $res;
}

?>
<html>
	<head>
		<title>Спецсимволы</title>
<style>
table {
	background-color: #CCC;
	width: 220px;
	height: 170px;
	border: 0px;
}
td {
	background-color: 	#FFF;
	text-align:		center;
	width: 			40px;
	height:			37px;
	cursor:			hand;

	font-weight: bold;
}

a {
	text-decoration: none;
	background-color: #FFF;
	font-family: Verdana;
	font-size: 13px;
	color: #000;
	
}
</style>

<style type="text/css">
html, body {
  background: ButtonFace;
  color: ButtonText;
  font: 11px Tahoma,Verdana,sans-serif;
  margin: 0px;
  padding: 0px;
}
body { padding: 5px; }
table {
  font: 11px Tahoma,Verdana,sans-serif;
}
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
table .label { text-align: right; width: 8em; }

.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}

#buttons {
      margin-top: 1em; border-top: 1px solid #999;
      padding: 2px; text-align: right;
}
</style>

<script>
	function returnChar(cchar) {
		var umiPopup = window.parent.umiPopup.getSelf();
		umiPopup.setReturnValue(cchar);
		umiPopup.callCallbackFunction();
		umiPopup.close();
	}


	function onClose() {
		window.parent.umiPopup.getSelf().close();
	}

	document.onkeydown = function(e) {
        	var is_ie = !(navigator.appName.indexOf("Netscape") != -1);

		if(!is_ie) {
			event = e;
		}

		if(event.keyCode == 27) {
			onClose();
		}
	}
</script>
	</head>

	<body style="margin: 0px;">

<div class="title">Выберите символ</div>

<table>
<?php echo gen_chars() ?>
</table>

<div id="buttons">
  <button type="button" name="cancel" onclick="return onClose();">Cancel</button>
</div>

	</body>
</html>