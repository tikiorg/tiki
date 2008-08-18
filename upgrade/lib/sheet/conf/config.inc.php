<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*Information sur la configuration des chemins*/
@define("APATH_ROOT_WIN", 		str_replace(basename(dirname(__FILE__)), "", dirname(__FILE__)));
@define("APATH_ROOT", 			ereg_replace("//","/",join('/',preg_split("/[\/\\\]/",APATH_ROOT_WIN))."/"));
@define("APICO_ROOT", 			"http://".$_SERVER["HTTP_HOST"].str_replace($_SERVER["DOCUMENT_ROOT"], "", APATH_ROOT));
@define("CACHE_PATH", 			APATH_ROOT."repository");
@define("APIC_LIBRARY_PATH", 	APATH_ROOT."include/");
  
@Define("DRVXML", 				"DOMXML");


/*information sur la configuration de la securité @PICNet FrameWorks*/
@define("SECUR_INCLUDE", 			"./");

/*Configuration de la gestion des erreurs de moteur de template*/
@define("ERROR_MANAGER_SYSTEM", 	"on");    		//Les erreurs sont remontées pour on, ignorées pour off.
@define("ERROR_MANAGER_LEVEL", 		"0");      		//Précise le niveau d"erreur toléré, plus il est bas, moins les erreurs sont tolérées.
//@define("ERROR_MANAGER_ESCAPE", 	APATH_ROOT."include/org/apicnet/ui/erreur.html"); //Permet de spécifier une url locale de remplacement en cas de remontée d"erreurs.
@define("ERROR_MANAGER_LOG", 		APATH_ROOT."cache/erreur.log");	//Permet de définir un fichier de log.
@define("ERROR_MANAGER_ALARME", 	"");	//Permet de définir une série d"adresse email à laquelle sera envoyé un mail d"alerte.

/*Configuration générale de l'application*/
@define("APIC_VERBOSE_MODE", 	 	TRUE);
@define("APIC_ZEND_ENCODER_MODE", 	FALSE);

/*Inclusion du moteur d'import des class php*/
include_once(APATH_ROOT."include/core/APIClass.php");
?>
