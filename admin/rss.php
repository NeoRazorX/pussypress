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

function posts2rss(&$posts)
{
   if( !file_exists('feeds') )
      mkdir('feeds');

   if( !file_exists('feeds/posts') )
      mkdir('feeds/posts');

   $file = fopen('feeds/posts/rss', 'w');
   if($file)
   {
      fwrite($file, '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
  <title>'.PUSSY_TITLE.'</title>
  <link>http://www.'.PUSSY_DOMAIN.'</link>
  <description>'.PUSSY_DESCRIPTION.'</description>'."\n");

      $i = 0;
      foreach( array_reverse($posts) as $p)
      {
         if($i > 100)
            break;
         else
         {
            fwrite($file, '<item>
      <title>'.$p->title.'</title>
      <link>http://www.'.PUSSY_DOMAIN.'/'.$p->link.'</link>
      <description>'.$p->description().'</description>
      </item>'."\n");
            $i++;
         }
      }

      fwrite($file, '</channel></rss>');
      fclose($file);
   }
}

function posts2atom(&$posts)
{
	if( !file_exists('feeds') )
      mkdir('feeds');

   if( !file_exists('feeds/posts') )
      mkdir('feeds/posts');

   $file = fopen('feeds/posts/default', 'w');
   if($file)
   {
      fwrite($file, '<?xml version="1.0" encoding="UTF-8" ?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title>'.PUSSY_TITLE.'</title>
  <link href="http://www.'.PUSSY_DOMAIN.'"/>
  <description>'.PUSSY_DESCRIPTION.'</description>'."\n");

      $i = 0;
      foreach( array_reverse($posts) as $p)
      {
         if($i > 100)
            break;
         else
         {
            fwrite($file, '<entry>
      <title type="text">'.$p->title.'</title>
      <link href="http://www.'.PUSSY_DOMAIN.'/'.$p->link.'"/>
      <summary type="text">'.$p->description().'</summary>
      </entry>'."\n");
            $i++;
         }
      }

      fwrite($file, '</feed>');
      fclose($file);
   }
}

?>