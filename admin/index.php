<?php
/*
 * This file is part of PussyPress
 * Copyright (C) 2013-2016  Carlos Garcia Gomez  neorazorx@gmail.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if( !file_exists('config.php') )
{
	echo 'ERROR: No se encuentra el archivo "admin/config.php". Tienes que renombrar
		el archivo "admin/config-sample.php y rellenarlo."';
}
else if( !function_exists('mb_strlen') )
{
   echo 'ERROR: No se encuentra la función mb_strlen. Instala el paquete php-mbstring';
}
else if( !function_exists('simplexml_load_file') )
{
   echo 'ERROR: No se encuentra la función simplexml_load_file. Instala el paquete php-xml';
}
else
{
	require_once 'config.php';
	require_once 'raintpl/rain.tpl.class.php';
	require_once 'post.class.php';
	require_once 'compile.php';
	require_once 'import.php';
	require_once 'fail2ban.php';

	/// salimos del directorio admin
	chdir('..');

	/// configuramos rain.tpl
	raintpl::configure('base_url', NULL);
	raintpl::configure('path_replace', FALSE);
	raintpl::configure('tpl_dir', 'admin/');
	raintpl::configure('tpl_dir2', 'themes/'.PUSSY_THEME.'/');
	raintpl::configure('cache_dir', '/tmp/');

	$tpl = new RainTPL();
	$tpl->assign('pussy_title', PUSSY_TITLE);
	$tpl->assign('pussy_description', PUSSY_DESCRIPTION);
	$tpl->assign('pussy_theme', PUSSY_THEME);
	$tpl->assign('pussy_domain', PUSSY_DOMAIN);

	$messages = array();
	$post = new post();


	if( isset($_COOKIE['pussy_pswd']) )
   {
		$pussy_pswd = $_COOKIE['pussy_pswd'];
   }
	else
		$pussy_pswd = '';


	if( !file_exists('index.html') OR isset($_GET['recompile']) )
	{
		$messages[] = compile_blog();
	}
	else if( isset($_FILES['blog_file']['tmp_name']) )
	{
		$ips = array();

		if( banned_ip($ips) )
		{
			$messages[] = 'Tu IP ha sido baneada, tendr&aacute;s que esperar una hora antes de volver a entrar.';
			ban_ip($ips);
		}
		else if($_POST['password'] == PUSSY_PASSWORD)
		{
			/// guardamos la contraseña
			setcookie('pussy_pswd', $_POST['password'], time()+3600);
			$pussy_pswd = $_POST['password'];

			if( is_uploaded_file($_FILES['blog_file']['tmp_name']) )
			{
				$messages[] = 'Importando datos del archivo...';
				import_file();
				compile_blog();
				$messages[] = 'Blog recompilado!';
			}
			else
			{
            $max = intval( ini_get('post_max_size') );
            if( intval(ini_get('upload_max_filesize')) < $max )
            {
               $max = intval(ini_get('upload_max_filesize'));
            }
            
				$messages[] = 'No se encuentra el archivo. Es posible que exceda el tama&ntilde;o m&aacute;ximo que permite php: '
                    .$max.' MB. Edita el archivo php.ini para solucionarlo.';
			}
		}
		else
		{
			$messages[] = 'Contrase&ntilde;a incorrecta.';
			ban_ip($ips);
		}
	}
	else
	{
		if( isset($_POST['file']) )
		{
			$post = new post( urldecode($_POST['file']) );
			$ips = array();

			if( banned_ip($ips) )
			{
				$messages[] = 'Tu IP ha sido baneada, tendrás que esperar una hora antes de volver a entrar.';
				ban_ip($ips);
			}
			else if($_POST['password'] == PUSSY_PASSWORD)
			{
				/// guardamos la contraseña
				setcookie('pussy_pswd', $_POST['password'], time()+3600);
				$pussy_pswd = $_POST['password'];

				if( isset($_POST['delete']) )
				{
					$post->delete();
					$messages[] = 'Post eliminado.';
				}
				else
				{
					$post->title = $_POST['title'];
					$post->body = $_POST['body'];
					$post->keywords = $_POST['keywords'];
					$post->save();
					$messages[] = 'Datos guardados.';
				}

				compile_blog();
			}
			else
			{
				$messages[] = 'Contraseña incorrecta.';
				ban_ip($ips);
			}
		}
		else if( isset($_GET['edit']) )
      {
			$post = new post( urldecode($_GET['edit']) );
      }
	}

	$tpl->assign('pussy_pswd', $pussy_pswd);
	$tpl->assign('post', $post);
	$tpl->assign('messages', $messages);
	$tpl->draw('admin.html');

	/// volvemos a admin
	chdir('admin');
}
