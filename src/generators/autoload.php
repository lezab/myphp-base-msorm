<?php
function searchFile($dir,$filename){
	$last = $dir[strlen($dir)-1];
	if($last != '/' && $last != '\\') {
		$dir .= '/';
	}
	$filelist = new DirectoryIterator($dir);
	foreach($filelist as $file) {
		if ($file->isDot()) {
			continue;
		}
		if($file->isDir()) {
			if($res = searchFile($dir.$file->getFilename(),$filename)) {
				return $res;
			} else {
				continue;
			}
		}
		if($file->getFilename() == $filename) {
			return $dir.$file->getFilename();
		}
	}
	return false;
}

function autoload($class_name){
	if($file = searchFile("generators","$class_name.php")) {
		include_once $file;
		return true;
	}
	return false;
}

spl_autoload_register("autoload");
?>