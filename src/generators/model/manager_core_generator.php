<?php
fwrite($file, "<?php\n");
fwrite($file, "/**\n");
fwrite($file, " * File generated with MySimpleORM v$msorm_version\n");
fwrite($file, " *\n");
fwrite($file, " * You should not modify this file.\n");
fwrite($file, " * If you need to add or modify some functionalities offered by this file,\n");
fwrite($file, " * you should see and modify the child class corresponding to this file.\n");
fwrite($file, " */\n");
fwrite($file, "\n");
fwrite($file, "namespace $nsp"."core;\n");
fwrite($file, "\n");
fwrite($file, "use \\$nsp"."$classname;\n");
fwrite($file, "use \\$nsp"."exceptions\\$manager_classname"."Exception;\n");
fwrite($file, "\n");
fwrite($file, "/**\n");
fwrite($file, " * @class $manager_core_classname : a class for managing $classname objects.\n");
fwrite($file, " * Like all other CoreManager classes, the class is a singleton an should not be instanciate.\n");
fwrite($file, " * The right way to deal with this class is to call the getInstance method on the subclass $manager_classname.\n");
fwrite($file, " * Ex :\n");
fwrite($file, " * \$manager = $manager_classname::getInstance();\n");
fwrite($file, " * \$objects = \$manager->getList();\n");
fwrite($file, " * ...\n");
fwrite($file, " */\n");
fwrite($file, "class $manager_core_classname {\n");
fwrite($file, "	\n");
fwrite($file, "	// Class attributes for object management\n");
fwrite($file, "	protected \$db;\n");
fwrite($file, "	protected static \$instance;\n");
fwrite($file, "\n");
fwrite($file, "	protected static \$attributesList = array(");
$first = true;
$hasRefObject = false;
$hasObjectsList = false;
$hasCrossObjectsList = false;
$hasCrossObjectsExtraAttributes = false;
$hasObject = false;
$hasUniqueAttributes = false;
$hasNonUniqueAttributes = false;
$hasRequiredAttributes = false;
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == "value") || ($infos['type'] == "refValue")){
if(! $first){
fwrite($file, ", ");
}
fwrite($file, "\"$attribute\"");
$first = false;
if($infos['required']){
$hasRequiredAttributes = true;
}
if($infos['unique'] || ($infos['primary_key'] && (count($datas['primary_key']) == 1 ))){
$hasUniqueAttributes = true;
}
else{
$hasNonUniqueAttributes = true;
}
}
elseif($infos['type'] == 'refObject'){
$hasRefObject = true;
}
elseif(($infos['type'] == 'objectsList') && ($infos['ext_cardinal'] == 'many')){
$hasObjectsList = true;
$hasCrossObjectsList = true;
if(! empty($infos['ext_extra_attrs'])){
$hasCrossObjectsExtraAttributes = true;
}
}
elseif($infos['type'] == 'objectsList'){
$hasObjectsList = true;
}
elseif($infos['type'] == 'object'){
$hasObject = true;
}
}
fwrite($file, ");\n");
if($hasCrossObjectsList){
fwrite($file, "	protected static \$extAttributesList = array(");
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == 'objectsList') && ($infos['ext_cardinal'] == 'many')){
if(! $first){
fwrite($file, ",");
}
fwrite($file, "\"".$infos['ext_column']."\"");
$first = false;
}
}
fwrite($file, ");\n");
}
if($hasUniqueAttributes && $hasNonUniqueAttributes){
fwrite($file, "	protected static \$uniqueAttributesList = array(");
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == 'value' || $infos['type'] == 'refValue') && ($infos['unique'] || ($infos['primary_key'] && (count($datas['primary_key']) == 1 )))){
if(! $first){
fwrite($file, ",");
}
fwrite($file, "\"$attribute\"");
$first = false;
}
}
fwrite($file, ");\n");
}
if($hasRequiredAttributes){
fwrite($file, "	protected static \$requiredAttributesList = array(");
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == 'value') && $infos['required']){
if(! $first){
fwrite($file, ",");
}
fwrite($file, "\"$attribute\"");
$first = false;
}
}
fwrite($file, ");\n");
}
fwrite($file, "\n");
fwrite($file, "	protected \$stmts = array();\n");
fwrite($file, "	\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Constructor\n");
fwrite($file, "	 * This constructor should not be used.\n");
fwrite($file, "	 * Use getInstance method instead.\n");
fwrite($file, "	 * @see getInstance()\n");
fwrite($file, "	 */\n");
fwrite($file, "	protected function __construct(){\n");
fwrite($file, "		\$this->db = DatabaseConnectionProvider::getInstance();\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Use this method to get the instance of the manager\n");
fwrite($file, "	 * @return \\$nsp"."$manager_classname\n");
fwrite($file, "	 */\n");
fwrite($file, "	public static function getInstance(){\n");
fwrite($file, "		if(! isset(static::\$instance)) {\n");
fwrite($file, "			static::\$instance = new static;\n");
fwrite($file, "		}\n");
fwrite($file, "		return static::\$instance;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
$multi_pk = count($datas['primary_key']) > 1 ? true : false;
if($multi_pk){
$pk = $datas['primary_key'];
$pk_infos = array();
foreach($pk as $k){
$pk_infos[] = $datas['attributes'][$k];
}
$pk_params = array();
foreach($pk_infos as $i => $infos){
$pk_params[$i] = $this->getPDOParams($infos['datatype']);
}
}
else{
$pk = $datas['primary_key'][0];
$pk_infos = $datas['attributes'][$pk];
$pk_params = $this->getPDOParams($pk_infos['datatype']);
}

if($multi_pk){
$mpk_id = '$'.join(', $', $pk);
fwrite($file, "	/**\n");
fwrite($file, "	 * Use this method to get a $classname extracted from database\n");
foreach($pk as $k){
fwrite($file, "	 * @param \$$k part of the object identifier (primary key)\n");
}
fwrite($file, "	 * @return \\$nsp"."$classname\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function get($mpk_id){\n");
fwrite($file, "		if(! isset(\$this->stmts['get'])){\n");
$mpk_where = array();
foreach($pk as $k){
$mpk_where[] = "`$k` = :$k";
}
$mpk_where = join(' AND ', $mpk_where);
fwrite($file, "			\$this->stmts['get'] = \$this->db->prepare('SELECT * FROM `$tablename` WHERE $mpk_where');\n");
fwrite($file, "		}\n");
fwrite($file, "		\$stmt = \$this->stmts['get'];\n");
fwrite($file, "		try{\n");
foreach($pk as $i => $k){
fwrite($file, "			\$stmt->bindValue(':$k', \$$k".$pk_params[$i].");\n");
}
fwrite($file, "			\$stmt->execute();\n");
fwrite($file, "			\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC);\n");
fwrite($file, "			if(\$datas){\n");
fwrite($file, "				return new $classname(\$datas);\n");
fwrite($file, "			}\n");
fwrite($file, "			return null;\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\\Exception \$e){\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Use this method to check if a $classname exists in the database.\n");
fwrite($file, "	 * Using this method is faster than get method then check if value is returned.\n");
foreach($pk as $k){
fwrite($file, "	 * @param \$$k part of the object identifier (primary key)\n");
}
fwrite($file, "	 * @return boolean true if a corresponding entry is found, false otherwise\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function exists($mpk_id){\n");
fwrite($file, "		if(! isset(\$this->stmts['exists'])){\n");
fwrite($file, "			\$this->stmts['exists'] = \$this->db->prepare('SELECT EXISTS(SELECT 1 FROM `$tablename` WHERE $mpk_where LIMIT 1)');\n");
fwrite($file, "		}\n");
fwrite($file, "		\$stmt = \$this->stmts['exists'];\n");
fwrite($file, "		try{\n");
foreach($pk as $i => $k){
fwrite($file, "			\$stmt->bindValue(':$k', \$$k".$pk_params[$i].");\n");
}
fwrite($file, "			\$stmt->execute();\n");
fwrite($file, "			\$datas = \$stmt->fetch(\\PDO::FETCH_NUM);\n");
fwrite($file, "			return (bool)\$datas[0];\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\\Exception \$e){\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Use this method to delete a $classname using his primary key elements instead of the object itself.\n");
fwrite($file, "	 * Using this method could be easier sometimes but does the same than retrieving the object then delete it.\n");
foreach($pk as $k){
fwrite($file, "	 * @param \$$k part of the object identifier (primary key)\n");
}
fwrite($file, "	 */\n");
fwrite($file, "	public function deleteByKey($mpk_id){\n");
fwrite($file, "		\$object = \$this->get($mpk_id);\n");
fwrite($file, "		if(\$object != null){\n");
fwrite($file, "			\$this->delete(\$object);\n");
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
fwrite($file, "			throw new ".$manager_classname."Exception('No corresponding object found in the database', 1);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
}
else{
fwrite($file, "	/**\n");
fwrite($file, "	 * Use this method to get a $classname extracted from database\n");
fwrite($file, "	 * @param \$id the object identifier (primary key)\n");
fwrite($file, "	 * @return \\$nsp"."$classname\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function get(\$id){\n");
fwrite($file, "		if(! isset(\$this->stmts['get'])){\n");
fwrite($file, "			\$this->stmts['get'] = \$this->db->prepare('SELECT * FROM `$tablename` WHERE `$pk` = :$pk');\n");
fwrite($file, "		}\n");
fwrite($file, "		\$stmt = \$this->stmts['get'];\n");
fwrite($file, "		try{\n");
fwrite($file, "			\$stmt->bindValue(':$pk', \$id$pk_params);\n");
fwrite($file, "			\$stmt->execute();\n");
fwrite($file, "			\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC);\n");
fwrite($file, "			if(\$datas){\n");
fwrite($file, "				return new $classname(\$datas);\n");
fwrite($file, "			}\n");
fwrite($file, "			return null;\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\\Exception \$e){\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Use this method to check if a $classname exists in the database.\n");
fwrite($file, "	 * Using this method is faster than get method then check if value is returned.\n");
fwrite($file, "	 * @param \$id the object identifier (primary key)\n");
fwrite($file, "	 * @return boolean true if a corresponding entry is found, false otherwise\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function exists(\$id){\n");
fwrite($file, "		if(! isset(\$this->stmts['exists'])){\n");
fwrite($file, "			\$this->stmts['exists'] = \$this->db->prepare('SELECT EXISTS(SELECT 1 FROM `$tablename` WHERE `$pk` = :$pk LIMIT 1)');\n");
fwrite($file, "		}\n");
fwrite($file, "		\$stmt = \$this->stmts['exists'];\n");
fwrite($file, "		try{\n");
fwrite($file, "			\$stmt->bindValue(':$pk', \$id$pk_params);\n");
fwrite($file, "			\$stmt->execute();\n");
fwrite($file, "			\$datas = \$stmt->fetch(\\PDO::FETCH_NUM);\n");
fwrite($file, "			return (bool)\$datas[0];\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\\Exception \$e){\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Use this method to delete a $classname using his primary key instead of the object itself.\n");
fwrite($file, "	 * Using this method could be easier sometimes but does the same than retrieving the object then delete it.\n");
fwrite($file, "	 * @param \$id the object identifier (primary key)\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function deleteByKey(\$id){\n");
fwrite($file, "		\$object = \$this->get(\$id);\n");
fwrite($file, "		if(\$object != null){\n");
fwrite($file, "			\$this->delete(\$object);\n");
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
fwrite($file, "			throw new ".$manager_classname."Exception('No corresponding object found in the database', 1);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
}
if($hasUniqueAttributes){
foreach($datas['attributes'] as $attribute => $infos){
if((($infos['type'] == 'value') || ($infos['type'] == "refValue")) && ($infos['unique'] || ($infos['primary_key'] && (! $multi_pk)))){
$params = $this->getPDOParams($infos['datatype']);
fwrite($file, "	/**\n");
fwrite($file, "	 * getBy".$this->camelize($attribute)." method\n");
fwrite($file, "	 * @param \$value a value of $attribute (defined as unique or primary key)\n");
fwrite($file, "	 * @return \\$nsp"."$classname\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function getBy".$this->camelize($attribute)."(\$value){\n");
fwrite($file, "		if(! isset(\$this->stmts['get_by_$attribute'])){\n");
fwrite($file, "			\$this->stmts['get_by_$attribute'] = \$this->db->prepare('SELECT * FROM `$tablename` WHERE `$attribute` = :value');\n");
fwrite($file, "		}\n");
fwrite($file, "		\$stmt = \$this->stmts['get_by_$attribute'];\n");
fwrite($file, "		try{\n");
fwrite($file, "			\$stmt->bindValue(':value', \$value$params);\n");
fwrite($file, "			\$stmt->execute();\n");
fwrite($file, "			\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC);\n");
fwrite($file, "			if(\$datas){\n");
fwrite($file, "				return new $classname(\$datas);\n");
fwrite($file, "			}\n");
fwrite($file, "			return null;\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\\Exception \$e){\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * existsBy".$this->camelize($attribute)." method\n");
fwrite($file, "	 * Using this method is faster than using corresponding get method then check if a value is returned.\n");
fwrite($file, "	 * @param \$value a value of $attribute (defined as unique or primary key)\n");
fwrite($file, "	 * @return boolean true if a corresponding entry is found, false otherwise\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function existsBy".$this->camelize($attribute)."(\$value){\n");
fwrite($file, "		if(! isset(\$this->stmts['exists_by_$attribute'])){\n");
fwrite($file, "			\$this->stmts['exists_by_$attribute'] = \$this->db->prepare('SELECT EXISTS(SELECT 1 FROM `$tablename` WHERE `$attribute` = :value LIMIT 1)');\n");
fwrite($file, "		}\n");
fwrite($file, "		\$stmt = \$this->stmts['exists_by_$attribute'];\n");
fwrite($file, "		try{\n");
fwrite($file, "			\$stmt->bindValue(':value', \$value$params);\n");
fwrite($file, "			\$stmt->execute();\n");
fwrite($file, "			\$datas = \$stmt->fetch(\\PDO::FETCH_NUM);\n");
fwrite($file, "			return \$datas[0];\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\\Exception \$e){\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
}
}
}
fwrite($file, "	/**\n");
fwrite($file, "	 * Inserts a new object in the database.\n");
fwrite($file, "	 * Related objects are added or modified according the object attributes\n");
fwrite($file, "	 * @param \$object a $classname object\n");
fwrite($file, "	 * @param \$cascade this parameter should not be used. It exists for internal purpose only.\n");
if($multi_pk){
fwrite($file, "	 * @return array - object identifiers array (values of the primary key)\n");
}
else{
fwrite($file, "	 * @return ".$pk_infos['datatype']['type']." object identifier (primary key)\n");
}
fwrite($file, "	 */\n");
fwrite($file, "	public function add($classname \$object, \$cascade = false){\n");
fwrite($file, "		if(! \$object->_isNew()){\n");
fwrite($file, "			throw new ".$manager_classname."Exception('This object is extracted from the database or has already been save. Perhaps you should use update method instead', 1);\n");
fwrite($file, "		}\n");
fwrite($file, "		if(\$object->_isDeleted()){\n");
fwrite($file, "			throw new ".$manager_classname."Exception('This object has been deleted. You cannot add it again', 1);\n");
fwrite($file, "		}\n");
fwrite($file, "\n");
fwrite($file, "		try{\n");
fwrite($file, "			if(! \$cascade){\n");
fwrite($file, "				\$this->db->beginTransaction();\n");
fwrite($file, "			}\n");
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == "refObject"){
fwrite($file, "			\$o = \$object->get".$this->camelize($attribute)."();\n");
fwrite($file, "			if(\$o != null && \$o->_isNew()){\n");
fwrite($file, "				\$id = \\$nsp".$infos['manager']."::getInstance()->add(\$o, true);\n");
fwrite($file, "				if(\$object->get".$this->camelize($infos['ref'])."() == null){\n");
fwrite($file, "					\$object->set".$this->camelize($infos['ref'])."(\$id);\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
}
}
fwrite($file, "			if(! isset(\$this->stmts['add'])){\n");
fwrite($file, "				\$this->stmts['add'] = \$this->db->prepare('INSERT INTO `$tablename` (");
$first = true;
$following_values_stmt_part = "";
$following_bind_values_part = "";
foreach($datas['attributes'] as $attribute => $infos){
if((($infos['type'] == "value") || ($infos['type'] == "refValue")) && (! $infos['datatype']['auto_increment'])){
if(! $first){
fwrite($file, ", ");
$following_values_stmt_part .= ", ";
}
$first = false;
fwrite($file, "`$attribute`");
$following_values_stmt_part .= ":$attribute";
$params = $this->getPDOParams($infos['datatype']);
$following_bind_values_part .= "			\$stmt->bindValue(':$attribute', \$object->get".$this->camelize($attribute)."()$params);\n";
}
}
fwrite($file, ") VALUES ($following_values_stmt_part)');\n");
fwrite($file, "			}\n");
if($hasCrossObjectsList){
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == "objectsList") && ($infos['ext_cardinal'] == 'many')){
fwrite($file, "			if(! isset(\$this->stmts['add_$attribute'])){\n");
fwrite($file, "				\$this->stmts['add_$attribute'] = \$this->db->prepare('INSERT INTO `".$infos['ext_table']."` (`".$infos['ext_my_id']."`, `".$infos['ext_column']."`");
$following_values_stmt_part = ":oid, :extid";
foreach($infos['ext_extra_attrs'] as $extra_attr){
$extra_attr_original_name = substr($extra_attr[0], $extra_attr[1]);
fwrite($file, ", `".$extra_attr_original_name."`");
$following_values_stmt_part .= ", :$extra_attr[0]";
}
fwrite($file, ") VALUES ($following_values_stmt_part)');\n");
fwrite($file, "			}\n");
}
}
}
fwrite($file, "			\$stmt = \$this->stmts['add'];\n");
fwrite($file, "$following_bind_values_part");
fwrite($file, "			\$stmt->execute();\n");
fwrite($file, "\n");
fwrite($file, "			\$object->_setNew(false);\n");
fwrite($file, "\n");
if($multi_pk){
fwrite($file, "			\$oid = array();\n");
foreach($pk as $i => $k){
if($pk_infos[$i]['datatype']['type'] == 'integer' && $pk_infos[$i]['datatype']['auto_increment']){
fwrite($file, "			\$oid[$i] = \$this->db->lastInsertId();\n");
fwrite($file, "			\$object->set".$this->camelize($k)."(\$oid[$i]);\n");
}
else{
fwrite($file, "			\$oid[$i] = \$object->get".$this->camelize($k)."();\n");
}
}
}
else{
if($pk_infos['datatype']['type'] == 'integer' && $pk_infos['datatype']['auto_increment']){
fwrite($file, "			\$oid = \$this->db->lastInsertId();\n");
fwrite($file, "			\$object->set".$this->camelize($pk)."(\$oid);\n");
}
else{
fwrite($file, "			\$oid = \$object->get".$this->camelize($pk)."();\n");
}
}
if($hasObject){
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == "object"){
$method = 'set'.$this->camelize($infos['ext_my_id']);
fwrite($file, "\n");
fwrite($file, "			\$o = \$object->get".$this->camelize($attribute)."();\n");
fwrite($file, "			if(isset(\$o)){\n");
fwrite($file, "				\$manager = \\$nsp".$infos['manager']."::getInstance();\n");
fwrite($file, "				\$o->".$method."(\$oid);\n");
fwrite($file, "				if(\$o->_isNew()){\n");
fwrite($file, "					\$manager->add(\$o, true);\n");
fwrite($file, "				}\n");
fwrite($file, "				else{\n");
fwrite($file, "					\$manager->update(\$o, false, true);\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
}
}
}
if($hasObjectsList){
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == "objectsList"){
$cardinal = $infos['ext_cardinal'];
if($cardinal == 'one'){
$method = 'set'.$this->camelize($infos['ext_my_id']);
fwrite($file, "\n");
fwrite($file, "			\$manager = \\$nsp".$infos['manager']."::getInstance();\n");
fwrite($file, "			foreach(\$object->get".$this->camelize($attribute)."() as \$o){\n");
fwrite($file, "				\$o->".$method."(\$oid);\n");
fwrite($file, "				if(\$o->_isNew()){\n");
fwrite($file, "					\$manager->add(\$o, true);\n");
fwrite($file, "				}\n");
fwrite($file, "				else{\n");
fwrite($file, "					\$manager->update(\$o, false, true);\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "\n");
}
else{
$method = 'add'.$datas['object_name'];
$params = $this->getPDOParams($infos['ext_datatype']);
fwrite($file, "\n");
fwrite($file, "			\$manager = \\$nsp".$infos['manager']."::getInstance();\n");
fwrite($file, "			\$stmt = \$this->stmts['add_$attribute'];\n");
fwrite($file, "			\$stmt->bindValue(':oid', \$oid".$pk_params.");\n");
fwrite($file, "			foreach(\$object->get".$this->camelize($attribute)."() as \$o){\n");
fwrite($file, "				if(\$o->_isNew()){\n");
foreach($infos['ext_extra_attrs'] as $extra_attr){
fwrite($file, "					\$object->set".$this->camelize($extra_attr[0])."(\$o->get".$this->camelize($extra_attr[0])."());\n");
}
fwrite($file, "					\$o->".$method."(\$object);\n");
fwrite($file, "					\$manager->add(\$o, true);\n");
fwrite($file, "				}\n");
fwrite($file, "				else{\n");
fwrite($file, "					\$stmt->bindValue(':extid', \$o->get".$this->camelize($infos['id_attributes'][0])."()".$params.");\n");
foreach($infos['ext_extra_attrs'] as $extra_attr){
$extra_params = $this->getPDOParams($datas['attributes'][$extra_attr[0]]['datatype']);
fwrite($file, "					\$stmt->bindValue(':$extra_attr[0]', \$o->get".$this->camelize($extra_attr[0])."()".$extra_params.");\n");
}
fwrite($file, "					\$stmt->execute();\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
}
}
}
}
fwrite($file, "\n");
fwrite($file, "			if(! \$cascade){\n");
fwrite($file, "				\$this->db->commit();\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\\Exception \$e){\n");
fwrite($file, "			if(! \$cascade){\n");
fwrite($file, "				\$this->db->rollBack();\n");
fwrite($file, "			}\n");
fwrite($file, "			\$object->_setNew(true);\n");
if($multi_pk){
foreach($pk as $i => $k){
if($pk_infos[$i]['datatype']['type'] == 'integer' && $pk_infos[$i]['datatype']['auto_increment']){
fwrite($file, "			\$object->set".$this->camelize($k)."(\$oid[$i]);\n");
}
}
}
else{
if($pk_infos['datatype']['type'] == 'integer' && $pk_infos['datatype']['auto_increment']){
fwrite($file, "			\$object->set".$this->camelize($pk)."(null);\n");
}
}
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "\n");
if($hasObjectsList){
fwrite($file, "		\$object->_reinit(true);\n");
}
fwrite($file, "		return \$oid;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Updates an existing object in the database according the modifications done on it.\n");
fwrite($file, "	 * Related objects are added or modified according the object attributes.\n");
fwrite($file, "	 * @param \$object a $classname object\n");
fwrite($file, "	 * @param \$cascade this parameter should not be used. It exists for internal purpose only.\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function update($classname \$object, \$forceCrossRefsAttributesChecking = false, \$cascade = false){\n");
fwrite($file, "		if(\$object->_isNew()){\n");
fwrite($file, "			throw new ".$manager_classname."Exception('This object is a new object. Perhaps you should use add method instead', 1);\n");
fwrite($file, "		}\n");
fwrite($file, "		if(\$object->_isDeleted()){\n");
fwrite($file, "			throw new ".$manager_classname."Exception('This object has been deleted. You cannot update it', 1);\n");
fwrite($file, "		}\n");
fwrite($file, "\n");
if($hasCrossObjectsList && $hasCrossObjectsExtraAttributes){
fwrite($file, "		if(\$forceCrossRefsAttributesChecking){\n");
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == "objectsList") && ($infos['ext_cardinal'] == 'many') && (! empty($infos['ext_extra_attrs']))){
fwrite($file, "			if(! isset(\$this->stmts['update_$attribute'])){\n");
fwrite($file, "				\$this->stmts['update_$attribute'] = \$this->db->prepare('UPDATE `".$infos['ext_table']."` SET ");
$first = true;
foreach($infos['ext_extra_attrs'] as $extra_attr){
if(! $first){
fwrite($file, ", ");
}
$first = false;
$extra_attr_original_name = substr($extra_attr[0], $extra_attr[1]);
fwrite($file, "`".$extra_attr_original_name."` = :".$extra_attr[0]."");
}
fwrite($file, " WHERE `".$infos['ext_my_id']."` = :oid AND `".$infos['ext_column']."` = :extid');\n");
fwrite($file, "			}			\n");
}
}
fwrite($file, "			try{\n");
fwrite($file, "				\$oid = \$object->get".$this->camelize($pk)."();\n");
fwrite($file, "\n");
fwrite($file, "				\$this->db->beginTransaction();\n");
fwrite($file, "\n");
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == "objectsList") && ($infos['ext_cardinal'] == 'many') && (! empty($infos['ext_extra_attrs']))){
fwrite($file, "				\$stmt = \$this->stmts['update_$attribute'];\n");
fwrite($file, "				\$stmt->bindValue(':oid', \$oid".$pk_params.");\n");
fwrite($file, "				foreach(\$object->getOriginal".$this->camelize($attribute)."() as \$o){\n");
fwrite($file, "					if(\$o->_isModified()){\n");
fwrite($file, "						\$stmt->bindValue(':extid', \$o->get".$this->camelize($infos['id_attributes'][0])."()".$params.");\n");
foreach($infos['ext_extra_attrs'] as $extra_attr){
$extra_params = $this->getPDOParams($datas['attributes'][$extra_attr[0]]['datatype']);
fwrite($file, "						\$stmt->bindValue(':$extra_attr[0]', \$o->get".$this->camelize($extra_attr[0])."()".$extra_params.");\n");
}
fwrite($file, "						\$stmt->execute();\n");
fwrite($file, "					}\n");
fwrite($file, "				}\n");
}
}
fwrite($file, "\n");
fwrite($file, "				\$this->db->commit();\n");
fwrite($file, "			}\n");
fwrite($file, "			catch(\\Exception \$e){\n");
fwrite($file, "				\$this->db->rollBack();\n");
fwrite($file, "				throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
}
fwrite($file, "\n");
fwrite($file, "		if(\$object->_isModified()){\n");
fwrite($file, "			try{\n");
fwrite($file, "				if(! \$cascade){\n");
fwrite($file, "					\$this->db->beginTransaction();\n");
fwrite($file, "				}\n");
fwrite($file, "\n");
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == "refObject"){
fwrite($file, "				\$o = \$object->get".$this->camelize($attribute)."();\n");
fwrite($file, "				if(\$o != null && \$o->_isNew()){\n");
fwrite($file, "					\$id = \\$nsp".$infos['manager']."::getInstance()->add(\$o, true);\n");
fwrite($file, "					if(\$object->get".$this->camelize($infos['ref'])."() == null){\n");
fwrite($file, "						\$object->set".$this->camelize($infos['ref'])."(\$id);\n");
fwrite($file, "					}\n");
fwrite($file, "				}\n");
fwrite($file, "\n");
}
}
if($multi_pk){
fwrite($file, "				\$oid = array();\n");
foreach($pk as $i => $k){
fwrite($file, "				\$oid[$i] = \$object->get".$this->camelize($k)."();\n");
}
}
else{
fwrite($file, "				\$oid = \$object->get".$this->camelize($pk)."();\n");
}
fwrite($file, "\n");
fwrite($file, "				if(\$object->_isRenamed()){\n");
if($multi_pk){
fwrite($file, "					\$ooid = array();\n");
foreach($pk as $i => $k){
fwrite($file, "					\$ooid[$i] = \$object->getOriginal".$this->camelize($k)."();\n");
}
}
else{
fwrite($file, "					\$ooid = \$object->getOriginal".$this->camelize($pk)."();\n");
}
fwrite($file, "					if(! isset(\$this->stmts['rename'])){\n");
fwrite($file, "						\$this->stmts['rename'] = \$this->db->prepare('UPDATE `$tablename` SET ");
$first = true;
$following = "";
foreach($datas['attributes'] as $attribute => $infos){
if((($infos['type'] == "value") || ($infos['type'] == "refValue")) && ($infos['primary_key'])){
if(! $first){
fwrite($file, ", ");
}
$first = false;
fwrite($file, "`$attribute` = :n_$attribute");
$params = $this->getPDOParams($infos['datatype']);
$following .= "					\$stmt->bindValue(':n_$attribute', \$object->get".$this->camelize($attribute)."()$params);\n";
}
}
if($multi_pk){
foreach($pk as $i => $k){
$following .= "					\$stmt->bindValue(':$k', \$ooid[$i]".$pk_params[$i].");\n";
}
fwrite($file, " WHERE $mpk_where');\n");
}
else{
$following .= "					\$stmt->bindValue(':$pk', \$ooid$pk_params);\n";
fwrite($file, " WHERE `$pk` = :$pk');\n");
}
fwrite($file, "					}\n");
fwrite($file, "					\$stmt = \$this->stmts['rename'];\n");
fwrite($file, "$following\n");
fwrite($file, "					\$stmt->execute();\n");
fwrite($file, "					\$object->_setRenamed(false);\n");
fwrite($file, "				}\n");
fwrite($file, "\n");
fwrite($file, "				if(! isset(\$this->stmts['update'])){\n");
fwrite($file, "					\$this->stmts['update'] = \$this->db->prepare('UPDATE `$tablename` SET ");
$first = true;
$following = "";
foreach($datas['attributes'] as $attribute => $infos){
if((($infos['type'] == "value") || ($infos['type'] == "refValue")) && (! $infos['primary_key'])){
if(! $first){
fwrite($file, ", ");
}
$first = false;
fwrite($file, "`$attribute` = :$attribute");
$params = $this->getPDOParams($infos['datatype']);
$following .= "				\$stmt->bindValue(':$attribute', \$object->get".$this->camelize($attribute)."()$params);\n";
}
}
if($multi_pk){
foreach($pk as $i => $k){
$following .= "				\$stmt->bindValue(':$k', \$oid[$i]".$pk_params[$i].");\n";
}
fwrite($file, " WHERE $mpk_where');\n");
}
else{
$following .= "				\$stmt->bindValue(':$pk', \$oid$pk_params);\n";
fwrite($file, " WHERE `$pk` = :$pk');\n");
}
fwrite($file, "				}\n");
if($hasCrossObjectsList){
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == "objectsList") && ($infos['ext_cardinal'] == 'many')){
fwrite($file, "				if(! isset(\$this->stmts['add_$attribute'])){\n");
fwrite($file, "					\$this->stmts['add_$attribute'] = \$this->db->prepare('INSERT INTO `".$infos['ext_table']."` (`".$infos['ext_my_id']."`, `".$infos['ext_column']."`");
$following_values_stmt_part = ":oid, :extid";
foreach($infos['ext_extra_attrs'] as $extra_attr){
$extra_attr_original_name = substr($extra_attr[0], $extra_attr[1]);
fwrite($file, ", `".$extra_attr_original_name."`");
$following_values_stmt_part .= ", :$extra_attr[0]";
}
fwrite($file, ") VALUES ($following_values_stmt_part)');\n");
fwrite($file, "				}\n");
fwrite($file, "				if(! isset(\$this->stmts['del_$attribute'])){\n");
fwrite($file, "					\$this->stmts['del_$attribute'] = \$this->db->prepare('DELETE FROM `".$infos['ext_table']."` WHERE `".$infos['ext_my_id']."` = :oid AND `".$infos['ext_column']."` = :extid');\n");
fwrite($file, "				}\n");
}
}
}
fwrite($file, "\n");
fwrite($file, "				\$stmt = \$this->stmts['update'];\n");
fwrite($file, "$following\n");
fwrite($file, "				\$stmt->execute();\n");
if($hasObject){
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == "object"){
$method = 'set'.$this->camelize($infos['ext_my_id']);
fwrite($file, "\n");
fwrite($file, "				\$o = \$object->get".$this->camelize($attribute)."();\n");
fwrite($file, "				\$manager = \\$nsp".$infos['manager']."::getInstance();\n");
fwrite($file, "				if(isset(\$o)){\n");
fwrite($file, "					\$o->".$method."(\$oid);\n");
fwrite($file, "					if(\$o->_isNew()){\n");
fwrite($file, "						\$manager->add(\$o, true);\n");
fwrite($file, "					}\n");
fwrite($file, "					else{\n");
fwrite($file, "						\$manager->update(\$o, false, true);\n");
fwrite($file, "					}\n");
fwrite($file, "				}\n");
fwrite($file, "				else{\n");
fwrite($file, "					\$o = \$manager->getBy".$this->camelize($infos['ext_my_id'])."(\$oid);\n");
fwrite($file, "					if(isset(\$o)){\n");
if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
fwrite($file, "						\$manager->delete(\$o, true);\n");
}
else{
if(isset($infos['datatype']['default'])){
fwrite($file, "						\$o->".$method."(\"".$infos['datatype']['default']."\");\n");
}
else{
fwrite($file, "						\$o->".$method."(null);\n");
}
fwrite($file, "						\$manager->update(\$o, false, true);\n");
}
fwrite($file, "					}\n");
fwrite($file, "				}\n");
}
}
}
if($hasObjectsList){
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == "objectsList"){
$cardinal = $infos['ext_cardinal'];
if($cardinal == 'one'){
$method = 'set'.$this->camelize($infos['ext_my_id']);
fwrite($file, "\n");
fwrite($file, "				\$manager = \\$nsp".$infos['manager']."::getInstance();\n");
fwrite($file, "				foreach(\$object->getDeleted".$this->camelize($attribute)."() as \$o){\n");
if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
fwrite($file, "					\$manager->delete(\$o, true);\n");
}
else{
if(isset($infos['datatype']['default'])){
fwrite($file, "					\$o->".$method."(\"".$infos['datatype']['default']."\");\n");
}
else{
fwrite($file, "					\$o->".$method."(null);\n");
}
fwrite($file, "					\$manager->update(\$o, false, true);\n");
}
fwrite($file, "				}\n");
fwrite($file, "				foreach(\$object->getAdded".$this->camelize($attribute)."() as \$o){\n");
fwrite($file, "					\$o->".$method."(\$oid);\n");
fwrite($file, "					if(\$o->_isNew()){\n");
fwrite($file, "						\$manager->add(\$o, true);\n");
fwrite($file, "					}\n");
fwrite($file, "					else{\n");
fwrite($file, "						\$manager->update(\$o, false, true);\n");
fwrite($file, "					}\n");
fwrite($file, "				}\n");
}
else{
$method = 'add'.$datas['object_name'];
$params = $this->getPDOParams($infos['ext_datatype']);
fwrite($file, "\n");
fwrite($file, "				\$stmt = \$this->stmts['del_$attribute'];\n");
fwrite($file, "				\$stmt->bindValue(':oid', \$oid".$pk_params.");\n");
fwrite($file, "				foreach(\$object->getDeleted".$this->camelize($attribute)."() as \$o){\n");
fwrite($file, "					\$stmt->bindValue(':extid', \$o->get".$this->camelize($infos['id_attributes'][0])."()".$params.");\n");
fwrite($file, "					\$stmt->execute();\n");
fwrite($file, "				}\n");
fwrite($file, "				\$manager = \\$nsp".$infos['manager']."::getInstance();\n");
fwrite($file, "				\$stmt = \$this->stmts['add_$attribute'];\n");
fwrite($file, "				\$stmt->bindValue(':oid', \$oid".$pk_params.");\n");
fwrite($file, "				foreach(\$object->getAdded".$this->camelize($attribute)."() as \$o){\n");
fwrite($file, "					if(\$o->_isNew()){\n");
foreach($infos['ext_extra_attrs'] as $extra_attr){
fwrite($file, "						\$object->set".$this->camelize($extra_attr[0])."(\$o->get".$this->camelize($extra_attr[0])."());\n");
}
fwrite($file, "						\$o->".$method."(\$object);\n");
fwrite($file, "						\$manager->add(\$o, true);\n");
fwrite($file, "					}\n");
fwrite($file, "					else{\n");
fwrite($file, "						\$stmt->bindValue(':extid', \$o->get".$this->camelize($infos['id_attributes'][0])."()".$params.");\n");
foreach($infos['ext_extra_attrs'] as $extra_attr){
$extra_params = $this->getPDOParams($datas['attributes'][$extra_attr[0]]['datatype']);
fwrite($file, "						\$stmt->bindValue(':$extra_attr[0]', \$o->get".$this->camelize($extra_attr[0])."()".$extra_params.");\n");
}
fwrite($file, "						\$stmt->execute();\n");
fwrite($file, "					}\n");
fwrite($file, "				}\n");
}
}
}
}
fwrite($file, "				if(! \$cascade){\n");
fwrite($file, "					\$this->db->commit();\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "			catch(\\Exception \$e){\n");
fwrite($file, "				if(! \$cascade){\n");
fwrite($file, "					\$this->db->rollBack();\n");
fwrite($file, "				}\n");
fwrite($file, "				throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "			}\n");
fwrite($file, "\n");
if($hasObjectsList){
fwrite($file, "			\$object->_reinit(false);\n");
}
fwrite($file, "			\$object->_setModified(false);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Deletes an existing object in the database.\n");
fwrite($file, "	 * Related objects are modified according this deletion, but are not deleted.\n");
fwrite($file, "	 * @param \$object a $classname object\n");
fwrite($file, "	 * @param \$cascade this parameter should not be used. It exists for internal purpose only.\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function delete($classname \$object, \$cascade = false){\n");
fwrite($file, "		if(\$object->_isNew()){\n");
fwrite($file, "			throw new ".$manager_classname."Exception('This object is a new object. Cannot delete it from database', 1);\n");
fwrite($file, "		}\n");
fwrite($file, "		if(\$object->_isDeleted()){\n");
fwrite($file, "			throw new ".$manager_classname."Exception('This object has already been deleted. You cannot delete it again', 1);\n");
fwrite($file, "		}\n");
fwrite($file, "\n");
fwrite($file, "		try{\n");
fwrite($file, "			if(! \$cascade){\n");
fwrite($file, "				\$this->db->beginTransaction();\n");
fwrite($file, "			}\n");
fwrite($file, "\n");
if($hasObject){
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == "object"){
$method = 'set'.$this->camelize($infos['ext_my_id']);
fwrite($file, "\n");
fwrite($file, "			\$manager = \\$nsp".$infos['manager']."::getInstance();\n");
fwrite($file, "			\$o = \$object->get".$this->camelize($attribute)."();\n");
fwrite($file, "			if(isset(\$o)){\n");
fwrite($file, "				if(! \$o->_isNew()){\n");
if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
fwrite($file, "					\$manager->delete(\$o, true);\n");
}
else{
if(isset($infos['ext_default'])){
fwrite($file, "					\$o->".$method."(\"".$infos['ext_default']."\");\n");
}
else{
fwrite($file, "					\$o->".$method."(null);\n");
}
fwrite($file, "					\$manager->update(\$o, false, true);\n");
}
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "			else{\n");
fwrite($file, "				\$o = \$manager->getBy".$this->camelize($infos['ext_my_id'])."(\$object->get".$this->camelize($pk)."());\n");
fwrite($file, "				if(isset(\$o)){\n");
if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
fwrite($file, "					\$manager->delete(\$o, true);\n");
}
else{
if(isset($infos['ext_default'])){
fwrite($file, "					\$o->".$method."(\"".$infos['ext_default']."\");\n");
}
else{
fwrite($file, "					\$o->".$method."(null);\n");
}
fwrite($file, "					\$manager->update(\$o, false, true);\n");
}
fwrite($file, "				}\n");
fwrite($file, "			}\n");
}
}
}
if($hasObjectsList){
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == "objectsList"){
$cardinal = $infos['ext_cardinal'];
if($cardinal == 'one'){
$method = 'set'.$this->camelize($infos['ext_my_id']);
fwrite($file, "\n");
fwrite($file, "			\$manager = \\$nsp".$infos['manager']."::getInstance();\n");
fwrite($file, "			foreach(\$object->get".$this->camelize($attribute)."() as \$o){\n");
fwrite($file, "				if(! \$o->_isNew()){\n");
if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
fwrite($file, "					\$manager->delete(\$o, true);\n");
}
else{
if(isset($infos['ext_default'])){
fwrite($file, "					\$o->".$method."(\"".$infos['ext_default']."\");\n");
}
else{
fwrite($file, "					\$o->".$method."(null);\n");
}
fwrite($file, "					\$manager->update(\$o, false, true);\n");
}
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "			foreach(\$object->getDeleted".$this->camelize($attribute)."() as \$o){\n");
if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
fwrite($file, "				\$manager->delete(\$o, true);\n");
}
else{
if(isset($infos['ext_default'])){
fwrite($file, "				\$o->".$method."(\"".$infos['ext_default']."\");\n");
}
else{
fwrite($file, "				\$o->".$method."(null);\n");
}
fwrite($file, "				\$manager->update(\$o, false, true);\n");
}
fwrite($file, "			}\n");
}
else{
$method = 'delete'.$datas['object_name'];
$params = $this->getPDOParams($infos['ext_datatype']);
fwrite($file, "\n");
fwrite($file, "			\$manager = \\$nsp".$infos['manager']."::getInstance();\n");
fwrite($file, "			foreach(\$object->get".$this->camelize($attribute)."() as \$o){\n");
fwrite($file, "				\$o->".$method."(\$object);\n");
fwrite($file, "				if(! \$o->_isNew()){\n");
fwrite($file, "					\$manager->update(\$o, false, true);\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
}
}
}
}
fwrite($file, "\n");
fwrite($file, "			if(! isset(\$this->stmts['delete'])){\n");
if($multi_pk){
fwrite($file, "				\$this->stmts['delete'] = \$this->db->prepare('DELETE FROM `$tablename` WHERE $mpk_where');\n");
}
else{
fwrite($file, "				\$this->stmts['delete'] = \$this->db->prepare('DELETE FROM `$tablename` WHERE `$pk` = :$pk');\n");
}
fwrite($file, "			}\n");
if($multi_pk){
foreach($pk as $i => $k){
fwrite($file, "			\$this->stmts['delete']->bindValue(':$k', \$object->get".$this->camelize($k)."()".$pk_params[$i].");\n");
}
}
else{
fwrite($file, "			\$this->stmts['delete']->bindValue(':$pk', \$object->get".$this->camelize($pk)."()$pk_params);\n");
}
fwrite($file, "			\$this->stmts['delete']->execute();\n");
fwrite($file, "\n");
fwrite($file, "			if(! \$cascade){\n");
fwrite($file, "				\$this->db->commit();\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\\Exception \$e){\n");
fwrite($file, "			if(! \$cascade){\n");
fwrite($file, "				\$this->db->rollBack();\n");
fwrite($file, "			}\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "\n");
fwrite($file, "		\$object->_setDeleted(true);\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Get the number of objects in the database.\n");
fwrite($file, "	 * @return integer - the number of $classname objects.\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function count(){\n");
fwrite($file, "		if(! isset(\$this->stmts['count'])){\n");
fwrite($file, "			\$this->stmts['count'] = \$this->db->prepare('SELECT COUNT(*) FROM `$tablename`');\n");
fwrite($file, "		}\n");
fwrite($file, "		\$stmt = \$this->stmts['count'];\n");
fwrite($file, "		try{\n");
fwrite($file, "			\$stmt->execute();\n");
fwrite($file, "			\$datas = \$stmt->fetch(\\PDO::FETCH_NUM);\n");
fwrite($file, "			return \$datas[0];\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\Exception \$e){\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Get a set of existing objects in the database.\n");
fwrite($file, "	 * @param \$sortAttributes (optional) could be a single string or an array of string (or an associative array of string with order) corresponding to the columns names where datas are stored. Could be null. Default null.\n");
fwrite($file, "	 * @param \$offset (optional) an integer value : use it together whith \$limit param to extract a subset of objects stored. If null, all objects are returned. Default null.\n");
fwrite($file, "	 * @param \$limit (optional) an integer value : use it together whith \$offset param to extract a subset of objects stored. If null, all objects are returned. Default null.\n");
fwrite($file, "	 * @param \$asArray (optional) boolean : if true the return is a 2 dimensions array containing datas in the corresponding table ($tablename). Default false.\n");
fwrite($file, "	 * @return \\$nsp"."$classname"."[] - an array of $classname : the objects found in storage according \$offset and \$limit parameters passed. If \$asArray is set to true, returns a 2 dimensions array containing datas of these objects.\n");
fwrite($file, "	 * @example\n");
fwrite($file, "	 *		getList('name');  // assume order is ASC\n");
fwrite($file, "	 *		getList(array('name', 'firstname'));  // assume order is ASC for both name and firstname\n");
fwrite($file, "	 *		getList(array('name' => 'ASC', 'register_date' => 'DESC'));  // last registered first\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function getList(\$sortAttributes = null, \$offset = null, \$limit = null, \$asArray = false){\n");
fwrite($file, "		\$objects = array();\n");
fwrite($file, "		\$order_string = '';\n");
fwrite($file, "		\$limit_string = '';\n");
fwrite($file, "		\$stmt = '';\n");
fwrite($file, "\n");
fwrite($file, "		try{\n");
fwrite($file, "			if( (! isset(\$sortAttributes)) && ( ! (isset(\$offset) && isset(\$limit)))){\n");
fwrite($file, "				if(! isset(\$this->stmts['list'])){\n");
fwrite($file, "					\$this->stmts['list'] = \$this->db->prepare('SELECT * FROM `$tablename`');\n");
fwrite($file, "				}\n");
fwrite($file, "				\$stmt = \$this->stmts['list'];\n");
fwrite($file, "				\$stmt->execute();\n");
fwrite($file, "			}\n");
fwrite($file, "			else{\n");
fwrite($file, "				if(isset(\$offset) && isset(\$limit)){\n");
fwrite($file, "					\$limit_string = ' LIMIT :offset, :limit';\n");
fwrite($file, "				}\n");
fwrite($file, "				if(isset(\$sortAttributes)){\n");
fwrite($file, "					if(is_array(\$sortAttributes)){\n");
fwrite($file, "						if(array_values(\$sortAttributes) === \$sortAttributes){	//it's not an associative array (just column names as values), assume order is ASC\n");
fwrite($file, "							\$first = true;\n");
fwrite($file, "							foreach(\$sortAttributes as \$attr){\n");
fwrite($file, "								if(in_array(\$attr, self::\$attributesList)){\n");
fwrite($file, "									if(! \$first){\n");
fwrite($file, "										\$order_string .= ',';\n");
fwrite($file, "									}\n");
fwrite($file, "									\$first = false;\n");
fwrite($file, "									\$order_string .= \"`\$attr`\";\n");
fwrite($file, "								}\n");
fwrite($file, "							}\n");
fwrite($file, "						}\n");
fwrite($file, "						else{\n");
fwrite($file, "							\$first = true;\n");
fwrite($file, "							foreach(\$sortAttributes as \$attr => \$order){\n");
fwrite($file, "								if(in_array(\$attr, self::\$attributesList) && (\$order == 'ASC' || \$order == 'DESC')){\n");
fwrite($file, "									if(! \$first){\n");
fwrite($file, "										\$order_string .= ',';\n");
fwrite($file, "									}\n");
fwrite($file, "									\$first = false;\n");
fwrite($file, "									\$order_string .= \"`\$attr` \$order\";\n");
fwrite($file, "								}\n");
fwrite($file, "							}\n");
fwrite($file, "						}\n");
fwrite($file, "						if(\$order_string != ''){\n");
fwrite($file, "							\$order_string = ' ORDER BY '.\$order_string;\n");
fwrite($file, "						}\n");
fwrite($file, "					}\n");
fwrite($file, "					elseif(in_array(\$sortAttributes, self::\$attributesList)){\n");
fwrite($file, "						\$order_string = \" ORDER BY `\$sortAttributes`\";\n");
fwrite($file, "					}\n");
fwrite($file, "				}\n");
fwrite($file, "				\$sql = \"SELECT * FROM `$tablename`\$order_string\$limit_string\";\n");
fwrite($file, "				\$md5 = md5(\$sql);\n");
fwrite($file, "				if(!isset(\$this->stmts[\$md5])){\n");
fwrite($file, "					\$this->stmts[\$md5] = \$this->db->prepare(\$sql);\n");
fwrite($file, "				}\n");
fwrite($file, "				\$stmt = \$this->stmts[\$md5];\n");
fwrite($file, "\n");
fwrite($file, "				if(\$limit_string != ''){\n");
fwrite($file, "					\$stmt->bindValue(':offset', \$offset, \\PDO::PARAM_INT);\n");
fwrite($file, "					\$stmt->bindValue(':limit', \$limit, \\PDO::PARAM_INT);\n");
fwrite($file, "				}\n");
fwrite($file, "				\$stmt->execute();\n");
fwrite($file, "			}\n");
fwrite($file, "\n");
fwrite($file, "			if(\$asArray){\n");
fwrite($file, "				\$objects = \$stmt->fetchAll(\\PDO::FETCH_ASSOC);\n");
fwrite($file, "			}\n");
fwrite($file, "			else{\n");
fwrite($file, "				while (\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC)){\n");
fwrite($file, "					\$objects[] = new $classname(\$datas);\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "			return \$objects;\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\Exception \$e){\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
if($hasNonUniqueAttributes || $hasCrossObjectsList){
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Get a set of existing objects in the database according columns are equals to specified values.\n");
fwrite($file, "	 * @param \$attVals an associative array with column names on which we want to test the values as keys and value for which we want to keep the objects as values. The array keys of the parameter cannot be columns defined as (single)unique or (single)primary-key.\n");
fwrite($file, "	 * @param \$sortAttributes (optional) could be a single string or an array of string (or an associative array of string with order) corresponding to the columns names where datas are stored. Could be null. Default null.\n");
fwrite($file, "	 * @param \$offset (optional) an integer value : use it together whith \$limit param to extract a subset of objects stored. If null, all objects are returned. Default null.\n");
fwrite($file, "	 * @param \$limit (optional) an integer value : use it together whith \$offset param to extract a subset of objects stored. If null, all objects are returned. Default null.\n");
fwrite($file, "	 * @param \$asArray (optional) boolean : if true the return is a 2 dimensions array containing datas in the corresponding table ($tablename). Default false.\n");
fwrite($file, "	 * @return \\$nsp"."$classname"."[] - an array of $classname : the objects found in storage according \$offset and \$limit parameters passed. If \$asArray is set to true, returns a 2 dimensions array containing datas of these objects.\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function getFilteredList(\$attVals, \$sortAttributes = null, \$offset = null, \$limit = null, \$asArray = false){\n");
fwrite($file, "		\$objects = array();\n");
fwrite($file, "		\$order_string = '';\n");
fwrite($file, "		\$limit_string = '';\n");
fwrite($file, "		\$stmt = '';\n");
fwrite($file, "\n");
fwrite($file, "		try{\n");
fwrite($file, "			foreach(array_keys(\$attVals) as \$attribute){\n");
if($hasUniqueAttributes && $hasNonUniqueAttributes){
fwrite($file, "				if(in_array(\$attribute, self::\$uniqueAttributesList)){\n");
fwrite($file, "					throw new ".$manager_classname."Exception(\"Cannot filter on \$attribute. This attribute is defined as unique or primary key. You should use getBy attribute method instead to get the unique object for this value\", 1);\n");
fwrite($file, "				}\n");

}
if($hasCrossObjectsList){
fwrite($file, "				if(! (in_array(\$attribute, self::\$attributesList) || in_array(\$attribute, self::\$extAttributesList))){\n");
}
else{
fwrite($file, "				if(! in_array(\$attribute, self::\$attributesList)){\n");
}
fwrite($file, "					throw new ".$manager_classname."Exception(\"Cannot filter on \$attribute. Unknown column\", 1);\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "\n");
fwrite($file, "\n");
fwrite($file, "			if(isset(\$offset) && isset(\$limit)){\n");
fwrite($file, "				\$limit_string = ' LIMIT :offset, :limit';\n");
fwrite($file, "			}\n");
fwrite($file, "			if(isset(\$sortAttributes)){\n");
fwrite($file, "				if(is_array(\$sortAttributes)){\n");
fwrite($file, "					if(array_values(\$sortAttributes) === \$sortAttributes){	//it's not an associative array (just column names as values), assume order is ASC\n");
fwrite($file, "						\$first = true;\n");
fwrite($file, "						foreach(\$sortAttributes as \$attr){\n");
fwrite($file, "							if(in_array(\$attr, self::\$attributesList)){\n");
fwrite($file, "								if(! \$first){\n");
fwrite($file, "									\$order_string .= ',';\n");
fwrite($file, "								}\n");
fwrite($file, "								\$first = false;\n");
fwrite($file, "								\$order_string .= \"`\$attr`\";\n");
fwrite($file, "							}\n");
fwrite($file, "						}\n");
fwrite($file, "					}\n");
fwrite($file, "					else{\n");
fwrite($file, "						\$first = true;\n");
fwrite($file, "						foreach(\$sortAttributes as \$attr => \$order){\n");
fwrite($file, "							if(in_array(\$attr, self::\$attributesList) && (\$order == 'ASC' || \$order == 'DESC')){\n");
fwrite($file, "								if(! \$first){\n");
fwrite($file, "									\$order_string .= ',';\n");
fwrite($file, "								}\n");
fwrite($file, "								\$first = false;\n");
fwrite($file, "								\$order_string .= \"`\$attr` \$order\";\n");
fwrite($file, "							}\n");
fwrite($file, "						}\n");
fwrite($file, "					}\n");
fwrite($file, "					if(\$order_string != ''){\n");
fwrite($file, "						\$order_string = ' ORDER BY '.\$order_string;\n");
fwrite($file, "					}\n");
fwrite($file, "				}\n");
fwrite($file, "				elseif(in_array(\$sortAttributes, self::\$attributesList)){\n");
fwrite($file, "					\$order_string = \" ORDER BY `\$sortAttributes`\";\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "\n");
fwrite($file, "			\$select = \"SELECT `$tablename`.*\";\n");
fwrite($file, "			\$from = \"FROM `$tablename`\";\n");
fwrite($file, "			\$conditions = array();\n");
$else = "";
if($hasCrossObjectsList){
fwrite($file, "			foreach(array_keys(\$attVals) as \$attribute){		\n");
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == 'objectsList') && ($infos['ext_cardinal'] == 'many')){
fwrite($file, "				".$else."if(\$attribute == '".$infos['ext_column']."'){\n");
foreach($infos['ext_extra_attrs'] as $extra_attr){
$extra_attr_original_name = substr($extra_attr[0], $extra_attr[1]);
fwrite($file, "					\$select .= \", `".$infos['ext_table']."`.`$extra_attr_original_name` `$extra_attr[0]`\";\n");
}
fwrite($file, "					\$from .= \" INNER JOIN `".$infos['ext_table']."` ON `$tablename`.`$pk` = `".$infos['ext_table']."`.`".$infos['ext_my_id']."`\";\n");
fwrite($file, "					\$conditions[] = \"`".$infos['ext_table']."`.`".$infos['ext_column']."` = :val_\$attribute\";\n");
fwrite($file, "				}\n");
$else = "else";
}
}
fwrite($file, "				else{\n");
fwrite($file, "					\$conditions[] = \"`\$attribute` = :val_\$attribute\";\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
}
else{
fwrite($file, "			foreach(array_keys(\$attVals) as \$attribute){\n");
fwrite($file, "				\$conditions[] = \"`\$attribute` = :val_\$attribute\";\n");
fwrite($file, "			}\n");
}
fwrite($file, "			\$sql = \$select.\" \".\$from.\" WHERE \".join(' AND ', \$conditions).\"\$order_string\$limit_string\";\n");
fwrite($file, "\n");
fwrite($file, "			\$md5 = md5(\$sql);\n");
fwrite($file, "			if(!isset(\$this->stmts[\$md5])){\n");
fwrite($file, "				\$this->stmts[\$md5] = \$this->db->prepare(\$sql);\n");
fwrite($file, "			}\n");
fwrite($file, "			\$stmt = \$this->stmts[\$md5];\n");
fwrite($file, "\n");
fwrite($file, "			if(\$limit_string != ''){\n");
fwrite($file, "				\$stmt->bindValue(':offset', \$offset, \\PDO::PARAM_INT);\n");
fwrite($file, "				\$stmt->bindValue(':limit', \$limit, \\PDO::PARAM_INT);\n");
fwrite($file, "			}\n");
fwrite($file, "\n");
fwrite($file, "			foreach(\$attVals as \$attribute => \$value){\n");
$else = "";
foreach($datas['attributes'] as $attribute => $infos){
if((($infos['type'] == 'value' || $infos['type'] == 'refValue') && (! ($infos['unique'] || ($infos['primary_key'] && (count($datas['primary_key']) == 1 )))))  || (($infos['type'] == 'objectsList') && ($infos['ext_cardinal'] == 'many'))){
if(($infos['type'] == 'objectsList') && ($infos['ext_cardinal'] == 'many')){
fwrite($file, "				".$else."if(\$attribute == '".$infos['ext_column']."'){\n");
$datatype = $infos['ext_datatype'];
$params = $this->getPDOParams($datatype);
fwrite($file, "					\$stmt->bindValue(':val_".$infos['ext_column']."', \$value$params);\n");
fwrite($file, "				}\n");
}
else{
fwrite($file, "				".$else."if(\$attribute == '$attribute'){\n");
$datatype = $infos['datatype'];
$params = $this->getPDOParams($datatype);
fwrite($file, "					\$stmt->bindValue(':val_$attribute', \$value$params);\n");
fwrite($file, "				}\n");
}
$else = "else";
}
}
fwrite($file, "			}\n");
fwrite($file, "\n");
fwrite($file, "			\$stmt->execute();\n");
fwrite($file, "\n");
fwrite($file, "			if(\$asArray){\n");
fwrite($file, "				\$objects = \$stmt->fetchAll(\\PDO::FETCH_ASSOC);\n");
fwrite($file, "			}\n");
fwrite($file, "			else{\n");
fwrite($file, "				while (\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC)){\n");
fwrite($file, "					\$objects[] = new $classname(\$datas);\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "			return \$objects;\n");
fwrite($file, "		}\n");
fwrite($file, "		catch(\Exception \$e){\n");
fwrite($file, "			throw new ".$manager_classname."Exception(\$e->getMessage(),2, \$e);\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
}
fwrite($file, "}\n");
fwrite($file, "?>");
?>