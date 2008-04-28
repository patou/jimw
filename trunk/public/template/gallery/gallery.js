
function see_img(chemin_img,hauteur,largeur){
	var hauteur=hauteur ;
	var largeur=largeur;
	$('photo_loading').innerHTML = "";
	$('photo_image').innerHTML = "<img src=\""+chemin_img+"\" width=\""+largeur+"\" height=\""+hauteur+"\" alt=\"\" onclick=\"hide_img();\" />";
	$('photo_image').style.display = 'block';
	var fx = new Fx.Style($('photo_image'), 'opacity', {duration: 500}).start(0, 1);
}

function hide_img()
{
	//var fx = new Fx.Slide('photo_image', {duration: 500});
	//fx.slideOut ();
	$('photo_image').innerHTML = "";
	$('photo_loading').innerHTML = "";
	$('photo_titre').innerHTML = "";
	$('photo_prev').innerHTML = "";
	$('photo_next').innerHTML = "";
}

function redim_image(url, height, width)
{
	var maxWidth = 600;
	var maxHeight = 400;
	$('photo_loading').innerHTML = "Chargement Terminé !";
	var h = dH = height;
	var w = dW = width;
	if ((h >= maxHeight) || (w >= maxWidth)) {
		if ((h >= maxHeight) && (w >= maxWidth)) {
			if (h > w) {
				dH = maxHeight;
				dW = parseInt((w * dH) / h, 10);
			} else {
				dW = maxWidth;
				dH = parseInt((h * dW) / w, 10);
			}
		} else if ((h > maxHeight) && (w < maxWidth)) {
			dH = maxHeight;
			dW = parseInt((w * dH) / h, 10);
		} else if ((h < maxHeight) && (w > maxWidth)) {
			dW = maxWidth;
			dH = parseInt((h * dW) / w, 10);
		}
	}
	see_img(url,dH,dW);
}

function display_img(url, titre)
{
	$('photo_loading').innerHTML = "Chargement de l'image ...";
	$('photo_image').innerHTML = "<img id=\"photo_image_id\" align=\#center\" src=\""+url+"\" Onload=\"redim_image('"+url+"', this.height, this.width);\" style=\"opacity: 0\">";
	if (navigator.appVersion.indexOf("MSIE") == -1)
		$('photo_image').style.display = 'none';
	var title = titre;
	$('photo_titre').innerHTML = title;
}

function display_picture(num)
{
	display_img(image[num].url, image[num].title);
	if (num > 0)
		$('photo_prev').innerHTML = '<a href="#" onclick="display_picture('+(num - 1)+');">Précedente</a>';
	else
		$('photo_prev').innerHTML = '';
	if (num + 1 < image.length)
		$('photo_next').innerHTML = '<a href="#" onclick="display_picture('+(num + 1)+');">Suivante</a>';
	else
		$('photo_next').innerHTML = '';
}


function display_download() {
	$$('span.download_hidden').each(function(el) {
		el.toggleClass('download_hidden');
	});
	$('download_button').toggleClass('download_hidden');
	$('download_link').addClass('download_hidden');
}

function select_all() {
	$$('input.check').each( function(el) {
		el.checked = true;
	});
}