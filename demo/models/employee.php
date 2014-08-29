<?php


class employee extends objectModel {
	
	
	
	function __construct($id = NULL) {
		
		$this->settings['addon'] =	array('pagename' => 'Mitarbeiter', 'menu' => true, 'position' => 2, 'recorddelete' => true, 'recordedit' => true);
		$this->settings['database'] = array(
										'table' => 'employees',
										'primary' => 'id_employee',
										'fields' => array('id_employee' => array(
																			'type' => 'hidden',
																			'label' => 'ID',
																			'rexlist' => true,
																			'rexform' => false,
																		  ),
								 						  'firstname' => array(
								 						  				'type' => 'input',
													 					'label' => 'Vorname',
																		'rexlist' => true,
																		'rexform' => true,
								 									),
								 						  'lastname' => array(
								 						  				'type' => 'input',
													 					'label' => 'Nachname',
																		'rexlist' => true,
																		'rexform' => true,
								 									),
								 									
								 						  'email' => array(
								 						  				'type' => 'input',
													 					'label' => 'Email',
																		'rexlist' => true,
																		'rexform' => true,
								 									),
								 						
													 ),
											
											'relations' => array(
												
	 						  					  	'member' =>  array('type' => 'MANY_MANY',
																		'model' => 'project',
																		'title' => 'Projekte',
	 						  					  					    'table' => 'projects_employees', 
	 						  					  					    'primary' => 'id_projects_employees',
	 						  					  					    'where' => '',
	 						  					  					    'select' => 'projects.title',
	 						  					  					    'rexform' => true,
	 						  					  					    'readonly' => true
	 						  					  					  ),
	 						  					  			)
	 						  					  
										);
												
		
				
		
		parent::__construct($id);
		
	}
	
	
}