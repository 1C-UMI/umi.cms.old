var last_src = "";


function cms_vote_postDo(formName, inputName, nstext) {
	fObj = document.forms[formName];
	if(!fObj)
		return false;

	eval('iObj = fObj.' + inputName + ';');
	if(!iObj)
		return false;

	res = false;
	for(i = 0; i < iObj.length; i++)
		if(iObj[i].checked)
			res = iObj[i].value;


	if(res) {
		sc = document.createElement("script");
		sc.src = "/vote/post/" + res + "/?m=" + new Date().getTime();

		fObj.appendChild(sc);
	} else {
		if(nstext) {
			alert(nstext);
		}
	}
}
