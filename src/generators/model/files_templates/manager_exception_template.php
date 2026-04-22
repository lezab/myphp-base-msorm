##<?php
##namespace $nsp"."exceptions;
##
##
##/**
## * @class ".$manager_classname."Exception
## * @extends Exception
## * Can be cought if error occurs in the manager.
## * You can throw this exception in any method you would define in the $manager_classname class.
## * Exceptions with code 1 are due to problem detected in the manager according msorm logic.
## * Exceptions with code 2 are due to another exception caught here and propagated.
## */
##class ".$manager_classname."Exception extends \\Exception {
##
##	public function ".$manager_classname."Exception(\$message = '', \$code = 0, \$e = null) {
##		parent::__construct(\$message, \$code, \$e);
##	}
##}
#?>