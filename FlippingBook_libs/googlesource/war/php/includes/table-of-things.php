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

require('../../locale/locale.php');

function print_locale_toc_title() {
	global $loc;
	echo $loc->getLOCALE_TOC_TITLE();				
}

function print_locale_toc_back() {
	global $loc;
	echo $loc->getLOCALE_TOC_BACK();				
}

function print_locale_thing() {
	global $loc;
	echo $loc->getLOCALE_SHARER_LABEL1();				
}

?>
	
		<div id="table-of-contents">
			<div class="center">
				<div class="header">
					<a class="go-back" href="/"><?php print_locale_toc_back() ?></a>
					<h2><span><?php print_locale_toc_title() ?></span></h2>
					<hr>
				</div>
				<ul>
				<?php 
					$chapterCounter = 1;
					
					foreach ( $pages as $key => $value ) {
						if( $value['hidden']!='1') {
							$totIndex = $chapterCounter;
							$totArticle = $key;
							$totTitle = $value['title'];
							$totSubtitle = str_replace( '"', "'", $value['subtitle'] );
							$totActive = $value['active'];
							$totClass = $totActive ? $totArticle : 'disabled '.$totArticle;					
				?>					
							<li class="<?php echo $totClass; ?>">
								<a href="<?php echo '/' . $key ?>" data-article="<?php echo $totArticle; ?>">
									<div class="medium-book">
										<div class="illustration"></div>
										<p><?php echo print_locale_thing(); ?> <?php echo $totIndex; ?></p>
									</div>
									<h3><?php echo $totTitle; ?></h3>
									<p><?php echo $totSubtitle; ?></p>
								</a>
							</li>
				<?php
						$chapterCounter++;
						}
					}
				?>
				</ul>
			</div>
		</div>