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

function banned_ip(&$ips)
{
	$banned = FALSE;

	if( file_exists('/tmp/pussy_ip.log') )
   {
   	$file = fopen('/tmp/pussy_ip.log', 'r');
   	if($file)
      {
      	while( !feof($file) )
         {
         	$line = explode(';', trim(fgets($file)));

         	if( intval($line[2]) > time() )
            {
            	if($line[0] == $_SERVER['REMOTE_ADDR'] AND intval($line[1]) > 3)
            		$banned = TRUE;

            	$ips[] = $line;
            }
         }

         fclose($file);
      }
   }

   return $banned;
}

function ban_ip(&$ips)
{
	$file = fopen('/tmp/pussy_ip.log', 'w');
	if($file)
   {
   	$found = FALSE;

   	foreach($ips as $ip)
      {
      	if($ip[0] == $_SERVER['REMOTE_ADDR'])
         {
         	fwrite( $file, $ip[0].';'.( 1+intval($ip[1]) ).';'.( time()+3600 ) );
         	$found = TRUE;
         }
         else
         	fwrite( $file, join(';', $ip) );
      }

      if(!$found)
      	fwrite( $file, $_SERVER['REMOTE_ADDR'].';1;'.( time()+3600 ) );

      fclose($file);
   }
}

?>