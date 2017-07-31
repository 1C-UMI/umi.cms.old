<?php
	header("Content-type: text/javascript; charset=utf-8");

	$g_css_content = "";
	include "cssread.php";
	include "../utf8.php";
	$styles = parse_css("../css/cms/style.css");
?>
var areas = new Array('content');
function reg_area(area_name) {
	if(area_name != "content")
		areas[areas.length] = area_name;
	else
		return false;
}

G_INCLUDED = true;
var editors = Array();

function initEditor() {
	HTMLArea.loadPlugin("TableOperations");

	sz = areas.length;
	for(i = 0; i < sz; i++) {
		if(!document.getElementById(areas[i])) continue;
		initEditor_run(areas[i]);
	}
}

function initEditor_run(area_id) {
  var editor = null;

  editor = new HTMLArea(area_id);
  //HTMLArea.loadPlugin("TableOperations");
  editors['area_id'] = editor;

  var cfg = editor.config; // this is the default configuration

  var cfg = editor.config; // this is the default configuration


	cfg.toolbar = [
		[ "bold", "italic", "underline", "strikethrough", "separator",
		  "copy", "cut", "paste", "space", "undo", "redo" ,

		  "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator", "forecolor", "hilitecolor", "separator",
		  "insertorderedlist", "insertunorderedlist", "outdent", "indent", "inserttable", "separator",
		  "createlink", "removelink", "treelink", "separator", "insertimage", "insertserverimage", "separator",
		  "specchar", "xmloff", "separator", "htmlmode"
		],
		[
		  "classes", "formatblock", "space", "fontname", "space", "fontsize"
		]
	];


//  editor.registerPlugin(TableOperations);

IE = (document.all);
//for mozilla

//for ie
if(IE) {
  cfg.pageStyle = "body { background-color: #FFF; border: #000 0.5pt solid} .hilite { background-color: #FFF; } "+
                  ".sample { color: green; font-family: monospace; }" + 
		  "td { border: #000 1px dashed; }";
} else {
  cfg.pageStyle = "body { background-color: #FFF;} .hilite { background-color: #FFF; } "+
                  "/sample { color: green; font-family: monospace; }" + 
		  "td { border: #000 1px dashed; }";
}

cfg.pageStyle += "<?php
$res = "";
$res = str_replace("\n", "", str_replace("\r\n", "", $g_css_content));
$res = str_replace("\"", "\\\"", $res);
$res = iconv("CP1251", "UTF-8", $res);
echo $res;
$res = "";
?>";
  cfg.formatblock = {};
 cfg.formatblock['Normal Text'] = '';
<?php


$res = "";
for($i = 0; $i < sizeof($styles); $i++) {

	if(!$styles[$i]['tag'] || $styles[$i]['class'])
		continue;

	$res .= "cfg.formatblock['" . iconv("CP1251", "UTF-8", $styles[$i]['alias']) . "'] = '" . trim($styles[$i]['tag']) . "';\r\n";
}
echo $res;
?>


cfg.classes = {};
cfg.classes['No style'] = '';
<?php

$res = "";
if(sizeof($styles) > 0)

for($i = 0; $i < sizeof($styles); $i++) {

	if(!$styles[$i]['class'])
		continue;

	$res .= "cfg.classes['" . iconv("CP1251", "UTF-8", $styles[$i]['alias']) . "'] = '" . trim($styles[$i]['class']) . "';\r\n";
}
echo $res;

?>





HTMLArea.prototype.setClasses = function(inputClass, sel) {
	if(!inputClass)
		return;
	sel.value = "";

	this.focusEditor();

	var el = this.getParentElement();

	if(!HTMLArea._hasClass(el, inputClass))
		HTMLArea._addClass(el,inputClass);
	else
		HTMLArea._removeClass(el,inputClass);
};

/* new buttons */
  cfg.registerButton({
    id        : "treelink",
    tooltip   : "Вставить ссылку из структуры сайта",
    image     : "/htmlarea/images/ed_treelink.gif",
    textMode  : false,
    action    : function(editor) {
			var outparam = null;
			if (typeof link == "undefined") {
				link = editor.getParentElement();
				if (link && !/^a$/i.test(link.tagName))
					link = null;
			}

			if (link) {
				outparam = {
					f_href   : HTMLArea.is_ie ? editor.stripBaseURL(link.href) : link.getAttribute("href"),
					f_title  : link.title,
					f_target : link.target
				};

				if(HTMLArea.is_ie) {
					r = link.outerHTML.match(/href=['"]?([^"']+)['"]?/);
					outparam.f_href = r[1].replace(/%20/g, ' ');
				}

			}


			editor.setIsAllEventsDisabled(false);
			var sel = editor._getSelection();
			var range = editor._createRange(sel);

			var callback = function(param) {
				if(HTMLArea.is_ie) {
					range.execCommand("createlink", false, param);
				} else {
					editor._doc.execCommand("createlink", false, param);
				}
			}

			var umiPopup = editor.umiPopup;
			umiPopup.setSize(525, 250);
			umiPopup.setCallbackFunction(callback);
			umiPopup.inputParam = outparam;
			umiPopup.open();
			umiPopup.setUrl(window.pre_lang + "/admin/content/treelink/");
			editor.setIsAllEventsDisabled(true);
                }
 });

  cfg.registerButton({
    id        : "insertserverimage",
    tooltip   : "Вставить изображение с сервера",
    image     : "/htmlarea/images/ed_folderimage.gif",
    textMode  : false,
    action    : function(editor) {

			var sel = editor._getSelection();
			var range = editor._createRange(sel);

			var callback = function(param) {
				editor.setIsAllEventsDisabled(false);
				if(HTMLArea.is_ie) {
					range.execCommand("insertimage", false, param);
				} else {
					editor._doc.execCommand("insertimage", false, param);
				}
			}

			var umiPopup = editor.umiPopup;
			umiPopup.setSize(525, 295);
			umiPopup.setCallbackFunction(callback);
			umiPopup.open();
			umiPopup.setUrl(window.pre_lang + "/admin/content/insertimage/");
			editor.setIsAllEventsDisabled(true);
                }
 });


  cfg.registerButton({
    id        : "specchar",
    tooltip   : "Вставить специальный символ",
    image     : "/htmlarea/images/ed_charmap.gif",
    textMode  : false,
    action    : function(editor) {
			var sel = editor._getSelection();
			var range = editor._createRange(sel);

			var callback = function(param) {
				editor.setIsAllEventsDisabled(false);
				if(HTMLArea.is_ie) {
					range.pasteHTML(param);
				} else {
					editor.insertHTML(param);
				}
			}

			var umiPopup = editor.umiPopup;
			umiPopup.setSize(240, 250);
			umiPopup.setCallbackFunction(callback);
			umiPopup.open();
			umiPopup.setUrl("/htmlarea/specialchars.php");
			editor.setIsAllEventsDisabled(true);
                }
 });





  cfg.registerButton({
    id        : "insertmacros",
    tooltip   : "Вставить макрос",
    image     : "/htmlarea/images/ed_macros.gif",
    textMode  : false,
    action    : function(editor) {
			dlg = window.showModalDialog(window.pre_lang + '/admin/content/insertmacros/',
                                 '',
                                 'width=50, height=50,resizable=yes,help=no,status=no,scroll=no');

			if(!dlg)
				return false;
//			var html = editor.getSelectedHTML();
			// the following also deletes the selection
			editor.insertHTML(dlg);

                }
 });



  cfg.registerButton({
    id        : "xmloff",
    tooltip   : "Очистить от тегов Word'а",
    image     : "/htmlarea/images/ed_xmloff.gif",
    textMode  : false,
    action    : function(editor) {
			editor._wordClean();
                }
 });
//this._wordClean()

/* / new buttons */


  editor.generate();
}
function insertHTML() {
  var html = prompt("Enter some HTML code here");
  if (html) {
    editor.insertHTML(html);
  }
}
function highlight() {
  editor.surroundHTML('<span style="background-color: yellow">', '</span>');
}
