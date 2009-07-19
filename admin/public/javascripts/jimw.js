function addOnloadScript(fct) {
	if (window.addEventListener)
		window.addEventListener('load', fct, false);
	else
		window.attachEvent('onload', fct);
}

function openWindow(url, width, height, options) {
	if (options == undefined)
		options = '';
	var iLeft = (screen.width - width) / 2;
	var iTop  = (screen.heigth - height) / 2;

	var sOptions = "menubar=no,location=no,directories=no,toolbar=no,status=no,resizable=no,dependent=yes,scrollbars=auto" ;
	sOptions += ",width=" + width ;
	sOptions += ",height=" + height ;
	sOptions += ",left=" + iLeft ;
	sOptions += ",top=" + iTop ;
	sOptions += options;

	window.open( url, 'JIMWBrowseWindow', sOptions ) ;
}

function cleanString(chaine) {
  if (chaine == "/")
	  return chaine;
  temp = chaine.toLowerCase().replace(/[àâäàã]/gi,"a");
  temp = temp.replace(/[éèêë€]/gi,"e");
  temp = temp.replace(/[ìïî]/gi,"i");
  temp = temp.replace(/[öôò]/gi,"o");
  temp = temp.replace(/[ùûü]/gi,"u");
  temp = temp.replace(/[ç]/gi,"c");
  temp = temp.replace(/([^a-z0-9_]+)/g,"-");
  return temp
}

function fillAlias(value, form) {
  if (form.tree_menutitle && form.tree_menutitle.value == "") {
  		form.tree_menutitle.value = value;
  }
  if (form.tree_pagetitle && form.tree_pagetitle.value == "") {
  		form.tree_pagetitle.value = value;
  }
  if (form.tree_alias && form.tree_alias.value == "") {
  		form.tree_alias.value = cleanString(value);
  }
}