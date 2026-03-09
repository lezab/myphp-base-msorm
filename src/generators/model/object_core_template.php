##<?php
##/**
## * File generated with MySimpleORM v$msorm_version
## * 
## * You should not modify this file.
## * If you need to add or modify some functionalities offered by this file,
## * you should see and modify the child class corresponding to this file.
## */
##
##namespace $nsp"."core;
##
##use \\$nsp"."exceptions\\$classname"."Exception;
##
##/**
## * @class $core_classname.
## * Like all other Core classes, the class is the base class for objects instances in the database.
## * You should not use this class directly but the subclass $classname which inherits all the methods of this class.
## * Ex :
## * \$manager = $manager_classname::getInstance();
## * \$object = new $classname();
## * ... // set object properties
## * \$manager->add(\$object);
## */
##class $core_classname {
##
##	// Class attributes for object management
##	protected \$_new = true;
##	protected \$_modified = false;
##	protected \$_deleted = false;
##	protected \$_renamed = false;
##
##	// Attributes relatives to table columns
/** premier parcours, on en profite pour initiliser des variables */
$hasRefObject = false;
$hasObjectsList = false;
$hasObject = false;
$hascrossRefValue = false;
foreach($datas['attributes'] as $attribute => $infos){
	if(($infos['type'] == 'value') || ($infos['type'] == 'refValue')){
		if(isset($infos['datatype']['default'])){
			if($infos['datatype']['type'] == 'boolean' || $infos['datatype']['type'] == 'integer' || $infos['datatype']['type'] == 'float'){
##	protected \$".$attribute." = ".$infos['datatype']['default'].";
			}
			else{
##	protected \$".$attribute." = \"".$infos['datatype']['default']."\";
			}
		}
		else{
##	protected \$".$attribute." = null;
		}
	}
	elseif($infos['type'] == 'refObject'){
		$hasRefObject = true;
	}
	elseif($infos['type'] == 'objectsList'){
		$hasObjectsList = true;
	}
	elseif($infos['type'] == 'object'){
		$hasObject = true;
	}
	elseif($infos['type'] == "crossRefValue"){
		$hascrossRefValue = true;
	}
}
##
##	// Attributes added to deal with primary key changing
foreach($datas['primary_key'] as $attribute){
##	protected \$_original_".$attribute." = null;
}
##
if($hascrossRefValue){
##	// Attributes relatives to crossRef's table extra-columns
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == 'crossRefValue'){
			if(isset($infos['datatype']['default'])){
				if($infos['datatype']['type'] == 'boolean' || $infos['datatype']['type'] == 'integer' || $infos['datatype']['type'] == 'float'){
##	protected \$".$attribute." = ".$infos['datatype']['default'].";
				}
				else{
##	protected \$".$attribute." = \"".$infos['datatype']['default']."\";
				}
			}
			else{
##	protected \$".$attribute." = null;
			}
		}
	}
}
##
$setters_string = "\$_setters = array(";
#	protected static \$_attributesList = array(
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
	if(($infos['type'] == "value") || ($infos['type'] == "refValue") || ($infos['type'] == "crossRefValue")){
		if(! $first){
			#, 
			$setters_string .= ", ";
		}
		#\"$attribute\"
		$first = false;
		$setters_string .= "'$attribute' => 'set".$this->camelize($attribute)."'";
	}
}
$setters_string .= ");";
##);
if($hascrossRefValue){
#	protected static \$_crossRefAttributesList = array(
	$first = true;
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == "crossRefValue"){
			if(! $first){
			#, 
			}
		#\"$attribute\"
			$first = false;
		}
	}
##);
}
##	protected static $setters_string
##
if($hasRefObject){
##	// Objects attributes relatives to an attribute value which is a reference
##	// to an other table (Many to One or One to One)
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == 'refObject'){
##	protected \$".$attribute." = null;
		}
	}
##
}
if($hasObject){
##	// Objects attributes due to a relation defined in an other table/object on
##	// an attribute which is unique or primary key (One to One)
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == 'object'){
##	protected \$".$attribute." = null;
##	protected \$".$attribute."_uptodate = false;
		}
	}
##
}
if($hasObjectsList){
##	// Objects lists attributes due to a relation defined in an other table/object on
##	// an attribute which is not unique. (One to Many : the other side of Many to One)
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == 'objectsList'){
##	protected \$".$attribute." = array();
##	protected \$".$attribute."_ids = array();
##	protected \$".$attribute."_add = array();
##	protected \$".$attribute."_del = array();
##	protected \$".$attribute."_del_ids = array();
##	protected \$".$attribute."_uptodate = false;
		}
	}
##
}
##
##	/**
##	 * Constructor
##	 * @params \$datas array : a set of key,value to initialize the object.
##	 * @see setDatas(array \$datas).
##	 */
##	public function __construct(array \$datas = null){			
##		if(! empty(\$datas)){
##			\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3) : debug_backtrace();
##			if((isset(\$trace[1]['class']) && isset(\$trace[2]['class']) && (\$trace[1]['class'] == '$nsp$classname') && (\$trace[2]['class'] == '$nsp$manager_classname' || \$trace[2]['class'] == '$namespace\core\\$manager_core_classname'))
##				|| (isset(\$trace[1]['class']) && (\$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname'))) {
##				foreach (\$datas as \$key => \$value) {
##					\$this->\$key = \$value;
##				}
##				\$this->_new = false;
##			}
##			else{
##				\$this->setDatas(\$datas);
##			}
##		}
##	}
##
##
##	//--------------------------------------------------------------------
##	// Operationals methods for object management. Should not be used.
##	//--------------------------------------------------------------------
##
##	/**
##	 * Tells if the object is a new one or already exists in the database.
##	 * This method is basically used by manager. You may not have to use it.
##	 * @return boolean true if the object is a new one, false otherwise.
##	 */
##	final public function _isNew(){
##		return \$this->_new;
##	}
##
##	/**
##	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class
##	 * You should not use this method.
##	 * @param \$var boolean
##	 */
##	final public function _setNew(\$var){
##		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
##		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();
##		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {
##			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";
##			\$message .= \"You should not use this method.\";
##			throw new ".$classname."Exception(\$message);
##		}
##		\$this->_new = \$var;
##	}
##
##	/**
##	 * Tells if the object has been modified or not.
##	 * This method is basically used by manager. You may not have to use it.
##	 * @return boolean true if the object has been modified, false otherwise.
##	 */
##	final public function _isModified(){
$or = '';
if($hasObjectsList){
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == 'objectsList'){
			$or .= " || (! empty(\$this->".$attribute."_add))";
			$or .= " || (! empty(\$this->".$attribute."_del))";
		}
	}
}
##		return \$this->_modified".$or.";
##	}
##
##	/**
##	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class
##	 * You should not use this method.
##	 * @param \$var boolean
##	 */
##	final public function _setModified(\$var){
##		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
##		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();
##		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {
##			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";
##			\$message .= \"You should not use this method.\";
##			throw new ".$classname."Exception(\$message);
##		}
##		\$this->_modified = \$var;
##	}
##
##	/** Tells if the object has been deleted from database or not.
##	 * This method is basically used by manager. You may not have to use it.
##	 * @return boolean true if the object has been deleted, false otherwise.
##	 */
##	final public function _isDeleted(){
##		return \$this->_deleted;
##	}
##
##	/**
##	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class
##	 * You should not use this method.
##	 * @param \$var boolean
##	 */
##	final public function _setDeleted(\$var){
##		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
##		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();
##		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {
##			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";
##			\$message .= \"You should not use this method.\";
##			throw new ".$classname."Exception(\$message);
##		}
##		\$this->_deleted = \$var;
##	}
##
##	/** Tells if the object has been renamed (primary key has changed) or not.
##	 * This method is basically used by manager. You may not have to use it.
##	 * @return boolean true if the object has been renamed, false otherwise.
##	 */
##	final public function _isRenamed(){
##		return \$this->_renamed;
##	}
##
##	/**
##	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class
##	 * You should not use this method.
##	 * @param \$var boolean
##	 */
##	final public function _setRenamed(\$var){
##		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
##		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();
##		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {
##			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";
##			\$message .= \"You should not use this method.\";
##			throw new ".$classname."Exception(\$message);
##		}
##		\$this->_renamed = \$var;
##		if(! \$var){
foreach($datas['primary_key'] as $attribute){
##			\$this->_original_".$attribute." = null;
}
##		}
##	}
##
if($hasObjectsList){
##	/**
##	 * Due to limitations of php the method is public but can only be used from $nsp$manager_classname class
##	 * You should not use this method.
##	 * @param \$new just to know if is called from add or update method in a goal of optimization
##	 */
##	final public function _reinit(\$new){
##		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
##		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();
##		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {
##			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";
##			\$message .= \"You should not use this method.\";
##			throw new ".$classname."Exception(\$message);
##		}
##		if(\$new){
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == 'objectsList'){
##			\$this->$attribute = \$this->".$attribute."_add;
##			\$this->".$attribute."_add = array();
##			foreach(\$this->$attribute as \$o){
			if(count($infos['id_attributes']) > 1){
##				\$ids = array();
				foreach($infos['id_attributes'] as $id_attr){
##				\$ids[] = \$o->get".$this->camelize($id_attr)."();
				}
##				\$this->".$attribute."_ids[] = \$ids;
			}
			else{
##				\$this->".$attribute."_ids[] = \$o->get".$this->camelize($infos['id_attributes'][0])."();
			}
##			}
##			\$this->".$attribute."_uptodate = true;
		}
	}
##		}
##		else{
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == 'objectsList'){
##			if(\$this->".$attribute."_uptodate){
##				\$this->$attribute = array_merge(\$this->$attribute, \$this->".$attribute."_add);
##				\$this->".$attribute."_ids = array();
##				foreach(\$this->$attribute as \$o){
			if(count($infos['id_attributes']) > 1){
##					\$ids = array();
				foreach($infos['id_attributes'] as $id_attr){
##					\$ids[] = \$o->get".$this->camelize($id_attr)."();
				}
##					\$this->".$attribute."_ids[] = \$ids;
			}
				else{
##					\$this->".$attribute."_ids[] = \$o->get".$this->camelize($infos['id_attributes'][0])."();
				}
##				}
##			}
##			\$this->".$attribute."_add = array();
##			\$this->".$attribute."_del = array();
##			\$this->".$attribute."_del_ids = array();
		}
	}
##		}
##	}
##
}
##
##	//--------------------------------------------------------------------
##	// Globals getter and setter ==> sets and gets all attributtes at once
##	//--------------------------------------------------------------------
##
##	/**
##	 * Set datas to the object according datas provided.
##	 * @params \$datas array : a set of key,value to initialize the object. Can be used on a new object to initialize it avoiding to use setters,
##	 * when a lot of attributes.
##	 */
##	public function setDatas(array \$datas){
##		foreach (\$datas as \$key => \$value) {
##			if(isset(self::\$_setters[\$key])){
##				\$method = self::\$_setters[\$key];
##				\$this->\$method(\$value);
##			}
##		}
##	}
##
##	/**
##	 * @return array all object's attributes in a set of key,value (faster then call any getter)
##	 */
##	public function getDatas(){
##		\$datas = array();
##		foreach (self::\$_attributesList as \$attribute) {
##			\$datas[\$attribute] = \$this->\$attribute;
##		}
##		return \$datas;
##	}
##
##	//--------------------------------------------------------------------
##	// Getters and setters for all attributes
##	//--------------------------------------------------------------------
##
foreach($datas['attributes'] as $attribute => $infos){
	if(($infos['type'] == 'value') || ($infos['type'] == 'crossRefValue')){
##	/**
##	 * @return ".$infos['datatype']['type']."
##	 */
##	public function get".$this->camelize($attribute)."(){
##		return \$this->$attribute;
##	}
##
##	public function set".$this->camelize($attribute)."(\$value){
		$conditions = $this->getConditions($infos['datatype']);
		$message = $this->getErrorMessage($infos['datatype']);
##		if(is_null(\$value) || ($conditions)){
##			if(\$value !== \$this->$attribute){
		if($infos['type'] == 'value' && $infos['primary_key']){
##				if(is_null(\$this->_original_$attribute)){
			foreach($datas['primary_key'] as $pk_attribute){
##					\$this->_original_$pk_attribute = \$this->$pk_attribute;
			}
##				}
		}
##				\$this->$attribute = \$value;
##				\$this->_modified = true;
		if($infos['type'] == 'value' && $infos['primary_key']){
##				\$this->_renamed = true;
		}
##			}
##		}
##		else{
##			throw new ".$classname."Exception(\"$message\");
##		}
##	}
##
		if($infos['type'] == 'value' && $infos['primary_key']){
##	public function getOriginal".$this->camelize($attribute)."(){
##		return \$this->_original_$attribute;
##	}
##
		}
	}
	elseif($infos['type'] == 'refValue'){
##	/**
##	 * @return ".$infos['datatype']['type']."
##	 */
##	public function get".$this->camelize($attribute)."(){
##		return \$this->$attribute;
##	}
##
##	public function set".$this->camelize($attribute)."(\$value){
		$conditions = $this->getConditions($infos['datatype']);
		$message = $this->getErrorMessage($infos['datatype']);
##		if(is_null(\$value) || ($conditions)){
##			if(\$value !== \$this->$attribute){
		if($infos['primary_key']){
##				if(is_null(\$this->_original_$attribute)){
			foreach($datas['primary_key'] as $pk_attribute){
##					\$this->_original_$pk_attribute = \$this->$pk_attribute;
			}
##				}
		}
##				\$this->$attribute = \$value;
##				if(isset(\$this->".$infos['ref'].")){
##					unset(\$this->".$infos['ref'].");
##				}
##				\$this->_modified = true;
		if($infos['primary_key']){
##				\$this->_renamed = true;
		}
##			}
##		}
##		else{
##			throw new ".$classname."Exception(\"$message\");
##		}
##	}
##
		if($infos['primary_key']){
##	/**
##	 * @return ".$infos['datatype']['type']."
##	 */
##	public function getOriginal".$this->camelize($attribute)."(){
##		return \$this->_original_$attribute;
##	}
##
		}
	}
	elseif($infos['type'] == 'refObject'){
##
##	/**
##	 * @return \\$nsp".$infos['datatype']."
##	 */
##	public function get".$this->camelize($attribute)."(){
##		if(isset(\$this->$attribute)){
##			return \$this->$attribute;
##		}
##		elseif(isset(\$this->".$infos['ref'].")){
##			\$this->$attribute = \\$nsp".$infos['manager']."::getInstance()->getBy".$this->camelize($infos['key'])."(\$this->".$infos['ref'].");
##			return \$this->$attribute;
##		}
##		return null;
##	}
##
##	public function set".$this->camelize($attribute)."(\\$nsp".$infos['datatype']." \$value){
##		if(isset(\$value) && (\$value->_isNew() || (\$value->get".$this->camelize($infos['key'])."() != \$this->".$infos['ref']."))){
##			\$this->$attribute = \$value;
		if($datas['attributes'][$infos['ref']]['primary_key']){
##			if(\$this->_original_".$infos['ref']." == null){
			foreach($datas['primary_key'] as $pk_attribute){
##				\$this->_original_$pk_attribute = \$this->$pk_attribute;
			}
##			}
		}
##			\$this->".$infos['ref']." = \$value->get".$this->camelize($infos['key'])."();
##			\$this->_modified = true;
		if($datas['attributes'][$infos['ref']]['primary_key']){
##			\$this->_renamed = true;
		}
##		}
##		elseif(! isset(\$value) && isset(\$this->".$infos['ref'].")){
##			unset(\$this->$attribute);
		if($datas['attributes'][$infos['ref']]['primary_key']){
##			if(\$this->_original_".$infos['ref']." == null){
			foreach($datas['primary_key'] as $pk_attribute){
##				\$this->_original_$pk_attribute = \$this->$pk_attribute;
			}
##			}
		}
##			unset(\$this->".$infos['ref'].");
##			\$this->_modified = true;
		if($datas['attributes'][$infos['ref']]['primary_key']){
##			\$this->_renamed = true;
		}
##		}
##	}
	}
	elseif($infos['type'] == 'object'){
##
##	protected function update".$this->camelize($attribute)."(){
		$attr = $infos['ext_my_id'];
		$key = $infos['ext_my_attr_id'];
##		\$this->$attribute = \\$nsp".$infos['manager']."::getInstance()->getBy".$this->camelize($attr)."(\$this->$key);
##		\$this->".$attribute."_uptodate = true;
##	}
##
##	/**
##	 * @return \\$nsp".$infos['datatype']."
##	 */
##	public function get".$this->camelize($attribute)."(){
##		if(\$this->_isNew()){
##			return \$this->".$attribute.";
##		}
##		else{
##			if((\$this->".$attribute." === null) && (! \$this->".$attribute."_uptodate)){
##				\$this->update".$this->camelize($attribute)."();
##			}
##			return \$this->".$attribute.";
##		}
##	}
##
##	public function set".$this->camelize($attribute)."(\\$nsp".$infos['datatype']." \$value){
##		\$this->$attribute = \$value;
##		\$this->_modified = true;
##		\$this->".$attribute."_uptodate = true;
##	}
	}
	elseif($infos['type'] == 'objectsList'){
##
##	protected function update".$this->camelize($attribute)."(){
		$attr = $infos['ext_my_id'];
		$key = $infos['ext_my_attr_id'];
##		\$this->$attribute = \\$nsp".$infos['manager']."::getInstance()->getFilteredList(array('$attr' => \$this->$key));
##		foreach(\$this->$attribute as \$o){
		if((count($infos['id_attributes']) > 1)){
##			\$id = array();
			foreach($infos['id_attributes'] as $id_attr){
##			\$id[] = \$o->get".$this->camelize($id_attr)."();
			}
##			\$this->".$attribute."_ids[] = \$id;
		}
		else{
##			\$this->".$attribute."_ids[] = \$o->get".$this->camelize($infos['id_attributes'][0])."();
		}
##		}
##		\$this->".$attribute."_uptodate = true;
##	}
##
##	/**
##	 * @return \\$nsp".$infos['datatype']."[]
##	 */
##	public function get".$this->camelize($attribute)."(){
##		if(\$this->_isNew()){
##			return \$this->".$attribute."_add;
##		}
##		else{
##			if(! \$this->".$attribute."_uptodate){
##				\$this->update".$this->camelize($attribute)."();
##			}
##			return array_merge(\$this->$attribute, \$this->".$attribute."_add);
##		}
##	}
##
##	public function getOriginal".$this->camelize($attribute)."(){
##		if(\$this->_isNew()){
##			return array();
##		}
##		else{
##			if(! \$this->".$attribute."_uptodate){
##				\$this->update".$this->camelize($attribute)."();
##			}
##			return \$this->$attribute;
##		}
##	}
##
##	public function getAdded".$this->camelize($attribute)."(){
##		return \$this->".$attribute."_add;
##	}
##
##	public function getDeleted".$this->camelize($attribute)."(){
##		return \$this->".$attribute."_del;
##	}
##
##	public function add".$infos['sf_name']."(\\$nsp".$infos['datatype']." \$value){
##		if(\$this->_isNew() || \$value->_isNew()){
##			if(! in_array(\$value, \$this->".$attribute."_add)){
##				\$this->".$attribute."_add[] = \$value;
##			}
##		}
##		else{
##			if(! \$this->".$attribute."_uptodate){
##				\$this->update".$this->camelize($attribute)."();
##			}
		if((count($infos['id_attributes']) > 1)){
##			\$id = array();
			foreach($infos['id_attributes'] as $id_attr){
##			\$id[] = \$value->get".$this->camelize($id_attr)."();
			}
		}
		else{
##			\$id = \$value->get".$this->camelize($infos['id_attributes'][0])."();
		}
##			if(! in_array(\$id, \$this->".$attribute."_ids)){
##				if((\$index = array_search(\$id, \$this->".$attribute."_del_ids)) !== false){
##					\$this->".$attribute."[] = \$value;
##					\$this->".$attribute."_ids[] = \$id;
##					array_splice(\$this->".$attribute."_del, \$index, 1);
##					array_splice(\$this->".$attribute."_del_ids, \$index, 1);
##				}
##				else{
##					\$this->".$attribute."_add[] = \$value;
##				}
##			}	
##		}
##	}
##
##	public function delete".$infos['sf_name']."(\\$nsp".$infos['datatype']." \$value){
##		if((\$index = array_search(\$value, \$this->".$attribute."_add)) !== false){
##			array_splice(\$this->".$attribute."_add, \$index, 1);
##		}
##		elseif(! (\$this->_isNew() || \$value->_isNew())){
##			if(! \$this->".$attribute."_uptodate){
##				\$this->update".$this->camelize($attribute)."();
##			}
		if((count($infos['id_attributes']) > 1)){
##			\$id = array();
			foreach($infos['id_attributes'] as $id_attr){
##			\$id[] = \$value->get".$this->camelize($id_attr)."();
			}
		}
		else{
##			\$id = \$value->get".$this->camelize($infos['id_attributes'][0])."();
		}
##			if((\$index = array_search(\$id, \$this->".$attribute."_ids)) !== false){
##				array_splice(\$this->".$attribute."_ids, \$index, 1);
##				array_splice(\$this->".$attribute.", \$index, 1);
##				\$this->".$attribute."_del_ids[] = \$id;
##				\$this->".$attribute."_del[] = \$value;
##			}
##		}
##	}
	}
}
##}
#?>