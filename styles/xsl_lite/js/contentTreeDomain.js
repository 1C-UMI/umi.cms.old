function contentTreeDomain(contentTreeInstance, domainName) {
	this.tree = contentTreeInstance;
	this.domain = this.domainName = domainName;
	this.id = 0;
}

contentTreeDomain.prototype.tree = null;
contentTreeDomain.prototype.domainName = null;
contentTreeDomain.prototype.placer = null;
contentTreeDomain.prototype.placer_childs = null;

contentTreeDomain.prototype.draw = function (placer) {
	this.placer = placer;

	//preparing
	var containerObj = document.createElement("div");
	var icoObj = document.createElement("div");
	var titleObj = document.createElement("div");
	var controlsObj = document.createElement("div");
	var switcherObj = document.createElement("div");
	var separatorObj = document.createElement("div");
	var childsContainerObj = document.createElement("div");

	//css
	containerObj.className = "contentTreeDomainContainer";
	titleObj.className = "contentTreeDomainTitle";
	controlsObj.className = "contentTreeDomainControls";
	icoObj.className = "contentTreeDomainIco";
	switcherObj.className = "contentTreeDomainSwitcher";
	separatorObj.className = "contentTreePageSeparator";
	childsContainerObj.className = "contentTreeDomainChildsContainer";

	//tuning
	titleObj.appendChild( document.createTextNode(this.domainName) );

	//setting controls
	var __self = this;

	var buttonObj = document.createElement("div");
	buttonObj.className = "contentTreePageControlsButton";
	buttonObj.style.backgroundImage = "url('/images/cms/admin/full/tree/ico_add.gif')";
	buttonObj.onclick = function () {
		window.location = __self.tree.pre_lang  + "/admin/content/add_page/?parent=" + __self.domainName;
	};
	controlsObj.appendChild(buttonObj);


	//appending
	containerObj.appendChild(switcherObj);
	containerObj.appendChild(icoObj);
	containerObj.appendChild(controlsObj);
	containerObj.appendChild(titleObj);
	containerObj.appendChild(childsContainerObj);
	childsContainerObj.appendChild(separatorObj);

	this.titleObj = titleObj;
	this.separatorObj = separatorObj;
//	placer.appendChild(separatorObj);

//	placer.appendChild(containerObj);

	this.placer_childs = childsContainerObj;

	this.refresh();

	this.attachDragEvents();

	return containerObj;
};


contentTreeDomain.prototype.refresh = function () {};

contentTreeDomain.prototype.loadChilds = function () {
	var __self = this;
	var handler = function (args) {
		__self.onLoadChilds(args);
	}

	this.tree.sendRequest("load", this.domainName, handler);
};

contentTreeDomain.prototype.onLoadChilds = function (childs) {
	var sz = childs.length, i;
	var placer = this.placer_childs;

	this.childs = new Array();

	for(i = 0; i < sz; i++) {
		var child = childs[i];
		var page = new contentTreePage(this.tree, child.title, child.id, child.rel, child.childs_count, child.prev_page_id, child.domain, child.module, child.method, child.add_link, child.edit_link);
		//placer.appendChild(page.draw(placer));
		page.draw(placer, undefined, this);

		this.childs[this.childs.length] = page;
	}

	this.childs_count = i;
}


contentTreeDomain.prototype.childs = new Array();


contentTreeDomain.prototype.killChild = function (pageId) {
	var i;

	for(i in this.childs) {
		var page = this.childs[i];
		if(!page) continue;

		if(page.id == pageId) {
			if(page.containerObj.parentNode) {
				page.containerObj.parentNode.removeChild(page.containerObj);
			}

			if(page.separatorObj.parentNode) {
				page.separatorObj.parentNode.removeChild(page.separatorObj);
			}

			page.childs = new Array();
			page.is_loaded = false;
			page.is_expanded = false;

			this.childs[i] = undefined;

			--this.childs_count;
		}
	}
};

contentTreeDomain.prototype.getFirstPage = function () {
	var i;
	for(i in this.childs) {
		return this.childs[i];
	}
	return false;
};

contentTreeDomain.prototype.getLastPage = function () {
	return this.childs[this.childs.length - 1];
};

contentTreeDomain.prototype.getNextPage = function (pageId) {
	var i, sz = this.childs.length;

	var is_next = false;

	for(i = 0; i < sz; i++) {
		var page = this.childs[i];
		if(!page) continue;

		if(is_next) {
			return page;
		}

		if(page.id == pageId) {
			is_next = true;
		}
	}
	return false;
};

contentTreeDomain.prototype.insertLast = function (page) {
	page.parent_id = this.id;

	this.childs[this.childs.length] = page;

	page.draw(this.placer_childs);

	++this.childs_count;
	this.refresh();
};

contentTreeDomain.prototype.insert = function (page, nextPage) {
	this.rebuildChilds(page, nextPage);

	page.parent_id = this.id;

	this.childs[this.childs.length] = page;
	page.draw(this.placer_childs, nextPage);

	++this.childs_count;
	this.refresh();
};

contentTreeDomain.prototype.insertFirst = function (page) {
	var nextPage = this.getFirstPage();

	this.rebuildChilds(page, nextPage);

	page.parent_id = this.id;

	this.childs[this.childs.length] = page;
	page.draw(this.placer_childs, nextPage);

	++this.childs_count;
	this.refresh();
};


contentTreeDomain.prototype.rebuildChilds = function (page, nextPage) {
	var temp = new Array(), i;

	for(i in this.childs) {
		var cc = this.childs[i];

		if(cc == page) {
			continue;
		}

		if(cc == nextPage) {
			temp[temp.length] = page;
		}
		temp[temp.length] = cc;
	}
	this.childs = temp;
};






contentTreeDomain.prototype.attachDragEvents = function () {
	var __self = this;

	this.titleObj.onmousedown = function () {
		__self.tree.prepareDragEvent(__self.id, __self.parent_id, __self.domainName);
	};

	this.titleObj.onmouseover = function () {
		if(__self.tree.drag_event) {
			this.className = "contentTreePageTitle contentTreePageTitleActive";
		}
	};

	this.separatorObj.onmouseover = function () {
		if(__self.tree.drag_event) {
			this.className = "contentTreePageSeparator contentTreePageSeparatorActive";
		}
	};


	this.titleObj.onmouseout = function () {
		this.className = "contentTreePageTitle";
	};

	this.separatorObj.onmouseout = function () {
		this.className = "contentTreePageSeparator";
	};

	this.titleObj.onmouseup = function () {
		this.className = "contentTreePageTitle";

		if(__self.tree.drag_event) {
			//TODO
			__self.tree.insertLast(__self.id, __self.domainName);
			__self.tree.resetDragEvent();
		}
	}

	this.separatorObj.onmouseup = function () {
		this.className = "contentTreePageSeparator";

		if(__self.tree.drag_event) {
			//TODO
			__self.tree.insertFirst(__self.id, __self.domainName);
			__self.tree.resetDragEvent();
		}
	};
};
