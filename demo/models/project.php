<?php


class project extends objectModel {
	
	
	
	function __construct($id = NULL) {
		
		$this->settings['addon'] =	array('pagename' => 'Projekte', 'menu' => true, 'position' => 1, 'recorddelete' => true, 'recordedit' => true);
		$this->settings['database'] = array(
										'table' => 'projects',
										'primary' => 'id_project',
										'fields' => array('id_project' => array(
																			'type' => 'hidden',
																			'label' => 'ID',
																			'rexlist' => true,
																			'rexform' => false,
																		  ),
								 						  'title' => array(
								 						  				'type' => 'input',
													 					'label' => 'Titel',
																		'rexlist' => true,
																		'rexform' => true,
								 									),
								 						  'short_description' => array(
								 						  					  'type' => 'textarea',
								 						  					  'label' => 'Kurzbeschreibung',
								 						  					  'rexlist' => true,
								 						  					  'rexform' => true,
													 	   				   ),
													 	  'long_description' => array(
								 						  					  'type' => 'textarea',
								 						  					  'label' => 'Beschreibung',
								 						  					  'rexlist' => false,
								 						  					  'rexform' => true,
								 						  					  'cssClass' => 'tinyMCEEditor-table',
													 	   				   ),
													 	   				   
													 	   'logo' => array(
								 						  					  'type' => 'mediabutton',
								 						  					  'label' => 'Logo',
								 						  					  'rexlist' => false,
								 						  					  'rexform' => true
													 	   				   ),
													 	   				   
													 	   'images' => array(
								 						  					  'type' => 'medialist',
								 						  					  'label' => 'Bilder',
								 						  					  'rexlist' => false,
								 						  					  'rexform' => true
													 	   				   ),
													 	   'link' => array(
								 						  					  'type' => 'linkbutton',
								 						  					  'label' => 'Link',
								 						  					  'rexlist' => false,
								 						  					  'rexform' => true
													 	   				   ),				   
													 	   'show' => array(
								 						  					  'type' => 'radio',
								 						  					  'label' => 'Anzeigen',
								 						  					  'rexlist' => false,
								 						  					  'values' => array(0 => 'ja',1 => 'nein'),
								 						  					  'rexform' => true,
								 						  					  
													 	   				   ),
													 	   				   
											
													 	  'devices' => array(
								 						  					  'type' => 'checkbox',
								 						  					  'label' => 'Geräte',
								 						  					  'rexlist' => false,
								 						  					  'values' => array(0 => 'iPhone',1 => 'iPad', 2 => 'Desktop'),
								 						  					  'rexform' => true,
								 						  					  
													 	   				   ),
													 	    				   
													 	  'manager' => array(
								 						  					  'type' => 'select',
								 						  					  'label' => 'Manager',
								 						  					  'rexlist' => true,
								 						  					  'relation' => true,
								 						  					  'rexform' => true,
								 						  					  
													 	   				   ),
													 	   				   

													 	  'status' => array(
								 						  					  'type' => 'cycle',
								 						  					  'label' => 'Status',
								 						  					  'rexlist' => true,
								 						  					  'values' => array('offline' => 'closedbeta', 'closedbeta' => 'online', 'online' => 'offline'),
								 						  					  'rexform' => true,
								 						  					  
													 	   				   ),
													 ),
											'relations' => array(
													
													'manager' => array('type' => 'BELONGS_TO',
																		'model' => 'employee',
	 						  					  					    'table' => 'employees', 
	 						  					  					    'primary' => 'id_employee',
	 						  					  					    'where' => '',
	 						  					  					    'select' => 'CONCAT(employees.firstname," (",employees.email,")")',
	 						  					  					  ),
	 						  					 
	 						  					  	'team' =>  array('type' => 'MANY_MANY',
																		'model' => 'employee',
																		'title' => 'Team',
	 						  					  					    'table' => 'projects_employees', 
	 						  					  					    'primary' => 'id_projects_employees',
	 						  					  					    'where' => '',
	 						  					  					     'select' => 'CONCAT(employees.firstname," ",employees.lastname," (",employees.email,")")',
	 						  					  					    'rexform' => true,
	 						  					  					  ),
	 						  					  					  
	 						  					  	'notes' =>  array('type' => 'HAS_MANY',
																		'model' => 'note',
																		'title' => 'Notiz',
	 						  					  					    'table' => 'notes', 
	 						  					  					    'primary' => 'id_note',
	 						  					  					    'foreign' => 'id_project',
	 						  					  					    'where' => '',
	 						  					  					     'select' => 'title',
	 						  					  					    'rexform' => true,
	 						  					  					  ),
											
											
	 						  					  	)
										);
												
		
				
		
		parent::__construct($id);
		
	}
	
	
}