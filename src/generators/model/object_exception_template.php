##<?php
##namespace $nsp"."exceptions;
##
##
##/**
## * @class ".$classname."Exception
## * @extends Exception
## * Can be cought if error occurs in the object.
## * You can throw this exception in any method you would define in the $classname class.
## */
##class ".$classname."Exception extends \\Exception {
##
##	public function ".$classname."Exception(\$message) {
##		parent::__construct(\$message);
##	}
##}
#?>