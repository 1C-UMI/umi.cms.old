function contentTree(placer) {
	if(typeof placer == "string") 
		placer = document.getElementById(placer);
	if(!placer)
		return error("No placer found");
	this.placer = placer;

	this.setOnLoadEvent();

	var __self = this;
	document.onmouseup = function () {
		__self.resetDragEvent();
	};
}

contentTree.prototype.domains = new Array();
contentTree.prototype.isOnLoadEventOn = false;
contentTree.prototype.requests = new Array();
contentTree.prototype.drag_event = false;

contentTree.prototype.pages = new Array();
contentTree.prototype.pages_rel = new Array();

contentTree.prototype.mode = "drag";

contentTree.prototype.addDomain = function (domainName) {
	this.domains[this.domains.length] = new contentTreeDomain(this, domainName);
};

contentTree.prototype.setOnLoadEvent = function () {
	if(this.isOnLoadEventOn) return;

	var __self = this;

	var h = function () {
		__self.initDraw();
	}

	addOnLoadEvent(h);

	this.isOnLoadEventOn = true;
};

contentTree.prototype.initDraw = function () {
	var placer = this.placer;
	placer.className = "contentTreeContainer";

	var sz = this.domains.length, i;
	for(i = 0; i < sz; i++) {
		var domain = this.domains[i];
		placer.appendChild(domain.draw(placer));

		var domainsSeparatorObj = document.createElement("div");
		domainsSeparatorObj.className = "contentTreeDomainSeparatorObj";
		placer.appendChild(domainsSeparatorObj);

		domain.loadChilds();
	}

	var spacerObj = document.createElement("div");
	spacerObj.className = "contentTreeContainerSpacer";
	this.placer.appendChild(spacerObj);
};

contentTree.prototype.sendRequest = function (action, param, handler) {
	var requestId = this.requests.length;
	this.requests[requestId] = handler;

	var url = this.pre_lang + "/admin/content/json_" + action + "/" + requestId + "/?requestParam=" + param;
	var scriptObj = document.createElement("script");
	scriptObj.src = url;
	scriptObj.charset = "utf-8";
	this.placer.appendChild(scriptObj);
};

contentTree.prototype.reportRequest = function (requestId, args) {
	this.requests[requestId](args);
	this.requests[requestId] = undefined;
}


contentTree.prototype.prepareDragEvent = function (pageId, rel, domain) {
	this.drag_event = {
		"pageId"	: pageId,
		"rel"		: rel,
		"domain"	: domain
	};

	var handler = function() {
		document.lmouse.resetAllSelections();
	}
	document.lmouse.setHandler(handler);

};

contentTree.prototype.resetDragEvent = function () {
	this.drag_event = false;
	document.lmouse.resetHandler();
};

contentTree.prototype.getPage = function (pageId, domainName) {
	if(pageId > 0) {
		return (this.pages[pageId]) ? this.pages[pageId] : false;
	} else {
		for(i in this.domains) {
			var domain = this.domains[i];

			if(domain.domainName == domainName) {
				return domain;
			}
		}
	}
};


contentTree.prototype.deleteSelectedNode = function () {
	if(!this.drag_event) return false;

	var page_id = this.drag_event.pageId;
	var parent_page_id = this.drag_event.rel;
	var parent_page_domain = this.drag_event.domain;

	var parent_page = this.getPage(parent_page_id, parent_page_domain);
	parent_page.killChild(page_id);
	parent_page.refresh();
};

contentTree.prototype.insertLast = function (newParentId, domainName) {
	//TODO
	if(newParentId == this.drag_event.pageId) return false;

	this.deleteSelectedNode();

	var old_parent_page = this.getPage(this.drag_event.rel);
	var page = this.getPage(this.drag_event.pageId);
	var new_parent_page = this.getPage(newParentId, domainName);

	page.domain = new_parent_page.domain;

	new_parent_page.insertLast(page);

	var url = this.pre_lang + "/admin/content/json_move/?id=" + page.id + "&rel=" + newParentId + "&domain=" + domainName;
	var scriptObj = document.createElement("script");
	scriptObj.src = url;
	scriptObj.charset = "utf-8";
	this.placer.appendChild(scriptObj);

};

contentTree.prototype.insertFirst = function (newParentId, domainName) {
	//TODO
	this.deleteSelectedNode();

	if(newParentId == this.drag_event.pageId) return false;

	var old_parent_page = this.getPage(this.drag_event.rel);
	var page = this.getPage(this.drag_event.pageId);
	var new_parent_page = this.getPage(newParentId, domainName);

	page.domain = new_parent_page.domain;
	new_parent_page.insertFirst(page);

	var first_page_id = "";
	if(new_parent_page.getFirstPage()) {
		first_page_id = new_parent_page.getFirstPage().id;
		first_page_id = new_parent_page.getNextPage(first_page_id).id;
	}

	var url = this.pre_lang + "/admin/content/json_move/?id=" + page.id + "&rel=" + newParentId + "&before=" + first_page_id + "&domain=" + domainName;
	var scriptObj = document.createElement("script");
	scriptObj.src = url;
	scriptObj.charset = "utf-8";
	this.placer.appendChild(scriptObj);

};

contentTree.prototype.insert = function (newParentId, prevNodeId, parentDomain, domainName) {
	//TODO
	if(newParentId == this.drag_event.pageId) return false;

	this.deleteSelectedNode();

	var old_parent_page = this.getPage(this.drag_event.rel, parentDomain);

	var page = this.getPage(this.drag_event.pageId);

	var new_parent_page = this.getPage(newParentId, domainName);
	var next_page = new_parent_page.getNextPage(prevNodeId);

	page.domain = new_parent_page.domain;
	new_parent_page.insert(page, next_page);

	if(typeof next_page.id == "undefined") {
		var url = this.pre_lang + "/admin/content/json_move/?id=" + page.id + "&rel=" + newParentId + "&domain=" + domainName;
	} else {
		var url = this.pre_lang + "/admin/content/json_move/?id=" + page.id + "&rel=" + newParentId + "&before=" + next_page.id + "&doman=" + domainName;
	}

	var scriptObj = document.createElement("script");
	scriptObj.src = url;
	scriptObj.charset = "utf-8";
	this.placer.appendChild(scriptObj);
};

contentTree.prototype.rememberExpand = function (nodeId, mode) {
	if(mode) {
		addUCookie('r' + nodeId, 1);
	} else {
		deleteUCookie('r' + nodeId, 0);
	}
};


contentTree.prototype.copyElement = function (elementId, domain) {
	var url = this.pre_lang + "/admin/content/json_copy/?id=" + elementId + "&domain=" + domain;

	var scriptObj = document.createElement("script");
	scriptObj.src = url;
	scriptObj.charset = "utf-8";
	this.placer.appendChild(scriptObj);
};


contentTree.prototype.reportCopy = function (pageId, response) {
	var rel_id = response.rel;
	var root = this.getPage(rel_id);
	var placer = root.placer_childs;

	var child = response;
	var page = new contentTreePage(this, child.title, child.id, child.rel, child.childs_count, child.prev_page_id, child.domain, child.module, child.method, child.add_link, child.edit_link, true);
	page.draw(placer);

	root.childs[root.childs.length] = page;
};


contentTree.prototype.deleteElement = function (elementId, domain) {
	var page_id = elementId;
	var page = this.getPage(page_id);
	var parent_page_id = page.parent_id;
	var parent_page_domain = domain;

	var parent_page = this.getPage(parent_page_id, domain);
	parent_page.killChild(page_id);


	var url = this.pre_lang + "/admin/content/json_del/?id=" + elementId + "&domain=" + domain;

	var scriptObj = document.createElement("script");
	scriptObj.src = url;
	scriptObj.charset = "utf-8";
	this.placer.appendChild(scriptObj);

};
