<?php
class ModelLoader{
	
	public $schema;
	public $model;
	public $error;
		
	public function __construct($file){
		
		libxml_use_internal_errors(true);
		
		$schema = new DomDocument();
		if(! $schema->load($file)){
			throw new ModelLoaderException(self::libxml_get_errors(), 0);
		}
		$this->schema = $schema;
	}
	
	public function getErrors(){
		return $this->error;
	}
	
	public function getModel(){
		if(! $this->checkAndRecord()){
			throw new ModelLoaderException($this->getErrors());
		}
		return $this->model;
	}
	
	
	public function checkAndRecord(){

		$model = array();
		
		$model_xml = $this->schema;
		if (! $model_xml->schemaValidate(__DIR__.'/msorm_schema.xsd')) {
			$this->error = self::libxml_get_errors();
			return false;
		}
		
		$model['database'] = array();
		$db = $model_xml->getElementsByTagName("database");
		$model['database']['host'] = $db->item(0)->getAttribute('host');
		$model['database']['port'] = $db->item(0)->getAttribute('port');
		$model['database']['name'] = $db->item(0)->getAttribute('name');
		$model['database']['user'] = $db->item(0)->getAttribute('user');
		$model['database']['password'] = $db->item(0)->getAttribute('password');
		$model['database']['phpNamespace'] = $db->item(0)->getAttribute('phpNamespace');
		
		
		$dbmodel = array();
		$tables = $model_xml->getElementsByTagName("table");
		
		// Loading all tables in a first pass
		foreach($tables as $table){
		
			$table_name = $table->getAttribute("name");
			
			
			if(isset($dbmodel[$table_name])){
				$this->error = "Cannot define $table_name twice. Table name should be unique in the schema";
				return false;
			}
		
			$dbmodel[$table_name] = array();
		
			$dbmodel[$table_name]['isCrossRef'] = $table->getAttribute("isCrossRef") == "true" ? true : false;
			if(! $dbmodel[$table_name]['isCrossRef']){
				$dbmodel[$table_name]['object_name'] = $table->getAttribute("phpObjectName");
				if($dbmodel[$table_name]['object_name'] == ""){
					$this->error = "$table_name should define at least phpObjectName attribute while not a cross ref table";
					return false;
				}
				$dbmodel[$table_name]['object_pf_name'] = $table->getAttribute("phpObjectPFName");
				if($dbmodel[$table_name]['object_pf_name'] == ""){
					$dbmodel[$table_name]['object_pf_name'] = $dbmodel[$table_name]['object_name']."s";
				}
				$dbmodel[$table_name]['manager_name'] = $table->getAttribute("phpManagerName");
				if($dbmodel[$table_name]['manager_name'] == ""){
					$dbmodel[$table_name]['manager_name'] = $dbmodel[$table_name]['object_pf_name']."Manager";
				}
				if($table->hasAttribute("phpExtraAttributesPrefix")){
					$this->error = "$table_name should not define phpExtraAttributesPrefix attribute while not a cross ref table";
					return false;
				}
			}
			else{
				if($table->hasAttribute("phpExtraAttributesPrefix")){
					$dbmodel[$table_name]['extra_attributes_prefix'] = $table->getAttribute("phpExtraAttributesPrefix");
				}
			}
			
			$dbmodel[$table_name]['columns'] = array();
			$auto = false;
			$referencedTables = array();
			foreach($table->getElementsByTagName("column") as $column){
		
				$column_name = $column->getAttribute("name");
				
				if(isset($dbmodel[$table_name]['columns'][$column_name])){
					$this->error = "Cannot define $column_name twice. Column name should be unique in a table (in table $table_name)";
					return false;
				}
		
				$dbmodel[$table_name]['columns'][$column_name] = array();
				
				
				$column_type = array();
				$column_type['type'] = $column->getAttribute("type");
				
				$column_type['size'] = $column->getAttribute("size");
				// Doit etre défini si "type" est char ou varchar
				if(($column_type['type'] == "char") || ($column_type['type'] == "varchar")){
					if($column_type['size'] == ""){
						$this->error = "size attribute should be defined for ".$column_type['type']." column (column $column_name in table $table_name)";
						return false;
					}
				}
				// Ne doit pas être defini si type different des suivants
				if(! (($column_type['type'] == "int") || ($column_type['type'] == "integer") || ($column_type['type'] == "text") || ($column_type['type'] == "char") || ($column_type['type'] == "varchar"))){
					if($column_type['size'] != ""){
						$this->error = "size attribute should not be defined for ".$column_type['type']." column (column $column_name in table $table_name)";
						return false;
					}
				}
				
				
				$column_type['values'] = $column->getAttribute("values");
				// Doit etre défini si et seulement si "type" est enum
				if($column_type['type'] == "enum"){
					if($column_type['values'] == ""){
						$this->error = "values attribute should be defined for enum column (column $column_name in table $table_name)";
						return false;
					}
				}
				if(! ($column_type['type'] == "enum")){
					if($column_type['values'] != ""){
						$this->error = "values attribute should not be defined for ".$column_type['type']." column (column $column_name in table $table_name)";
						return false;
					}
				}

				$column_type['auto_increment'] = $column->getAttribute("autoIncrement") == "true" ? true : false;
				if($column_type['auto_increment']){
					//Ne peut être spécifié qu'une seule fois pour une table donnée
					if($auto){
						$this->error = "auto_increment attribute cannot be set twice in a table (column $column_name in table $table_name)";
						return false;
					}
					$auto = true;
				
					// Ne peut etre défini que sur une clé primaire
					// TODO : à voir et implémenter si besoin
				
					// Ne peut etre défini que si "type" est int, integer ...
					if(! (($column_type['type'] == "int") || ($column_type['type'] == "integer") || ($column_type['type'] == "tinyint") || ($column_type['type'] == "smallint") || ($column_type['type'] == "bigint"))){
						$this->error = "auto_increment attribute can only be set for integer (int, smallint, bigint ...) column (column $column_name in table $table_name)";
						return false;
					}
				}
				
				$column_type['unsigned'] = $column->getAttribute("unsigned") == "true" ? true : false;
				if($column_type['unsigned']){
					// Ne peut etre défini que si "type" est int, integer ... ou float ...
					if(! (($column_type['type'] == "int") || ($column_type['type'] == "integer") || ($column_type['type'] == "tinyint") || ($column_type['type'] == "smallint") || ($column_type['type'] == "bigint")
							|| ($column_type['type'] == "float") || ($column_type['type'] == "real") || ($column_type['type'] == "decimal") || ($column_type['type'] == "double"))){
						$this->error = "unsigned attribute can only be set for numeric (int, smallint ..., float, double ...) column (column $column_name in table $table_name)";
						return false;
					}
				}
				
				$column_type['default'] = $column->getAttribute("default");
				if($column_type['default'] != ""){
					if($column_type['auto_increment']){
						$this->error = "default value cannot be set for an autoincrement column ($column_name in table $table_name)";
						return false;
					}
					
					
					$default = $column_type['default'];
					
					if(($column_type['type'] == "int") || ($column_type['type'] == "integer") || ($column_type['type'] == "tinyint") || ($column_type['type'] == "smallint") || ($column_type['type'] == "bigint")){
						if($column_type['unsigned']){
							if(substr($default, 0,1) == '-' ){
								$this->error = "default value should not be negative in unsigned column (column $column_name in table $table_name)";
								return false;
							}
						}
						$intval = intval($default);
						if("$intval" != $default){
							$this->error = "default value ($default) should be an integer value (column $column_name in table $table_name)";
							return false;
						}
					}
					elseif(($column_type['type'] == "float") || ($column_type['type'] == "real") || ($column_type['type'] == "decimal") || ($column_type['type'] == "double")){
						if($column_type['unsigned']){
							if(substr($default, 0,1) == '-' ){
								$this->error = "default value should not be negative in unsigned column (column $column_name in table $table_name)";
								return false;
							}
						}
						if(! is_numeric($default)){
							$this->error = "default value should be a float value (column $column_name in table $table_name)";
							return false;
						}
					}
					elseif(($column_type['type'] == "bool") || ($column_type['type'] == "boolean")){
						if(! ($default == 'true' || $default == 'false')){
							$this->error = "default value should be a 'true' or 'false' value (column $column_name in table $table_name)";
							return false;
						}
						//$column_type['default'] = $default == "true" ? 1 : 0;
					}
					elseif(($column_type['type'] == "date") || ($column_type['type'] == "time") || ($column_type['type'] == "datetime")){
						if($column_type['type'] == "date"){
							$format = "Y-m-d";
						}
						elseif($column_type['type'] == "time"){
							$format = "h:i:s";
						}
						elseif($column_type['type'] == "datetime"){
							$format = "Y-m-d h:i:s";
						}
						if(! $date = DateTime::createFromFormat($format, $default)){
							$this->error = "Wrong format for default value  (column $column_name in table $table_name)";
							return false;
						}
						if($date->format($format) != $default){
							$this->error = "$default is not a valid value (column $column_name in table $table_name)";
							return false;
						}
					}
					elseif($column_type['type'] == "enum"){
						$values = explode(',', $column_type['values']);
						if(!in_array($default, $values)){
							$this->error = "default value \"$default\" is not a one of the authorized values (column $column_name in table $table_name)";
							return false;
						}
					}
				}
				else{
					$column_type['default'] = null;
				}
				
				$dbmodel[$table_name]['columns'][$column_name]['data'] = $column_type;
		
				if($column->hasChildNodes()){
					$reference = $column->getElementsByTagName("reference")->item(0);
					$column_reference = array('table' => $reference->getAttribute('table'), 'column' => $reference->getAttribute('column'));
					if($reference->getAttribute('phpAttributeName') != ""){
						$column_reference['attribute_name'] =  $reference->getAttribute('phpAttributeName');
					}
					if($reference->getAttribute('phpRefAttributeName') != ""){
						$column_reference['ref_attribute_name'] =  $reference->getAttribute('phpRefAttributeName');
						
						if($reference->getAttribute('phpRefAttributePFName') != ""){
							$column_reference['ref_attribute_pf_name'] =  $reference->getAttribute('phpRefAttributePFName');
						}
						else{
							$column_reference['ref_attribute_pf_name'] =  $reference->getAttribute('phpRefAttributeName').'s';
						}
					}
					$column_reference['delete_cascade'] = $reference->getAttribute('deleteCascade') == "true" ? true : false;
					
					if(!in_array($column_reference['table'], $referencedTables)){
						$referencedTables[] = $column_reference['table'];
					}
					else{
						if(! (isset($column_reference['attribute_name']) && isset($column_reference['ref_attribute_name']))){
							$this->error  = $column_reference['table']." is referenced more than once in the same table ($table_name).".PHP_EOL;
							$this->error .= "You must use phpAttributeName and phpRefAttributeName attributes in references for columns referencing this table to avoid confusion.";
							return false;
						}
					}
					$dbmodel[$table_name]['columns'][$column_name]['reference'] = $column_reference;
				}
		
				$dbmodel[$table_name]['columns'][$column_name]['primary_key'] = $column->getAttribute("primaryKey") == "true" ? true : false;
				$dbmodel[$table_name]['columns'][$column_name]['unique'] = $column->getAttribute("unique") == "true" ? true : false;
				
				if($dbmodel[$table_name]['columns'][$column_name]['unique'] && $dbmodel[$table_name]['columns'][$column_name]['primary_key']){
					$this->error = "A column cannot be primary key and unique (column $column_name in table $table_name)";
					return false;
				}
					
				$dbmodel[$table_name]['columns'][$column_name]['required'] = $column->getAttribute("required") == "true" ? true : ($column->getAttribute("required") == "false" ? false : null);
				
				if(isset($dbmodel[$table_name]['columns'][$column_name]['required'])){
					if($dbmodel[$table_name]['columns'][$column_name]['required'] && $column_type['auto_increment']){
						$this->error = "A column cannot be required and auto_increment (column $column_name in table $table_name)";
						return false;
					}
					if(! $dbmodel[$table_name]['columns'][$column_name]['required'] && $dbmodel[$table_name]['columns'][$column_name]['primary_key']){
						$this->error = "A primary key cannot be set 'required=false' (column $column_name in table $table_name)";
						return false;
					}
				}
				else{
					if($column_type['auto_increment']){
						$dbmodel[$table_name]['columns'][$column_name]['required'] = false;
					}
					elseif($dbmodel[$table_name]['columns'][$column_name]['primary_key']){
						$dbmodel[$table_name]['columns'][$column_name]['required'] = true;
					}
					else{
						$dbmodel[$table_name]['columns'][$column_name]['required'] = false;
					}
				}
				
				$dbmodel[$table_name]['columns'][$column_name]['multi-unique'] = false;
			}
			
			$dbmodel[$table_name]['uniques'] = array();
			foreach($table->getElementsByTagName("uniques") as $unique){
				$columns = $unique->getAttribute("columns");
				$columns = explode(",", $columns);
				foreach($columns as $col){
					$col = trim($col);
					if(! isset($dbmodel[$table_name]['columns'][$col])){
						$this->error = "Multi unique key (uniques tag) make reference to a column ($col) which is not defined (in table $table_name)";
						return false;
					}
					// Think it was an error
					/*if($dbmodel[$table_name]['columns'][$col]['primary_key']){
						$this->error = "Column $col in unique key is already part of the primary key of the table (in table $table_name)";
						return false;
					}*/
					if($dbmodel[$table_name]['columns'][$col]['unique']){
						$this->error = "Column $col cannot be part of a multi-unique key and be unique itself (in table $table_name)";
						return false;
					}
					$dbmodel[$table_name]['columns'][$col]['multi-unique'] = true;
				}
				$dbmodel[$table_name]['uniques'][] = $columns;
			}
			
			// A second pass on each columns of a table to check default values concistancy against other considerations
			$pk_is_single_col = true;
			$pk_count = 0;
			foreach($dbmodel[$table_name]['columns'] as $column){
				if($column['primary_key']){
					$pk_count++;
					if($pk_count > 1){
						$pk_is_single_col = false;
						break;
					}
				}
			}
			foreach($table->getElementsByTagName("column") as $column){
		
				$column_name = $column->getAttribute("name");
				$default = $column->getAttribute("default");
				$autoincrement = $column->getAttribute("autoIncrement") == "true" ? true : false;
				
				if($default != ""){
					if($autoincrement){
						$this->error = "default value cannot be set for an autoincrement column ($column_name in table $table_name)";
						return false;
					}
					if($dbmodel[$table_name]['columns'][$column_name]['unique'] 
						|| ($dbmodel[$table_name]['columns'][$column_name]['primary_key'] && $pk_is_single_col)){
						$this->error = "default value cannot be set for a column defined as unique or primary key (column $column_name in table $table_name)";
						return false;
					}
				}
				else{
					if($column->hasChildNodes() && ($column->getAttribute("required") == "true")){
						if(! $dbmodel[$table_name]['columns'][$column_name]['reference']['delete_cascade']){
							$this->error  = "a default value should be set for a required reference column (column $column_name in table $table_name)".PHP_EOL;
							$this->error .= "if not, you must set deleteCascade=\"true\" parameter";
							return false;
						}
					}
				}
			}
		}
		
		// A second pass to check references
		foreach($tables as $table){
			$table_name = $table->getAttribute("name");
			foreach($table->getElementsByTagName("column") as $column){
				$column_name = $column->getAttribute("name");
				if(isset($dbmodel[$table_name]['columns'][$column_name]['reference'])){
					$reference = $dbmodel[$table_name]['columns'][$column_name]['reference'];
					if(! isset($dbmodel[$reference['table']])){
						$this->error = "Column $column_name in $table_name references a table which is not defined (".$reference['table'].")";
						return false;
					}
					if(! isset($dbmodel[$reference['table']]['columns'][$reference['column']])){
						$this->error = "Column $column_name in $table_name references a column which is not defined (".$reference['table']."(".$reference['column']."))";
						return false;
					}
					
					if(! ($dbmodel[$reference['table']]['columns'][$reference['column']]['primary_key'] || $dbmodel[$reference['table']]['columns'][$reference['column']]['unique'])){
						$this->error = "Column $column_name in $table_name cannot reference a column which is not primary key or unique (".$reference['table']."(".$reference['column']."))";
						return false;
					}
					$ref_pk_count = 0;
					foreach($dbmodel[$reference['table']]['columns'] as $col){
						if($col['primary_key']){
							$ref_pk_count++;
						}
					}
					if($ref_pk_count > 1){
						$this->error = "Column $column_name in $table_name cannot reference a column which is part of a multi primary key (".$reference['table']."(".$reference['column']."))";
						return false;
					}
				}
				
			}
		}
		
		$model['tables'] = $dbmodel;
		$this->model = $model;
		
		//print_r($model);
		return true;
	}
	
	public function test(){
		$model_xml = $this->schema;
		if (! $model_xml->schemaValidate(__DIR__.'/msorm_schema.xsd')) {
			$this->error = self::libxml_get_errors();
			return false;
		}
		return true;
	}

	private static function libxml_get_error($error) {
		$return = "\n";
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "Warning $error->code ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "Error $error->code ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "Fatal Error $error->code ";
				break;
		}
		$return .= "on line $error->line : ";
		$return .= trim($error->message);
		$return .= "\n";
		return $return;
	}
	
	private static function libxml_get_errors() {
		$message = "";
		$errors = libxml_get_errors();
		foreach ($errors as $error) {
			$message.= self::libxml_get_error($error);
		}
		libxml_clear_errors();
		return $message;
	}
}

class ModelLoaderException extends Exception {
	public function ModelLoaderException($message = '', $code = 0, $e = null) {
		parent::__construct($message, $code, $e);
	}
}
?>