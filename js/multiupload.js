var im = 0;
var comments_arr = new Array();

function add_filetype(isdis) {
	++im;

	tobj = document.getElementById("fl" + (im-1));

	if(tobj) {

		if(!isdis)
			tobj.innerHTML = "<input type='file'name='files[" + im + "]' id='tff" + im + "' onchange='add_filetype()' size='40'><div id='fl" + im + "' name='fl" + im + "'>" + "</div>";
		else
			tobj.innerHTML = "<input type='file'name='files[" + im + "]' id='tff" + im + "' onchange='add_filetype()' size='40' disabled='disabled'><div id='fl" + im + "' name='fl" + im + "'>" + "</div>";

		ntobj = document.getElementById("tff" + (im-1));
//		alert(im);

		if(ntobj) {
			ntobj.style.display = "none";
			fname = ntobj.value;


			fname_arr = fname.split("\\");
			frname = fname_arr[fname_arr.length-1];

			comments_arr[comments_arr.length] = [frname, im];
			if(fname_arr.length < 2)
				del_filetype(im);
		}

		gen_filetype();
	}
}

function gen_filetype() {
		cobj = document.getElementById("comments");

		cobj.innerHTML = "<table cellspacing='0' cellpadding='5' border='1' width='100%'>\r\n";
		count = 0;
		for(n = 0; n < comments_arr.length; n++) {
			frname = comments_arr[n][0];
			index = comments_arr[n][1];

			if(index == -1)
				continue;

			++count;

			cobj.innerHTML += "<tr><td>" + count + ") " + frname + " </td><td> <a href='#' onclick='return del_filetype(" + index + ")' class='glink'><nobr>" + text_delete + "</nobr></a>" + "<br/></td></tr>\r\n";
		}
		cobj.innerHTML += "</table>\r\n";
}

function del_filetype(index) {
//	alert(index);
	ntobj = document.getElementById("tff" + (index-1));
	if(!ntobj)
	    return;
	ntobj.disabled = true;

	for(n = 0; n < comments_arr.length; n++)
		if(comments_arr[n][1] == index)
			comments_arr[n][1] = -1;
	gen_filetype();
	return false;
}