<?php

/**
 * Copyright 2011 Google Inc.
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */ 

/**
 * Check logged in state
 * 
 */
if( !isset($_COOKIE['f46f703eb202ff76ce8700e6b6b0e678']) || $_COOKIE['f46f703eb202ff76ce8700e6b6b0e678'] !== '3ad6ada94da82d1729d9c826d417b16b' ) {
	$query = isset($_SERVER['QUERY_STRING']) ? '?' . urlencode($_SERVER['QUERY_STRING']) : '';
	 header('Location: /login?ref=' . $_SERVER['SCRIPT_URL'] . $query);
}

?>