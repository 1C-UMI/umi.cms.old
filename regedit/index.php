<?php
//exit();
error_reporting(0);

include "../mysql.php";

error_reporting(0);

$vars = $_REQUEST['vars'];
$vals = $_REQUEST['vals'];

if(is_array($vars)) {
	foreach($vars as $id => $var) {
		$id = (int) $id;

		$var = mysql_escape_string($var);
		$val = mysql_escape_string($vals[$id]);
		$sql = "UPDATE cms_reg SET var = '$var', val='$val' WHERE id='$id'";
		mysql_query($sql);
	}
}


$dels = $_REQUEST['dels'];
if(is_array($dels)) {
	foreach($dels as $id) {
		$id = (int) $id;
		$sql = "DELETE FROM cms_reg WHERE id='$id'";
		mysql_query($sql);

		if(is_file("../cache/reg")) {
			if(is_writable("../cache/reg")) {
				unlink("../cache/reg");
			}
		}
	}
}


$var_new = $_REQUEST['var_new'];
$val_new = $_REQUEST['val_new'];

if(is_array($var_new)) {
	foreach($var_new as $rel => $var) {
		$rel = (int) $rel;

		$var = mysql_escape_string($var);
		$val = mysql_escape_string($val_new[$rel]);

		if(!$var)
			continue;


		if(mysql_num_rows(mysql_query("SELECT * FROM cms_reg WHERE rel='$rel' AND var='$var'")) == 0) {
			$sql = "INSERT INTO cms_reg (rel, var, val) VALUES('$rel', '$var', '$val')";
			mysql_query($sql);
		}
	}

	if(is_file("../cache/reg")) {
		if(is_writable("../cache/reg")) {
			unlink("../cache/reg");
		}
	}

}


function regedit_get_childs($rel = 0, $i = 14) {
	$rel = (int) $rel;

	$sql = "SELECT id, var, val, rel FROM cms_reg WHERE rel='$rel'";
	$result = mysql_query($sql);

	$color = "#" . dechex($i) . dechex($i) . dechex($i);


	echo <<<DIV

<div class="keygroup" id="expand_$rel" style="display: none;">

<br />

<table border="0" cellspacing="0" cellpadding="0" style="background-color: $color; margin: 0px; width: 700px;">

DIV;

	while($row = mysql_fetch_row($result)) {
		list($id, $var, $val, $rel) = $row;
//		list($c) = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM cms_reg WHERE rel='$id'"));
$c = true;

		if($c) {
			$exp_link = <<<EXP
<a href="#" onclick="javascript: sh_exp(this, 'expand_$id'); return false; " class="key" id="ekey_$id">+</a>
EXP;
			$nums = "($c)";
		} else {
			$exp_link = "";
			$nums = "";
		}
		

		echo <<<ITEM

<tr>
	<td valign="middle" style="width: 20px;">
		$exp_link
	</td>

	<td valign="middle">
		<input type="text" name="vars[$id]" value="$var" size="20" />
	</td>

	<td valign="middle" align="left" style="padding-left: 10px; width: 300px;">
		<input type="text" name="vals[$id]" value="$val" size="50" />
	</td>

	<td>
		<input type="checkbox" name="dels[]" value="$id" name="sd"></input>
	</td>
</tr>

<tr>
	<td colspan="4">

ITEM;

		if($c)
			regedit_get_childs($id, ($i - 1));

		echo <<<END
	</td>
</tr>

END;
	}

	echo <<<DIV

<tr style="background-color: #FFF;">
	<td valign="middle" style="width: 20px;"></td>

	<td valign="middle">
		<input type="text" name="var_new[$rel]" value="" size="20" />
	</td>

	<td valign="middle" align="left" style="padding-left: 10px; width: 300px;">
		<input type="text" name="val_new[$rel]" value="" size="50" />
	</td>

	<td></td>
</tr>

</table>

<br />

</div>

<script>
	is_exp = findUCookie("e_expand_$rel", "regedit");
	if(is_exp) {
//		document.write(is_exp);
		toExpand[toExpand.length] = $rel;
	}
</script>

DIV;

}




?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
		<title>UMI.CMS 2.0 - RegEdit</title>

		<link href="/styles/xsl/umicms.css" type="text/css" rel="stylesheet" />

		<style type="text/css">
			div.regedit_tree {
				margin: 20px;
			}

			div.keygroup {
				margin-left: 30px;
				font-size: 11px;
			}

			input {
				font-size: 10px;
				border: #555 1px solid;
				height: 16px;
			}

			a {
				color:			#000;
				text-decoration:	none;
				font-size:		12px;
			}

			a.key {
				font-weight: bold;
			}
		</style>

		<script type="text/javascript">
			var toExpand = new Array('0');

			function sh_exp(lobj, oid, unc) {
				obj = document.getElementById(oid);
				if(!obj) {
					alert("No object(" + oid + ") found");
					return false;
				}

				is_expanded = false;
				if(obj.style.display == '')
					is_expanded = true;

				if(is_expanded) {
					if(!unc)
						deleteUCookie("e_" + oid, "1000", "regedit");
					obj.style.display = 'none';
					lobj.innerHTML = '+';
				} else {
					if(!unc)
						addUCookie("e_" + oid, "1", "1000", "regedit");
					obj.style.display = '';
					lobj.innerHTML = '&#151;';
				}
			}

			/* Simple cookies */
			function addCookie(szName,szValue,dtDaysExpires) {
				szName = szName.replace(/\./g, "_");

				var dtExpires = new Date();
				var dtExpiryDate = "";

				dtExpires.setTime(dtExpires.getTime() + 
				dtDaysExpires * 60 * 60 * 1000);

				dtExpiryDate = dtExpires.toGMTString();

				document.cookie = szName + "=" + szValue + "; path=/; expires=" + dtExpiryDate;
			}

			function findCookie(szName) {
				szName = szName.replace(/\./g, "_");

				var i = 0;
				var nStartPosition = 0;
				var nEndPosition = 0;  
				var szCookieString = document.cookie;  

				while(i <= szCookieString.length) {
					nStartPosition = i;
					nEndPosition = nStartPosition + szName.length;

					if(szCookieString.substring(nStartPosition,nEndPosition) == szName) {
						nStartPosition = nEndPosition + 1;
						nEndPosition = document.cookie.indexOf(";",nStartPosition);

						if(nEndPosition < nStartPosition)
							nEndPosition = document.cookie.length;

						return document.cookie.substring(nStartPosition,nEndPosition);
						break;    
					}

					i++;  
				}

				return "";
			}

			/* Extra sized cookies. 4094 bytes per block max! */

			function addUCookie(vName, vVal, vExp, gName) {
				if(!gName)
					gName = "ucookie";

				if(!vExp)
					vExp = 3600*24*365;

				gVal = findCookie(gName);

				nVal = "";

				gArr = gVal.split("*");
				in_array = false;

				for(i = 0; i < gArr.length; i++) {
					tmp = gArr[i];
					cgArr = tmp.split("|");
					cVar = cgArr[0];
					cVal = cgArr[1];

					if(cVar == "")
						continue;

					if(cVar == vName) {
						in_array = true;
						nVal += vName + "|" + vVal + "*";
					} else
						nVal += tmp + "*";
				}

				if(!in_array)
					nVal += vName + "|" + vVal + "*";

				nVal = nVal.substr(0, nVal.length - 1);

				addCookie(gName, nVal, vExp);
			}

			function findUCookie(vName, gName) {
				if(!gName)
					gName = "ucookie";

//				document.write("<b>" + gName + "->" + vName + "</b><br />");

				gVal = findCookie(gName);
				gArr = gVal.split("*");


				in_array = false;
//				document.write(gVal);
				for(i = 0; i < gArr.length; i++) {
					tmp = gArr[i];
					cgArr = tmp.split("|");
					cVar = cgArr[0];
					cVal = cgArr[1];

					if(cVar == vName) {
						return cVal;
					}
				}
				return "";

			}

			function deleteUCookie(vName, vExp, gName) {
				if(!gName)
					gName = "ucookie";

				if(!vExp)
					vExp = 3600*24*365;

				gVal = findCookie(gName);
				gArr = gVal.split("*");

				nVal = "";

				for(i = 0; i < gArr.length; i++) {
					tmp = gArr[i];
					cgArr = tmp.split("|");
					cVar = cgArr[0];
					cVal = cgArr[1];

					if(cVar != vName)
						nVal += cVar + "|" + cVal + "*";
				}

				nVal = nVal.substr(0, nVal.length - 1);

				addCookie(gName, nVal, vExp);
			}

			function autoExpand() {
				setTimeout("autoExpandDo()", "1");
			}

			function autoExpandDo() {
				l = toExpand.length;
				for (i in toExpand) {
					cid = toExpand[i];
//					alert(cid);
					lobj = document.getElementById("ekey_" + cid);
					if(!lobj)
						continue;
					sh_exp(lobj, "expand_" + cid, 1);
				}
			}

		</script>

	</head>
	<body onload="javascript: autoExpand();">

<form method="post">

<div class="regedit_tree">
<b><a href="#" onclick="javascript: sh_exp(this, 'expand_0');" class="key" id="ekey_0">+</a> Реестр UMI.CMS 2.0</b><br />
<?php

regedit_get_childs();

?>

<p><input type="submit" value="Сохранить" /></p>
</div>



</form>

	</body>
</html>