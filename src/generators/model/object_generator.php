<?php
fwrite($file, "<?php\n");
fwrite($file, "namespace $namespace;\n");
fwrite($file, "\n");
fwrite($file, "class $classname extends core\\$core_classname {\n");
fwrite($file, "\n");
fwrite($file, "}\n");
fwrite($file, "?>");
?>