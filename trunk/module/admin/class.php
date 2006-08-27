<?php 

class configuration extends config
{
	var $tab_config;
	var $erreurs;
			
	function configuration()
	{
	 	$this->config((isset($_REQUEST['rubrique']))?$_REQUEST['rubrique']:'site');	 
		$this->menu();
		$this->get_config();
		if (isset($_POST['submit']))
		{
			$this->verifier();
		}	
		$this->afficher();
	}
	
	function get_config()
	{
        if (file_exists(PAGE_ROOT . REP_MODULE . $this->rubrique . '/module_config.php'))
		{
			include(PAGE_ROOT . REP_MODULE . $this->rubrique . '/module_config.php');
			$this->tab_config = $config;
		}	
		$this->getconfig();
	}
	
	function verifier_mail($nom, $valeur, $option)
	{
       if (!preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,4})$`',$valeur))
       {
       	  $this->erreur('L\'adresse mail ' . $valeur . ' n\'est pas valide');
       	  return FALSE;
       }
       return TRUE;
	}

	function verifier_couleur($nom, $valeur, $option)
	{
       if (!preg_match('`^\#[0-9a-fA-F]{6}$`',$valeur))
       {
       	  $this->erreur('La couleur ' . $valeur . ' n\'est pas valide');
       	  return FALSE;
       }
       return TRUE;
	}
		
	function verifier_nombre($nom, $valeur, $option)
	{
       if (!is_numeric($valeur))
       {
       	  $this->erreur($valeur . ' n\'est pas un nombre valide');
       	  return FALSE;
       }
       if (isset($option['bornesup']) && $valeur > $option['bornsup'])
	   {
	   	  $this->erreur($valeur . ' doit être inférieur à ' . $option['bornesup']);
       	  return FALSE;
	   }
       if (isset($option['borneinf']) && $valeur < $option['borninf'])
	   {
	   	  $this->erreur($valeur . ' doit être supérieur à ' . $option['bornesup']);
       	  return FALSE;
	   } 
       return TRUE;
	}
	
	function verifier()
	{
		if (is_array($this->tab_config))
  		   while(list($nom, list($titre, $type, $default, $option)) = each($this->tab_config))
		     {
			 if (isset($_POST[$nom]) && !empty($_POST[$nom]))
		     {
			 	if (method_exists($this,'verifier_'.$type))
				 {
				   	  //if (call_user_func(array($this,'verifier_'.$type), $nom, $_POST[$nom],$option))
				   	  if (eval('$this->verifier_'. $type. '($nom, $_POST[$nom],$option);'))
 	  		  	  	  	 $this->setconfig($nom, $_POST[$nom]);
				  	  else
				  	     $this->config_tab[$nom] = $_POST[$nom];
		         }
				 else
 	           	 	 $this->setconfig($nom, $_POST[$nom]);
		     }
			 else
			 {
			 	 $this->erreur('La valeur '. $nom . ' n\'est pas définie');
              }
       }	   
 	}
 	
 	function erreur($msg)
 	{
 	    $this->erreurs[] = $msg;
 	}
	
	function menu()
	{
	  $dir = opendir(PAGE_ROOT . REP_MODULE);
      while ($module = readdir($dir)) 
	  {
        if (is_file(PAGE_ROOT . REP_MODULE . $module . '/module_config.php')) {
    		echo '<a href="'.ADMIN_NOM_PAGE.'rubrique='.$module.'" style="">'.$module.'</a>&nbsp;&nbsp;';
        }
      }	
      $dir = closedir($dir);
	}
	
	function afficher_haut()
	{
	 echo '<script src="./editeur/colorpicker.js" type="text/javascript"></script>';
			 echo '			 
   <form action="', ADMIN_NOM_PAGE, '" method="post" name="form" target="_self" id="form">
   <input type="hidden" name="submit" value="true">
   <input type="hidden" name="rubrique" value="',$this->rubrique,'">
   <table width="650" valign="top" align="center" border="1" bordercolor="#339933" cellspacing="0">
    <tr>
      <td colspan="2" bgcolor="#339933">
        <div align="center"><H1>Configuration Général</H1> </div>
      </td>
    </tr>
    <tr>
      <td  colspan="2">
      ';
      $this->afficher_erreur();
      echo '<br />
        Merci de le remplir avec exactitude. Vos informations personnels en seront en aucun cas divulgu&eacute;.</td>
    </tr>';
	}
	
	function afficher_bas()
	{
		echo '<tr>
      	<td height="32" bgcolor="#ffffcc">&nbsp;</td>
      	<td><input name="submit" type="submit" value="Sauvegarder"></td>
    	</tr>
  		</table></form>';	
	}
	
	function afficher_erreur()
	{
	   if (count($this->erreurs) > 0)
	   {
	   	echo '<font color=red>Quelques Erreurs sont apparues :<br />';
	   	reset($this->erreurs);
		while(list(, $err) = each($this->erreurs))
			echo $err, '<br />';
		echo '</font>';	
	   }
	}		 
 
	function afficher_texte($nom, $valeur, $option)
	{
	 	$return = '<textarea name="'. $nom .'"';
		if (isset($option['cols']))
		   $return .= ' cols="' . $option['cols'] . '"';
        if (isset($option['rows']))
		   $return .= ' rows="' . $option['rows'] . '"';
		$return .= '>'.$valeur.'</textarea>';
		return $return; 
	}
	
	function afficher_pass($nom, $valeur, $option)
	{
		return '<input type="password" name="'. $nom .'"><br />
			   <input type="password" name="'.$nom.'2">'; 
	}
	
	function afficher_option($nom, $valeur, $option)
	{
	  while(list($val, $titre) = each($option['liste']))
	  {
	 	$return .= '<input type="radio" name="'. $nom .'" value="'.$val.'"';
	 	if ($val == $valeur)
	 	   $return .= ' checked '; 
		$return .= ' > ' . $titre . '&nbsp&nbsp;';
	  }
	  return $return; 
	}
	
	function afficher_case($nom, $valeur, $option)
	{
	  while(list($val, $titre) = each($option['liste']))
	  {
	 	$return .= '<input type="checkbox" name="'. $nom .'[]" value="'.$val.'"';
	 	if (in_array($val, $valeur))
	 	   $return .= ' checked '; 
		$return .= ' > ' . $titre . '&nbsp&nbsp;';
	  }
	  return $return; 
	}
	
	function afficher_couleur($nom, $valeur, $option)
	{	  
	  $return = "<script>initPicker('" . $nom. "', '" . $valeur; 
	  $return .= "');</script>"; 
	  return $return;
	}
	
	function afficher_liste($nom, $valeur, $option)
	{
	  $return = '<select name="' . $nom . '">';
	  while(list($val, $titre) = each($option['liste']))
	  {
	 	$return .= '<option value="'.$val.'"';
		if ($val == $valeur)
		   $return .= ' selected '; 
	    $return .= '> ' . $titre . '&nbsp&nbsp;';
	  }
	  $return .= '</select>';
	  return $return; 
	}
	
	function afficher_($nom, $titre, $type, $option)
	{
		echo '<tr>
      <td bgcolor="#ffffcc">', $titre, '</td>
      <td>';
      if (method_exists($this,'afficher_'.$type))
      	echo call_user_func(array($this,'afficher_'.$type), $nom, $this->get($nom),$option);
 	  else
     	echo '<input name="', $nom, '" type="text" id="', $nom, '"  value="', $this->get($nom), '">';
 	  echo '</td>
    </tr>';
	}
	
	function afficher()
	{
			$this->afficher_haut();
			if (is_array($this->tab_config))
			{
			   reset($this->tab_config);
			   while(list($nom, list($titre, $type, $default, $option)) = each($this->tab_config))
			   {
 					$this->afficher_($nom, $titre, $type, $option);			 
			   }				
			}
			else
				echo 'Pas de configuration pour ce module';			   
			$this->afficher_bas();
	}
}
?>