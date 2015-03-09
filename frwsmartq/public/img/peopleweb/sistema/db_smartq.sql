/*
SQLyog Community Edition- MySQL GUI v8.05 
MySQL - 5.1.33-community-log : Database - db_smartq
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_smartq` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `db_smartq`;

/*Table structure for table `auditoria` */

DROP TABLE IF EXISTS `auditoria`;

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `detalle` varchar(255) NOT NULL,
  `controlador` varchar(50) DEFAULT NULL,
  `accion` varchar(50) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `hora_ingreso` time DEFAULT NULL,
  `fecha_salida` date DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `duracion` time DEFAULT NULL,
  `creacion_at` date DEFAULT NULL COMMENT '	',
  PRIMARY KEY (`id`,`usuario_id`),
  KEY `fk_auditoria_usuario1` (`usuario_id`),
  CONSTRAINT `fk_auditoria_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `auditoria` */

/*Table structure for table `banner` */

DROP TABLE IF EXISTS `banner`;

CREATE TABLE `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ubicacion` varchar(100) DEFAULT NULL,
  `serie` int(11) DEFAULT NULL,
  `posicion` int(11) DEFAULT NULL,
  `display_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_banner_display1` (`display_id`),
  CONSTRAINT `fk_banner_display1` FOREIGN KEY (`display_id`) REFERENCES `display` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `banner` */

insert  into `banner`(`id`,`ubicacion`,`serie`,`posicion`,`display_id`) values (1,'banner1.gif',1,1,1),(2,'banner1.gif',1,1,1),(3,'banner.gif',0,1,1),(4,'banner1.gif',0,1,1),(5,'banner.gif',0,1,1);

/*Table structure for table `caja` */

DROP TABLE IF EXISTS `caja`;

CREATE TABLE `caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_caja` varchar(11) DEFAULT NULL,
  `descripcion` varchar(145) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `usuario` varchar(45) DEFAULT NULL,
  `tipo_calificacion_operador` varchar(10) DEFAULT 'D',
  `horario_id` int(11) DEFAULT NULL,
  `transferir_uno` tinyint(1) DEFAULT '1',
  `transferir_todos` tinyint(1) DEFAULT '1',
  `ubicacion_id` int(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `tiempo` int(5) DEFAULT '0',
  `timbre` int(1) DEFAULT '0',
  `calificar` int(1) DEFAULT '0',
  `usuario_actual` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_caja_ubicacion` (`ubicacion_id`),
  KEY `FK_caja_horario` (`horario_id`),
  CONSTRAINT `FK_caja_horario` FOREIGN KEY (`horario_id`) REFERENCES `horario` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_caja_ubicacion` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicacion` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `caja` */

insert  into `caja`(`id`,`numero_caja`,`descripcion`,`estado`,`usuario`,`tipo_calificacion_operador`,`horario_id`,`transferir_uno`,`transferir_todos`,`ubicacion_id`,`ip`,`tiempo`,`timbre`,`calificar`,`usuario_actual`) values (1,'1','AtenciÃ³n servicio al cliente',1,'','D',9,1,1,3,'192.168.25.29',5,NULL,NULL,103);

/*Table structure for table `caja_pausas` */

DROP TABLE IF EXISTS `caja_pausas`;

CREATE TABLE `caja_pausas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `caja_id` int(11) DEFAULT NULL,
  `pausas_id` int(11) NOT NULL DEFAULT '5',
  `estado` varchar(20) NOT NULL DEFAULT '1',
  `fecha_inicio` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `duracion` time DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_cajapausas_caja` (`caja_id`),
  KEY `FK_cajapausas_pausas` (`pausas_id`),
  KEY `FK_caja_pausas_usuario` (`usuario_id`),
  CONSTRAINT `FK_cajapausas_caja` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_cajapausas_pausas` FOREIGN KEY (`pausas_id`) REFERENCES `pausas` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_caja_pausas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `caja_pausas` */

/*Table structure for table `calificacion` */

DROP TABLE IF EXISTS `calificacion`;

CREATE TABLE `calificacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_calificacion` varchar(50) DEFAULT NULL,
  `puntos` tinyint(3) DEFAULT NULL,
  `orden` tinyint(2) DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `calificacion` */

insert  into `calificacion`(`id`,`nom_calificacion`,`puntos`,`orden`,`creacion_at`) values (1,'Excelente',4,1,'0000-00-00'),(2,'Muy Bueno',3,2,'2011-01-24'),(3,'Bueno',2,3,'0000-00-00'),(4,'Regular',1,4,'2011-01-24');

/*Table structure for table `categoriafeeds` */

DROP TABLE IF EXISTS `categoriafeeds`;

CREATE TABLE `categoriafeeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(50) NOT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `categoriafeeds` */

insert  into `categoriafeeds`(`id`,`nombre_categoria`,`creacion_at`) values (1,'PolÃ­tica','2011-07-14'),(2,'Deportes','2011-07-14'),(3,'Varios','2011-09-11');

/*Table structure for table `colas` */

DROP TABLE IF EXISTS `colas`;

CREATE TABLE `colas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caja_id` int(11) DEFAULT NULL,
  `por_atender` tinyint(1) DEFAULT NULL,
  `atendido` tinyint(1) DEFAULT NULL,
  `fecha_inicio_atencion` date DEFAULT NULL,
  `hora_inicio_atencion` time DEFAULT NULL,
  `fecha_fin_atencion` date DEFAULT NULL,
  `hora_fin_atencion` time DEFAULT NULL,
  `duracion` time DEFAULT NULL,
  `calificacion` varchar(20) DEFAULT 'NO CALIFICADO',
  `creacion_at` date DEFAULT NULL,
  `id_username` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `colas` */

/*Table structure for table `colaspreguntas` */

DROP TABLE IF EXISTS `colaspreguntas`;

CREATE TABLE `colaspreguntas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `preguntas_id` int(11) DEFAULT NULL,
  `caja_id` int(11) DEFAULT NULL,
  `colas_id` int(11) DEFAULT NULL,
  `puntuacion` tinyint(3) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `colaspreguntas` */

/*Table structure for table `configuracionsistema` */

DROP TABLE IF EXISTS `configuracionsistema`;

CREATE TABLE `configuracionsistema` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `activar_calificador` tinyint(1) DEFAULT NULL,
  `activar_difusion` tinyint(1) DEFAULT NULL,
  `publicar_noticias` tinyint(1) DEFAULT NULL,
  `velocidad_publicacion` varchar(4) DEFAULT NULL,
  `pc_difusion` varchar(50) DEFAULT NULL,
  `puerto` varchar(10) DEFAULT NULL,
  `ver_tiempo_maximo` tinyint(1) DEFAULT '1',
  `ver_tiempo_atencion` tinyint(1) DEFAULT '1',
  `ubicacion_impresora` varchar(100) DEFAULT '192.168.10.110',
  `nombre_impresora` varchar(100) DEFAULT 'EPSON TM-T81 Receipt',
  `creacion_at` date DEFAULT NULL,
  `tono` tinyint(1) DEFAULT '1' COMMENT '1 timbre, 2 voz',
  `tiempo_tono` int(3) DEFAULT '1',
  `ventana` tinyint(1) DEFAULT '1',
  `logo` tinyint(1) DEFAULT '1',
  `logo_sinticket` tinyint(4) DEFAULT '1',
  `logo_conticket` tinyint(1) DEFAULT '1',
  `logo_calificador` tinyint(1) DEFAULT '1',
  `logo_ticket` tinyint(1) DEFAULT '1',
  `pantalla_ticket` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `configuracionsistema` */

insert  into `configuracionsistema`(`id`,`activar_calificador`,`activar_difusion`,`publicar_noticias`,`velocidad_publicacion`,`pc_difusion`,`puerto`,`ver_tiempo_maximo`,`ver_tiempo_atencion`,`ubicacion_impresora`,`nombre_impresora`,`creacion_at`,`tono`,`tiempo_tono`,`ventana`,`logo`,`logo_sinticket`,`logo_conticket`,`logo_calificador`,`logo_ticket`,`pantalla_ticket`) values (1,1,0,1,'5',NULL,NULL,1,1,NULL,'EPSON BA-T500 Full cut','0000-00-00',0,0,0,1,1,1,1,1,0);

/*Table structure for table `controlacceso` */

DROP TABLE IF EXISTS `controlacceso`;

CREATE TABLE `controlacceso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `sesion_inicio` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `sesion_fin` date DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `duracion` time DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mensajes_usuario1` (`usuario_id`),
  CONSTRAINT `fk_mensajes_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `controlacceso` */

/*Table structure for table `datospersonales` */

DROP TABLE IF EXISTS `datospersonales`;

CREATE TABLE `datospersonales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(45) NOT NULL,
  `conyugeId` int(11) DEFAULT NULL,
  `tipoSujeto_id` int(11) NOT NULL,
  `apellido_paterno` varchar(45) DEFAULT NULL,
  `apellido_materno` varchar(45) NOT NULL,
  `primer_nombre` varchar(45) NOT NULL,
  `segundo_nombre` varchar(45) DEFAULT NULL,
  `titulo_id` int(11) NOT NULL,
  `lugar_nacimiento` varchar(45) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` int(11) NOT NULL,
  `genero` varchar(25) NOT NULL,
  `tipo_documento` int(11) NOT NULL DEFAULT '1',
  `ci` varchar(15) DEFAULT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `pasaporte` varchar(45) DEFAULT NULL,
  `ubicacion_id` int(11) NOT NULL,
  `direccion_domicilio` varchar(100) NOT NULL,
  `interseccion` varchar(45) DEFAULT NULL,
  `nomenclatura` varchar(15) DEFAULT NULL,
  `pos_x` decimal(9,6) DEFAULT NULL,
  `pos_y` decimal(9,6) DEFAULT NULL,
  `telefono` varchar(15) NOT NULL,
  `movil` varchar(15) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`,`codigo`),
  KEY `fk_datos_personales_parroquia1` (`ubicacion_id`),
  KEY `fk_datosPersonales_tipoSujeto1` (`tipoSujeto_id`),
  KEY `fk_datosPersonales_titulo1` (`titulo_id`),
  CONSTRAINT `fk_datosPersonales_tipoSujeto1` FOREIGN KEY (`tipoSujeto_id`) REFERENCES `tiposujeto` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datosPersonales_titulo1` FOREIGN KEY (`titulo_id`) REFERENCES `titulo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_datos_personales_parroquia1` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicacion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `datospersonales` */

/*Table structure for table `detallehorario` */

DROP TABLE IF EXISTS `detallehorario`;

CREATE TABLE `detallehorario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `horario_id` int(11) DEFAULT NULL,
  `dia` varchar(10) NOT NULL,
  `hora_inicial1` time NOT NULL,
  `hora_final1` time NOT NULL,
  `hora_inicial2` time DEFAULT NULL,
  `hora_final2` time DEFAULT NULL,
  `hora_inicial3` time DEFAULT NULL,
  `hora_final3` time DEFAULT NULL,
  `hora_inicial4` time DEFAULT NULL,
  `hora_final4` time DEFAULT NULL,
  `horas_laborables` time DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_detallehorario` (`horario_id`),
  CONSTRAINT `FK_detallehorario` FOREIGN KEY (`horario_id`) REFERENCES `horario` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

/*Data for the table `detallehorario` */

insert  into `detallehorario`(`id`,`horario_id`,`dia`,`hora_inicial1`,`hora_final1`,`hora_inicial2`,`hora_final2`,`hora_inicial3`,`hora_final3`,`hora_inicial4`,`hora_final4`,`horas_laborables`,`creacion_at`) values (16,9,'Lunes','08:00:00','13:00:00','13:30:00','16:00:00','00:00:00','00:00:00','00:00:00','00:00:00','07:30:00','2011-10-18'),(17,9,'Martes','08:00:00','13:00:00','13:30:00','16:00:00','00:00:00','00:00:00','00:00:00','00:00:00','07:30:00','2011-10-18'),(18,9,'MiÃ©rcoles','08:00:00','13:00:00','13:30:00','16:00:00','00:00:00','00:00:00','00:00:00','00:00:00','07:30:00','2011-10-18'),(19,9,'Jueves','08:00:00','13:00:00','13:30:00','16:00:00','00:00:00','00:00:00','00:00:00','00:00:00','07:30:00','2011-10-18'),(20,9,'Viernes','08:00:00','13:00:00','13:30:00','16:00:00','00:00:00','00:00:00','00:00:00','00:00:00','07:30:00','2011-10-18');

/*Table structure for table `dias` */

DROP TABLE IF EXISTS `dias`;

CREATE TABLE `dias` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `dia` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Data for the table `dias` */

insert  into `dias`(`id`,`dia`) values (1,'Monday'),(2,'Tuesday'),(3,'Wednesday'),(4,'Thursday'),(5,'Friday'),(6,'Saturday');

/*Table structure for table `dispensador` */

DROP TABLE IF EXISTS `dispensador`;

CREATE TABLE `dispensador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) DEFAULT NULL,
  `tipo_dispensador` varchar(20) DEFAULT 'touch',
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `dispensador` */

insert  into `dispensador`(`id`,`descripcion`,`tipo_dispensador`,`usuario_id`) values (1,'Dispensador Primario','touch',102);

/*Table structure for table `dispensadorservicio` */

DROP TABLE IF EXISTS `dispensadorservicio`;

CREATE TABLE `dispensadorservicio` (
  `dispensador_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  PRIMARY KEY (`dispensador_id`,`servicio_id`),
  KEY `fk_dispensador_has_servicio_dispensador1` (`dispensador_id`),
  KEY `fk_dispensador_has_servicio_servicio1` (`servicio_id`),
  CONSTRAINT `fk_dispensador_has_servicio_dispensador1` FOREIGN KEY (`dispensador_id`) REFERENCES `dispensador` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_dispensador_has_servicio_servicio1` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `dispensadorservicio` */

insert  into `dispensadorservicio`(`dispensador_id`,`servicio_id`) values (1,1);

/*Table structure for table `display` */

DROP TABLE IF EXISTS `display`;

CREATE TABLE `display` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formato` tinyint(4) DEFAULT NULL,
  `turnos` varchar(45) DEFAULT NULL,
  `chkbanner` tinyint(4) DEFAULT NULL,
  `numvideos` int(11) DEFAULT NULL,
  `horainicio` time DEFAULT NULL,
  `horafin` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `display` */

insert  into `display`(`id`,`formato`,`turnos`,`chkbanner`,`numvideos`,`horainicio`,`horafin`) values (1,1,'4',0,1,'07:00:00','19:00:00');

/*Table structure for table `displaycajas` */

DROP TABLE IF EXISTS `displaycajas`;

CREATE TABLE `displaycajas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cajanumero` varchar(15) DEFAULT NULL,
  `ubicacion` tinyint(4) DEFAULT NULL,
  `llamo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `displaycajas` */

/*Table structure for table `empresa` */

DROP TABLE IF EXISTS `empresa`;

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombrecomercial` varchar(45) DEFAULT NULL,
  `alias_empresa` varchar(50) DEFAULT NULL,
  `razonsocial` varchar(180) DEFAULT NULL,
  `matriz` tinyint(1) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `modulo_operadores` tinyint(1) DEFAULT NULL,
  `modulo_cajas` tinyint(1) DEFAULT NULL,
  `calif_4botones_teclado` tinyint(1) DEFAULT NULL,
  `calif_4botones_pantalla` tinyint(1) DEFAULT NULL,
  `calif_matriz_pantalla` tinyint(1) DEFAULT NULL,
  `dispensador_simple` tinyint(1) DEFAULT '1',
  `dispensador_touch` tinyint(1) DEFAULT '1',
  `dispensador_botonera` tinyint(1) DEFAULT '1',
  `dispensador_touch_pequenia` tinyint(1) DEFAULT '1',
  `carpeta` varchar(30) DEFAULT NULL,
  `seleccion_operador` tinyint(1) DEFAULT '0',
  `servidor_node` varchar(20) DEFAULT NULL,
  `configura_sucursales` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `empresa` */

insert  into `empresa`(`id`,`nombrecomercial`,`alias_empresa`,`razonsocial`,`matriz`,`direccion`,`modulo_operadores`,`modulo_cajas`,`calif_4botones_teclado`,`calif_4botones_pantalla`,`calif_matriz_pantalla`,`dispensador_simple`,`dispensador_touch`,`dispensador_botonera`,`dispensador_touch_pequenia`,`carpeta`,`seleccion_operador`,`servidor_node`,`configura_sucursales`) values (1,'PEOPLEWEB',NULL,'RAZON',1,NULL,1,0,0,1,1,0,1,0,0,'peopleweb',0,NULL,0);

/*Table structure for table `estado` */

DROP TABLE IF EXISTS `estado`;

CREATE TABLE `estado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siglas` varchar(10) DEFAULT NULL,
  `descripcion` varchar(45) NOT NULL,
  `equivalencia` varchar(20) DEFAULT NULL,
  `creacion_at` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `estado` */

/*Table structure for table `evento` */

DROP TABLE IF EXISTS `evento`;

CREATE TABLE `evento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(45) NOT NULL,
  `descripcion` text,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `evento` */

/*Table structure for table `feed` */

DROP TABLE IF EXISTS `feed`;

CREATE TABLE `feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoriafeeds_id` int(11) NOT NULL,
  `url_feed` varchar(200) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_feed_categoriafeeds` (`categoriafeeds_id`),
  CONSTRAINT `FK_feed_categoriafeeds` FOREIGN KEY (`categoriafeeds_id`) REFERENCES `categoriafeeds` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `feed` */

insert  into `feed`(`id`,`categoriafeeds_id`,`url_feed`,`activo`,`creacion_at`) values (1,1,'http://www.eluniverso.com/rss/internacionales.xml',1,'0000-00-00'),(2,2,'http://eluniverso.feedsportal.com/c/34629/f/633279/index.rss',1,'0000-00-00'),(3,1,'http://www.eluniverso.com/rss/elpais.xml',1,'0000-00-00');

/*Table structure for table `grupo` */

DROP TABLE IF EXISTS `grupo`;

CREATE TABLE `grupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_largo` varchar(100) NOT NULL,
  `nombre_corto` varchar(30) DEFAULT NULL,
  `unico` smallint(1) DEFAULT '1' COMMENT '1=un usuario solo puede acceder a un solo rol',
  `descripcion` text,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `grupo` */

insert  into `grupo`(`id`,`nombre_largo`,`nombre_corto`,`unico`,`descripcion`,`creacion_at`) values (1,'Configuradores del sistema','Sistema',0,NULL,'2010-04-13'),(3,'Administradores','Admin',0,NULL,'2010-04-23'),(4,'Dispensadores','Dispensadores',1,NULL,'2010-06-22'),(5,'Operador con ticket','operadores',0,NULL,'2010-06-22'),(6,'Pantallas','pantallas',1,NULL,'2010-06-23'),(7,'Operador sin ticket','cajeros',0,NULL,NULL);

/*Table structure for table `grupopregunta` */

DROP TABLE IF EXISTS `grupopregunta`;

CREATE TABLE `grupopregunta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_grupo` varchar(100) DEFAULT NULL,
  `cracion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `grupopregunta` */

insert  into `grupopregunta`(`id`,`nom_grupo`,`cracion_at`) values (1,'Grupo Pregunta 1','2014-05-24');

/*Table structure for table `gruposervicio` */

DROP TABLE IF EXISTS `gruposervicio`;

CREATE TABLE `gruposervicio` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `nombre_grupo_servicio` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `gruposervicio` */

insert  into `gruposervicio`(`id`,`nombre_grupo_servicio`) values (1,'BalcÃ³n de Servicios');

/*Table structure for table `grupousuario` */

DROP TABLE IF EXISTS `grupousuario`;

CREATE TABLE `grupousuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_grupo_grupousuario` (`grupo_id`),
  KEY `FK_usuario_grupousuario` (`usuario_id`),
  CONSTRAINT `FK_grupo_grupousuario` FOREIGN KEY (`grupo_id`) REFERENCES `grupo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usuario_grupousuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `grupousuario` */

insert  into `grupousuario`(`id`,`grupo_id`,`usuario_id`,`creacion_at`) values (67,1,8,NULL),(70,3,1,'2014-05-24'),(71,6,101,'2014-05-24'),(72,4,102,'2014-05-24'),(73,5,103,'2014-05-24');

/*Table structure for table `horario` */

DROP TABLE IF EXISTS `horario`;

CREATE TABLE `horario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_horario` varchar(30) NOT NULL,
  `descripcion_horario` varchar(50) DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Data for the table `horario` */

insert  into `horario`(`id`,`nombre_horario`,`descripcion_horario`,`creacion_at`) values (9,'Horario normal','Horario normal de trabajo L-V','0000-00-00');

/*Table structure for table `horariopublicidad` */

DROP TABLE IF EXISTS `horariopublicidad`;

CREATE TABLE `horariopublicidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pantalla_id` int(11) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `tipo` varchar(15) NOT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_horariopublicidad_pantalla` (`pantalla_id`),
  CONSTRAINT `FK_horariopublicidad_pantalla` FOREIGN KEY (`pantalla_id`) REFERENCES `pantalla` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=latin1;

/*Data for the table `horariopublicidad` */

insert  into `horariopublicidad`(`id`,`pantalla_id`,`hora_inicio`,`hora_fin`,`tipo`,`creacion_at`) values (80,1,'07:00:00','18:00:00','P','2014-05-24');

/*Table structure for table `menu` */

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo_id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `ruta` varchar(255) DEFAULT NULL,
  `idreferencia` int(11) NOT NULL DEFAULT '0',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `orden` int(11) NOT NULL DEFAULT '1',
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `posicion` varchar(50) DEFAULT 'left',
  `tipo_ventana` varchar(20) NOT NULL DEFAULT '_self',
  PRIMARY KEY (`id`),
  KEY `fk_menu_modulo1` (`modulo_id`),
  CONSTRAINT `fk_menu_modulo1` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `menu` */

insert  into `menu`(`id`,`modulo_id`,`nombre`,`ruta`,`idreferencia`,`estado`,`orden`,`principal`,`posicion`,`tipo_ventana`) values (1,1,'Monitoreo','/',0,1,1,0,'left','_self'),(2,2,'General','/',0,1,1,1,'left','_self'),(3,2,'Servicios','servicio/index',2,1,5,0,'left','_self'),(4,2,'MÃ³dulo','caja',51,1,2,0,'left','_self'),(5,2,'DiseÃ±o Ticket','turnoseteo',2,1,6,0,'left','_self'),(6,3,'Operacion','/',0,0,1,1,'left','_self'),(7,2,'Dispensadores','dispensador',51,1,3,0,'left','_self'),(15,3,'Turnos de caja','listacaja',1,1,1,0,'left','_self'),(16,3,'Turnos','operador/index/',1,1,2,0,'left','popup'),(17,3,'Reportes','/',52,1,1,0,'left','_self'),(20,1,'Configuracion','/',0,1,1,0,'left','_self'),(21,2,'Datos Usuarios','usuario',51,1,1,0,'left','_self'),(22,2,'Pantallas','pantalla',51,1,4,0,'left','_self'),(23,2,'Videos','video',2,1,7,0,'left','_self'),(26,2,'UbicaciÃ³n','ubicacion',2,1,3,0,'left','_self'),(27,2,'Seteo turnos','inicioturnos',2,0,10,0,'left','popup'),(29,2,'Grupo de Servicios','gruposervicio',2,1,4,0,'left','_self'),(30,3,'Turnos Atendidos Con Ticket','reportes/turnosConTicket',17,1,1,0,'left','_self'),(33,3,'Turnos Atendidos Sin Ticket','reportes/turnosSinTicket',17,1,3,0,'left','_self'),(37,2,'Noticias','noticias',2,1,8,0,'left','_self'),(38,2,'Config. Varios','configuracionsistema',2,1,12,0,'left','_self'),(39,1,'General','/',0,1,1,1,'left','_self'),(41,1,'Estilo pantalla operador','estilopantallaoperador/editar',39,0,2,0,'left','popup'),(42,1,'Empresa','empresa',39,1,1,0,'left','_self'),(43,2,'Calificación','calificacion',2,0,9,0,'left','_self'),(44,2,'Preguntas','preguntas',2,1,10,0,'left','_self'),(47,2,'Horario','horario',2,1,1,0,'left','_self'),(48,2,'Datos Sucursales','sucursal',50,1,1,0,'left','_self'),(49,2,'Pausas','pausas',2,1,2,0,'left','_self'),(50,2,'Sucursales','/',0,1,4,1,'left','_self'),(51,2,'Usuarios','/',0,1,3,1,'left','_self'),(52,3,'Local','/',0,1,1,1,'left','_self'),(53,3,'Sucursales','/',0,1,1,1,'left','_self'),(65,3,'Sesiones','sesiones',52,1,3,0,'left','_self'),(66,3,'Pausas','caja_pausas',52,1,4,0,'left','_self'),(67,2,'CategorÃ­a RSS','categoriafeeds',2,1,10,0,'left','_self'),(68,2,'RSS','feed',2,1,11,0,'left','_self'),(69,1,'Permisos','permiso',39,1,3,0,'left','_self'),(70,3,'Status','status/ver',52,1,4,0,'left','_self'),(71,2,'Publicidad','publicidad',2,1,9,0,'left','_self'),(81,2,'Grupo de Preguntas','grupopregunta',2,1,9,0,'left','_self'),(89,3,'Transferencia de turnos','admturnos/inicio',52,1,1,0,'left','_self'),(90,1,'Menu','menu',39,1,1,0,'left','_self'),(91,2,'Roles y Permisos','/',0,1,2,1,'left','_self'),(92,2,'Gestionar Roles','grupo',91,1,1,0,'left','_self'),(93,2,'Permisos','permiso/rolPermisos',91,1,3,0,'left','_self'),(94,2,'Asingar Roles','grupousuario',91,0,2,0,'left','_self');

/*Table structure for table `menu_reportes` */

DROP TABLE IF EXISTS `menu_reportes`;

CREATE TABLE `menu_reportes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_menu` varchar(60) DEFAULT NULL,
  `nombre_aplicacion` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `posicion` tinyint(2) DEFAULT NULL,
  `tipo` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

/*Data for the table `menu_reportes` */

insert  into `menu_reportes`(`id`,`nombre_menu`,`nombre_aplicacion`,`estado`,`posicion`,`tipo`) values (1,'Turnos por Usuario en M&oacutedulo','turnos_xusuario',1,1,0),(2,'Turnos por Servicio en M&oacutedulo','turnos_xservicio',1,1,0),(3,'Turnos por Servicio y Usuario','usuario_xservicio',1,1,0),(4,'Pausas por Usuario','pausas_xusuario',1,1,0),(5,'Calificaciones por Usuario','calificacion_usuario',1,1,0),(6,'Turnos Transferidos','turnos_transferidos_usuarios',1,1,0),(7,'Atenci&oacuten Con Ticket',NULL,0,1,10),(8,'Atenci&oacuten Sin Ticket',NULL,0,2,10),(9,'Turnos por Usuario en Caja','colas_xusuario',0,1,0),(10,'Reportes Matriz',NULL,0,1,0),(11,'Opciones Reportes','ReportesConTicket',0,1,7),(12,'Opciones Reportes Sin Ticket','ReportesSinTicket',0,1,8),(13,'Pausas por Cajero','pausas_xusuario',0,1,0),(22,'Calificaciones por Cajero','calificacion_promedio_colas',0,1,0),(23,'Horas Pico de Atenci&oacuten Turnos','horas_pico_atencion',1,1,0),(25,'Horas Pico de Atenci&oacuten Colas','horas_pico_atencion_colas',0,1,0),(26,'Detalle por Turno','detalle_turnos',1,1,0),(27,'Turnos de Demora M&aacutexima','maximo_duracion',1,1,0),(28,'Turnos de Demora M&iacutenima','minimo_duracion',1,1,0),(29,'Turnos de Espera M&aacutexima','maximo_espera',1,1,0),(30,'Turnos de Espera M&iacutenima ','minimo_espera',1,1,0);

/*Table structure for table `modulo` */

DROP TABLE IF EXISTS `modulo`;

CREATE TABLE `modulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `ruta` varchar(255) DEFAULT NULL,
  `tipo` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `modulo` */

insert  into `modulo`(`id`,`nombre`,`estado`,`ruta`,`tipo`) values (1,'Sistema',1,NULL,NULL),(2,'ConfiguraciÃ³n',1,NULL,NULL),(3,'AdministraciÃ³n',1,NULL,NULL);

/*Table structure for table `noticias` */

DROP TABLE IF EXISTS `noticias`;

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) DEFAULT NULL,
  `noticia` varchar(500) DEFAULT NULL,
  `publicar` tinyint(1) DEFAULT NULL,
  `fecha_inicio_publicacion` date DEFAULT NULL,
  `fecha_fin_publicacion` date DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/*Data for the table `noticias` */

insert  into `noticias`(`id`,`titulo`,`noticia`,`publicar`,`fecha_inicio_publicacion`,`fecha_fin_publicacion`,`creacion_at`) values (3,'Nuestros Valores','*Solidaridad\r\n*Honestidad\r\n*Confianza\r\n*Lealtad\r\n*Respeto',1,'2014-01-01','2020-12-31','0000-00-00'),(11,'Visita nuestra pÃ¡gina web','www.peopleweb.com.ec',1,'2014-01-01','2020-12-31','0000-00-00');

/*Table structure for table `pantalla` */

DROP TABLE IF EXISTS `pantalla`;

CREATE TABLE `pantalla` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` tinyint(4) DEFAULT NULL,
  `descripcion` char(200) DEFAULT NULL,
  `ubicacion_id` int(11) DEFAULT NULL,
  `timbre` tinyint(1) DEFAULT '0',
  `ip_equipo` varchar(15) DEFAULT NULL,
  `tipo_pantalla` varchar(30) DEFAULT 'Pantalla Operador',
  `con_ticket` tinyint(1) DEFAULT '0',
  `color_turnos` varchar(10) DEFAULT 'fff',
  `color_noticias` varchar(10) DEFAULT 'fff',
  `color_reloj` varchar(10) DEFAULT 'fff',
  `efecto_turno_superior` tinyint(1) DEFAULT '0',
  `creacion_at` date DEFAULT NULL,
  `tono` int(1) DEFAULT '1',
  `tiempo_tono` int(5) DEFAULT '5',
  `ventana` int(1) DEFAULT NULL,
  `formato_voz` varchar(100) DEFAULT 'pase_a_la_caja-#',
  `tipo_voz` varchar(1) DEFAULT 'M',
  `usuario_id` int(11) DEFAULT NULL,
  `ip_senal_tv` varchar(25) DEFAULT ' ',
  `llamado_con_tecla` smallint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_pantalla` (`ubicacion_id`),
  CONSTRAINT `FK_pantalla` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicacion` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `pantalla` */

insert  into `pantalla`(`id`,`numero`,`descripcion`,`ubicacion_id`,`timbre`,`ip_equipo`,`tipo_pantalla`,`con_ticket`,`color_turnos`,`color_noticias`,`color_reloj`,`efecto_turno_superior`,`creacion_at`,`tono`,`tiempo_tono`,`ventana`,`formato_voz`,`tipo_voz`,`usuario_id`,`ip_senal_tv`,`llamado_con_tecla`) values (1,1,'Pantalla1',3,0,NULL,'Pantalla Operador',0,'FFFFFF','000000','000000',0,'0000-00-00',1,5,0,'caja-#','M',101,':',0);

/*Table structure for table `pantallafeed` */

DROP TABLE IF EXISTS `pantallafeed`;

CREATE TABLE `pantallafeed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pantalla_id` int(11) NOT NULL,
  `feed_id` int(11) NOT NULL,
  `categoriafeeds_id` int(11) NOT NULL,
  `publicar_icono` tinyint(1) DEFAULT '1',
  `publicar_titulo` tinyint(1) DEFAULT '1',
  `publicar_fecha` tinyint(1) DEFAULT '1',
  `publicar_hora` tinyint(1) DEFAULT '1',
  `publicar_contenido` tinyint(1) DEFAULT '1',
  `limite_items` tinyint(1) DEFAULT '1',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_pantallafeed_pantalla` (`pantalla_id`),
  KEY `FK_pantallafeed_feed` (`feed_id`),
  KEY `FK_pantallafeed_categoriafeeds` (`categoriafeeds_id`),
  CONSTRAINT `FK_pantallafeed_categoriafeeds` FOREIGN KEY (`categoriafeeds_id`) REFERENCES `categoriafeeds` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_pantallafeed_feed` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_pantallafeed_pantalla` FOREIGN KEY (`pantalla_id`) REFERENCES `pantalla` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `pantallafeed` */

/*Table structure for table `pantallapublicidad` */

DROP TABLE IF EXISTS `pantallapublicidad`;

CREATE TABLE `pantallapublicidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pantalla_id` int(11) NOT NULL,
  `publicidad_id` int(11) NOT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_pantallapublicidad_pantalla` (`pantalla_id`),
  KEY `FK_pantallapublicidad` (`publicidad_id`),
  CONSTRAINT `FK_pantallapublicidad` FOREIGN KEY (`publicidad_id`) REFERENCES `publicidad` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_pantallapublicidad_pantalla` FOREIGN KEY (`pantalla_id`) REFERENCES `pantalla` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `pantallapublicidad` */

insert  into `pantallapublicidad`(`id`,`pantalla_id`,`publicidad_id`,`creacion_at`) values (4,1,12,'2014-05-24');

/*Table structure for table `pantallavideos` */

DROP TABLE IF EXISTS `pantallavideos`;

CREATE TABLE `pantallavideos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pantalla_id` int(11) DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL,
  `activo` tinyint(4) DEFAULT NULL,
  `orden` tinyint(4) DEFAULT NULL,
  `reproducir` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `pantallavideos` */

/*Table structure for table `pausas` */

DROP TABLE IF EXISTS `pausas`;

CREATE TABLE `pausas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_pausa` varchar(25) NOT NULL,
  `maximo_permitido` time DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `pausas` */

insert  into `pausas`(`id`,`nombre_pausa`,`maximo_permitido`,`creacion_at`) values (1,'Almuerzo','01:00:00','0000-00-00');

/*Table structure for table `permiso` */

DROP TABLE IF EXISTS `permiso`;

CREATE TABLE `permiso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `permiso` tinyint(1) DEFAULT '1',
  `creacion_at` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`,`menu_id`,`grupo_id`),
  KEY `fk_permiso_menu1` (`menu_id`),
  KEY `fk_permiso_grupo1` (`grupo_id`),
  CONSTRAINT `fk_permiso_grupo1` FOREIGN KEY (`grupo_id`) REFERENCES `grupo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_permiso_menu1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=283 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `permiso` */

insert  into `permiso`(`id`,`menu_id`,`grupo_id`,`hora_inicio`,`hora_fin`,`permiso`,`creacion_at`) values (1,1,5,'07:00:00','12:00:00',1,NULL),(94,1,1,NULL,NULL,1,NULL),(95,2,1,NULL,NULL,1,NULL),(96,3,1,NULL,NULL,1,NULL),(97,4,1,NULL,NULL,1,NULL),(98,5,1,NULL,NULL,1,NULL),(99,7,1,NULL,NULL,1,NULL),(100,15,1,NULL,NULL,1,NULL),(101,16,1,NULL,NULL,1,NULL),(102,17,1,NULL,NULL,1,NULL),(105,20,1,NULL,NULL,1,NULL),(106,21,1,NULL,NULL,1,NULL),(107,22,1,NULL,NULL,1,NULL),(108,23,1,NULL,NULL,1,NULL),(109,26,1,NULL,NULL,1,NULL),(110,29,1,NULL,NULL,1,NULL),(111,30,1,NULL,NULL,1,NULL),(112,33,1,NULL,NULL,0,NULL),(113,37,1,NULL,NULL,1,NULL),(114,38,1,NULL,NULL,1,NULL),(115,39,1,NULL,NULL,1,NULL),(116,42,1,NULL,NULL,1,NULL),(117,44,1,NULL,NULL,1,NULL),(118,47,1,NULL,NULL,1,NULL),(119,48,1,NULL,NULL,1,NULL),(120,49,1,NULL,NULL,1,NULL),(121,50,1,NULL,NULL,1,NULL),(122,51,1,NULL,NULL,1,NULL),(123,52,1,NULL,NULL,1,NULL),(124,53,1,NULL,NULL,1,NULL),(133,65,1,NULL,NULL,1,NULL),(134,66,1,NULL,NULL,1,NULL),(135,67,1,NULL,NULL,1,NULL),(136,68,1,NULL,NULL,1,NULL),(137,69,1,NULL,NULL,1,NULL),(138,70,1,NULL,NULL,1,NULL),(139,71,1,NULL,NULL,1,NULL),(149,81,1,NULL,NULL,1,NULL),(157,89,1,NULL,NULL,1,NULL),(158,90,1,NULL,NULL,1,NULL),(159,91,1,NULL,NULL,1,NULL),(160,92,1,NULL,NULL,1,NULL),(161,93,1,NULL,NULL,1,NULL),(221,2,3,NULL,NULL,1,NULL),(222,3,3,NULL,NULL,1,NULL),(223,4,3,NULL,NULL,1,NULL),(224,5,3,NULL,NULL,1,NULL),(225,7,3,NULL,NULL,1,NULL),(226,15,3,NULL,NULL,1,NULL),(227,16,3,NULL,NULL,1,NULL),(228,17,3,NULL,NULL,1,NULL),(231,21,3,NULL,NULL,1,NULL),(232,22,3,NULL,NULL,1,NULL),(233,23,3,NULL,NULL,1,NULL),(234,26,3,NULL,NULL,1,NULL),(235,29,3,NULL,NULL,1,NULL),(236,30,3,NULL,NULL,1,NULL),(237,33,3,NULL,NULL,0,NULL),(238,37,3,NULL,NULL,1,NULL),(239,38,3,NULL,NULL,1,NULL),(240,44,3,NULL,NULL,1,NULL),(241,47,3,NULL,NULL,1,NULL),(242,48,3,NULL,NULL,1,NULL),(243,49,3,NULL,NULL,1,NULL),(244,50,3,NULL,NULL,1,NULL),(245,51,3,NULL,NULL,1,NULL),(246,52,3,NULL,NULL,1,NULL),(247,53,3,NULL,NULL,1,NULL),(256,65,3,NULL,NULL,1,NULL),(257,66,3,NULL,NULL,1,NULL),(258,67,3,NULL,NULL,1,NULL),(259,68,3,NULL,NULL,1,NULL),(260,70,3,NULL,NULL,1,NULL),(261,71,3,NULL,NULL,1,NULL),(271,81,3,NULL,NULL,1,NULL),(279,89,3,NULL,NULL,1,NULL),(280,91,3,NULL,NULL,1,NULL),(281,92,3,NULL,NULL,1,NULL),(282,93,3,NULL,NULL,1,NULL);

/*Table structure for table `preguntas` */

DROP TABLE IF EXISTS `preguntas`;

CREATE TABLE `preguntas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_grupopregunta` int(11) DEFAULT NULL,
  `nom_pregunta` varchar(100) DEFAULT NULL,
  `publicar` tinyint(1) DEFAULT NULL,
  `orden` tinyint(2) DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_preguntas_grupopregunta` (`id_grupopregunta`),
  CONSTRAINT `FK_preguntas_grupopregunta` FOREIGN KEY (`id_grupopregunta`) REFERENCES `grupopregunta` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `preguntas` */

insert  into `preguntas`(`id`,`id_grupopregunta`,`nom_pregunta`,`publicar`,`orden`,`creacion_at`) values (1,1,'Pregunta 1',1,1,'2014-05-24');

/*Table structure for table `prioridad` */

DROP TABLE IF EXISTS `prioridad`;

CREATE TABLE `prioridad` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `grupo_id` int(10) NOT NULL,
  `servicio_id` int(10) NOT NULL,
  `id_turno` int(10) DEFAULT NULL,
  `numero` int(10) DEFAULT NULL,
  `cantidad` int(10) NOT NULL,
  `orden` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `prioridad` */

/*Table structure for table `publicidad` */

DROP TABLE IF EXISTS `publicidad`;

CREATE TABLE `publicidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `archivo_publicidad` varchar(100) NOT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Data for the table `publicidad` */

insert  into `publicidad`(`id`,`archivo_publicidad`,`creacion_at`) values (12,'publicidad.swf','2012-05-15');

/*Table structure for table `servicio` */

DROP TABLE IF EXISTS `servicio`;

CREATE TABLE `servicio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `letra` varchar(5) DEFAULT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `inicio` int(11) DEFAULT NULL,
  `fin` int(11) DEFAULT NULL,
  `actual` int(11) DEFAULT NULL,
  `ubicacion_id` int(11) DEFAULT NULL,
  `estilo_letra` varchar(100) DEFAULT NULL,
  `gruposervicio_id` tinyint(4) DEFAULT NULL,
  `prioridad_cantidad` int(11) DEFAULT NULL,
  `tiempo_maximo` time DEFAULT '00:10:00',
  `letra_alias` varchar(1) NOT NULL,
  `atencion_preferencial` tinyint(1) DEFAULT '0',
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_servicio` (`ubicacion_id`),
  KEY `FK_servicio1` (`gruposervicio_id`),
  CONSTRAINT `FK_servicio` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicacion` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_servicio1` FOREIGN KEY (`gruposervicio_id`) REFERENCES `gruposervicio` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `servicio` */

insert  into `servicio`(`id`,`nombre`,`letra`,`descripcion`,`estado`,`inicio`,`fin`,`actual`,`ubicacion_id`,`estilo_letra`,`gruposervicio_id`,`prioridad_cantidad`,`tiempo_maximo`,`letra_alias`,`atencion_preferencial`,`creacion_at`) values (1,'Servicio 1','A','Servicio 1',1,0,0,0,3,'27691D',1,0,'00:20:00',' ',0,'0000-00-00');

/*Table structure for table `serviciocaja` */

DROP TABLE IF EXISTS `serviciocaja`;

CREATE TABLE `serviciocaja` (
  `servicio_id` int(11) NOT NULL,
  `secundario` tinyint(1) DEFAULT '0',
  `llamar` tinyint(1) DEFAULT '1',
  `caja_id` int(11) NOT NULL,
  PRIMARY KEY (`servicio_id`,`caja_id`),
  KEY `fk_servicio_has_caja_servicio1` (`servicio_id`),
  KEY `fk_servicio_has_caja_caja1` (`caja_id`),
  CONSTRAINT `fk_servicio_has_caja_caja1` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_servicio_has_caja_servicio1` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `serviciocaja` */

insert  into `serviciocaja`(`servicio_id`,`secundario`,`llamar`,`caja_id`) values (1,0,1,1);

/*Table structure for table `sesiones` */

DROP TABLE IF EXISTS `sesiones`;

CREATE TABLE `sesiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `caja_id` int(11) DEFAULT NULL,
  `ubicacion_id` int(11) DEFAULT NULL,
  `estado` varchar(15) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `duracion` time DEFAULT '00:00:00',
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_sesiones_usuarios` (`usuario_id`),
  KEY `FK_sesiones_caja` (`caja_id`),
  KEY `FK_sesiones_ubicacion` (`ubicacion_id`),
  CONSTRAINT `FK_sesiones_caja` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_sesiones_ubicacion` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicacion` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_sesiones_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sesiones` */

/*Table structure for table `sinccajas` */

DROP TABLE IF EXISTS `sinccajas`;

CREATE TABLE `sinccajas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sucursal_id` int(11) NOT NULL,
  `base_datos` varchar(50) NOT NULL,
  `caja_id_sucursal` int(11) NOT NULL,
  `usuario_sucursal` varchar(50) NOT NULL,
  `numero_caja_sucursal` int(11) NOT NULL,
  `area_id_sucursal` int(11) NOT NULL,
  `area_nombre_sucursal` varchar(100) DEFAULT NULL,
  `fecha_inicio_atencion` date NOT NULL,
  `hora_inicio_atencion` time NOT NULL,
  `fecha_fin_atencion` date NOT NULL,
  `hora_fin_atenciom` time NOT NULL,
  `duracion` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_sinccajas_sucursal` (`sucursal_id`),
  CONSTRAINT `FK_sinccajas_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `sinccajas` */

/*Table structure for table `sinchistorialcajas` */

DROP TABLE IF EXISTS `sinchistorialcajas`;

CREATE TABLE `sinchistorialcajas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sucursal_id` int(11) NOT NULL,
  `fecha_sincronizacion` date NOT NULL,
  `hora_sincronizacion` time NOT NULL,
  `registros_sincronizados` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_sinchistorialcajas_sucursal` (`sucursal_id`),
  CONSTRAINT `FK_sinchistorialcajas_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sinchistorialcajas` */

/*Table structure for table `sincturnos` */

DROP TABLE IF EXISTS `sincturnos`;

CREATE TABLE `sincturnos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_referencia` int(11) DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `base_datos` varchar(50) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `numero_modulo` tinyint(4) DEFAULT NULL,
  `modulo_id_sucursal` int(11) DEFAULT NULL,
  `area_id_sucursal` int(11) DEFAULT NULL,
  `nombre_area_sucursal` varchar(30) DEFAULT NULL,
  `nombre_servicio` varchar(50) DEFAULT NULL,
  `letra` varchar(1) DEFAULT NULL,
  `servicio_id_sucursal` int(11) DEFAULT NULL,
  `numero_turno` varchar(5) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `hora_emision` time DEFAULT NULL,
  `atendido` tinyint(4) DEFAULT NULL,
  `fecha_inicio_atencion` date DEFAULT NULL,
  `hora_inicio_atencion` time DEFAULT NULL,
  `fecha_fin_atencion` date DEFAULT NULL,
  `hora_fin_atencion` time DEFAULT NULL,
  `duracion` time DEFAULT NULL,
  `rechazado` tinyint(4) DEFAULT NULL,
  `calificacion` varchar(20) DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_sincturnos_sucursal` (`sucursal_id`),
  CONSTRAINT `FK_sincturnos_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sincturnos` */

/*Table structure for table `sistema` */

DROP TABLE IF EXISTS `sistema`;

CREATE TABLE `sistema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombrecomercial` varchar(255) NOT NULL,
  `razonsocial` varchar(255) NOT NULL,
  `logentradasalida` tinyint(1) DEFAULT NULL,
  `logmodificacion` tinyint(1) DEFAULT NULL,
  `logvisualizacion` tinyint(1) DEFAULT NULL,
  `intentoslogin` tinyint(1) DEFAULT '3',
  `tiemposesion` float DEFAULT '30',
  `validezclave` int(11) DEFAULT '1',
  `contenidoclave` varchar(50) DEFAULT NULL,
  `modclaveacceso` tinyint(1) DEFAULT NULL,
  `emailadmin` varchar(255) DEFAULT NULL,
  `claveadmin` varchar(255) DEFAULT NULL,
  `logosuperior` varchar(50) DEFAULT NULL,
  `logoreporte` varchar(50) DEFAULT NULL,
  `logoprincipal` varchar(50) DEFAULT NULL,
  `idioma` varchar(5) DEFAULT NULL,
  `simbolomoneda` varchar(5) NOT NULL,
  `formatoreporte` varchar(5) NOT NULL,
  `notificarbloqueo` tinyint(1) DEFAULT NULL,
  `plantilla` varchar(50) DEFAULT NULL,
  `numerodecimal` int(11) DEFAULT '2',
  `tamanocontrasena` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sistema` */

/*Table structure for table `suceso` */

DROP TABLE IF EXISTS `suceso`;

CREATE TABLE `suceso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_id` int(11) NOT NULL,
  `auditoria_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `detalle` varchar(250) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `hora_inico` time DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `duracion_dias` date DEFAULT NULL,
  `duracion_horas` time DEFAULT NULL,
  `ruta_accion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_suceso_evento1` (`evento_id`),
  KEY `fk_suceso_auditoria1` (`auditoria_id`,`usuario_id`),
  CONSTRAINT `fk_suceso_auditoria1` FOREIGN KEY (`auditoria_id`, `usuario_id`) REFERENCES `auditoria` (`id`, `usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_suceso_evento1` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `suceso` */

/*Table structure for table `sucursal` */

DROP TABLE IF EXISTS `sucursal`;

CREATE TABLE `sucursal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias_sucursal` varchar(100) NOT NULL,
  `host` varchar(100) NOT NULL,
  `nombre_bd` varchar(100) DEFAULT NULL,
  `usuario_bd` varchar(100) DEFAULT NULL,
  `password_bd` varchar(100) DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sucursal` */

/*Table structure for table `t_prensa_externa` */

DROP TABLE IF EXISTS `t_prensa_externa`;

CREATE TABLE `t_prensa_externa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servicio_id` int(11) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `hora_emision` time DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `caja_id` int(4) DEFAULT NULL,
  `por_atender` tinyint(4) DEFAULT NULL,
  `atendido` tinyint(4) DEFAULT NULL,
  `fecha_inicio_atencion` date DEFAULT NULL,
  `hora_inicio_atencion` time DEFAULT NULL,
  `fecha_fin_atencion` date DEFAULT NULL,
  `hora_fin_atencion` time DEFAULT NULL,
  `duracion` time DEFAULT NULL,
  `rechazado` tinyint(4) DEFAULT NULL,
  `calificacion` varchar(20) DEFAULT NULL,
  `transferido` tinyint(1) DEFAULT '0',
  `username` varchar(60) DEFAULT NULL,
  `nombre_usuario` varchar(90) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_turnos` (`servicio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `t_prensa_externa` */

/*Table structure for table `tiposujeto` */

DROP TABLE IF EXISTS `tiposujeto`;

CREATE TABLE `tiposujeto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `padreid` int(11) DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `tiposujeto` */

/*Table structure for table `titulo` */

DROP TABLE IF EXISTS `titulo`;

CREATE TABLE `titulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `concepto` varchar(45) NOT NULL,
  `siglas` varchar(10) NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `titulo` */

/*Table structure for table `turnos` */

DROP TABLE IF EXISTS `turnos`;

CREATE TABLE `turnos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servicio_id` int(11) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `hora_emision` time DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `caja_id` int(4) DEFAULT NULL,
  `por_atender` tinyint(4) DEFAULT NULL,
  `atendido` tinyint(4) DEFAULT NULL,
  `fecha_inicio_atencion` date DEFAULT NULL,
  `hora_inicio_atencion` time DEFAULT NULL,
  `fecha_fin_atencion` date DEFAULT NULL,
  `hora_fin_atencion` time DEFAULT NULL,
  `duracion` time DEFAULT NULL,
  `rechazado` tinyint(4) DEFAULT NULL,
  `calificacion` varchar(20) DEFAULT 'NO CALIFICADO',
  `transferido` tinyint(1) DEFAULT '0',
  `username` varchar(60) DEFAULT NULL,
  `nombre_usuario` varchar(90) DEFAULT NULL,
  `adm_revisado` tinyint(1) NOT NULL DEFAULT '0',
  `numero_alias` int(11) DEFAULT NULL,
  `prioridad` tinyint(1) DEFAULT NULL,
  `id_username` int(15) DEFAULT NULL,
  `valor_calificacion` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_turnos` (`servicio_id`),
  KEY `index_fecha_emision` (`fecha_emision`),
  CONSTRAINT `FK_turnos` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `turnos` */

/*Table structure for table `turnos_resumen` */

DROP TABLE IF EXISTS `turnos_resumen`;

CREATE TABLE `turnos_resumen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_modulo` int(11) DEFAULT NULL,
  `Fecha_cierre` date DEFAULT NULL,
  `hora_cierre` time DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `can_atendios` double DEFAULT NULL,
  `can_anulados` double DEFAULT NULL,
  `Promedio_Atencion` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_turnos_resumen` (`id_modulo`),
  CONSTRAINT `FK_turnos_resumen` FOREIGN KEY (`id_modulo`) REFERENCES `caja` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `turnos_resumen` */

/*Table structure for table `turnos_transferidos` */

DROP TABLE IF EXISTS `turnos_transferidos`;

CREATE TABLE `turnos_transferidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servicio_id` int(11) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `hora_emision` time DEFAULT NULL,
  `hora_transferido` time DEFAULT NULL,
  `estado` tinyint(4) DEFAULT '0',
  `caja_id` int(4) DEFAULT NULL,
  `por_atender` tinyint(4) DEFAULT '0',
  `atendido` tinyint(4) DEFAULT '0',
  `fecha_inicio_atencion` date DEFAULT NULL,
  `hora_inicio_atencion` time DEFAULT NULL,
  `fecha_fin_atencion` date DEFAULT NULL,
  `hora_fin_atencion` time DEFAULT NULL,
  `duracion` time DEFAULT NULL,
  `rechazado` tinyint(4) DEFAULT '0',
  `calificacion` varchar(20) DEFAULT NULL,
  `permiso_cajas` varchar(50) DEFAULT NULL,
  `letra` varchar(5) DEFAULT NULL,
  `remitente` int(30) DEFAULT NULL,
  `ubicacion_id` int(11) DEFAULT NULL,
  `tipo` varchar(15) DEFAULT NULL,
  `fecha_atender` date DEFAULT NULL,
  `hora_atender` time DEFAULT NULL,
  `adm_revisado` tinyint(1) DEFAULT '0',
  `id_user_atiende` int(15) DEFAULT NULL,
  `id_user_transfiere` int(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_turnos_transferidos` (`servicio_id`),
  CONSTRAINT `FK_turnos_transferidos` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `turnos_transferidos` */

/*Table structure for table `turnoseteo` */

DROP TABLE IF EXISTS `turnoseteo`;

CREATE TABLE `turnoseteo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fraseinicial` varchar(45) DEFAULT NULL,
  `chkfraseinicial` tinyint(4) DEFAULT NULL,
  `empresa` varchar(45) DEFAULT NULL,
  `chkempresa` tinyint(4) DEFAULT NULL,
  `logo` varchar(45) DEFAULT NULL,
  `chklogo` tinyint(4) DEFAULT NULL,
  `chkinicial` tinyint(4) DEFAULT NULL,
  `chkservicio` tinyint(4) DEFAULT NULL,
  `chkubicacion` tinyint(4) DEFAULT NULL,
  `chkfecha` tinyint(4) DEFAULT NULL,
  `chktiempoespera` tinyint(4) DEFAULT NULL,
  `chkturnoespera` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `turnoseteo` */

insert  into `turnoseteo`(`id`,`fraseinicial`,`chkfraseinicial`,`empresa`,`chkempresa`,`logo`,`chklogo`,`chkinicial`,`chkservicio`,`chkubicacion`,`chkfecha`,`chktiempoespera`,`chkturnoespera`) values (1,NULL,1,NULL,0,'logo-cooperativa.bmp',1,0,1,0,1,1,1);

/*Table structure for table `turnospreguntas` */

DROP TABLE IF EXISTS `turnospreguntas`;

CREATE TABLE `turnospreguntas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `preguntas_id` int(11) DEFAULT NULL,
  `caja_id` int(11) DEFAULT NULL,
  `turnos_id` int(11) DEFAULT NULL,
  `puntuacion` tinyint(3) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_turnospreguntas_turnos` (`turnos_id`),
  KEY `FK_turnospreguntas_preguntas` (`preguntas_id`),
  KEY `FK_turnospreguntas_caja` (`caja_id`),
  CONSTRAINT `FK_turnospreguntas_caja` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_turnospreguntas_preguntas` FOREIGN KEY (`preguntas_id`) REFERENCES `preguntas` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_turnospreguntas_turnos` FOREIGN KEY (`turnos_id`) REFERENCES `turnos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `turnospreguntas` */

/*Table structure for table `ubicacion` */

DROP TABLE IF EXISTS `ubicacion`;

CREATE TABLE `ubicacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_ubicacion` varchar(45) NOT NULL,
  `descripcion` varchar(45) DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `ubicacion` */

insert  into `ubicacion`(`id`,`nombre_ubicacion`,`descripcion`,`creacion_at`) values (3,'Principal',NULL,'0000-00-00');

/*Table structure for table `usuario` */

DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `ci` varchar(15) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `movil` varchar(15) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Activo',
  `username` varchar(16) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `actclave` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `descripcion` mediumtext,
  `foto` varchar(50) DEFAULT NULL,
  `login` smallint(1) DEFAULT '0',
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `usuario` */

insert  into `usuario`(`id`,`nombres`,`ci`,`telefono`,`movil`,`estado`,`username`,`password`,`actclave`,`email`,`descripcion`,`foto`,`login`,`creacion_at`) values (1,'Administrador',NULL,NULL,NULL,'Activo','administrador',NULL,'0000-00-00',NULL,'Administrador',NULL,0,'0000-00-00'),(8,'Superadministrador',NULL,NULL,NULL,'Activo','superadmin','','0000-00-00',NULL,NULL,'',0,'0000-00-00'),(101,'Pantalla','','','','Activo','pantalla','','0000-00-00','','','',NULL,'2014-05-24'),(102,'Dispensador','','','','Activo','dispensador','','0000-00-00','','','',NULL,'2014-05-24'),(103,'Operador 1','','','','Activo','operador','','0000-00-00','','','',0,'2014-05-24');

/*Table structure for table `video` */

DROP TABLE IF EXISTS `video`;

CREATE TABLE `video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `activo` tinyint(4) DEFAULT NULL,
  `creacion_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;

/*Data for the table `video` */

insert  into `video`(`id`,`nombre`,`ubicacion`,`duracion`,`activo`,`creacion_at`) values (49,'z-inicio.mpg','c:/videos',4,1,'2010-09-28');

/* Function  structure for function  `promedio_global` */

/*!50003 DROP FUNCTION IF EXISTS `promedio_global` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `promedio_global`(f date, n varchar(100), ns VARCHAR(100) ) RETURNS int(11)
    DETERMINISTIC
BEGIN
	-- Function logic here
	SET @documentos_entregados = (
SELECT SUM(promedio_calificacion) FROM 
(SELECT 
 CASE 
WHEN t.calificacion ='Excelente' THEN 4
WHEN t.calificacion ='Muy Bueno' THEN 3
WHEN t.calificacion ='Bueno' THEN 2
WHEN t.calificacion ='Regular' THEN 1
ELSE 0 END * COUNT(t.calificacion) AS promedio_calificacion
FROM turnos t, usuario u, servicio s 
WHERE u.id=t.id_username  AND s.id=t.servicio_id AND t.calificacion!='"NO CALIFICADO"' AND 
fecha_emision = f AND s.nombre = n AND u.nombres = ns
GROUP BY  fecha_inicio_atencion, s.id, u.id,  calificacion) AS TABLA);
	
	
	
	RETURN @documentos_entregados;
END */$$
DELIMITER ;

/* Function  structure for function  `promedio_global1` */

/*!50003 DROP FUNCTION IF EXISTS `promedio_global1` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `promedio_global1`(f DATE, n VARCHAR(100), ns VARCHAR(100) ) RETURNS int(11)
    DETERMINISTIC
BEGIN
	-- Function logic here
	SET @documentos_entregados = (
SELECT SUM(promedio_calificacion) FROM 
(SELECT 
 CASE 
WHEN co.calificacion ='Excelente' THEN 4
WHEN co.calificacion ='Muy Bueno' THEN 3
WHEN co.calificacion ='Bueno' THEN 2
WHEN co.calificacion ='Regular' THEN 1
ELSE 0 END * COUNT(co.calificacion) AS promedio_calificacion
FROM colas co, usuario u, caja c
WHERE u.id=co.id_username  AND c.id=co.caja_id AND co.calificacion!='"NO CALIFICADO"' AND 
fecha_inicio_atencion = f AND c.descripcion = n AND u.nombres = ns
GROUP BY  fecha_inicio_atencion, c.id, u.id,  calificacion) AS TABLA);
	
	
	
	RETURN @documentos_entregados;
END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
