function contentTreePage(contentTreeInstance, title, pageId, parentId, childs_count, prev_page_id, domain, module, method, add_link, edit_link, is_cloned, is_active, is_visible, view_link, type_title) {
	this.tree = contentTreeInstance;

	this.title = title;

	this.id = pageId;
	this.parent_id = parentId;


	this.childs_count = parseInt(childs_count);
	this.childs = new Array();
	this.tree = contentTreeInstance;

	this.prev_page_id = prev_page_id;


	this.add_link = add_link;
	this.edit_link = edit_link;

	this.module = module;
	this.method = method;

	this.tree.pages[pageId] = this;
	this.tree.pages_rel[pageId] = new Array(parentId, prev_page_id);

	this.is_active = parseInt(is_active);
	this.is_visible = parseInt(is_visible);

	this.is_cloned = (is_cloned) ? true : false;
	this.domain = domain;

	this.view_link = view_link;

	this.type_title = type_title;
}

contentTreePage.prototype.childs = new Array();
contentTreePage.prototype.tree = null;
contentTreePage.prototype.id = 0;
contentTreePage.prototype.parent_id = 0;
contentTreePage.prototype.title = "";
contentTreePage.prototype.placer = null;
contentTreePage.prototype.placer_childs = null;
contentTreePage.prototype.separator = null;
contentTreePage.prototype.is_expanded = false;
contentTreePage.prototype.is_loaded = false;
contentTreePage.prototype.parentDomain = null;
contentTreePage.prototype.domain = null;
contentTreePage.prototype.is_active = null;
contentTreePage.prototype.is_visible = null;
contentTreePage.prototype.view_link = null;
contentTreePage.prototype.type_title = null;

contentTreePage.prototype.draw = function (placer, nextPage, parentDomain) {
	this.placer = placer;
	this.parentDomain = parentDomain;

	//preparing
	var containerObj = document.createElement("div");
	var lineContainerObj = document.createElement("div");
	var icoObj = document.createElement("div");
	var titleObj = document.createElement("div");
	var controlsObj = document.createElement("div");
	var switcherObj = document.createElement("div");
	var separatorObj = document.createElement("div");
	var firstSeparatorObj = document.createElement("div");
	var childsContainerObj = document.createElement("div");

	//css
	containerObj.className = "contentTreePageContainer";
	titleObj.className = "contentTreePageTitle";
	controlsObj.className = "contentTreePageControls";
	icoObj.className = "contentTreePageIco";
	switcherObj.className = "contentTreePageSwitcher";

	lineContainerObj.className = "contentTreePageLineContainer";
	if(this.is_cloned) {
		lineContainerObj.className = "contentTreePageLineContainerCopy";
	}

	firstSeparatorObj.className = "contentTreePageSeparator";
	separatorObj.className = "contentTreePageSeparator";
	childsContainerObj.className = "contentTreePageChildsContainer";
	childsContainerObj.style.display = 'none';

	//tuning
	var lObj = document.createElement("a");
	lObj.className = (this.is_visible) ? "contentTreeActivePage" : "contentTreeUnActivePage";
	lObj.appendChild( document.createTextNode(this.title) );
	lObj.title = "Редактировать страницу (" + this.view_link + ")";
	lObj.style.cursor = "pointer";
	titleObj.appendChild( lObj );


	//setting ico
	icoObj.style.backgroundImage = "url('/images/cms/admin/full/tree/ico_" + this.module + "_" + this.method + ".gif')";
	icoObj.title = this.type_title;


	//appending
//	placer.appendChild(containerObj);
	lineContainerObj.appendChild(switcherObj);
	lineContainerObj.appendChild(icoObj);
	lineContainerObj.appendChild(controlsObj);
	lineContainerObj.appendChild(titleObj);

	containerObj.appendChild(lineContainerObj);
	containerObj.appendChild(childsContainerObj);
	childsContainerObj.appendChild(firstSeparatorObj);
//	containerObj.appendChild(separatorObj);


	//creating buttons
	var __self = this;

	lObj.href = __self.edit_link;

	var buttonObj = document.createElement("a");
	buttonObj.className = "contentTreePageControlsButton";
	buttonObj.style.backgroundImage = "url('/images/cms/admin/full/tree/ico_del.gif')";
	buttonObj.onclick = function () {
		var ____self = __self;

		var callback = function () {
			____self.deleteSelf();
		};

		var contDiv = document.createElement("div");
		var html = "<div style='margin: 15px;'><h1>Вы уверены, что хотите удалить эту страницу?</h1>";
		html += "<p>Вы собираетесь удалить страницу. Если вы уверены, нажмите 'Удалить'.<br />";
		html += "После удаления страница попадет в <a href='/admin/data/trash/'>корзину удаленных страниц</a>, откуда ее сможет восстановить администратор сайта.</p>";
		html += "<p align='right'><input type='button' value='Удалить' style='width: 100px; font-weight: bold;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().callCallbackFunction();' id='confirmDeleteButton' /> <input type='button' value='Отменить' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
		html += "</div>";

		contDiv.innerHTML = html;

		var up = new umiPopup();
		up.setSize(430, 130);
		up.setCallbackFunction(callback);
		up.open();
		up.setContent(contDiv);

		var delButton = document.getElementById('confirmDeleteButton');
		if(delButton) {
			delButton.focus();
		}


		return false;

	};
	buttonObj.title = "Удалить страницу";
	controlsObj.appendChild(buttonObj);

	var buttonObj = document.createElement("a");
	buttonObj.className = "contentTreePageControlsButton";
	if(this.add_link) {
		buttonObj.style.backgroundImage = "url('/images/cms/admin/full/tree/ico_add.gif')";
		buttonObj.href = __self.add_link;
		buttonObj.title = "Добавить подстраницу";
	} else {
		buttonObj.style.cursor = "default";
	}

	controlsObj.appendChild(buttonObj);


	var buttonObj = document.createElement("a");
	buttonObj.className = "contentTreePageControlsButton";
	buttonObj.style.marginLeft = "5px";

	if(this.edit_link) {
		buttonObj.style.backgroundImage = "url('/images/cms/admin/full/tree/ico_edit.gif')";
		buttonObj.href = __self.edit_link;
		buttonObj.title = "Редактировать страницу";
	}
	controlsObj.appendChild(buttonObj);



	var isActiveButtonObj = document.createElement("div");
	isActiveButtonObj.className = "contentTreePageControlsButton";
	isActiveButtonObj.style.backgroundImage = (this.is_active) ? ("url('/images/cms/admin/full/ico_unblock.gif')") : ("url('/images/cms/admin/full/ico_block.gif')");
	isActiveButtonObj.style.marginLeft = "10px";
	isActiveButtonObj.onclick = function () {
		__self.switchSelfIsActive(this);
	};

	if(this.is_active) {
		isActiveButtonObj.title = "Выключить страницу";
	} else {
		isActiveButtonObj.title = "Включить страницу";
	}
	controlsObj.appendChild(isActiveButtonObj);



	var buttonObj = document.createElement("div");
	buttonObj.className = "contentTreePageControlsButton";
	buttonObj.style.backgroundImage = "url('/images/cms/admin/full/tree/copy.gif')";


	if(this.childs_count > 0) {
		buttonObj.onclick = function () {
			var ____self = __self;

			var callback = function () {
				var mode = umiPopup.getSelf().getReturnValue(); 
				____self.copyElement(mode);
			};

			var contDiv = document.createElement("div");
			var html = "<div style='margin: 15px;'><h1>Как выполнять копирование?</h1>";
			html += "<p>Вы собиретесь создать виртуальную копию страницу, у которой уже есть вложенные страницы.<br />";
			html += "Если вы хотите сделать копию только этой страницы, нажмите 'Только эту страницы'.</p>";
			html += "Если вы хотите скопировать все подразделы, нажмите 'Копировать все'.</p>";
			html += "<p align='right'><input type='button' value='Копировать все' style='width: 110px; font-weight: bold;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().setReturnValue(true); umiPopup.getSelf().callCallbackFunction();' id='confirmCopyAllButton' /> ";
			html += "<input type='button' value='Копировать страницу' style='width: 140px; font-weight: normal;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().setReturnValue(false); umiPopup.getSelf().callCallbackFunction();' id='confirmCopyButton' /> ";
			html += "<input type='button' value='Отменить' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
			html += "</div>";

			contDiv.innerHTML = html;

			var up = new umiPopup();
			up.setSize(500, 130);
			up.setCallbackFunction(callback);
			up.open();
			up.setContent(contDiv);

			var copyAllButton = document.getElementById('confirmCopyAllButton');
			if(copyAllButton) {
				copyAllButton.focus();
			}


			return false;
		};
	} else {
		buttonObj.onclick = function () {
			__self.copyElement();
		}
	}
	buttonObj.title = "Создать виртуальную копию";
	controlsObj.appendChild(buttonObj);



	var buttonObj = document.createElement("div");
	buttonObj.className = "contentTreePageControlsButton";
	buttonObj.style.backgroundImage = "url('/images/cms/admin/full/tree/clone.gif')";
	buttonObj.style.marginLeft = "10px";


	if(this.childs_count > 0) {
		buttonObj.onclick = function () {
			var ____self = __self;

			var callback = function () {
				var mode = umiPopup.getSelf().getReturnValue(); 
				____self.cloneElement(mode);
			};

			var contDiv = document.createElement("div");
			var html = "<div style='margin: 15px;'><h1>Как выполнять копирование?</h1>";
			html += "<p>Вы собираетесь скопировать страницу, у которой уже есть вложенные страницы.<br />";
			html += "Если вы хотите сделать копию только этой страницы, нажмите 'Только эту страницы'.</p>";
			html += "Если вы хотите скопировать все подразделы, нажмите 'Копировать все'.</p>";
			html += "<p align='right'><input type='button' value='Копировать все' style='width: 110px; font-weight: bold;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().setReturnValue(true); umiPopup.getSelf().callCallbackFunction();' id='confirmCopyAllButton' /> ";
			html += "<input type='button' value='Копировать страницу' style='width: 140px; font-weight: normal;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().setReturnValue(false); umiPopup.getSelf().callCallbackFunction();' id='confirmCopyButton' /> ";
			html += "<input type='button' value='Отменить' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
			html += "</div>";

			contDiv.innerHTML = html;

			var up = new umiPopup();
			up.setSize(500, 130);
			up.setCallbackFunction(callback);
			up.open();
			up.setContent(contDiv);

			var copyAllButton = document.getElementById('confirmCopyAllButton');
			if(copyAllButton) {
				copyAllButton.focus();
			}


			return false;
		};
	} else {
		buttonObj.onclick = function () {
			__self.cloneElement();
		}
	}
	buttonObj.title = "Создать копию";
	controlsObj.appendChild(buttonObj);



	var buttonObj = document.createElement("a");
	buttonObj.className = "contentTreePageControlsButton";
	buttonObj.style.backgroundImage = "url('/images/cms/admin/full/tree/view.gif')";
	buttonObj.href = this.view_link;
	buttonObj.title = "Посмотреть (" + this.view_link + ")";
	controlsObj.appendChild(buttonObj);





	//placing
	this.placer_childs = childsContainerObj;
	this.switcherObj = switcherObj;
	this.titleObj = titleObj;
	this.separatorObj = separatorObj;
	this.childsContainerObj = childsContainerObj;
	this.firstSeparatorObj = firstSeparatorObj;
	this.containerObj = containerObj;
	this.isActiveButtonObj = isActiveButtonObj;
	this.controlsObj = controlsObj;
	this.lineContainerObj = lineContainerObj;

	this.refresh();

	if(nextPage) {
		placer.insertBefore(containerObj, nextPage.containerObj);
		placer.insertBefore(separatorObj, nextPage.containerObj);
	} else {
		placer.appendChild(containerObj);
		placer.appendChild(separatorObj);
	}


	this.attachDragEvents();
	this.autoLoad();
};

contentTreePage.prototype.refresh = function () {
	if(this.childs_count > 0) {
		this.setExpandable(true);
	} else {
		this.setExpandable(false);
	}
};

contentTreePage.prototype.setExpandable = function (mode) {
	if(mode) {
		var __self = this;

		if(this.is_expanded) {
			this.switcherObj.className = "contentTreePageSwitcher contentTreePageSwitcherExpanded";
			this.switcherObj.onclick = function () { __self.tree.rememberExpand(__self.id, false); __self.roll(); };
		} else {
			this.switcherObj.onclick = function () { __self.tree.rememberExpand(__self.id, true); __self.expand(); };
			this.switcherObj.className = "contentTreePageSwitcher contentTreePageSwitcherRolled";
		}
	} else {
		this.switcherObj.className = "contentTreePageSwitcher";
		this.is_expanded = false;

		this.switcherObj.onclick = function () {};
	}
};

contentTreePage.prototype.setIsExpanded = function (mode) {
	this.is_expanded = mode;
	this.setExpandable(true);
};


contentTreePage.prototype.expand = function () {
	var __self = this;

	var callback = function () {
		__self.placer_childs.style.display = '';
		if(__self.is_loaded) {
			__self.setIsExpanded(true);
		} else {
			__self.loadChilds();
		}

		var ____self = __self;
		__self.switcherObj.onclick = function () {
			____self.roll();
		};
	};

	if(this.childs_count >= 50) {
		var contDiv = document.createElement("div");
		var html = "<div style='margin: 15px;'><h1>В разеделе много подразделов, развернуть?</h1>";
		html += "<p>Вы собираетесь развернуть раздел, содержащий большое количество подразделов (" + this.childs_count + " подразделов).<br />";
		html += "Это может занять некоторое время, в течение которого окно браузера может не реагировать на ваши действия.</p>";
		html += "<p align='right'><input type='button' value='Развернуть' style='width: 100px; font-weight: bold;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().callCallbackFunction();' id='confirmDeleteButton' /> <input type='button' value='Отменить' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
		html += "</div>";

		contDiv.innerHTML = html;

		var up = new umiPopup();
		up.setSize(430, 135);
		up.setCallbackFunction(callback);
		up.open();
		up.setContent(contDiv);

		var delButton = document.getElementById('confirmDeleteButton');
		if(delButton) {
			delButton.focus();
		}
		return false;
	} else {
		callback();
		return true;
	}
};


contentTreePage.prototype.roll = function () {
	this.placer_childs.style.display = 'none';

	var __self = this;
	this.switcherObj.onclick = function () {
		__self.expand();
	};

	this.setIsExpanded(false);
};

contentTreePage.prototype.loadChilds = function () {
	var __self = this;
	var handler = function (args) {
		__self.onLoadChilds(args);
	}

	this.tree.sendRequest("load", this.id, handler);
};

contentTreePage.prototype.onLoadChilds = function (childs) {
	this.is_loaded = true;

//	this.childs = new Array();

	this.setIsExpanded(true);

	var sz = childs.length, i;
	var placer = this.placer_childs;

	for(i = 0; i < sz; i++) {
		var child = childs[i];
		var page = new contentTreePage(this.tree, child.title, child.id, child.rel, child.childs_count, child.prev_page_id, child.domain, child.module, child.method, child.add_link, child.edit_link, false, child.is_active, child.is_visible, child.view_link, child.type_title);
		page.draw(placer);

		this.childs[this.childs.length] = page;
	}
}

contentTreePage.prototype.attachDragEvents = function () {
	var __self = this;

	this.titleObj.onmousedown = function () {
		__self.tree.prepareDragEvent(__self.id, __self.parent_id, __self.domain);
	};


	this.titleObj.onmouseover = function () {
		if(__self.tree.drag_event) {
			this.className = "contentTreePageTitle contentTreePageTitleActive";
		} else {
			this.className = "contentTreePageTitle contentTreePageTitleHover";
			__self.lineContainerObj.className = "contentTreePageLineContainer contentTreePageTitleHover";
		}
	};


	this.controlsObj.onmouseover = function () {
		if(__self.tree.drag_event) {
			__self.titleObj.className = "contentTreePageTitle contentTreePageTitleActive";
		} else {
			__self.titleObj.className = "contentTreePageTitle contentTreePageTitleHover";
		__self.lineContainerObj.className = "contentTreePageLineContainer contentTreePageTitleHover";
		}
	};

	this.separatorObj.onmouseover = function () {
		if(__self.tree.drag_event) {
			this.className = "contentTreePageSeparator contentTreePageSeparatorActive";
		}
	};


	this.firstSeparatorObj.onmouseover = function () {
		if(__self.tree.drag_event) {
			this.className = "contentTreePageSeparator contentTreePageSeparatorActive";
		}
	}

	this.titleObj.onmouseout = function () {
		this.className = "contentTreePageTitle";
		__self.lineContainerObj.className = "contentTreePageLineContainer";
	};
	
	
	this.controlsObj.onmouseout = function () {
		__self.titleObj.className = "contentTreePageTitle";
		__self.lineContainerObj.className = "contentTreePageLineContainer";
	};

	this.separatorObj.onmouseout = function () {
		this.className = "contentTreePageSeparator";
	};

	this.firstSeparatorObj.onmouseout = function () {
		this.className = "contentTreePageSeparator";
	};


	this.titleObj.onmouseup = function () {
		this.className = "contentTreePageTitle";

		var ___self = __self;
		var drag_event = __self.tree.drag_event;
		
		if(!drag_event) return false;
		if(drag_event.pageId == __self.id) return false;
		
		var callback = function () {
			if(drag_event) {
				___self.tree.drag_event = drag_event;

				___self.tree.insertLast(___self.id, ___self.domain);
				___self.tree.resetDragEvent();
			}
		};

		var contDiv = document.createElement("div");
		var html = "<div style='margin: 15px;'><h1>Вы уверены, что хотите переместить эту страницу?</h1>";
		html += "<p>Вы собираетесь переместить страницу. Если вы уверены, нажмите 'Переместить'.<br />";
		html += "Перемещение страницы повлияет на структуру страниц. Будьте внимательны: адрес страницы может измениться и ссылки на нее с внешних сайтов могут перестать работать.</p>";
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

		var ___self = __self;
		var drag_event = __self.tree.drag_event;
		var callback = function () {
			if(drag_event) {
				___self.tree.drag_event = drag_event;

				___self.tree.insert(___self.parent_id, ___self.id, ___self.parentDomain, ___self.domain);
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
	};

	this.firstSeparatorObj.onmouseup = function () {
		this.className = "contentTreePageSeparator";

		var ___self = __self;
		var drag_event = __self.tree.drag_event;
		var callback = function () {
			if(drag_event) {
				___self.tree.drag_event = drag_event;

				___self.tree.insertFirst(___self.id, ___self.domain);
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
	};

};



contentTreePage.prototype.killChild = function (pageId) {
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
	if(this.childs_count == 0) {
		this.roll();
		this.setExpandable(false);
	}
};

contentTreePage.prototype.getFirstPage = function () {
	return this.childs[0];
};

contentTreePage.prototype.getLastPage = function () {
	return this.childs[this.childs.length - 1];
};

contentTreePage.prototype.getNextPage = function (pageId) {
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

contentTreePage.prototype.insertLast = function (page) {
	page.parent_id = this.id;

	if(this.is_loaded) {
		this.childs[this.childs.length] = page;
		page.draw(this.placer_childs);
	}

	++this.childs_count;
	this.refresh();
};

contentTreePage.prototype.insert = function (page, nextPage) {
	this.rebuildChilds(page, nextPage);

	page.parent_id = this.id;

	if(this.is_loaded) {
		this.childs[this.childs.length] = page;
		page.draw(this.placer_childs, nextPage);
	}


	++this.childs_count;
	this.refresh();
};

contentTreePage.prototype.insertFirst = function (page) {
	var nextPage = this.getFirstPage();

	this.rebuildChilds(page, nextPage);

	page.parent_id = this.id;

	if(this.is_loaded) {
		this.childs[this.childs.length] = page;
		page.draw(this.placer_childs, nextPage);
	}

	++this.childs_count;
	this.refresh();
};


contentTreePage.prototype.rebuildChilds = function (page, nextPage) {
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


contentTreePage.prototype.autoLoad = function () {
	if(this.childs_count > 0) {
		if(findUCookie('r' + this.id)) {
			this.expand();
		}
	}
};


contentTreePage.prototype.copyElement = function (mode) {
	this.tree.copyElement(this.id, this.domain, mode);
};


contentTreePage.prototype.cloneElement = function (mode) {
	this.tree.cloneElement(this.id, this.domain, mode);
};


contentTreePage.prototype.deleteSelf = function () {
	this.tree.deleteElement(this.id, this.domain);
};

contentTreePage.prototype.setIsActive = function (mode) {
	this.is_active = (mode) ? true : false;

	this.isActiveButtonObj.style.backgroundImage = (this.is_active) ? ("url('/images/cms/admin/full/ico_unblock.gif')") : ("url('/images/cms/admin/full/ico_block.gif')");

	var url = this.tree.pre_lang + "/admin/content/json_set_is_active/?id=" + this.id + "&mode=" + this.is_active;
	var scriptObj = document.createElement("script");
	scriptObj.src = url;
	scriptObj.charset = "utf-8";
	this.placer.appendChild(scriptObj);
};


contentTreePage.prototype.switchSelfIsActive = function(isActiveButtonObj) {
	if(this.is_active) {
		this.setIsActive(false);
		isActiveButtonObj.title = "Включить страницу";
	} else {
		this.setIsActive(true);
		isActiveButtonObj.title = "Выключить страницу";
	}
};


contentTreePage.prototype.viewPage = function() {
	window.location = this.view_link;
};


