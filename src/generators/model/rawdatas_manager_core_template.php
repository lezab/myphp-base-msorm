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
##use \\$nsp"."exceptions\\RawDatasManagerException;
##
##/**
## * @class RawDatasManagerCore : a class for managing datas in the database.
## * Unlike all other Core manager classes this manager doesn't deal with objects but only with datas of any table of the schema as described
## * in the xml file used for project generation, including crossref tables.
## * This could already be done in any other manager of the project, but for clarity reasons, it's better to use this manager any time you 
## * have to do specific query's implying multiple tables (data maintenance, weird things, raw datas extractions ...)
## * Like all other CoreManager classes, the class is a singleton an should not be instanciate.
## * The right way to deal with this class is to call the getInstance method on the subclass RawDatasManager
## * Ex :
## * \$manager = RawDatasManager::getInstance();
## * \$datas = \$manager->select('tablename',array('columname', 'value'));
## * ...
## */
##class RawDatasManagerCore {
##	
##	// Class attributes for object management
##	protected \$db;
##	protected static \$instance;
##
#	protected static \$tables = array(
$first = true;
foreach(array_keys($raw_db_model) as $tablename){
	if(! $first){
		#, 
	}
	#'$tablename'
	$first = false;
}
##);
##	protected static \$columns = array(
$first = true;
foreach($raw_db_model as $tablename => $datas){
	if(! $first){
		##, 
	}
	#		'$tablename' => array(
	$first_column = true;
	foreach(array_keys($datas['columns']) as $column_name){
		if(! $first_column){
			#, 
		}
		#'$column_name'
		$first_column = false;
	}
	#)
	$first = false;
}
##
##	);
##	
##	protected \$stmts = array();
##	
##	protected const OPERATORS = array(
##		'>' => array(
##			'format' => '> ?',
##			'parametrized' => true
##		),
##		'>=' => array(
##			'format' => '>= ?',
##			'parametrized' => true
##		),
##		'<' => array(
##			'format' => '< ?',
##			'parametrized' => true
##		),
##		'<=' => array(
##			'format' => '<= ?',
##			'parametrized' => true
##		),
##		'!=' => array(
##			'format' => '!= ?',
##			'parametrized' => true
##		),
##		'LIKE' => array(
##			'format' => 'LIKE ?',
##			'parametrized' => true
##		),
##		'NOT LIKE' => array(
##			'format' => 'NOT LIKE ?',
##			'parametrized' => true
##		),
##		'IN' => array(
##			'format' => 'IN (%s)',
##			'parametrized' => true
##		),
##		'NOT IN' => array(
##			'format' => 'NOT IN (%s)',
##			'parametrized' => true
##		),
##		'BETWEEN' => array(
##			'format' => 'BETWEEN ? AND ?',
##			'parametrized' => true
##		),
##		'NOT BETWEEN' => array(
##			'format' => 'NOT BETWEEN ? AND ?',
##			'parametrized' => true
##		),
##		'IS NULL' => array(
##			'format' => 'IS NULL',
##			'parametrized' => false
##		),
##		'IS NOT NULL' => array(
##			'format' => 'IS NOT NULL',
##			'parametrized' => false
##		)
##	);
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
##	 * @return \\$nsp"."RawDatasManager
##	 */
##	public static function getInstance(){
##		if(! isset(static::\$instance)) {
##			static::\$instance = new static;
##		}
##		return static::\$instance;
##	}
##
##	/**
##	 * @param string \$query an sql query with '?' if parametrized
##	 * @param array \$params values of query parameters. The number of elements should match the number of '?' in the query
##	 *
##	 * @return mixed if query is an sql SELECT query returns an associated array of all the rows in the result. If not, returns the result of PDO::execute method
##	 */
##	public function query(\$query, \$params = array()) {
##		try{
##			\$md5 = md5(\$query);
##			if(!isset(\$this->stmts[\$md5])){
##				\$this->stmts[\$md5] = \$this->db->prepare(\$query);
##			}
##			\$stmt = \$this->stmts[\$md5];
##			
##			if (empty(\$params))	{
##				\$result = \$stmt->execute();
##			}
##			else {
##				\$result = \$stmt->execute(\$params);
##			}
##			if (strpos(\$query, 'SELECT') !== false) {
##				return \$result->fetchAll(\PDO::FETCH_ASSOC);
##			}
##			return \$result;
##		}
##		catch(\Exception \$e){
##			throw new RawDatasManagerException(\$e->getMessage(), 2, \$e);
##		}
##	}
##	
##	/**
##	 * 
##	 * @param string \$table the table name on which we want to retrieve datas
##	 * @param array \$where an associative array which define condition for datas to be retrieved
##	 * Ex :
##	 *	array('column_name1' => \$value1, 'column_name2' => \$value2) // assume operator is 'equals'
##	 *  array('column_name1' => array('>', \$value1))
##	 * @param integer \$offset optional
##	 * @param integer \$limit optional
##	 * @param array \$order optional. If not an associative array, assume all values in the array are column names and order is ASC. Else, keys are column names and values are order (ASC or DESC)
##	 * Ex :
##	 *	array('column_name1', 'column_name2') // assume order is ASC
##	 *  array('column_name1' => 'DESC', 'column_name2' => 'ASC')
##	 * @param string \$fields the columns of table to retrieve. default is '*'
##	 * @return array an associative array according the query
##	 * @throws RawDatasManagerException
##	 */
##	public function select(\$table, \$where = array(), \$offset = null, \$limit = null, \$order = null, \$fields = '*') {
##		if(in_array(\$table, self::\$tables)){
##			try {
##				if (is_array(\$fields)) {
##					\$selectfields = array();
##					foreach (\$fields as \$field) {
##						if(!in_array(\$field, self::\$columns[\$table])){
##							throw new RawDatasManagerException(\"Column \$field is not part of the \$table table\", 1);
##						}
##						\$selectfields[] = \"`\$field`\";
##					}
##					\$fieldList = implode(', ', \$selectfields);
##				}
##				else {
##					\$fieldList = '*';
##				}
##				\$sqlWhere = \"\";
##				\$sqlLimit = \"\";
##				\$sqlOrder = \"\";
##				\$values = array();
##				if(is_array(\$where) && !empty(\$where)){
##					list(\$sqlWhere, \$values) = \$this->processWhere(\$where);
##				}
##				if(! (\$offset == null && \$limit == null)){
##					if(\$offset >=0 && \$limit >= 0) {
##						\$sqlLimit = ' LIMIT :offset, :limit';
##						\$values[] = \$offset;
##						\$values[] = \$limit;
##					}
##					else{
##						throw new RawDatasManagerException(\"offset and limit cannot be negative values\", 1);
##					}
##				}
##				if(isset(\$order)){
##					\$sqlOrder = \$this->processOrder(\$order, \$table);
##				}
##				\$sql = \"SELECT \$fieldList FROM `\$table`\".\$sqlWhere.\$sqlLimit.\$sqlOrder;
##				
##				\$md5 = md5(\$sql);
##				if(!isset(\$this->stmts[\$md5])){
##					\$this->stmts[\$md5] = \$this->db->prepare(\$sql);
##				}
##				\$stmt = \$this->stmts[\$md5];
##				\$this->bindValues(\$stmt, \$values);
##				\$stmt->execute();
##				
##				return \$stmt->fetchAll(\PDO::FETCH_ASSOC);
##			}
##			catch(RawDatasManagerException \$rdme) {
##				throw \$rdme;
##			}
##			catch(\Exception \$e) {
##				throw new RawDatasManagerException(\$e->getMessage(), 2, \$e);
##			}
##		}
##		else{
##			throw new RawDatasManagerException(\"Table \$table is not part of the model\", 1);
##		}
##	}
##
##	/**
##	 * 
##	 * @param string \$table the table name on which we want to insert datas
##	 * @param array \$record an associative array containing values to insert
##	 * Ex :
##	 *  array('column_name1' => 'value_1', 'column_name2' => 'value_2')
##	 * @throws RawDatasManagerException
##	 */
##	public function insert(\$table, \$record) {
##		if(in_array(\$table, self::\$tables)){
##			try {
##				\$sql = \"INSERT INTO `\$table`\";
##				\$fields = array();
##				\$values = array();
##				\$stmt_vars = array();
##				foreach (\$record as \$field => \$value) {
##					if(in_array(\$field, self::\$columns[\$table])){
##						\$fields[] = \"`\$field`\";
##						\$stmt_vars[] = '?';
##						\$values[] = \$value;
##					}
##					else{
##						throw new RawDatasManagerException(\"Column \$field is not part of the \$table table\", 1);
##					}
##				}
##				\$sql .= \" (\".implode(', ', \$fields).\") VALUES (\".implode(', ', \$stmt_vars).\")\";
##				\$md5 = md5(\$sql);
##				if(!isset(\$this->stmts[\$md5])){
##					\$this->stmts[\$md5] = \$this->db->prepare(\$sql);
##				}
##				\$stmt = \$this->stmts[\$md5];
##				\$this->bindValues(\$stmt, \$values);
##				\$stmt->execute();
##			}
##			catch(RawDatasManagerException \$rdme) {
##				throw \$rdme;
##			}
##			catch(\Exception \$e) {
##				throw new RawDatasManagerException(\$e->getMessage(), 2, \$e);
##			}
##		}
##		else{
##			throw new RawDatasManagerException(\"Table \$table is not part of the model\", 1);
##		}
##	}
##	
##	/**
##	 * 
##	 * @param string \$table the table name on which we want to insert datas
##	 * @param array \$fields an array containing the column names on wich we are going to give the values
##	 * Ex :
##	 *  array('column_name1', 'column_name2')
##	 * @param array \$sets an array of arrays containing values of each sets we want to insert
##	 *  array(array('value_11', 'value_12'), array('value_21', 'value_22'), array('value_31', 'value_32'))
##	 * @throws RawDatasManagerException
##	 */
##	public function insertMultiple(\$table, \$fields, \$sets) {
##		if(in_array(\$table, self::\$tables)){
##			try {
##				\$sql = \"INSERT INTO `\$table`\";
##				\$sql_fields = array();
##				\$stmt_vars = array();
##				foreach (\$fields as \$field) {
##					if(in_array(\$field, self::\$columns[\$table])){
##						\$sql_fields[] = \"`\$field`\";
##						\$stmt_vars[] = '?';
##					}
##					else{
##						throw new RawDatasManagerException(\"Column \$field is not part of the \$table table\", 1);
##					}
##				}
##				\$sql .= \" (\".implode(', ', \$sql_fields).\") VALUES (\".implode(', ', \$stmt_vars).\")\";
##				\$md5 = md5(\$sql);
##				if(!isset(\$this->stmts[\$md5])){
##					\$this->stmts[\$md5] = \$this->db->prepare(\$sql);
##				}
##				\$stmt = \$this->stmts[\$md5];
##				\$this->db->beginTransaction();
##				foreach(\$sets as \$values){
##					if(count(\$values) != count(\$fields)){
##						throw new RawDatasManagerException(\"Set has not the right number of values\", 1);
##					}
##					\$this->bindValues(\$stmt, \$values);
##					\$stmt->execute();
##				}
##				\$this->db->commit();
##			}
##			catch(RawDatasManagerException \$rdme) {
##				throw \$rdme;
##			}
##			catch(\Exception \$e) {
##				\$this->db->rollBack();
##				throw new RawDatasManagerException(\$e->getMessage(), 2, \$e);
##			}
##		}
##		else{
##			throw new RawDatasManagerException(\"Table \$table is not part of the model\", 1);
##		}
##	}
##	
##
##	/**
##	 * 
##	 * @param string \$table the table name on which we want to update datas
##	 * @param array \$record an associative array containing values to updated
##	 * Ex :
##	 *  array('column_name1' => 'value_1', 'column_name2' => 'value_2')
##	 * @param array \$where an associative array which define condition for datas to be update
##	 * Ex :
##	 *	array('column_name1' => \$value1, 'column_name2' => \$value2) // assume operator is 'equals'
##	 *  array('column_name1' => array('>', \$value1))
##	 * @throws RawDatasManagerException
##	 */
##	public function update(\$table, \$record, \$where = array()) {
##		if(in_array(\$table, self::\$tables)){
##			try {
##				\$sql = \"UPDATE `\$table` SET \";
##				\$sql_fields_stmt = array();
##				\$values = array();
##				foreach (\$record as \$field => \$value) {
##					if(in_array(\$field, self::\$columns[\$table])){
##						\$sql_fields_stmt[] = \"`\$field`=?\";
##						\$values[] = \$value;
##					}
##					else{
##						throw new RawDatasManagerException(\"Column \$field is not part of the \$table table\", 1);
##					}
##				}
##				\$sql .= implode(', ', \$sql_fields_stmt);
##				\$sqlWhere = \"\";
##				if(is_array(\$where) && !empty(\$where)){
##					list(\$sqlWhere, \$whereValues) = \$this->processWhere(\$where);
##					foreach(\$whereValues as \$val){
##						\$values[] = \$val;
##					}
##				}
##				\$sql .= \$sqlWhere;
##				\$md5 = md5(\$sql);
##				if(!isset(\$this->stmts[\$md5])){
##					\$this->stmts[\$md5] = \$this->db->prepare(\$sql);
##				}
##				\$stmt = \$this->stmts[\$md5];
##				\$this->bindValues(\$stmt, \$values);
##				\$stmt->execute();
##			}
##			catch(RawDatasManagerException \$rdme) {
##				throw \$rdme;
##			}
##			catch(\Exception \$e) {
##				throw new RawDatasManagerException(\$e->getMessage(), 2, \$e);
##			}
##		}
##		else{
##			throw new RawDatasManagerException(\"Table \$table is not part of the model\", 1);
##		}	
##	}
##	
##
##	/**
##	 * 
##	 * @param string \$table the table name on which we want to delete datas
##	 * @param array \$where an associative array which define condition for datas to be deleted
##	 * Ex :
##	 *	array('column_name1' => \$value1, 'column_name2' => \$value2) // assume operator is 'equals'
##	 *  array('column_name1' => array('>', \$value1))
##	 * @throws RawDatasManagerException
##	 */
##	public function delete(\$table, \$where) {
##		if(in_array(\$table, self::\$tables)){
##			try {
##				\$sql = \"DELETE FROM `\$table`\";
##				\$sqlWhere = \"\";
##				if(is_array(\$where) && !empty(\$where)){
##					list(\$sqlWhere, \$whereValues) = \$this->processWhere(\$where);
##					foreach(\$whereValues as \$val){
##						\$values[] = \$val;
##					}
##				}
##				else{
##					throw new RawDatasManagerException(\"WHERE clause in delete method have to be set. if you want to clean a table, use truncate method instead\", 1);
##				}
##				if(\$sqlWhere == \"\"){
##					throw new RawDatasManagerException(\"Computing WHERE clause from \\\$where parameter returned an empty string, use truncate method instead\", 1);
##				}
##				\$sql .= \$sqlWhere;
##				\$md5 = md5(\$sql);
##				if(!isset(\$this->stmts[\$md5])){
##					\$this->stmts[\$md5] = \$this->db->prepare(\$sql);
##				}
##				\$stmt = \$this->stmts[\$md5];
##				\$this->bindValues(\$stmt, \$values);
##				\$stmt->execute();
##			}
##			catch(RawDatasManagerException \$rdme) {
##				throw \$rdme;
##			}
##			catch(\Exception \$e) {
##				throw new RawDatasManagerException(\$e->getMessage(), 2, \$e);
##			}
##		}
##		else{
##			throw new RawDatasManagerException(\"Table \$table is not part of the model\", 1);
##		}	
##	}
##	
##	
##	/**
##	 * 
##	 * @param string \$table the table name on which we want to count the number of rows
##	 * @return integer the number of rows in the table
##	 * @throws RawDatasManagerException
##	 */
##	public function count(\$table) {
##		if(in_array(\$table, self::\$tables)){
##			try {
##				\$sql = \"SELECT count(*) FROM `\$table`\";
##				\$md5 = md5(\$sql);
##				if(!isset(\$this->stmts[\$md5])){
##					\$this->stmts[\$md5] = \$this->db->prepare(\$sql);
##				}
##				\$stmt = \$this->stmts[\$md5];
##				\$stmt->execute();
##				
##				\$datas = \$stmt->fetch(\PDO::FETCH_NUM);
##				return \$datas[0];
##			}
##			catch(\Exception \$e) {
##				throw new RawDatasManagerException(\$e->getMessage(), 2, \$e);
##			}
##		}
##		else{
##			throw new RawDatasManagerException(\"Table \$table is not part of the model\", 1);
##		}
##	}
##	
##	/**
##	 * 
##	 * @param string \$table the table name we want to delete datas
##	 * @throws RawDatasManagerException
##	 */
##	public function truncate(\$table) {
##		if(in_array(\$table, self::\$tables)){
##			try {
##				\$sql = \"TRUNCATE TABLE `\$table`\";\$md5 = md5(\$sql);
##				if(!isset(\$this->stmts[\$md5])){
##					\$this->stmts[\$md5] = \$this->db->prepare(\$sql);
##				}
##				\$stmt = \$this->stmts[\$md5];
##				\$stmt->execute();
##			}
##			catch (\Exception \$e) {
##				throw new RawDatasManagerException(\$e->getMessage(), 2, \$e);
##			}
##		}
##		else{
##			throw new RawDatasManagerException(\"Table \$table is not part of the model\", 1);
##		}
##	}
##	
##
##	
##	protected function processWhere(\$where) {
##		\$conditions = array();
##		\$values = array();
##		foreach (\$where as \$field => \$condition) {
##			list(\$sql, \$vals) = \$this->getCondition(\$field, \$condition);
##			\$conditions[] = \$sql;
##			if(isset(\$vals)){
##				if(is_array(\$vals)){
##					foreach(\$vals as \$val){
##						\$values[] = \$val;
##					}
##				}
##				else{
##					\$values[] = \$vals;
##				}
##			}
##		}
##		return array(\" WHERE \".implode(' AND ', \$conditions), \$values);
##	}
##	
##
##	protected function getCondition(\$field, \$condition) {
##		//echo print_r(\$condition,true);
##		if(is_array(\$condition)) { // operator, value(s)
##			\$values = array();
##			\$keys = array();
##			\$operator = \$condition[0];
##			if(! is_array(\$condition[1])){
##				\$values[] = \$condition[1];
##			}
##			else {
##				foreach(\$condition[1] as \$val) {
##					\$values[] = \$val;
##					\$keys[] = \"?\";
##				}
##			}
##			return array(\"`\$field` \".sprintf(self::OPERATORS[strtoupper(\$operator)]['format'], implode(', ', \$keys)), \$values);
##		}
##		elseif(isset(self::OPERATORS[strtoupper(\$condition)]) && (! self::OPERATORS[strtoupper(\$condition)]['parametrized'])){
##			return array(\"`\$field` \".self::OPERATORS[strtoupper(\$condition)]['format'], null);
##		}
##		return array(\"`\$field` = ?\", \$condition);
##	}
##	
##	protected function processOrder(\$sortAttributes, \$table) {
##		\$order_string = \"\";
##		if(is_array(\$sortAttributes)){
##			\$order_string_tab = array();
##			if(array_values(\$sortAttributes) === \$sortAttributes){	//it's not an associative array (just column names as values), assume order is ASC
##				foreach(\$sortAttributes as \$attr){
##					if(in_array(\$attr, self::\$columns[\$table])){
##						\$order_string_tab[] = \"`\$attr`\";
##					}
##				}
##			}
##			else{
##				foreach(\$sortAttributes as \$attr => \$order){
##					if(in_array(\$attr, self::\$columns[\$table]) && (\$order == 'ASC' || \$order == 'DESC')){
##						\$order_string_tab[] = \"`\$attr` \$order\";
##					}
##				}
##			}
##			if(! empty(\$order_string_tab)){
##				\$order_string = ' ORDER BY '.implode(', ', \$order_string_tab);
##			}
##		}
##		elseif(in_array(\$sortAttributes, self::\$columns[\$table])){
##			\$order_string = \" ORDER BY `\$sortAttributes`\";
##		}
##		return \$order_string;
##	}
##	
##	
##	protected function bindValues(\$statement, \$values) {
##		if(is_array(\$values)) {
##			foreach(\$values as \$i => \$value) {
##				if(is_integer(\$value) || (is_numeric(\$value) && (strval(\$value) === strval(intval(\$value))))) {
##					\$type = \PDO::PARAM_INT;
##					\$value = intval(\$value);
##				}
##				elseif(is_null(\$value) || \$value === 'NULL') {
##					\$type = \PDO::PARAM_NULL;
##					\$value = null;
##				}
##				elseif(is_bool(\$value)) {
##					\$type = \PDO::PARAM_BOOL;
##				}
##				else{
##					\$type = \PDO::PARAM_STR;
##				}
##				\$statement->bindValue(intval(\$i + 1), \$value, \$type);
##			}
##		}
##	}
##}
#?>