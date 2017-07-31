document.is_ie = !(navigator.appName.indexOf("Netscape") != -1);

function multipleGuideInput(id) {
	if(!id) {
		return alert("[multipleGuideInput] Incorrect id given.");
	}

	this.id = id;
	this.init();
	this.collectValues();
	this.bindEvents();
}

multipleGuideInput.prototype.id = null;
multipleGuideInput.prototype.selectObj = null;
multipleGuideInput.prototype.inputObj = null;
multipleGuideInput.prototype.values = new Array();


multipleGuideInput.prototype.init = function () {
	var selectObj = document.getElementById("multipleGuideSelect_" + this.id);
	if(typeof selectObj != "object") {
		return alert("[multipleGuideInput] Can't find correct selectbox.");
	}
	this.selectObj = selectObj;

	var inputObj = document.getElementById("multipleGuideInput_" + this.id);
	if(typeof inputObj != "object") {
		return alert("[multipleGuideInput] Cant find correct input.");
	}
	this.inputObj = inputObj;
};


multipleGuideInput.prototype.collectValues = function () {
	var options = this.selectObj.options, l = options.length, i;
	var values = new Array();

	for(i = 0; i < l; i++) {
		values.push(options[i].innerHTML);
	}
	this.values = values;
};



multipleGuideInput.prototype.bindEvents = function () {
	var __self = this;
	this.inputObj.onkeypress = function (keyEvent) {
		var keyCode = (document.is_ie) ? event.keyCode : keyEvent.keyCode;

		if(keyCode == 13) {
			var val = this.value;

			if(val) {
				__self.addItem(val);
				this.value = "";
			}

			return false;
		} else {
			return true;
		}
	};
};


multipleGuideInput.prototype.checkIfExists = function (value) {
	var i, l = this.values.length;
	for(i = 0; i < l; i++) {
		if(this.values[i] == value) {
			return true;
		}
	}
	return false;
};


multipleGuideInput.prototype.addItem = function (value) {
	if(this.checkIfExists(value)) {
		//TODO
	} else {
		var option = document.createElement("option");
		option.innerHTML = "&rarr;&nbsp;&nbsp;" + value;
		option.value = value;

		if(this.selectObj.options.length == 0) {
			this.selectObj.appendChild(option);
		} else {
			this.selectObj.insertBefore(option, this.selectObj.firstChild);
		}
		this.values.push(value);
	}

	this.selectItem(value);
};


multipleGuideInput.prototype.selectItem = function (value) {
	var options = this.selectObj.options, l = options.length, i;

	for(i = 0; i < l; i++) {
		var option = options[i];

		if(option.innerHTML == value || option.value == value) {
			option.selected = true;
			option.checked = true;
			return true;
		}
	}

	return false;
};
