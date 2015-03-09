<?php

/**
 * Louder Application Forms
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
 * @category 	Louder
 * @package 	Louder
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright  	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (c) 2008-2009 Oscar Garavito (game013@gmail.com)
 * @license 	New BSD License
 * @version 	$Id$
 */

/**
 * CreateForm
 *
 * Genera formularios de varias clases.
 *
 * @category 	Louder
 * @package 	Louder
 * @copyright	Copyright (c) 2008-2009 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright  	Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @copyright	Copyright (c) 2008-2009 Oscar Garavito (game013@gmail.com)
 * @license 	New BSD License
 */
abstract class CreateForm extends UserComponent {

	/**
	 * Atributos
	 *
	 * @var ActiveRecordResultset
	 */
	protected static $_attributes;

	/**
	 * Crea un nuevo Formulario con todas las caracteristicas deseadas.
	 *
	 * @param 	DbBase $connection
	 * @param 	string $controlador
	 * @param 	string $extappname
	 * @param 	string $source
	 * @param 	string $titulo
	 */
	public static function newForm($connection, $controlador, $extappname, $source, $titulo) {
		$conditions = "app_name = '{$extappname}' AND table_name='{$source}' AND component = 'CR'";
		$modelsDir = "apps/{$extappname}/models";
		$atts = new Attributes();
                $attributes=null;
		if($atts->count($conditions)>0){
			$attributes = $atts->find($conditions);
			foreach($attributes as $attribute){
				$relation = $attribute->getRelations();                          
				ComponentBuilder::createModel($connection, $modelsDir, $relation->getTableRelation());
			}
		}
		$path = 'apps/'.$extappname.'/models/'.$source.'.php';
		ComponentBuilder::createModel($connection, $modelsDir, $source,'',$attributes);

		@mkdir("apps/{$extappname}/views/{$controlador}");

		$conditions = "app_name = '{$extappname}' AND table_name='{$source}'";
		$atts = new Attributes();
		self::$_attributes = $atts->find($conditions);

		if(file_put_contents("apps/{$extappname}/views/{$controlador}/index.phtml",
			CreateForm::getIndexViewCode($controlador,$titulo))){
                        echo "Se creo la vista index<br/>";
                }
		/*/file_put_contents("apps/{$extappname}/views/{$controlador}/visualizar.phtml",
			CreateForm::getSearchViewCode($controlador,$titulo));*/
		if(file_put_contents("apps/{$extappname}/views/{$controlador}/nuevo.phtml",
			CreateForm::getNewViewCode($controlador,$titulo)))
                {
                    echo "Se creo la vista nuevo<br/>";
                }
		if(file_put_contents("apps/{$extappname}/views/{$controlador}/editar.phtml",
			CreateForm::getEditViewCode($controlador,$titulo))){
                    echo "Se creo la vista editar<br/>";
                }


		if(file_put_contents("apps/{$extappname}/controllers/{$controlador}_controller.php",
		CreateForm::getControllerCode($controlador,$source))){
                    print "Se creo el controlador: $controlador\n";
                }
	}

	/**
	 * Retorna una cadena de caracteres en la cual esta el contenido del archivo de la vista Index.
	 *
	 * @param 	string $controlador
	 * @param 	string $titulo
	 * @return 	string
	 */
	protected static function getIndexViewCode($controlador,$titulo) {
		/*$indexview = "<div id='mainContent'>\n\n";
		$indexview.= "<?php echo View::getContent(); ?>\n";
		$indexview.= "<h1>{$titulo}</h1>\n\n";
		$indexview.= "<?php echo Tag::form(\"$controlador/buscar\",'autocomplete: off'); ?>\n";
		$indexview.= "<div class='userStatus'>Estado: Buscar un {$titulo}</div>\n\n";
		$indexview.= "<div align='right' class='nuevoButtonDiv'>\n";
		$indexview.= "\t<?php echo Tag::buttonToAction(\"Nuevo\", \"$controlador/nuevo\") ?>\n";
		$indexview.= "</div>\n";
		$indexview.= "<table class='tableFormSearch' cellspacing='0'>\n";
		foreach(self::$_attributes as $attribute){
			$fieldName = Utils::lcfirst(Utils::camelize($attribute->getFieldName()));
			$labelCode = "\t<tr>\n\t\t<td align='right'><label for='{$fieldName}'><b>"
				.$attribute->getLabel()."</b>:</label></td>\n";
			$size = $attribute->getSize();
			if($size!=""){
				$size = ", \"size: $size\"";
			}
			$maxlength = $attribute->getMaxlength();
			if($maxlength!=""){
				$maxlength = ", \"maxlength: $maxlength\"";
			}
			//Caja de Texto
			if($attribute->getComponent()=='TE'){
				$componentCode = "\t\t<td><?php echo Tag::textField(\"{$fieldName}\"{$size}"
					."{$maxlength}) ?></td>\n";
			}
			//Caja Numérica
			if($attribute->getComponent()=='TN'){
				$componentCode = "\t\t<td><?php echo Tag::numericField(\"{$fieldName}\"{$size}"
					."{$maxlength}) ?></td>\n";
			}
			//Campo Fecha
			if($attribute->getComponent()=='DA'){
				$componentCode = "\t\t<td><?php echo Tag::dateField(\"{$fieldName}\") ?></td>\n";
			}
			if($attribute->getComponent()=='CR'){
				$relation = $attribute->getRelations();
				$relationsList = $relation->getRelationsList();
				$className = Utils::camelize($relation->getTableRelation());
				foreach($relationsList as $relationList){
					$componentCode = "\t\t<td><?php echo Tag::select(\"{$fieldName}\", "
						."\${$className}->find(\"order: {$relation->getFieldOrder()}\"), \"using: "
						."{$relationList->getFieldName()},{$relation->getFieldDetail()}\", \"use_dummy: "
						."yes\") ?></td>\n";
					break;
				}
			}
			if($attribute->getSearch()=='Y'){
				$indexview.= $labelCode;
				$indexview.=$componentCode;
				$indexview.= "\t</tr>\n";
			}
		}
		$indexview.= "\t<tr>\n\t\t<td></td>\n\t\t<td><?php echo Tag::submitButton('Buscar') ?></td>\n\t</tr>\n";
		$indexview.= "</table>\n";
		$indexview.= "<?php echo Tag::endForm(); ?>\n";
		$indexview.= "</div>";
		return $indexview;*/
                $indexview = "<div id='mainContent'>\n\n";
                $indexview.= "<?php echo View::getContent();\n";
                //$indexview.="      echo Tag::jqGrid(\"caption: '$titulo'\",\"typewinedit: 'popup'\",\"optwinedit: 'width=590,height=400,scrollbars=yes'\");\n";
                $indexview.="      echo Tag::jqGrid(\"caption: '$titulo'\");\n";
                $indexview.="?>\n";
                $indexview.="</div>";
                return $indexview;
	}

	/**
	 * Retorna una cadena de caracteres en la cual esta el contenido del archivo de la vista Buscar.
	 *
	 * @param String $controlador
	 * @param String $titulo
	 * @return String Contenido del archivo de la vista Buscar.
	 */
	protected static function getSearchViewCode($controlador,$titulo) {
		$searchview = "<div id='mainContent'>\n\n";
		$searchview.= "<?php echo View::getContent(); ?>";
		$searchview.= "<h1>Buscar : {$titulo}</h1>\n\n";
		$searchview.= "<div class='userStatus'>Estado: Visualizar un {$titulo}</div>\n\n";
		$searchview.= "<table cellspacing='0' class='tableResults'>\n";
		$searchview.= "\t<tr>\n";
		$searchview.= "\t\t<thead>\n";
		foreach(self::$_attributes as $attribute){
			$fieldName = Utils::lcfirst(Utils::camelize($attribute->getFieldName()));
			if($attribute->getBrowse()=='Y'){
				$searchview.= "\t\t\t<th><?php echo Tag::linkTo(\"$controlador/visualizar?ordenar={$fieldName}".
					"\", \"{$attribute->getLabel()}\") ?></th>\n";
			}
		}
		$searchview.="\t\t\t<th width=\"7%\"></th>\n";
		$searchview.="\t\t\t<th width=\"7%\"></th>\n";
		$searchview.= "\t\t</thead>\n";
		$searchview.= "\t</tr>\n";
		$searchview.= "\t<?php\n";
		$searchview.= "\tif(count(\$resultados)>0){ \n";
		$searchview.= "\t\t\$resultadosPagina = Tag::paginate(\$resultados, \$pagina, 15);\n";
		$searchview.= "\t\tforeach(\$resultadosPagina->items as \$resultado){\n";
		$searchview.= "\t\t\techo Tag::trClassName(array('trResults1', 'trResults2'));\n";
		$browseNumber = 0;
		$primaryKey = array();
		foreach(self::$_attributes as $attribute){
			if($attribute->getPrimaryKey()=='Y'){
				$primaryKey[] = $attribute->getFieldName();
			}
			if($attribute->getBrowse()=='Y'){
				$fieldName = Utils::camelize($attribute->getFieldName());
				$browseNumber++;
				if(strpos($attribute->getType(), "char")!==false){
					$searchview.="\t\t\tprint \"<td>\".\$resultado->get".$fieldName
						."().\"</td>\";\n";
				} else {
					$searchview.="\t\t\tprint \"<td align='right'>\".\$resultado->get"
						.$fieldName."().\"</td>\";\n";
				}
			}
		}
		if(count($primaryKey)){
			$urlItems = array();
			if(count($primaryKey)>1){
				foreach($primaryKey as $key){
					$urlItems[] = "{\$resultado->get".ucfirst($key)."()}";
				}
				$url = join("/", $urlItems);
			} else {
				$fieldName = Utils::lcfirst(Utils::camelize($primaryKey[0]));
				$url = "{\$resultado->get".ucfirst($primaryKey[0])."()}";
			}
			$searchview.="\t\t\tprint \"<td align='center'>\".Tag::buttonToAction(\"Editar\", \"$controlador"
				."/editar/$url\", \"class: editButton\").\"</td>\";\n";
			$searchview.="\t\t\tprint \"<td align='center'>\".Tag::buttonToAction(\"Eliminar\", \"$controlador"
				."/eliminar/$url\", \"class: deleteButton\").\"</td>\";\n";
		}

		$searchview.= "\t\t\tprint \"</tr>\";\n";
		$searchview.= "\t\t} \n";
		$searchview.= "\t?>\n";
		$searchview.= "\t<tr class='resultsNavigator'>\n";
		$searchview.= "\t\t<td colspan='$browseNumber' align='right'>\n";
		$searchview.= "\t\t\t<?php echo Tag::form(\"$controlador/visualizar\",'autocomplete: off') ?>\n";
		$searchview.= "\t\t\t<table class='resultsNavigatorControls' cellspacing='0'>\n";
		$searchview.= "\t\t\t\t<tr>\n";
		$searchview.= "\t\t\t\t\t<td><?php echo Tag::linkTo(\"$controlador/visualizar?pagina=1\", \"<div "
			."class='goToFirstButton'></div>\") ?></td>\n";
		$searchview.= "\t\t\t\t\t<td><?php echo Tag::linkTo(\"$controlador/visualizar?pagina=\"."
			."\$resultadosPagina->before, \"<div class='goPrevButton'></div>\") ?></td>\n";
		$searchview.= "\t\t\t\t\t<td><?php echo Tag::numericField(\"pagina\", \"size: 3\", \"value: "
			."{\$resultadosPagina->current}\") ?> de <?php echo \$resultadosPagina->total_pages ?></td>\n";
		$searchview.= "\t\t\t\t\t<td><?php echo Tag::linkTo(\"$controlador/visualizar?pagina=\"."
			."\$resultadosPagina->next, \"<div class='goNextButton'></div>\") ?></td>\n";
		$searchview.= "\t\t\t\t\t<td><?php echo Tag::linkTo(\"$controlador/visualizar?pagina=\"."
			."\$resultadosPagina->total_pages, \"<div class='goToLastButton'></div>\") ?></td>\n";
		$searchview.= "\t\t\t\t</tr>\n";
		$searchview.= "\t\t\t</table>\n";
		$searchview.= "\t\t\t<?php echo Tag::endForm() ?>\n";
		$searchview.= "\t\t</td>\n";
		$searchview.= "\t</tr>\n";
		$searchview.= "\t<?php\n";
		$searchview.= "\t} else {\n";
		$searchview.= "\t\tprint \"<tr><td colspan='$browseNumber' align='center'>NO HAY RESULTADOS EN LA "
			."B&Uacute;SQUEDA</td>\";\n";
		$searchview.= "\t}\n";
		$searchview.= "\t?>\n";
		$searchview.= "</table>\n";
		$searchview.= "<div align='right' class='backButtonDiv'>\n";
		$searchview.= "\t<?php echo Tag::buttonToAction(\"Volver\", \"$controlador/index\") ?>\n";
		$searchview.= "</div>\n";
		$searchview.= "</div>\n";
		return $searchview;
	}

	/**
	 * Retorna una cadena de caracteres en la cual esta el contenido del archivo de la vista Nuevo.
	 *
	 * @param String $controlador
	 * @param String $titulo
	 * @return String Contenido del archivo de la vista Nuevo.
	 */
	protected static function getNewViewCode($controlador,$titulo) {
		/*$nuevoview = "<div id='mainContent'>\n\n";
		$nuevoview.= "<?php echo View::getContent(); ?>\n";
		$nuevoview.= "<h1>{$titulo}</h1>\n\n";
		$nuevoview.= "<div class='userStatus'>Estado: Creando un {$titulo}</div>\n\n";
		$nuevoview.= "<?php echo Tag::form(\"$controlador/guardar\",'autocomplete: off'); ?>\n";
		$nuevoview.= "<table class='tableFormNuevo' cellspacing='0'>\n";
		foreach(self::$_attributes as $attribute){
			$labelCode = "\t<tr>\n\t\t<td align='right'><label for='{$attribute->getFieldName()}'><b>"
				.$attribute->getLabel()."</b>:</label></td>\n";
			$size = $attribute->getSize();
			if($size!=""){
				$size = ", \"size: $size\"";
			}
			$maxlength = $attribute->getMaxlength();
			if($maxlength!=""){
				$maxlength = ", \"maxlength: $maxlength\"";
			}
			if($attribute->getComponent()=='TE'){
				$componentCode = "\t\t<td><?php echo Tag::textField(\"{$attribute->getFieldName()}\""
					.$size.$maxlength.") ?></td>\n";
			}
			if($attribute->getComponent()=='TN'){
				$componentCode = "\t\t<td><?php echo Tag::numericField(\"{$attribute->getFieldName()}\""
					.$size.$maxlength.") ?></td>\n";
			}
			if($attribute->getComponent()=='DA'){
				$componentCode = "\t\t<td><?php echo Tag::dateField(\"{$attribute->getFieldName()}\") ?></td>\n";
			}
			if($attribute->getComponent()=='CR'){
				$relation = $attribute->getRelations();
				$relationsList = $relation->getRelationsList();
				$className = Utils::camelize($relation->getTableRelation());
				foreach($relationsList as $relationList){
					$componentCode = "\t\t<td><?php echo Tag::select(\"{$attribute->getFieldName()}\", \$"
						."{$className}->find(\"order: {$relation->getFieldOrder()}\"), \"using: "
						."{$relationList->getFieldName()},{$relation->getFieldDetail()}\", \"use_dummy: "
						."yes\") ?></td>\n";
					break;
				}
			}
			$nuevoview.= $labelCode;
			$nuevoview.=$componentCode;
			$nuevoview.= "\t</tr>\n";
		}

		$nuevoview.= "\t<tr>\n\t\t<td></td>\n\t\t<td><?php echo Tag::submitButton(\"Crear\") ?>
		<?php echo Tag::linkTo(\"$controlador/index\", \"Cancelar\") ?></td>\n\t</tr>\n";
		$nuevoview.= "</table>\n";
		$nuevoview.= "<?php echo Tag::endForm(); ?>\n";
		$nuevoview.= "</div>\n";
		return $nuevoview;*/

                $nuevoview = "<div id='mainContent'>\n\n";
		$nuevoview.= "\t<?php echo View::getContent();\n";
                $nuevoview.="\t\t echo Tag::form('$controlador/guardar');\n";
                $nuevoview.="\t?>\n";
                $nuevoview.="\t<fieldset class='ui-corner-all ui-widget-content'>\n";
                $nuevoview.="\t\t<legend>Datos de {$titulo}</legend>\n";
                foreach(self::$_attributes as $attribute){
                      if($attribute->getHidden()=='N'){
                            $labelCode="\t\t<p><label class='labelform' ><span for='{$attribute->getFieldName()}'><b>".$attribute->getLabel()."</b>:</span></label>";
                            $size = $attribute->getSize();
                            if($size!=""){
                                    $size = ", \"size: $size\"";
                            }
                            $maxlength = $attribute->getMaxlength();

                            if($maxlength!=""){
                                    $maxlength = ", \"maxlength: $maxlength\"";
                            }
                            $tclass="";
                            if($attribute->getAllowNull()!="Y")  $tclass.=" required";

                            if($attribute->getComponent()=='TN')  $tclass.=" numeric";

                            if($attribute->getComponent()=='DA')  $tclass.=" dateISO";

                            if($attribute->getComponent()=='EM')  $tclass.=" email";

                            if($attribute->getComponent()=='URL') $tclass.=" url";

                            $class=",\"class: $tclass\"";

                            if($attribute->getComponent()=='TE' || $attribute->getComponent()=='TN' || $attribute->getComponent()=='DA' || $attribute->getComponent()=='EM' || $attribute->getComponent()=='URL'){
                                    $componentCode = "<?php echo Tag::textField(\"{$attribute->getFieldName()}\""
                                            .$size.$maxlength.$class.") ?>";
                            }
                            if($attribute->getComponent()=='CHK'){
                                $componentCode="<?php echo Tag::checkboxField(\"{$attribute->getFieldName()}\");?>";
                            }
                            if($attribute->getComponent()=='TA'){
                                $componentCode="<?php echo Tag::textArea(\"{$attribute->getFieldName()}\",\"cols: 90\",\"rows: 15\");?>";
                            }
                            if($attribute->getComponent()=='CO'){
                                $componentCode="<?php echo Tag::hiddenField(\"{$attribute->getFieldName()}\");?>\n";
                            }

                            /*if($attribute->getComponent()=='TN'){
                                    $componentCode = "<?php echo Tag::numericField(\"{$attribute->getFieldName()}\""
                                            .$size.$maxlength.") ?>";
                            }
                            if($attribute->getComponent()=='DA'){
                                    $componentCode = "<?php echo Tag::dateField(\"{$attribute->getFieldName()}\") ?>";
                            }*/
                            if($attribute->getComponent()=='CR'){
                                    $relation = $attribute->getRelations();
                                    $relationsList = $relation->getRelationsList();
                                    $className = Utils::camelize($relation->getTableRelation());
                                    foreach($relationsList as $relationList){
                                            $componentCode = "<?php echo Tag::select(\"{$attribute->getFieldName()}\", \$"
                                                    ."{$className}->find(\"order: {$relation->getFieldOrder()}\"), \"using: "
                                                    ."{$relationList->getFieldName()},{$relation->getFieldDetail()}\", \"use_dummy: "
                                                    ."yes\",\"dummyValue: ''\"".$class.") ?>";
                                            break;
                                    }
                            }
                            if($attribute->getComponent()!='CO'){
                                $nuevoview.= $labelCode;
                                $nuevoview.=$componentCode;
                                $nuevoview.="</p>\n";
                            }else{
                                $nuevoview.=$componentCode;
                            }
                      }
		}
                $nuevoview.="\t</fieldset>\n";
                $nuevoview.="\t<div class='ui-corner-all ui-widget-content'>\n";
		$nuevoview.= "\t\t<?php echo Tag::submitButton(\"Crear\"); echo Tag::button(\"Cancelar\",\"onclick: window.location='index';\"); ?>\n";
		$nuevoview.="\t</div>\n";
		$nuevoview.= "\t<?php echo Tag::endForm(); ?>\n\n";
		$nuevoview.= "</div>\n";
		return $nuevoview;
	}

	/**
	 * Retorna una cadena de caracteres en la cual esta el contenido del archivo de la vista Editar.
	 *
	 * @param String $controlador
	 * @param String $titulo
	 * @return String Contenido del archivo de la vista Editar.
	 */
	protected static function getEditViewCode($controlador,$titulo) {
		/*$editarview = "<?php echo View::getContent(); ?>";
		$editarview.= "<h1>{$titulo}</h1>\n\n";
		$editarview.= "<div class='userStatus'>Estado: Editando un {$titulo}</div>\n\n";
		$editarview.= "<?php echo Tag::form(\"$controlador/guardar/true\",'autocomplete: off'); ?>\n";
		$editarview.= "<table class='tableFormEditar' cellspacing='0'>\n";
		foreach(self::$_attributes as $attribute){
			$labelCode = "\t<tr>\n\t\t<td align='right'><label for='{$attribute->getFieldName()}'><b>"
				.$attribute->getLabel()."</b>:</label></td>\n";
			$size = $attribute->getSize();
			if($size!=""){
				$size = ", \"size: $size\"";
			}
			$maxlength = $attribute->getMaxlength();
			if($maxlength!=""){
				$maxlength = ", \"maxlength: $maxlength\"";
			}
			if($attribute->getComponent()=='TE'){
				$componentCode = "\t\t<td><?php echo Tag::textField(\"{$attribute->getFieldName()}\""
					.$size.$maxlength.") ?></td>\n";
			}
			if($attribute->getComponent()=='TN'){
				$componentCode = "\t\t<td><?php echo Tag::numericField(\"{$attribute->getFieldName()}\""
					.$size.$maxlength.") ?></td>\n";
			}
			if($attribute->getComponent()=='DA'){
				$componentCode = "\t\t<td><?php echo Tag::dateField(\"{$attribute->getFieldName()}\") ?></td>\n";
			}
			if($attribute->getComponent()=='CR'){
				$relation = $attribute->getRelations();
				$relationsList = $relation->getRelationsList();
				$className = Utils::camelize($relation->getTableRelation());
				foreach($relationsList as $relationList){
					$componentCode = "\t\t<td><?php echo Tag::select(\"{$attribute->getFieldName()}\", \$"
						."{$className}->find(\"order: {$relation->getFieldOrder()}\"), \"using: "
						."{$relationList->getFieldName()},{$relation->getFieldDetail()}\", \"use_dummy: "
						."yes\") ?></td>\n";
					break;
				}
			}
			$editarview.= $labelCode;
			$editarview.=$componentCode;
			$editarview.= "\t</tr>\n";
		}
		$editarview.= "\t<tr>\n\t\t<td></td>\n\t\t<td><?php echo Tag::submitButton(\"Guardar\") ?>
		<?php echo Tag::linkTo(\"$controlador/index\", \"Cancelar\") ?></td>\n\t</tr>\n";
		$editarview.= "</table>\n";
		$editarview.= "<?php echo Tag::endForm(); ?>\n";
		return $editarview;*/
                $editarview = "<div id='mainContent'>\n\n";
		$editarview.= "\t<?php echo View::getContent();\n";
                $editarview.="\t\t echo Tag::form('$controlador/guardar/true');\n";
                $editarview.="\t?>\n";
                $editarview.="\t<fieldset class='ui-corner-all ui-widget-content'>\n";
                $editarview.="\t\t<legend>Datos de {$titulo}</legend>\n";
                foreach(self::$_attributes as $attribute){
                    if($attribute->getHidden()=='N'){
                        $labelCode="\t\t<p><label class='labelform' ><span for='{$attribute->getFieldName()}'><b>".$attribute->getLabel()."</b>:</span></label>";
			$size = $attribute->getSize();
			if($size!=""){
				$size = ", \"size: $size\"";
			}
			$maxlength = $attribute->getMaxlength();

			if($maxlength!=""){
				$maxlength = ", \"maxlength: $maxlength\"";
			}
                        $tclass="";
                        if($attribute->getAllowNull()!="Y")  $tclass.=" required";

                        if($attribute->getComponent()=='TN')  $tclass.=" numeric";

                        if($attribute->getComponent()=='DA')  $tclass.=" dateISO";

                        if($attribute->getComponent()=='EM')  $tclass.=" email";

                        if($attribute->getComponent()=='URL') $tclass.=" url";

                        $class=",\"class: $tclass\"";

			if($attribute->getComponent()=='TE' || $attribute->getComponent()=='TN' || $attribute->getComponent()=='DA' || $attribute->getComponent()=='EM' || $attribute->getComponent()=='URL'){
				$componentCode = "<?php echo Tag::textField(\"{$attribute->getFieldName()}\""
					.$size.$maxlength.$class.") ?>";
			}
                        if($attribute->getComponent()=='CHK'){
                            $componentCode="<?php echo Tag::checkboxField(\"{$attribute->getFieldName()}\");?>";
                        }
                        if($attribute->getComponent()=='TA'){
                            $componentCode="<?php echo Tag::textArea(\"{$attribute->getFieldName()}\",\"cols: 90\",\"rows: 15\");?>";
                        }
                        if($attribute->getComponent()=='CO'){
                                $componentCode="<?php echo Tag::hiddenField(\"{$attribute->getFieldName()}\");?>\n";
                        }
			/*if($attribute->getComponent()=='TN'){
				$componentCode = "<?php echo Tag::numericField(\"{$attribute->getFieldName()}\""
					.$size.$maxlength.") ?>";
			}
			if($attribute->getComponent()=='DA'){
				$componentCode = "<?php echo Tag::dateField(\"{$attribute->getFieldName()}\") ?>";
			}*/
			if($attribute->getComponent()=='CR'){
				$relation = $attribute->getRelations();
				$relationsList = $relation->getRelationsList();
				$className = Utils::camelize($relation->getTableRelation());
				foreach($relationsList as $relationList){
					$componentCode = "<?php echo Tag::select(\"{$attribute->getFieldName()}\", \$"
						."{$className}->find(\"order: {$relation->getFieldOrder()}\"), \"using: "
						."{$relationList->getFieldName()},{$relation->getFieldDetail()}\", \"use_dummy: "
						."yes\",\"dummyValue: ''\"".$class.") ?>";
					break;
				}
			}
                        if($attribute->getComponent()!='CO'){
                            $editarview.= $labelCode;
                            $editarview.=$componentCode;
                            $editarview.="</p>\n";
                        }else{
                            $editarview.=$componentCode;
                        }
                    }
		}
                $editarview.="\t</fieldset>\n";
                $editarview.="\t<div class='ui-corner-all ui-widget-content'>\n";
		$editarview.= "\t\t<?php echo Tag::submitButton(\"Grabar\"); echo Tag::button(\"Cancelar\",\"onclick: window.location='../index';\"); ?>\n";
		$editarview.="\t</div>\n";
		$editarview.= "\t<?php echo Tag::endForm(); ?>\n\n";
		$editarview.= "</div>\n";
		return $editarview;
	}

	/**
	 * Retorna una cadena de caracteres que contiene el archivo del Controlador.
	 *
	 * @param 	string $controlador
	 * @param 	string $source
	 * @return 	string
	 * @static
	 */
	protected static function getControllerCode($controlador, $source){

		$controladorVar = Utils::lcfirst(Utils::camelize($controlador));
		$model = Utils::camelize($source);
		$controller = "<?php\n\n";
		$controller.= "/**\n";
 		$controller.= " * Controlador ".ucfirst($controlador)."\n";
		$controller.= " *\n";
		$controller.= " * @access public\n";
		$controller.= " * @version 1.0\n";
		$controller.= " */\n";
		$controller.= "class ".ucfirst($controlador)."Controller extends ApplicationController {\n\n";

		$icontroller = '';
                $aurl='';
                $addUrlItems=array();
		foreach(self::$_attributes as $attribute){
                        if($attribute->getPrimaryKey()=='Y')
                          $addUrlItems[] = "\${$attribute->getFieldName()}=null";
			$fieldName = Utils::lcfirst(Utils::camelize($attribute->getFieldName()));
			$controller.= "\t/**\n";
			$controller.= "\t * ".$attribute->getFieldName()."\n";
			$controller.= "\t *\n";
			if(strpos($attribute->getType(), "int")!==false){
				$controller.= "\t * @var int\n";
			}
			if(strpos($attribute->getType(), "decimal")!==false){
				$controller.= "\t * @var double\n";
			}
			if(strpos($attribute->getType(), "number")!==false){
				$controller.= "\t * @var double\n";
			}
			if(strpos($attribute->getType(), "char")!==false){
				$controller.= "\t * @var string\n";
			}
			$controller.= "\t */\n";
			$controller.= "\tpublic \$$fieldName;\n\n";
                        
			/*$icontroller.= "\t\t\$this->$fieldName = '';\n";
			$icontroller.= "\t\tTag::displayTo('$fieldName', '');\n";*/
                        if($attribute->getSearch()=='Y'){
                            $icontroller.="\t\t\$columnsGrid[]=array('name'=>'".$attribute->getLabel()."','index'=>'".$attribute->getFieldName()."');\n";
                            
                        }
		}
                if(count($addUrlItems)>0)
                    $aurl=",".join(",", $addUrlItems);
		//$controller.= "\t/**\n";
		//$controller.= "\t * Condiciones de búsqueda temporales\n";
		//$controller.= "\t *\n";
		//$controller.= "\t * @var string\n";
		//$controller.= "\t */\n";
		//$controller.= "\tpublic \$condiciones;\n\n";
                
		//$controller.= "\t/**\n";
		//$controller.= "\t * Ordenamiento de la visualización\n";
		//$controller.= "\t *\n";
		//$controller.= "\t * @var string\n";
		//$controller.= "\t */\n";
		//$controller.= "\tpublic \$ordenamiento;\n\n";

		//$controller.= "\t/**\n";
		//$controller.= "\t * Página actual en la visualización\n";
		//$controller.= "\t *\n";
		//$controller.= "\t * @var int\n";
		//$controller.= "\t */\n";
		//$controller.= "\tpublic \$pagina;\n\n";

		$controller.= "\t/**\n";
		$controller.= "\t * Inicializador del controlador/\n";
		$controller.= "\t *\n";
		$controller.= "\t */\n";
		$controller.= "\tpublic function initialize(){\n";
		$controller.= "\t\t\$this->setPersistance(true);\n";
		$controller.= "\t}\n\n";

		$controller.= "\t/**\n";
		$controller.= "\t * Acción por defecto del controlador/\n";
		$controller.= "\t *\n";
		$controller.= "\t */\n";
		$controller.= "\tpublic function indexAction(){\n";

                if($icontroller!=""){
                   // $controller.= "\t\t\$this->setResponse('ajax');\n";
                    $controller.=$icontroller;
                    $controller.="\t\t Tag::setColumnsToGrid(\$columnsGrid);\n";
                }
		
		$controller.= "\t}\n\n";

		$controller.= "\t/**\n";
		$controller.= "\t * Crear un $model/\n";
		$controller.= "\t *\n";
		$controller.= "\t */\n";
		$controller.= "\tpublic function nuevoAction(){\n\n";
		$controller.= "\t}\n\n";

		$gcontroller = "\t/**\n";
		$gcontroller.= "\t * Guardar el $model\n";
		$gcontroller.= "\t *\n";
		$gcontroller.= "\t */\n";
		$gcontroller.= "\tpublic function guardarAction(\$isEdit=false$aurl){\n\n";

		//$controller.= "\t/**\n";
		//$controller.= "\t * Realiza una busqueda de registros en $model\n";
		//$controller.= "\t *\n";
		//$controller.= "\t */\n";
		//$controller.= "\tpublic function buscarAction(){\n";
		//$controller.="\n";

		$localcodeuf = array();
		$editcode = '';

		foreach(self::$_attributes as $attribute){
			$fieldName = Utils::lcfirst(Utils::camelize($attribute->getFieldName()));
			$filters = array();
			//print $attribute->getType();
			if(stripos($attribute->getType(), "int")!==false){
				$filters[] = "\"int\"";
			}
			if(stripos($attribute->getType(), "decimal")!==false){
				$filters[] = "\"double\"";
			}
			if(stripos($attribute->getType(), "number")!==false){
				$filters[] = "\"double\"";
			}
			if(stripos($attribute->getType(), "char")!==false&&$attribute->getSize()==1){
				$filters[] = "\"onechar\"";
			} else {
				if(stripos($attribute->getType(), "char")!==false){
					$filters[] = "\"striptags\"";
					$filters[] = "\"extraspaces\"";
				}
			}
			if(count($filters)>0){
				$localcode = "\t\t\$$fieldName = \$this->getPostParam(\"{$attribute->getFieldName()}\");\n";
				$localcodeuf[$fieldName] = "\t\t\$$fieldName = \$this->getPostParam(\"{$attribute->getFieldName()}\", "
					.join(", ", $filters).");\n";
			} else {
				$localcode = "\t\t\$$fieldName = \$this->getPostParam(\"{$attribute->getFieldName()}\");\n";
				$localcodeuf[$fieldName] = "\t\t\$$fieldName = \$this->getPostParam(\"{$attribute->getFieldName()}\");\n";
			}
			/*if($attribute->getSearch()=='Y'){
				$controller.=$localcode;
			}*/
                        if($attribute->getPrimaryKey()=='N')
			      $gcontroller.=$localcodeuf[$fieldName];
                        if($attribute->getPrimaryKey()=='Y' && count($filters)>0){
				$editcode.= "\t\t\$$fieldName = \$filter->applyFilter(\$$fieldName,".join(", ", $filters).");\n";
			}
		}
		//$controller.="\n";
		//$controller.="\t\t\$condiciones = array();\n";

		$gcontroller.="\t\t\$$controladorVar = new ".Utils::camelize($controlador)."();\n";

		$primaryKey = array();
		$editUrlItems = array();
		//$findItems = array();
		foreach(self::$_attributes as $attribute){
			if($attribute->getPrimaryKey()=='Y'){
				$primaryKey[] = $attribute->getFieldName();
				$editUrlItems[] = "\${$attribute->getFieldName()}=null";
				//$findItems[] = "'{$attribute->getFieldName()} = '.\${$attribute->getFieldName()}";
			}
			$fieldName = Utils::lcfirst(Utils::camelize($attribute->getFieldName()));
			/*if($attribute->getSearch()=='Y'){
				if($attribute->getComponent()=='CR'){
					$controller.="\t\tif(\$$fieldName!=\"@\"){\n";
				} else {
					$controller.="\t\tif(\$$fieldName!==\"\"){\n";
				}
				if(in_array($attribute->getComponent(), array('TE', 'TA'))){
					$controller.="\t".$localcodeuf[$fieldName];
					$controller.="\t\t\t\$$fieldName = preg_replace(\"/[ ]+/\", \" \", \$$fieldName);\n";
					$controller.="\t\t\t\$$fieldName = str_replace(\" \", \"%\", \$$fieldName);\n";
					$controller.="\t\t\t\$condiciones[] = \"".$attribute->getFieldName()
						." LIKE '%\$$fieldName%'\";\n";
				} else {
					$controller.=$localcodeuf[$fieldName];
					$controller.="\t\t\t\$condiciones[] = \"".$attribute->getFieldName()
						." = '\$$fieldName'\";\n";
				}
				$controller.="\t\t}\n";
			}*/
			$gcontroller.= "\t\t\${$controladorVar}->set".ucfirst($fieldName)."(\$$fieldName);\n";
		}

		$econtroller = "\t/**\n";
		$econtroller.= "\t * Editar el $model\n";
		$econtroller.= "\t *\n";
		$econtroller.= "\t */\n";
		$eurl = join(",", $editUrlItems);
		$econtroller.= "\tpublic function editarAction($eurl){\n\n";
		$econtroller.= "\t\t\$filter = new Filter();\n";
		$econtroller.= $editcode;
		$econtroller.= "\t\t\$$controladorVar = \$this->".ucfirst($controlador)."->findFirst($"
			.join(',$',$primaryKey).");\n";
		$econtroller.= "\t\tif(\$$controlador){\n";

		$dcontroller = "\t/**\n";
		$dcontroller.= "\t * Eliminar el $model\n";
		$dcontroller.= "\t *\n";
		$dcontroller.= "\t */\n";
		//$dcontroller.= "\tpublic function eliminarAction($eurl){\n\n";
                $dcontroller.= "\tpublic function eliminarAction(){\n\n";
                $dcontroller.= "\t\t\$this->setResponse('ajax');\n";
		//$dcontroller.= "\t\t\$filter = new Filter();\n";
		//$dcontroller.= $editcode;
                $dcontroller.="\t\t\$msg='';\n";
                
		/*$dcontroller.= "\t\t\$$controlador = \$this->".ucfirst($controlador)."->count("
			.join(' AND ',$findItems).");\n";
		$dcontroller.= "\t\tif(\$$controlador==1){\n";*/
                $dcontroller.= "\t\tif(\$this->getPostParam('oper')=='del'){\n";
                $dcontroller.="\t\t\t\$ids=explode(',',\$this->getPostParam('id'));\n";
                $dcontroller.="\t\t\tfor(\$i=0;\$i<count(\$ids);\$i++){\n";
		//$dcontroller.= "\t\t\tif(!\$this->".ucfirst($controlador)."->delete(".join(' AND ',$findItems).")){\n";
                $dcontroller.= "\t\t\t\tif(!\$this->".ucfirst($controlador)."->delete(\$ids[\$i])){\n";
		$dcontroller.= "\t\t\t\t\t\$msg.=\"El registro \$ids[\$i] no pudo ser eliminado.\";\n";
		$dcontroller.= "\t\t\t\t}else {\n";
		//$dcontroller.= "\t\t\t\tFlash::success('El registro fue eliminado correctamente.');\n";
                $dcontroller.= "\t\t\t\t\$msg.=\"El registro \$ids[\$i] fue eliminado correctamente.\";\n";
		$dcontroller.= "\t\t\t\t}\n";
                $dcontroller.="\t\t\t}\n";
                $dcontroller.="\t\t}\n";
                $dcontroller.="\t\techo \$msg;\n";
		$dcontroller.= "\t}\n";

		$gcontroller.= "\t\tif(\${$controladorVar}->save()==false){\n";
		$gcontroller.= "\t\t\tFlash::error('Hubo un error guardando el registro.');\n";
		$gcontroller.= "\t\t}else {\n";
		$gcontroller.= "\t\t\tFlash::success('Registro guardado con éxito.');\n";
		$gcontroller.= "\t\t}\n\n";
		$gcontroller.= "\t\t\$this->routeTo('action: index');\n";
		$gcontroller.= "\t}\n\n";

		/*$controller.="\t\tif(count(\$condiciones)>0){\n";
		$controller.="\t\t\t\$this->condiciones = join(\" OR \", \$condiciones);\n";
		$controller.="\t\t} else {\n";
		$controller.="\t\t\t\$this->condiciones = \"\";\n";
		$controller.="\t\t}\n";
		$controller.="\t\t\$this->ordenamiento = \"1\";\n";
		$controller.="\t\t\$this->routeTo(\"action: visualizar\");\n";
		$controller.= "\t}\n\n";*/

		//$controller.= "\t/**\n";
		//$controller.= "\t * Visualiza los registros encontrados en la busqueda\n";
		//$controller.= "\t *\n";
		//$controller.= "\t */\n";
		//$controller.= "\tpublic function visualizarAction(){\n";
		//$controller.="\n";
		//$controller.="\t\t\$controllerRequest = ControllerRequest::getInstance();\n";
		//$controller.="\t\tif(\$controllerRequest->isSetQueryParam(\"ordenar\")){\n";
		//$controller.="\t\t\t\$posibleOrdenar = array(\n";
		//$posibleOrdenar = array();*/
		foreach(self::$_attributes as $attribute){
			$fieldName = Utils::lcfirst(Utils::camelize($attribute->getFieldName()));
			$econtroller.= "\t\t\tTag::displayTo('{$attribute->getFieldName()}', \${$controladorVar}->get".ucfirst($fieldName)
				."());\n";
		}

		$econtroller.= "\t\t}else {\n";
		$econtroller.= "\t\t\tFlash::error('Registro no encontrado.');\n";
		$econtroller.= "\t\t\t\$this->routeTo('action: index');\n";
		$econtroller.= "\t\t}\n";
		$econtroller.= "\t}\n\n";

		/*$controller.=join(",\n", $posibleOrdenar);
		$controller.="\n\t\t\t);\n";
		$controller.="\t\t\t\$ordenar = \$controllerRequest->getParamQuery(\"ordenar\", \"alpha\");\n";
		$controller.="\t\t\tif(isset(\$posibleOrdenar[\$ordenar])==true){\n";
		$controller.="\t\t\t\t\$this->ordenamiento = \$posibleOrdenar[\$ordenar];\n";
		$controller.="\t\t\t} else {\n";
		$controller.="\t\t\t\t\$this->ordenamiento = \"1\";\n";
		$controller.="\t\t\t}\n";
		$controller.="\t\t}\n";
		$controller.="\t\tif(\$controllerRequest->isSetRequestParam(\"pagina\")){\n";
		$controller.="\t\t\t\$this->pagina = \$controllerRequest->getParamRequest(\"pagina\", \"int\");\n";
		$controller.="\t\t} else {\n";
		$controller.="\t\t\t\$this->pagina = 1;\n";
		$controller.="\t\t}\n";
		$controller.="\t\tif(\$this->condiciones!=\"\"){\n";
		$controller.="\t\t\t\$resultados = \$this->{$model}->find(\$this->condiciones, \"order: "
			."{\$this->ordenamiento}\");\n";
		$controller.="\t\t} else {\n";
		$controller.="\t\t\t\$resultados = \$this->{$model}->find(\"order: {\$this->ordenamiento}\");\n";
		$controller.="\t\t}\n\n";
		$controller.="\t\t\$this->setParamToView(\"resultados\", \$resultados);\n";
		$controller.= "\t}\n\n";*/

                $grdcontroller="\tpublic function obtenerDatosGridAction(){\n";
                $grdcontroller.="\t\t\$this->setResponse('ajax');  // asignamos el tipo de respuesta para esta accion\n";
                $grdcontroller.="\t\t\$pagina = \$this->getPostParam('page'); // obtener el numero de pagina\n";
                $grdcontroller.="\t\t\$limite = \$this->getPostParam('rows'); // obtener el n�mero de filas que queremos tener en el grid\n";
                $grdcontroller.="\t\t\$col_orden = \$this->getPostParam('sidx'); // Obtener la fila de �ndice - es decir, cuando el usuario haga clic en la columna para ordenar\n";
                $grdcontroller.="\t\t\$dir_orden = \$this->getPostParam('sord'); // obtener la direcci�n de ordenado\n";
                $grdcontroller.="\t\tif(!\$col_orden) \$col_orden =1;\n";
                $grdcontroller.="\t\t //construccion de condicion de consulta\n\t\t\$condicion='1';\n";
                $grdcontroller.="\t\t\$buscar=\$this->getPostParam('_search','stripslashes'); //obtenemos la variable que activa la busqueda\n";
                $grdcontroller.="\t\t\$strbusqueda=\$this->getPostParam('searchString');  // obtenemos la cadena de busqueda\n";
                $grdcontroller.="\t\t\$campoBusqueda=\$this->getPostParam('searchField'); // obtenemos el campo por el cual se va a realizar la busqueda\n";
                $grdcontroller.="\t\t\$abrevoper=\$this->getPostParam('searchOper');  // obtenemos el operador por el cual se var realizar la busqueda\n";
                $grdcontroller.="\t\t\$filtroBusqueda=\$this->getPostParam('filters','stripslashes');\n";
                $grdcontroller.="\t\t if(\$buscar=='true'){ //verificamos si la busqueda es activada\n";
                $grdcontroller.="\t\t\tif(\$strbusqueda!=''){    // construccion de la cadena de condicion para la busqueda normal \n";
                $grdcontroller.="\t\t\t\tswitch(\$campoBusqueda){\n";
                foreach(self::$_attributes as $attribute){
                    if($attribute->getComponent()=='CR'){
                        $relation = $attribute->getRelations();
                        $tableRelation=$relation->getTableRelation();
                        $tableR=ucfirst($tableRelation);
                        $grdcontroller.="\t\t\t\t\tcase '{$attribute->getFieldName()}':\n";
                        $grdcontroller.="\t\t\t\t\t\t\$cond$tableR=Utils::toSqlParamSearchGrid('{$relation->getFieldDetail()}',\$abrevoper,\$strbusqueda);\n";
                        $grdcontroller.="\t\t\t\t\t\t\$$tableR=\$this->{$tableR}->find(\$cond$tableR);\n";
                        $grdcontroller.="\t\t\t\t\t\tif(count($$tableR)>0){\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\$arrayIds$tableR=array();\n";
                        $grdcontroller.="\t\t\t\t\t\t\tforeach($$tableR as \$fila){\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t\$arrayIds{$tableR}[]=\$fila->getId();\n";
                        $grdcontroller.="\t\t\t\t\t\t\t}\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\$strbusqueda=join(',',\$arrayIds$tableR);\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\$abrevoper='in';\n";
                        $grdcontroller.="\t\t\t\t\t\t}\n";
                        $grdcontroller.="\t\t\t\t\tbreak;\n"; 
                    }
                }
                $grdcontroller.="\t\t\t\t}\n";
                $grdcontroller.="\t\t\t\t\$condicion.=' AND '.Utils::toSqlParamSearchGrid(\$campoBusqueda, \$abrevoper, \$strbusqueda); // construimos la cadena de condicion en base al operador de busqueda\n";
                $grdcontroller.="\t\t\t}elseif(\$filtroBusqueda){\n";
                $grdcontroller.="\t\t\t\t\$jsona = json_decode(\$filtroBusqueda,true);\n";
                $grdcontroller.="\t\t\t\tif(is_array(\$jsona)){\n";
                $grdcontroller.="\t\t\t\t\t\$gopr = \$jsona['groupOp'];\n";
                $grdcontroller.="\t\t\t\t\t\$rules = \$jsona['rules'];\n";
                $grdcontroller.="\t\t\t\t\t\$i =0;\n";
                $grdcontroller.="\t\t\t\t\tforeach(\$rules as \$key=>\$val) {\n";
                $grdcontroller.="\t\t\t\t\t\t\$field = \$val['field'];\n";
                $grdcontroller.="\t\t\t\t\t\tswitch(\$field){\n";
                foreach(self::$_attributes as $attribute){
                    if($attribute->getComponent()=='CR'){
                        $relation = $attribute->getRelations();
                        $tableRelation=$relation->getTableRelation();
                        $tableR=ucfirst($tableRelation);
                        $grdcontroller.="\t\t\t\t\t\t\tcase '{$attribute->getFieldName()}':\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t\$cond$tableR=Utils::toSqlParamSearchGrid('{$relation->getFieldDetail()}',\$val['op'],\$val['data']);\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t\$$tableR=\$this->{$tableR}->find(\$cond$tableR);\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\tif(count($$tableR)>0){\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t\t\$arrayIds$tableR=array();\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t\tforeach($$tableR as \$fila){\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t\t\t\$arrayIds{$tableR}[]=\$fila->getId();\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t\t}\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t\t\$val['data']=join(',',\$arrayIds$tableR);\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t\t\$val['op']='in';\n";
                        $grdcontroller.="\t\t\t\t\t\t\t\t}\n";
                        $grdcontroller.="\t\t\t\t\t\t\tbreak;\n";
                    }
                }
                $grdcontroller.="\t\t\t\t\t\t}\n";
                $grdcontroller.="\t\t\t\t\t\t\$op = \$val['op'];\n";
                $grdcontroller.="\t\t\t\t\t\t\$v = \$val['data'];\n";
                $grdcontroller.="\t\t\t\t\t\tif(\$v && \$op) {\n";
                $grdcontroller.="\t\t\t\t\t\t\t\$i++;\n";
                $grdcontroller.="\t\t\t\t\t\t\t\$v=Utils::toSqlParamSearchGrid(\$field, \$op, \$v);\n";
                $grdcontroller.="\t\t\t\t\t\t\tif (\$i == 1)\n";
                $grdcontroller.="\t\t\t\t\t\t\t\t\$condicion.=' AND ';\n";
                $grdcontroller.="\t\t\t\t\t\t\telse\n";
                $grdcontroller.="\t\t\t\t\t\t\t\t\$condicion.= \" \" .\$gopr.\" \";\n";
                $grdcontroller.="\t\t\t\t\t\t\t\$condicion.= \$v;\n";
                $grdcontroller.="\t\t\t\t\t\t}\n";
                $grdcontroller.="\t\t\t\t\t}\n";
                $grdcontroller.="\t\t\t\t}\n";
                $grdcontroller.="\t\t\t}\n";
                $grdcontroller.="\t\t\t//construimos la condicion por barra de busqueda del grid\n";
             
                $grdcontroller.="\t\t\t\$sarr = \$_POST;\n";
                $grdcontroller.="\t\t\tforeach( \$sarr as \$k=>\$v) {\n";
                $grdcontroller.="\t\t\t\tswitch (\$k) {\n";
                $fieldGrd=array();
                foreach(self::$_attributes as $attribute){
                    $fieldName = Utils::lcfirst(Utils::camelize($attribute->getFieldName()));
                    if($attribute->getSearch()=="Y"){
                        $fieldGrd[$attribute->getFieldName()]="\$".ucfirst($controlador)."->get".ucfirst($fieldName)."()";
                        $grdcontroller.="\t\t\t\t\tcase '{$attribute->getFieldName()}':\n";
                        $oper='bw';
                        if($attribute->getType()=='int')
                            $oper='eq';
                        if($attribute->getComponent()=='CR'){
                            $relation = $attribute->getRelations();
                            $tableRelation=$relation->getTableRelation();
                            $tableR=ucfirst($tableRelation);
                            $grdcontroller.="\t\t\t\t\t\t\$cond$tableR=Utils::toSqlParamSearchGrid('{$relation->getFieldDetail()}','bw',\$v);\n";
                            $grdcontroller.="\t\t\t\t\t\t\$$tableR=\$this->{$tableR}->find(\$cond$tableR);\n";
                            $grdcontroller.="\t\t\t\t\t\tif(count($$tableR)>0){\n";
                            $grdcontroller.="\t\t\t\t\t\t\t\$arrayIds$tableR=array();\n";
                            $grdcontroller.="\t\t\t\t\t\t\tforeach($$tableR as \$fila){\n";
                            $grdcontroller.="\t\t\t\t\t\t\t\t\$arrayIds{$tableR}[]=\$fila->getId();\n";
                            $grdcontroller.="\t\t\t\t\t\t\t}\n";
                            $grdcontroller.="\t\t\t\t\t\t\t\$v=join(',',\$arrayIds$tableR);\n";
                            $grdcontroller.="\t\t\t\t\t\t\t\$condicion.=' AND '.Utils::toSqlParamSearchGrid(\$k,'in',\$v);\n";
                            $grdcontroller.="\t\t\t\t\t\t}\n";
                            $grdcontroller.="\t\t\t\t\t\telse{\n";
                            $grdcontroller.="\t\t\t\t\t\t\t\$condicion.=' AND 0';\n";
                            $grdcontroller.="\t\t\t\t\t\t}\n";
                            $grdcontroller.="\t\t\t\t\t\tbreak;\n";
                              
                        }else{
                            $grdcontroller.="\t\t\t\t\t\t\$condicion.=' AND '.Utils::toSqlParamSearchGrid(\$k,'$oper',\$v);break;\n";
                        }
                    }
                }
                $grdcontroller.="\t\t\t\t}\n";
                $grdcontroller.="\t\t\t}\n";
                $grdcontroller.="\t\t}\n";
               
                $grdcontroller.="\t\t\$orden=\"\$col_orden \$dir_orden\";  //construimos la cadena de orden\n";
                $grdcontroller.="\t\t //comparar que el grupo no este en grupo_usuario\n";
                $grdcontroller.="\t\t\$contar =\$this->".ucfirst($controlador)."->count(\"conditions: \$condicion\");  //contar el numero total de registros existentes\n";
                $grdcontroller.="\t\t//obtenemos el numero de paginas para el grid\n";
                $grdcontroller.="\t\tif( \$contar >0 ) {\n";
                $grdcontroller.="\t\t\t\$total_pags = ceil(\$contar/\$limite);\n";
                $grdcontroller.="\t\t} else {\n";
                $grdcontroller.="\t\t\t\$total_pags = 0;\n";
                $grdcontroller.="\t\t}\n";
                $grdcontroller.="\t\tif (\$pagina > \$total_pags) \$pagina=\$total_pags;\n";
                $grdcontroller.="\t\t\$inicio = \$limite*\$pagina - \$limite; // no poner \$limite*(\$pagina - 1)\n";
                $grdcontroller.="\t\t if (\$inicio<0) \$inicio = 0;\n";
                $grdcontroller.="\t\t\$limite=\$inicio+\$limite;  // igualamos el limite al total de registros que se obtendra hasta la pagina actual\n";
                $grdcontroller.="\t\t\$resultado=\$this->".ucfirst($controlador)."->find(\"conditions: \$condicion\",\"order: \$orden\",\"limit: \$limite\");\n";
                $grdcontroller.="\t\t//construimos el resultado para el grid como objeto\n";
                $grdcontroller.="\t\t\$jqgrid=null;\n";
                $grdcontroller.="\t\t @\$jqgrid->page = \$pagina;   //pagina de navegacion actual\n";
                $grdcontroller.="\t\t@\$jqgrid->total = \$total_pags; // numero total de paginas en base al numero de registros obtenidos\n";
                $grdcontroller.="\t\t@\$jqgrid->records = \$contar;  // numero total de registros obtenidos\n";
                $grdcontroller.="\t\tif(\$limite>\$contar)\$limite=\$contar;\n";
                $grdcontroller.="\t\tfor(\$i=\$inicio;\$i<\$limite;\$i++){\n";
                $grdcontroller.="\t\t\t\$".ucfirst($controlador)."=\$resultado[\$i];\n";
                 foreach(self::$_attributes as $attribute){
                     if($attribute->getComponent()=='CR'){
                         $relation = $attribute->getRelations();
			 $tableRelation=$relation->getTableRelation();
                        // $relationList = $relation->getRelationsList();
			 //foreach($relationList as $list){
                               $tableR=ucfirst($tableRelation);
			       $grdcontroller.="\t\t\t\$$tableR=$".ucfirst($controlador)."->get$tableR();\n";
                               $fieldGrd[$attribute->getFieldName()]="\${$tableR}->get".ucfirst($relation->getFieldDetail())."()";
                               
			 //}
                     }
                 }
                $grdcontroller.="\t\t\t\$jqgrid->rows[]=array('id'=>$".ucfirst($controlador)."->get".ucfirst($primaryKey[0])."(),'cell'=>array(".join(',',$fieldGrd)."));\n";
                $grdcontroller.="\t\t}\n";
                $grdcontroller.="\t\t//impresion de la respuesta formato json\n";
                $grdcontroller.="\t\t echo \$this->jsonEncode(\$jqgrid);\n";
                
                $grdcontroller.="\t}\n";

		$controller.=$econtroller;
		$controller.=$gcontroller;
		$controller.=$dcontroller;
                $controller.=$grdcontroller;
		$controller.= "}\n\n";
		return $controller;
	}
}

