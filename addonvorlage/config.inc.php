<?php


$base = dirname(__FILE__);
$page = basename($base);									

if(!class_exists('addonctrl'))
	include_once($base."/classes/addonctrl.php");

$addon = new addonctrl($base);

rex_register_extension('PAGE_HEADER', function() {
	$addonname = 'addonvorlage';
	echo '<script type="text/javascript" src="/files/addons/'.$addonname.'/js/main.js"></script>
		 <link media="screen" href="/files/addons/'.$addonname.'/css/styles.css" type="text/css" rel="stylesheet">';
});


if($REX['REDAXO'] && is_object($REX['USER'])) {
	
	$REX['ADDON']['name'][$page] 			= 'addonvorlage';
	$REX['ADDON']['perm'][$page] 			= $page.'[]';
	$REX['ADDON']['version'][$page] 		= '0.1';
	$REX['ADDON']['author'][$page] 			= ' ';
	$REX['ADDON']['supportpage'][$page] 	= 'forum.redaxo.de';
	
	$REX['PERM'][] = $page.'[]'; 
	
	$REX['ADDON'][$page]['STARTPAGE'] = $addon->models[key($addon->models)]->settings['addon']['pagename'];
	
	foreach($addon->models as $key => $model)
		if($model->settings['addon']['menu'])
			$REX['ADDON'][$page]['SUBPAGES'][$model->settings['addon']['position']] = array($key,$model->settings['addon']['pagename']);
		
	ksort($REX['ADDON'][$page]['SUBPAGES']);
}



?>
