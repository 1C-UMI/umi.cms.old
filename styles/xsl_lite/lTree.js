function error(str) {
	alert(str);
	return false;
}

function lTree(placer) {
	if(typeof placer == "string") 
		placer = document.getElementById(placer);
	if(!placer)
		return error("No placer found");
	this.placer = placer;
	placer.className = "lTreeContainer";
}

lTree.prototype.treeStruct = new Array();
lTree.prototype.domains = new Array();
lTree.prototype.maxId = 0;

lTree.prototype.currDragItem = null;
lTree.prototype.currSelectItem = null;

lTree.prototype.getNode = function(nodeId, beforeId) {
	if(nodeId == 0 && !this.treeStruct[nodeId]) {
		return this.placer;
	}

	if(beforeId) {
		return this.treeStruct[beforeId]['separatorObj'];
	}

	if(typeof this.treeStruct[nodeId] != "undefined") {
		return this.treeStruct[nodeId]['placerObj'];
	} else {
		return false;
	}
}

lTree.prototype.getNewId = function() {
	return ++this.maxId;
}

lTree.prototype.addDomain = function(domain) {
	this.domains[this.domains.length] = domain;
}

lTree.prototype.init = function() {
	var i;

	for(i = 0; i < this.domains.length; i++) {
		this.addItem(0, this.domains[i], this.domains[i], '', 1);
	}
}

lTree.prototype.addItem = function(rootId, nodeText, newId, beforeId, hasChilds, domain) {
	var __self = this;

	if(beforeId) {
		var obj = this.getNode(rootId, beforeId);
	} else {
		var obj = this.getNode(rootId);
	}

	if(typeof newId == "undefined") {
		newId = this.getNewId();
	} else {
		if(this.maxId < newId)
			this.maxId = newId;
	}

	if(parseInt(newId) == newId) {
		var is_domain = false;
	} else {
		var is_domain = true;
	}

	var nodeObj = document.createElement("div");
	var separatorObj = document.createElement("div");
	var placerObj = document.createElement("div");
	var pmObj = document.createElement("div");
	var transId = newId;

	this.bindNodeEvents(nodeObj, newId);
	this.bindSeparatorEvents(separatorObj, newId);

	placerObj.className = "lTreePlacer";


	var controlsObj = document.createElement("div");
	controlsObj.className = "lTreeControlDiv";

	var r = "";

	if(!is_domain) {
		r += "<a href=\"/admin/content/edit_page/" + newId + "/\"><img src=\"/images/cms/admin/full/tree/ico_edit.gif\" border=\"0\" /></a>&nbsp;&nbsp;";
		r += "<a href=\"/admin/content/add_page/?parent=" + newId + "\"><img src=\"/images/cms/admin/full/tree/ico_add.gif\" border=\"0\" /></a>&nbsp;&nbsp;";
		r += "<a href=\"/admin/content/del_page/?pid=" + newId + "\" onclick=\"javascript: return confirm(window.lang_are_you_sured);\"><img src=\"/images/cms/admin/full/tree/ico_del.gif\" border=\"0\" /></a>";
	} else {
		r += "<a href=\"/admin/content/add_page/?target_domain=" + newId + "\" style=\"margin-right: 16px;\"><img src=\"/images/cms/admin/full/tree/ico_add.gif\" border=\"0\" /></a>&nbsp;&nbsp;";
	}

	controlsObj.innerHTML = r;

	nodeObj.appendChild(controlsObj);



	pmObj.className = "lTreePM";

	if(hasChilds > 0) {
		pmObj.innerHTML = "<img src=\"/images/cms/admin/full/tree/minus.gif\" width=\"16\" height=\"16\" hspace='0' />";
		pmObj.onclick = function() {
			__self.reShow(transId);
			__self.stopDrag();
		}

	} else {
		pmObj.innerHTML = "<img src=\"/images/cms/admin/full/tree/minus_gray.gif\" width=\"16\" height=\"16\" hspace='0' />";
	}

	if(is_domain) {
		pmObj.innerHTML += "&nbsp;<img src=\"/images/cms/admin/full/tree/ico_domain.gif\" hspace='0' />";
	} else {
		pmObj.innerHTML += "&nbsp;<img src=\"/images/cms/admin/full/tree/ico_page.gif\" hspace='0' />";
	}

	nodeObj.appendChild(pmObj);


//	nodeObj.appendChild( document.createTextNode(nodeText) );
	var linkObj = document.createElement("a");
	linkObj.className = 'lTreeLink';
	linkObj.appendChild( document.createTextNode(nodeText) );
	var d = document.createElement("div");
	d.style.float = "left";
	d.innerHTML = "<table style='border: 0px; width: 300px;'><tr><td><a href='/admin/content/edit_page/" + newId + "/' class='lTreeLink'>" + linkObj.innerHTML + "</a></td></tr></table>";
//	d.innerHTML = "<table style='border: 0px; width: 300px;'><tr><td style='color: #3281CD;'>" + linkObj.innerHTML + "</td></tr></table>";
	nodeObj.appendChild( d );

	if(beforeId) {
		obj.parentNode.insertBefore(separatorObj, obj);
		obj.parentNode.insertBefore(nodeObj, obj);
		obj.parentNode.insertBefore(placerObj, obj);

	} else {
		if(typeof obj == "object") {
			obj.appendChild(separatorObj);
			obj.appendChild(nodeObj);
			obj.appendChild(placerObj);
		}
	}

	if(is_domain) {
		placerObj.className += " lTreeDomainGroup";
	}

	var structItem =	{
					"id":			newId,
					"rel":			rootId,
					"nodeObj":		nodeObj,
					"separatorObj":		separatorObj,
					"placerObj":		placerObj,
					"nodeText":		nodeText,
					"pmObj":		pmObj,
					"hasChilds":		hasChilds,
					"is_domain":		is_domain
				};

	this.treeStruct[newId] = structItem;

	return newId;
}

lTree.prototype.startDrag = function(nodeObj, nodeId) {
	if(parseInt(nodeId) != nodeId) {
		return false;
	}

	this.stopDrag ();

	this.currDragItem = this.treeStruct[nodeId];
	var __self = this;
	var handler = function() {
		__self.catchDrag();
	}
	document.lmouse.setHandler(handler);
}

lTree.prototype.stopDrag = function() {
	var clonedNode;
	document.lmouse.resetHandler();
	if(clonedNode = document.getElementById('lTreeClonedNode')) {
		clonedNode.parentNode.removeChild(clonedNode);
	}
	this.currDragItem = null;
}

lTree.prototype.catchDrag = function() {
	var clonedNode;

	if(clonedNode = document.getElementById('lTreeClonedNode')) {
		clonedNode.parentNode.removeChild(clonedNode);
	}

	clonedNode = document.createElement("div");
	clonedNode.id = 'lTreeClonedNode';
	clonedNode.className = 'lTreeClonedNode';
	clonedNode.innerHTML = this.currDragItem['nodeText'];

	var __self = this;

	document.onmouseup = function() {
		__self.stopDrag();
	}

	this.placer.appendChild(clonedNode);

	document.lmouse.resetAllSelections();
}

lTree.prototype.getChildNodes = function(relId, res) {
	if(!res) {
		var res = new Array();
	}

	for(i in this.treeStruct) {
		if(this.treeStruct[i]['rel'] == relId) {
			res[res.length] = this.treeStruct[i];
			this.getChildNodes(this.treeStruct[i]['id'], res);
		}
	}
	return res;
}

lTree.prototype.recNodeCopy = function(nodes, newRootId) {
	for(i in nodes) {
		if(nodes[i]['rel'] == newRootId) {
			this.addItem(newRootId, nodes[i]['nodeText'], nodes[i]['id'], nodes[i]['is_domain']);
			this.recNodeCopy(nodes, nodes[i]['id']);
		}
	}
}

lTree.prototype.replaceNode = function(nodeId, newRootId, beforeId) {
	if(nodeId == newRootId) {
		return false;
	}

	if(newRootId == 0) return false;


	var childs = this.getChildNodes(nodeId);
	for(i in childs) {
		if(childs[i]['id'] == newRootId) {
//			return error("Can't copy parent to child");
			return false;
		}
	}

	this.currDragItem['nodeObj'].parentNode.removeChild(this.currDragItem['nodeObj']);
	this.currDragItem['separatorObj'].parentNode.removeChild(this.currDragItem['separatorObj']);
	this.currDragItem['placerObj'].parentNode.removeChild(this.currDragItem['placerObj']);

	var parentPmObj;
	if(parentPmObj = this.treeStruct[this.currDragItem['rel']]['pmObj']) {
		var per_childs = this.getChildNodes(this.currDragItem['rel']);
		if(per_childs.length == 1) {
			parentPmObj.onclick = function() {};

			parentPmObj.innerHTML = "<img src=\"/images/cms/admin/full/tree/minus_gray.gif\" width=\"16\" height=\"16\" hspace='0' />" + 
						"&nbsp;<img src=\"/images/cms/admin/full/tree/ico_page.gif\" hspace='0' />";
		}
	}

	if(beforeId) {
		this.addItem(newRootId, this.currDragItem['nodeText'], this.currDragItem['id'], beforeId);
	} else {
		this.addItem(newRootId, this.currDragItem['nodeText'], this.currDragItem['id']);
	}
	this.recNodeCopy(childs, this.currDragItem['id']);

	beforeId = (beforeId) ? beforeId : "";
	var url = "/admin/content/replace/?id=" + nodeId + "&rel=" + newRootId + "&before=" + beforeId;
	execInternalScript(url);

	this.treeStruct[newRootId]['pmObj'].innerHTML = "<img src=\"/images/cms/admin/full/tree/minus.gif\" width=\"16\" height=\"16\" hspace='0' />";

	if(this.treeStruct[newRootId]['is_domain']) {
		this.treeStruct[newRootId]['pmObj'].innerHTML += "&nbsp;<img src=\"/images/cms/admin/full/tree/ico_domain.gif\" hspace='0' />";
	} else {
		this.treeStruct[newRootId]['pmObj'].innerHTML += "&nbsp;<img src=\"/images/cms/admin/full/tree/ico_page.gif\" hspace='0' />";
	}


	var __self = this;
	var transId = newRootId;
	this.treeStruct[newRootId]['pmObj'].onclick = function() {
			__self.reShow(transId);
	}

}

lTree.prototype.insertBefore = function(nodeId) {
	if(this.currDragItem['id'] == nodeId) {
		return false;
	}
	this.replaceNode(this.currDragItem['id'], this.treeStruct[nodeId]['rel'], nodeId);
}

lTree.prototype.bindNodeEvents = function(nodeObj, newId) {
	var __self = this;
	var transId = newId;

	//binding node object
	nodeObj.className = "lTreeItem";
	nodeObj.onmousedown = function() {
		//start drag
		__self.startDrag(this, transId);
		removeSelection();
	}

	nodeObj.onmouseover = function() {
		if(__self.treeStruct[transId]['is_domain']) {
			this.className = "lTreeItem lTreeDomainItem";
			return;
		}

		if(__self.currDragItem != null) {
			this.className = "lTreeItem lTreeItemActive";
		}
		removeSelection();
	}

	nodeObj.onmouseout = function() {
		if(__self.currSelectItem != newId) {
			this.className = "lTreeItem";
		}
		removeSelection();
	}

	nodeObj.onmouseup = function() {
		if(typeof __self.currDragItem != "undefined") {
			if(__self.currDragItem) {
				__self.replaceNode(__self.currDragItem['id'], newId);
			}
		}
		__self.stopDrag();
		removeSelection();
	}

	nodeObj.onclick = function () {
		__self.stopDrag();
		removeSelection();
	}
/*
	nodeObj.onclick = function() {
		if(__self.pmed) {
			__self.pmed = false;
			return;
		}

		if(__self.currSelectItem != newId) {
			this.className = "lTreeItem lTreeItemActive";
			if(__self.currSelectItem) {
				__self.treeStruct[__self.currSelectItem]['nodeObj'].className = "lTreeItem";
			}
			__self.currSelectItem = newId;
		} else {
			this.className = "lTreeItem";
			__self.currSelectItem = null;
		}
	}
*/

}

lTree.prototype.bindSeparatorEvents = function(separatorObj, newId) {
	var __self = this;
	var transId = newId;
	//binding separator object
//	separatorObj.className = "lTreeSeparator";
	separatorObj.className = "lTreeSeparator";

	separatorObj.onmouseover = function() {
		if(__self.currDragItem != null) {
			this.className = "lTreeSeparator lTreeSeparatorActive";
		}
	}

	separatorObj.onmouseup = function() {
		if(__self.currDragItem != null) {
			__self.insertBefore(transId);
		}
	}


	separatorObj.onmouseout = function() {
		this.className = "lTreeSeparator";
	}
}

lTree.prototype.addElement = function(nodeText) {
	if(this.currSelectItem) {
		var newId = this.addItem(this.currSelectItem, nodeText);

		var url = "/json.php?action=add&id=" + newId + "&rel=" + this.currSelectItem + "&name=" + nodeText;
		execInternalScript(url);
	}
}

lTree.prototype.removeNode = function(nodeId) {
	if(typeof this.treeStruct[nodeId] == "undefined") {
		return false;
	}

	if(this.treeStruct[nodeId]['rel'] == 0) {
//		return error("Can't delete the root element");
		return false;
	}

	this.treeStruct[nodeId]['nodeObj'].parentNode.removeChild(this.treeStruct[nodeId]['nodeObj']);
	this.treeStruct[nodeId]['separatorObj'].parentNode.removeChild(this.treeStruct[nodeId]['separatorObj']);
	this.treeStruct[nodeId]['placerObj'].parentNode.removeChild(this.treeStruct[nodeId]['placerObj']);

	var url = "/json.php?action=del&id=" + nodeId;
	execInternalScript(url);
}

lTree.prototype.delElement = function () {
	if(this.currSelectItem) {

		if(this.treeStruct[this.currSelectItem]['rel'] == 0) {
			return false;
		}

		var childs = this.getChildNodes(this.currSelectItem);
		this.removeNode(this.currSelectItem);
		for(i = 0; i < childs.length; i++) {
			this.removeNode(childs[i]['id']);
		}
	}
	this.currSelectItem = null;
}

lTree.prototype.reShow = function(nodeId) {
	var myPlacer = this.treeStruct[nodeId]['placerObj'];
	if(myPlacer.style.display == 'none') {
		if(this.treeStruct[nodeId]['hasChilds'] > 0) {
			this.treeStruct[nodeId]['pmObj'].innerHTML = "<img src=\"/images/cms/admin/full/tree/minus.gif\" width=\"16\" height=\"16\" hspace='0' />";
		} else {
			this.treeStruct[nodeId]['pmObj'].innerHTML = "<img src=\"/images/cms/admin/full/tree/minus_gray.gif\" width=\"16\" height=\"16\" hspace='0' />";
		}
		myPlacer.style.display  = '';
	} else {
		if(this.treeStruct[nodeId]['hasChilds'] > 0) {
			this.treeStruct[nodeId]['pmObj'].innerHTML = "<img src=\"/images/cms/admin/full/tree/plus.gif\" width=\"16\" height=\"16\" hspace='0' />";
		} else {
			this.treeStruct[nodeId]['pmObj'].innerHTML = "<img src=\"/images/cms/admin/full/tree/plus.gif\" width=\"16\" height=\"16\" hspace='0' />";
		}
		myPlacer.style.display = 'none';
	}

	if(this.treeStruct[nodeId]['is_domain']) {
		this.treeStruct[nodeId]['pmObj'].innerHTML += "&nbsp;<img src=\"/images/cms/admin/full/tree/ico_domain.gif\" hspace='0' />";
	} else {
		this.treeStruct[nodeId]['pmObj'].innerHTML += "&nbsp;<img src=\"/images/cms/admin/full/tree/ico_page.gif\" hspace='0' />";
	}

	this.treeStruct[nodeId]['nodeObj'].className = "lTreeItem";
	this.currSelectItem = null;
	this.pmed = true;

}


function lgetSelection() {
	if(document.is_ie)
		return document.selection;
	else
		return window.getSelection();
};

function lgetSelectRange () {
	var selObj = lgetSelection();

	if(document.is_ie) {
		if(selObj) {
			return selObj.createRange();
		}
	} else {
		if (typeof selObj != "undefined") {
			try {
				return selObj.getRangeAt(0);
			} catch(e) {
				return document.createRange();
			}
		} else {
			return document.createRange();
		}
	}

	return false;
};

function removeSelection () {
	var sel = lgetSelection();
	var range = lgetSelectRange();

	if(document.is_ie) {
		sel.empty();
	} else {
		sel.removeAllRanges();
	}
};
