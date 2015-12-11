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
 
	$printLocale = $_GET['locale'] ? ' &rarr; <a class="breadcrumb" href="/cmseditlocale?locale=' . $_GET['locale'] . '">' . $_GET['locale'] . '</a>' : '';
	$printArticle = $_GET['article'] ? ' &rarr; <a class="breadcrumb" href="/cmseditarticle?locale=' . $_GET['locale'] . '&article=' . $_GET['article'] .'">' . $_GET['article'] . '</a>' : '';
	$printPage = $_GET['pagenumber'] ? ' &rarr; <a class="breadcrumb" href="/cmseditpage?locale=' . $_GET['locale'] . '&article=' . $_GET['article'] . '&pagenumber=' . $_GET['pagenumber'] .'">' . $_GET['pagenumber'] . '</a>' : '';
?>

<p id="nav">
	<a class="home" href="/cmshome">20 Things CMS</a> &rarr;
	<a class="breadcrumb" href="/cmshome">locales</a><?php echo $printLocale; ?><?php echo $printArticle; ?><?php echo $printPage; ?>
	<a id="logout" href="/login?logout=true">Log Out</a>
</p>
