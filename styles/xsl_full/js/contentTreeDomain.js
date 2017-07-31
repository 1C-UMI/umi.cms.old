function contentTreeDomain(contentTreeInstance, domainName) {
	this.tree = contentTreeInstance;
	this.domain = this.domainName = domainName;
	this.id = 0;
}

contentTreeDomain.prototype.tree = null;
contentTreeDomain.prototype.domainName = null;
contentTreeDomain.prototype.placer = null;
contentTreeDomain.prototype.placer_childs = null;
contentTreeDomain.prototype.is_loaded = false;
contentTreeDomain.prototype.is_expanded = false;


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

	var buttonObj = document.createElement("a");
	buttonObj.className = "contentTreePageControlsButton";
	buttonObj.style.backgroundImage = "url('/images/cms/admin/full/tree/ico_add.gif')";
	buttonObj.href = __self.tree.pre_lang  + "/admin/content/add_page/?parent=" + __self.domainName;
	buttonObj.title = "Создать страницу";

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
	this.switcherObj = switcherObj;
//	placer.appendChild(separatorObj);
//	containerObj.appendChild(separatorObj);

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

	this.setIsExpanded(true);

	this.childs = new Array();

	for(i = 0; i < sz; i++) {
		var child = childs[i];

		var page = new contentTreePage(this.tree, child.title, child.id, child.rel, child.childs_count, child.prev_page_id, child.domain, child.module, child.method, child.add_link, child.edit_link, false, child.is_active, child.is_visible, child.view_link, child.type_title);
		//placer.appendChild(page.draw(placer));
		page.draw(placer, undefined, this);

		this.childs[this.childs.length] = page;
	}

	this.childs_count = i;
	this.is_loaded = true;
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

		var ___self = __self;
		var drag_event = __self.tree.drag_event;
		
		if(!drag_event) return false;
		if(!drag_event.pageId) return false;
		
		var callback = function () {
			if(drag_event) {
				___self.tree.drag_event = drag_event;


				___self.tree.insertLast(___self.id, ___self.domainName);
				___self.tree.resetDragEvent();
			}
		};

		var contDiv = document.createElement("div");
		var html = "<div style='margin: 15px;'><h1>Вы уверены, что хотите переместить эту страницу?</h1>";
		html += "<p>Вы собираетесь переместить страницу. Если вы уверены, нажмите 'Переместить'.<br />";
		html += "Перемещение страницы повлияет на структуру страниц.<br />Будьте внимательны: адрес страницы может измениться и ссылки на нее с внешних сайтов могут перестать работать.</p>";
		html += "<p align='right'><input type='button' value='Переместить' style='width: 100px; font-weight: bold;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().callCallbackFunction();' id='confirmMoveButton' /> <input type='button' value='Отменить' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
		html += "</div>";


		contDiv.innerHTML = html;

		var up = new umiPopup();
		up.setSize(475, 145);
		up.setCallbackFunction(callback);
		up.open();
		up.setContent(contDiv);

		var moveButton = document.getElementById('confirmMoveButton');
		if(moveButton) {
			moveButton.focus();
		}

		return false;
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




contentTreeDomain.prototype.setExpandable = function (mode) {
	if(mode) {
		var __self = this;

		if(this.is_expanded) {
			this.switcherObj.className = "contentTreePageSwitcher contentTreePageSwitcherExpanded";
			this.switcherObj.onclick = function () { __self.tree.rememberExpand(__self.domain, true); __self.roll(); };
		} else {
			this.switcherObj.onclick = function () { __self.tree.rememberExpand(__self.domain, false); __self.expand(); };
			this.switcherObj.className = "contentTreePageSwitcher contentTreePageSwitcherRolled";
		}
	} else {
		this.switcherObj.className = "contentTreePageSwitcher";
		this.is_expanded = false;

		this.switcherObj.onclick = function () {};
	}
};


contentTreeDomain.prototype.setIsExpanded = function (mode) {
	this.is_expanded = mode;
	this.setExpandable(true);
};


contentTreeDomain.prototype.expand = function () {
	this.placer_childs.style.display = '';
	if(this.is_loaded) {
		this.setIsExpanded(true);
	} else {
		this.loadChilds();
		this.is_loaded = true;
	}

	var __self = this;
	this.switcherObj.onclick = function () {
		__self.roll();
	};
};


contentTreeDomain.prototype.roll = function () {
	this.placer_childs.style.display = 'none';

	var __self = this;
	this.switcherObj.onclick = function () {
		__self.expand();
	};

	this.setIsExpanded(false);
};


contentTreeDomain.prototype.autoLoad = function () {
//	if(this.childs_count > 0) {
		if(!findUCookie('r' + this.domain)) {
			this.expand();
		}
//	}
};
