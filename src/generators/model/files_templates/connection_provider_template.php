##<?php
##namespace $nsp"."core;
##
##use \\$nsp"."exceptions\\DatabaseConnectionProviderException;
##
##class DatabaseConnectionProvider {
##	
##	protected static \$pdo;
##	
##	/**
##	 * @throws DatabaseConnectionProviderException
##	 * @return \\PDO
##	 */
##	public static function getInstance(){
##		if (!isset(self::\$pdo)){
##			
##			include(__DIR__.\"/__db_params.conf.php\");
##			
##			try {
##				self::\$pdo = new \\PDO(\"mysql:host=\$db_url;port=\$db_port;dbname=\$db_name\", \$db_user, \$db_password, array( \\PDO::ATTR_PERSISTENT => true, \\PDO::ATTR_ERRMODE => \\PDO::ERRMODE_EXCEPTION));
##				self::\$pdo->query(\"SET NAMES 'UTF8'\");
##			}
##			catch (\\PDOException \$e) {
##				throw new DatabaseConnectionProviderException(\$e);
##			}
##		}
##     	return self::\$pdo;
##	}
##}
#?>