/*
SQLyog Community Edition- MySQL GUI v8.05 
MySQL - 5.0.51a-community-nt : Database - dbcolas
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`dbcolas` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dbcolas`;

/*Table structure for table `auditoria` */

DROP TABLE IF EXISTS `auditoria`;

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL auto_increment,
  `usuario_id` int(11) NOT NULL,
  `detalle` varchar(255) NOT NULL,
  `controlador` varchar(50) default NULL,
  `accion` varchar(50) default NULL,
  `ip` varchar(45) default NULL,
  `fecha_ingreso` date default NULL,
  `hora_ingreso` time default NULL,
  `fecha_salida` date default NULL,
  `hora_salida` time default NULL,
  `duracion` time default NULL,
  `creacion_at` date default NULL COMMENT '	',
  PRIMARY KEY  (`id`,`usuario_id`),
  KEY `fk_auditoria_usuario1` (`usuario_id`),
  CONSTRAINT `fk_auditoria_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `auditoria` */

/*Table structure for table `banner` */

DROP TABLE IF EXISTS `banner`;

CREATE TABLE `banner` (
  `id` int(11) NOT NULL auto_increment,
  `ubicacion` varchar(100) default NULL,
  `serie` int(11) default NULL,
  `posicion` int(11) default NULL,
  `display_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_banner_display1` (`display_id`),
  CONSTRAINT `fk_banner_display1` FOREIGN KEY (`display_id`) REFERENCES `display` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `banner` */

insert  into `banner`(`id`,`ubicacion`,`serie`,`posicion`,`display_id`) values (1,'banner1.gif',1,1,1),(2,'banner1.gif',1,1,1),(3,'banner.gif',0,1,1),(4,'banner1.gif',0,1,1),(5,'banner.gif',0,1,1);

/*Table structure for table `caja` */

DROP TABLE IF EXISTS `caja`;

CREATE TABLE `caja` (
  `id` int(11) NOT NULL auto_increment,
  `numero` int(11) default NULL,
  `descripcion` varchar(145) default NULL,
  `estado` tinyint(1) default NULL,
  `usuario` varchar(45) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

/*Data for the table `caja` */

insert  into `caja`(`id`,`numero`,`descripcion`,`estado`,`usuario`) values (29,1,'caja principal 1',1,NULL),(30,2,'caja principal p',1,NULL),(31,3,'caja 3',1,NULL);

/*Table structure for table `controlacceso` */

DROP TABLE IF EXISTS `controlacceso`;

CREATE TABLE `controlacceso` (
  `id` int(11) NOT NULL auto_increment,
  `usuario_id` int(11) NOT NULL,
  `ip` varchar(45) default NULL,
  `sesion_inicio` date default NULL,
  `hora_inicio` time default NULL,
  `sesion_fin` date default NULL,
  `hora_fin` time default NULL,
  `duracion` time default NULL,
  `estado` tinyint(1) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_mensajes_usuario1` (`usuario_id`),
  CONSTRAINT `fk_mensajes_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `controlacceso` */

insert  into `controlacceso`(`id`,`usuario_id`,`ip`,`sesion_inicio`,`hora_inicio`,`sesion_fin`,`hora_fin`,`duracion`,`estado`,`creacion_at`) values (1,1,'192.168.1.106','2010-04-13','12:00:00','2010-04-13','14:00:00','02:00:00',0,'0000-00-00');

/*Table structure for table `datospersonales` */

DROP TABLE IF EXISTS `datospersonales`;

CREATE TABLE `datospersonales` (
  `id` int(11) NOT NULL auto_increment,
  `codigo` varchar(45) NOT NULL,
  `conyugeId` int(11) default NULL,
  `tipoSujeto_id` int(11) NOT NULL,
  `apellido_paterno` varchar(45) default NULL,
  `apellido_materno` varchar(45) NOT NULL,
  `primer_nombre` varchar(45) NOT NULL,
  `segundo_nombre` varchar(45) default NULL,
  `titulo_id` int(11) NOT NULL,
  `lugar_nacimiento` varchar(45) default NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` int(11) NOT NULL,
  `genero` varchar(25) NOT NULL,
  `tipo_documento` int(11) NOT NULL default '1',
  `ci` varchar(15) default NULL,
  `ruc` varchar(20) default NULL,
  `pasaporte` varchar(45) default NULL,
  `ubicacion_id` int(11) NOT NULL,
  `direccion_domicilio` varchar(100) NOT NULL,
  `interseccion` varchar(45) default NULL,
  `nomenclatura` varchar(15) default NULL,
  `pos_x` decimal(9,6) default NULL,
  `pos_y` decimal(9,6) default NULL,
  `telefono` varchar(15) NOT NULL,
  `movil` varchar(15) default NULL,
  `email` varchar(45) default NULL,
  `estado` tinyint(1) NOT NULL default '1',
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`,`codigo`),
  KEY `fk_datos_personales_parroquia1` (`ubicacion_id`),
  KEY `fk_datosPersonales_tipoSujeto1` (`tipoSujeto_id`),
  KEY `fk_datosPersonales_titulo1` (`titulo_id`),
  CONSTRAINT `fk_datosPersonales_tipoSujeto1` FOREIGN KEY (`tipoSujeto_id`) REFERENCES `tiposujeto` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datosPersonales_titulo1` FOREIGN KEY (`titulo_id`) REFERENCES `titulo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datos_personales_parroquia1` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicacion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `datospersonales` */

/*Table structure for table `detalleturno` */

DROP TABLE IF EXISTS `detalleturno`;

CREATE TABLE `detalleturno` (
  `usuario_id` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `caja_id` int(11) NOT NULL,
  `fechaEmision` date default NULL,
  `horaEmision` time default NULL,
  `fechaAtencion` date default NULL,
  `horaAtencion` time default NULL,
  PRIMARY KEY  (`usuario_id`,`turno_id`),
  KEY `fk_usuario_has_turno_usuario1` (`usuario_id`),
  KEY `fk_usuario_has_turno_turno1` (`turno_id`),
  KEY `fk_detalleTurno_serviciocaja1` (`servicio_id`,`caja_id`),
  CONSTRAINT `fk_detalleTurno_serviciocaja1` FOREIGN KEY (`servicio_id`, `caja_id`) REFERENCES `serviciocaja` (`servicio_id`, `caja_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuario_has_turno_turno1` FOREIGN KEY (`turno_id`) REFERENCES `turno` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuario_has_turno_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `detalleturno` */

/*Table structure for table `dispensador` */

DROP TABLE IF EXISTS `dispensador`;

CREATE TABLE `dispensador` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(45) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Data for the table `dispensador` */

insert  into `dispensador`(`id`,`descripcion`) values (1,'Dispensador Primario'),(2,'Dispensador Secundario'),(16,'Dispensador tercero'),(17,'Dispensador Hoy');

/*Table structure for table `dispensadorservicio` */

DROP TABLE IF EXISTS `dispensadorservicio`;

CREATE TABLE `dispensadorservicio` (
  `dispensador_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  PRIMARY KEY  (`dispensador_id`,`servicio_id`),
  KEY `fk_dispensador_has_servicio_dispensador1` (`dispensador_id`),
  KEY `fk_dispensador_has_servicio_servicio1` (`servicio_id`),
  CONSTRAINT `fk_dispensador_has_servicio_dispensador1` FOREIGN KEY (`dispensador_id`) REFERENCES `dispensador` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_dispensador_has_servicio_servicio1` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `dispensadorservicio` */

insert  into `dispensadorservicio`(`dispensador_id`,`servicio_id`) values (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(2,1),(2,2),(2,3),(2,5),(2,6),(2,7),(2,8),(16,2),(16,5),(17,1),(17,4),(17,5),(17,6),(17,8);

/*Table structure for table `display` */

DROP TABLE IF EXISTS `display`;

CREATE TABLE `display` (
  `id` int(11) NOT NULL auto_increment,
  `formato` tinyint(4) default NULL,
  `turnos` varchar(45) default NULL,
  `chkbanner` tinyint(4) default NULL,
  `numvideos` int(11) default NULL,
  `horainicio` time default NULL,
  `horafin` time default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `display` */

insert  into `display`(`id`,`formato`,`turnos`,`chkbanner`,`numvideos`,`horainicio`,`horafin`) values (1,1,'4',0,1,'07:00:00','19:00:00');

/*Table structure for table `displayturno` */

DROP TABLE IF EXISTS `displayturno`;

CREATE TABLE `displayturno` (
  `id` int(11) NOT NULL auto_increment,
  `displayId` int(11) default NULL,
  `numeroturno` varchar(10) default NULL,
  `valor` int(11) default NULL,
  `cajanumero` tinyint(4) default NULL,
  `turno` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

/*Data for the table `displayturno` */

insert  into `displayturno`(`id`,`displayId`,`numeroturno`,`valor`,`cajanumero`,`turno`) values (16,NULL,'A1',NULL,2,''),(17,NULL,'B1',NULL,2,''),(18,NULL,'A2',NULL,1,''),(19,NULL,'B2',NULL,2,'');

/*Table structure for table `empresa` */

DROP TABLE IF EXISTS `empresa`;

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL auto_increment,
  `nombrecomercial` varchar(45) default NULL,
  `razonsocial` varchar(180) default NULL,
  `logosuperior` varchar(180) default NULL,
  `logoprincipal` varchar(180) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `empresa` */

insert  into `empresa`(`id`,`nombrecomercial`,`razonsocial`,`logosuperior`,`logoprincipal`) values (1,'EMPRESA DE SERVICIOS',NULL,NULL,NULL);

/*Table structure for table `estado` */

DROP TABLE IF EXISTS `estado`;

CREATE TABLE `estado` (
  `id` int(11) NOT NULL auto_increment,
  `siglas` varchar(10) default NULL,
  `descripcion` varchar(45) NOT NULL,
  `equivalencia` varchar(20) default NULL,
  `creacion_at` varchar(45) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `estado` */

/*Table structure for table `evento` */

DROP TABLE IF EXISTS `evento`;

CREATE TABLE `evento` (
  `id` int(11) NOT NULL auto_increment,
  `tipo` varchar(45) NOT NULL,
  `descripcion` text,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `evento` */

/*Table structure for table `grupo` */

DROP TABLE IF EXISTS `grupo`;

CREATE TABLE `grupo` (
  `id` int(11) NOT NULL auto_increment,
  `modulo_id` int(11) NOT NULL,
  `nombre_largo` varchar(100) NOT NULL,
  `nombre_corto` varchar(30) NOT NULL,
  `descripcion` text,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_grupo_modulo1` (`modulo_id`),
  CONSTRAINT `fk_grupo_modulo1` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `grupo` */

insert  into `grupo`(`id`,`modulo_id`,`nombre_largo`,`nombre_corto`,`descripcion`,`creacion_at`) values (1,1,'Configuradores','Conf',NULL,'2010-04-13'),(2,2,'Administradores','Admin',NULL,'2010-04-13'),(3,3,'Operadores','operadores',NULL,'2010-04-23'),(4,5,'Dispensadores','Dispensadores',NULL,'2010-06-22'),(5,6,'Cajeros','Cajeros',NULL,'2010-06-22'),(6,7,'Pantallas','pantallas',NULL,'2010-06-23');

/*Table structure for table `grupousuario` */

DROP TABLE IF EXISTS `grupousuario`;

CREATE TABLE `grupousuario` (
  `grupo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`grupo_id`,`usuario_id`),
  KEY `FK_grupo_grupousuario` (`grupo_id`),
  KEY `FK_usuario_grupousuario` (`usuario_id`),
  CONSTRAINT `FK_grupo_grupousuario` FOREIGN KEY (`grupo_id`) REFERENCES `grupo` (`id`),
  CONSTRAINT `FK_usuario_grupousuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `grupousuario` */

insert  into `grupousuario`(`grupo_id`,`usuario_id`,`creacion_at`) values (1,1,'2010-04-13'),(2,1,'2010-04-14'),(3,1,'2010-04-26'),(3,3,'2010-04-14'),(4,5,'2010-06-22'),(4,6,'2010-06-23'),(4,25,'2010-07-05'),(5,7,NULL),(5,8,NULL),(5,32,NULL);

/*Table structure for table `instruccion` */

DROP TABLE IF EXISTS `instruccion`;

CREATE TABLE `instruccion` (
  `id` int(11) NOT NULL auto_increment,
  `tipoInstruccion_id` int(11) NOT NULL,
  `instituciones_id` int(11) NOT NULL,
  `desde` date NOT NULL,
  `hasta` date NOT NULL,
  `titulo` varchar(45) default NULL,
  `creacion_at` date default NULL,
  `datosPersonales_id` int(11) NOT NULL,
  `datosPersonales_codigo` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_instruccion_datosPersonales1` (`datosPersonales_id`,`datosPersonales_codigo`),
  CONSTRAINT `fk_instruccion_datosPersonales1` FOREIGN KEY (`datosPersonales_id`, `datosPersonales_codigo`) REFERENCES `datospersonales` (`id`, `codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `instruccion` */

/*Table structure for table `licencia` */

DROP TABLE IF EXISTS `licencia`;

CREATE TABLE `licencia` (
  `id` int(11) NOT NULL auto_increment,
  `licencia` text,
  `fecha_adquisicion` date default NULL,
  `fecha_caducidad` date default NULL,
  `estado` tinyint(1) default '1',
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `licencia` */

/*Table structure for table `menu` */

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id` int(11) NOT NULL auto_increment,
  `modulo_id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `ruta` varchar(255) default NULL,
  `idreferencia` int(11) NOT NULL default '0',
  `estado` tinyint(1) NOT NULL default '1',
  `orden` int(11) NOT NULL default '1',
  `principal` tinyint(1) NOT NULL default '0',
  `posicion` varchar(50) default 'left',
  `tipo_ventana` varchar(20) NOT NULL default '_self',
  PRIMARY KEY  (`id`),
  KEY `fk_menu_modulo1` (`modulo_id`),
  CONSTRAINT `fk_menu_modulo1` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `menu` */

insert  into `menu`(`id`,`modulo_id`,`nombre`,`ruta`,`idreferencia`,`estado`,`orden`,`principal`,`posicion`,`tipo_ventana`) values (1,1,'Monitoreo','/',0,1,1,0,'left','_self'),(2,2,'General','/',0,1,1,1,'left','_self'),(3,2,'Servicios','servicio/index',2,1,1,0,'left','_self'),(4,2,'Cajas','caja',2,1,3,0,'left','_self'),(5,2,'Turno','turno',2,0,4,0,'left','popup'),(6,3,'Operacion','/',0,0,1,1,'left','_self'),(7,2,'Dispensador','dispensador',2,1,5,0,'left','_self'),(15,3,'Turnos de caja','listacaja',1,1,1,1,'left','_self'),(16,3,'Turnos','operador/index/',1,1,2,0,'left','poup'),(17,2,'Reportes','/',0,1,1,1,'left','_self'),(18,2,'Estadisticas','reporte',17,1,1,0,'left','_self'),(19,5,'Dispensador de servicios','/dispensadorservicio',0,1,1,0,'left','_self'),(20,1,'Configuracion','/',0,1,1,0,'left','_self'),(21,2,'Usuarios','usuario',2,1,1,0,'left','_self'),(22,2,'Pantallas','pantalla',2,1,1,0,'left','_self'),(23,2,'Videos','video',2,1,1,0,'left','_self');

/*Table structure for table `modulo` */

DROP TABLE IF EXISTS `modulo`;

CREATE TABLE `modulo` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL default '1',
  `ruta` varchar(255) default NULL,
  `tipo` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `modulo` */

insert  into `modulo`(`id`,`nombre`,`estado`,`ruta`,`tipo`) values (1,'Configuracion',1,NULL,NULL),(2,'Administracion',1,NULL,NULL),(3,'Operacion',1,NULL,NULL),(4,'Reportes',1,NULL,NULL),(5,'Dispensador',1,NULL,NULL),(6,'Cajeros',1,NULL,NULL),(7,'Pantalla',1,NULL,NULL);

/*Table structure for table `pantalla` */

DROP TABLE IF EXISTS `pantalla`;

CREATE TABLE `pantalla` (
  `id` int(11) NOT NULL auto_increment,
  `numero` tinyint(4) default NULL,
  `descripcion` char(200) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*Data for the table `pantalla` */

insert  into `pantalla`(`id`,`numero`,`descripcion`,`creacion_at`) values (1,0,'Pantalla de espera Principal1','0000-00-00'),(2,0,'Pantalla de espera Secundario','0000-00-00'),(21,0,'Pantalla3','2010-07-05'),(22,0,'p4','0000-00-00');

/*Table structure for table `pantallavideos` */

DROP TABLE IF EXISTS `pantallavideos`;

CREATE TABLE `pantallavideos` (
  `id` int(11) NOT NULL auto_increment,
  `pantalla_id` int(11) default NULL,
  `video_id` int(11) default NULL,
  `activo` tinyint(4) default NULL,
  `orden` tinyint(4) default NULL,
  `reproducir` tinyint(4) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;

/*Data for the table `pantallavideos` */

insert  into `pantallavideos`(`id`,`pantalla_id`,`video_id`,`activo`,`orden`,`reproducir`) values (64,1,4,1,1,1),(65,1,5,1,2,0),(66,1,3,1,3,0),(67,1,1,1,4,0),(68,1,2,1,5,0),(74,21,2,1,1,0),(75,21,3,1,2,1),(76,2,4,1,1,0),(77,2,5,1,2,0),(78,2,3,1,3,0),(79,2,1,1,4,0),(80,2,2,1,5,1),(83,22,3,1,1,0),(84,22,1,1,2,1);

/*Table structure for table `permiso` */

DROP TABLE IF EXISTS `permiso`;

CREATE TABLE `permiso` (
  `menu_id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `permiso` tinyint(1) default '1',
  `creacion_at` varchar(45) default NULL,
  PRIMARY KEY  (`menu_id`),
  KEY `fk_permiso_menu1` (`menu_id`),
  KEY `fk_permiso_grupo1` (`grupo_id`),
  CONSTRAINT `fk_permiso_grupo1` FOREIGN KEY (`grupo_id`) REFERENCES `grupo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_permiso_menu1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `permiso` */

insert  into `permiso`(`menu_id`,`grupo_id`,`hora_inicio`,`hora_fin`,`permiso`,`creacion_at`) values (1,5,'07:00:00','12:00:00',1,NULL),(2,2,'07:00:00','12:00:00',1,NULL),(3,2,'07:00:00','12:00:00',1,NULL),(4,2,'07:00:00','12:00:00',1,NULL),(5,2,'07:00:00','12:00:00',1,NULL),(6,3,'07:00:00','12:00:00',1,NULL),(7,2,'07:00:00','12:00:00',1,NULL),(15,3,'07:00:00','12:00:00',1,NULL),(16,3,'07:00:00','12:00:00',1,NULL),(17,2,'07:00:00','12:00:00',1,NULL),(18,2,'07:00:00','12:00:00',1,NULL),(19,4,'07:00:00','12:00:00',1,NULL),(21,2,'07:00:00','12:00:00',1,NULL),(22,2,'07:00:00','12:00:00',1,NULL),(23,2,'07:00:00','12:00:00',1,NULL);

/*Table structure for table `serie` */

DROP TABLE IF EXISTS `serie`;

CREATE TABLE `serie` (
  `id` int(11) NOT NULL,
  `display_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_serie_display1` (`display_id`),
  CONSTRAINT `fk_serie_display1` FOREIGN KEY (`display_id`) REFERENCES `display` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `serie` */

insert  into `serie`(`id`,`display_id`) values (1,1);

/*Table structure for table `serievideo` */

DROP TABLE IF EXISTS `serievideo`;

CREATE TABLE `serievideo` (
  `serie_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `prioridad` int(11) default NULL,
  PRIMARY KEY  (`serie_id`,`video_id`),
  KEY `fk_serie_has_video_serie1` (`serie_id`),
  KEY `fk_serie_has_video_video1` (`video_id`),
  CONSTRAINT `fk_serie_has_video_serie1` FOREIGN KEY (`serie_id`) REFERENCES `serie` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_serie_has_video_video1` FOREIGN KEY (`video_id`) REFERENCES `video` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `serievideo` */

/*Table structure for table `servicio` */

DROP TABLE IF EXISTS `servicio`;

CREATE TABLE `servicio` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) default NULL,
  `letra` varchar(1) default NULL,
  `descripcion` varchar(150) default NULL,
  `estado` tinyint(1) default NULL,
  `inicio` int(11) default NULL,
  `fin` int(11) default NULL,
  `actual` int(11) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `servicio` */

insert  into `servicio`(`id`,`nombre`,`letra`,`descripcion`,`estado`,`inicio`,`fin`,`actual`,`creacion_at`) values (1,'Servicio1','A','caja',1,0,100,19,'0000-00-00'),(2,'Servicio2','B','servicio1',1,0,100,8,'0000-00-00'),(3,'Servicio3','C','',1,0,0,0,'0000-00-00'),(4,'Servicio4','D','',1,0,0,0,'2010-06-04'),(5,'Servicio5','E','',1,0,0,0,'2010-06-04'),(6,'Servicio6','F','Reclamos',1,0,0,0,'0000-00-00'),(7,'Servicio7','G','servico 7',1,0,100,0,'0000-00-00'),(8,'Servicio8','H','fgfdsf',1,4,4,0,'0000-00-00');

/*Table structure for table `serviciocaja` */

DROP TABLE IF EXISTS `serviciocaja`;

CREATE TABLE `serviciocaja` (
  `servicio_id` int(11) NOT NULL,
  `caja_id` int(11) NOT NULL,
  PRIMARY KEY  (`servicio_id`,`caja_id`),
  KEY `fk_servicio_has_caja_servicio1` (`servicio_id`),
  KEY `fk_servicio_has_caja_caja1` (`caja_id`),
  CONSTRAINT `fk_servicio_has_caja_caja1` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_servicio_has_caja_servicio1` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `serviciocaja` */

insert  into `serviciocaja`(`servicio_id`,`caja_id`) values (1,29),(1,30),(1,31),(2,29),(2,30),(2,31),(3,30),(3,31),(4,30),(4,31),(5,30),(5,31),(6,30),(6,31),(7,30),(7,31),(8,30),(8,31);

/*Table structure for table `sistema` */

DROP TABLE IF EXISTS `sistema`;

CREATE TABLE `sistema` (
  `id` int(11) NOT NULL auto_increment,
  `nombrecomercial` varchar(255) NOT NULL,
  `razonsocial` varchar(255) NOT NULL,
  `logentradasalida` tinyint(1) default NULL,
  `logmodificacion` tinyint(1) default NULL,
  `logvisualizacion` tinyint(1) default NULL,
  `intentoslogin` tinyint(1) default '3',
  `tiemposesion` float default '30',
  `validezclave` int(11) default '1',
  `contenidoclave` varchar(50) default NULL,
  `modclaveacceso` tinyint(1) default NULL,
  `emailadmin` varchar(255) default NULL,
  `claveadmin` varchar(255) default NULL,
  `logosuperior` varchar(50) default NULL,
  `logoreporte` varchar(50) default NULL,
  `logoprincipal` varchar(50) default NULL,
  `idioma` varchar(5) default NULL,
  `simbolomoneda` varchar(5) NOT NULL,
  `formatoreporte` varchar(5) NOT NULL,
  `notificarbloqueo` tinyint(1) default NULL,
  `plantilla` varchar(50) default NULL,
  `numerodecimal` int(11) default '2',
  `tamanocontrasena` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `sistema` */

insert  into `sistema`(`id`,`nombrecomercial`,`razonsocial`,`logentradasalida`,`logmodificacion`,`logvisualizacion`,`intentoslogin`,`tiemposesion`,`validezclave`,`contenidoclave`,`modclaveacceso`,`emailadmin`,`claveadmin`,`logosuperior`,`logoreporte`,`logoprincipal`,`idioma`,`simbolomoneda`,`formatoreporte`,`notificarbloqueo`,`plantilla`,`numerodecimal`,`tamanocontrasena`) values (1,'Luis Caiza','ServiTec-Info',0,0,0,0,NULL,0,NULL,0,NULL,'bGl0bw==',NULL,NULL,NULL,NULL,'$','A4',0,NULL,0,0);

/*Table structure for table `suceso` */

DROP TABLE IF EXISTS `suceso`;

CREATE TABLE `suceso` (
  `id` int(11) NOT NULL auto_increment,
  `evento_id` int(11) NOT NULL,
  `auditoria_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `detalle` varchar(250) default NULL,
  `idusuario` int(11) default NULL,
  `fecha_inicio` date default NULL,
  `hora_inico` time default NULL,
  `fecha_fin` date default NULL,
  `hora_fin` time default NULL,
  `duracion_dias` date default NULL,
  `duracion_horas` time default NULL,
  `ruta_accion` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_suceso_evento1` (`evento_id`),
  KEY `fk_suceso_auditoria1` (`auditoria_id`,`usuario_id`),
  CONSTRAINT `fk_suceso_auditoria1` FOREIGN KEY (`auditoria_id`, `usuario_id`) REFERENCES `auditoria` (`id`, `usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_suceso_evento1` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `suceso` */

/*Table structure for table `tiposujeto` */

DROP TABLE IF EXISTS `tiposujeto`;

CREATE TABLE `tiposujeto` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(45) NOT NULL,
  `padreid` int(11) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `tiposujeto` */

/*Table structure for table `titulo` */

DROP TABLE IF EXISTS `titulo`;

CREATE TABLE `titulo` (
  `id` int(11) NOT NULL auto_increment,
  `concepto` varchar(45) NOT NULL,
  `siglas` varchar(10) NOT NULL,
  `estado` tinyint(1) default '1',
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `titulo` */

/*Table structure for table `turno` */

DROP TABLE IF EXISTS `turno`;

CREATE TABLE `turno` (
  `id` int(11) NOT NULL auto_increment,
  `numero` bigint(20) default NULL,
  `fechaEmision` date default NULL,
  `horaEmision` time default NULL,
  `terceraEdad` tinyint(1) default NULL,
  `prioridad` tinyint(1) default NULL,
  `estado` tinyint(1) default '0',
  `servicio_id` int(11) NOT NULL,
  `por_atender` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_turno_servicio1` (`servicio_id`),
  CONSTRAINT `fk_turno_servicio1` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `turno` */

/*Table structure for table `turnos` */

DROP TABLE IF EXISTS `turnos`;

CREATE TABLE `turnos` (
  `id` int(11) NOT NULL auto_increment,
  `servicio_id` int(11) default NULL,
  `numero` int(11) default NULL,
  `fecha_emision` date default NULL,
  `hora_emision` time default NULL,
  `estado` tinyint(4) default NULL,
  `caja` tinyint(11) default NULL,
  `por_atender` tinyint(4) default NULL,
  `atendido` tinyint(4) default NULL,
  `fecha_atencion` date default NULL,
  `hora_atencion` time default NULL,
  `rechazado` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_turnos` (`servicio_id`),
  CONSTRAINT `FK_turnos` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

/*Data for the table `turnos` */

insert  into `turnos`(`id`,`servicio_id`,`numero`,`fecha_emision`,`hora_emision`,`estado`,`caja`,`por_atender`,`atendido`,`fecha_atencion`,`hora_atencion`,`rechazado`) values (8,1,1,'2010-07-07','18:38:16',0,2,1,1,'2010-07-07','18:41:25',0),(9,3,1,'2010-07-07','18:38:17',0,NULL,0,0,'0000-00-00','00:00:00',0),(10,5,1,'2010-07-07','18:38:17',0,NULL,0,0,'0000-00-00','00:00:00',0),(11,2,1,'2010-07-07','18:38:18',0,2,1,1,'2010-07-07','18:41:25',0),(12,4,1,'2010-07-07','18:38:18',0,NULL,0,0,'0000-00-00','00:00:00',0),(13,1,2,'2010-07-07','18:38:20',0,1,1,0,'0000-00-00','00:00:00',0),(14,3,2,'2010-07-07','18:38:21',0,NULL,0,0,'0000-00-00','00:00:00',0),(15,2,2,'2010-07-07','18:38:21',0,2,1,0,'0000-00-00','00:00:00',0),(16,4,2,'2010-07-07','18:38:21',0,NULL,0,0,'0000-00-00','00:00:00',0),(17,3,3,'2010-07-07','18:38:22',0,NULL,0,0,'0000-00-00','00:00:00',0),(18,1,3,'2010-07-07','18:38:22',0,NULL,0,0,'0000-00-00','00:00:00',0),(19,2,3,'2010-07-07','18:38:23',0,NULL,0,0,'0000-00-00','00:00:00',0);

/*Table structure for table `turnoseteo` */

DROP TABLE IF EXISTS `turnoseteo`;

CREATE TABLE `turnoseteo` (
  `id` int(11) NOT NULL auto_increment,
  `fraseinicial` varchar(45) default NULL,
  `chkfraseinicial` tinyint(4) default NULL,
  `empresa` varchar(45) default NULL,
  `chkempresa` tinyint(4) default NULL,
  `logo` varchar(45) default NULL,
  `chklogo` tinyint(4) default NULL,
  `chkinicial` tinyint(4) default NULL,
  `chkservicio` tinyint(4) default NULL,
  `chkfecha` tinyint(4) default NULL,
  `chktiempoespera` tinyint(4) default NULL,
  `chkturnoespera` tinyint(4) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `turnoseteo` */

insert  into `turnoseteo`(`id`,`fraseinicial`,`chkfraseinicial`,`empresa`,`chkempresa`,`logo`,`chklogo`,`chkinicial`,`chkservicio`,`chkfecha`,`chktiempoespera`,`chkturnoespera`) values (1,'bienvenido',1,'COOPERATIVA',1,NULL,1,1,1,1,1,1);

/*Table structure for table `ubicacion` */

DROP TABLE IF EXISTS `ubicacion`;

CREATE TABLE `ubicacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `padreid` int(11) default NULL,
  `descripcion` varchar(45) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `ubicacion` */

/*Table structure for table `usercaja` */

DROP TABLE IF EXISTS `usercaja`;

CREATE TABLE `usercaja` (
  `id` int(11) NOT NULL auto_increment,
  `usuario_id` int(11) default NULL,
  `caja_id` int(11) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_usercaja` (`usuario_id`),
  KEY `FK_usercaja1` (`caja_id`),
  CONSTRAINT `FK_usercaja` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_usercaja1` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/*Data for the table `usercaja` */

insert  into `usercaja`(`id`,`usuario_id`,`caja_id`,`creacion_at`) values (9,7,29,'2010-07-06'),(10,8,30,'2010-07-06'),(11,32,31,'2010-07-06');

/*Table structure for table `userdispensador` */

DROP TABLE IF EXISTS `userdispensador`;

CREATE TABLE `userdispensador` (
  `id` int(11) NOT NULL auto_increment,
  `usuario_id` int(11) default NULL,
  `dispensador_id` int(11) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_userdispensador` (`usuario_id`),
  KEY `FK_userdispensador2` (`dispensador_id`),
  CONSTRAINT `FK_userdispensador` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_userdispensador2` FOREIGN KEY (`dispensador_id`) REFERENCES `dispensador` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Data for the table `userdispensador` */

insert  into `userdispensador`(`id`,`usuario_id`,`dispensador_id`,`creacion_at`) values (1,5,1,'2010-06-22'),(5,6,2,NULL),(11,25,16,'2010-07-05'),(12,28,17,'2010-07-05');

/*Table structure for table `userpantalla` */

DROP TABLE IF EXISTS `userpantalla`;

CREATE TABLE `userpantalla` (
  `id` int(11) NOT NULL auto_increment,
  `usuario_id` int(11) default NULL,
  `pantalla_id` int(11) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_userpantalla` (`usuario_id`),
  KEY `FK_userpantalla1` (`pantalla_id`),
  CONSTRAINT `FK_userpantalla` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_userpantalla1` FOREIGN KEY (`pantalla_id`) REFERENCES `pantalla` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `userpantalla` */

insert  into `userpantalla`(`id`,`usuario_id`,`pantalla_id`,`creacion_at`) values (1,9,1,'2010-06-28'),(2,10,2,'2010-06-29'),(6,30,21,'2010-07-05'),(7,31,22,'2010-07-05');

/*Table structure for table `uservideo` */

DROP TABLE IF EXISTS `uservideo`;

CREATE TABLE `uservideo` (
  `id` int(11) NOT NULL auto_increment,
  `usuario_id` int(11) default NULL,
  `video_id` int(11) default NULL,
  `activo` tinyint(4) default NULL,
  `orden` tinyint(4) default NULL,
  `reproducir` tinyint(4) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `uservideo` */

/*Table structure for table `usuario` */

DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL auto_increment,
  `nombres` varchar(100) NOT NULL,
  `ci` varchar(15) default NULL,
  `telefono` varchar(15) default NULL,
  `movil` varchar(15) default NULL,
  `estado` varchar(20) default 'Activo',
  `username` varchar(16) NOT NULL,
  `password` varchar(255) default NULL,
  `actclave` date default NULL,
  `email` varchar(100) default NULL,
  `descripcion` mediumtext,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `usuario` */

insert  into `usuario`(`id`,`nombres`,`ci`,`telefono`,`movil`,`estado`,`username`,`password`,`actclave`,`email`,`descripcion`,`creacion_at`) values (1,'Nelson LÃ³pez','1718847799','023042043','083421906','Activo','nel',NULL,'0000-00-00','nelson.lopez@peopleweb.com.ec',NULL,'0000-00-00'),(2,'prueba administrador','1234567890','024586654',NULL,'Activo','123','',NULL,NULL,NULL,'2010-04-15'),(3,'prueba operador','123122','123122',NULL,'Activo','prueba',NULL,NULL,NULL,NULL,'2010-04-23'),(4,'administrador','1234',NULL,NULL,'Activo','admin1',NULL,NULL,NULL,NULL,'2010-04-27'),(5,'dispensador1','123456',NULL,NULL,'Activo','dispensador1',NULL,NULL,NULL,NULL,'2010-06-22'),(6,'dispensador2','123',NULL,NULL,'Activo','dispensador2',NULL,'0000-00-00',NULL,NULL,'0000-00-00'),(7,'Nelson - Cajero1','456',NULL,NULL,'Activo','caja1',NULL,'0000-00-00',NULL,NULL,'0000-00-00'),(8,'Alex - Cajero2','9878',NULL,NULL,'Activo','caja2',NULL,NULL,NULL,NULL,NULL),(9,'Pantalla1','9658',NULL,NULL,'Activo','pantalla1',NULL,'0000-00-00',NULL,'descripciÃ³n','0000-00-00'),(10,'pantalla2',NULL,NULL,NULL,'Activo','pantalla2',NULL,'0000-00-00',NULL,NULL,'0000-00-00'),(25,'dispensador3','','','','Activo','dispensador3','','0000-00-00','','Dispensador 3','2010-07-05'),(28,'dispensador5','','','','Activo','dispensador5','','0000-00-00','','df','2010-07-05'),(30,'pantalla3','','','','Activo','pantalla3','','0000-00-00','','p3','2010-07-05'),(31,'pantalla4','','','','Activo','pantala4','','0000-00-00','','p4','2010-07-05'),(32,'cajero alex lÃ³pez',NULL,NULL,NULL,'Activo','cajeroal',NULL,'0000-00-00',NULL,'cajero principal','0000-00-00');

/*Table structure for table `video` */

DROP TABLE IF EXISTS `video`;

CREATE TABLE `video` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(100) default NULL,
  `ubicacion` varchar(100) default NULL,
  `duracion` int(11) default NULL,
  `activo` tinyint(4) default NULL,
  `creacion_at` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `video` */

insert  into `video`(`id`,`nombre`,`ubicacion`,`duracion`,`activo`,`creacion_at`) values (1,'video.avi',NULL,6,1,'2010-06-30'),(2,'vives.avi',NULL,8,1,'2010-06-30'),(3,'cabina.avi',NULL,10,1,'2010-06-30'),(4,'aventura-hermanita.avi',NULL,12,1,'2010-06-30'),(5,'avion.avi',NULL,14,1,'2010-06-30');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
