<?php
fwrite($file, "<?php\n");
if(isset($namespace)){
fwrite($file, "namespace $namespace\core;\n");
}
else{
fwrite($file, "namespace core;\n");
}
fwrite($file, "\n");
fwrite($file, "class MSORM {\n");
fwrite($file, "	\n");
fwrite($file, "	public static function getVersion(){\n");
fwrite($file, "     	return '$msorm_version';\n");
fwrite($file, "	}\n");
fwrite($file, "}\n");
fwrite($file, "?>");
?>