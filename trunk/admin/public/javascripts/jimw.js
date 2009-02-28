function addOnloadScript(fct) {
	if (window.addEventListener)
		window.addEventListener('load', fct, false);
	else
		window.attachEvent('onload', fct);
}