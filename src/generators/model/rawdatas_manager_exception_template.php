##<?php
##namespace $nsp"."exceptions;
##
##
##/**
## * @class RawDatasManagerException
## * @extends Exception
## * Can be cought if error occurs in the manager.
## * You can throw this exception in any method you would define in the RawDatasManager class.
## * Exceptions with code 1 are due to problem detected in the manager according msorm logic.
## * Exceptions with code 2 are due to another exception caught here and propagated.
## */
##class RawDatasManagerException extends \\Exception {
##
##	public function RawDatasManagerException(\$message = '', \$code = 0, \$e = null) {
##		parent::__construct(\$message, \$code, \$e);
##	}
##}
#?>