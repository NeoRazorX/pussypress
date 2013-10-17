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

include "config.php";

function post2array($filename)
{
	$post = array(
		'link' => '/',
		'title' => 'Sin título',
		'published' => time(),
		'updated' => time(),
		'keywords' => '',
		'body' => ''
	);

	$file = fopen($filename, 'r');
	if($file)
	{
		$post['link'] = substr($filename, 0, -4).'html';
		$read_post = false;

		while( !feof($file) )
		{
			$line = trim( fgets($file, 1024) );

			if( substr($line, 0, 7) == 'title: ' )
				$post['title'] = substr($line, 7);
         else if( substr($line, 0, 11) == 'published: ' )
            $post['published'] = strtotime( substr($line, 11) );
         else if( substr($line, 0, 9) == 'updated: ' )
            $post['updated'] = strtotime( substr($line, 9) );
         else if( substr($line, 0, 10) == 'keywords: ' )
            $post['keywords'] = substr($line, 10);
			else if( $line == '----------' )
				$read_post = true;
         else if($read_post)
         	$post['body'] .= $line;
		}

		fclose($file);
   }

	return $post;
}

function compile_post_list($filename, $posts)
{
	echo "Compilando ".$filename."\n";

	$file = fopen($filename, 'w');
   if($file)
   {
      $template = file_get_contents('html/post-list.html');
      $template = str_replace(array("\r\n", "\r", "\n"), '', $template);
      $template = str_replace('{{$title}}', PUSSY_TITLE, $template);
      $template = str_replace('{{$description}}', PUSSY_DESCRIPTION, $template);
      $template = str_replace('{{$blog_title}}', PUSSY_TITLE, $template);
      $template = str_replace('{{$blog_description}}', PUSSY_DESCRIPTION, $template);

      $aux = array();
      $foreach_post_code = '';
      $foreach_post = '';
      if(preg_match_all('#\{\{foreach_post\}\}(.*?)\{\{\/foreach_post\}\}#', $template, $aux) )
      {
      	$foreach_post_code = $aux[0][0];
      	$foreach_post = $aux[1][0];
      }

      $new_foreach_post = '';
      foreach( $posts as $post )
      {
      	$new_foreach_post .= $foreach_post;
      	$new_foreach_post = str_replace('{{$post_link}}', $post['link'], $new_foreach_post);
      	$new_foreach_post = str_replace('{{$post_title}}', $post['title'], $new_foreach_post);
      	$new_foreach_post = str_replace('{{$post_body}}', $post['body'], $new_foreach_post);
      }

      $template = str_replace($foreach_post_code, $new_foreach_post, $template);
      $template = str_replace('{{$css}}', file_get_contents('html/style.css'), $template);
      fwrite($file, $template);
      fclose($file);
   }
}

function compile_root($files)
{
	$posts = array();
   foreach($files as $filename)
      $posts[] = post2array($filename);

   usort($posts, function($a, $b) { return $b['published'] - $a['published']; });
   $post_count = 5;
   foreach($posts as $i => $value)
   {
      if($post_count < 1)
         unset($posts[$i]);

      $post_count--;
   }

   compile_post_list('index.html', $posts);
}

function compile_year($folder, $files)
{
	$posts = array();
   foreach($files as $filename)
      $posts[] = post2array($filename);

   usort($posts, function($a, $b) { return $b['published'] - $a['published']; });
   $post_count = 10;
   foreach($posts as $i => $value)
   {
      if($post_count < 1)
         unset($posts[$i]);

      $post_count--;
   }

   compile_post_list($folder.'/index.html', $posts);
}

function compile_month($folder, $files)
{
	$posts = array();
   foreach($files as $filename)
      $posts[] = post2array($filename);

   usort($posts, function($a, $b) { return $b['published'] - $a['published']; });
   compile_post_list($folder.'/index.html', $posts);
}

function compile_file($post_filename)
{
	$filename = substr($post_filename, 0, -4).'html';
   echo 'compilando '.$filename."\n";
   
   $post = post2array($post_filename);
   $description = strip_tags( stripslashes($post['body']) );
   if( strlen($description) > 150 )
      $description = substr($description, 0, 150).'...';
   
   $comment_filename = substr($post_filename, 0, -4).'comment';
   
   $file = fopen($filename, 'w');
   if($file)
   {
      $template = file_get_contents('html/post.html');
      $template = str_replace('{{$title}}', $post['title'], $template);
      $template = str_replace('{{$description}}', $description, $template);
      $template = str_replace('{{$keywords}}', $post['keywords'], $template);
      $template = str_replace('{{$blog_title}}', PUSSY_TITLE, $template);
      $template = str_replace('{{$blog_description}}', PUSSY_DESCRIPTION, $template);
      $template = str_replace('{{$post_link}}', $post['link'], $template);
      $template = str_replace('{{$post_title}}', $post['title'], $template);
      $template = str_replace('{{$post_body}}', $post['body'], $template);
      $template = str_replace('{{$css}}', file_get_contents('html/style.css'), $template);
      fwrite($file, $template);
      fclose($file);
   }
}


$all_files = array();
foreach(scandir('.') as $year)
{
	if( is_dir($year) AND is_numeric($year) )
   {
   	$year_files = array();
   	foreach(scandir($year) as $month)
      {
      	if( is_dir($year.'/'.$month) AND is_numeric($month) )
         {
            $month_files = array();
            foreach(scandir($year.'/'.$month) as $file)
            {
               if( substr($file, -5) == '.post' )
               {
                  $all_files[] = $year.'/'.$month.'/'.$file;
                  $year_files[] = $year.'/'.$month.'/'.$file;
                  $month_files[] = $year.'/'.$month.'/'.$file;

                  compile_file($year.'/'.$month.'/'.$file);
               }
            }
            
            compile_month($year.'/'.$month, $month_files);
         }
      }

      compile_year($year, $year_files);
   }
}

compile_root($all_files);

?>