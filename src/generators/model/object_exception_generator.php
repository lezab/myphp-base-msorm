<?php
fwrite($file, "<?php\n");
fwrite($file, "namespace $nsp"."exceptions;\n");
fwrite($file, "\n");
fwrite($file, "\n");
fwrite($file, "/**\n");
fwrite($file, " * @class ".$classname."Exception\n");
fwrite($file, " * @extends Exception\n");
fwrite($file, " * Can be cought if error occurs in the object.\n");
fwrite($file, " * You can throw this exception in any method you would define in the $classname class.\n");
fwrite($file, " */\n");
fwrite($file, "class ".$classname."Exception extends \\Exception {\n");
fwrite($file, "\n");
fwrite($file, "	public function ".$classname."Exception(\$message) {\n");
fwrite($file, "		parent::__construct(\$message);\n");
fwrite($file, "	}\n");
fwrite($file, "}\n");
fwrite($file, "?>");
?>