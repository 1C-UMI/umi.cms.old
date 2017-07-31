function data_blocks(field_types, field_groups, fields, type_id) {
	this.placer = document.getElementById('data_fields_placer');
	window.data_blocks_inst = this;

	this.field_types = field_types;
	this.field_groups = field_groups;
	this.fields = fields;
	this.type_id = type_id;

	this.init();
}

data_blocks.prototype.placer = null;
data_blocks.prototype.is_field_drag_active = false;
data_blocks.prototype.is_group_drag_active = false;

data_blocks.get = function () {
	return window.data_blocks_inst;
}


data_blocks.prototype.init = function () {
	var i;

	var objSpacer = document.createElement("div");
	objSpacer.id = "dataGroupSpacerEnd";
	objSpacer.className = "dataGroupSpacer";
	this.placer.appendChild(objSpacer);
	objSpacer.onmouseout = function () { this.className = "dataGroupSpacer"; };

	objSpacer.onmouseover = function () {
		if(data_blocks.get().is_group_drag_active) {
			this.className = "dataGroupSpacer dataGroupSpacerActive";
		}
	}

	objSpacer.onmouseup = function () {
		if(data_blocks.get().is_group_drag_active && data_blocks.get().group_drag_id) {
			data_blocks.get().replaceGroup(data_blocks.get().group_drag_id, false, true);
		}
	};

	for(i = 0; i < this.field_groups.length; i++) {
		var objs = this.renderGroupBlock(i);

		var sp = this.getGroupSpacerEnd();
		sp.parentNode.insertBefore(objs[0], sp);
		sp.parentNode.insertBefore(objs[1], sp);

		this.postrenderGroupBlock(i, objs[1]);
	}

	var h = document.onmouseup;
	document.onmouseup = function () {
		data_blocks.get().is_field_drag_active = false;
		data_blocks.get().is_group_drag_active = false;

		if(typeof h == "function") {
			h();
		}
	}
}


data_blocks.prototype.postrenderGroupBlock = function (i, obj) {
		var group = this.field_groups[i];
		var id = group[0];
		var n;

		for(n = 0; n < this.fields.length; n++) {
			var field = this.fields[n];

			if(field[1] != id) continue;

			this.renderField(field[0]);
		}

		var objSpacer = document.createElement("div");
		objSpacer.id = "dataGroupFieldSpacerEnd_" + id;
		objSpacer.className = "dataGroupFIeldSpacer";
		objSpacer.onmouseover = function () { this.className = "dataGroupFIeldSpacer dataGroupFIeldSpacerActive"; };
		objSpacer.onmouseout = function () { this.className = "dataGroupFIeldSpacer"; };
		obj.appendChild(objSpacer);

		objSpacer.onmouseover = function () {
			if(data_blocks.get().isFieldDragActive()) {
				this.className = "dataGroupFIeldSpacer dataGroupFIeldSpacerActive";
			}
		};

		var dest_group_id = id;
		objSpacer.onmouseup = function () {
			data_blocks.get().is_field_drag_active = false;
			data_blocks.get().replaceNodes(data_blocks.get().field_drag_id, false, dest_group_id);
		};

}

data_blocks.prototype.renderGroupBlock = function (i) {
		var group = this.field_groups[i];
		var id = group[0];


		var objSpacer = document.createElement("div");
		objSpacer.id = "dataGroupSpacer_" + id;
		objSpacer.className = "dataGroupSpacer";

		objSpacer.onmouseout = function () { this.className = "dataGroupSpacer"; };


		var obj = document.createElement("div");
		obj.id = "dataGroupContainer_" + id;
		obj.className = "dataGroupContainer";

		if(group[3] == 0) {
			obj.className = "dataGroupContainer dataGroupContainerHidden";
		}

		var objLine = document.createElement("div");
		objLine.className = "dataGroupLine";

		if(group[3] == 0) {
			objLine.className = "dataGroupLine dataGroupLineHidden";
		}

		var objLineControls = document.createElement("div");
		objLineControls.className = "dataGroupLineControls";
//		objLineControls.innerHTML = "<a href='/admin/data/type_field_add/" + id + "/" + this.type_id + "/'><img src='/images/cms/admin/full/ico_add.gif' border='0' /></a>&nbsp;&nbsp;<a href='/admin/data/type_group_edit/" + id + "/" + this.type_id + "/'><img src='/images/cms/admin/full/tree/ico_edit.gif' border='0' /></a>&nbsp;&nbsp;<a href='#' onclick='javascript: data_blocks.get().deleteGroup(" + id + "); return false;'><img src='/images/cms/admin/full/ico_del.gif' border='0' /></a>";
		if(group[4] == 1) {
			objLineControls.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='/images/cms/admin/full/ico_data_locked.gif' border='0' />";
		} else {
			objLineControls.innerHTML = "<a href='/admin/data/type_group_edit/" + id + "/" + this.type_id + "/'><img src='/images/cms/admin/full/tree/ico_edit.gif' border='0' /></a>&nbsp;&nbsp;<a href='#' onclick='javascript: data_blocks.get().deleteGroup(" + id + "); return false;'><img src='/images/cms/admin/full/ico_del.gif' border='0' /></a>";
		}
		objLine.appendChild(objLineControls);


		var objLineDrag = document.createElement("div");
		objLineDrag.className = "dataGroupLineDrag";

		var objLineTitle = document.createElement("div");
		objLineTitle.className = "dataGroupLineTitle";
		objLineTitle.innerHTML = group[2] + " - " + group[1];
		

		objLine.appendChild(objLineDrag);
		objLine.appendChild(objLineTitle);
		obj.appendChild(objLine);




		if(group[4] != 1) {
			var objAddButton = document.createElement("div");
			objAddButton.className = "dataFieldAddDiv";
			objAddButton.innerHTML = "<table><tr><td valign='middle'><a href='/admin/data/type_field_add/" + id + "/" + this.type_id + "/'><img src='/images/cms/admin/full/ico_data_add_field.gif' border='0' /></a></td><td valign='middle'>&nbsp;<a href='/admin/data/type_field_add/" + id + "/" + this.type_id + "/'>Добавить поле</a></td></tr></table>";
			obj.appendChild(objAddButton);
		}

		var objFieldsPlacer = document.createElement("div");
		objFieldsPlacer.className = "dataGroupFieldsPlacer";
		objFieldsPlacer.id = "data_fieldplacer_" + id;
		obj.appendChild(objFieldsPlacer);


		var group_id = id;
		objLineDrag.onmousedown = function () {
			data_blocks.get().is_group_drag_active = true;
			data_blocks.get().group_drag_id = group_id;

			var handler = function() {
				document.lmouse.resetAllSelections();
			}
			document.lmouse.setHandler(handler);
		};

		objSpacer.onmouseover = function () {
			if(data_blocks.get().is_group_drag_active) {
				this.className = "dataGroupSpacer dataGroupSpacerActive";
			}
		};

		var before_group_id = id;
		objSpacer.onmouseup = function () {
			if(data_blocks.get().is_group_drag_active && data_blocks.get().group_drag_id) {
				data_blocks.get().replaceGroup(data_blocks.get().group_drag_id, before_group_id, false);
			}
		};












		return new Array(objSpacer, obj);
}

data_blocks.prototype.getFieldById = function (id) {
	var i;
	for(i = 0; i < this.fields.length; i++) {
		if(this.fields[i][0] == id) return this.fields[i];
	}
	return false;
}

data_blocks.prototype.getGroupPlacer = function (id) {
	var obj = document.getElementById("data_fieldplacer_" + id);
	return (typeof obj == "object") ? obj : false;
}

data_blocks.prototype.renderField = function (id) {
	var objs = this.preRenderField(id);
	var field = this.getFieldById(id);

	var placer = this.getGroupPlacer(field[1]);
	placer.appendChild(objs[0]);
	placer.appendChild(objs[1]);
}

data_blocks.prototype.preRenderField = function (id) {
	var field = this.getFieldById(id);


	var objSpacer = document.createElement("div");
	objSpacer.id = "dataGroupFieldSpacer_" + id;
	objSpacer.className = "dataGroupFIeldSpacer";
	objSpacer.onmouseout = function () { this.className = "dataGroupFIeldSpacer"; };




	var objFieldContainer = document.createElement("div");
	objFieldContainer.id = "dataGroupFieldContainer_" + id;
	objFieldContainer.className = "dataGroupFieldContainer";

	if(field[6] == 0) {
		objFieldContainer.className = "dataGroupFieldContainer dataGroupFieldContainerHidden";
	}

	var objLine = document.createElement("div");
	objLine.className = "dataGroupFieldLine";
	objFieldContainer.appendChild(objLine);

	var objLineControls = document.createElement("div");
	objLineControls.className = "dataGroupFieldLineControls";

	if(field[7] == 1) {
		objLineControls.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='/images/cms/admin/full/ico_data_locked.gif' border='0' />";
	} else {
		objLineControls.innerHTML = "<a href='/admin/data/type_field_edit/" + id + "/" + field[5] + "/'><img src='/images/cms/admin/full/tree/ico_edit.gif' border='0' /></a>&nbsp;&nbsp;<a href='#' onclick='javascript: data_blocks.get().deleteField(" + id + "); return false;'><img src='/images/cms/admin/full/ico_del.gif' border='0' /></a>";
	}
	objLine.appendChild(objLineControls);


	var objLineDrag = document.createElement("div");
	objLineDrag.className = "dataFieldLineDrag";
	objLine.appendChild(objLineDrag);

	var objLineTitle = document.createElement("div");
	objLineTitle.className = "dataGroupFieldLineTitle";
	objLineTitle.innerHTML = field[4] + " - " + field[3];
	objLine.appendChild(objLineTitle);


	objSpacer.onmouseover = function () {
		if(data_blocks.get().isFieldDragActive()) {
			this.className = "dataGroupFIeldSpacer dataGroupFIeldSpacerActive";
		}
	};


	var field_id = id;
	objLineDrag.onmousedown = function () {
		data_blocks.get().is_field_drag_active = true;
		data_blocks.get().field_drag_id = field_id;

		var handler = function() {
			document.lmouse.resetAllSelections();
		}
		document.lmouse.setHandler(handler);
	};

	var before_field_id = id;
	objSpacer.onmouseup = function () {
		data_blocks.get().is_field_drag_active = false;
		data_blocks.get().replaceNodes(data_blocks.get().field_drag_id, before_field_id, false);
	}

	return new Array(objSpacer, objFieldContainer);
}

data_blocks.prototype.isFieldDragActive = function () {
	return this.is_field_drag_active;
}

data_blocks.prototype.getFieldSpacer = function(field_id) {
	var obj;

	if(obj = document.getElementById("dataGroupFieldSpacer_" + field_id)) {
		return obj;
	} else {
		return false;
	}
}

data_blocks.prototype.getFieldSpacerEnd = function(is_last) {
	var obj;

	if(obj = document.getElementById("dataGroupFieldSpacerEnd_" + is_last)) {
		return obj;
	} else {
		return false;
	}
}

data_blocks.prototype.removeFieldNode = function(field_id) {
	var obj;

	if(obj = document.getElementById("dataGroupFieldContainer_" + field_id)) {
		obj.parentNode.removeChild(obj);
	} else {
		return false;
	}

	if(obj = document.getElementById("dataGroupFieldSpacer_" + field_id)) {
		obj.parentNode.removeChild(obj);
	} else {
		return false;
	}

	return true;
}

data_blocks.prototype.replaceNodes = function (field_id, before_field_id, is_last) {
	if(!data_blocks.get().field_drag_id || field_id == before_field_id) return false;

	this.removeFieldNode(field_id);

	if(before_field_id) {
		var sp = this.getFieldSpacer(before_field_id);
		var objs = this.preRenderField(field_id);

		sp.parentNode.insertBefore(objs[0], sp);
		sp.parentNode.insertBefore(objs[1], sp);
	}

	if(is_last) {
		var sp = this.getFieldSpacerEnd(is_last);
		var objs = this.preRenderField(field_id);

		sp.parentNode.insertBefore(objs[0], sp);
		sp.parentNode.insertBefore(objs[1], sp);
	}
	data_blocks.get().field_drag_id = 0;

	var i = 0, sz = this.fields.length;
	var fields = new Array();
	var r_field = this.getFieldById(field_id);

	for(i = 0; i < sz; i++) {
		var field = this.fields[i];


		if(field[0] == field_id) {
			continue;
		}

		if(field[0] == before_field_id) {
			r_field[1] = field[1];
			fields[fields.length] = r_field;
		}

		fields[fields.length] = field;
	}

	if(is_last) {
		r_field[1] = is_last;
		fields[fields.length] = r_field;
	}

	this.fields = fields;

	var url = "/admin/data/json_move_field_after/" + field_id + "/" + before_field_id + "/" + is_last + "/" + this.type_id + "/?";
	execInternalScript(url);
}


data_blocks.prototype.getGroupSpacer = function (before_group_id) {
	var obj;
	return (obj = document.getElementById("dataGroupSpacer_" + before_group_id)) ? obj : false;
}


data_blocks.prototype.getGroupSpacerEnd = function () {
	var obj;
	return (obj = document.getElementById("dataGroupSpacerEnd")) ? obj : false;
}



data_blocks.prototype.removeGroupNode = function (group_id) {
	var obj;

	if(obj = document.getElementById("dataGroupSpacer_" + group_id)) {
		obj.parentNode.removeChild(obj);
	} else return false;

	if(obj = document.getElementById("dataGroupContainer_" + group_id)) {
		obj.parentNode.removeChild(obj);
	} else return false; return true;
}


data_blocks.prototype.replaceGroup = function (group_id, before_group_id, is_end) {
	if(group_id == before_group_id) return false;

	this.removeGroupNode(group_id);

	var n, i;
	for(n = 0; n < this.field_groups.length; n++) {
		if(this.field_groups[n][0] == group_id) { i = n; break; }
	}

	if(is_end) {
		var sp = this.getGroupSpacerEnd();

		var objs = this.renderGroupBlock(i);

		sp.parentNode.insertBefore(objs[0], sp);
		sp.parentNode.insertBefore(objs[1], sp);

		this.postrenderGroupBlock(i, objs[1]);
	} else {
		var sp = this.getGroupSpacer(before_group_id);

		var objs = this.renderGroupBlock(i);

		sp.parentNode.insertBefore(objs[0], sp);
		sp.parentNode.insertBefore(objs[1], sp);

		this.postrenderGroupBlock(i, objs[1]);
	}

	this.group_drag_id = 0;

	var url = "/admin/data/json_move_group_after/" + group_id + "/" + before_group_id + "/" + this.type_id + "/?";
	execInternalScript(url);
};


data_blocks.prototype.deleteField = function (field_id) {
	if(!confirm(are_you_sure)) return false;

	this.removeFieldNode(field_id);

	var i = 0, sz = this.fields.length;
	var fields = new Array();
	var r_field = this.getFieldById(field_id);

	for(i = 0; i < sz; i++) {
		var field = this.fields[i];
		if(field[0] == field_id) continue; else fields[fields.length] = field;
	}
	this.fields = fields;


	var url = "/admin/data/json_delete_field/" + field_id + "/" + this.type_id + "/?";
	execInternalScript(url);
};


data_blocks.prototype.deleteGroup = function (group_id) {
	if(!confirm(are_you_sure)) return false;

	this.removeGroupNode(group_id);

	var n, i;
	for(n = 0; n < this.field_groups.length; n++) {
		if(this.field_groups[n][0] == group_id) continue;
	}

	var url = "/admin/data/json_delete_group/" + group_id + "/" + this.type_id + "/?";
	execInternalScript(url);
};
































