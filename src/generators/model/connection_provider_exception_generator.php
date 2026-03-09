<?php
fwrite($file, "<?php\n");
fwrite($file, "namespace $nsp"."exceptions;\n");
fwrite($file, "\n");
fwrite($file, "class DatabaseConnectionProviderException extends \\Exception {\n");
fwrite($file, "\n");
fwrite($file, "	public function DatabaseConnectionProviderException(\$e) {\n");
fwrite($file, "		parent::__construct(\$e->getMessage(), \$e->getCode(), \$e);\n");
fwrite($file, "	}\n");
fwrite($file, "}\n");
fwrite($file, "?>");
?>