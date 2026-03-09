<?php
fwrite($file, "<?php\n");
fwrite($file, "namespace $nsp"."exceptions;\n");
fwrite($file, "\n");
fwrite($file, "\n");
fwrite($file, "/**\n");
fwrite($file, " * @class ".$manager_classname."Exception\n");
fwrite($file, " * @extends Exception\n");
fwrite($file, " * Can be cought if error occurs in the manager.\n");
fwrite($file, " * You can throw this exception in any method you would define in the $manager_classname class.\n");
fwrite($file, " * Exceptions with code 1 are due to problem detected in the manager according msorm logic.\n");
fwrite($file, " * Exceptions with code 2 are due to another exception caught here and propagated.\n");
fwrite($file, " */\n");
fwrite($file, "class ".$manager_classname."Exception extends \\Exception {\n");
fwrite($file, "\n");
fwrite($file, "	public function ".$manager_classname."Exception(\$message = '', \$code = 0, \$e = null) {\n");
fwrite($file, "		parent::__construct(\$message, \$code, \$e);\n");
fwrite($file, "	}\n");
fwrite($file, "}\n");
fwrite($file, "?>");
?>