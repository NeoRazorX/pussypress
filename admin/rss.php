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
	<description>'.PUSSY_DESCRIPTION.'</description>
   <link>http://www.'.PUSSY_DOMAIN.'</link>'."\n");

      $i = 0;
      foreach($posts as $p)
      {
         if($i > 100)
            break;
         else
         {
            fwrite($file, '	<item>
      <title>'.$p->title.'</title>
      <description>'.$p->description().'</description>
      <link>http://www.'.PUSSY_DOMAIN.'/'.$p->link.'</link>
      <pubDate>'.Date('r', $p->published).'</pubDate>
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
	<subtitle>'.PUSSY_DESCRIPTION.'</subtitle>
	<link href="http://www.'.PUSSY_DOMAIN.'/feeds/posts/default" rel="self" />
	<link href="http://www.'.PUSSY_DOMAIN.'" />'."\n");

      $i = 0;
      foreach($posts as $p)
      {
         if($i > 100)
            break;
         else
         {
            fwrite($file, '	<entry>
      <published>'.Date('Y-m-d', $p->published).'</published>
      <updated>'.Date('Y-m-d', $p->updated).'</updated>
      <title type="text">'.$p->title.'</title>
      <link rel="replies" type="text/html" href="http://www.'.PUSSY_DOMAIN.'/'.$p->link.'#comment-form" title="comentar"/>
      <link rel="alternate" type="text/html" href="http://www.'.PUSSY_DOMAIN.'/'.$p->link.'"/>
      <link href="http://www.'.PUSSY_DOMAIN.'/'.$p->link.'">http://www.'.PUSSY_DOMAIN.'/'.$p->link.'</link>
      <summary type="text">'.$p->description().'</summary>
	</entry>'."\n");
            $i++;
         }
      }

      fwrite($file, '</feed>');
      fclose($file);
   }
}

function posts2sitemap(&$posts)
{
   $file = fopen('sitemap.xml', 'w');
   if($file)
   {
      fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>
      	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");

      $i = 0;
      foreach($posts as $p)
      {
         if($i > 100)
            break;
         else
         {
            fwrite($file, '<url>
            	<loc>http://www.'.PUSSY_DOMAIN.'/'.$p->link.'</loc>
            	<lastmod>'.Date('Y-m-d', $p->published).'</lastmod>
            	<changefreq>always</changefreq>
            	<priority>0.8</priority>
            	</url>'."\n");
            $i++;
         }
      }

      fwrite($file, '</urlset>');
      fclose($file);
   }
}

?>