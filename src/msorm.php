#!/usr/bin/php
<?php

$version = "8.0.3";

function version(){
	global $version;
	?>

	My Simple ORM v<?=$version?>
	
	<?php
	echo PHP_EOL;
	exit(0);
}

function usage(){
	global $version;
	?>
	
		My Simple ORM v<?=$version?>
		
		
		Usage :
		msorm.php -h
	  	msorm.php -g sql -p myproject
	  	msorm.php -g model -p myproject -o outputdir
	  	msorm.php --generate model --project=myproject --update
		msorm.php --check -p myproject
		msorm.php --clean
		
		<args>	
			-h, -?, --help :
			
					leads to this message
			
			-v, --version :
			
					Gives the version
					
			-g, --generate :
					<options>
						model : generates model files
						sql   : generates sql file
					
					should be followed by :
					-p <project_name> : the name of a subdirectory of projects directory, containing a file description.xml
			
					optional (for model generation only) :
					-u : tells the model must be updated only (only core classes are overwritten)
					-o <destination> : output folder where model classes are generated
						
			-p, --project :
			
				The name of a subdirectory of projects directory, containing a file description.xml which describes the database schema
				
			-o, --output :
			
				The output folder for model classes.
				If not specified model classes are stored in a "model" subdirectory of project folder
				
			-u, --update :
			
				If set, only core classes are overwritten (except __db_params.conf.php file) to preserve what has already been added by user.
				
			--check :
			
					should be followed by 
						-p <project_name> : checks if file "projects/<project_name>/description.xml" is well written.
						
			--clean :
			
					deletes both sql file and model directory for all projects found
	  	

	  		Arguments with options accept these formats ("a" is the short format argument, "aaa" is the long format argument, "option" is the option):
			-a option
			--aaa option
			--aaa=option

	<?php
	echo PHP_EOL;
	exit(0);
}

include(__DIR__.'/generators/autoload.php');

function generateModel($project_name, $output = null, $update_only = false){

	if(! checkFile($project_name)){
		exit(0);
	}
	$file = "./projects/$project_name/description.xml";
	
	echo "Processing from file : $file".PHP_EOL;
	echo "...".PHP_EOL;
	
	if(! $model = load($file)){
		exit(0);
	}
	
	$tables = $model['tables'];
	$database = $model['database'];
		
	try{
		$output = isset($output) ? $output : "./projects/$project_name/model";
		
		$generator = new ModelGenerator($project_name, $tables, $database, $output, $update_only);
		//$generator->test();
		$generator->run();
		
	}
	catch(ModelGeneratorException $e){
		echo "Model class files generation interrupted :".PHP_EOL.PHP_EOL;
		echo $e->getMessage().PHP_EOL;
		exit(0);
	}
	
	echo "Process complete.".PHP_EOL;
}

function generateSql($project_name){
	
	if(! checkFile($project_name)){
		exit(0);
	}
	$file = "./projects/$project_name/description.xml";
	
		
	echo "Processing from file : $file".PHP_EOL;
	echo "...".PHP_EOL;
	
	if(! $model = load($file)){
		exit(0);
	}
	
	$tables = $model['tables'];
	$database = $model['database'];
	
	try{
		$generator = new SqlGenerator($project_name, $tables, $database);
		$generator->run();
	}
	catch(SqlGeneratorException $e){
		echo "\Sql file generation interrupted".PHP_EOL.PHP_EOL;
		echo $e->getMessage().PHP_EOL;
		exit(0);
	}
	
	echo "Process complete.".PHP_EOL;
}

function load($file){
	try{
		$loader = new ModelLoader($file);
		$model = $loader->getModel();
	}
	catch (ModelLoaderException $e){
		echo "Errors found in $file :".PHP_EOL.PHP_EOL;
		echo $e->getMessage().PHP_EOL;
		echo "Process interrupted".PHP_EOL.PHP_EOL;
		return false;
	}
	
	return $model;
}

function checkFile($project_name){
	$directory = "./projects/$project_name";
	if(!is_dir($directory)){
		echo "Directory $directory not found".PHP_EOL;
		echo "Make sure the project's name is well written".PHP_EOL.PHP_EOL;
		return false;
	}
	
	$file = "$directory/description.xml";
	if(!is_file($file)){
		echo "Description file not found in project's directory".PHP_EOL;
		echo "Make sure you named your project's description file description.xml and the file is located in your project directory".PHP_EOL.PHP_EOL;
		return false;
	}
	return true;
}

function check($project_name){
	if(!checkFile($project_name)){
		exit(0);
	}	
	
	$file = "./projects/$project_name/description.xml";
	
	echo "Checking file : $file".PHP_EOL;
	echo "...".PHP_EOL;
	
	try{
		$loader = new ModelLoader($file);
		
		if(! $loader->checkAndRecord()){
			echo "Checking ends on errors :".PHP_EOL.PHP_EOL;
			echo $loader->getErrors().PHP_EOL;
			exit(0);
		}
	}
	catch (ModelLoaderException $e){
		echo "Checking ends on errors :".PHP_EOL.PHP_EOL;
		echo $e->getMessage().PHP_EOL;
		exit(0);
	}
	
	echo PHP_EOL."Checking complete : OK".PHP_EOL.PHP_EOL;
}

function deleteDirectory($dir) {
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path){
		if($path->isDir() && !$path->isLink()){
			if(! rmdir($path->getPathname())){
				return false;
			}
		}
		else{
			if(!  unlink($path->getPathname())){
				return false;
			}
		}
	}
	if(! rmdir($dir)){
		return false;
	}
	return true;
}

function cleanAll(){
	if(!is_dir("./projects/")){
		echo "Directory projects/ not found".PHP_EOL;
		exit(0);
	}

	echo "Cleaning projects :".PHP_EOL;
	echo "...".PHP_EOL.PHP_EOL;
	
	$maindir = dir("./projects/");
	
	$globalsuccess = true;
	
	while($directory = $maindir->read()) {
		$project_name = $directory;
		
		if(is_dir("./projects/".$directory) && ($directory != ".") && ($directory != "..")){
			$success = true;
			echo "	$project_name ...";
			if(is_dir("./projects/".$directory."/sql")){
				if(! deleteDirectory("./projects/".$directory."/sql")){
					$success = false;
					$globalsuccess = false;
				}
			}
			if(is_dir("./projects/".$directory."/model")){
				if(! deleteDirectory("./projects/".$directory."/model")){
					$success = false;
					$globalsuccess = false;
				}

			}
			if($success){
				echo " OK".PHP_EOL;
			}
			else{
				echo "FAILED".PHP_EOL;
			}
		}
	}
	$maindir->close();
	if($globalsuccess){
		echo PHP_EOL."Cleaning complete : OK".PHP_EOL.PHP_EOL;
	}
	else{
		echo PHP_EOL."Cleaning not complete".PHP_EOL.PHP_EOL;
	}
	exit(0);
}


/**
 * Parses $args command line and return them as an array
 *
 * Supports:
 * -e
 * -e <value>
 * --long-param
 * --long-param=<value>
 * --long-param <value>
 * <value>
 *
 * @param array $args
 */
function read_args($args){
	$result = array();
	
	array_shift($args);
	reset($args);
	while ($param = current($args)){
		if ($param[0] == '-') {
			$param_name = substr($param, 1);
			$value = true;
			if ($param_name[0] == '-') {
				// long-opt (--<param>)
				$param_name = substr($param_name, 1);
				if (strpos($param, '=') !== false) {
					// value specified inline (--<param>=<value>)
					list($param_name, $value) = explode('=', substr($param, 2), 2);
				}
			}
			// check if next parameter is a descriptor or a value
			$next_param = next($args);
			if ($value === true && $next_param !== false && $next_param[0] != '-'){
				$value = $next_param;
				next($args);
			}
			$result[$param_name] = $value;
		}
		else {
			// param doesn't belong to any option
			$result[] = $param;
			next($args);
		}
	}
	return $result;
}



/**************************************************************************************/
/**************************************************************************************/
/*                                                                                    */
/*                                        MAIN                                        */
/*                                                                                    */
/**************************************************************************************/
/**************************************************************************************/

$known_options = array(
	'?', 'h', 'help',
	'v', 'version',
	'check',
	'clean',
	'g', 'generate',
	'p', 'project',
	'o', 'output',
	'u', 'update');

$args = read_args($argv);


if(empty($args) || isset($args['?']) || isset($args['h']) || isset($args['help'])){
	usage();
}
elseif(isset($args['v']) || isset($args['version'])){
	version();
}
elseif(isset($args['check'])){
	$project = isset($args['p']) ? $args['p'] : (isset($args['project']) ? $args['project'] : null);
	if(isset($project)){
		check($project);
	}
	else{
		echo "Missing parameter --project (-p) when using --check".PHP_EOL;
		usage();
	}
}
elseif(isset($args['clean'])){
	cleanAll();
}
elseif(isset($args['g']) || isset($args['generate'])){
	$mode = isset($args['g']) ? $args['g'] : $args['generate'];
	$project = isset($args['p']) ? $args['p'] : (isset($args['project']) ? $args['project'] : null);
	if(isset($project)){
		if($mode == "model"){
			$output = isset($args['o']) ? $args['o'] : (isset($args['output']) ? $args['output'] : null);
			$update = isset($args['u']) ? true : (isset($args['update']) ? true : false);
			generateModel($project, $output, $update);
		}
		elseif($mode == "sql"){
			generateSql($project);
		}
		else{
			echo "Unknown option \"$mode\" for parameter --generate (-g)".PHP_EOL;
			usage();
		}
	}
	else{
		echo "Missing parameter --project (-p) when using --generate (or -g) ".PHP_EOL;
		usage();
	}
}
else{
	foreach(array_keys($args) as $arg){
		if(!in_array($arg, $known_options)){
			echo "Unknown arg : $arg".PHP_EOL;
		}
	}
	usage();
}
?>