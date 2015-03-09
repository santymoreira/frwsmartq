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
 * @category 	Kumbia
 * @package 	Tag
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright 	Copyright (C) 2007-2007 Roger Jose Padilla Camacho (rogerjose81 at gmail.com)
 * @copyright 	Copyright (c) 2007-2008 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @copyright 	Copyright (c) 2007-2008 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license 	New BSD License
 * @version 	$Id: Tag.php 33 2009-05-04 04:52:28Z gutierrezandresfelipe $
 */

/**
 * Tag
 *
 * Este componente actua como una biblioteca de etiquetas que permite generar
 * tags XHTML en la presentación de una aplicación mediante métodos estáticos
 * PHP predefinidos flexibles que integran tecnología del lado del cliente
 * como CSS y Javascript.
 *
 * @category 	Kumbia
 * @package		Tag
 * @copyright 	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright 	Copyright (C) 2007-2007 Roger Jose Padilla Camacho (rogerjose81 at gmail.com)
 * @copyright 	Copyright (c) 2007-2008 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @copyright 	Copyright (c) 2007-2008 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license 	New BSD License
 * @abstract
 */
abstract class Tag {

	/**
	 * Indica si se debe usar localizacion
	 *
	 * @var boolean
	 */
	private static $_useLocale = true;

	/**
	 * Valores de los componentes
	 *
	 * @var array
	 */
	private static $_displayValues = array();

	/**
	 * Titulo del Documento HTML
	 *
	 * @var string
	 */
	private static $_documentTitle = '';

	/**
	 * Establece el valor de un componente de UI
	 *
	 * @param string $id
	 * @param string $value
	 */
	public static function displayTo($id, $value){
		/*if(is_object($value)||is_array($value)||is_resource($value)){
			throw new TagException('Solo valores escalares pueden ser asiganados a los componentes UI');
		}*/
		self::$_displayValues[$id] = $value;
	}

	/**
	 * Obtiene el valor de un componente tomado
	 * del mismo valor del nombre del campo en $_displayValues
	 * del mismo nombre del controlador o el indice en
	 * $_POST
	 *
	 * @param string $name
	 * @return mixed
	 * @static
	 */
	public static function getValueFromAction($name){
		if(@isset(self::$_displayValues[$name])){
			return self::$_displayValues[$name];
		} else {
			if(@isset($_POST[$name])){
				if(get_magic_quotes_gpc()==false){
					return $_POST[$name];
				} else {
					return stripslashes($_POST[$name]);
				}
			} else {
				$controller = Dispatcher::getController();
				if(isset($controller->$name)){
					return $controller->$name;
				} else {
					return "";
				}
			}
		}
	}

	/**
	 * Crea un enlace en una aplicacion respetando las convenciones del framework
	 *
	 * @param string $action
	 * @param string $text
	 * @return string
	 */
	public static function linkTo($action, $text=''){
		if(func_num_args()>2){
			$numberArguments = func_num_args();
			$action = Utils::getParams(func_get_args(), $numberArguments);
		}
		if(is_array($action)){
			if(isset($action['confirm'])&&$action['confirm']){
				$action['onclick'] = "if(!confirm(\"{$action['confirm']}\")) { return false; }; ".$action['onclick'];
				unset($action['confirm']);
			}
			$code = "<a href='".Utils::getKumbiaUrl($action)."' ";
			if(!isset($action['text'])||!$action['text']){
				$action['text'] = $action[1];
			}
			foreach($action as $key => $value){
				if(!is_integer($key)&&$key!='text'){
					$code.=" $key='$value' ";
				}
			}
			$code.='>'.$action['text'].'</a>';
			return $code;
		} else {
			if($text==="") {
				$text = str_replace('_', ' ', $action);
				$text = str_replace('/', ' ', $text);
				$text = ucwords($text);
			}
			return "<a href='".Utils::getKumbiaUrl($action)."'>".$text."</a>";
		}
	}

	/**
	 * Crea un enlace a una accion dentro del controlador Actual
 	 *
	 * @param string $action
	 * @param string $text
	 * @return string
	 */
	static public function linkToAction($action, $text=''){
		if(func_num_args()>2){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
		}
		$controller_name = Router::getController();
		if(is_array($action)){
			if(isset($action['confirm'])){
				$action['onclick'] = "if(!confirm(\"{$action['confirm']}\")) if(document.all) event.returnValue = false; else event.preventDefault(); ".$action['onclick'];
				unset($action['confirm']);
			}
			$code = "<a href='".Utils::getKumbiaUrl("$controller_name/{$action[0]}")."' ";
			foreach($action as $key => $value){
				if(!is_integer($key)){
					$code.=' '.$key.'=\''.$value.'\'';
				}
			}
			$code.=">{$action[1]}</a>";
			return $code;
		} else {
			if(!$text) {
				$text = str_replace('_', ' ', $action);
				$text = str_replace('/', ' ', $text);
				$text = ucwords($text);
			}
			return "<a href='".Utils::getKumbiaUrl("$controller_name/$action")."'>$text</a>";
		}
	}

	/**
	 * Permite ejecutar una acci&oacute;n en la vista actual dentro de un contenedor
	 * HTML usando AJAX
	 *
	 * confirm: Texto de Confirmaci&oacute;n
	 * success: Codigo JavaScript a ejecutar cuando termine la petici&oacute;n AJAX
	 * before: Codigo JavaScript a ejecutar antes de la petici&oacute;n AJAX
	 * oncomplete: Codigo JavaScript que se ejecuta al terminar la petici&oacute;n AJAX
	 * update: Que contenedor HTML ser&oacute; actualizado
	 * action: Accion que ejecutar&oacute; la petici&oacute;n AJAX
	 * text: Texto del Enlace
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	static public function linkToRemote(){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['update'])||!$params['update']){
			$update = isset($params[2]) ? $params[2] : "";
		} else {
			$update = $params['update'];
			unset($params['update']);
		}
		if(!isset($params['text'])||!$params['text']){
			$text = isset($params[1]) ? $params[1] : "";
		} else {
			$text = $params['text'];
		}
		if(!$text){
			$text = $params[0];
		}
		if(!isset($params['action'])||!$params['action']){
			$action = $params[0];
		} else {
			$action = $params['action'];
		}
		$code = "<a href=\"#\" onclick=\"";
		if(isset($params['confirm'])){
			$code.= "if(confirm('{$params['confirm']}')){";
		}
		$code.= "new Ajax.Request(Utils.getKumbiaURL('$action'), {";
		$call = array();
		if(isset($params['asynchronous'])){
			if($params['asynchronous']=='false'||!$params['asynchronous']){
				$call[] = "asynchronous: false";
			} else {
				$call[] = "asynchronous: true";
			}
			unset($params['asynchronous']);
		}
		if(isset($params['onLoading'])){
			$call[] = "onLoading: function(){ {$params['onLoading']} }";
			unset($params['onLoading']);
		}
		if(isset($params['onSuccess'])){
			$call[] = "onSuccess: function(transport){ {$params['onSuccess']} }";
			unset($params['onSuccess']);
		}
		if(isset($params['onFailure'])){
			$call[] = "onFailure: function(transport){ {$params['onFailure']} }";
			unset($params['onFailure']);
		}
		if(isset($params['onComplete'])){
			$call[] = "onComplete: function(transport){ {$params['onComplete']}; $('$update').update(transport.responseText); }";
			unset($params['onComplete']);
		} else {
			$call[] = "onComplete: function(transport){ $('$update').update(transport.responseText); }";
		}
		if(count($call)>0){
			$code.= join(',', $call);
		}
		$code.="})";
		if(isset($params['confirm'])){
			$code.=" }";
			unset($params['confirm']);
		}
		$code.="; return false\"";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.=" $key='$value' ";
			}
		}
		return $code.">$text</a>";
	}

	/**
	 * Caja de Texto que autocompleta los resultados
	 *
	 * @param mixed $params
	 * @return string
	 * @static
	 */
	public static function textFieldWithAutocomplete($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$value = self::getValueFromAction($params[0]);
		$hash = md5(mt_rand(1, 100));
		if(!isset($params['name'])||!$params['name']) {
			$params['name'] = $params[0];
		}
		if(!isset($params['after_update'])||!$params['after_update']) {
			$params['after_update'] = "function(){}";
		}
		if(!isset($params['id'])||!$params['id']) {
			$params['id'] = $params['name'] ? $params['name'] : $params[0];
		}
		if(!isset($params['message'])||!$params['message']) {
			$params['message'] = "Consultando...";
		}
		if(!isset($params['param_name'])||!$params['param_name']) {
			$params['param_name'] = $params[0];
		}
		$code = "<input type='text' id='{$params[0]}' name='{$params['name']}'";
		foreach($params as $key => $value){
			if(!in_array($key, array('id', 'name', 'param_name', 'message', 'action', 'after_update'))){
				if(!is_integer($key)){
					$code.="$key='$value' ";
				}
			}
		}
		$instancePath = Core::getInstancePath();
		$code.= " />
		<span id='indicator$hash' style='display: none'><img src='{$instancePath}img/spinner.gif' alt='{$params['message']}'/></span>
		<div id='{$params[0]}_choices' class='autocomplete'></div>
		<script type='text/javascript'>
		// <![CDATA[
		new Ajax.Autocompleter(\"{$params[0]}\", \"{$params[0]}_choices\", Utils.getKumbiaURL(\"{$params['action']}\"), { minChars: 2, indicator: 'indicator$hash', afterUpdateElement : {$params['after_update']}, paramName: '{$params['param_name']}'});
		// ]]>
		</script>";
		return $code;
	}

	/**
	 * Crea un TextArea
	 *
	 * @access public
	 * @param array $configuration
	 * @return string
	 * @static
	 */
	public static function textArea($configuration){
		if(func_num_args()==1){
			$configuration = func_get_args();
                        
			$value = self::getValueFromAction($configuration[0]);
                        
			return "<textarea id=\"{$configuration[0]}\" name=\"{$configuration[0]}\" cols=\"40\" rows=\"25\">$value</textarea>\r\n";
		} else {
			$numberArguments = func_num_args();
			$configuration = Utils::getParams(func_get_args(), $numberArguments);
			if(!isset($configuration['name'])||$configuration['name']) {
				$configuration['name'] = $configuration[0];
			}
			if(!isset($configuration['cols'])||!$configuration['cols']) {
				$configuration['cols'] = 40;
			}
			if(!isset($configuration['rows'])||!$configuration['rows']) {
				$configuration['rows'] = 25;
			}
			if(!isset($configuration['value'])){
				$value = self::getValueFromAction($configuration[0]);
			} else {
				$value = $configuration['value'];
			}
			return "<textarea id=\"{$configuration['name']}\" name=\"{$configuration['name']}\" cols=\"{$configuration['cols']}\" rows=\"{$configuration['rows']}\">$value</textarea>\r\n";
		}
	}

	/**
	 * Crea una caja de texto que solo acepta numeros
	 *
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function numericField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if(!isset($params['onkeydown'])) {
			$params['onkeydown'] = "valNumeric(event)";
		} else {
			$params['onkeydown'].=";valNumeric(event)";
		}
		$code = "<input type='text' id='{$params[0]}' value='$value' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea una caja de password que solo acepta numeros
	 *
	 * @param 	mixed $params
	 * @return 	string
	 */
	public static function numericPasswordField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$value = self::getValueFromAction($params);
		if(!$params[0]) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(!$value) {
			$value = isset($params['value']) ? $params['value'] : "";
		}
		if(!isset($params['onkeydown'])) {
			$params['onkeydown'] = "valNumeric(event)";
		} else {
			$params['onkeydown'].=";valNumeric(event)";
		}
		$code = "<input type='password' id='{$params[0]}' value='$value' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un campo que acepta solo fechas
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function dateField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']) {
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if($value){
			$ano = substr($value, 0, 4);
			$mes = substr($value, 5, 2);
			$dia = substr($value, 8, 2);
		} else {
			$ano = date('Y');
			$mes = 0;
			$dia = 0;
		}

		if(isset($params['useDummy'])&&$params['useDummy']){
			$useDummy = true;
			unset($params['useDummy']);
		} else {
			$useDummy = false;
		}
		$attributes = array();
		foreach($params as $_key => $_value){
			if(in_array($_key, array('name'))==false&&!is_integer($_key)){
				$attributes[] = "$_key='$_value'";
			}
		}
		$code ="<table ".join(" ", $attributes)."><tr><td>";
		if(self::$_useLocale){
			$locale = Locale::getApplication();
			if($locale->isDefaultLocale()==false){
				$meses = array();
				$i = 1;
				foreach($locale->getAbrevMonthList() as $month){
					$meses[sprintf('%02s', $i)] = ucfirst($month);
					$i++;
				}
			}
		}
		if(!isset($meses)){
			$meses = array(
				'01' => 'Ene', '02' => 'Feb',
				'03' => 'Mar', '04' => 'Abr',
				'05' => 'May', '06' => 'Jun',
				'07' => 'Jul', '08' => 'Ago',
				'09' => 'Sep', '10' => 'Oct',
				'11' => 'Nov', '12' => 'Dic',
			);
		}
		if($useDummy){
			$displayJS = 'if(this.selectedIndex>0){$(\''.$params[0].'_day\').show();$(\''.$params[0].'_year\').show();}else{$(\''.$params[0].'_day\').hide();$(\''.$params[0].'_year\').hide();$(\''.$params[0].'\').value = \'\'};';
		} else {
			$displayJS = '';
		}
		$code .= "<select name='{$params[0]}_month' id='{$params[0]}_month' onchange=\"$displayJS$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value\">";
		if($useDummy){
			$code.="<option value='@'>Sel...</option>\n";
		}
		foreach($meses as $numero_mes => $nombre_mes){
			if($numero_mes==$mes){
				$code.="<option value='$numero_mes' selected='selected'>$nombre_mes</option>\n";
			} else {
				$code.="<option value='$numero_mes'>$nombre_mes</option>\n";
			}
		}
		$code.="</select></td><td>";

		if($useDummy){
			$display = 'style="display:none"';
		} else {
			$display = '';
		}
		$code.="<select name='{$params[0]}_day' id='{$params[0]}_day' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value;\" $display>";
		for($i=1;$i<=31;$i++){
			$n = $i<10 ? '0'.$i : $i;
			if($n==$dia){
				$code.="<option value='$n' selected='selected'>$n</option>\n";
			} else {
				$code.="<option value='$n'>$n</option>\n";
			}
		}
		$code.="</select></td><td>";
		if($useDummy){
			$display = 'style="display:none"';
		} else {
			$display = '';
		}
		$code.="<select name='{$params[0]}_year' id='{$params[0]}_year' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value\" $display>\n";
		if(isset($params['startYear'])){
			$startYear = $params['startYear'];
		} else {
			$startYear = 1900;
		}
		if(isset($params['finalYear'])){
			$finalYear = $params['finalYear'];
		} else {
			$finalYear = date('Y')+5;
		}
		for($i=$finalYear;$i>=$startYear;$i--){
			if($i==$ano){
				$code.="<option value='$i' selected='selected'>$i</option>\n";
			} else {
				$code.="<option value='$i'>$i</option>\n";
			}
		}
		$code.="</select></td><td>";
		$code.="</table>";
		$code.="<input type='hidden' id='{$params[0]}' name='{$params[0]}' value='$value' />";

		return $code;
	}

	/**
	 * Crea un campo para la captura de fechas que permite personalizar
	 * los meses de acuerdo a la localizacion
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @param 	Traslate $traslate
	 * @return 	string
	 * @static
	 */
	public static function localeDateField($params, $traslate){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']) {
			$params['name'] = $params[0];
		}

		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}

		if($value){
			$ano = substr($value, 0, 4);
			$mes = substr($value, 5, 2);
			$dia = substr($value, 8, 2);
		} else {
			$ano = date('Y');
			$mes = 0;
			$dia = 0;
		}

		$attributes = array();
		foreach($params as $_key => $_value){
			if(in_array($_key, array("name"))==false&&!is_integer($_key)){
				$attributes[] = "$_key = '$_value'";
			}
		}

		$code ="<table ".join(" ", $attributes)."><tr><td>";

		$meses = array(
			'01' => $traslate->_('Ene'),
			'02' => $traslate->_('Feb'),
			'03' => $traslate->_('Mar'),
			'04' => $traslate->_('Abr'),
			'05' => $traslate->_('May'),
			'06' => $traslate->_('Jun'),
			'07' => $traslate->_('Jul'),
			'08' => $traslate->_('Ago'),
			'09' => $traslate->_('Sep'),
			'10' => $traslate->_('Oct'),
			'11' => $traslate->_('Nov'),
			'12' => $traslate->_('Dic'),
		);
		$code .= "<select name='{$params[0]}_month' id='{$params[0]}_month' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value\">";
		foreach($meses as $numero_mes => $nombre_mes){
			if($numero_mes==$mes){
				$code.="<option value='$numero_mes' selected='selected'>$nombre_mes</option>\n";
			} else {
				$code.="<option value='$numero_mes'>$nombre_mes</option>\n";
			}
		}
		$code.="</select></td><td>";

		$code.="<select name='{$params[0]}_day' id='{$params[0]}_day' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value\">";
		for($i=1;$i<=31;$i++){
			$n = sprintf("%02s", $i);
			if($n==$dia){
				$code.="<option value='$n' selected='selected'>$n</option>\n";
			} else {
				$code.="<option value='$n'>$n</option>\n";
			}
		}
		$code.="</select></td><td>";

		$code.="<select name='{$params[0]}_year' id='{$params[0]}_year' onchange=\"$('{$params[0]}').value = $('{$params[0]}_year').options[$('{$params[0]}_year').selectedIndex].value+'-'+$('{$params[0]}_month').options[$('{$params[0]}_month').selectedIndex].value+'-'+$('{$params[0]}_day').options[$('{$params[0]}_day').selectedIndex].value\">";
		if(isset($params['startYear'])){
			$startYear = $params['startYear'];
		} else {
			$startYear = 1900;
		}
		if(isset($params['finalYear'])){
			$finalYear = $params['finalYear'];
		} else {
			$finalYear = date('Y')+5;
		}
		for($i=$finalYear;$i>=$startYear;$i--){
			if($i==$ano){
				$code.="<option value='$i' selected='selected'>$i</option>\n";
			} else {
				$code.="<option value='$i'>$i</option>\n";
			}
		}
		$code.="</select></td><td>";
		$code.="</table>";

		$code.="<input type='hidden' id='{$params[0]}' name='{$params[0]}' value='$value' />";

		return $code;
	}

	/**
	 * Crea un combo que toma los valores de un array
	 *
	 * @param 	mixed $params
	 * @param 	string $data
	 * @return 	string
	 */
	public static function selectStatic($params='', $data=''){
		if(func_num_args()>1){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
			if(is_array($params)){
				$value = "";
				if(!isset($params['value'])){
					$value = self::getValueFromAction($params[0]);
				} else {
					$value = $params['value'];
				}
				$code ="<select id='{$params[0]}' name='{$params[0]}' ";
				if(!isset($params['dummyValue'])){
					$dummyValue = '@';
				} else {
					$dummyValue = $params['dummyValue'];
					unset($params['dummyValue']);
				}
				if(!isset($params['dummyText'])){
					$dummyText = 'Seleccione...';
				} else {
					$dummyText = $params['dummyText'];
					unset($params['dummyText']);
				}
				if(is_array($params)){
					foreach($params as $at => $val){
						if(!is_integer($at)){
							if(!is_array($val)){
								$code.="$at='".$val."' ";
							}
						}
					}
				}
				$code.=">\r\n";
				if(isset($params['use_dummy'])&&$params['use_dummy']){
					$code.="\t<option value='$dummyValue'>$dummyText</option>\r\n";
					unset($params['use_dummy']);
				} else {
					if(isset($params['useDummy'])&&$params['useDummy']){
						$code.="\t<option value='$dummyValue'>$dummyText</option>\r\n";
						unset($params['useDummy']);
					}
				}
				if(is_array($params[1])){
					foreach($params[1] as $k => $d){
						if($k==$value){
							$code.="\t<option value='$k' selected='selected'>$d</option>\r\n";
						} else {
							$code.="\t<option value='$k'>$d</option>\r\n";
						}
					}
				}
				$code.= "</select>\r\n";
			}
		} else {
			$code = "<select id='$params' name='$params'></select>";
		}
		return $code;
	}

	/**
	 * Crea una lista SELECT
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @param 	array $data
	 * @static
	 */
	public static function select($params='', $data=''){
		
               if(func_num_args()>1){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
		}
		if(is_array($params)){
			if(!isset($params['value'])){
				$value = self::getValueFromAction($params[0]);
			} else {
				$value = $params['value'];
			}
			$callback = false;
			if(isset($params['option_callback'])){
				if(strpos($params['option_callback'], ".")){
					$callback = explode(".", $params['option_callback']);
				} else {
					$callback = $params['option_callback'];
				}
				if(is_callable($callback)==false){
					throw new TagException("El option_callback no es valido");
				}
				unset($params['option_callback']);
			}
			$code ="<select id='{$params[0]}' name='{$params[0]}' ";
			if(is_array($params)){
				foreach($params as $at => $val){
					if(!is_integer($at)){
						if(!is_array($val)&&!in_array($at, array('using', 'use_dummy'))){
							$code.="$at='".$val."' ";
						}
					}
				}
			}
			$code.=">\r\n";

			if(!isset($params['dummyValue'])){
				$dummyValue = '@';
			} else {
				$dummyValue = $params['dummyValue'];
				unset($params['dummyValue']);
			}
			if(!isset($params['dummyText'])){
				$dummyText = 'Seleccione...';
			} else {
				$dummyText = $params['dummyText'];
				unset($params['dummyText']);
			}

			if(isset($params['use_dummy'])&&$params['use_dummy']==true){
				$code.="\t<option value='$dummyValue'>$dummyText</option>\r\n";
			} else {
				if(isset($params['useDummy'])&&$params['useDummy']==true){
					$code.="\t<option value='$dummyValue'>$dummyText...</option>\r\n";
				}
			}
			if(is_object($params[1])){
				if(!isset($params['using'])){
					throw new TagException("Debe indicar el parámetro 'using' para el helper Tag::select()");
				}
				$using = explode(",", $params['using']);
				foreach($params[1] as $o){
					if($callback==false){
						if($value==$o->readAttribute($using[0])){
							$code.="\t<option selected='selected' value='".trim($o->readAttribute($using[0]))."'>".trim($o->readAttribute($using[1]))."</option>\r\n";
						} else {
							$code.="\t<option value='".trim($o->readAttribute($using[0]))."'>".trim($o->readAttribute($using[1]))."</option>\r\n";
						}
					} else {
						$code.=call_user_func_array($callback, array($o, $value));
					}
				}
			} else {
				if(is_array($params[1])){
					foreach($params[1] as $d){
						$code.="\t<option value='{$d[0]}'>{$d[1]}</option>\r\n";
					}
				} else {
					throw new TagException("La collección de opciones no es valida");
				}
			}
			$code.= "</select>\r\n";
		} else {
			$code = "<select id='$params' name='$params'></select>";
		}
		return $code;
	}

	/**
	 * Crea una lista SELECT cuyos textos de las opciones estan localizados
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @param 	array $data
	 * @param 	Traslate $traslate
	 * @return 	string
	 * @static
	 */
	public static function localeSelect($params='', $data='', $traslate){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(is_array($params)){
			if(!isset($params['value'])){
				$value = self::getValueFromAction($params[0]);
			} else {
				$value = $params['value'];
			}
			$callback = false;
			if(isset($params['option_callback'])){
				if(strpos($params['option_callback'], '.')){
					$callback = explode('.', $params['option_callback']);
				} else {
					$callback = $params['option_callback'];
				}
				if(is_callable($callback)==false){
					throw new TagException('El option_callback no es valido');
				}
				unset($params['option_callback']);
			}
			$code ="<select id='{$params[0]}' name='{$params[0]}' ";
			if(is_array($params)){
				foreach($params as $at => $val){
					if(!is_integer($at)){
						if(!is_array($val)&&!in_array($at, array('using', 'use_dummy'))){
							$code.="$at='".$val."' ";
						}
					}
				}
			}
			$code.=">\r\n";
			if(isset($params['use_dummy'])&&$params['use_dummy']==true){
				$code.="\t<option value='@'>Seleccione...</option>\r\n";
			}
			if(is_object($params[1])){
				if(!isset($params['using'])){
					throw new TagException("Debe indicar el par&aacute;metro 'using' para el helper Tag::select()");
				}
				$using = explode(",", $params['using']);
				foreach($params[1] as $o){
					if($callback==false){
						if($value==$o->readAttribute($using[0])){
							$code.="\t<option selected='selected' value='{$o->readAttribute($using[0])}'>".$traslate->_($o->readAttribute($using[1]))."</option>\r\n";
						} else {
							$code.="\t<option value='{$o->readAttribute($using[0])}'>".$traslate->_($o->readAttribute($using[1]))."</option>\r\n";
						}
					} else {
						$code.=call_user_func_array($callback, array($o, $value));
					}
				}
			} else {
				foreach($params[1] as $d){
					$code.="\t<option value='{$d[0]}'>{$d[1]}</option>\r\n";
				}
			}
			$code.= "</select>\r\n";
		} else {
			$code.="<select id='$params' name='$params'></select>";
		}
		return $code;
	}

	/**
	 * Crea una lista SELECT con datos de modelos y de arrays
	 *
	 * @access 	public
	 * @param 	string $name
	 * @param 	string $modelData
	 * @param 	array $arrayData
	 * @return 	string
	 * @static
	 */
	public static function selectMixed($name='', $modelData='', $arrayData=''){
		if(func_num_args()>1){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
		}
		if(is_array($params)){
			if(!isset($params['value'])){
				$value = self::getValueFromAction($params[0]);
			} else {
				$value = $params['value'];
			}
			$callback = false;
			if(isset($params['option_callback'])){
				if(strpos($params['option_callback'], ".")){
					$callback = explode(".", $params['option_callback']);
				} else {
					$callback = $params['option_callback'];
				}
				if(is_callable($callback)==false){
					throw new TagException("El option_callback no es valido");
				}
				unset($params['option_callback']);
			}
			$code ="<select id='{$params[0]}' name='{$params[0]}' ";
			if(is_array($params)){
				foreach($params as $_attribute => $_value){
					if(!is_integer($_attribute)){
						if(!is_array($_value)&&!in_array($_attribute, array('using', 'use_dummy'))){
							$code.="$_attribute='$_value' ";
						}
					}
				}
			}
			$code.=">\r\n";
			if(isset($params['use_dummy'])&&$params['use_dummy']==true){
				$code.="\t<option value='@'>Seleccione...</option>\r\n";
			}
			if(is_array($arrayData)){
				foreach($arrayData  as $k => $d){
					if($k==$value){
						$code.="\t<option value='$k' selected='selected'>$d</option>\r\n";
					} else {
						$code.="\t<option value='$k'>$d</option>\r\n";
					}
				}
			}
			if(is_object($params[1])){
				if(!isset($params['using'])){
					throw new TagException("Debe indicar el par&aacute;metro 'using' para el helper Tag::select()");
				}
				$using = explode(",", $params['using']);
				foreach($params[1] as $o){
					if($callback==false){
						if($value==$o->readAttribute($using[0])){
							$code.="\t<option selected='selected' value='{$o->readAttribute($using[0])}'>{$o->readAttribute($using[1])}</option>\r\n";
						} else {
							$code.="\t<option value='{$o->readAttribute($using[0])}'>{$o->readAttribute($using[1])}</option>\r\n";
						}
					} else {
						$code.=call_user_func_array($callback, array($o, $value));
					}
				}
			} else {
				foreach($params[1] as $d){
					$code.="\t<option value='{$d[0]}'>{$d[1]}</option>\r\n";
				}
			}
			$code.= "</select>\r\n";
		} else {
			$code.="<select id='$params' name='$params'></select>";
		}
		return $code;
	}

	/**
	 * Carga el framework javascript y funciones auxiliares
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function javascriptBase(){
		$application = Router::getActiveApplication();
		$controllerName = Router::getController();
		$actionName = Router::getAction();
		$module = Router::getModule();
		$id = Router::getId();
		$path = Core::getInstancePath();
                $code=self::includejQueryMin();
                $code.=self::includejQueryPluginBase();
                $code.=self::includejQueryUI();
                $code.=self::includejQueryUIPluginBase();
                $code.=self::includejQueryIncPlugin();
                //$code.=self::javascriptLibrary('framework/scriptaculous/protoculous');
		//$code.= "<script type='text/javascript' src='".$path."javascript/core/base.js'></script>\r\n";
		$code.= "<script type='text/javascript' src='".$path."javascript/core/validations.js'></script>\r\n";
		$code.= "<script type='text/javascript' src='".$path."javascript/core/main.php?app=$application&module=$module&path=".urlencode($path)."&controller=$controllerName&action=$actionName&id=$id'></script>\r\n";

                $code.= "<script type='text/javascript' src='".$path."javascript/core/jquery-ui-timepicker-addon.js'></script>\r\n";
                $code.= "<script type='text/javascript' src='".$path."javascript/core/jquery.qtip.min.js'></script>\r\n";
                $code.= "<script type='text/javascript' src='".$path."javascript/core/fechas_horas.js'></script>\r\n";
                $code.= "<script type='text/javascript' src='".$path."javascript/core/ajax.js'></script>\r\n";
                $code.= "<script type='text/javascript' src='".$path."js/highcharts.js'></script>\r\n";
                //$code.= '<script type="text/javascript" src="http://api.html5media.info/1.1.5/html5media.min.js"></script>';      //comentado por nelson
                $code.= "<script type='text/javascript' src='".$path."js/html5media.min.js'></script>\r\n";
                $code.= "<script type='text/javascript' src='".$path."js/jquery.blockUI.js'></script>\r\n";
                
                //$code.=self::includeLinkCSS('themes/ui.jqgrid');        //añadido por nelson ya que antes no se cargada de primera
                //$code.=self::includePluginjQuery('jqGrid/len/grid.locale-sp','jqGrid/jquery.jqGrid.min');       //añadido por nelson ya que antes no se cargada de primera
        

                //jquery-ui-timepicker-addon.js

                /*$code.=self::javascriptLibrary('framework/scriptaculous/protoculous');
                $code.=self::javascriptLibrary('base');
                $code.=self::javascriptLibrary('validations');
                $code.= "<script type='text/javascript' src='".$path."javascript/core/main.php?app=$application&module=$module&path=".urlencode($path)."&controller=$controllerName&action=$actionName&id=$id'></script>\r\n";*/
                
		return $code;
	}

	/**
	 * Genera una etiqueta script que apunta a un archivo JavaScript
	 * respetando las rutas y convenciones de Kumbia
 	 *
	 * @param string $src
	 * @param string $cache
	 * @return string
	 */
	public static function javascriptInclude($src='', $cache=true){
		if($src==""){
			$src = Router::getController();
		}
		$src.='.js';
		if(!$cache){
			$cache = mt_rand(0, 999999);
			$src.="?nocache=".$cache;
		}
		$instancePath = Core::getInstancePath();
		return "<script type='text/javascript' src='{$instancePath}javascript/$src'></script>\r\n";
         
	}

	/**
	 * Incluye una etiqueta SCRIPT con plugins de jQuery regular
	 *
	 * @param string $srcs
	 */
        public static function includePluginjQuery($src){
              $instancePath = Core::getInstancePath();
              $arraySrc=array();
              if(func_num_args()==1)
                $arraySrc[]=$src;
              else
                $arraySrc=func_get_args();
              
              $include='';
              $coma='';
              $code="<script type='text/javascript'>";
                      
              $code.='var urls=[';
              for($i=0;$i<count($arraySrc);$i++){
                  
                 
                  $code.="$coma'{$instancePath}javascript/core/framework/jQuery/{$arraySrc[$i]}.js'";
                  $coma=',';
                
              }
              $code.="];
                       
                               jQuery.include(urls);
                       </script>\n";
              
            
           
           return $code;
        }
        /**
	 * Incluye una etiqueta css
	 *
	 * @param string $src
	 */
        public static function includeLinkCSS($src){
              $instancePath = Core::getInstancePath();
              $arraySrc=array();
              if(func_num_args()==1)
                $arraySrc[]=$src;
              else
                $arraySrc=func_get_args();
              
              $include='';
              $coma='';
              $code="<script type='text/javascript'>";

              $code.='var urls=[';
              for($i=0;$i<count($arraySrc);$i++){

                  
                  $code.="$coma'{$instancePath}css/{$arraySrc[$i]}.css'";
                  $coma=',';
                
              }
              $code.="];

                               jQuery.include(urls);
                       </script>\n";


           
           return $code;
        }
        /**
	 * Incluye una etiqueta SCRIPT con un plugin de jQuery minimizada
	 *
	 * @param string $src
	 */
        public static function includePluginjQueryMin($src){
            $src='core/framework/jQuery/'.$src.'.min';
            return self::javascriptInclude($src);
        }
        
        /**
	 * Incluye una etiqueta SCRIPT de jQuery regular
	 *
	 * @param string $version
	 */
        public static function  includejQuery($version=''){
            if(trim($version)==""){
                $version='1.3.2';
                //$version='1.4.4';
                //$version='1.5.1';
            }
            $src='core/framework/jQuery/jquery-'.$version;
            return self::javascriptInclude($src);
        }
        public static function  includejQueryUI($version=''){
            if(trim($version)==""){
                $version='1.7.2.custom';
            }
            $src='core/framework/jQuery/jquery-ui-'.$version.'.min';
            return self::javascriptInclude($src);
        }

        public static function  includejQueryUIPluginBase(){
             $code='';
            $src='core/framework/jQuery/';
            $code.=self::javascriptInclude($src.'jquery.layout');
            $code.=self::javascriptInclude($src.'ui.multiselect');
           // $code.=self::javascriptInclude($src.'themeswitchertoll');
            return $code;
        }

        public static function  includejQueryPluginBase(){
             $code='';
            $src='core/framework/jQuery/';
            $code.=self::javascriptInclude($src.'jquery.tablednd');
            $code.=self::javascriptInclude($src.'jquery.contextmenu');
            
           

            //jquery validate
            $code.=self::javascriptInclude($src.'validate/jquery.validate');
            $code.=self::javascriptInclude($src.'validate/additional-methods');
            $code.=self::javascriptInclude($src.'validate/localization/messages_es');

            //funciones genarales
            $code.=self::javascriptInclude($src.'functions');
            return $code;
        }
        /**
	 * Incluye una etiqueta SCRIPT de jQuery minimizada
	 *
	 * @param string $version
	 */

        public static function includejQueryMin($version=''){
            if(trim($version)==""){
                $version='1.3.2';
                //$version='1.4.4';
                //$version='1.5.1';
            }
            $src='core/framework/jQuery/jquery-'.$version;
            return self::javascriptMinifiedInclude($src);
        }
     
        public static function includejQueryIncPlugin(){
            $src='core/framework/jQuery/jquery.includeMany-1.2.0';
            return self::javascriptInclude($src);
        }
        /**
	 * Incluye una etiqueta SCRIPT con un recurso javascript minizado
	 *
	 * @param string $src
	 */
	public static function javascriptMinifiedInclude($src){
		if(class_exists('Jsmin')==false){
			require 'Library/Kumbia/Tag/Jsmin/Jsmin.php';
		}
		$jsSource = 'public/javascript/'.$src.'.js';
		$jsMinSource = 'public/javascript/'.$src.'.min.js';
		if(file_exists($jsMinSource)==false){
			$minified = Jsmin::minify(file_get_contents($jsSource));
			file_put_contents($jsMinSource, $minified);
		} else {
			if(filemtime($jsSource)>filemtime($jsMinSource)){
				$minified = Jsmin::minify(file_get_contents($jsSource));
				file_put_contents($jsMinSource, $minified);
			}
		}
		return self::javascriptInclude($src.'.min');
	}

	/**
 	 * Crea un boton de submit tipo imagen para el formulario actual
	 *
	 * @access 	public
	 * @param 	string $caption
	 * @param 	string $src
	 * @return 	string
	 * @static
	 */
	public static function submitImage($caption, $src){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['caption'])){
			$params['caption'] = $params[0];
		}
		if(!isset($params['src'])){
			$params['src'] = $params[1];
		}
		$code = "<input type='image' src='{$params['src']}' value='{$params['caption']}' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un boton HTML
	 *
	 * @return string
	 * @static
	 */
	public static function button(){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['value'])){
			$params['value'] = $params[0];
		}
		if(isset($params['id'])&&$params['id']&&!isset($params['name'])) {
			$params['name'] = $params['id'];
		}
		if(!isset($params['id'])) {
			$params['id'] = isset($params['name']) ? $params['name'] : "";
		}
		$code = "<input type='button' ";
		foreach($params as $key => $value){
			if(!is_integer($key)&&$key!=$params){
				$code.="$key=\"$value\" ";
			}
		}
		return $code." />\r\n";
	}

	/**
	 * Agrega una etiqueta script que apunta a un archivo en public/javascript/kumbia
	 *
	 * @param string $src
	 * @return string
	 */
	public static function javascriptLibrary($src){
		
                $instancePath = Core::getInstancePath();
		return "<script type='text/javascript' src='".$instancePath."javascript/core/$src.js'></script>\r\n";
                
	}

	/**
	 * Permite incluir una imagen dentro de una vista respetando
	 * las convenciones de directorios y rutas en Kumbia
	 *
	 * @param string $img
	 * @return string
	 * @static
	 */
	public static function image($img){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$code = "";
		if(!isset($params['src'])||!$params['src']){
			$instancePath = Core::getInstancePath();
			$code.="<img src='{$instancePath}img/{$params[0]}' ";
		} else {
			$code.="<img src='{$params['src']}' ";
			unset($params['src']);
		}
		if(!isset($params['alt'])||!$params['alt']) {
			$params['alt'] = "";
		}
		if(is_array($params)){
			if(!$params['alt']){
				$params['alt'] = "";
			}
			foreach($params as $at => $val){
				if(!is_integer($at)){
					$code.="$at=\"".$val."\" ";
				}
			}
		}
		$code.= "/>\r\n";
		return $code;
	}

	/**
	 * Permite generar un formulario remoto
	 *
	 * @param 	mixed $params
	 * @return 	string
	 */
	public static function formRemote($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['action'])||!$params['action']) {
			$params['action'] = $params[0];
		}
		$params['callbacks'] = array();
		$id = Router::getId();
		if(isset($params['complete'])&&$params['complete']){
			$params['callbacks'][] = ' complete: function(){ '.$params['complete'].' }';
		}
		if(isset($params['before'])&&$params['before']){
			$params['callbacks'][] = ' before: function(){ '.$params['before'].' }';
		}
		if(isset($params['success'])&&$params['success']){
			$params['callbacks'][] = ' success: function(){ '.$params['success'].' }';
		}
		if(isset($params['required'])&&$params['required']){
			$requiredFields = array();
			foreach($params['required'] as $required){
				$requiredFields[] = "'".$required."'";
			}
			$requiredFields = join(',', $requiredFields);
			$code = "<form action='".Utils::getKumbiaUrl($params['action'].'/'.$id)."' method='post'
			onsubmit='if(validaForm(this,new Array({$requiredFields}))){ return ajaxRemoteForm(this,\"{$params['update']}\",{".join(",",$params['callbacks'])."}); } else{ return false; }'";
			unset($params['required']);
		} else{
			if(!isset($params['update'])){
				throw new ViewException('Debe indicar el contenedor a actualizar con el parámetro "update"');
			}
			$code = "<form action='".Utils::getKumbiaUrl($params['action'].'/'.$id)."' method='post'
			onsubmit='return ajaxRemoteForm(this, \"{$params['update']}\", { ".join(",", $params['callbacks'])." });'";
		}
		foreach($params as $at => $val){
			if(!is_integer($at)&&(!in_array($at, array('action', 'complete', 'before', 'success', 'callbacks')))){
				$code.="$at=\"".$val."\" ";
			}
		}
		return $code.=">\r\n";
	}

	/**
	 * Crea un boton de submit para el formulario remoto actual
	 *
	 * @param string $caption
	 * @return string
	 */
	public static function submitRemote($caption){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!$params['caption']) {
			$params['caption'] = $params[0];
		}
		$params['callbacks']	= array();
		if($params['complete']){
			$params['callbacks'][] = " complete: function(){ ".$params['complete']." }";
		}
		if($params['before']){
			$params['callbacks'][] = " before: function(){ ".$params['before']." }";
		}
		if($params['success']){
			$params['callbacks'][] = " success: function(){ ".$params['success']." }";
		}
		$code = "<input type='submit' value='{$params['caption']}' ";
		foreach($params as $at => $value){
			if(!is_integer($at)&&(!in_array($at, array("action", "complete", "before", "success", "callbacks", "caption", "update")))){
				$code.="$at='$value' ";
			}
		}
		$code.=" onclick='return ajaxRemoteForm(this.form, \"{$params['update']}\")' />\r\n";
		return $code;
	}

	/**
	 * Establece una etiqueta meta
	 *
	 * @access public
	 * @param string $name
	 * @param string $content
	 * @static
	 */
	public static function setMeta($name, $content){
		MemoryRegistry::prepend('CORE_META_TAGS', "<meta name='$name' content='$content'/>\r\n");
	}

	/**
	 * Imprime las metas cargadas
	 *
	 * @access public
	 * @static
	 */
	public static function getMetas(){
		$metas = MemoryRegistry::get('CORE_META_TAGS');
		if(is_array($metas)){
			foreach($metas as $meta){
				print $meta;
			}
		}
	}

	/**
	 * Establece el titulo del documento HTML
	 *
	 * @access public
	 * @param string $title
	 * @static
	 */
	public static function setDocumentTitle($title){
		self::$_documentTitle = $title;
	}

	/**
	 * Agrega al final un texto del titulo actual del documento HTML
	 *
	 * @access public
	 * @param string $title
	 * @static
	 */
	public static function appendDocumentTitle($title){
		self::$_documentTitle.= $title;
	}

	/**
	 * Agrega al prinicipio un texto del titulo actual del documento HTML
	 *
	 * @access public
	 * @param string $title
	 * @static
	 */
	public static function prependDocumentTitle($title){
		self::$_documentTitle = $title.self::$_documentTitle;
	}

	/**
	 * Devuelve el titulo del documento HTML
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function getDocumentTitle(){
		return '<title>'.self::$_documentTitle.'</title>'."\r\n";
	}

	/**
	 * Agrega una etiqueta link para incluir un archivo CSS respetando
	 * las rutas y convenciones de Kumbia
	 *
	 * @access public
	 * @param string $src
	 * @param boolean $useVariables
	 * @static
	 */
	public static function stylesheetLink($src='', $useVariables=false){
		if(!$src) {
			$src = Router::getController();
		}
		$instancePath = Core::getInstancePath();
		if($useVariables==true){
			if($instancePath){
				$kb = substr($instancePath, 0, strlen($instancePath)-1);
			} else {
				$kb = '/';
			}
			$code = "<link rel='stylesheet' type='text/css' href='".$instancePath."css.php?c=$src&p=$kb' />\r\n";
		} else {
			$code = "<link rel='stylesheet' type='text/css' href='".$instancePath."css/$src.css' />\r\n";
		}
		MemoryRegistry::prepend('CORE_CSS_IMPORTS', $code);
		return $code;
	}

        public static function stylesheetBase(){
               $code='';
               $code.=self::stylesheetLink('style', true);
               $code.=self::stylesheetLink('themes/redmond/jquery-ui-1.7.1.custom');
               //$code.=self::stylesheetLink('themes/redmond/jquery-ui-1.7.2.custom');
               $code.=self::stylesheetLink('themes/ui.multiselect');
               $code.=self::stylesheetLink('themes/redmond/estilo');
               return $code;
        }

	/**
	 * Imprime los CSS cargados mediante Tag::stylesheetLink
	 *
	 * @return unknown
	 */
	public static function stylesheetLinkTags(){
		$styleSheets = MemoryRegistry::get('CORE_CSS_IMPORTS');
		$code = '';
		if(is_array($styleSheets)){
			foreach($styleSheets as $css){
				$code.= $css;
			}
		}
		return $code;
	}

	/**
	 * Elimina los tags agregados a la salida
	 *
	 * @access public
	 * @static
	 */
	public static function removeStylesheets(){
		MemoryRegistry::reset('CORE_CSS_IMPORTS');
	}

	/**
	 * Crea una etiqueta de formulario
	 *
	 * @access 	public
	 * @param 	string $action
	 * @return 	string
	 * @static
	 */
	public static function form($action){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$id = Router::getId();
		if($action==""){
			$action = isset($params['action']) ? $params['action'] : "";
		}
		if(!isset($params['method'])||!$params['method']) {
			$params['method'] = "post";
		}
		if(isset($params['confirm'])&&$params['confirm']){
			$params['onsubmit'].=$params['onsubmit'].";if(!confirm(\"{$params['confirm']}\")) { return false; }";
			unset($params['confirm']);
		}
		if(is_null($id)||$id===""){
			$str = "<form action='".Utils::getKumbiaUrl("$action")."' ";
		} else {
			$str = "<form action='".Utils::getKumbiaUrl("$action/$id")."' ";
		}
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$str.= "$key='$value' ";
			}
		}
		return $str.">\r\n";
	}

	/**
	 * Etiqueta para cerrar un formulario
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function endForm(){
		return "</form>\r\n";
	}

	/**
 	 * Crea una caja de Texto
 	 *
 	 * @access 	public
 	 * @param 	mixed $params
 	 * @return 	string
 	 * @static
 	 */
	static public function textField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		$code = "<input type='text' id='{$params[0]}' value='$value' ";
		foreach($params as $_key => $_value){
			if(!is_integer($_key)){
				$code.="$_key='$_value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un componente para capturar Passwords
	 *
	 * @param 	mixed $params
	 * @return 	string
	 */
	static public function passwordField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!is_array($params)){
			return "<input type='password' id='$params' name='$params'/>\r\n";
		} else {
			if(!isset($params[0])) {
				$params[0] = $params['id'];
			}
			if(!isset($params['name'])||!$params['name']) {
				$params['name'] = $params[0];
			}
			if(!isset($params['value'])){
				$params['value'] = self::getValueFromAction($params[0]);
			}
			$code = "<input type='password' id='{$params[0]}' ";
			foreach($params as $key => $value){
				if(!is_integer($key)){
					$code.="$key='$value' ";
				}
			}
			$code.=" />\r\n";
			return $code;
		}
	}

	/**
	 * Crea un botón de submit para el formulario actual
	 *
	 * @access	public
	 * @param	string $caption
	 * @return	string
	 * @static
	 */
	public static function submitButton($caption){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['caption'])) {
			$params['caption'] = $params[0];
		} else {
			if(!$params['caption']) {
				$params['caption'] = $params[0];
			}
		}
		$code = "<input type='submit' value='{$params['caption']}' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un CheckBox
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function checkboxField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$value = self::getValueFromAction($params[0]);
		if(!isset($params[0])||!$params[0]) {
			$params[0] = isset($params['id']) ? $params['id'] : "";
		}
		if(!isset($params['name'])||!$params['name']) {
			$params['name'] = $params[0];
		}

		if($value!==""&&!is_null($value)&&$value){
			$params['checked'] = "checked";
                      
		}
		$code = "<input type='checkbox' id='{$params[0]}' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea una caja de texto que acepta solo texto en Mayuscula
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function textUpperField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||$params['name']==""){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if(!isset($params['onblur'])){
			$params['onblur'] = "keyUpper2(this)";
		} else {
			$params['onblur'].=";keyUpper2(this)";
		}
		$code = "<input type='text' id='{$params[0]}' value='$value' ";
		foreach($params as $_key => $_value){
			if(!is_integer($_key)){
				$code.="$_key='$_value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un Input tipo Text
	 *
	 * @access 	public
	 * @param 	string $name
	 * @return 	string
	 * @static
	 */
	public static function fileField($name){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		$value = self::getValueFromAction($name);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		$code = "<input type='file' id='{$params[0]}' ";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea un input tipo Radio
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function radioField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		if(isset($params[1])&&is_array($params[1])){
			$code = "<table><tr>";
			foreach($params[1] as $key=>$text){
				if($value==$key){
					$code.= "<td><input type='radio' name='{$params[0]}' id='{$params[0]}' value='$key' checked='checked' /></td><td>$text</td>\r\n";
				} else {
					$code.= "<td><input type='radio' name='{$params[0]}' id='{$params[0]}' value='$key' /></td><td>$text</td>\r\n";
				}
			}
			$code.= "</tr></table>";
		} else {
			$code = "<input type='radio' name='{$params[0]}' value='$value' ";
			foreach($params as $key => $value){
				if(!is_integer($key)){
					$code.="$key='$value' ";
				}
			}
			$code.="/>";
		}
		return $code;
	}

	/**
	 * Crea un Componente Oculto
	 *
	 * @access 	public
	 * @param 	mixed $params
	 * @return 	string
	 * @static
	 */
	public static function hiddenField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])){
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])){
			$params['name'] = $params[0];
		}
		if(!isset($params['value'])){
			$params['value'] = self::getValueFromAction($params[0]);
		}
		$code="<input type='hidden' id='{$params[0]}'";
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$code.="$key='$value' ";
			}
		}
		$code.=" />\r\n";
		return $code;
	}

	/**
	 * Crea una opcion de un SELECT
	 *
	 * @access 	public
	 * @param	string $value
	 * @param 	string $text
	 * @static
	 */
	public static function option($value, $text){
		if(func_num_args()>1){
			$numberArguments = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArguments);
			$value = $params[0];
			$text = $params[1];
		} else {
			$value = '';
		}
		$code = "<option value='$value' ";
		if(is_array($params)){
			foreach($params as $at => $val){
				if(!is_integer($at)){
					$code.="$at='".$val."' ";
				}
			}
		}
		$code.= ">$text</option>\r\n";
		return $code;
	}

	/**
	 * Crea un componente para Subir Imagenes
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function uploadImage(){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params['name'])){
			$params['name'] = $params[0];
		}
		$code.="<span id='{$params['name']}_span_pre'>
		<select name='{$params[0]}' id='{$params[0]}' onchange='show_upload_image(this)'>";
		$code.="<option value='@'>Seleccione...\n";
		foreach(scandir("public/img/upload") as $file){
			if($file!='index.html'&&$file!='.'&&$file!='..'&&$file!='Thumbs.db'&&$file!='desktop.ini'){
				$nfile = str_replace('.gif', '', $file);
				$nfile = str_replace('.jpg', '', $nfile);
				$nfile = str_replace('.png', '', $nfile);
				$nfile = str_replace('.bmp', '', $nfile);
				$nfile = str_replace('_', ' ', $nfile);
				$nfile = ucfirst($nfile);
				if(urlencode("upload/$file")==$params['value']){
					$code.="<option selected='selected' value='upload/$file' style='background: #EAEAEA'>$nfile</option>\n";
				} else {
					$code.="<option value='upload/$file'>$nfile</option>\n";
				}
			}
		}
		$code.="</select> <a href='#{$params['name']}_up' name='{$params['name']}_up' id='{$params['name']}_up' onclick='enable_upload_file(\"{$params['name']}\")'>Subir Imagen</a></span>
		<span style='display:none' id='{$params['name']}_span'>
		<input type='file' id='{$params['name']}_file' onchange='upload_file(\"{$params['name']}\")' />
		<a href='#{$params['name']}_can' name='{$params['name']}_can' id='{$params['name']}_can' style='color:red' onclick='cancel_upload_file(\"{$params['name']}\")'>Cancelar</a></span>
		";
		if(!isset($params['width'])){
			$params['width'] = 128;
		}
		if($params['value']){
			$params['style']="border: 1px solid black;margin: 5px;".$params['value'];
		} else {
			$params['style']="border: 1px solid black;display:none;margin: 5px;".$params['value'];
		}
		$code.="<div>".Tag::image(urldecode($params['value']), 'width: '.$params['width'], 'style: '.$params['style'], 'id: '.$params['name']."_im")."</div>";
		return $code;
	}

	/**
	 * Hace que un elemento reciba items con drag-n-drop
	 *
	 * @access 	public
	 * @param 	string $obj
	 * @param 	string $action
	 * @return 	string
	 * @static
	 */
	public static function setDroppable($obj, $action=''){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!$params['name']){
			$params['name'] = $params[0];
		}
		return "<script type=\"text/javascript\">Droppables.add('{$params['name']}', {hoverclass: '{$params['hover_class']}',onDrop:{$params['action']}})</script>";
	}

	/**
	 * Hace que un elemento reciba items con drag-n-drop
	 *
	 * @access 	public
	 * @param 	string $action
	 * @param 	double $seconds
	 * @return 	string
	 * @static
	 */
	public static function redirectTo($action, $seconds = 0.01){
		$seconds*=1000;
		return "<script type=\"text/javascript\">setTimeout('window.location=\"?/$action\"', $seconds)</script>";
	}

	/**
	 * Imprime una etiqueta TR cada $n llamados a este helper
	 *
	 * @access public
	 * @param int $n
	 * @static
	 */
	public static function trBreak($n=''){
		static $l;
		if($n=='') {
			$l = 0;
			return;
		}
		if(!$l) {
			$l = 1;
		} else {
			$l++;
		}
		if(($l%$n)==0) {
			print "</tr><tr>";
		}
	}

	/**
	 * Imprime una etiqueta BR cada $n llamados a este helper
	 *
	 * @access public
	 * @param int $n
	 * @static
	 */
	public static function brBreak($n=''){
		static $l;
		if($n=='') {
			$l = 0;
			return;
		}
		if(!$l) {
			$l = 1;
		} else {
			$l++;
		}
		if(($l%$n)==0) {
			print "<br/>\n";
		}
	}

	/**
	 * Intercala entre llamados una lista de colores para etiquetas TR
	 *
	 * @access 	public
	 * @param 	array $colors
	 * @static
	 */
	public static function trColor($colors){
		static $i;
		if(func_num_args()>1){
			$numberArgs = func_num_args();
			$params = Utils::getParams(func_get_args(), $numberArgs);
		}
		if(!$i) {
			$i = 1;
		}
		print "<tr bgcolor=\"{$colors[$i-1]}\"";
		if(count($colors)==$i) {
			$i = 1;
		} else {
			$i++;
		}
		if(isset($params)){
			if(is_array($params)){
				foreach($params as $key => $value){
					if(!is_integer($key)){
						print " $key = '$value'";
					}
				}
			}
		}
		print ">";
	}

	/**
	 * Intercala entre llamados una lista de clases CSS para etiquetas TR
	 *
	 * @access 	public
	 * @param 	array $classes
	 * @static
	 */
	public static function trClassName($classes){
		static $i;
		if(func_num_args()>1){
			$params = Utils::getParams(func_get_args());
		}
		if(!$i) {
			$i = 1;
		}
		$code = "<tr class=\"{$classes[$i-1]}\"";
		if(count($classes)==$i) {
			$i = 1;
		} else {
			$i++;
		}
		if(isset($params)){
			if(is_array($params)){
				foreach($params as $key => $value){
					if(!is_integer($key)){
						$code.= " $key = '$value'";
					}
				}
			}
		}
		$code.=">";
		return $code;
	}

	/**
	 * Crea un botón que al hacer click carga un controlador y una acción determinada
	 *
	 * @access 	public
	 * @param 	string $caption
	 * @param 	string $action
	 * @param 	string $classCSS
	 * @return 	string
	 * @static
	 */
	static public function buttonToAction($caption, $action, $classCSS=''){
		return "<input type='button' class='$classCSS' onclick='window.location=\"".Utils::getKumbiaUrl($action)."\"' value='$caption' />";
	}

	/**
	 * Crea un Button que al hacer click carga con AJAX un controlador y una accion determinada
	 *
	 * @param 	string $caption
	 * @param 	string $action
	 * @param 	string $classCSS
	 * @return 	string
	 */
	static public function buttonToRemoteAction($caption, $action, $classCSS=''){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(func_num_args()==2){
			$params['action'] = $params[1];
			$params['caption'] = $params[0];
		} else {
			if(!isset($params['action'])||!$params['action']) {
				$params['action'] = $params[1];
			}
			if(!isset($params['caption'])||!$params['caption']) {
				$params['caption'] = $params[0];
			}
		}
		if(!isset($params['update'])){
			$params['update'] = "";
		}
		$code = "<button onclick='AJAX.execute({action:\"{$params['action']}\", container:\"{$params['update']}\", callbacks: { success: function(){{$params['success']}}, before: function(){{$params['before']}} } })'";
		unset($params['action']);
		unset($params['success']);
		unset($params['before']);
		unset($params['complete']);
		foreach($params as $k => $v){
			if(!is_integer($k)&&$k!='caption'){
				$code.=" $k='$v' ";
			}
		}
		$code.=">{$params['caption']}</button>";
		return $code;
	}

	/**
	 * Crea un select multiple que actualiza un container
	 * usando una accion ajax que cambia dependiendo del id
	 * selecionado en el select
	 *
	 * @access 	public
	 * @param 	string $id
	 * @return 	string
	 * @static
	 */
	public static function updaterSelect($id){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(func_num_args()==1){
			$params['id'] = $id;
		}
		if(!$params['id']){
			$params['id'] = $params[0];
		}
		if(!$params['container']){
			$params['container'] = $params['update'];
		}
		$code = "
		<select multiple onchange='AJAX.viewRequest({
			action: \"{$params['action']}/\"+selectedItem($(\"{$params['id']}\")).value,
			container: \"{$params['container']}\"
		})' ";
		unset($params['container']);
		unset($params['update']);
		unset($params['action']);
		foreach($params as $k => $v){
			if(!is_integer($k)){
				$code.=" $k='$v' ";
			}
		}
		$code.=">\n";
		return $code;
	}

	/**
	 * Helper de Paginacion
	 *
	 * @param array $items
	 * @param integer $pageNumber
	 * @param integer $show
	 * @return object
	 */
	public static function paginate($items, $pageNumber=null, $show=10){
		$n = count($items);
		$page = new stdClass();
		$start = $show*($pageNumber-1);
		if(is_array($items)){  
			if($pageNumber===null){
				$pageNumber = 1;
			}
			$page->items = array_slice($items, $start, $show);
		} else {
			if($pageNumber===null){
				$pageNumber = 0;
			}
			if(is_object($items)){
				if($items instanceof ActiveRecordResultset){
					if($start<0){
						throw new CoreException("El n&uacute;mero de la p&aacute;gina es negativo &oacute; cero ($start)");
					}
					$page->items = array();
					$total = count($items);
					if($total>0){
						if($start<=$total){
							$items->seek($start);
						} else {
							$items->seek(1);
							$pageNumber = 1;
						}
						$i = 1;
						while($items->valid()==true){
							$page->items[] = $items->current();
							if($i>=$show){
								break;
							}
							$i++;
						}
					}
				}
			}
		}
		$page->first = 1;
		$page->next = ($start + $show)<$n ? ($pageNumber+1) : ((int)($n/$show) + 1);
		$page->before = ($pageNumber>1) ? ($pageNumber-1) : 1;
		$page->current = $pageNumber;
		$page->total_pages = ($n % $show) ? ((int)($n/$show) + 1) : ($n/$show);
		$page->last = $page->total_pages;
		return $page;
	}

	/**
	 * Crea pestañas de diferentes colores
	 *
	 * @access public
	 * @param array $tabs
	 * @param string $color
	 * @param int $width
	 * @static
	 */
	/*static public function tab($tabs, $color='green', $width=800){

		switch($color){
			case 'blue':
				$col1 = '#E8E8E8'; $col2 = '#C0c0c0'; $col3 = '#000000';
				break;

			case 'pink':
				$col1 = '#FFE6F2'; $col2 = '#FFCCE4'; $col3 = '#FE1B59';
				break;

			case 'orange':
				$col1 = '#FCE6BC'; $col2 = '#FDF1DB'; $col3 = '#DE950C';
				break;

			case 'green':
				$col2 = '#EAFFD7'; $col1 = '#DAFFB9'; $col3 = '#008000';
				break;
		}


		print "
			<table cellspacing='0' cellpadding='0' width=$width>
			<tr>";
		$p = 1;
		$w = $width;
		foreach($tabs as $tab){
			if($p==1){
				$color = $col1;
			} else {
				$color = $col2;
			}
			$ww = (int) ($width * 0.22);
			$www = (int) ($width * 0.21);
			print "<td align='center'
				  width=$ww style='padding-top:5px;padding-left:5px;padding-right:5px;padding-bottom:-5px'>
				  <div style='width:$www"."px;border-top:1px solid $col3;border-left:1px solid $col3;border-right:1px solid $col3;background:$color;padding:2px;color:$col3;cursor:pointer' id='spanm_$p'
				  onclick='showTab($p, this)'
				  >".$tab['caption']."</div></td>";
			$p++;
			$w-=$ww;
		}
		print "
			<script type='text/javascript'>
				function showTab(p, obj){
				  	for(i=1;i<=$p-1;i++){
					    $('tab_'+i).hide();
					    $('spanm_'+i).style.background = '$col2';
					}
					$('tab_'+p).show();
					obj.style.background = '$col1'
				}
			</script>
			";
		$p = $p + 1;
		
		print "<td width=$w></td><tr>";
		print "<td colspan=$p style='border:1px solid $col3;background:$col1;padding:10px'>";
		$p = 1;
		foreach($tabs as $tab){
			if($p!=1){
				print "<div id='tab_$p' style='display:none'>";
			} else {
				print "<div id='tab_$p'>";
			}
			View::renderPartial($tab['partial']);
			print "</div>";
			$p++;
		}
		print "<br></td><td width='30'></td>";
		print "</table>";
	}*/
        
	static public function updateDiv(){
		$params = Utils::getParams(func_get_args());
		$name = $params[0];
		if(isset($params['value'])){
			$value = $params['value'];
		} else {
			$value = "";
		}
		$html = "<div><div id='{$name}1' ondblclick=\"$('$name').show();$('$name').activate();this.hide()\" onmouseover='this.style.background=\"#ffffcc\"' onmouseout='this.style.background=\"transparent\"'>$value</div>";
		$html.= "<input id='{$name}' type='text' value='$value' style='display:none' onblur='$(\"{$name}1\").show();this.hide();$(\"{$name}1\").innerHTML=this.value'";
		unset($params['value']);
		foreach($params as $key => $value){
			if(!is_integer($key)){
				$html.= "$key = '$value'";
			}
		}
		$html.= "/></div>";
		return $html;
	}

        static public function setColumnsToGrid($columns){
            $name="grd".Router::getController();

            self::displayTo($name,$columns);
        }

        /**
	 * Crea una grilla de datos
         * usando una accion ajax para la carga de datos
	 
	 * @access public
         * @param string $id
	 
	 * @retun string

	 * @static
	 */
        static public function jqGrid(){

        $jqGrid='';
       
        //$jqGrid.=self::includeLinkCSS('themes/ui.jqgrid');                    //comentado por nelson ya que se carga de primera
        //$jqGrid.=self::includePluginjQuery('jqGrid/len/grid.locale-sp','jqGrid/jquery.jqGrid.min');
        
        
        $numeroArgs = func_num_args();
	$parametros = Utils::getParams(func_get_args(), $numeroArgs);
        
        if(!isset($parametros['controller']))
           $parametros['controller']=Router::getController();
           //identificador de la tabla del grid

        if(!isset($parametros['id']))
           $parametros['id']="grd{$parametros['controller']}";
         $paramc=self::getValueFromAction($parametros['id']);
         if(!isset($parametros['colNames'])){
             
             if(isset($paramc) && is_array($paramc)){
                $nombreCol='[';
                $comaNC='';
                $parametro=$paramc;
                for($i=0;$i<count($parametro);$i++){
                     foreach($parametro[$i] as $at => $val){
                          if(!is_integer($at)&&$at=='name')
                          {   $nombreCol.=$comaNC."'$val'";
                                $comaNC=',';
                          }
                     }
                }
                $nombreCol.=']';
                $parametros['colNames']=$nombreCol;
             }
         }
         if(!isset($parametros['colModel'])){
             
             $modeloCol='[';
             if(isset($paramc) && is_array($paramc)){
                 
                    $parametro=$paramc;
                    $comaCM='';
                    for($i=0;$i<count($parametro);$i++){
                        $comaMC='';
                        $modeloCol.=$comaCM.'{';
                        foreach($parametro[$i] as $at => $val){
                            if(!is_integer($at)&&(in_array($at, array('index','width','align','sortable','resizable','editable','edittype','editoptions','sorttype','hidden','editrules','formatter','formatoptions','searchoptions','formoptions'))))
                            { if(!is_bool($val)){
                                 $modeloCol.= $comaMC.$at.":'$val'";
                                 if($at=='index')
                                   $modeloCol.= ",name:'$val'";

                                }
                              else{
                                 if($val)
                                   $modeloCol.= $comaMC.$at.":true";
                                 else
                                   $modeloCol.= $comaMC.$at.":false";
                              }
                              $comaMC=',';
                            }
                        }
                      $modeloCol.='}';
                      $comaCM=',';
                    }

                $modeloCol.=']';
                $parametros['colModel']=$modeloCol;

             }
            
         }
         
                    
               
        if(!isset($parametros['action']))
                $parametros['action']='obtenerDatosGrid';
        $parametros['url']="'".Utils::getKumbiaUrl("{$parametros['controller']}/{$parametros['action']}")."'";

        if(!isset($parametros['datatype'])){
            $parametros['datatype']="'json'";
        }

        if(!isset($parametros['mtype']))
            $parametros['mtype']="'POST'";

        if(!isset($parametros['pager']))
            $parametros['pager']="'#paginador_{$parametros['id']}'";
         
        if(!isset($parametros['caption']))
            $parametros['caption']="'jqGrid'";

        
        if(!isset($parametros['rowList']))
            $parametros['rowList']='[10,20,30]';
        
        if(!isset($parametros['viewrecords']))
            $parametros['viewrecords']="true";

        if(!isset($parametros['sortname']))
            $parametros['sortname']="'id'";

        if(!isset($parametros['sortorder']))
            $parametros['sortorder']="'desc'";

        
        if(!isset($parametros['autowidth']) && !isset($parametros['width']))
            $parametros['autowidth']="true";
        

        // parametro numerar filas true o false

       if(!isset($parametros['rownumbers']))
            $parametros['rownumbers']="true";

       if(!isset($parametros['ajaxedit']))
            $parametros['ajaxedit']="false";
            
        if($parametros['ajaxedit']=="false"){
                
            if(!isset($parametros['addurl']))
                $parametros['addurl']="'{$parametros['controller']}/nuevo'";

            if(!isset($parametros['editurl']))
                $parametros['editurl']="'{$parametros['controller']}/editar'";
            
            if(!isset($parametros['delurl']))
              $parametros['delurl']="'{$parametros['controller']}/eliminar'";
        }
        else{
            
           if(!isset($parametros['addurl']))
               $parametros['addurl']="'{$parametros['controller']}/ajaxEditarGrid'";
           if(!isset($parametros['editurl']))
                $parametros['editurl']="'{$parametros['controller']}/ajaxEditarGrid'";
           if(!isset($parametros['delurl']))
             $parametros['delurl']="'{$parametros['controller']}/ajaxEditarGrid'";
        }

        // parametro para ordenar filas valor true o false
        
        if(!isset($parametros['sortable']))
            $parametros['sortable']="true";


      /*if(!isset($parametros['viewsortcols']))
            $parametros['viewsortcols']="true";*/

      // parametros de barra de navegacion
        if(!isset($parametros['navGrid'])){
            $parametros['navGrid']="{edit:false,add:false,del:false}";
        }
        if(!isset($parametros['edioptions']))
           $parametros['edioptions']='{}';

        if(!isset($parametros['addoptions']))
           $parametros['addoptions']='{}';

        if(!isset($parametros['deloptions']))
           $parametros['deloptions']='{}';

        if(!isset($parametros['searchoptions']))
           $parametros['searchoptions']='{multipleSearch:true}';

        // parametro de barra de filtro x columna valor true o false
        
        if(!isset($parametros['filterToolbar']))
          $parametros['filterToolbar']=true;

         //parametro de selccion de columnas  valor true o false
        if(!isset($parametros['columnChooser'])){
          $parametros['columnChooser']=true;
        }

        //parametro para ajuste automatico del ancho de las columnas al contenido
        if(!isset($parametros['shrinkToFit'])){
             $parametros['shrinkToFit']='false';
        }

        if(!isset($parametros['multiselect']))
            $parametros['multiselect']='false';
        if(!isset($parametros['addbutton']))
            $parametros['addbutton']='true';

        if(!isset($parametros['editbutton']))
            $parametros['editbutton']='true';

        if(!isset($parametros['delbutton']))
            $parametros['delbutton']='true';

        if(!isset($parametros['sortableRows']))
            $parametros['sortableRows']='true';


        if(!isset($parametros['gridResize']))
           $parametros['gridResize']='{minWidth:350,maxWidth:2048,minHeight:80, maxHeight:600}';
        //parametro con propiedades para mostrar barra de tareas
        if(!isset($parametros["typewinedit"])){
            $parametros["typewinedit"]="'_self'";
        }
        if(!isset($parametros["optwinedit"])){
            $parametros["optwinedit"]="'width=590,height=400'";
        }

        if($parametros['ajaxedit']=='false'){
            $btn='';
            if($parametros['addbutton']=='true')
               $btn.='<td><div class="ui-pg-div" ><span class="ui-icon ui-icon-plus"/> </div></td>';
            if($parametros['editbutton']=='true')
               $btn.='<td><div class="ui-pg-div" ><span class="ui-icon ui-icon-pencil"/> </div></td>';
            if($parametros['delbutton']=='true')
               $btn.='<td><div class="ui-pg-div" ><span class="ui-icon ui-icon-trash"/> </div></td>';

            if($btn!=""){
                if(!isset($parametros['toolbar']))
                    $parametros['toolbar']='[true,"top"]';
                $btn="<table class=\"ui-pg-table navtable\" style=\"cursor:pointer;\"><tr>$btn</tr></table>";
                
            }

        }

        
        //construccion de la tabla del grid
        $jqGrid.="<table id='{$parametros['id']}'></table>
            <div id='paginador_{$parametros['id']}'></div>";

        $jqGrid.="
            <script type='text/javascript'>\n
        jQuery(document).ready(function() {\n";

        $jqGrid.="var opc_{$parametros['id']}={};\n";
        $jqGrid.="opc_{$parametros['id']}={";
        $comaOpc='';

        foreach ($parametros as $at=>$valor){
            if(!is_integer($at)&&(in_array($at, array('url','datatype','mtype','pager','colNames','colModel','rowNum','rowList','pager','sortname','viewrecords','sortorder','sortable','caption','loadonce','multiselect','editurl','height','width','hidegrid','recordpos','subGrid','subGridUrl','subGridModel','subGridRowExpanded','subGridRowColapsed','pgbuttons','pgtext','pginput','multikey','xmlReader','jsonReader','postData','loadui','hiddengrid','treeGrid','forceFit','ExpandColumn','toolbar','scrollrows','treeGridModel','autowidth','rownumbers','rownumWidth','gridview','treedatatype','footerrow','userDataOnFooter','altRows','viewsortcols','direction','shrinkToFit','scroll','cellEdit','cellsubmit','serializeGridData','onSortCol','ondblClickRow','onSelectRow','loadComplete','gridComplete','afterInsertRow','loadError','onHeaderClick','afterEditCell','afterSaveCell')))&& $at<>'id')
            {
                $jqGrid.=$comaOpc."$at:$valor";
                $comaOpc=',';
            }
        }
        $jqGrid.='};';
        
        $jqGrid.="
           jQuery('#{$parametros['id']}').jqGrid(opc_{$parametros['id']});
           jQuery('#{$parametros['id']}').jqGrid('navGrid','#paginador_{$parametros['id']}',{$parametros['navGrid']},{$parametros['edioptions']},{$parametros['addoptions']},{$parametros['deloptions']},{$parametros['searchoptions']});

            ";
        if($parametros['filterToolbar']=='true')
            $jqGrid.="jQuery('#{$parametros['id']}').jqGrid('filterToolbar');";
            
        if($parametros['columnChooser']=='true'){
            $jqGrid.="
                    jQuery('#{$parametros['id']}').jqGrid('navButtonAdd','#paginador_{$parametros['id']}',
                            {   caption: 'Columnas',
                                title: 'Reordenar Columnas',
                                onClickButton : function (){
                                        jQuery('#{$parametros['id']}').jqGrid('columnChooser');
                                }
                            });
                    ";
        }
        if($btn){
            $jqGrid.="$('#t_{$parametros['id']}').append('$btn');";
            $jqGrid.="$('#t_{$parametros['id']} .ui-icon-plus').click(function(){
                        abrirpaginaform('".Utils::getKumbiaUrl(str_replace("'","",$parametros['addurl']))."',{$parametros["typewinedit"]},{$parametros["optwinedit"]});
                        //window.location='".Utils::getKumbiaUrl(str_replace("'","",$parametros['addurl']))."';
            });";
            $jqGrid.="$('#t_{$parametros['id']} .ui-icon-pencil').click(function(){
                var id = jQuery('#{$parametros['id']}').jqGrid('getGridParam','selrow');
                if(id>0){
                    abrirpaginaform('".Utils::getKumbiaUrl(str_replace("'","",$parametros['editurl'])."/'+id").",{$parametros["typewinedit"]},{$parametros["optwinedit"]});
                   //window.location='".Utils::getKumbiaUrl(str_replace("'","",$parametros['editurl'])."/'+id").";
                }
                else
                   alert('Seleccione un fila');
            });";
            $getparam='selrow';
            if($parametros['multiselect']=='true')
                $getparam='selarrrow';

         
            $jqGrid.="$('#t_{$parametros['id']} .ui-icon-trash').click(function(){
                
                    var gr = jQuery('#{$parametros['id']}').jqGrid('getGridParam','$getparam');
                    jQuery('#{$parametros['id']}').jqGrid('setGridParam',{editurl: '".Utils::getKumbiaUrl(str_replace("'","",$parametros['delurl']))."'});
                    if( gr != null )
                        jQuery('#{$parametros['id']}').jqGrid('delGridRow',gr,{reloadAfterSubmit:true});
                    else
                        alert('Por favor seleccione una fila a eliminar!');
            });";


        }
         if($parametros['sortableRows']=='true'){
            $jqGrid.="
                       jQuery('#{$parametros['id']}').jqGrid('sortableRows');";

         }
         if($parametros['gridResize']!=""){
            $jqGrid.="
                    jQuery('#{$parametros['id']}').jqGrid('gridResize',{$parametros['gridResize']});
                ";
         }
        $jqGrid.="});\n

           function abrirpaginaform(url,tipov,options){
                if(tipov=='popup'){
                   var Xpos=(screen.width/2)-285;
                   var Ypos=(screen.height/2)-220;
                   window.open(url,'popup_".$parametros['id']."',options+',left='+Xpos+',top='+Ypos);
                }else{
                     window.location=url;
                }
           }
           function recargar{$parametros['id']}(){
                $('#{$parametros['id']}').trigger('reloadGrid');
           }
            </script>";
        return $jqGrid;
       

        }

        static public function pane($panes){
                
                if(count($panes)>0 && is_array($panes) ){
                    
                    print self::includeLinkCSS('themes/redmond/pane/complex-pane');
                    $opttop='{}';
                    $optleft='{}';
                    $optright='{}';
                    $optbottom='{}';
                    $optcenter='{}';
                   
                    $js='';
                    foreach($panes as $pane){
                        $class='';
                        $pos='';
                       switch(@$pane['position']){
                        case 'top':
                            $class=' ui-layout-north '.@$pane['class'];
                            $opttop="{".@$pane['options']."}";
                            $pos='north';
                            break;
                        case 'left':
                            $class=' ui-layout-west '.@$pane['class'];
                            $optleft="{".@$pane['options']."}";
                            $pos='west';
                            if(isset($pane['pin-button']) && @$pane['pin-button']==true)
                            {    $js.="
                                $('<span></span>').addClass('pin-button').prependTo('body > .ui-layout-west');
                                body_layout.addPinBtn('body > .ui-layout-west .pin-button', 'west');";

                            }
                            if(isset($pane['close-button']) && @$pane['close-button']==true)
                            {    $js.="
                                    $('<span></span>').attr('id', 'west-closer' ).prependTo('body > .ui-layout-west');
                                    body_layout.addCloseBtn('#west-closer', 'west');
                                 ";
                            }
                            break;
                        case 'right':
                            $class=' ui-layout-east '.@$pane['class'];
                            $optright="{".@$pane['options']."}";
                            $pos='east';
                            if(isset($pane['pin-button']) && @$pane['pin-button']==true){
                                $js.="
                                $('<span></span>').addClass('pin-button').prependTo('body > .ui-layout-east');
                                body_layout.addPinBtn('body > .ui-layout-east .pin-button', 'east');";

                             }
                            if(isset($pane['close-button']) && @$pane['close-button']==true)
                            {    $js.="
                                    $('<span></span>').attr('id', 'east-closer' ).prependTo('body > .ui-layout-east ');
                                    body_layout.addCloseBtn('#east-closer', 'east');
                                 ";
                                
                            }
                            break;
                            
                        case 'bottom':
                            $class=' ui-layout-south '.@$pane['class'];
                             $optbottom="{".@$pane['options']."}";
                             $pos='south';
                             break;

                        case 'middle':
                            $class=' ui-layout-center '.@$pane['class'];
                             $optcenter="{".@$pane['options']."}";
                             $pos='center';

                            break;
                        }
                        if($class!="")
                            print '<div id="'.@$pane['id'].'" class="'.$class.'">';

                       if(isset($pane['caption']) || @$pane['pin-button']==true || @$pane['close-button']==true){
                           print "<div class='header ui-widget-header' style='height:13px;'>".@$pane['caption']."</div>";
                       }
                       if(isset($pane['partial']) || isset($pane['content'])){
                           print "<div id='content-$pos'>";
                           if(isset($pane['partial']))
                                View::renderPartial($pane['partial']);
                           else
                                print "{$pane['content']}";
                           print "</div>";
                       }
                       
                       print '</div>';
                       if($pos=='west' || $pos=='east'){
                           if($pos=='west')
                              $aling='left:0px;';
                           else
                              $aling='right:0px;';
                           if(@$pane['close-button']==true || @$pane['pin-button']==true){
                                                                
                                $js.='$("body > .ui-state-default-'.$pos.'").after(\'<div aria-disabled="true" class="resizer resizer-'.$pos.' resizer-closed resizer-'.$pos.'-closed ui-draggable-disabled" id="'.$pos.'-open" style="margin: 0px; padding: 0px; overflow: hidden; cursor: pointer; position: absolute; font-size: 1px; text-align: left; z-index: -1; '.$aling.' height: 100%; width: 21px;" title="Slide Open"><div title="Open '.$pos.' Pane" class="ui-layout-toggler ui-layout-toggler-'.$pos.' ui-layout-toggler-closed ui-layout-toggler-'.$pos.'-closed" style="margin: 0px; padding: 0px; overflow: hidden; position: absolute; text-align: center; font-size: 1px; cursor: pointer; z-index: 1; height: 21px; width: 21px;  left: 0px; display: block;" id="">\');';
                                $js.='body_layout.addOpenBtn( "#'.$pos.'-open", "'.$pos.'" );';
                                $js.='
                                        $("#'.$pos.'-open").css("top",(body_layout.state.north.size+6));
                                          body_layout.options.north.onresize_end=function(){
                                                $("#'.$pos.'-open").css("top",(body_layout.state.north.size+6));
                                          }
                                          body_layout.options.north.onclose_end =function(){
                                                
                                                $("#'.$pos.'-open").css("top",6);
                                          }
                                          body_layout.options.north.onopen_end =function(){

                                                $("#'.$pos.'-open").css("top",(body_layout.state.north.size+6));
                                          }
                                          body_layout.options.west.onclose_end =function(){
                                                var w=$("body > .ui-layout-center").css("width");
                                                w=parseFloat(w.replace("px",""));
                                                $("body > .ui-layout-center").css("left",21);
                                                $("body > .ui-layout-center").css("width",w-14);
                                          }
                                          body_layout.options.east.onclose_end =function(){

                                                var w=$("body > .ui-layout-center").css("width");
                                                w=parseFloat(w.replace("px",""));
                                                
                                                $("body > .ui-layout-center").css("width",(w-14));
                                          }
                                      ';

                           }
                       }
                    }
                    
                        print "<script>$(document).ready(function(){
                                var defaultopt={
                                    resizerClass: 'ui-state-default'
                                };
                                var body_layout=$('body').layout({defaults: defaultopt,north: $opttop,south: $optbottom, west: $optleft, east: $optright,center: $optcenter });
                                $js
                        });</script>";
                }
                

        }

        static public function accordion($tabs){
             $numeroArgs = func_num_args();
	     $parametros = Utils::getParams(func_get_args(), $numeroArgs);
             $opciones='{}';
             if(!isset($parametros['id']) || trim(@$parametros['id'])=="")
                $parametros['id']='accordion';
             if(is_array($tabs) && count($tabs)>0){
                 if(isset($parametros['options']))
                    $opciones="{{$parametros['options']}}";
                 echo "<div id='{$parametros['id']}'>";
                 foreach ($tabs as $tab){
                     echo "<h3><a href='#'>".@$tab['caption']."</a></h3>";
                     echo "<div>";
                     if(isset($tab['partial']))
                          view::renderPartial($tab['partial']);
                     else
                         echo @$tab['content'];
                     echo "</div>";
                 }
                echo "</div>";
             }
             echo "<script>
                $(function() {
                    $('#{$parametros['id']}').accordion($opciones);
	        });

                </script>";
        }
        static public function tab($tabs){
            $numeroArgs = func_num_args();
	     $parametros = Utils::getParams(func_get_args(), $numeroArgs);
             $opciones='{}';
             if(!isset($parametros['id']) || trim(@$parametros['id'])=="")
                $parametros['id']='tabs';
                
          /*   if(isset($parametros['ajaxEnabled']))
                if(!is_bool($parametros['ajaxEnabled']))
                    $parametros['ajaxEnabled']=false;
             else
                $parametros['ajaxEnabled']=false;*/
                
             if(is_array($tabs) && count($tabs)>0){
                 if(isset($parametros['options']))
                    $opciones="{{$parametros['options']}}";
                 print "<div id='{$parametros['id']}'>";
                 $c=1;
                 print "<ul class='ocultar'>";
                     foreach ($tabs as $tab)
                     {   if(!isset($tab['id']))
                             $tab['id']="tab-$c";
                         print "<li><a href='#{$tab['id']}'>".@$tab['caption']."</a></li>";
                         $c++;
                     }
                 print "</ul>";
                 $c=1;
                 reset($tabs);
                 foreach ($tabs as $tab){
                     if(!isset($tab['id']))
                             $tab['id']="tab-$c";
                     print "<div id='{$tab['id']}'>";
                     if(isset($tab['partial']))
                          view::renderPartial($tab['partial']);
                     else
                         print @$tab['content'];
                     print "</div>";
                     $c++;
                 }
                 print "</div>";
             }
             print "<script>
                $(function() {
                    $('#{$parametros['id']}').tabs($opciones);
	        });

                </script>";
        }

        static public function treeview($id,$htmlmenu){

           $html=self::includeLinkCSS('themes/redmond/treeview/jquery.treeview');
           //$html.=self::includePluginjQuery('treeview/jquery.treeview');
           $path = Core::getInstancePath();
           $html.= "<script type='text/javascript' src='".$path."javascript/core/framework/jQuery/treeview/jquery.treeview.js'></script>\r\n";
           $numeroArgs = func_num_args();
	   $parametros = Utils::getParams(func_get_args(), $numeroArgs);
           if(!isset($parametros['options']))
              $parametros['options']="{}";
           else
              $parametros['options']="{{$parametros['options']}}";
           if(!isset($parametros['class']))
              $parametros['class']='';
           if(trim($id)=="" || is_numeric($id))$id='treeview'.$id;
           $html.="<ul id='$id' class='{$parametros['class']}'>$htmlmenu</ul>";


           $html.="<script>
                $(document).ready(function(){ $('#$id').treeview({$parametros['options']});});
            </script>";
            
           return $html;

        }
        static public function themeUI(){
             $html='';
             $html.=self::includePluginjQuery('themeswitchertoll');
             $html.="<div id='themeui'></div>";
             $html.="<script type='text/javascript'>
                $(document).ready(function(){
                    $('#themeui').themeswitcher({initialText: 'Temas',buttonPreText: 'Tema:'});
                });

             </script>";
             return $html;
        }
        static public function CKEditor($configuration){
            $instancePath = Core::getInstancePath();
            $rutajs=$instancePath.'javascript/';
            $html=self::textArea($configuration);
            $id='ckeditor';
            if(func_num_args()==1){
                $configuration = func_get_args();
                $id=$configuration[0];
            }else{
                $numberArguments = func_num_args();
		$configuration = Utils::getParams(func_get_args(), $numberArguments);
                if(!isset($configuration['name'])||$configuration['name']) {
			$configuration['name'] = $configuration[0];
	        }
                $id=$configuration['name'];
            }
            $html.='<script type="text/javascript">
                    jQuery.include("'.$rutajs.'ckeditor/ckeditor.js");
                    jQuery.include("'.$rutajs.'ckeditor/adapters/jquery.js");
                    
                    $(function(){
                         
                         $("#'.$id.'").ckeditor({extraPlugins : "uicolor",uiColor: "#C2DAEF"/*,
					toolbar :
					[   
                                            [ "UIColor"]
					]*/});
                    });
                </script>';

            return $html;
            
        }
        static public function progressBar($configuration){
           
            if(func_num_args()==1){
                $configuration = func_get_args();
                $configuration['id']=$configuration[0];
                $configuration['value']=0;
                $configuration['valuereference']=1;
            }else{
                $numberArguments = func_num_args();
		$configuration = Utils::getParams(func_get_args(), $numberArguments);
                if(!isset($configuration['id'])||$configuration['id']) {
			$configuration['id'] = $configuration[0];
	        }
                if(!isset($configuration['value'])||!is_numeric($configuration['value'])) {
			$configuration['value'] = 0;
                        
	        }else{
                    if($configuration['value']>100)
                        $configuration['value']=100;
                    if($configuration['value']<0)
                        $configuration['value']=0;
                }
                if(!isset($configuration['valuereference'])||!is_numeric($configuration['valuereference'])) {
			$configuration['valuereference'] = 100;
                }
            }
            $html="<div id='{$configuration['id']}'></div>";
             $html.="<script type='text/javascript'>
                $(document).ready(function(){
                    $('#{$configuration['id']}').progressbar({";
                $coma='';
                foreach($configuration as $key => $value){
                       
			if(!is_integer($key)&& $key!='id' && $key!='valuereference'){
				$html.= "$coma$key: $value";
                                $coma=',';
			}
		}
                $html.="});
                
                });
                var valor{$configuration['id']}={$configuration['value']};
                var inc{$configuration['id']}=100/{$configuration['valuereference']};
               
                  $.{$configuration['id']} = {
                    nextValue: function(){ 
                        value=valor{$configuration['id']}+inc{$configuration['id']};

                        if(value>100) value=100;
                        
                        $('#{$configuration['id']}').progressbar('option', 'value', value);
                        valor{$configuration['id']}=value;
                    },
                    previousValue: function(){
                        value=valor{$configuration['id']}-inc{$configuration['id']};
                        if(value<0) value=0;
                        $('#{$configuration['id']}').progressbar('option', 'value', value);
                        valor{$configuration['id']}=value;
                    },
                    initialValue:function(){
                        value={$configuration['value']};
                        $('#{$configuration['id']}').progressbar('option', 'value', value);
                        valor{$configuration['id']}=value;
                    },
                    lastValue:function(){
                        $('#{$configuration['id']}').progressbar('option', 'value', 100);
                        valor{$configuration['id']}=100;
                    },
                    setValue:function(value){
                        if(!isNaN(value)){
                            value=valor{$configuration['id']};
                        }else if(value<0){
                            value=0;
                        }else if(value>100){
                            value=100;
                        }
                        
                        $('#{$configuration['id']}').progressbar('option', 'value', value);
                        valor{$configuration['id']}=value;
                    }
                };
               
                </script>";
              return $html;
        }

}
