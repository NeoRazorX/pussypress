<?php
/*
 * This file is part of YASB
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

foreach(scandir('.') as $year)
{
	if( is_dir($year) AND is_numeric($year) )
	{
		foreach(scandir($year) as $month)
		{
			if( is_dir($year.'/'.$month) AND is_numeric($month) )
			{
				foreach(scandir($year.'/'.$month) as $file)
				{
					if( substr($file, -5) == '.post' )
						compile_file($year.'/'.$month.'/'.$file);
				}
			}
		}
	}
}

function compile_file($post_filename)
{
	echo 'compilando '.$post_filename."\n";

	$post_title = '';
	$keywords = '';
	$post_html = '';

	$file = fopen($post_filename, 'r');
	if($file)
	{
		$read_post = false;

		while( !feof($file) )
		{
			$line = trim( fgets($file, 1024) );

			if( substr($line, 0, 7) == 'title: ' )
				$post_title = substr($line, 7);
			else if( substr($line, 0, 10) == 'keywords: ' )
				$keywords = substr($line, 10);
			else if( $line == '----------' )
				$read_post = true;
			else if($read_post)
				$post_html .= $line;
		}

		fclose($file);
	}

	$description = strip_tags( stripslashes($post_html) );

	$comment_filename = substr($post_filename, 0, -4).'comment';

	$filename = substr($post_filename, 0, -4).'html';
	$file = fopen($filename, 'w');
	if($file)
	{
		$template = file_get_contents('html/post.html');
		$template = str_replace('{{$title}}', $post_title, $template);
		$template = str_replace('{{$description}}', $description, $template);
		$template = str_replace('{{$keywords}}', $keywords, $template);
		$template = str_replace('{{$post_title}}', $post_title, $template);
		$template = str_replace('{{$post_html}}', $post_html, $template);

		fwrite($file, $template);
		fclose($file);
	}
}

?>