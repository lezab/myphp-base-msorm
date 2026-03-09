<?php
abstract class MGenerator{
	
	protected function camelize($word) {
		return preg_replace_callback('/(^|_|-)([A-Za-z])/', function($m) {return strtoupper($m[2]);} , $word);
	}
	
	protected static function compileTemplates($dir, $templates){
		foreach($templates as $template){
			$lines = file(__DIR__."/$dir/".$template."_template.php");
			$h = fopen(__DIR__."/$dir/".$template."_generator.php", "w");
			if(! (strpos($lines[0], "<?php") === 0)){
				fwrite($h, "<?php\n");
			}
			foreach($lines as $line){
				$line = ltrim($line);
				if(strpos($line, "##") === 0){
					$line = rtrim($line, "\r\n");
					$line = substr($line, 2);
					fwrite($h, "fwrite(\$file, \"$line\\n\");\n");
				}
				elseif(strpos($line, "#\#") === 0){
					$line = rtrim($line, "\r\n");
					$line = substr($line, 2);
					fwrite($h, "fwrite(\$file, \"$line\");\n");
				}
				elseif(strpos($line, "#") === 0){
					$line = rtrim($line, "\r\n");
					$line = substr($line, 1);
					fwrite($h, "fwrite(\$file, \"$line\");\n");
				}
				elseif(! ((strpos($line, "//") === 0) || (strpos($line, "/*") === 0))){
					$line = rtrim($line, "\r\n");
					fwrite($h, "$line\n");
				}
			}
			if(! (strpos($lines[count($lines)-1], "?>") === 0)){
				fwrite($h, "?>");
			}
			fclose($h);
		}
	}
	
	abstract public function run();
}
?>