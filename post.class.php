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

require 'config.php';
require 'raintpl/rain.tpl.class.php';

class post
{
	public $file;
	public $link;
	public $title;
	public $published;
	public $updated;
	public $keywords;
	public $body;
	public $next_link;
	public $previous_link;
	public $comments;

	public function __construct($filename)
	{
		$aux = explode('/', substr($filename, 0, -4).'html');

		$this->file = $filename;
		$this->link = substr($filename, 0, -4).'html';
		$this->title = 'Sin título';
		$this->description = '';
		$this->published = time();
		$this->updated = time();
		$this->keywords = '';
		$this->body = '';
		$this->next_link = '/';
		$this->previous_link = '/';
		$this->comments = array();

		$file = fopen($filename, 'r');
		if($file)
		{
			$read_post = false;

			while( !feof($file) )
			{
				$line = trim( fgets($file, 1024) );

				if( substr($line, 0, 7) == 'title: ' )
					$this->title = substr($line, 7);
      	   else if( substr($line, 0, 11) == 'published: ' )
      	   	$this->published = intval( substr($line, 11) );
	         else if( substr($line, 0, 9) == 'updated: ' )
	         	$this->updated = intval( substr($line, 9) );
      	   else if( substr($line, 0, 10) == 'keywords: ' )
      	   	$this->keywords = substr($line, 10);
				else if( $line == '----------' )
					$read_post = true;
      	   else if($read_post)
      	   	$this->body .= $line;
			}

			fclose($file);
   	}
	}

	public function description()
	{
		$description = strip_tags( stripslashes($this->body) );

		if( mb_strlen($description) > 150 )
			return mb_substr($description, 0, 150).'...';
		else
			return $description;
	}

	public function year()
	{
		$aux = explode('/', $this->link);
		return $aux[0];
	}

	public function month()
	{
		$aux = explode('/', $this->link);
		return $aux[1];
	}

	public function root($from='index.html')
	{
		$url = '';
		for($i = 1; $i < count( explode('/', $from) ); $i++)
			$url .= '../';
		return $url.'index.html';
	}

	public function link($from='index.html')
	{
		$url = '';
		for($i = 1; $i < count( explode('/', $from) ); $i++)
			$url .= '../';
		return $url.$this->link;
	}

	public function previous($from='index.html')
	{
		$url = '';
		for($i=1; $i < count(explode('/', $from)); $i++)
			$url .= '../';
		return $url.$this->previous_link;
	}

	public function next($from='index.html')
	{
		$url = '';
		for($i=1; $i < count(explode('/', $from)); $i++)
			$url .= '../';
		return $url.$this->next_link;
	}

	public function compile($filename)
	{
		$file = fopen($filename, 'w');
		if($file)
		{
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
			$tpl->assign('pussy_disqus', PUSSY_DISQUS);
			$tpl->assign('post', $this);
			$tpl->assign('here', $filename);
			fwrite($file, $tpl->draw('post', true) );
			fclose($file);
   	}
	}
}

?>