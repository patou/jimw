<?php

/*
****************** JPIE *******************
***** Javascript and PHP Image Editor *****
*******************************************

Dec. 16. 2007
By Juan Alfonso Pati�o S�nchez
alfonso at dusnic.com

*/

	include('../configure.php');
	$img_size = getImageSize(TMP_IMAGE_PATH . $_GET['src']);
?>

<html>
<head>
<title>Resize options</title>
<script>
function resize(){
	new_width = document.getElementById('x_scale').value;
	new_height = document.getElementById('y_scale').value;	
	window.opener.resize(new_width,new_height);
}

function changeSize(x){

	<?php echo "imgRatio = " . $img_size[0]/$img_size[1] . ";"; ?>
	if (document.getElementById('proportional').checked  == true){
		switch(x){
			case 'w':
				document.getElementById('y_scale').value = Math.floor(document.getElementById('x_scale').value / imgRatio);			
			break;
			case 'h':
				document.getElementById('x_scale').value = Math.floor(document.getElementById('y_scale').value * imgRatio);
			break;
		}	
	}
	
}
</script>
</head>
<body>
	<table>
		<tr>
			<td>Width:</td>
			<td><input type="text" name="x_scale" id="x_scale" value="<?php echo $img_size[0] ?>" onKeyUp="changeSize('w');"> px</td>
		</tr>
		<tr>
			<td>Height:</td>
			<td><input type="text" name="y_scale" id="y_scale" value="<?php echo $img_size[1] ?>" onKeyUp="changeSize('h');"> px</td>
		</tr>
	</table>
	<div>
		<input type="checkbox" value="1" id="proportional" checked /> mantain aspect ratio
	</div>
	<input type="button" name="resize" value="Resize image" onClick="resize();">
	<input type="button" name="cancel" value="Cancel" onClick="window.close();">
</body>
</html>