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

function post2root($post, $tags)
{
	$file = @fopen('index.html', 'w');
	if($file)
	{
		$tpl = new RainTPL();
		$tpl->assign('pussy_title', PUSSY_TITLE);
		$tpl->assign('pussy_description', PUSSY_DESCRIPTION);
		$tpl->assign('pussy_theme', PUSSY_THEME);
		$tpl->assign('pussy_domain', PUSSY_DOMAIN);
		$tpl->assign('pussy_google_analytics', PUSSY_GOOGLE_ANALYTICS);
		$tpl->assign('pussy_twitter', PUSSY_TWITTER);
		$tpl->assign('pussy_github', PUSSY_GITHUB);
		$tpl->assign('post', $post);
		$tpl->assign('tags', $tags);
		$tpl->assign('here', 'index.html');
		fwrite($file, $tpl->draw('root.html', true) );
		fclose($file);
   }
}

?>