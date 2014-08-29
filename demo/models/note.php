<?php


class note extends objectModel {
	
	
	
	function __construct($id = NULL) {
		
		$this->settings['addon'] =	array('pagename' => 'Notizen', 'menu' => true, 'position' => 3, 'recorddelete' => true, 'recordedit' => true);
		$this->settings['database'] = array(
										'table' => 'notes',
										'primary' => 'id_note',
										'fields' => array('id_note' => array(
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
								 						  'note' => array(
								 						  				'type' => 'textarea',
													 					'label' => 'Notiz',
													 					'cssClass' => 'tinyMCEEditor-table',
																		'rexlist' => true,
																		'rexform' => true,
								 									),
								 									
								 						  'id_project' => array(
								 						  				'type' => 'select',
													 					'label' => 'Projekt',
																		'rexlist' => false,
																		'rexform' => true,
																		'relation' => true
								 									),
								 						
													 ),
										'relations' => array(
													
													'id_project' => array('type' => 'BELONGS_TO',
																		'model' => 'project',
	 						  					  					    'table' => 'projects', 
	 						  					  					    'primary' => 'id_project',
	 						  					  					    'where' => '',
	 						  					  					    'select' => 'projects.title',
	 						  					  					  ),
	 						  			
	 						  					  		)
										);
												
		
				
		
		parent::__construct($id);
		
	}
	
	
}