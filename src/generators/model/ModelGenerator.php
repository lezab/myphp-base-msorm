<?php
class ModelGenerator extends MGenerator{
	
	public $version = '8.0.3';
	
	public $model_directory;
	public $model_core_directory;
	public $model_exceptions_directory;
	
	public $update;
	
	public $dbmodel;
	public $database;
	public $namespace;
	
	private $objectModel = null;
	
	private $templates = array(
		"object", "object_core", "object_exception",
		"manager", "manager_core", "manager_exception",
		"connection_provider", "connection_provider_exception",
		'msorm_object',
		'rawdatas_manager_core', 'rawdatas_manager_exception', 'rawdatas_manager'
	);
		
	public function __construct($project_name, $tables, $database, $output, $update_only){
		
		$namespace = $database['phpNamespace'] != "" ? rtrim($database['phpNamespace'], "\\") : $project_name;
		
		$directory = $output."/".preg_replace("#\\\#", DIRECTORY_SEPARATOR, $namespace)."/";

		$this->model_directory = $directory;
		if(! file_exists($directory)){
			mkdir($directory, 0755, true);
			mkdir($directory."core", 0755, true);
			mkdir($directory."exceptions", 0755, true);
		}
		else{
			if(! file_exists($directory."core")){
				mkdir($directory."core", 0755, true);
			}
			if(! file_exists($directory."exceptions")){
				mkdir($directory."exceptions", 0755, true);
			}
		}
		$this->model_core_directory = $directory."core/";
		$this->model_exceptions_directory = $directory."exceptions/";
		$this->dbmodel = $tables;
		$this->database = $database;
		$this->namespace = $namespace;
		$this->update = $update_only;
	}
	
	public function run(){
		
		echo "Cleaning output directory".PHP_EOL;
		echo "...".PHP_EOL;
		
		$success = true;
		if($this->update){
			$core_dir = dir($this->model_directory."core");
			while($entry = $core_dir->read()) {
				if(is_file($this->model_directory."core/".$entry) && ($entry != "__db_params.conf.php")){
					if(! unlink($this->model_directory."core/".$entry)){
						echo "Fail to delete file ".$this->model_directory."exceptions/".$entry.PHP_EOL.PHP_EOL;
						$success = false;
					}
				}
			}
			$exceptions_dir = dir($this->model_directory."exceptions");
			while($entry = $exceptions_dir->read()) {
				if(is_file($this->model_directory."exceptions/".$entry)){
					if(! unlink($this->model_directory."exceptions/".$entry)){
						echo "Fail to delete file ".$this->model_directory."exceptions/".$entry.PHP_EOL.PHP_EOL;
						$success = false;
					}
				}
			}
			$main_dir = dir($this->model_directory);
			$current_model_files = array("RawDatasManager.php");
			foreach($this->getObjectModel() as $tablename => $datas){
				$current_model_files[] = $datas['object_name'].".php";
				$current_model_files[] = $datas['manager_name'].".php";
			}
			while($entry = $main_dir->read()) {
				if(is_file($this->model_directory.$entry)
					&& (! in_array($entry, $current_model_files))
					&& (substr_compare($entry, ".bak", -4) !== 0)){
					if(! rename($this->model_directory.$entry, $this->model_directory.$entry.".bak")){
						echo "Fail to rename file ".$this->model_directory.$entry." as ".$this->model_directory.$entry.".bak".PHP_EOL.PHP_EOL;
						$success = false;
					}
				}
			}
		}
		else{
			foreach(array("", "core", "exceptions") as $dir_name){
				$directory = dir($this->model_directory.$dir_name);
				while($entry = $directory->read()) {
					if(is_file($this->model_directory.$dir_name."/".$entry)){
						if(! unlink($this->model_directory.$dir_name."/".$entry)){
							echo "Fail to delete file ".$this->model_directory.$dir_name."/".$entry.PHP_EOL.PHP_EOL;
							$success = false;
						}
					}
				}
			}
		}
		
		if($success){
			echo " OK".PHP_EOL;
		}
		else{
			echo "FAILED".PHP_EOL;
			echo "Terminating process".PHP_EOL;
			exit(0);
		}
		echo PHP_EOL;
		
		
		echo "Generating model files".PHP_EOL;
		echo "...".PHP_EOL;
		
		self::compileTemplates("model", $this->templates);
		
		$database = $this->database;
		
		$model = $this->getObjectModel();
		//print_r($model);
		
		$namespace = $this->namespace;
		$nsp = $namespace.'\\';
		
		
		$msorm_version = $this->version;
		
		
		/** ******************************************/
		/** DatabaseConnectionProvider            ****/
		/** ******************************************/
		$file = fopen($this->model_core_directory."DatabaseConnectionProvider.php", "w+");
		include(__DIR__.'/connection_provider_generator.php');
		fclose($file);
		$file = fopen($this->model_exceptions_directory."DatabaseConnectionProviderException.php", "w+");
		include(__DIR__.'/connection_provider_exception_generator.php');
		fclose($file);
		
		if((! $this->update) || (! file_exists($this->model_core_directory."__db_params.conf.php"))){
			$file = fopen($this->model_core_directory."__db_params.conf.php", "w+");
			fwrite($file, "<?php\n");
			fwrite($file, "\$db_url=\"".$database['host']."\";\n");
			fwrite($file, "\$db_port=\"".$database['port']."\";\n");
			fwrite($file, "\$db_name=\"".$database['name']."\";\n");
			fwrite($file, "\$db_user=\"".$database['user']."\";\n");
			fwrite($file, "\$db_password=\"".$database['password']."\";\n");
			fwrite($file, "?>");
			fclose($file);
		}
		
		
		/** ******************************************/
		/** MSORM object                          ****/
		/** ******************************************/
		$file = fopen($this->model_core_directory."MSORM.php", "w+");
		include(__DIR__.'/msorm_object_generator.php');
		fclose($file);
		
		
		/** ******************************************/
		/** RawDatasManager                       ****/
		/** ******************************************/
		$raw_db_model = $this->dbmodel;
		
		// managercore
		$file = fopen($this->model_core_directory."RawDatasManagerCore.php", "w+");
		include(__DIR__.'/rawdatas_manager_core_generator.php');
		fclose($file);
		// exception
		$file = fopen($this->model_exceptions_directory."RawDatasManagerException.php", "w+");
		include(__DIR__.'/rawdatas_manager_exception_generator.php');
		fclose($file);
		// manager
		if((! $this->update) || (! file_exists($this->model_directory."RawDatasManager.php"))){
			$file = fopen($this->model_directory."RawDatasManager.php", "w+");
			include(__DIR__.'/rawdatas_manager_generator.php');
			fclose($file);
		}
		
		/** ******************************************/
		/** Tables objects                        ****/
		/** ******************************************/
		foreach($model as $tablename => $datas){
			
			$classname = $datas['object_name'];
			$core_classname = $datas['object_name']."Core";
			$exception_classname = $datas['object_name']."Exception";
			$manager_classname = $datas['manager_name'];
			$manager_core_classname = $datas['manager_name']."Core";
			$manager_exception_classname = $datas['manager_name']."Exception";
			
			echo PHP_EOL."Processing : $classname :".PHP_EOL;
			
			/** ******************************************/
			/** Classe objectCore                     ****/
			/** ******************************************/
			// création du fichier
			$filename = $core_classname.".php";
			echo "	generating : $filename".PHP_EOL;
			$file = fopen($this->model_core_directory.$filename, "w+");
			include(__DIR__.'/object_core_generator.php');
			fclose($file);
			
			/** ******************************************/
			/** Classe objectException                ****/
			/** ******************************************/
			// création du fichier
			$filename = $exception_classname.".php";
			echo "	generating : $filename".PHP_EOL;
			$file = fopen($this->model_exceptions_directory.$filename, "w+");
			include(__DIR__.'/object_exception_generator.php');
			fclose($file);
			
			
			/** ******************************************/
			/** Classe object                         ****/
			/** ******************************************/
			$filename = $classname.".php";
			if((! $this->update) || (! file_exists($this->model_directory.$filename))){
				// création du fichier
				echo "	generating : $filename".PHP_EOL;
				$file = fopen($this->model_directory.$filename, "w+");
				include(__DIR__.'/object_generator.php');
				fclose($file);
			}
			
			
			/** ******************************************/
			/** Classe objectManagerCore              ****/
			/** ******************************************/
			// création du fichier
			$filename = $manager_core_classname.".php";
			echo "	generating : $filename".PHP_EOL;
			$file = fopen($this->model_core_directory.$filename, "w+");
			include(__DIR__.'/manager_core_generator.php');
			fclose($file);
			
			/** ******************************************/
			/** Classe objectManagerException         ****/
			/** ******************************************/
			// création du fichier
			$filename = $manager_exception_classname.".php";
			echo "	generating : $filename".PHP_EOL;
			$file = fopen($this->model_exceptions_directory.$filename, "w+");
			include(__DIR__.'/manager_exception_generator.php');
			fclose($file);
			
			
			/** ******************************************/
			/** Classe objectManager                  ****/
			/** ******************************************/
			$filename = $manager_classname.".php";
			if((! $this->update) || (! file_exists($this->model_directory.$filename))){
				// création du fichier
				echo "	generating : $filename".PHP_EOL;
				$file = fopen($this->model_directory.$filename, "w+");
				include(__DIR__.'/manager_generator.php');
				fclose($file);
			}
		}
		
		echo "Done.".PHP_EOL.PHP_EOL;
	}
	
	/**
	 * Takes the raw database model as described in the xml file loaded in an array and returns
	 * an other array describing something closer of the object model (for example crossRef tables are not objects)
	 * 
	 * @param array $dbModel
	 * @return array|false
	 */
	public function getObjectModel(){
		if(isset($this->objectModel)){
			return $this->objectModel;
		}
		$dbModel = $this->dbmodel;
		//print_r($dbModel);
		$model = array();
		$objectModel = array();
		/**
		 * $objectModel[obj_name]['attributes'][attr_name]['type'] can have 6 values :
		 * 
		 * value : correspond to a simple column of data in the database
		 * crossRefValue : correspond to a simple column of data, but defined in a crossRef table
		 * refValue : correspond to a column of data which contains id of another table
		 * refObject : an object attribute which contain the object corresponding to the refValue attribute
		 * 
		 * object : an object attribute which is the object which references the object
		 * objectList : an array of objects attribute which are all the objects which references the object
		 *
		 * object and objectList attribute are attributes added to objects by analysing other tables and not corresponds
		 * to colums of table itself.
		 */
		foreach($dbModel as $tablename => $datas){
			$model[$tablename] = $datas;
			foreach($datas['columns'] as $column_name => $infos){
				$datatype = $infos['data'];
				switch(strtolower($infos['data']['type'])){
					
					case 'bigint' : $datatype['type'] = 'integer'; break;
					case 'int' : $datatype['type'] = 'integer'; break;
					//case 'integer' : $datatype['type'] = 'integer'; break;
					case 'tinyint' : $datatype['type'] = 'integer'; break;
					case 'smallint' : $datatype['type'] = 'integer'; break;
					
					case 'bool' : $datatype['type'] = 'boolean'; break;
					case 'boolean' : $datatype['type'] = 'boolean'; break;
					
					case 'char' : $datatype['type'] = 'string'; $datatype['length'] = $infos['data']['size']; break;
					case 'varchar' : $datatype['type'] = 'string'; $datatype['length'] = $infos['data']['size']; break;
					case 'text' :
						$datatype['type'] = 'string';
						$datatype['length'] = ($infos['data']['size'] != "") ? $infos['data']['size'] : 65535;
						break;
					
					case 'double' : $datatype['type'] = 'float'; break;
					//case 'float' : $datatype['type'] = 'float'; break;
					case 'real' : $datatype['type'] = 'float'; break;
					case 'decimal' : $datatype['type'] = 'float'; break;
					default : break;
				}
				$model[$tablename]['columns'][$column_name]['data'] = $datatype;
			}
		}
		
		//print_r($model);
		
		//echo "------------------------------------------------------------------------------------------\n";
		//echo "------------------------------------------------------------------------------------------\n";
		//echo "------------------------------------------------------------------------------------------\n";
		
		foreach($model as $tablename => $datas){

			if(! $datas['isCrossRef']){
				
				$objectModel[$tablename]['object_name'] = $datas['object_name'];
				$objectModel[$tablename]['object_pf_name'] = $datas['object_pf_name'];
				$objectModel[$tablename]['manager_name'] = $datas['manager_name'];
				$objectModel[$tablename]['primary_key'] = array();
				$objectModel[$tablename]['uniques'] = $datas['uniques'];
				
				foreach($datas['columns'] as $column_name => $infos){
					if(!isset($infos['reference'])){
						// Les attributs simples
						$objectModel[$tablename]['attributes'][$column_name]['type'] = 'value';
					}
					else{
						$objectModel[$tablename]['attributes'][$column_name]['type'] = 'refValue';
					}
					$objectModel[$tablename]['attributes'][$column_name]['datatype'] = $infos['data'];
					$objectModel[$tablename]['attributes'][$column_name]['required'] = $infos['required'];
					$objectModel[$tablename]['attributes'][$column_name]['unique'] = $infos['unique'];
					$objectModel[$tablename]['attributes'][$column_name]['multi-unique'] = $infos['multi-unique'];
					$objectModel[$tablename]['attributes'][$column_name]['primary_key'] = $infos['primary_key'];
					if($infos['primary_key']){
						$objectModel[$tablename]['primary_key'][] = $column_name;
					}
				}
				
				foreach($datas['columns'] as $column_name => $infos){
					if(isset($infos['reference'])){
						
						if($infos['unique'] || ($infos['primary_key'] && (count($objectModel[$tablename]['primary_key']) == 1 ))){
							// La colonne qui référence est unique seule ou primaire seule
							// Relation One to One
							$attr_name = isset($infos['reference']['attribute_name']) ? $infos['reference']['attribute_name'] : $model[$infos['reference']['table']]['object_name'];
							$objectModel[$tablename]['attributes'][$column_name]['ref'] = $attr_name;
							$objectModel[$tablename]['attributes'][$attr_name]['type'] = 'refObject';
							$objectModel[$tablename]['attributes'][$attr_name]['datatype'] = $model[$infos['reference']['table']]['object_name'];
							$objectModel[$tablename]['attributes'][$attr_name]['manager'] = $model[$infos['reference']['table']]['manager_name'];
							$objectModel[$tablename]['attributes'][$attr_name]['key'] = $infos['reference']['column'];
							$objectModel[$tablename]['attributes'][$attr_name]['ref'] = $column_name;
							
							$attr_name = isset($infos['reference']['ref_attribute_name']) ? $infos['reference']['ref_attribute_name'] : $datas['object_name'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['type'] = 'object';
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['datatype'] = $datas['object_name'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['manager'] = $datas['manager_name'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['id_attributes'] = $objectModel[$tablename]['primary_key'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_my_id'] = $column_name;
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_my_attr_id'] = $infos['reference']['column'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['delete_cascade'] = $infos['reference']['delete_cascade'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_default'] = $infos['data']['default'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_required'] = $infos['required'];
							
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_cardinal'] = 'one';
							
						}
						else{
							// La colonne qui référence n'est ni unique seule ni primaire seule
							// ==> Relation Many to One
							$attr_name = isset($infos['reference']['attribute_name']) ? $infos['reference']['attribute_name'] : $model[$infos['reference']['table']]['object_name'];
							$objectModel[$tablename]['attributes'][$column_name]['ref'] = $attr_name;
							$objectModel[$tablename]['attributes'][$attr_name]['type'] = 'refObject';
							$objectModel[$tablename]['attributes'][$attr_name]['datatype'] = $model[$infos['reference']['table']]['object_name'];
							$objectModel[$tablename]['attributes'][$attr_name]['manager'] = $model[$infos['reference']['table']]['manager_name'];
							$objectModel[$tablename]['attributes'][$attr_name]['key'] = $infos['reference']['column']; // ce qui est référencé a une clé unique
							$objectModel[$tablename]['attributes'][$attr_name]['ref'] = $column_name;
								
							$attr_name = isset($infos['reference']['ref_attribute_pf_name']) ? $infos['reference']['ref_attribute_pf_name'] : $datas['object_pf_name'];
							$attr_sf_name = isset($infos['reference']['ref_attribute_name']) ? $infos['reference']['ref_attribute_name'] : $datas['object_name'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['type'] = 'objectsList';
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['datatype'] = $datas['object_name'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['sf_name'] = $attr_sf_name;
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['manager'] = $datas['manager_name'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['id_attributes'] = $objectModel[$tablename]['primary_key'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_my_id'] = $column_name;
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_my_attr_id'] = $infos['reference']['column'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['delete_cascade'] = $infos['reference']['delete_cascade'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_default'] = $infos['data']['default'];
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_required'] = $infos['required'];
							
							$objectModel[$infos['reference']['table']]['attributes'][$attr_name]['ext_cardinal'] = 'one';
						}
					}
				}
			}
		}
		
		foreach($model as $tablename => $datas){
			if($datas['isCrossRef']){
				$column_names = array_keys($datas['columns']);
				$column_name_1 = $column_names[0];
				$column_name_2 = $column_names[1];
				$infos_1 = $datas['columns'][$column_name_1];
				$infos_2 = $datas['columns'][$column_name_2];

				$attr_name = $model[$infos_2['reference']['table']]['object_pf_name'];
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['type'] = 'objectsList';
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['datatype'] = $model[$infos_2['reference']['table']]['object_name'];
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['sf_name'] = $model[$infos_2['reference']['table']]['object_name'];
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['manager'] = $model[$infos_2['reference']['table']]['manager_name'];
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['id_attributes'] = $objectModel[$infos_2['reference']['table']]['primary_key'];
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['ext_table'] = $tablename;
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['ext_column'] = $column_name_2;
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['ext_datatype'] = $infos_2['data'];
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['ext_my_id'] = $column_name_1;
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['ext_my_attr_id'] = $infos_1['reference']['column'];
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['ext_cardinal'] = 'many';
				$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['ext_extra_attrs'] = array();

				$attr_name = $model[$infos_1['reference']['table']]['object_pf_name'];

				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['type'] = 'objectsList';
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['datatype'] = $model[$infos_1['reference']['table']]['object_name'];
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['sf_name'] = $model[$infos_1['reference']['table']]['object_name'];
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['manager'] = $model[$infos_1['reference']['table']]['manager_name'];
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['id_attributes'] = $objectModel[$infos_1['reference']['table']]['primary_key'];
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['ext_table'] = $tablename;
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['ext_column'] = $column_name_1;
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['ext_datatype'] = $infos_1['data'];
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['ext_my_id'] = $column_name_2;
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['ext_my_attr_id'] = $infos_2['reference']['column'];
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['ext_cardinal'] = 'many';
				$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['ext_extra_attrs'] = array();
				
				for($i=2; $i<count($column_names); $i++){
					$infos = $datas['columns'][$column_names[$i]];

					$prefix = isset($datas['extra_attributes_prefix']) ? $datas['extra_attributes_prefix'] : "";
					$attr_name = $prefix != "" ? $prefix."_".$column_names[$i] : $column_names[$i];
					//$attr_name = $model[$infos_2['reference']['table']]['object_name']."_".$column_names[$i];
					$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['type'] = 'crossRefValue';
					$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['datatype'] = $infos['data'];
					$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['required'] = $infos['required'];
					$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['unique'] = $infos['unique'];
					$objectModel[$infos_1['reference']['table']]['attributes'][$attr_name]['multi-unique'] = $infos['multi-unique'];
					$relative_attr = $model[$infos_2['reference']['table']]['object_pf_name'];
					$objectModel[$infos_1['reference']['table']]['attributes'][$relative_attr]['ext_extra_attrs'][] = array($attr_name, $prefix == "" ? 0 : strlen($prefix) + 1);
					
					//$attr_name = $model[$infos_1['reference']['table']]['object_name']."_".$column_names[$i];
					$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['type'] = 'crossRefValue';
					$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['datatype'] = $infos['data'];
					$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['required'] = $infos['required'];
					$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['unique'] = $infos['unique'];
					$objectModel[$infos_2['reference']['table']]['attributes'][$attr_name]['multi-unique'] = $infos['multi-unique'];
					$relative_attr = $model[$infos_1['reference']['table']]['object_pf_name'];
					$objectModel[$infos_2['reference']['table']]['attributes'][$relative_attr]['ext_extra_attrs'][] = array($attr_name, $prefix == "" ? 0 : strlen($prefix) + 1);
				}
			}
		}
		$this->objectModel = $objectModel;
		return $objectModel;
	}
	
	
	private function getPDOParams($datatype, $forBindParamMethod = false){
		$type = $datatype['type'];
		switch($type){
			case 'integer' : $params = ', \\PDO::PARAM_INT'; break;
			case 'boolean' : $params = ', \\PDO::PARAM_BOOL'; break;
			case 'string'  :
				$params = ', \\PDO::PARAM_STR';
				if($forBindParamMethod){
					$params .= ", ".$datatype['length'];
				}
				break;
			case 'enum'    :
				$params = ', \\PDO::PARAM_STR';
				if($forBindParamMethod){
					$length = 1;
					foreach($datatype['values'] as $value){
						if(strlen($value) > $length){
							$length = strlen($value);
						}
					}
					$params .= ", $length";
				}
				break;	
			case 'date' :
				$params = ', \\PDO::PARAM_STR';
				if($forBindParamMethod){
					$params .= ", 10";
				}
				break;
			case 'datetime' :
				$params = ', \\PDO::PARAM_STR';
				if($forBindParamMethod){
					$params .= ", 19";
				}
				break;
			case 'time' :
				$params = ', \\PDO::PARAM_STR';
				if($forBindParamMethod){
					$params .= ", 8";
				}
				break;
			default : $params = ', \\PDO::PARAM_STR'; break;
		}
		return $params;
	}
	
	private function getConditions($datatype){
		$type = $datatype['type'];
		switch($type){
			case 'integer' :
				if($datatype['unsigned']){
					$cdts = "(filter_var(\$value, FILTER_VALIDATE_INT) !== false) && (\$value >= 0)";
				}
				else{
					$cdts = "filter_var(\$value, FILTER_VALIDATE_INT) !== false";
				}
				break;
			case 'float' :
				if($datatype['unsigned']){
					$cdts = "(filter_var(\$value, FILTER_VALIDATE_FLOAT) !== false) && (\$value >= 0)";
				}
				else{
					$cdts = "filter_var(\$value, FILTER_VALIDATE_FLOAT) !== false";
				}
				break;
			case 'boolean' :
				$cdts = "is_bool(\$value) || (\$value === 0) || (\$value === \"0\") || (\$value === 1) || (\$value === \"1\")";
				break;
			case 'string'  :
				$cdts = "is_string(\$value) && strlen(\$value) <= ".$datatype['length'];
				break;
			case 'enum'    :
				$cdts = "in_array(\$value, array(";
				$first = true;
				foreach(explode(',', $datatype['values']) as $val){
					if(! $first){
						$cdts .= ", ";
					}
					$cdts .= "'$val'";
					$first = false;
				}
				$cdts .= "))";
				break;
			case 'date' :
				$cdts = "\\DateTime::createFromFormat('Y-m-d', \$value)";
				break;
			case 'datetime' :
				$cdts = "\\DateTime::createFromFormat('Y-m-d H:i:s', \$value) || \\DateTime::createFromFormat('Y-m-d H:i:s.u', \$value)";
				break;
			case 'time' :
				$cdts = "\\DateTime::createFromFormat('H:i:s', \$value) || \\DateTime::createFromFormat('H:i:s.u', \$value)";
				break;
			default : 
				$cdts = 'true';
				echo "!!! WARNING : type unknown : $type -- setter may be unsafe".PHP_EOL;
				break;
		}
		return $cdts;
	}
	
	private function getErrorMessage($datatype){
		$type = $datatype['type'];
		switch($type){
			case 'integer' :
				if($datatype['unsigned']){
					$msg = "Value must be an unsigned integer";
				}
				else{
					$msg = "Value must be an integer";
				}
				break;
			case 'float' :
				if($datatype['unsigned']){
					$msg = "Value must be an unsigned decimal number";
				}
				else{
					$msg = "Value must be a decimal number";
				}
				break;
			case 'boolean' :
				$msg = 'Value is not a boolean';
				break;
			case 'string'  :
				$msg = "Value is not a correct string or is too long ( < ".$datatype['length']." chars)";
				break;
			case 'enum'    :
				$msg = "value must be one of : ";
				$first = true;
				foreach(explode(',', $datatype['values']) as $val){
					if(! $first){
						$msg .= ", ";
					}
					$msg .= "'$val'";
					$first = false;
				}
				break;
			case 'date' :
				$msg = "Wrong date format";
				break;
			case 'datetime' :
				$msg = "Wrong datetime format";
				break;
			case 'time' :
				$msg = "Wrong time format";
				break;
			default :
				$msg = "";
				break;
		}
		return $msg;
	}
	
	
	public function test(){
		print_r($this->dbmodel);
		$model = $this->getObjectModel();
		print_r($model);
	}
}

class ModelGeneratorException extends Exception {
	public function ModelGeneratorException($message = '', $code = 0, $e = null) {
		parent::__construct($message, $code, $e);
	}
}
?>
