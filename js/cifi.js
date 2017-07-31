	gzindex = 1;

	function getAbsoluteLocation(obj)
	{
		var pX = 0;
		var pY = 0;
		var oParent = obj.offsetParent; 

		while (oParent)
		{
			pX += oParent.offsetLeft;
			pY += oParent.offsetTop;
			oParent = oParent.offsetParent; 
		}

		return { x: pX + obj.offsetLeft, y: pY + obj.offsetTop }
	}

	function cifi_generate_select(cifi_name, images_arr, def) {
		if("undefined" == typeof cifi_upload_text)
			cifi_upload_text = "";

		form_html = "";

		form_html += "<table cellspacing='0' cellpadding='0' border='0' style='border: #FFF 1px solid;'><tr><td><select id='cifi_select_" + cifi_name + "' name='select_" + cifi_name + "' style='width: 317px'>\n";

		form_html += "<option value=''></option>\n";
		if(def == "NEW") {
			file_obj = document.getElementById("cifi_file_" + cifi_name);
//			file_path = file_obj.value;

			fn_arr = file_path.split("\\");
			file_name = fn_arr[fn_arr.length - 1];
			form_html += "<option value='" + file_name + "' selected>&rarr; " + file_name + "</option>\n";
		}


		for(i = 0; i < images_arr.length; i++) {
			is_checked = "";

			if(images_arr[i] == def) {
				is_checked = " selected";
			}
			form_html += "\t<option value='" + images_arr[i] + "'" + is_checked + ">" + images_arr[i] + "</option>\n";
		}

		form_html += "</select></td>\n";
		form_html += "<td>&nbsp;<a href='#' onclick='javascript: cifi_swap(\"" + cifi_name + "\"); return false;' class=\"ftext_upl\">" + cifi_upload_text + "</a></td></tr></table>";

		return form_html;

	}

	function cifi_regenerate_select(cifi_name, images_arr, def) {
		select_html = cifi_generate_select(cifi_name, images_arr, def);
		sobj = document.getElementById("cifi_ssdiv_" + cifi_name);
		if(sobj)
			sobj.innerHTML = select_html;

		
	}

	function cifi_generate(cifi_name, images_arr, def) {

		off_left = 80;
		off_top = 3;
		off_left_sel = 32;

		//getting main div
		main_div = document.getElementById("cifi_mdiv_" + cifi_name);
		if(!main_div)
			return false;

		//let's generate interface
		form_html = "";

		form_html += "<div id='cifi_sdiv_" + cifi_name + "' style='display: none'1>\n";
		form_html += "<input type='file' id='cifi_file_" + cifi_name + "' name='pics[" + cifi_name + "]' onchange=\"javascript: cifi_file_changed('" + cifi_name + "', this, cifi_images_arr_" + cifi_name + ")\" size=\"50\">\n";
		form_html += "</div>\n";

		form_html += "<div class='cifi' id='cifi_ssdiv_" + cifi_name + "'>\n";
		form_html += cifi_generate_select(cifi_name, images_arr, def);
		form_html += "</div>\n";

		//insert into main div
		main_div.innerHTML = form_html;

	}

	function cifi_swap(cifi_name) {
		is_ie = ((HTMLArea.agt.indexOf("msie") != -1) && (HTMLArea.agt.indexOf("opera") == -1));


		d1 = document.getElementById('cifi_sdiv_' + cifi_name);
		d2 = document.getElementById('cifi_ssdiv_' + cifi_name);


		if(d1.style.display == '') {
			d1.style.display = 'none';
			d2.style.display = '';
		} else {
			d1.style.display = '';
			d2.style.display = 'none';
		}
		
	}

	function cifi_file_changed(cifi_name, file_obj, images_arr) {
		file_path = file_obj.value;
		if(!file_path)
			return false;
		else {
			cifi_swap(cifi_name);
			cifi_regenerate_select(cifi_name, images_arr, "NEW");
			
		}
	}

	function cifi_getPath(cifi_name) {
		res = "";
		sobj = document.getElementById("cifi_select_" + cifi_name);
		src = sobj.options[sobj.selectedIndex].value;

		if(!src)
			return false;

		upld_value = document.getElementById("cifi_file_" + cifi_name).value;

		if(upld_value.substr(upld_value.length - src.length, src.length) == src) {
			return upld_value;
		}

		return src;
	}