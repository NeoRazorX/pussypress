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

class post
{
	public $file;
	public $link;
	public $title;
	public $published;
	public $updated;
	public $keywords;
	private $array_keywords;
	public $body;
	public $next_link;
	public $previous_link;
	public $comments;

	public function __construct($filename=false)
	{
		$aux = explode('/', substr($filename, 0, -4).'html');

		$this->title = 'Hola mundo';
		$this->description = '';
		$this->published = time();
		$this->updated = time();
		$this->keywords = '';
		$this->body = 'Sin texto.';
		$this->next_link = '/';
		$this->previous_link = '/';
		$this->comments = array();

		if($filename)
		{
			$this->file = $filename;
			$this->link = substr($filename, 0, -4).'html';

			/// leemos el archivo .post
			if( file_exists($filename) )
			{
				$file = fopen($filename, 'r');
				if($file)
				{
					$read_post = false;

					while( !feof($file) )
					{
						$lineb = fgets($file, 1024);
						$line = trim($lineb);

						if( substr($line, 0, 7) == 'title: ' )
							$this->title = substr($line, 7);
						else if( substr($line, 0, 11) == 'published: ' )
							$this->published = intval( substr($line, 11) );
						else if( substr($line, 0, 9) == 'updated: ' )
							$this->updated = intval( substr($line, 9) );
						else if( substr($line, 0, 10) == 'keywords: ' )
							$this->keywords = substr($line, 10);
						else if( $line == '----------' )
						{
							$read_post = true;
							$this->body = '';
						}
						else if($read_post)
							$this->body .= $lineb;
					}

					fclose($file);
				}
			}

			/// leemos el archivo .comments
			if( file_exists( substr($filename, 0, -4).'comments' ) )
			{
				$file = fopen( substr($filename, 0, -4).'comments', 'r');
				if($file)
				{
					$new_comment = true;
					$read_txt = false;

					while( !feof($file) )
					{
						if($new_comment)
						{
							$comment = array(
								'author' => 'anonymous',
								'published' => Date('d-m-Y'),
								'updated' => Date('d-m-Y'),
								'txt' => ''
							);
							$new_comment = false;
						}

						$lineb = fgets($file, 1024);
						$line = trim($lineb);

						if( substr($line, 0, 8) == 'author: ' )
							$comment['author'] = substr($line, 8);
						else if( substr($line, 0, 11) == 'published: ' )
							$comment['published'] = Date('d-m-Y', intval( substr($line, 11) ) );
						else if( substr($line, 0, 9) == 'updated: ' )
							$comment['updated'] = Date('d-m-Y', intval( substr($line, 9) ) );
						else if( $line == '----------' )
							$read_txt = true;
						else if( $line == '*****END*COMMENT*****' )
						{
							$this->comments[] = $comment;
							$new_comment = true;
							$read_txt = false;
						}
						else if($read_txt)
							$comment['txt'] .= $lineb;
					}

					fclose($file);
				}
			}
		}
		else
		{
			$this->file = Date('Y/m', $this->published).'/'.$this->sanitize($this->title).'-'.mt_rand(0, 999).'.post';
			$this->link = substr($this->file, 0, -4).'html';
			$this->body = 'Este es un post de ejemplo generado automáticamente.';
		}
	}

	public function description()
	{
		$description = strip_tags( stripslashes($this->body) );
		return $this->true_text_break($description, 150);
	}

	public function published()
	{
		return Date('d-m-Y', $this->published);
	}

	public function updated()
	{
		return Date('d-m-Y', $this->updated);
	}

	public function keywords()
	{
		if( !isset($this->array_keywords) )
		{
			$this->array_keywords = array();

			foreach( explode(',', $this->keywords) as $key )
			{
				$tag = $this->sanitize($key);
				if($tag != '')
					$this->array_keywords[] = $tag;
			}
		}

		return $this->array_keywords;
	}

	private function sanitize($str)
	{
		$str = trim( strtolower($str) );
		$changes = array('/à/' => 'a', '/á/' => 'a', '/â/' => 'a', '/ã/' => 'a', '/ä/' => 'a',
			 '/å/' => 'a', '/æ/' => 'ae', '/ç/' => 'c', '/è/' => 'e', '/é/' => 'e', '/ê/' => 'e',
			 '/ë/' => 'e', '/ì/' => 'i', '/í/' => 'i', '/î/' => 'i', '/ï/' => 'i', '/ð/' => 'd',
			 '/ñ/' => 'n', '/ò/' => 'o', '/ó/' => 'o', '/ô/' => 'o', '/õ/' => 'o', '/ö/' => 'o',
			 '/ő/' => 'o', '/ø/' => 'o', '/ù/' => 'u', '/ú/' => 'u', '/û/' => 'u', '/ü/' => 'u',
			 '/ű/' => 'u', '/ý/' => 'y', '/þ/' => 'th', '/ÿ/' => 'y', '/ñ/' => 'ny',
			 '/&quot;/' => '-'
		);
		$str = preg_replace(array_keys($changes), $changes, $str);
		$str = preg_replace('/[^a-z0-9]/i', '-', $str);
		$str = preg_replace('/-+/', '-', $str);
		
		return $str;
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

	public function new_url($url, $from='index.html')
	{
		$nurl = '';
		for($i = 1; $i < count( explode('/', $from) ); $i++)
			$nurl .= '../';
		return $nurl.$url;
	}

	public function edit_url($from='index.html')
	{
		$nurl = '';
		for($i = 1; $i < count( explode('/', $from) ); $i++)
			$nurl .= '../';
		return $nurl.'admin/index.php?edit='.urlencode($this->file);
	}

	public function tweet_url()
	{
		return 'https://twitter.com/share?url='.urlencode('http://'.PUSSY_DOMAIN.'/'.$this->link).
					'&amp;text='.urlencode($this->title);
	}

	public function plusone_url()
	{
		return 'https://plus.google.com/share?url='.urlencode('http://'.PUSSY_DOMAIN.'/'.$this->link);
	}

	public function compile($filename)
	{
		$file = fopen($filename, 'w');
		if($file)
		{
			$tpl = new RainTPL();
			$tpl->assign('pussy_title', PUSSY_TITLE);
			$tpl->assign('pussy_description', PUSSY_DESCRIPTION);
			$tpl->assign('pussy_theme', PUSSY_THEME);
			$tpl->assign('pussy_domain', PUSSY_DOMAIN);
			$tpl->assign('pussy_google_analytics', PUSSY_GOOGLE_ANALYTICS);
			$tpl->assign('pussy_disqus', PUSSY_DISQUS);
			$tpl->assign('pussy_twitter', PUSSY_TWITTER);
			$tpl->assign('pussy_github', PUSSY_GITHUB);
			$tpl->assign('post', $this);
			$tpl->assign('here', $filename);
			fwrite($file, $tpl->draw('post.html', true) );
			fclose($file);
		}
	}

	/*
	 * Esta función devuelve una copia del string $str al que se le ha
	 * añadido un caracter de espacio vacío &#8203; cada $max_width
	 * caracteres a cada palabra de más de $max_width caracteres.
	 * Las caracteres escapados de HTML se cuentan como uno solo.
	 */
	public function true_word_break($str, $max_width=30)
	{
		/// Eliminamos cualquier rastro de &#8203;
		$str = str_replace('&#8203;', '', $str);
		
		$chrArray = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
		$pos0 = 0;
		$width = 0;
		while( $pos0 < count($chrArray) )
		{
			$char = $chrArray[$pos0];
			
			if($char == ' ')
				$width = 0;
			else if($char == '&')
			{
				/// nos saltamos los elementos escapados
				for($pos1 = $pos0 + 1; $pos1 < min( array($pos0 + 9, count($chrArray)) ) ; $pos1++)
				{
					if( $chrArray[$pos1] == ';' )
					{
						$pos0 = $pos1;
						break;
					}
				}
				$width++;
			}
			else if($width >= $max_width)
			{
				$chrArray[$pos0] = '&#8203;' . $chrArray[$pos0];
				$width = 0;
			}
			else
				$width++;
			
			$pos0++;
		}
		
		$str = '';
		foreach($chrArray as $ch)
			$str .= $ch;
		
		return $str;
	}
	
	/*
	 * Esta función devuelve una copia de $str con una longitud máxima de
	 * $max_t_width caracteres, pero sin cortar la última palabra.
	 * 
	 * Además añade un caracter de espacio vacío cada $max_w_width
	 * caracteres usando la functión fs_model::true_word_break().
	 * 
	 * Las elementos HTML son escapados mediante fs_model::no_html().
	 */
	public function true_text_break($str, $max_t_width=500, $max_w_width=30)
	{
		$desc = $this->true_word_break( $this->no_html($str), $max_w_width );
		
		if( mb_strlen($desc) <= $max_t_width )
			return $desc;
		else
		{
			$description = '';
			
			foreach(explode(' ', $desc) as $aux)
			{
				if( mb_strlen($description.' '.$aux) < $max_t_width-3 )
				{
					if($description == '')
						$description = $aux;
					else
						$description .= ' ' . $aux;
				}
				else
					break;
			}
			
			return $description.'...';
		}
	}

	/*
	 * Esta función convierte:
	 * < en &lt;
	 * > en &gt;
	 * " en &quot;
	 * ' en &#39;
	 * 
	 * Además elimina los espacios extra.
	 * 
	 * No tengas la tentación de sustiturla por htmlentities o htmlspecialshars
	 * porque te encontrarás con muchas sorpresas desagradables.
	 */
	public function no_html($t)
	{
		$newt = trim( preg_replace('/\s+/', ' ', $t) );
		$newt = str_replace('<', '&lt;', $newt);
		$newt = str_replace('>', '&gt;', $newt);
		$newt = str_replace('"', '&quot;', $newt);
		$newt = str_replace("&nbsp;", ' ', $newt);
		return str_replace("'", '&#39;', $newt);
	}

	public function save()
	{
		/*
		 * Si no existe el archivo, es porque era un boceto, así que buscamos un
		 * nombre de archivo más acorde.
		 */
		if( !file_exists($this->file) )
		{
			$this->file = Date('Y/m', $this->published).'/'.$this->sanitize($this->title).'-'.mt_rand(0, 999).'.post';
			$this->link = substr($this->file, 0, -4).'html';
		}

		if( !file_exists( $this->year() ) )
			mkdir( $this->year() );

		if( !file_exists( $this->year().'/'.$this->month() ) )
			mkdir( $this->year().'/'.$this->month() );

		$file = fopen($this->file, 'w');
		if($file)
		{
			$this->updated = time();

			fwrite($file, 'title: '.$this->title."\n");
			fwrite($file, 'published: '.$this->published."\n");
			fwrite($file, 'updated: '.$this->updated."\n");
			fwrite($file, 'keywords: '.$this->keywords."\n");
			fwrite($file, "----------\n".$this->body);
			fclose($file);
		}
	}

	public function delete()
	{
		if( file_exists($this->file) )
			unlink($this->file);

		if( file_exists( substr($this->file, 0, -4).'comments') )
			unlink( substr($this->file, 0, -4).'comments');

		if( file_exists($this->link) )
			unlink($this->link);
	}
}

?>