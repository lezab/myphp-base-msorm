<?php
fwrite($file, "<?php\n");
fwrite($file, "namespace $namespace;\n");
fwrite($file, "\n");
fwrite($file, "class $manager_classname extends core\\$manager_core_classname {\n");
fwrite($file, "\n");
fwrite($file, "}\n");
fwrite($file, "?>");
?>