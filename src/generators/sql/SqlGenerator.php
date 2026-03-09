<?php
class SqlGenerator{
	
	public $file;
	public $tables;
	public $database;
	
	
	public function __construct($project_name, $tables, $database){
		$directory = "./projects/$project_name/sql";
		if(! file_exists($directory)){
			mkdir($directory, 0755, true);
		}
		$this->file = "$directory/".str_replace("/", "-", $project_name).".sql";
		$this->tables = $tables;
		$this->database = $database;
	}
	
	function run(){

		echo "Generating Sql file\n";
		echo "... \n";

		$tables = $this->tables;
		$database = $this->database;

		$filename = $this->file;
		$file = fopen($filename, "w+");

		fwrite($file, "SET time_zone = \"+00:00\";\n");

		if(isset($database['name'])){
			$db_name = $database['name'];
			fwrite($file, "-- --------------------------------------------------------\n");
			fwrite($file, "-- Definition de la base\n");
			fwrite($file, "-- --------------------------------------------------------\n");
			fwrite($file, "CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\n");
			fwrite($file, "USE `$db_name`;\n");
			fwrite($file, "\n\n");
		}

		foreach($tables as $tablename => $datas){
			fwrite($file, "-- --------------------------------------------------------\n");
			fwrite($file, "-- Table $tablename\n");
			fwrite($file, "-- --------------------------------------------------------\n");
			fwrite($file, "CREATE TABLE IF NOT EXISTS `$tablename` (\n");

			foreach($datas['columns'] as $column_name => $infos){
				switch(strtolower($infos['data']['type'])){

					case 'bigint' : $datatype = 'bigint'; break;
					case 'int' : $datatype = 'int'; if($infos['data']['size']!= ""){$datatype .= "(".$infos['data']['size'].")";} break;
					case 'integer' : $datatype = 'int'; if($infos['data']['size']!= ""){$datatype .= "(".$infos['data']['size'].")";} break;
					case 'tinyint' : $datatype = 'tinyint'; break;
					case 'smallint' : $datatype = 'smallint'; break;

					case 'bool' : $datatype = 'boolean'; break;
					case 'boolean' : $datatype = 'boolean'; break;

					case 'char' : $datatype = 'char('.$infos['data']['size'].')'; break;
					case 'varchar' : $datatype = 'varchar('.$infos['data']['size'].')'; break;
					case 'text' :
						$datatype = 'text';
						if($infos['data']['size'] != ""){
							$datatype .= "(".$infos['data']['size'].")";
						}
						break;
					case 'enum' :
						$datatype = 'enum(';
						$values = $infos['data']['values'];
						$values = explode(",", $values);
						foreach($values as $value){
							$value = trim($value);
							$datatype .= "'$value',";
						}
						$datatype = substr($datatype,0,-1);
						$datatype .= ')';
						break;

					case 'date' : $datatype = 'date'; break;
					case 'datetime' : $datatype = 'datetime'; break;
					case 'time' : $datatype = 'time'; break;

					case 'double' : $datatype = 'float'; break;
					case 'float' : $datatype = 'float'; break;
					case 'real' : $datatype = 'real'; break;
					case 'decimal' : $datatype = 'float'; break;
				}
				$unsigned = $infos['data']['unsigned'] ? " unsigned" : "";
				$auto = $infos['data']['auto_increment'] ? " auto_increment" : "";
				$notnull = $infos['required'] && (! $infos['primary_key']) ? " NOT NULL" : "";

				fwrite($file, "		`$column_name` $datatype$unsigned$notnull$auto,\n");
			}
			$pk = ""; $first = true;
			foreach($datas['columns'] as $column_name => $infos){
				if($infos['primary_key']){
					if(! $first){
						$pk .= ',';
					}
					$pk .= "`$column_name`";
					$first = false;
				}
			}
			if($pk != ""){
				fwrite($file, "		PRIMARY KEY ($pk),\n");
			}
			foreach($datas['columns'] as $column_name => $infos){
				if($infos['unique']){
					fwrite($file, "		UNIQUE KEY `".$column_name."_UNIQUE` (`$column_name`),\n");
				}
			}

			$index = 1;
			foreach($datas['uniques'] as $columns){
				fwrite($file, "		UNIQUE KEY `".$tablename."_UNIQUE$index` (");
				foreach($columns as $column_name){
					if($index > 1){
						fwrite($file, ',');
					}
					fwrite($file, "`$column_name`");
					$index++;
				}
				fwrite($file, "),\n");
			}

			$stats = fstat($file);
			ftruncate($file, $stats['size']-2);
			fclose($file);

			$file = fopen($filename, "a+");
			fwrite($file, "\n) ENGINE=InnoDB  DEFAULT CHARSET=utf8;\n");
			//fwrite($file, "\n\n");
		}
		fclose($file);

		echo "OK.\n\n";
	}
}

class SqlGeneratorException extends Exception {
	public function SqlGeneratorException($message = '', $code = 0, $e = null) {
		parent::__construct($message, $code, $e);
	}
}
?>