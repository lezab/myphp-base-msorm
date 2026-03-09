<?php
fwrite($file, "<?php\n");
fwrite($file, "/**\n");
fwrite($file, " * File generated with MySimpleORM v$msorm_version\n");
fwrite($file, " * \n");
fwrite($file, " * You should not modify this file.\n");
fwrite($file, " * If you need to add or modify some functionalities offered by this file,\n");
fwrite($file, " * you should see and modify the child class corresponding to this file.\n");
fwrite($file, " */\n");
fwrite($file, "\n");
fwrite($file, "namespace $nsp"."core;\n");
fwrite($file, "\n");
fwrite($file, "use \\$nsp"."exceptions\\$classname"."Exception;\n");
fwrite($file, "\n");
fwrite($file, "/**\n");
fwrite($file, " * @class $core_classname.\n");
fwrite($file, " * Like all other Core classes, the class is the base class for objects instances in the database.\n");
fwrite($file, " * You should not use this class directly but the subclass $classname which inherits all the methods of this class.\n");
fwrite($file, " * Ex :\n");
fwrite($file, " * \$manager = $manager_classname::getInstance();\n");
fwrite($file, " * \$object = new $classname();\n");
fwrite($file, " * ... // set object properties\n");
fwrite($file, " * \$manager->add(\$object);\n");
fwrite($file, " */\n");
fwrite($file, "class $core_classname {\n");
fwrite($file, "\n");
fwrite($file, "	// Class attributes for object management\n");
fwrite($file, "	protected \$_new = true;\n");
fwrite($file, "	protected \$_modified = false;\n");
fwrite($file, "	protected \$_deleted = false;\n");
fwrite($file, "	protected \$_renamed = false;\n");
fwrite($file, "\n");
fwrite($file, "	// Attributes relatives to table columns\n");
$hasRefObject = false;
$hasObjectsList = false;
$hasObject = false;
$hascrossRefValue = false;
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == 'value') || ($infos['type'] == 'refValue')){
if(isset($infos['datatype']['default'])){
if($infos['datatype']['type'] == 'boolean' || $infos['datatype']['type'] == 'integer' || $infos['datatype']['type'] == 'float'){
fwrite($file, "	protected \$".$attribute." = ".$infos['datatype']['default'].";\n");
}
else{
fwrite($file, "	protected \$".$attribute." = \"".$infos['datatype']['default']."\";\n");
}
}
else{
fwrite($file, "	protected \$".$attribute." = null;\n");
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
fwrite($file, "\n");
fwrite($file, "	// Attributes added to deal with primary key changing\n");
foreach($datas['primary_key'] as $attribute){
fwrite($file, "	protected \$_original_".$attribute." = null;\n");
}
fwrite($file, "\n");
if($hascrossRefValue){
fwrite($file, "	// Attributes relatives to crossRef's table extra-columns\n");
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == 'crossRefValue'){
if(isset($infos['datatype']['default'])){
if($infos['datatype']['type'] == 'boolean' || $infos['datatype']['type'] == 'integer' || $infos['datatype']['type'] == 'float'){
fwrite($file, "	protected \$".$attribute." = ".$infos['datatype']['default'].";\n");
}
else{
fwrite($file, "	protected \$".$attribute." = \"".$infos['datatype']['default']."\";\n");
}
}
else{
fwrite($file, "	protected \$".$attribute." = null;\n");
}
}
}
}
fwrite($file, "\n");
$setters_string = "\$_setters = array(";
fwrite($file, "	protected static \$_attributesList = array(");
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == "value") || ($infos['type'] == "refValue") || ($infos['type'] == "crossRefValue")){
if(! $first){
fwrite($file, ", ");
$setters_string .= ", ";
}
fwrite($file, "\"$attribute\"");
$first = false;
$setters_string .= "'$attribute' => 'set".$this->camelize($attribute)."'";
}
}
$setters_string .= ");";
fwrite($file, ");\n");
if($hascrossRefValue){
fwrite($file, "	protected static \$_crossRefAttributesList = array(");
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == "crossRefValue"){
if(! $first){
fwrite($file, ", ");
}
fwrite($file, "\"$attribute\"");
$first = false;
}
}
fwrite($file, ");\n");
}
fwrite($file, "	protected static $setters_string\n");
fwrite($file, "\n");
if($hasRefObject){
fwrite($file, "	// Objects attributes relatives to an attribute value which is a reference\n");
fwrite($file, "	// to an other table (Many to One or One to One)\n");
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == 'refObject'){
fwrite($file, "	protected \$".$attribute." = null;\n");
}
}
fwrite($file, "\n");
}
if($hasObject){
fwrite($file, "	// Objects attributes due to a relation defined in an other table/object on\n");
fwrite($file, "	// an attribute which is unique or primary key (One to One)\n");
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == 'object'){
fwrite($file, "	protected \$".$attribute." = null;\n");
fwrite($file, "	protected \$".$attribute."_uptodate = false;\n");
}
}
fwrite($file, "\n");
}
if($hasObjectsList){
fwrite($file, "	// Objects lists attributes due to a relation defined in an other table/object on\n");
fwrite($file, "	// an attribute which is not unique. (One to Many : the other side of Many to One)\n");
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == 'objectsList'){
fwrite($file, "	protected \$".$attribute." = array();\n");
fwrite($file, "	protected \$".$attribute."_ids = array();\n");
fwrite($file, "	protected \$".$attribute."_add = array();\n");
fwrite($file, "	protected \$".$attribute."_del = array();\n");
fwrite($file, "	protected \$".$attribute."_del_ids = array();\n");
fwrite($file, "	protected \$".$attribute."_uptodate = false;\n");
}
}
fwrite($file, "\n");
}
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Constructor\n");
fwrite($file, "	 * @params \$datas array : a set of key,value to initialize the object.\n");
fwrite($file, "	 * @see setDatas(array \$datas).\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function __construct(array \$datas = null){			\n");
fwrite($file, "		if(! empty(\$datas)){\n");
fwrite($file, "			\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3) : debug_backtrace();\n");
fwrite($file, "			if((isset(\$trace[1]['class']) && isset(\$trace[2]['class']) && (\$trace[1]['class'] == '$nsp$classname') && (\$trace[2]['class'] == '$nsp$manager_classname' || \$trace[2]['class'] == '$namespace\core\\$manager_core_classname'))\n");
fwrite($file, "				|| (isset(\$trace[1]['class']) && (\$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname'))) {\n");
fwrite($file, "				foreach (\$datas as \$key => \$value) {\n");
fwrite($file, "					\$this->\$key = \$value;\n");
fwrite($file, "				}\n");
fwrite($file, "				\$this->_new = false;\n");
fwrite($file, "			}\n");
fwrite($file, "			else{\n");
fwrite($file, "				\$this->setDatas(\$datas);\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "\n");
fwrite($file, "	//--------------------------------------------------------------------\n");
fwrite($file, "	// Operationals methods for object management. Should not be used.\n");
fwrite($file, "	//--------------------------------------------------------------------\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Tells if the object is a new one or already exists in the database.\n");
fwrite($file, "	 * This method is basically used by manager. You may not have to use it.\n");
fwrite($file, "	 * @return boolean true if the object is a new one, false otherwise.\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _isNew(){\n");
fwrite($file, "		return \$this->_new;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class\n");
fwrite($file, "	 * You should not use this method.\n");
fwrite($file, "	 * @param \$var boolean\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _setNew(\$var){\n");
fwrite($file, "		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);\n");
fwrite($file, "		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();\n");
fwrite($file, "		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {\n");
fwrite($file, "			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";\n");
fwrite($file, "			\$message .= \"You should not use this method.\";\n");
fwrite($file, "			throw new ".$classname."Exception(\$message);\n");
fwrite($file, "		}\n");
fwrite($file, "		\$this->_new = \$var;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Tells if the object has been modified or not.\n");
fwrite($file, "	 * This method is basically used by manager. You may not have to use it.\n");
fwrite($file, "	 * @return boolean true if the object has been modified, false otherwise.\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _isModified(){\n");
$or = '';
if($hasObjectsList){
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == 'objectsList'){
$or .= " || (! empty(\$this->".$attribute."_add))";
$or .= " || (! empty(\$this->".$attribute."_del))";
}
}
}
fwrite($file, "		return \$this->_modified".$or.";\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class\n");
fwrite($file, "	 * You should not use this method.\n");
fwrite($file, "	 * @param \$var boolean\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _setModified(\$var){\n");
fwrite($file, "		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);\n");
fwrite($file, "		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();\n");
fwrite($file, "		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {\n");
fwrite($file, "			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";\n");
fwrite($file, "			\$message .= \"You should not use this method.\";\n");
fwrite($file, "			throw new ".$classname."Exception(\$message);\n");
fwrite($file, "		}\n");
fwrite($file, "		\$this->_modified = \$var;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/** Tells if the object has been deleted from database or not.\n");
fwrite($file, "	 * This method is basically used by manager. You may not have to use it.\n");
fwrite($file, "	 * @return boolean true if the object has been deleted, false otherwise.\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _isDeleted(){\n");
fwrite($file, "		return \$this->_deleted;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class\n");
fwrite($file, "	 * You should not use this method.\n");
fwrite($file, "	 * @param \$var boolean\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _setDeleted(\$var){\n");
fwrite($file, "		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);\n");
fwrite($file, "		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();\n");
fwrite($file, "		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {\n");
fwrite($file, "			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";\n");
fwrite($file, "			\$message .= \"You should not use this method.\";\n");
fwrite($file, "			throw new ".$classname."Exception(\$message);\n");
fwrite($file, "		}\n");
fwrite($file, "		\$this->_deleted = \$var;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/** Tells if the object has been renamed (primary key has changed) or not.\n");
fwrite($file, "	 * This method is basically used by manager. You may not have to use it.\n");
fwrite($file, "	 * @return boolean true if the object has been renamed, false otherwise.\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _isRenamed(){\n");
fwrite($file, "		return \$this->_renamed;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class\n");
fwrite($file, "	 * You should not use this method.\n");
fwrite($file, "	 * @param \$var boolean\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _setRenamed(\$var){\n");
fwrite($file, "		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);\n");
fwrite($file, "		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();\n");
fwrite($file, "		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {\n");
fwrite($file, "			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";\n");
fwrite($file, "			\$message .= \"You should not use this method.\";\n");
fwrite($file, "			throw new ".$classname."Exception(\$message);\n");
fwrite($file, "		}\n");
fwrite($file, "		\$this->_renamed = \$var;\n");
fwrite($file, "		if(! \$var){\n");
foreach($datas['primary_key'] as $attribute){
fwrite($file, "			\$this->_original_".$attribute." = null;\n");
}
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
if($hasObjectsList){
fwrite($file, "	/**\n");
fwrite($file, "	 * Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\n");
fwrite($file, "	 * You should not use this method.\n");
fwrite($file, "	 * @param \$new just to know if is called from add or update method in a goal of optimization\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _reinit(\$new){\n");
fwrite($file, "		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);\n");
fwrite($file, "		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();\n");
fwrite($file, "		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {\n");
fwrite($file, "			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";\n");
fwrite($file, "			\$message .= \"You should not use this method.\";\n");
fwrite($file, "			throw new ".$classname."Exception(\$message);\n");
fwrite($file, "		}\n");
fwrite($file, "		if(\$new){\n");
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == 'objectsList'){
fwrite($file, "			\$this->$attribute = \$this->".$attribute."_add;\n");
fwrite($file, "			\$this->".$attribute."_add = array();\n");
fwrite($file, "			foreach(\$this->$attribute as \$o){\n");
if(count($infos['id_attributes']) > 1){
fwrite($file, "				\$ids = array();\n");
foreach($infos['id_attributes'] as $id_attr){
fwrite($file, "				\$ids[] = \$o->get".$this->camelize($id_attr)."();\n");
}
fwrite($file, "				\$this->".$attribute."_ids[] = \$ids;\n");
}
else{
fwrite($file, "				\$this->".$attribute."_ids[] = \$o->get".$this->camelize($infos['id_attributes'][0])."();\n");
}
fwrite($file, "			}\n");
fwrite($file, "			\$this->".$attribute."_uptodate = true;\n");
}
}
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == 'objectsList'){
fwrite($file, "			if(\$this->".$attribute."_uptodate){\n");
fwrite($file, "				\$this->$attribute = array_merge(\$this->$attribute, \$this->".$attribute."_add);\n");
fwrite($file, "				\$this->".$attribute."_ids = array();\n");
fwrite($file, "				foreach(\$this->$attribute as \$o){\n");
if(count($infos['id_attributes']) > 1){
fwrite($file, "					\$ids = array();\n");
foreach($infos['id_attributes'] as $id_attr){
fwrite($file, "					\$ids[] = \$o->get".$this->camelize($id_attr)."();\n");
}
fwrite($file, "					\$this->".$attribute."_ids[] = \$ids;\n");
}
else{
fwrite($file, "					\$this->".$attribute."_ids[] = \$o->get".$this->camelize($infos['id_attributes'][0])."();\n");
}
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "			\$this->".$attribute."_add = array();\n");
fwrite($file, "			\$this->".$attribute."_del = array();\n");
fwrite($file, "			\$this->".$attribute."_del_ids = array();\n");
}
}
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
}
fwrite($file, "\n");
fwrite($file, "	//--------------------------------------------------------------------\n");
fwrite($file, "	// Globals getter and setter ==> sets and gets all attributtes at once\n");
fwrite($file, "	//--------------------------------------------------------------------\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Set datas to the object according datas provided.\n");
fwrite($file, "	 * @params \$datas array : a set of key,value to initialize the object. Can be used on a new object to initialize it avoiding to use setters,\n");
fwrite($file, "	 * when a lot of attributes.\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function setDatas(array \$datas){\n");
fwrite($file, "		foreach (\$datas as \$key => \$value) {\n");
fwrite($file, "			if(isset(self::\$_setters[\$key])){\n");
fwrite($file, "				\$method = self::\$_setters[\$key];\n");
fwrite($file, "				\$this->\$method(\$value);\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * @return array all object's attributes in a set of key,value (faster then call any getter)\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function getDatas(){\n");
fwrite($file, "		\$datas = array();\n");
fwrite($file, "		foreach (self::\$_attributesList as \$attribute) {\n");
fwrite($file, "			\$datas[\$attribute] = \$this->\$attribute;\n");
fwrite($file, "		}\n");
fwrite($file, "		return \$datas;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	//--------------------------------------------------------------------\n");
fwrite($file, "	// Getters and setters for all attributes\n");
fwrite($file, "	//--------------------------------------------------------------------\n");
fwrite($file, "\n");
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == 'value') || ($infos['type'] == 'crossRefValue')){
fwrite($file, "	/**\n");
fwrite($file, "	 * @return ".$infos['datatype']['type']."\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function get".$this->camelize($attribute)."(){\n");
fwrite($file, "		return \$this->$attribute;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function set".$this->camelize($attribute)."(\$value){\n");
$conditions = $this->getConditions($infos['datatype']);
$message = $this->getErrorMessage($infos['datatype']);
fwrite($file, "		if(is_null(\$value) || ($conditions)){\n");
fwrite($file, "			if(\$value !== \$this->$attribute){\n");
if($infos['type'] == 'value' && $infos['primary_key']){
fwrite($file, "				if(is_null(\$this->_original_$attribute)){\n");
foreach($datas['primary_key'] as $pk_attribute){
fwrite($file, "					\$this->_original_$pk_attribute = \$this->$pk_attribute;\n");
}
fwrite($file, "				}\n");
}
fwrite($file, "				\$this->$attribute = \$value;\n");
fwrite($file, "				\$this->_modified = true;\n");
if($infos['type'] == 'value' && $infos['primary_key']){
fwrite($file, "				\$this->_renamed = true;\n");
}
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
fwrite($file, "			throw new ".$classname."Exception(\"$message\");\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
if($infos['type'] == 'value' && $infos['primary_key']){
fwrite($file, "	public function getOriginal".$this->camelize($attribute)."(){\n");
fwrite($file, "		return \$this->_original_$attribute;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
}
}
elseif($infos['type'] == 'refValue'){
fwrite($file, "	/**\n");
fwrite($file, "	 * @return ".$infos['datatype']['type']."\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function get".$this->camelize($attribute)."(){\n");
fwrite($file, "		return \$this->$attribute;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function set".$this->camelize($attribute)."(\$value){\n");
$conditions = $this->getConditions($infos['datatype']);
$message = $this->getErrorMessage($infos['datatype']);
fwrite($file, "		if(is_null(\$value) || ($conditions)){\n");
fwrite($file, "			if(\$value !== \$this->$attribute){\n");
if($infos['primary_key']){
fwrite($file, "				if(is_null(\$this->_original_$attribute)){\n");
foreach($datas['primary_key'] as $pk_attribute){
fwrite($file, "					\$this->_original_$pk_attribute = \$this->$pk_attribute;\n");
}
fwrite($file, "				}\n");
}
fwrite($file, "				\$this->$attribute = \$value;\n");
fwrite($file, "				if(isset(\$this->".$infos['ref'].")){\n");
fwrite($file, "					unset(\$this->".$infos['ref'].");\n");
fwrite($file, "				}\n");
fwrite($file, "				\$this->_modified = true;\n");
if($infos['primary_key']){
fwrite($file, "				\$this->_renamed = true;\n");
}
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
fwrite($file, "			throw new ".$classname."Exception(\"$message\");\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
if($infos['primary_key']){
fwrite($file, "	/**\n");
fwrite($file, "	 * @return ".$infos['datatype']['type']."\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function getOriginal".$this->camelize($attribute)."(){\n");
fwrite($file, "		return \$this->_original_$attribute;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
}
}
elseif($infos['type'] == 'refObject'){
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * @return \\$nsp".$infos['datatype']."\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function get".$this->camelize($attribute)."(){\n");
fwrite($file, "		if(isset(\$this->$attribute)){\n");
fwrite($file, "			return \$this->$attribute;\n");
fwrite($file, "		}\n");
fwrite($file, "		elseif(isset(\$this->".$infos['ref'].")){\n");
fwrite($file, "			\$this->$attribute = \\$nsp".$infos['manager']."::getInstance()->getBy".$this->camelize($infos['key'])."(\$this->".$infos['ref'].");\n");
fwrite($file, "			return \$this->$attribute;\n");
fwrite($file, "		}\n");
fwrite($file, "		return null;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function set".$this->camelize($attribute)."(\\$nsp".$infos['datatype']." \$value){\n");
fwrite($file, "		if(isset(\$value) && (\$value->_isNew() || (\$value->get".$this->camelize($infos['key'])."() != \$this->".$infos['ref']."))){\n");
fwrite($file, "			\$this->$attribute = \$value;\n");
if($datas['attributes'][$infos['ref']]['primary_key']){
fwrite($file, "			if(\$this->_original_".$infos['ref']." == null){\n");
foreach($datas['primary_key'] as $pk_attribute){
fwrite($file, "				\$this->_original_$pk_attribute = \$this->$pk_attribute;\n");
}
fwrite($file, "			}\n");
}
fwrite($file, "			\$this->".$infos['ref']." = \$value->get".$this->camelize($infos['key'])."();\n");
fwrite($file, "			\$this->_modified = true;\n");
if($datas['attributes'][$infos['ref']]['primary_key']){
fwrite($file, "			\$this->_renamed = true;\n");
}
fwrite($file, "		}\n");
fwrite($file, "		elseif(! isset(\$value) && isset(\$this->".$infos['ref'].")){\n");
fwrite($file, "			unset(\$this->$attribute);\n");
if($datas['attributes'][$infos['ref']]['primary_key']){
fwrite($file, "			if(\$this->_original_".$infos['ref']." == null){\n");
foreach($datas['primary_key'] as $pk_attribute){
fwrite($file, "				\$this->_original_$pk_attribute = \$this->$pk_attribute;\n");
}
fwrite($file, "			}\n");
}
fwrite($file, "			unset(\$this->".$infos['ref'].");\n");
fwrite($file, "			\$this->_modified = true;\n");
if($datas['attributes'][$infos['ref']]['primary_key']){
fwrite($file, "			\$this->_renamed = true;\n");
}
fwrite($file, "		}\n");
fwrite($file, "	}\n");
}
elseif($infos['type'] == 'object'){
fwrite($file, "\n");
fwrite($file, "	protected function update".$this->camelize($attribute)."(){\n");
$attr = $infos['ext_my_id'];
$key = $infos['ext_my_attr_id'];
fwrite($file, "		\$this->$attribute = \\$nsp".$infos['manager']."::getInstance()->getBy".$this->camelize($attr)."(\$this->$key);\n");
fwrite($file, "		\$this->".$attribute."_uptodate = true;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * @return \\$nsp".$infos['datatype']."\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function get".$this->camelize($attribute)."(){\n");
fwrite($file, "		if(\$this->_isNew()){\n");
fwrite($file, "			return \$this->".$attribute.";\n");
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
fwrite($file, "			if((\$this->".$attribute." === null) && (! \$this->".$attribute."_uptodate)){\n");
fwrite($file, "				\$this->update".$this->camelize($attribute)."();\n");
fwrite($file, "			}\n");
fwrite($file, "			return \$this->".$attribute.";\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function set".$this->camelize($attribute)."(\\$nsp".$infos['datatype']." \$value){\n");
fwrite($file, "		\$this->$attribute = \$value;\n");
fwrite($file, "		\$this->_modified = true;\n");
fwrite($file, "		\$this->".$attribute."_uptodate = true;\n");
fwrite($file, "	}\n");
}
elseif($infos['type'] == 'objectsList'){
fwrite($file, "\n");
fwrite($file, "	protected function update".$this->camelize($attribute)."(){\n");
$attr = $infos['ext_my_id'];
$key = $infos['ext_my_attr_id'];
fwrite($file, "		\$this->$attribute = \\$nsp".$infos['manager']."::getInstance()->getFilteredList(array('$attr' => \$this->$key));\n");
fwrite($file, "		foreach(\$this->$attribute as \$o){\n");
if((count($infos['id_attributes']) > 1)){
fwrite($file, "			\$id = array();\n");
foreach($infos['id_attributes'] as $id_attr){
fwrite($file, "			\$id[] = \$o->get".$this->camelize($id_attr)."();\n");
}
fwrite($file, "			\$this->".$attribute."_ids[] = \$id;\n");
}
else{
fwrite($file, "			\$this->".$attribute."_ids[] = \$o->get".$this->camelize($infos['id_attributes'][0])."();\n");
}
fwrite($file, "		}\n");
fwrite($file, "		\$this->".$attribute."_uptodate = true;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * @return \\$nsp".$infos['datatype']."[]\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function get".$this->camelize($attribute)."(){\n");
fwrite($file, "		if(\$this->_isNew()){\n");
fwrite($file, "			return \$this->".$attribute."_add;\n");
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
fwrite($file, "			if(! \$this->".$attribute."_uptodate){\n");
fwrite($file, "				\$this->update".$this->camelize($attribute)."();\n");
fwrite($file, "			}\n");
fwrite($file, "			return array_merge(\$this->$attribute, \$this->".$attribute."_add);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function getOriginal".$this->camelize($attribute)."(){\n");
fwrite($file, "		if(\$this->_isNew()){\n");
fwrite($file, "			return array();\n");
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
fwrite($file, "			if(! \$this->".$attribute."_uptodate){\n");
fwrite($file, "				\$this->update".$this->camelize($attribute)."();\n");
fwrite($file, "			}\n");
fwrite($file, "			return \$this->$attribute;\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function getAdded".$this->camelize($attribute)."(){\n");
fwrite($file, "		return \$this->".$attribute."_add;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function getDeleted".$this->camelize($attribute)."(){\n");
fwrite($file, "		return \$this->".$attribute."_del;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function add".$infos['sf_name']."(\\$nsp".$infos['datatype']." \$value){\n");
fwrite($file, "		if(\$this->_isNew() || \$value->_isNew()){\n");
fwrite($file, "			if(! in_array(\$value, \$this->".$attribute."_add)){\n");
fwrite($file, "				\$this->".$attribute."_add[] = \$value;\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
fwrite($file, "			if(! \$this->".$attribute."_uptodate){\n");
fwrite($file, "				\$this->update".$this->camelize($attribute)."();\n");
fwrite($file, "			}\n");
if((count($infos['id_attributes']) > 1)){
fwrite($file, "			\$id = array();\n");
foreach($infos['id_attributes'] as $id_attr){
fwrite($file, "			\$id[] = \$value->get".$this->camelize($id_attr)."();\n");
}
}
else{
fwrite($file, "			\$id = \$value->get".$this->camelize($infos['id_attributes'][0])."();\n");
}
fwrite($file, "			if(! in_array(\$id, \$this->".$attribute."_ids)){\n");
fwrite($file, "				if((\$index = array_search(\$id, \$this->".$attribute."_del_ids)) !== false){\n");
fwrite($file, "					\$this->".$attribute."[] = \$value;\n");
fwrite($file, "					\$this->".$attribute."_ids[] = \$id;\n");
fwrite($file, "					array_splice(\$this->".$attribute."_del, \$index, 1);\n");
fwrite($file, "					array_splice(\$this->".$attribute."_del_ids, \$index, 1);\n");
fwrite($file, "				}\n");
fwrite($file, "				else{\n");
fwrite($file, "					\$this->".$attribute."_add[] = \$value;\n");
fwrite($file, "				}\n");
fwrite($file, "			}	\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function delete".$infos['sf_name']."(\\$nsp".$infos['datatype']." \$value){\n");
fwrite($file, "		if((\$index = array_search(\$value, \$this->".$attribute."_add)) !== false){\n");
fwrite($file, "			array_splice(\$this->".$attribute."_add, \$index, 1);\n");
fwrite($file, "		}\n");
fwrite($file, "		elseif(! (\$this->_isNew() || \$value->_isNew())){\n");
fwrite($file, "			if(! \$this->".$attribute."_uptodate){\n");
fwrite($file, "				\$this->update".$this->camelize($attribute)."();\n");
fwrite($file, "			}\n");
if((count($infos['id_attributes']) > 1)){
fwrite($file, "			\$id = array();\n");
foreach($infos['id_attributes'] as $id_attr){
fwrite($file, "			\$id[] = \$value->get".$this->camelize($id_attr)."();\n");
}
}
else{
fwrite($file, "			\$id = \$value->get".$this->camelize($infos['id_attributes'][0])."();\n");
}
fwrite($file, "			if((\$index = array_search(\$id, \$this->".$attribute."_ids)) !== false){\n");
fwrite($file, "				array_splice(\$this->".$attribute."_ids, \$index, 1);\n");
fwrite($file, "				array_splice(\$this->".$attribute.", \$index, 1);\n");
fwrite($file, "				\$this->".$attribute."_del_ids[] = \$id;\n");
fwrite($file, "				\$this->".$attribute."_del[] = \$value;\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
}
}
fwrite($file, "}\n");
fwrite($file, "?>");
?>