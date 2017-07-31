
function createDItem(sId, sTitle, sLink, bDragDisabled) {
	if (!sTitle) var sTitle = "";
	if (!arrDockItems[sId]) {
		arrDockItems[sId] = new dockItem(sId);
		arrDockItems[sId].create(null, null, sTitle, sLink, bDragDisabled);
		arrDockItems[sId].show();
		arrDockItems[sId].changeHelpVisible();
		return true;
	}
	return false;
}


function createMovedDItem(sId, sTitle) {
	if (!sTitle) var sTitle = "";
	if (!arrDockItems[sId]) {
		arrDockItems[sId] = new dockItem(sId);
		arrDockItems[sId].create(null, null, sTitle);
		arrDockItems[sId].startDrag();
	} else {
		arrDockItems[sId].startDrag();
	}
}

function absPos(theObj) {
	
	this.left = 0;
	this.top = 0;
	this.width = theObj.offsetWidth;
	this.height = theObj.offsetHeight;
	while (theObj.offsetParent) {
		this.left += theObj.offsetLeft;
		if (theObj.scrollLeft) this.left -= theObj.scrollLeft;
		this.top += theObj.offsetTop;
		if (theObj.scrollTop) this.top -= theObj.scrollTop;
		theObj = theObj.offsetParent;
	}
	this.right = this.left + this.width;
	this.bottom = this.height + this.top;

}

// dockItem Object
function dockItem(sId) {
	this.sId = sId;
}

dockItem.prototype.oldMouseMove = {};
dockItem.prototype.oldMouseUp = {};
dockItem.prototype.oldSelectStart = {};
dockItem.prototype.oldDragStart = {};

dockItem.prototype.oDockItem = null;
dockItem.prototype.oDockImg = null;
dockItem.prototype.oDock = null;
dockItem.prototype.oOverItem = null;
dockItem.prototype.bOverDock = false;
dockItem.prototype.bDragDisabled = false;
dockItem.prototype.oHelpLayer = null;
dockItem.prototype.pre_lang = '';
dockItem.prototype.isTrash = false;
dockItem.prototype.sImgSrc = null;
dockItem.prototype.sIcoExt = "png";
dockItem.prototype.iStartX = null;
dockItem.prototype.iStartY = null;
dockItem.prototype.iDragSensitivity = 10;


dockItem.prototype.create = function(oContainer, oBeforeDItem, sTitle, sLink, bDragDisabled) {
	if (!this.oDockItem) {
		if (!sTitle) var sTitle = "";
		if (!oContainer) var oContainer = document.getElementById('dock');
		if (!oBeforeDItem) var oBeforeDItem = null;
		this.pre_lang = (document.pre_lang)? document.pre_lang : "";
		
		if (typeof(sLink) == 'null' || typeof(sLink) == 'undefined') var sLink = '/admin/' + this.sId;
		sLink = this.pre_lang + sLink;
		if (bDragDisabled) this.bDragDisabled = true;
		if (!this.oHelpLayer) this.oHelpLayer = document.getElementById('dock_help');
		var oSelf = this;

		this.oDock = oContainer;
		this.oDockItem = document.createElement('span');
		this.oDockItem.id = 'dockitem_' + this.sId;
		this.oDockItem.style.display = 'none';

		if (sLink.length) {
			this.oDockItem.style.cursor = 'pointer';
			this.oDockItem.title = sTitle;
		}
		
		if (!this.sImgSrc) {
			this.sImgSrc = "/images/cms/admin/mac/icons/medium/" + this.sId + "." + this.sIcoExt;
		}
		
		this.oDockImg = document.createElement('img');


		this.oDockImg.title = sTitle;
		this.oDockImg.src = this.sImgSrc;
		if (this.sIcoExt == "png") {
			this.oDockImg.className = "png";
		}
		this.oDockImg.style.borderLeft = '2px solid white';
		this.oDockItem.appendChild(this.oDockImg);


		var oBeforeNode = null;
		if (oBeforeDItem && oBeforeDItem.oDockItem) oBeforeNode = oBeforeDItem.oDockItem;
		
		this.oDock.insertBefore(this.oDockItem, oBeforeNode);

		// actions
		this.oDockItem.onclick = function() {
			if (self.bDraging) {
				return false;
			} else {
				if (sLink.length) {
					document.location.href = sLink;
				}
			}
			return true;
		}

		if (!this.bDragDisabled) {
			this.oDockItem.onmousedown = function () {
				if (!self.bDraging) {
					oSelf.oDockItem = this;
					oSelf.startDrag();
				}
				// hack for MOZ
				return false;
			}
		}
	}
}

dockItem.prototype.changeHelpVisible = function () {
	if (this.oHelpLayer) {
		var bNeedShow = true;
		for (sId in arrDockItems) {
			if (arrDockItems[sId]) {
				if (!arrDockItems[sId].bDragDisabled) {
					bNeedShow = false;
					break;
				}
			}
		}
		this.oHelpLayer.style.display = bNeedShow? '': 'none';
	}
}

dockItem.prototype.show = function() {
	if (this.oDockItem && this.oDockItem.style.display != '') {
		this.oDockItem.style.display = '';
	}
}

dockItem.prototype.markPlace = function () {
	if (!this.isTrash) {
		if (this.oDockImg) {
			this.oDockImg.style.borderLeft = '2px dotted #D8DBDE';
		} else if (this.oDockItem) {
			this.oDockItem.style.borderLeft = '2px dotted #D8DBDE'
		}
	}
}

dockItem.prototype.unmarkPlace = function () {
	if (!this.isTrash) {
		if (this.oDockImg) {
			this.oDockImg.style.borderLeft = '2px solid white';
		} else if (this.oDockItem) {
			this.oDockItem.style.borderLeft = '2px solid white';
		}
	}
}

dockItem.prototype.clearSelection = function () {
	if(document.is_ie) {
			try {
				document.selection.empty();
				document.body.onselectstart = function () { return false; }
			}
			catch (theErr) {
			}
	} else {
		window.getSelection().removeAllRanges();
	}
}

dockItem.prototype.startDrag = function() {
	if (self.bDraging || this.oDock.style.display == 'none') return false;
	
	this.clearSelection();
	var oDockItem = this.oDockItem;
	if (!oDockItem) return false;
	

	document.body.style.cursor = 'pointer';

	dockItem.prototype.bOverDock = true;

	// save hdls
	this.oldMouseUp = document.onmouseup;
	this.oldMouseMove = document.onmousemove;
	this.oldSelectStart = document.is_ie? document.body.onselectstart : {};
	// hack for IE
	this.oldDragStart = document.is_ie? document.ondragstart: {};

	document.ondragstart = function () {
		return false;
	}

	var oSelf = this;
	var mouseMoveHdl = function (e) {

		var iX = 0;
		var iY = 0;
		if(document.is_ie) {
			iX = window.event.clientX + document.body.scrollLeft;
			iY = window.event.clientY + document.body.scrollTop;
		} else {
			iX = e.pageX + document.body.scrollLeft;
			iY = e.pageY + document.body.scrollTop;
		}


		if (!self.bDraging) {

			if (!oSelf.iStartX || !oSelf.iStartY) {
				oSelf.iStartX = iX;
				oSelf.iStartY = iY;
			}

			// sensitivity
			var dX = Math.abs(oSelf.iStartX - iX);
			var dY = Math.abs(oSelf.iStartY - iY);
			if (dX < oSelf.iDragSensitivity && dY < oSelf.iDragSensitivity) return true;

			self.bDraging = true;

			oSelf.oDockItem.style.zIndex = 3;
			oSelf.oDockItem.style.position = 'absolute';
			oSelf.oDockItem.style.borderLeft = '0px';
			oSelf.oDockItem.title = '';
			if (oSelf.oDockImg) {
				oSelf.oDockImg.style.borderLeft = '0px';
				oSelf.oDockImg.title = '';
			}

			oSelf.show();
			// hide main menu
			hide();
		}

		oSelf.clearSelection();

		var oDockPos = new absPos(oSelf.oDock);
		oSelf.bOverDock = iY > oDockPos.top && iY < oDockPos.bottom;
		if (oSelf.bOverDock) {
			var oOverDItem = null;
			// calculate over ditem
			for (sDId in arrDockItems) {
				oDItem = arrDockItems[sDId];
				if (oDItem && oDItem.oDockItem && oDItem.sId != oSelf.sId) {
					var oDItemPos = new absPos(oDItem.oDockItem);
					if (iX > oDItemPos.left && iX < oDItemPos.right) {
						oOverDItem = oDItem;
						break;
					}
				}
			}
			//

			if (oOverDItem) {
				if (oSelf.oOverItem) {
					if (oSelf.oOverItem.sId != oOverDItem.sId) {
						oSelf.oOverItem.unmarkPlace();
						oOverDItem.markPlace();
						oSelf.oOverItem = oOverDItem;
					}
				} else {
					oOverDItem.markPlace();
					oSelf.oOverItem = oOverDItem;
				}
			} else {
				if (oSelf.oOverItem) {
					oSelf.oOverItem.unmarkPlace();
					oSelf.oOverItem = null;
				}
			}

		} else if (oSelf.oOverItem) {
			oSelf.oOverItem.unmarkPlace();
			oSelf.oOverItem = null;
		}

		oDockItem.style.top = iY - (oDockItem.offsetHeight / 2) + 'px';
		oDockItem.style.left = iX - (oDockItem.offsetWidth / 2) + 'px';

	}

	document.onmousemove = mouseMoveHdl;

	// mouse up
	var mouseUpHdl = function (e) {
		
		oSelf.iStartX = null;
		oSelf.iStartY = null;

		oDockItem.style.position = '';
		oDockItem.onmouseup = {};
		document.onmousemove = oSelf.oldMouseMove;
		document.onmouseup = oSelf.oldMouseUp;
		document.ondragstart = oSelf.oldDragStart;
		document.body.style.cursor = 'default';
		if (document.is_ie) {
			document.body.onselectstart = this.oldSelectStart? this.oldSelectStart: function () { return true; };
		}
		if (self.bDraging) {
			if (oSelf.oOverItem) {
				if (!oSelf.oOverItem.isTrash) {
					oSelf.addInDock(oSelf.oOverItem);
				} else {
					oSelf.remove(true);
				}
			} else if (oSelf.bOverDock) {
					oSelf.addInDock();
			} else {
				oSelf.remove(true);
			}
			if (oSelf.oOverItem) oSelf.oOverItem.unmarkPlace();
		}

		oSelf.clearSelection();

		self.bDraging = false;

		return false;
	}
	
	document.onmouseup = mouseUpHdl;

}

dockItem.prototype.remove = function (bNeedSave) {
	if (!bNeedSave) var bNeedSave = false;
	try {
		this.oDock.removeChild(this.oDockItem);
		arrDockItems[this.sId] = null;
		if (bNeedSave) this.saveDock();
	} catch (theError) {
		//
	}
}

dockItem.prototype.saveDock = function () {
	if (this.oDock) {
		var sQuery = "";

		for (var iI = 0; iI < this.oDock.childNodes.length; iI++) {
			oDItemNode = this.oDock.childNodes.item(iI);
			if (oDItemNode) {
				sDItemId = oDItemNode.id.substr(9);
				if (sDItemId.length) {
					if (!arrDockItems[sDItemId].bDragDisabled) {
						sQuery += sDItemId + ",";
					}
				}
			}
		}

		if (sQuery.length) {
			sQuery = sQuery.substr(0, sQuery.length - 1);
		}

		this.changeHelpVisible();
		// request
		var url = "/admin/users/json_change_dock/?dock_panel="+sQuery;
		var scriptObj = document.createElement("script");
		scriptObj.src = url;
		document.body.appendChild(scriptObj);
	}
}

dockItem.prototype.addInDock = function (oBeforeDItem) {
	if (!oBeforeDItem) {
		var oBeforeDItem = null;
		for (sId in arrDockItems) {
			if (arrDockItems[sId]) {
				if (arrDockItems[sId].bDragDisabled) {
					oBeforeDItem = arrDockItems[sId];
					break;
				}
			}
		}
	}
	var sTitle = this.oDockItem.title;
	this.remove(false);
	this.oDockItem = null;
	this.create(null, oBeforeDItem, sTitle);
	this.show();
	arrDockItems[this.sId] = this;
	this.saveDock();
}

//