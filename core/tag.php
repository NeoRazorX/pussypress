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

require_once 'core/raintpl/rain.tpl.class.php';

function add2tags(&$tags, $tag)
{
	$found = FALSE;
	foreach($tags as $i => $value)
	{
		if($value[0] == $tag)
		{
			$tags[$i][1]++;
			$found = TRUE;
			break;
		}
	}

	if(!$found)
		$tags[] = array($tag, 1);
}

function sort_tags(&$tags)
{
	usort($tags, function($a, $b) {
		if($a[1] > $b[1])
			return -1;
		else if($a[1] < $b[1])
			return 1;
		else
			return 0;
	});

	$new_tags = array();
	$i = 0;
	foreach($tags as $t)
	{
		if($i < 30)
		{
			$new_tags[] = $t[0];
			$i++;
		}
		else
			break;
	}

	return $new_tags;
}

function tag2page($tag, &$all_posts)
{
	if( !file_exists('search') )
		mkdir('search');

	if( !file_exists('search/label') )
		mkdir('search/label');

	$file = fopen('search/label/'.$tag, 'w');
	if($file)
	{
		/// agrupamos los posts que tienen la etiqueta $tag
		$posts = array();
		foreach( array_reverse($all_posts) as $p)
		{
			if( in_array($tag, $p->keywords()) )
				$posts[] = $p;
		}

		/// configuramos rain.tpl
		raintpl::configure('base_url', NULL);
		raintpl::configure('path_replace', FALSE);
		raintpl::configure('tpl_dir', 'themes/'.PUSSY_THEME.'/');
		raintpl::configure('cache_dir', '/tmp/');
		$tpl = new RainTPL();
		$tpl->assign('pussy_title', PUSSY_TITLE);
		$tpl->assign('pussy_description', PUSSY_DESCRIPTION);
		$tpl->assign('pussy_theme', PUSSY_THEME);
		$tpl->assign('pussy_domain', PUSSY_DOMAIN);
		$tpl->assign('pussy_google_analytics', PUSSY_GOOGLE_ANALYTICS);
		$tpl->assign('here', 'index.html');
		$tpl->assign('tag', $tag);
		$tpl->assign('posts', $posts);
		fwrite($file, $tpl->draw('tag.html', true) );
		fclose($file);
   }
}

?>