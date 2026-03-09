<?php
fwrite($file, "<?php\n");
fwrite($file, "namespace $namespace;\n");
fwrite($file, "\n");
fwrite($file, "class RawDatasManager extends core\\RawDatasManagerCore {\n");
fwrite($file, "\n");
fwrite($file, "}\n");
fwrite($file, "?>");
?>