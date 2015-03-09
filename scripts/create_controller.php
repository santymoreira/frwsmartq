<?php

/**
 * Kumbia Enterprise Framework
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kumbia@kumbia.org so we can send you a copy immediately.
 *
 * @category Kumbia
 * @package Scripts
 * @copyright Copyright (c) 2005-2008 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license New BSD License
 */

require 'Library/Kumbia/Core/CoreClassPath/CoreClassPath.php';
require 'Library/Kumbia/Autoload.php';

class CreateController extends Script {

	public function __construct(){

		$posibleParameters = array(
			'name=s' => '--name nombre \t\tNombre del controlador',
			'application=s' => '--application nombre \tNombre de la aplicaci&oacute;n [opcional]',
			'force' => '--force \t\tForza a que se reescriba el controlador [opcional]',
			'help' => '--help \t\t\tMuestra esta ayuda'
		);

		$this->parseParameters($posibleParameters);

		if($this->isReceivedOption('help')){
			$this->showHelp($posibleParameters);
			return;
		}

		$this->checkRequired(array("name"));

		$name = $this->getOption('name');
		$application = $this->getOption('application');
		if($application==''){
			$application = 'default';
		}
		Router::setActiveApplication($application);
		Core::reloadMVCLocations();
		if($name){
			$controllersDir = Core::getActiveControllersDir();
			$code = "<?php\n\nclass ".Utils::camelize($name)."Controller extends ApplicationController {\n\n\tpublic function indexAction(){\n\n\t}\n\n}\n\n";
			if(!file_exists("$controllersDir/{$name}_controller.php")||$this->isReceivedOption('force')){
				file_put_contents("$controllersDir/{$name}_controller.php", $code);
			} else {
	 			throw new ScriptException("Ya existe el nombre del controlador en la aplicaci&oacute;n '$application'");
			}
		} else {
			throw new ScriptException("Debe indicar el nombre del controlador");
		}
	}

}

try {
	$script = new CreateController();
}
catch(CoreException $e){
	print get_class($e)." : ".$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	print "Exception : ".$e->getMessage()."\n";
}
