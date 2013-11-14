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

/// La zona horaria del blof
date_default_timezone_set('Europe/Madrid');

/// El título del blog
define('PUSSY_TITLE', 'NeoRazorX');

/// La descripción del blog
define('PUSSY_DESCRIPTION', "My new static blog. Why? Because fuck you, that's why!");

/// El aspecto seleccionado. Tiene que estar en la carpeta themes,
define('PUSSY_THEME', 'default');

/// El dominio del blog
define('PUSSY_DOMAIN', 'neorazorx.com');

/// ¿Usas google analytics? Escribe el identificador aquí.
define('PUSSY_GOOGLE_ANALYTICS', '');

/*
 * ¿Quieres usar la plataforma disqus para gestionar los comentarios?
 * Escribe aquí el shortname.
 */
define('PUSSY_DISQUS', 'neorazorx');

/// ¿Tienes twitter?
define('PUSSY_TWITTER', 'neorazorx');

/// ¿Tienes github?
define('PUSSY_GITHUB', 'neorazorx');

?>