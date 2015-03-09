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
 * to license@loudertechnology.com so we can send you a copy immediately.
 *
 * @category	Kumbia
 * @package		Core
 * @subpackage	CoreInfo
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @license		New BSD License
 * @version 	$Id: CoreConfig.php 5 2009-04-24 01:48:48Z gutierrezandresfelipe $
 */

/**
 * CoreInfo
 *
 * Consulta información del framework y genera la pantalla de bienvenida
 *
 * @category	Kumbia
 * @package		Core
 * @subpackage	CoreInfo
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @license		New BSD License
 * @access		public
 * @abstract
 */
abstract class CoreInfo {

	/**
	 * Muestra la pantalla de inicio de una aplicación
	 *
	 * @access 	public
	 * @static
	 */
	public static function showInfoScreen(){

		ob_start();

		Tag::setDocumentTitle('Bienvenido a Kumbia Enterprise Framework');
		Core::setInstanceName();

		echo Tag::javascriptBase();

		Tag::stylesheetLink('info');

		print "<div id='kumbia-info-content'><span id='kumbia-info-header'>Kumbia Enterprise Admin ".Core::FRAMEWORK_VERSION."</span>
		<h2>Kumbia Enterprise Framework Instance (".Core::getInstanceName()."/".Router::getApplication().") funciona!</h2><div>Para reemplazar esta p&aacute;gina
		edite el archivo <i>apps/default/controllers/application.php</i> en el DocumentRoot del servidor
		web <i>(".(isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : getcwd())."/".Core::getInstanceName().")</i>.<br><br>
		Está invitado a registrarse. El registro es opcional, al hacerlo usted obtiene:
		<ul>
			<li>Recibir información actualizada sobre proyectos y servicios</li>
			<li>Recibir información de ofertas y promociones</li>
			<li>Públicar temas y mensajes en los foros</li>
			<li>Descargar previews de proyectos y documentación</li>
		</ul></div>
		<hr color='#eaeaea'><div align='center' id='kumbia-info-footer'>
		<a href='http://www.loudertechnology.com/site/projects/license'>Licencia</a> |
		<a href='http://www.loudertechnology.com/'>Louder Technology</a> ".date("Y")."</div>";
		View::setContent(ob_get_contents());
		ob_end_clean();
		View::xhtmlTemplate();

	}

}