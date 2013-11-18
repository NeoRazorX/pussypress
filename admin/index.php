<?php
/*
 * This file is part of PussyPress
 * Copyright (C) 2013  Carlos Garcia Gomez  neorazorx@gmail.com
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

require_once 'config.php';
require_once 'raintpl/rain.tpl.class.php';
require_once 'post.class.php';
require_once 'compile.php';
require_once 'import.php';

if( !file_exists('../index.html') OR isset($_GET['recompile']) )
{
	/// salimos del directorio admin
   chdir('..');

	echo 'Compilando en blog...';
	compile_blog();
	echo '<br/>clic <a href="..">aqu&iacute;</a> para volver.';

	/// volvemos a admin
   chdir('admin');
}
else if( isset($_FILES['blog_file']['tmp_name']) )
{
	if($_POST['password'] == PUSSY_PASSWORD)
	{
		if( is_uploaded_file($_FILES['blog_file']['tmp_name']) )
		{
			/// salimos del directorio admin
   		chdir('..');

			echo 'Importando datos del archivo...';
			import_file();
			compile_blog();
			echo '<br/>clic <a href="..">aqu&iacute;</a> para volver.';

			/// volvemos a admin
	   	chdir('admin');
		}
		else
		{
			echo 'No se encuentra el archivo. Es posible que exceda el tama&ntilde;o m&aacute;ximo que permite php.
				Edita el archivo php.ini para solucionarlo.';
		}
	}
	else
	{
		echo 'Contrase&ntilde;a incorrecta.';
	}
}
else
{
	/// configuramos rain.tpl
	raintpl::configure('base_url', NULL);
	raintpl::configure('path_replace', FALSE);
	raintpl::configure('tpl_dir', './');
	raintpl::configure('cache_dir', '/tmp/');

	$tpl = new RainTPL();
	$tpl->assign('pussy_title', PUSSY_TITLE);
	$tpl->assign('pussy_description', PUSSY_DESCRIPTION);
	$tpl->assign('pussy_theme', PUSSY_THEME);
	$tpl->assign('pussy_domain', PUSSY_DOMAIN);
	$tpl->assign('pussy_google_analytics', PUSSY_GOOGLE_ANALYTICS);
	$tpl->assign('pussy_twitter', PUSSY_TWITTER);
	$tpl->assign('pussy_github', PUSSY_GITHUB);

	$messages = array();

	chdir('..');

	if( isset($_POST['file']) )
	{
		$post = new post( urldecode($_POST['file']) );

		if($_POST['password'] == PUSSY_PASSWORD)
		{
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
		}
		else
			$messages[] = 'Contraseña incorrecta.';
	}
	else if( isset($_GET['edit']) )
		$post = new post( urldecode($_GET['edit']) );
	else
		$post = new post();

	chdir('admin');

	$tpl->assign('post', $post);
	$tpl->assign('messages', $messages);
	$tpl->draw('admin.html');
}

?>