<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Servidor
|--------------------------------------------------------------------------
|
| Dirección del servidor de imap
|
|	imap.example.com/
|
*/
$config['server']	= 'vigiliam.com';


/*
|--------------------------------------------------------------------------
| Seguridad
|--------------------------------------------------------------------------
|
| Tipo de seguridad utilizada para la conexión imap
|
| 'SSL'				Defecto
| 'TLS'
|
*/
$config['security']	= 'SSL';

/*
|--------------------------------------------------------------------------
| Puerto para la conexión imap
|--------------------------------------------------------------------------
|
| Puerto utilizado para la conexión imap
|
| '993'				
| '143'
|
*/
$config['port']	= '993';

/*
|--------------------------------------------------------------------------
| Certificado de Seguridad
|--------------------------------------------------------------------------
|
| Si la conexión necesita añadir un certificado
|
*/
$config['cert']	= 'novalidate-cert';

/*
|--------------------------------------------------------------------------
| Server encoding
|--------------------------------------------------------------------------
|
| Codificacion del servidor de correo
|
| 'UTF-8'			Defecto
| 'ISO-8859-1'			

*/
$config['server_encoding'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Direccion de descarga de adjuntos
|--------------------------------------------------------------------------
|
| Ruta en la cual se descargarán los ficheros adjuntos inline
|
*/
$config['attachments_dir']	= 'adjuntos';

/*
|--------------------------------------------------------------------------
| Carpetas principales
|--------------------------------------------------------------------------
|
| Nombre de las carpetas que cargaremos como bandejas principales
|
*/
$config['folders']['INBOX'] = "Entrada";
$config['folders']['Enviados'] = "Enviados";
$config['folders']['Papelera'] = "Papelera";
$config['folders']['Borradorse'] = "Borradores";

