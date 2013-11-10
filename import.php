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

function new_post(&$entry)
{
	if($entry->link)
	{
		foreach($entry->link as $link)
		{
			if( (string)$link->attributes()->rel == 'alternate' )
			{
				$url = (string)$link->attributes()->href;
				$aux = explode('/', $url);
				$year = $aux[3];
				$month = $aux[4];
				$filename = $aux[5];
				$new_filename = $year.'/'.$month.'/'.substr($filename, 0, -4).'post';

				$keywords = array();
				foreach($entry->category as $category)
				{
					if( (string)$category->attributes()->scheme == 'http://www.blogger.com/atom/ns#' )
						$keywords[] = (string)$category->attributes()->term;
				}

				if( !file_exists($year) )
					mkdir($year);

				if( !file_exists($year.'/'.$month) )
					mkdir($year.'/'.$month);

				if( !file_exists($new_filename) )
				{
					$file = fopen($new_filename, 'w');
					if($file)
					{
						fwrite($file, 'title: '.(string)$entry->title."\n");
						fwrite($file, 'published: '.strtotime( (string)$entry->published )."\n");
						fwrite($file, 'updated: '.strtotime( (string)$entry->updated )."\n");
						fwrite($file, 'keywords: '.join(', ', $keywords)."\n");
						fwrite($file, "----------\n");
						fwrite($file, htmlspecialchars_decode( (string)$entry->content )."\n");
						fclose($file);
					}

					touch( substr($new_filename, 0, -4).'comments' );
				}
				break;
			}
		}
	}
}

function new_comment(&$entry)
{
	if($entry->link)
	{
		foreach($entry->link as $link)
		{
			if( (string)$link->attributes()->rel == 'alternate' )
			{
				$aux = explode('?', $link->attributes()->href);
				$url = $aux[0];
				$aux2 = explode('/', $url);
				if( count($aux2) == 6 )
				{
					$year = $aux2[3];
					$month = $aux2[4];
					$filename = $aux2[5];
					$new_filename = $year.'/'.$month.'/'.substr($filename, 0, -4).'comments';

					if( file_exists($new_filename) )
					{
						$file = fopen($new_filename, 'a');
						if($file)
						{
							fwrite($file, 'author: '.(string)$entry->author->name."\n");
							fwrite($file, 'published: '.strtotime( (string)$entry->published )."\n");
							fwrite($file, 'updated: '.strtotime( (string)$entry->updated )."\n");
							fwrite($file, "----------\n");
							fwrite($file, htmlspecialchars_decode( (string)$entry->content )."\n");
							fwrite($file, "*****END*COMMENT*****\n");
							fclose($file);
						}
					}
				}
			}
		}
	}
}


if( !isset($_SERVER["argv"]) )
   echo "uso: php5 import.php ruta_del_archivo.xml\n";
else if( count($_SERVER["argv"]) != 2 )
   echo "uso: php5 import.php ruta_del_archivo.xml\n";
else if( !file_exists($_SERVER["argv"][1]) )
   echo "Archivo no encontrado.\n";
else
{
	$xml = simplexml_load_file($_SERVER["argv"][1]);
	if($xml)
	{
		if($xml->entry)
		{
			$postcategory = "http://schemas.google.com/blogger/2008/kind#post";
			$postimport = "blogger.importType";
			$commentcategory = "http://schemas.google.com/blogger/2008/kind#comment";
			foreach($xml->entry as $entry)
			{
				if( strstr($entry->asXML(), $postcategory) AND !strstr($entry->asXML(), $postimport) )
				{
					new_post($entry);
				}
				else if( strstr($entry->asXML(), $commentcategory) )
				{
					new_comment($entry);
				}
			}
		}
	}
}

?>