function f4u() {
}

function v_switch(b_id) {
	lik = b_id;

	if(b_id.id)
		b_id = b_id.id

	b_id = b_id.replace("\n", "");

	tname = b_id + "_table";

	t_obj = document.getElementById(tname);
	v_obj = document.getElementById(b_id + "_vtext");

	if(!t_obj)
		return false;

	if(t_obj.style.display != "none")
		disp = "none";
	else
		disp = "";

	t_obj.style.display = disp;

	if(disp == "none") {
		document.images[b_id + "_img"].src = "/images/cms/admin/full/sg_arrow_up.gif";
		v_obj.innerHTML = "<xsl:value-of select="//umicms/phrases/core_xclose" />";
	}
	else {
		document.images[b_id + "_img"].src = "/images/cms/admin/full/sg_arrow_down.gif";
		v_obj.innerHTML= "<xsl:value-of select="//umicms/phrases/core_xopen" />";
	}

	addUCookie(tname, disp, 365, "setgroups");
}
function pre_is_initEditor(editor_id) {
	is_initEditor();
}


function autoSize() {
	root_table = document.getElementById('main');
	if(!root_table)
		return;
	
	root_table.style.height = document.body.clientHeight + 'px';
}
window.onresize = autoSize;

function initAll() {
	lobj = document.getElementById('login_field');
	if(lobj) {
		lobj.focus();
	}
}
