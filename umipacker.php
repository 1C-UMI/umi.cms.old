<?php
//exit();
$tmpfname = date("Y_m_d_h_i_s");
define ("COMPRESS_LEVEL", 9);

ini_set("include_path", dirname(__FILE__) . '/');

include "config.php";


function iswin32() {
	if(strtoupper(substr($_ENV["OS"], 0, 3)) == "WIN")
		return true;
	else
		return false;
}


$step = (!empty($_REQUEST['s'])) ? $_REQUEST['s'] : 0;

if(!$step) {
	$C_TITLE = "Начало";
	$C_H1 = "Создание архива. Начало.";
	$C_CONTENT = <<<END

<p>Создание упакованного архива <b>.ucp</b>, который можно использовать для переноса сайта на другой хостинг или резервного восстановления информации.</p>

<input type="button" value="Выбрать файлы >>>" onclick="javascript: nextStep(1)" />

END;
}

if($step == 1) {
	$C_TITLE = "Выбор файлов";
	$C_H1 = "Выберите файлы, которые необходимо упаковать";

	$pwd = dirname(__FILE__);
	$site_tree = "";

	$site_tree = readFiles($pwd);
	

	$C_CONTENT = <<<END
<form method="post"><input type="hidden" name="s" value="2">
$site_tree

<p><input type="submit" value="Собрать архив >>>" /></p>
</form>
END;

}

if($step == 2) {
	$C_TITLE = "Упаковка...";
	$C_H1 = "Создание пакета завершено";

	$files = $_REQUEST['files'];
	$dir =   $_REQUEST['dirs'];

	$pwd = trim(dirname(__FILE__));
	if(substr($pwd, strlen($pwd) - 1, 1) != "/")
		$pwd .= "/";

	$package = Array();

	//now - let's filter dirs, we need...
	$sz = sizeof($dir);
	$allowed_dirs = Array($pwd);
	$res = "";

	$rt = dirname(__FILE__) . "/";
	for($i = 0; $i < $sz; $i++) {
		$cdir = $dir[$i];

		if(if_allowed_dir($cdir, $allowed_dirs)) {
			$allowed_dirs[] = $cdir;
			$cdir = str_replace($rt, "", $cdir);

			$package[] = $cdir;
		}
	}

	$folders_count = $i;

	$sz = sizeof($files);
	for($i = 0; $i < $sz; $i++) {
		$cfile = $files[$i];
		if(if_allowed_file($cfile, $allowed_dirs, true)) {
			$cfile = str_replace($rt, "", $cfile);
			$package[] = $cfile;

		}

	}

	$r = <<<END
#UMI.CMS Autoconfig package-build file
END;

	foreach($package as $cc) {
		$r .= $cc . "\r\n";
	}
	file_put_contents("test.autoconf", $r);

}

function if_allowed_file($cdir, $adirs) {
	$sz = sizeof($adirs);
	for($i = 0; $i < $sz; $i++) {
		$sl = strlen($adirs[$i]);

		if(substr($cdir, 0, $sl) == $adirs[$i]) {
			$cmp = substr($cdir, $sl, strlen($cdir) - $sl);

			if(substr($cmp, 0, 1) == "/")
				$cmp = substr($cmp, 1, strlen($cmp) - 1);

			if(strstr($cmp, "/") === false)
				return true;
		}
	}
	return false;
}

function if_allowed_dir($cdir, $adirs) {
	$sz = sizeof($adirs);
	for($i = 0; $i < $sz; $i++) {
		$sl = strlen($adirs[$i]);
		if(substr($cdir, 0, $sl) == $adirs[$i]) {
			$cmp = substr($cdir, $sl, strlen($cdir) - $sl);
			if(substr($cmp, 0, 1) == "/")
				$cmp = substr($cmp, 1, strlen($cmp) - 1);
			if(strstr($cmp, "/") === false)
				return true;
		}
	}
	return false;
}

function readSql($table_name = "") {
	if(!$table_name)
		return false;

	$res = Array();

	$sql = "SELECT * FROM " . $table_name;
	$result = mysql_query($sql);
	while($row = mysql_fetch_assoc($result)) {
		$sql = "";

		$p1 = "";
		$p2 = "";

		foreach($row as $field => $val) {
			$p1 .= $field . ", ";
			$p2 .= "'" . mysql_escape_string($val) . "', ";
		}
		$p1 = substr($p1, 0, strlen($p1) - 2);
		$p2 = substr($p2, 0, strlen($p2) - 2);

		$sql = "INSERT INTO " . $table_name . " (" . $p1 . ") VALUES(" . $p2 . ")";

		$res[] = $sql;
	}
	return $res;
}

function readFiles($dir, $d = 0) {
	$dir = trim($dir);
	if(substr($dir, strlen($dir) - 1, 1) != "/")
		$dir .= "/";

	$hDir = opendir($dir);

	$res = "";

	$dirs = Array();
	$files = Array();
	while($obj = readdir($hDir)) {
		if($obj == "." || $obj == "..")
			continue;
		if(is_dir($dir . $obj))
			$dirs[] = $obj;
		if(is_file($dir . $obj))
			$files[] = $obj;
	}

	sort($dirs);
	sort($files);

	$sz = sizeof($dirs);
	for($i = 0; $i < $sz; $i++) {
		$ident = md5($dir.$dirs[$i]);

		if($dirs[$i] == "packages" || $dirs[$i] == "regedit")
			continue;

		$str = "<input type='checkbox' class='ch' id='input_" . $ident . "' name='dirs[]' value='" . $dir . $dirs[$i] . "' checked> <a href='/' onclick='javascript: switchLog(\"ul_" . $ident . "\"); return false;'>" . $dirs[$i] . "</a><br />";
		$res .= $str;

		if($sub_res = readFiles($dir . $dirs[$i], ($d+1)))
			$res .= "<ul style='display: none' id='ul_" . $ident . "'>" . $sub_res . "</ul>";
	}

	$sz = sizeof($files);
	for($i = 0; $i < $sz; $i++) {
		if($files[$i] == "config.php" || $files[$i] == "umipacker.php" || $files[$i] == "make_me_lite_or_free.php" || $files[$i] == "reg" || $files[$i] == "installed")
			continue;

		$str = "<input type='checkbox' class='ch' name='files[]' value='" . $dir . $files[$i] . "' checked> " . $files[$i] . "<br />";
		$res .= $str;
	}

	return $res;
}

function make_compress($structure) {
	return $res = gzcompress(serialize($structure), COMPRESS_LEVEL);
}

?>


<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<title>Упаковщик UMI.CMS 2.0 - <?php echo $C_TITLE; ?></title>

		<style type="text/css">
body, td, select {
	margin: 0px;
	background-color: #F7F7F7;
	font-family: Verdana;
	font-size: 11px;
}


h1 {
	font-family: Verdana, arial, helvetica, sans serif;
	font-weight: bold; font-size: 17px;
	color: #A7A7A7;
}

div.content {
	margin: 10px;
}

input {
	font-size: 11px;
	font-family: verdana;
	margin-top: 2px;
	margin-bottom: 2px;
	border: #C0C0C0 0.5pt solid;

	padding-left: 7px;
	padding-right: 7px;
	padding-bottom: 2px;
	height: 21px;
}

input.text {
	font-size: 11px;
	font-family: verdana;
	margin-top: 2px;
	margin-bottom: 2px;
	border: #C0C0C0 0.5pt solid;

	padding-left: 7px;
	padding-right: 7px;
	padding-bottom: 0px;
	height: 18px;
	width: 100%;
}

textarea.licence {
	width: 550px;
	height: 235px;
}

li {
	margin-top: 3px;
}

.c_true {
	color: green;
}

.c_false {
	color: red;
}

a {
	color: #008000;
	text-decoration: underline;
}

input.ch {
	height: 12px;
	border: none;
}

#log, #mods {
	margin-left: 15px;
}
		</style>

	<script type="text/javascript">
var inc = 0;

function nextStep(step) {
	window.location = "?s=" + step;
}

function readTime() {
	tOut = 10;

	bObj = document.getElementById('agreeButton');

	if(inc < tOut) {
		bObj.disabled = true;
		bObj.value = "Я согласен (" + (tOut - inc) + " сек)";
	}

	if(inc == tOut) {
		bObj.value = "Я согласен >>";
		bObj.disabled = false;
		return true;
	}

	if(inc++ < tOut)
		setTimeout(readTime,1000);
}

function switchLog(dName) {
	if(!(lObj = document.getElementById(dName)))
		return false;
	if(lObj.style.display == '')
		lObj.style.display = 'none';
	else
		lObj.style.display = '';
	return false;
}

	</script>

	</head>
	<body>

<div>
<img src="http://www.umicms.ru/images/cms/logo.gif" width="184" height="81" alt="UMI.CMS" /><img src="http://www.umicms.ru/images/cms/wing.gif" width="90" height="81" alt="UMI.CMS" />
</div>
<table width="100%" cellspacing="0">
   <tr>
    <td colspan="5" height="3" style="background-image: url('http://www.umicms.ru/images/cms/gray_line.gif')"></td>
   </tr>

   <tr>
    <td colspan="5" height="10" style="background-image: url('http://www.umicms.ru/images/cms/top_line.gif')"></td>
   </tr>

   <tr>
    <td colspan="5" height="3" style="background-image: url('http://www.umicms.ru/images/cms/gray_line.gif')"></td>
   </tr>

   <tr>
    <td colSpan="5" style="background-image: url('http://www.umicms.ru/images/cms/gray_line.gif')" height="1"></td>
   </tr>
</table>

<div class="content">

<h1><?php echo $C_H1; ?></h1>

<?php echo $C_CONTENT; ?>

	</body>
</html>