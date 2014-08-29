<?php

class addonctrl {
	
	public $base;
	public $models;
	public $addonname;
	public $output;
	
	function __construct($base = NULL) {
		
		$this->base = $base;
		$this->addonname = basename($this->base);
		$this->includeClasses();
		
	}
	
	private function includeClasses() {
		
		if(!class_exists('objectmodel'))
			include_once($this->base."/classes/objectmodel.php");
			
		foreach(glob($this->base."/models/*.php") as $file) {
			
			$class = rtrim(basename($file),'.php');
			if(class_exists($class))
				die('error objectmodel : duplicate model name');
		
			include_once($file);
			$this->models[$class] = new $class;
		}
		
	}
	
	public function getSiteTitle($class) {
		
		if(!array_key_exists($class,$this->models))
			return false;
			
		return $this->models[$class]->settings['addon']['pagename'];
	}
	
	public function formModel($class, $id) {
	
		if(!array_key_exists($class,$this->models))
			return false;
		
		/*
		if(isset($_POST['redirect'])) {
			// trick rex_form redirect 
			$_GET['subpage'] = rex_request('redirect', 'string');
			$_GET['primary'] = rex_request('redirectid', 'int');
		}
		*/		
		
		$form = rex_form::factory($this->models[$class]->settings['database']['table'],$this->models[$class]->settings['addon']['pagename'],$this->models[$class]->settings['database']['primary']."=".$id,'post',0);
		
		foreach($this->models[$class]->settings['database']['fields'] as $field => $settings)
			if($settings['rexform']) {
				
				switch($settings['type']) {
					
					case 'hidden':
						$formfield = &$form->addHiddenField($field);
					break;

					case 'input':
						$formfield = &$form->addTextField($field);
						$formfield->setLabel($settings['label']);
					break;
					
					case 'textarea':
						$formfield = &$form->addTextareaField($field);
						$formfield->setLabel($settings['label']);
						if($settings['cssClass'])
							$formfield->setAttribute('class',$settings['cssClass']);
					break;
					
					case 'select':
						$formfield = &$form->addSelectField($field);
						$formfield->setLabel($settings['label']);
						$formfieldselect =& $formfield->getSelect();
						$formfieldselect->setSize(1);				
						if(array_key_exists('relation', $settings) && $settings['relation'])
							$formfieldselect->addSqlOptions("SELECT ".$this->models[$class]->settings['database']['relations'][$field]['select']." as label, ".$this->models[$class]->settings['database']['relations'][$field]['primary']." as id FROM ".$this->models[$class]->settings['database']['relations'][$field]['table']." ".$this->models[$class]->settings['database']['relations'][$field]['where']);
						else
							foreach($settings['values'] as $value => $option) 
								$formfieldselect->addOption($option,$value);	
					break;
					
					case 'cycle':
						$formfield = &$form->addSelectField($field);
						$formfield->setLabel($settings['label']);
						$formfieldselect =& $formfield->getSelect();
						$formfieldselect->setSize(1);
						foreach($settings['values'] as $value => $option) 
							$formfieldselect->addOption($value,$value);	
					break;
					
					case 'radio':
						$formfield = &$form->addRadioField($field);
						$formfield->setLabel($settings['label']);
						foreach($settings['values'] as $value => $option) 
							$formfield->addOption($option,$value);	
					break;
					
					case 'checkbox':
						$formfield = &$form->addCheckboxField($field);
						$formfield->setLabel($settings['label']);
						foreach($settings['values'] as $value => $option) 
							$formfield->addOption($option,$value);	
					break;
					
					
					case 'linkbutton':
						$formfield = &$form->addLinkmapField($field);
						$formfield->setLabel($settings['label']);
					break;
					
					case 'mediabutton':
						$formfield = &$form->addMediaField($field);
						$formfield->setLabel($settings['label']);
					break;
					
					case 'medialist':
						$formfield = &$form->addMediaListField($field);
						$formfield->setLabel($settings['label']);
					break;
					
					
				}
				
			}
			
		if($id > 0)
			$form->addParam('primary', $id);
		
		if(rex_request('redirect','string') != '') {
			$form->addParam('redirect', rex_request('redirect','string'));
			$form->addParam('redirectid', rex_request('redirectid','int'));
		}
		
		
		$this->output .= $form->get();
		
		if(array_key_exists('relations', $this->models[$class]->settings['database']) && count($this->models[$class]->settings['database']['relations']) > 0)
			foreach($this->models[$class]->settings['database']['relations'] as $field => $settings) 
				if($settings['type'] == 'MANY_MANY')
					$this->listRelatedModelManyMany($settings, $class, $id);
				elseif($settings['type'] == 'HAS_MANY')
					$this->listRelatedModelHasMany($settings, $class, $id);
		
	}
	
	public function listRelatedModelHasMany($relation, $parent, $id) {
	
		if(!array_key_exists($relation['model'], $this->models))
			return false;
			
		
		$class = $relation['model'];
	
		$this->output .= '<div class="webzeitaddon-hasmany"><h2>'.$relation['title'].'</h2>';
			
			
		foreach($this->models[$class]->settings['database']['fields'] as $field => $settings)
			if($settings['rexlist'])
				$listFields[] = $this->models[$class]->settings['database']['table'].'.'.$field.' as '.$field;
		
			$list = rex_list::factory('SELECT '.implode($listFields,",").' FROM '.$relation['table'].' 
										WHERE '.$relation['table'].'.'.$relation['foreign'].' = '.$id, 30, NULL, 0);
		

		
		foreach($this->models[$class]->settings['database']['fields'] as $field => $settings)
			if($settings['rexlist']) {
				$list->setColumnLabel($field, $settings['label']);	
					if($settings['type'] == 'cycle') 
					$list->setColumnParams($field, array('func' => 'cycle', 'field' => $field, 'primary' => '###'.$this->models[$class]->settings['database']['primary'].'###'));
			}

		
		if($this->models[$class]->settings['addon']['recorddelete']) {
			$list->addColumn(
				'delete', 
				'<img src="/files/addons/'.$this->addonname.'/img/cross.png" alt="delete" title="delete" />',
				 -1, 
				 array( '<th class="rex-icon">###VALUE###</th>', '<td class="rex-addon-delete">###VALUE###</td>' )
			);
			$list->setColumnParams('delete', array('func' => 'del', 'redirect' =>$parent, 'redirectid' => $id, 'subpage' =>$class ,'primary' => '###'.$this->models[$class]->settings['database']['primary'].'###'));
			$list->setColumnLabel('delete', '');	
		}
		
		if($this->models[$class]->settings['addon']['recordedit']) {
			$list->addColumn(
				'edit', 
				'<img src="/files/addons/'.$this->addonname.'/img/pen.png" alt="edit" title="edit" />',
				 -1, 
				 array( '<th class="rex-icon"><a href="'. $list->getUrl(array('func' => 'add', 'subpage' => $class,  'redirect' =>$parent, 'redirectid' => $id, )) .'"><img src="/files/addons/'.$this->addonname.'/img/add.png" alt="add" title="add" /></a></th>', '<td class="rex-addon-edit">###VALUE###</td>' )
			);
			$list->setColumnParams('edit', array('func' => 'edit',  'redirect' =>$parent, 'redirectid' => $id, 'subpage' =>$class ,'primary' => '###'.$this->models[$class]->settings['database']['primary'].'###'));
			$list->setColumnLabel('edit', 'Edit');	
		}
			
		
		$this->output .= $list->get().'</div>';
		
	
	}
	
	
	public function listRelatedModelManyMany($relation, $parent, $id) {
		
	
		if(!array_key_exists($relation['model'], $this->models))
			return false;

		$model = $this->models[$relation['model']];
		$parent = $this->models[$parent];

		$this->output .= '<div class="webzeitaddon-manymany"><h2>'.$relation['title'].'</h2>';
		
		if(!array_key_exists('readonly',$relation))
			$relation['readonly'] = false;
			
		if($id > 0 && !$relation['readonly']) {
			$formRelated = rex_form::factory($relation['table'],'Link',$relation['primary']."=0",'post',0);
			$formfield = &$formRelated->addHiddenField($relation['primary']);
			$formfield = &$formRelated->addHiddenField($parent->settings['database']['primary'], $id);
		
			$formfield = &$formRelated->addSelectField($model->settings['database']['primary']);
			$formfield->setLabel($model->settings['addon']['pagename']);
			$formfieldselect =& $formfield->getSelect();
			$formfieldselect->setSize(1);	
			$formfieldselect->addSqlOptions('SELECT '.$relation['select'].' as label,'.$model->settings['database']['primary'].' as id FROM '.$model->settings['database']['table']);
			
			$formRelated->addParam('list', md5($relation['table'].$relation['primary']."=0".'post'));
			
			if($id > 0)
				$formRelated->addParam('primary', $id);
			
			$this->output .= $formRelated->get();
		}
		
		foreach($model->settings['database']['fields'] as $field => $settings)
			if($settings['rexlist'])
				$listFields[] = $model->settings['database']['table'].'.'.$field.' as '.$field;
		
		$list = rex_list::factory('SELECT '.implode($listFields,",").','.$relation['table'].'.'.$relation['primary'].' as relationid 
									FROM '.$relation['table'].' 
									LEFT JOIN '.$model->settings['database']['table'].' 
										ON ('.$relation['table'].'.'.$model->settings['database']['primary']. ' = '.$model->settings['database']['table'].'.'.$model->settings['database']['primary'].')
									WHERE '.$relation['table'].'.'.$parent->settings['database']['primary'].' = '.$id, 30, NULL, 0);
		

		foreach($model->settings['database']['fields'] as $field => $settings)
			if($settings['rexlist']) {
				$list->setColumnLabel($field, $settings['label']);	
					/*
					 * no cycle in form detail view
					 *
					if($settings['type'] == 'cycle') 
					$list->setColumnParams($field, array('func' => 'cycle', 'field' => $field, 'primary' => '###'.$this->models[$class]->settings['database']['primary'].'###'));
					*/
			}


			$list->removeColumn('relationid');
			
			if(!$relation['readonly']) {
				$list->addColumn(
					'del', 
					'<img src="/files/addons/'.$this->addonname.'/img/link.png" alt="unlink" title="unlink" />',
					 -1, 
					 array( '<th class="rex-icon"></th>', '<td class="rex-addon-edit">###VALUE###</td>' )
				);
				$list->setColumnParams('del', array('func' => 'delrelation', 'primary' => $id, 'relation_id' => '###relationid###', 'relation_table' => $relation['table'], 'relation_primary' => $relation['primary']));
				$list->setColumnLabel('del', '');	
			}
		
		$this->output .= $list->get().'</div>';

	}
	
	public function listModel($class) {
		
		if(!array_key_exists($class,$this->models))
			return false;

		$list = $this->initRexListFactory($class);
		
		foreach($this->models[$class]->settings['database']['fields'] as $field => $settings)
			if($settings['rexlist']) {
				$list->setColumnLabel($field, $settings['label']);	
					if($settings['type'] == 'cycle') 
					$list->setColumnParams($field, array('func' => 'cycle', 'field' => $field, 'primary' => '###'.$this->models[$class]->settings['database']['primary'].'###'));
			}

		
		if($this->models[$class]->settings['addon']['recorddelete']) {
			$list->addColumn(
				'delete', 
				'<img src="/files/addons/'.$this->addonname.'/img/cross.png" alt="delete" title="delete" />',
				 -1, 
				 array( '<th class="rex-icon">###VALUE###</th>', '<td class="rex-addon-delete">###VALUE###</td>' )
			);
			$list->setColumnParams('delete', array('func' => 'del', 'primary' => '###'.$this->models[$class]->settings['database']['primary'].'###'));
			$list->setColumnLabel('delete', '');	
		}
		
		if($this->models[$class]->settings['addon']['recordedit']) {
			$list->addColumn(
				'edit', 
				'<img src="/files/addons/'.$this->addonname.'/img/pen.png" alt="edit" title="edit" />',
				 -1, 
				 array( '<th class="rex-icon"><a href="'. $list->getUrl(array('func' => 'add')) .'"><img src="/files/addons/'.$this->addonname.'/img/add.png" alt="add" title="add" /></a></th>', '<td class="rex-addon-edit">###VALUE###</td>' )
			);
			$list->setColumnParams('edit', array('func' => 'edit', 'primary' => '###'.$this->models[$class]->settings['database']['primary'].'###'));
			$list->setColumnLabel('edit', 'Edit');	
		}
			
		
		$this->output .= $list->get();
				
	}
	
	private function initRexListFactory($class) {
		
		$joins = array();
		$where = '';
		$from = $this->models[$class]->settings['database']['table'];
		
		foreach($this->models[$class]->settings['database']['fields'] as $field => $settings)
			if($settings['rexlist']) {
			
				if(array_key_exists('relation', $settings) && $settings['relation']) {
					
					$listFields[] = $this->models[$class]->settings['database']['relations'][$field]['select'].' as '.$field;	
					
					$joins[] = 'LEFT JOIN '.$this->models[$class]->settings['database']['relations'][$field]['table'].' 
										ON ('.$this->models[$class]->settings['database']['relations'][$field]['table'].'.'.$this->models[$class]->settings['database']['relations'][$field]['primary']. ' = '.$this->models[$class]->settings['database']['table'].'.'.$field.')';
				
					
				
				} else {
					
					$listFields[] = $this->models[$class]->settings['database']['table'].'.'.$field.' as '.$field;	
				}
					
			}
			
			
				
			return rex_list::factory('SELECT '.implode($listFields,",").' FROM '.$from.' '.implode(" ", $joins).rtrim(rtrim($where),' AND'),30,NULL,0);
		
		
	}
	
	public function cycle($class,$field,$id) {

		$record = new $class($id);
		$record->{$field} = $record->settings['database']['fields'][$field]['values'][$record->{$field}];
		return $record->save();
		
	}
	
	public function deleteRecord($class,$id) {

		$record = new $class($id);
		
		if(array_key_exists('relations',$this->models[$class]->settings['database']) && 
				count($this->models[$class]->settings['database']['relations']) > 0)
					foreach($this->models[$class]->settings['database']['relations'] as $relation)
						if($relation['type'] == 'MANY_MANY')
							$this->deleteCascade($relation['model']);
		
		
		return $record->delete();
		
	}	
	
	private function deleteCascade($class) {
		
		$sql = rex_sql::factory();
		$record = $sql->setWhere($relation['database']['primary']. ' = '. $this->{$this->settings['database']['primary']});
		return $record->delete();	
		
	}
}



?>