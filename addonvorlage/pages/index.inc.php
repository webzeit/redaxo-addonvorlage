<?php

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$function = rex_request('func', 'string');


switch($function) {
	
	case 'add':
	case 'edit':
		$addon->formModel($subpage, rex_request('primary', 'int'));
	break;

	case 'del':
		$addon->deleteRecord($subpage, rex_request('primary', 'int'));
		$addon->listModel($subpage);
	break;
	
	case 'delrelation':
		$sql = rex_sql::factory();
		$sql->setTable(rex_request('relation_table', 'string'));
		$sql->setWhere(rex_request('relation_primary', 'string'). ' = '. rex_request('relation_id', 'int'));
	    $sql->delete();
		$addon->formModel($subpage, rex_request('primary', 'int'));
	break;
		
	case 'cycle':
		$addon->cycle($subpage, rex_request('field', 'string'), rex_request('primary', 'int'));
		$addon->listModel($subpage);
	break;
	
	default:
		$addon->listModel($subpage);
}


/* output */

require $REX['INCLUDE_PATH'].'/layout/top.php';
rex_title($addon->getSiteTitle($subpage), $REX['ADDON']['pages'][$page]);
echo  '<div class="rex-addon-output-v2 webzeitaddon">'.$addon->output.'</div>';
require $REX['INCLUDE_PATH'].'/layout/bottom.php';


?>