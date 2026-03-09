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
##use \\$nsp"."$classname;
##use \\$nsp"."exceptions\\$manager_classname"."Exception;
##
##/**
## * @class $manager_core_classname : a class for managing $classname objects.
## * Like all other CoreManager classes, the class is a singleton an should not be instanciate.
## * The right way to deal with this class is to call the getInstance method on the subclass $manager_classname.
## * Ex :
## * \$manager = $manager_classname::getInstance();
## * \$objects = \$manager->getList();
## * ...
## */
##class $manager_core_classname {
##	
##	// Class attributes for object management
##	protected \$db;
##	protected static \$instance;
##
/** premier parcours, on en profite pour initiliser des variables */
#	protected static \$attributesList = array(
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
			#, 
		}
		#\"$attribute\"
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
##);
if($hasCrossObjectsList){
#	protected static \$extAttributesList = array(
	$first = true;
	foreach($datas['attributes'] as $attribute => $infos){
		if(($infos['type'] == 'objectsList') && ($infos['ext_cardinal'] == 'many')){
			if(! $first){
				#,
			}
			#\"".$infos['ext_column']."\"
			$first = false;
		}
	}
##);
}
if($hasUniqueAttributes && $hasNonUniqueAttributes){
	#	protected static \$uniqueAttributesList = array(
	$first = true;
	foreach($datas['attributes'] as $attribute => $infos){
		if(($infos['type'] == 'value' || $infos['type'] == 'refValue') && ($infos['unique'] || ($infos['primary_key'] && (count($datas['primary_key']) == 1 )))){
			if(! $first){
				#,
			}
			#\"$attribute\"
			$first = false;
		}
	}
	##);
}
if($hasRequiredAttributes){
	#	protected static \$requiredAttributesList = array(
	$first = true;
	foreach($datas['attributes'] as $attribute => $infos){
		if(($infos['type'] == 'value') && $infos['required']){
			if(! $first){
				#,
			}
			#\"$attribute\"
			$first = false;
		}
	}
	##);
}
##
##	protected \$stmts = array();
##	
##	/**
##	 * Constructor
##	 * This constructor should not be used.
##	 * Use getInstance method instead.
##	 * @see getInstance()
##	 */
##	protected function __construct(){
##		\$this->db = DatabaseConnectionProvider::getInstance();
##	}
##
##	/**
##	 * Use this method to get the instance of the manager
##	 * @return \\$nsp"."$manager_classname
##	 */
##	public static function getInstance(){
##		if(! isset(static::\$instance)) {
##			static::\$instance = new static;
##		}
##		return static::\$instance;
##	}
##
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

/** ***************************************************************** */
/**                                                                   */
/** GET AND EXISTS OBJECT                                             */
/**                                                                   */
/** ***************************************************************** */
if($multi_pk){
	$mpk_id = '$'.join(', $', $pk);
##	/**
##	 * Use this method to get a $classname extracted from database
	foreach($pk as $k){
##	 * @param \$$k part of the object identifier (primary key)
	}
##	 * @return \\$nsp"."$classname
##	 */
##	public function get($mpk_id){
##		if(! isset(\$this->stmts['get'])){
	$mpk_where = array();
	foreach($pk as $k){
		$mpk_where[] = "`$k` = :$k";
	}
	$mpk_where = join(' AND ', $mpk_where);
##			\$this->stmts['get'] = \$this->db->prepare('SELECT * FROM `$tablename` WHERE $mpk_where');
##		}
##		\$stmt = \$this->stmts['get'];
##		try{
	foreach($pk as $i => $k){
##			\$stmt->bindValue(':$k', \$$k".$pk_params[$i].");
	}
##			\$stmt->execute();
##			\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC);
##			if(\$datas){
##				return new $classname(\$datas);
##			}
##			return null;
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
##	/**
##	 * Use this method to check if a $classname exists in the database.
##	 * Using this method is faster than get method then check if value is returned.
	foreach($pk as $k){
##	 * @param \$$k part of the object identifier (primary key)
	}
##	 * @return boolean true if a corresponding entry is found, false otherwise
##	 */
##	public function exists($mpk_id){
##		if(! isset(\$this->stmts['exists'])){
##			\$this->stmts['exists'] = \$this->db->prepare('SELECT EXISTS(SELECT 1 FROM `$tablename` WHERE $mpk_where LIMIT 1)');
##		}
##		\$stmt = \$this->stmts['exists'];
##		try{
	foreach($pk as $i => $k){
##			\$stmt->bindValue(':$k', \$$k".$pk_params[$i].");
	}
##			\$stmt->execute();
##			\$datas = \$stmt->fetch(\\PDO::FETCH_NUM);
##			return (bool)\$datas[0];
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
##	/**
##	 * Use this method to delete a $classname using his primary key elements instead of the object itself.
##	 * Using this method could be easier sometimes but does the same than retrieving the object then delete it.
	foreach($pk as $k){
##	 * @param \$$k part of the object identifier (primary key)
	}
##	 */
##	public function deleteByKey($mpk_id){
##		\$object = \$this->get($mpk_id);
##		if(\$object != null){
##			\$this->delete(\$object);
##		}
##		else{
##			throw new ".$manager_classname."Exception('No corresponding object found in the database', 1);
##		}
##	}
##
}
else{
##	/**
##	 * Use this method to get a $classname extracted from database
##	 * @param \$id the object identifier (primary key)
##	 * @return \\$nsp"."$classname
##	 */
##	public function get(\$id){
##		if(! isset(\$this->stmts['get'])){
##			\$this->stmts['get'] = \$this->db->prepare('SELECT * FROM `$tablename` WHERE `$pk` = :$pk');
##		}
##		\$stmt = \$this->stmts['get'];
##		try{
##			\$stmt->bindValue(':$pk', \$id$pk_params);
##			\$stmt->execute();
##			\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC);
##			if(\$datas){
##				return new $classname(\$datas);
##			}
##			return null;
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
##	/**
##	 * Use this method to check if a $classname exists in the database.
##	 * Using this method is faster than get method then check if value is returned.
##	 * @param \$id the object identifier (primary key)
##	 * @return boolean true if a corresponding entry is found, false otherwise
##	 */
##	public function exists(\$id){
##		if(! isset(\$this->stmts['exists'])){
##			\$this->stmts['exists'] = \$this->db->prepare('SELECT EXISTS(SELECT 1 FROM `$tablename` WHERE `$pk` = :$pk LIMIT 1)');
##		}
##		\$stmt = \$this->stmts['exists'];
##		try{
##			\$stmt->bindValue(':$pk', \$id$pk_params);
##			\$stmt->execute();
##			\$datas = \$stmt->fetch(\\PDO::FETCH_NUM);
##			return (bool)\$datas[0];
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
##	/**
##	 * Use this method to delete a $classname using his primary key instead of the object itself.
##	 * Using this method could be easier sometimes but does the same than retrieving the object then delete it.
##	 * @param \$id the object identifier (primary key)
##	 */
##	public function deleteByKey(\$id){
##		\$object = \$this->get(\$id);
##		if(\$object != null){
##			\$this->delete(\$object);
##		}
##		else{
##			throw new ".$manager_classname."Exception('No corresponding object found in the database', 1);
##		}
##	}
##
}
/** ***************************************************************** */
/**                                                                   */
/** GET OBJECT BY UNIQUE ATTRIBUTE                                    */
/**                                                                   */
/** ***************************************************************** */
if($hasUniqueAttributes){
	foreach($datas['attributes'] as $attribute => $infos){
		if((($infos['type'] == 'value') || ($infos['type'] == "refValue")) && ($infos['unique'] || ($infos['primary_key'] && (! $multi_pk)))){
			$params = $this->getPDOParams($infos['datatype']);
##	/**
##	 * getBy".$this->camelize($attribute)." method
##	 * @param \$value a value of $attribute (defined as unique or primary key)
##	 * @return \\$nsp"."$classname
##	 */
##	public function getBy".$this->camelize($attribute)."(\$value){
##		if(! isset(\$this->stmts['get_by_$attribute'])){
##			\$this->stmts['get_by_$attribute'] = \$this->db->prepare('SELECT * FROM `$tablename` WHERE `$attribute` = :value');
##		}
##		\$stmt = \$this->stmts['get_by_$attribute'];
##		try{
##			\$stmt->bindValue(':value', \$value$params);
##			\$stmt->execute();
##			\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC);
##			if(\$datas){
##				return new $classname(\$datas);
##			}
##			return null;
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** EXISTS BY UNIQUE ATTRIBUTE                                        */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * existsBy".$this->camelize($attribute)." method
##	 * Using this method is faster than using corresponding get method then check if a value is returned.
##	 * @param \$value a value of $attribute (defined as unique or primary key)
##	 * @return boolean true if a corresponding entry is found, false otherwise
##	 */
##	public function existsBy".$this->camelize($attribute)."(\$value){
##		if(! isset(\$this->stmts['exists_by_$attribute'])){
##			\$this->stmts['exists_by_$attribute'] = \$this->db->prepare('SELECT EXISTS(SELECT 1 FROM `$tablename` WHERE `$attribute` = :value LIMIT 1)');
##		}
##		\$stmt = \$this->stmts['exists_by_$attribute'];
##		try{
##			\$stmt->bindValue(':value', \$value$params);
##			\$stmt->execute();
##			\$datas = \$stmt->fetch(\\PDO::FETCH_NUM);
##			return \$datas[0];
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
		}
	}
}
/** ***************************************************************** */
/**                                                                   */
/** ADD OBJECT                                                        */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Inserts a new object in the database.
##	 * Related objects are added or modified according the object attributes
##	 * @param \$object a $classname object
##	 * @param \$cascade this parameter should not be used. It exists for internal purpose only.
if($multi_pk){
##	 * @return array - object identifiers array (values of the primary key)
}
else{
##	 * @return ".$pk_infos['datatype']['type']." object identifier (primary key)
}
##	 */
##	public function add($classname \$object, \$cascade = false){
##		if(! \$object->_isNew()){
##			throw new ".$manager_classname."Exception('This object is extracted from the database or has already been save. Perhaps you should use update method instead', 1);
##		}
##		if(\$object->_isDeleted()){
##			throw new ".$manager_classname."Exception('This object has been deleted. You cannot add it again', 1);
##		}
##
##		try{
##			if(! \$cascade){
##				\$this->db->beginTransaction();
##			}
foreach($datas['attributes'] as $attribute => $infos){
	if($infos['type'] == "refObject"){
##			\$o = \$object->get".$this->camelize($attribute)."();
##			if(\$o != null && \$o->_isNew()){
##				\$id = \\$nsp".$infos['manager']."::getInstance()->add(\$o, true);
##				if(\$object->get".$this->camelize($infos['ref'])."() == null){
##					\$object->set".$this->camelize($infos['ref'])."(\$id);
##				}
##			}
	}
}
##			if(! isset(\$this->stmts['add'])){
#				\$this->stmts['add'] = \$this->db->prepare('INSERT INTO `$tablename` (
$first = true;
$following_values_stmt_part = "";
$following_bind_values_part = "";
foreach($datas['attributes'] as $attribute => $infos){
	if((($infos['type'] == "value") || ($infos['type'] == "refValue")) && (! $infos['datatype']['auto_increment'])){
		if(! $first){
			#, 
			$following_values_stmt_part .= ", ";
		}
		$first = false;
		#`$attribute`
		$following_values_stmt_part .= ":$attribute";
		$params = $this->getPDOParams($infos['datatype']);
		$following_bind_values_part .= "			\$stmt->bindValue(':$attribute', \$object->get".$this->camelize($attribute)."()$params);\n";
	}
}
##) VALUES ($following_values_stmt_part)');
##			}
if($hasCrossObjectsList){
	foreach($datas['attributes'] as $attribute => $infos){
		if(($infos['type'] == "objectsList") && ($infos['ext_cardinal'] == 'many')){
##			if(! isset(\$this->stmts['add_$attribute'])){
#				\$this->stmts['add_$attribute'] = \$this->db->prepare('INSERT INTO `".$infos['ext_table']."` (`".$infos['ext_my_id']."`, `".$infos['ext_column']."`
				 $following_values_stmt_part = ":oid, :extid";
			foreach($infos['ext_extra_attrs'] as $extra_attr){
				$extra_attr_original_name = substr($extra_attr[0], $extra_attr[1]);
#, `".$extra_attr_original_name."`
				 $following_values_stmt_part .= ", :$extra_attr[0]";
			}
##) VALUES ($following_values_stmt_part)');
##			}
		}
	}
}
##			\$stmt = \$this->stmts['add'];
#$following_bind_values_part
##			\$stmt->execute();
##
##			\$object->_setNew(false);
##
if($multi_pk){
##			\$oid = array();
	foreach($pk as $i => $k){
		if($pk_infos[$i]['datatype']['type'] == 'integer' && $pk_infos[$i]['datatype']['auto_increment']){
##			\$oid[$i] = \$this->db->lastInsertId();
##			\$object->set".$this->camelize($k)."(\$oid[$i]);
		}
		else{
##			\$oid[$i] = \$object->get".$this->camelize($k)."();
		}
	}
}
else{
	if($pk_infos['datatype']['type'] == 'integer' && $pk_infos['datatype']['auto_increment']){
##			\$oid = \$this->db->lastInsertId();
##			\$object->set".$this->camelize($pk)."(\$oid);
	}
	else{
##			\$oid = \$object->get".$this->camelize($pk)."();
	}
}
if($hasObject){
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == "object"){
			$method = 'set'.$this->camelize($infos['ext_my_id']);
##
##			\$o = \$object->get".$this->camelize($attribute)."();
##			if(isset(\$o)){
##				\$manager = \\$nsp".$infos['manager']."::getInstance();
##				\$o->".$method."(\$oid);
##				if(\$o->_isNew()){
##					\$manager->add(\$o, true);
##				}
##				else{
##					\$manager->update(\$o, false, true);
##				}
##			}
		}
	}
}
if($hasObjectsList){
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == "objectsList"){
			$cardinal = $infos['ext_cardinal'];
			if($cardinal == 'one'){
				$method = 'set'.$this->camelize($infos['ext_my_id']);
##
##			\$manager = \\$nsp".$infos['manager']."::getInstance();
##			foreach(\$object->get".$this->camelize($attribute)."() as \$o){
##				\$o->".$method."(\$oid);
##				if(\$o->_isNew()){
##					\$manager->add(\$o, true);
##				}
##				else{
##					\$manager->update(\$o, false, true);
##				}
##			}
##
			}
			else{
				$method = 'add'.$datas['object_name'];
				$params = $this->getPDOParams($infos['ext_datatype']);
##
##			\$manager = \\$nsp".$infos['manager']."::getInstance();
##			\$stmt = \$this->stmts['add_$attribute'];
##			\$stmt->bindValue(':oid', \$oid".$pk_params.");
##			foreach(\$object->get".$this->camelize($attribute)."() as \$o){
##				if(\$o->_isNew()){
					foreach($infos['ext_extra_attrs'] as $extra_attr){
##					\$object->set".$this->camelize($extra_attr[0])."(\$o->get".$this->camelize($extra_attr[0])."());
					}
##					\$o->".$method."(\$object);
##					\$manager->add(\$o, true);
##				}
##				else{
##					\$stmt->bindValue(':extid', \$o->get".$this->camelize($infos['id_attributes'][0])."()".$params.");
					foreach($infos['ext_extra_attrs'] as $extra_attr){
						$extra_params = $this->getPDOParams($datas['attributes'][$extra_attr[0]]['datatype']);
##					\$stmt->bindValue(':$extra_attr[0]', \$o->get".$this->camelize($extra_attr[0])."()".$extra_params.");
					}
##					\$stmt->execute();
##				}
##			}
			}
		}
	}
}
##
##			if(! \$cascade){
##				\$this->db->commit();
##			}
##		}
##		catch(\\Exception \$e){
##			if(! \$cascade){
##				\$this->db->rollBack();
##			}
##			\$object->_setNew(true);
if($multi_pk){
	foreach($pk as $i => $k){
		if($pk_infos[$i]['datatype']['type'] == 'integer' && $pk_infos[$i]['datatype']['auto_increment']){
##			\$object->set".$this->camelize($k)."(\$oid[$i]);
		}
	}
}
else{
	if($pk_infos['datatype']['type'] == 'integer' && $pk_infos['datatype']['auto_increment']){
##			\$object->set".$this->camelize($pk)."(null);
	}
}
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##
if($hasObjectsList){
##		\$object->_reinit(true);
}
##		return \$oid;
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** UPDATE OBJECT                                                     */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Updates an existing object in the database according the modifications done on it.
##	 * Related objects are added or modified according the object attributes.
##	 * @param \$object a $classname object
##	 * @param \$cascade this parameter should not be used. It exists for internal purpose only.
##	 */
##	public function update($classname \$object, \$forceCrossRefsAttributesChecking = false, \$cascade = false){
##		if(\$object->_isNew()){
##			throw new ".$manager_classname."Exception('This object is a new object. Perhaps you should use add method instead', 1);
##		}
##		if(\$object->_isDeleted()){
##			throw new ".$manager_classname."Exception('This object has been deleted. You cannot update it', 1);
##		}
##
if($hasCrossObjectsList && $hasCrossObjectsExtraAttributes){
##		if(\$forceCrossRefsAttributesChecking){
	foreach($datas['attributes'] as $attribute => $infos){
		if(($infos['type'] == "objectsList") && ($infos['ext_cardinal'] == 'many') && (! empty($infos['ext_extra_attrs']))){
##			if(! isset(\$this->stmts['update_$attribute'])){
#				\$this->stmts['update_$attribute'] = \$this->db->prepare('UPDATE `".$infos['ext_table']."` SET 
			$first = true;
			foreach($infos['ext_extra_attrs'] as $extra_attr){
				if(! $first){
#, 
				}
				$first = false;
				$extra_attr_original_name = substr($extra_attr[0], $extra_attr[1]);
#`".$extra_attr_original_name."` = :".$extra_attr[0]."
			}
## WHERE `".$infos['ext_my_id']."` = :oid AND `".$infos['ext_column']."` = :extid');
##			}
		}
	}
##			try{
				//Puisqu'on est dans un un objet qui a une CrossObjectList, normalement il n'a pas de clé primaire multiple
##				\$oid = \$object->get".$this->camelize($pk)."();
##
##				\$this->db->beginTransaction();
##
	foreach($datas['attributes'] as $attribute => $infos){
		if(($infos['type'] == "objectsList") && ($infos['ext_cardinal'] == 'many') && (! empty($infos['ext_extra_attrs']))){
##				\$stmt = \$this->stmts['update_$attribute'];
##				\$stmt->bindValue(':oid', \$oid".$pk_params.");
##				foreach(\$object->getOriginal".$this->camelize($attribute)."() as \$o){
##					if(\$o->_isModified()){
##						\$stmt->bindValue(':extid', \$o->get".$this->camelize($infos['id_attributes'][0])."()".$params.");
						foreach($infos['ext_extra_attrs'] as $extra_attr){
							$extra_params = $this->getPDOParams($datas['attributes'][$extra_attr[0]]['datatype']);
##						\$stmt->bindValue(':$extra_attr[0]', \$o->get".$this->camelize($extra_attr[0])."()".$extra_params.");
						}
##						\$stmt->execute();
##					}
##				}
		}
	}
##
##				\$this->db->commit();
##			}
##			catch(\\Exception \$e){
##				\$this->db->rollBack();
##				throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##			}
##		}
}
##
##		if(\$object->_isModified()){
##			try{
##				if(! \$cascade){
##					\$this->db->beginTransaction();
##				}
##
foreach($datas['attributes'] as $attribute => $infos){
	if($infos['type'] == "refObject"){
##				\$o = \$object->get".$this->camelize($attribute)."();
##				if(\$o != null && \$o->_isNew()){
##					\$id = \\$nsp".$infos['manager']."::getInstance()->add(\$o, true);
##					if(\$object->get".$this->camelize($infos['ref'])."() == null){
##						\$object->set".$this->camelize($infos['ref'])."(\$id);
##					}
##				}
##
	}
}
if($multi_pk){
##				\$oid = array();
	foreach($pk as $i => $k){
##				\$oid[$i] = \$object->get".$this->camelize($k)."();
	}
}
else{
##				\$oid = \$object->get".$this->camelize($pk)."();
}
##
##				if(\$object->_isRenamed()){
if($multi_pk){
##					\$ooid = array();
	foreach($pk as $i => $k){
##					\$ooid[$i] = \$object->getOriginal".$this->camelize($k)."();
	}
}
else{
##					\$ooid = \$object->getOriginal".$this->camelize($pk)."();
}
##					if(! isset(\$this->stmts['rename'])){
#						\$this->stmts['rename'] = \$this->db->prepare('UPDATE `$tablename` SET 
$first = true;
$following = "";
foreach($datas['attributes'] as $attribute => $infos){
	if((($infos['type'] == "value") || ($infos['type'] == "refValue")) && ($infos['primary_key'])){
		if(! $first){
			#, 
		}
		$first = false;
		#`$attribute` = :n_$attribute
		$params = $this->getPDOParams($infos['datatype']);
		$following .= "					\$stmt->bindValue(':n_$attribute', \$object->get".$this->camelize($attribute)."()$params);\n";
	}
}
if($multi_pk){
	foreach($pk as $i => $k){
		$following .= "					\$stmt->bindValue(':$k', \$ooid[$i]".$pk_params[$i].");\n";
	}
## WHERE $mpk_where');
}
else{
	$following .= "					\$stmt->bindValue(':$pk', \$ooid$pk_params);\n";
## WHERE `$pk` = :$pk');
}
##					}
##					\$stmt = \$this->stmts['rename'];
##$following
##					\$stmt->execute();
##					\$object->_setRenamed(false);
##				}
##
##				if(! isset(\$this->stmts['update'])){
#					\$this->stmts['update'] = \$this->db->prepare('UPDATE `$tablename` SET 
$first = true;
$following = "";
foreach($datas['attributes'] as $attribute => $infos){
	if((($infos['type'] == "value") || ($infos['type'] == "refValue")) && (! $infos['primary_key'])){
		if(! $first){
			#, 
		}
		$first = false;
		#`$attribute` = :$attribute
		$params = $this->getPDOParams($infos['datatype']);
		$following .= "				\$stmt->bindValue(':$attribute', \$object->get".$this->camelize($attribute)."()$params);\n";
	}
}
if($multi_pk){
	foreach($pk as $i => $k){
		$following .= "				\$stmt->bindValue(':$k', \$oid[$i]".$pk_params[$i].");\n";
	}
## WHERE $mpk_where');
}
else{
	$following .= "				\$stmt->bindValue(':$pk', \$oid$pk_params);\n";
## WHERE `$pk` = :$pk');
}
##				}
if($hasCrossObjectsList){
	foreach($datas['attributes'] as $attribute => $infos){
		if(($infos['type'] == "objectsList") && ($infos['ext_cardinal'] == 'many')){
##				if(! isset(\$this->stmts['add_$attribute'])){
#					\$this->stmts['add_$attribute'] = \$this->db->prepare('INSERT INTO `".$infos['ext_table']."` (`".$infos['ext_my_id']."`, `".$infos['ext_column']."`
			$following_values_stmt_part = ":oid, :extid";
			foreach($infos['ext_extra_attrs'] as $extra_attr){
				$extra_attr_original_name = substr($extra_attr[0], $extra_attr[1]);
#, `".$extra_attr_original_name."`
				$following_values_stmt_part .= ", :$extra_attr[0]";
			}
##) VALUES ($following_values_stmt_part)');
##				}
##				if(! isset(\$this->stmts['del_$attribute'])){
##					\$this->stmts['del_$attribute'] = \$this->db->prepare('DELETE FROM `".$infos['ext_table']."` WHERE `".$infos['ext_my_id']."` = :oid AND `".$infos['ext_column']."` = :extid');
##				}
		}
	}
}
##
##				\$stmt = \$this->stmts['update'];
##$following
##				\$stmt->execute();
if($hasObject){
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == "object"){
			$method = 'set'.$this->camelize($infos['ext_my_id']);
##
##				\$o = \$object->get".$this->camelize($attribute)."();
##				\$manager = \\$nsp".$infos['manager']."::getInstance();
##				if(isset(\$o)){
##					\$o->".$method."(\$oid);
##					if(\$o->_isNew()){
##						\$manager->add(\$o, true);
##					}
##					else{
##						\$manager->update(\$o, false, true);
##					}
##				}
##				else{
##					\$o = \$manager->getBy".$this->camelize($infos['ext_my_id'])."(\$oid);
##					if(isset(\$o)){
					if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
##						\$manager->delete(\$o, true);
					}
					else{
						if(isset($infos['datatype']['default'])){
##						\$o->".$method."(\"".$infos['datatype']['default']."\");
						}
						else{
##						\$o->".$method."(null);
						}
##						\$manager->update(\$o, false, true);
					}
##					}
##				}
		}
	}
}
if($hasObjectsList){
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == "objectsList"){
			$cardinal = $infos['ext_cardinal'];
			if($cardinal == 'one'){
				$method = 'set'.$this->camelize($infos['ext_my_id']);
##
##				\$manager = \\$nsp".$infos['manager']."::getInstance();
##				foreach(\$object->getDeleted".$this->camelize($attribute)."() as \$o){
				if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
##					\$manager->delete(\$o, true);
				}
				else{
					if(isset($infos['datatype']['default'])){
##					\$o->".$method."(\"".$infos['datatype']['default']."\");
					}
					else{
##					\$o->".$method."(null);
					}
##					\$manager->update(\$o, false, true);
				}
##				}
##				foreach(\$object->getAdded".$this->camelize($attribute)."() as \$o){
##					\$o->".$method."(\$oid);
##					if(\$o->_isNew()){
##						\$manager->add(\$o, true);
##					}
##					else{
##						\$manager->update(\$o, false, true);
##					}
##				}
			}
			else{
				$method = 'add'.$datas['object_name'];
				$params = $this->getPDOParams($infos['ext_datatype']);
##
##				\$stmt = \$this->stmts['del_$attribute'];
##				\$stmt->bindValue(':oid', \$oid".$pk_params.");
##				foreach(\$object->getDeleted".$this->camelize($attribute)."() as \$o){
##					\$stmt->bindValue(':extid', \$o->get".$this->camelize($infos['id_attributes'][0])."()".$params.");
##					\$stmt->execute();
##				}
##				\$manager = \\$nsp".$infos['manager']."::getInstance();
##				\$stmt = \$this->stmts['add_$attribute'];
##				\$stmt->bindValue(':oid', \$oid".$pk_params.");
##				foreach(\$object->getAdded".$this->camelize($attribute)."() as \$o){
##					if(\$o->_isNew()){
						foreach($infos['ext_extra_attrs'] as $extra_attr){
##						\$object->set".$this->camelize($extra_attr[0])."(\$o->get".$this->camelize($extra_attr[0])."());
						}
##						\$o->".$method."(\$object);
##						\$manager->add(\$o, true);
##					}
##					else{
##						\$stmt->bindValue(':extid', \$o->get".$this->camelize($infos['id_attributes'][0])."()".$params.");
						foreach($infos['ext_extra_attrs'] as $extra_attr){
							$extra_params = $this->getPDOParams($datas['attributes'][$extra_attr[0]]['datatype']);
##						\$stmt->bindValue(':$extra_attr[0]', \$o->get".$this->camelize($extra_attr[0])."()".$extra_params.");
						}
##						\$stmt->execute();
##					}
##				}
			}
		}
	}
}
##				if(! \$cascade){
##					\$this->db->commit();
##				}
##			}
##			catch(\\Exception \$e){
##				if(! \$cascade){
##					\$this->db->rollBack();
##				}
##				throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##			}
##
if($hasObjectsList){
##			\$object->_reinit(false);
}
##			\$object->_setModified(false);
##		}
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** DELETE OBJECT                                                     */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Deletes an existing object in the database.
##	 * Related objects are modified according this deletion, but are not deleted.
##	 * @param \$object a $classname object
##	 * @param \$cascade this parameter should not be used. It exists for internal purpose only.
##	 */
##	public function delete($classname \$object, \$cascade = false){
##		if(\$object->_isNew()){
##			throw new ".$manager_classname."Exception('This object is a new object. Cannot delete it from database', 1);
##		}
##		if(\$object->_isDeleted()){
##			throw new ".$manager_classname."Exception('This object has already been deleted. You cannot delete it again', 1);
##		}
##
##		try{
##			if(! \$cascade){
##				\$this->db->beginTransaction();
##			}
##
if($hasObject){
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == "object"){
			$method = 'set'.$this->camelize($infos['ext_my_id']);
##
##			\$manager = \\$nsp".$infos['manager']."::getInstance();
##			\$o = \$object->get".$this->camelize($attribute)."();
##			if(isset(\$o)){
##				if(! \$o->_isNew()){
			if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
##					\$manager->delete(\$o, true);
			}
			else{
				if(isset($infos['ext_default'])){
##					\$o->".$method."(\"".$infos['ext_default']."\");
				}
				else{
##					\$o->".$method."(null);
				}
##					\$manager->update(\$o, false, true);
			}
##				}
##			}
##			else{
##				\$o = \$manager->getBy".$this->camelize($infos['ext_my_id'])."(\$object->get".$this->camelize($pk)."());
##				if(isset(\$o)){
			if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
##					\$manager->delete(\$o, true);
			}
			else{
				if(isset($infos['ext_default'])){
##					\$o->".$method."(\"".$infos['ext_default']."\");
				}
				else{
##					\$o->".$method."(null);
				}
##					\$manager->update(\$o, false, true);
			}
##				}
##			}
		}
	}
}
if($hasObjectsList){
	foreach($datas['attributes'] as $attribute => $infos){
		if($infos['type'] == "objectsList"){
			$cardinal = $infos['ext_cardinal'];
			if($cardinal == 'one'){
				$method = 'set'.$this->camelize($infos['ext_my_id']);
##
##			\$manager = \\$nsp".$infos['manager']."::getInstance();
##			foreach(\$object->get".$this->camelize($attribute)."() as \$o){
##				if(! \$o->_isNew()){
				if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
##					\$manager->delete(\$o, true);
				}
				else{
					if(isset($infos['ext_default'])){
##					\$o->".$method."(\"".$infos['ext_default']."\");
					}
					else{
##					\$o->".$method."(null);
					}
##					\$manager->update(\$o, false, true);
				}
##				}
##			}
##			foreach(\$object->getDeleted".$this->camelize($attribute)."() as \$o){
				if(in_array($infos['ext_my_id'], $infos['id_attributes']) || $infos['delete_cascade']){
##				\$manager->delete(\$o, true);
				}
				else{
					if(isset($infos['ext_default'])){
##				\$o->".$method."(\"".$infos['ext_default']."\");
					}
					else{
##				\$o->".$method."(null);
					}
##				\$manager->update(\$o, false, true);
				}
##			}
			}
			else{
				$method = 'delete'.$datas['object_name'];
				$params = $this->getPDOParams($infos['ext_datatype']);
##
##			\$manager = \\$nsp".$infos['manager']."::getInstance();
##			foreach(\$object->get".$this->camelize($attribute)."() as \$o){
##				\$o->".$method."(\$object);
##				if(! \$o->_isNew()){
##					\$manager->update(\$o, false, true);
##				}
##			}
			}
		}
	}
}
##
##			if(! isset(\$this->stmts['delete'])){
if($multi_pk){
##				\$this->stmts['delete'] = \$this->db->prepare('DELETE FROM `$tablename` WHERE $mpk_where');
}
else{
##				\$this->stmts['delete'] = \$this->db->prepare('DELETE FROM `$tablename` WHERE `$pk` = :$pk');
}
##			}
if($multi_pk){
	foreach($pk as $i => $k){
##			\$this->stmts['delete']->bindValue(':$k', \$object->get".$this->camelize($k)."()".$pk_params[$i].");
	}
}
else{
##			\$this->stmts['delete']->bindValue(':$pk', \$object->get".$this->camelize($pk)."()$pk_params);
}
##			\$this->stmts['delete']->execute();
##
##			if(! \$cascade){
##				\$this->db->commit();
##			}
##		}
##		catch(\\Exception \$e){
##			if(! \$cascade){
##				\$this->db->rollBack();
##			}
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##
##		\$object->_setDeleted(true);
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** COUNT OBJECTS                                                     */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Get the number of objects in the database.
##	 * @return integer - the number of $classname objects.
##	 */
##	public function count(){
##		if(! isset(\$this->stmts['count'])){
##			\$this->stmts['count'] = \$this->db->prepare('SELECT COUNT(*) FROM `$tablename`');
##		}
##		\$stmt = \$this->stmts['count'];
##		try{
##			\$stmt->execute();
##			\$datas = \$stmt->fetch(\\PDO::FETCH_NUM);
##			return \$datas[0];
##		}
##		catch(\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** LIST ALL OBJECTS                                                  */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Get a set of existing objects in the database.
##	 * @param \$sortAttributes (optional) could be a single string or an array of string (or an associative array of string with order) corresponding to the columns names where datas are stored. Could be null. Default null.
##	 * @param \$offset (optional) an integer value : use it together whith \$limit param to extract a subset of objects stored. If null, all objects are returned. Default null.
##	 * @param \$limit (optional) an integer value : use it together whith \$offset param to extract a subset of objects stored. If null, all objects are returned. Default null.
##	 * @param \$asArray (optional) boolean : if true the return is a 2 dimensions array containing datas in the corresponding table ($tablename). Default false.
##	 * @return \\$nsp"."$classname"."[] - an array of $classname : the objects found in storage according \$offset and \$limit parameters passed. If \$asArray is set to true, returns a 2 dimensions array containing datas of these objects.
##	 * @example
##	 *		getList('name');  // assume order is ASC
##	 *		getList(array('name', 'firstname'));  // assume order is ASC for both name and firstname
##	 *		getList(array('name' => 'ASC', 'register_date' => 'DESC'));  // last registered first
##	 */
##	public function getList(\$sortAttributes = null, \$offset = null, \$limit = null, \$asArray = false){
##		\$objects = array();
##		\$order_string = '';
##		\$limit_string = '';
##		\$stmt = '';
##
##		try{
##			if( (! isset(\$sortAttributes)) && ( ! (isset(\$offset) && isset(\$limit)))){
##				if(! isset(\$this->stmts['list'])){
##					\$this->stmts['list'] = \$this->db->prepare('SELECT * FROM `$tablename`');
##				}
##				\$stmt = \$this->stmts['list'];
##				\$stmt->execute();
##			}
##			else{
##				if(isset(\$offset) && isset(\$limit)){
##					\$limit_string = ' LIMIT :offset, :limit';
##				}
##				if(isset(\$sortAttributes)){
##					if(is_array(\$sortAttributes)){
##						if(array_values(\$sortAttributes) === \$sortAttributes){	//it's not an associative array (just column names as values), assume order is ASC
##							\$first = true;
##							foreach(\$sortAttributes as \$attr){
##								if(in_array(\$attr, self::\$attributesList)){
##									if(! \$first){
##										\$order_string .= ',';
##									}
##									\$first = false;
##									\$order_string .= \"`\$attr`\";
##								}
##							}
##						}
##						else{
##							\$first = true;
##							foreach(\$sortAttributes as \$attr => \$order){
##								if(in_array(\$attr, self::\$attributesList) && (\$order == 'ASC' || \$order == 'DESC')){
##									if(! \$first){
##										\$order_string .= ',';
##									}
##									\$first = false;
##									\$order_string .= \"`\$attr` \$order\";
##								}
##							}
##						}
##						if(\$order_string != ''){
##							\$order_string = ' ORDER BY '.\$order_string;
##						}
##					}
##					elseif(in_array(\$sortAttributes, self::\$attributesList)){
##						\$order_string = \" ORDER BY `\$sortAttributes`\";
##					}
##				}
##				\$sql = \"SELECT * FROM `$tablename`\$order_string\$limit_string\";
##				\$md5 = md5(\$sql);
##				if(!isset(\$this->stmts[\$md5])){
##					\$this->stmts[\$md5] = \$this->db->prepare(\$sql);
##				}
##				\$stmt = \$this->stmts[\$md5];
##
##				if(\$limit_string != ''){
##					\$stmt->bindValue(':offset', \$offset, \\PDO::PARAM_INT);
##					\$stmt->bindValue(':limit', \$limit, \\PDO::PARAM_INT);
##				}
##				\$stmt->execute();
##			}
##
##			if(\$asArray){
##				\$objects = \$stmt->fetchAll(\\PDO::FETCH_ASSOC);
##			}
##			else{
##				while (\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC)){
##					\$objects[] = new $classname(\$datas);
##				}
##			}
##			return \$objects;
##		}
##		catch(\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
/** ***************************************************************** */
/**                                                                   */
/** LIST OBJECTS WITH FILTER                                          */
/**                                                                   */
/** ***************************************************************** */
if($hasNonUniqueAttributes || $hasCrossObjectsList){
##
##	/**
##	 * Get a set of existing objects in the database according columns are equals to specified values.
##	 * @param \$attVals an associative array with column names on which we want to test the values as keys and value for which we want to keep the objects as values. The array keys of the parameter cannot be columns defined as (single)unique or (single)primary-key.
##	 * @param \$sortAttributes (optional) could be a single string or an array of string (or an associative array of string with order) corresponding to the columns names where datas are stored. Could be null. Default null.
##	 * @param \$offset (optional) an integer value : use it together whith \$limit param to extract a subset of objects stored. If null, all objects are returned. Default null.
##	 * @param \$limit (optional) an integer value : use it together whith \$offset param to extract a subset of objects stored. If null, all objects are returned. Default null.
##	 * @param \$asArray (optional) boolean : if true the return is a 2 dimensions array containing datas in the corresponding table ($tablename). Default false.
##	 * @return \\$nsp"."$classname"."[] - an array of $classname : the objects found in storage according \$offset and \$limit parameters passed. If \$asArray is set to true, returns a 2 dimensions array containing datas of these objects.
##	 */
##	public function getFilteredList(\$attVals, \$sortAttributes = null, \$offset = null, \$limit = null, \$asArray = false){
##		\$objects = array();
##		\$order_string = '';
##		\$limit_string = '';
##		\$stmt = '';
##
##		try{
##			foreach(array_keys(\$attVals) as \$attribute){
	if($hasUniqueAttributes && $hasNonUniqueAttributes){
##				if(in_array(\$attribute, self::\$uniqueAttributesList)){
##					throw new ".$manager_classname."Exception(\"Cannot filter on \$attribute. This attribute is defined as unique or primary key. You should use getBy attribute method instead to get the unique object for this value\", 1);
##				}
		
	}
	if($hasCrossObjectsList){
##				if(! (in_array(\$attribute, self::\$attributesList) || in_array(\$attribute, self::\$extAttributesList))){
	}
	else{
##				if(! in_array(\$attribute, self::\$attributesList)){
	}
##					throw new ".$manager_classname."Exception(\"Cannot filter on \$attribute. Unknown column\", 1);
##				}
##			}
##
##
##			if(isset(\$offset) && isset(\$limit)){
##				\$limit_string = ' LIMIT :offset, :limit';
##			}
##			if(isset(\$sortAttributes)){
##				if(is_array(\$sortAttributes)){
##					if(array_values(\$sortAttributes) === \$sortAttributes){	//it's not an associative array (just column names as values), assume order is ASC
##						\$first = true;
##						foreach(\$sortAttributes as \$attr){
##							if(in_array(\$attr, self::\$attributesList)){
##								if(! \$first){
##									\$order_string .= ',';
##								}
##								\$first = false;
##								\$order_string .= \"`\$attr`\";
##							}
##						}
##					}
##					else{
##						\$first = true;
##						foreach(\$sortAttributes as \$attr => \$order){
##							if(in_array(\$attr, self::\$attributesList) && (\$order == 'ASC' || \$order == 'DESC')){
##								if(! \$first){
##									\$order_string .= ',';
##								}
##								\$first = false;
##								\$order_string .= \"`\$attr` \$order\";
##							}
##						}
##					}
##					if(\$order_string != ''){
##						\$order_string = ' ORDER BY '.\$order_string;
##					}
##				}
##				elseif(in_array(\$sortAttributes, self::\$attributesList)){
##					\$order_string = \" ORDER BY `\$sortAttributes`\";
##				}
##			}
##
##			\$select = \"SELECT `$tablename`.*\";
##			\$from = \"FROM `$tablename`\";
##			\$conditions = array();
	$else = "";
	if($hasCrossObjectsList){
##			foreach(array_keys(\$attVals) as \$attribute){		
		foreach($datas['attributes'] as $attribute => $infos){
			if(($infos['type'] == 'objectsList') && ($infos['ext_cardinal'] == 'many')){
##				".$else."if(\$attribute == '".$infos['ext_column']."'){
				foreach($infos['ext_extra_attrs'] as $extra_attr){
					$extra_attr_original_name = substr($extra_attr[0], $extra_attr[1]);
##					\$select .= \", `".$infos['ext_table']."`.`$extra_attr_original_name` `$extra_attr[0]`\";
				}
##					\$from .= \" INNER JOIN `".$infos['ext_table']."` ON `$tablename`.`$pk` = `".$infos['ext_table']."`.`".$infos['ext_my_id']."`\";
##					\$conditions[] = \"`".$infos['ext_table']."`.`".$infos['ext_column']."` = :val_\$attribute\";
##				}
				$else = "else";
			}
		}
##				else{
##					\$conditions[] = \"`\$attribute` = :val_\$attribute\";
##				}
##			}
	}
	else{
##			foreach(array_keys(\$attVals) as \$attribute){
##				\$conditions[] = \"`\$attribute` = :val_\$attribute\";
##			}
	}
##			\$sql = \$select.\" \".\$from.\" WHERE \".join(' AND ', \$conditions).\"\$order_string\$limit_string\";
##
##			\$md5 = md5(\$sql);
##			if(!isset(\$this->stmts[\$md5])){
##				\$this->stmts[\$md5] = \$this->db->prepare(\$sql);
##			}
##			\$stmt = \$this->stmts[\$md5];
##
##			if(\$limit_string != ''){
##				\$stmt->bindValue(':offset', \$offset, \\PDO::PARAM_INT);
##				\$stmt->bindValue(':limit', \$limit, \\PDO::PARAM_INT);
##			}
##
##			foreach(\$attVals as \$attribute => \$value){
	$else = "";
	foreach($datas['attributes'] as $attribute => $infos){
		if((($infos['type'] == 'value' || $infos['type'] == 'refValue') && (! ($infos['unique'] || ($infos['primary_key'] && (count($datas['primary_key']) == 1 )))))  || (($infos['type'] == 'objectsList') && ($infos['ext_cardinal'] == 'many'))){
			if(($infos['type'] == 'objectsList') && ($infos['ext_cardinal'] == 'many')){
##				".$else."if(\$attribute == '".$infos['ext_column']."'){
				$datatype = $infos['ext_datatype'];
				$params = $this->getPDOParams($datatype);
##					\$stmt->bindValue(':val_".$infos['ext_column']."', \$value$params);
##				}
			}
			else{
##				".$else."if(\$attribute == '$attribute'){
				$datatype = $infos['datatype'];
				$params = $this->getPDOParams($datatype);
##					\$stmt->bindValue(':val_$attribute', \$value$params);
##				}
			}
			$else = "else";
		}
	}
##			}
##
##			\$stmt->execute();
##
##			if(\$asArray){
##				\$objects = \$stmt->fetchAll(\\PDO::FETCH_ASSOC);
##			}
##			else{
##				while (\$datas = \$stmt->fetch(\\PDO::FETCH_ASSOC)){
##					\$objects[] = new $classname(\$datas);
##				}
##			}
##			return \$objects;
##		}
##		catch(\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(),2, \$e);
##		}
##	}
}
##}
#?>
