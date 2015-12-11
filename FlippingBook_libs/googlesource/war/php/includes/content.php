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

import com.fi.twentythings.Locale;

require('../../locale/locale.php');


function print_print_path() {
	global $loc;	
	if(strrpos($_SERVER['REQUEST_URI'],$loc->getId())>0) {
		echo '/'.$loc->getId().'/all/print';		
	} else {
		echo '../'.$loc->getId().'/all/print';		
	}	
}

function print_locale_overlay_pdf_description() {
	global $loc;
	echo $loc->getLOCALE_OVERLAY_PDF_DESCRIPTION();			
}

function print_locale_overlay_pdf_title() {
	global $loc;
	echo $loc->getLOCALE_OVERLAY_PDF_TITLE();			
}

function print_locale_overlay_print_description() {
	global $loc;
	echo $loc->getLOCALE_OVERLAY_PRINT_DESCRIPTION();			
}

function print_locale_overlay_print_title() {
	global $loc;
	echo $loc->getLOCALE_OVERLAY_PRINT_TITLE();			
}

function print_locale_overlay_resume_yes() {
	global $loc;
	echo $loc->getLOCALE_OVERLAY_RESUME_YES();			
}

function print_locale_overlay_resume_no() {
	global $loc;
	echo $loc->getLOCALE_OVERLAY_RESUME_NO();			
}

function print_locale_overlay_resume_description() {
	global $loc;
	echo $loc->getLOCALE_OVERLAY_RESUME_DESCRIPTION();			
}

function print_locale_overlay_resume_title() {
	global $loc;
	echo $loc->getLOCALE_OVERLAY_RESUME_TITLE();			
}

function print_locale_overlay_close() {
	global $loc;
	echo $loc->getLOCALE_OVERLAY_CLOSE();			
}


function print_twenty_things_label() {
	global $loc;
	echo $loc->getLOCALE_TWENTY_THINGS_LABEL();			
}

function print_illustration_label() {
	global $loc;
	echo $loc->getLOCALE_ILLUSTRATION_LABEL();			
}

function print_writers_editors_label() {
	global $loc;
	echo $loc->getLOCALE_WRITERS_EDITORS_LABEL();			
}

function print_project_curator_label() {
	global $loc;
	echo $loc->getLOCALE_PROJECT_CURATOR_LABEL();			
}

function print_design_label() {
	global $loc;
	echo $loc->getLOCALE_DESIGN_LABEL();			
}

function print_development_label() {
	global $loc;
	echo $loc->getLOCALE_DEVELOPMENT_LABEL();			
}

function print_special_thanks_label() {
	global $loc;
	echo $loc->getLOCALE_SPECIAL_THANKS_LABEL();			
}

function print_html5_label() {
	global $loc;
	echo $loc->getLOCALE_BUILT_IN_HTML5_LABEL();			
}

function print_share_this_book_label() {
	global $loc;
	echo $loc->getLOCALE_SHARE_THIS_BOOK_LABEL();			
}

function print_share_on_label() {
	global $loc;
	echo $loc->getLOCALE_SHARE_ON_LABEL();			
}


			
?>
			
			</div>
			<div id="left-page">
				<img src="<?php echo IMAGE_ASSETS['left-page'] ?>" data-src-flipped="<?php echo IMAGE_ASSETS['left-page-flipped'] ?>" width="830" height="520">
			</div>
			<div id="right-page">
				<div id="paperstack">
					<div class="paper s1"></div>
					<div class="paper s2"></div>
					<div class="paper s3"></div>
					<div class="paper s4"></div>
					<div class="paper s5"></div>
					<div class="paper s6"></div>
					<div class="paper s7"></div>
					<div class="shadow"></div>
				</div>
				<img src="<?php echo IMAGE_ASSETS['right-page'] ?>" width="830" height="520">
			</div>
		</div>
		
		<nav id="chapter-nav">
			<p><?php print_twenty_things_label()?></p>
			<ul>
				<?php 
					$chapterCounter = 1;
					
					foreach ( $pages as $key => $value ) {
						if( !$value['hidden'] ) {
							$dataList  = ' data-title="'.$value['title'].'"';
							$dataList .= ' data-subtitle="'.str_replace( '"', "'", $value['subtitle'] ).'"';
							$dataList .= ' data-article="'.$key.'"';
							$dataList .= ' data-globalstartpage="'.$value['globalStartPage'].'"';
							$dataList .= ' data-globalendpage="'.$value['globalEndPage'].'"';
							$dataList .= ' data-numberofpages="'.$value['numberOfPages'].'"';
							
							$cnClass = $value['active'] ? $key : 'disabled '.$key;
							$cnTitle = $value['title'];
							$cnLink = '/' . $cnClass;
							
							if( $value['globalStartPage'] == $value['globalEndPage'] ) {
								$cnPages = ''.$value['globalStartPage'];
							}
							else {
								$cnPages = ''.$value['globalStartPage'].'-'.$value['globalEndPage'];
							}
							
							?>
							
				<li class="<?php echo $cnClass; ?>">
					<a href="<?php echo $cnLink; ?>" class="cnItem" title="<?php echo $cnTitle; ?>" <?php echo $dataList; ?>>
						<div class="illustration"></div>
						<span><?php echo $chapterCounter; ?></span>
					</a>
					<a class="over" href="<?php echo $cnLink; ?>">
						<div class="description">
							<p class="title"><?php echo $cnTitle; ?></p>
							<p class="pagenumber"><?php echo $cnPages; ?></p>
						</div>
						<div class="small-book">
							<div class="illustration"></div>
							<p class="index"><?php echo $chapterCounter; ?></p>
						</div>
					</a>
				</li>
				
							<?php
							
							$chapterCounter++;
						}
					}
		?>

			</ul>
		</nav>
		
		<div id="overlay">
			<div class="bookmark">
				<div class="content">
					<a class="close" href="#"><?php print_locale_overlay_close() ?></a>
					<h3><?php print_locale_overlay_resume_title() ?></h3>
					<p><?php print_locale_overlay_resume_description() ?></p>
					<a class="action resume" href="#"><?php print_locale_overlay_resume_yes() ?></a>
					<a class="action restart" href="#"><?php print_locale_overlay_resume_no() ?></a>
				</div>
			</div>
			
			<div class="print">
					<a class="close" href="#"><?php echo print_locale_overlay_close(); ?></a>
					<a class="printBook" href="<?php print_print_path() ?>"_blank">
						<h2><?php print_locale_overlay_print_title() ?></h2>
						<p><?php print_locale_overlay_print_description(); ?></p>
					</a>
					<a class="downloadPdf" target="_blank" href="/media/20ThingsILearnedaboutBrowsersandtheWeb.pdf">
						<h2><?php print_locale_overlay_pdf_title() ?></h2>
						<p><?php print_locale_overlay_pdf_description() ?></p>
					</a>
			</div>
		</div>
		
		<div id="credits">
			<div class="header">
				<h2><span><?php print_locale_menu_credits() ?></span></h2>
				<hr>
			</div>
			<div class="people">
				<ul>
					<li><h3><?php print_illustration_label()?></h3><a href="http://www.christophniemann.com/" target="_blank">Christoph Niemann</a></li>
					<li><h3><?php print_writers_editors_label()?></h3>Min Li Chan, Fritz Holznagel, Michael Krantz</li>
					<li><h3><?php print_project_curator_label()?></h3>Min Li Chan &amp; <br/>The Google Chrome Team</li>
					<li><h3><?php print_design_label()?></h3><a href="http://f-i.com" target="_blank">Fi</a><br/><a href="http://monocubed.com/" target="_blank">Paul Truong</a></li>
					<li><h3><?php print_development_label()?></h3><a href="http://f-i.com" target="_blank">Fi</a></li>
				</ul>
				<h4><?php print_special_thanks_label()?></h4>
				<p class="special-thanks">Brian Rakowski, Ian Fette, Chris DiBona, Alex Russell, Erik Kay, Jim Roskind, Mike Belshe, Dimitri Glazkov, Henry Bridge, Gregor Hochmuth, Jeffrey Chang, Mark Larson, Aaron Boodman, Wieland Holfelder, Jochen Eisinger, Bernhard Bauer, Adam Barth, Cory Ferreria, Erik Arvidsson, John Abd-Malek, Carlos Pizano, Justin Schuh, Wan-Teh Chang, Vangelis Kokkevis, Mike Jazayeri, Brad Chen, Darin Fisher, Dudley Carr, Richard Rabbat, Ji Lee, Glen Murphy, Valdean Klump, Aaron Koblin, Paul Irish, John Fu, Chris Wright, Sarah Nahm, Christos Apartoglou, Meredith Papp, Eric Antonow, Eitan Bencuya, Jay Nancarrow, Ben Lee, Gina Weakley, Linus Upson, Sundar Pichai & The Google Chrome Team</p>
				<p class="html5-logo"><?php print_html5_label()?><img src="/media/images/HTML5_Badge_32.png" title="Built in HTML5"></img></p>
			</div>
			<hr>
			<div class="share">
				<p><?php print_share_this_book_label()?></p>
				<ul>
					<li class="facebook"><a href="http://www.facebook.com/sharer.php?u=http://20thingsilearned.com&amp;t=20%20Things%20I%20learned%20About%20Browsers%20and%20the%20Web" title="Facebook"><span class="icon"></span><span class="text"><?php print_share_on_label()?><br/>FACEBOOK</span></a></li>
					<li class="twitter"><a href="http://twitter.com/share?original_referer=http://20thingsilearned.com&amp;text=20%20Things%20I%20learned%20About%20Browsers%20and%20the%20Web&amp;url=http://20thingsilearned.com" title="Twitter"><span class="icon"></span><span class="text"><?php print_share_on_label()?><br/>TWITTER</span></a></li>
					<li class="buzz"><a href="http://www.google.com/buzz/post?url=http://20thingsilearned.com&amp;imageurl=20%20Things%20I%20learned%20About%20Browsers%20and%20the%20Web" title="Buzz"><span class="icon"></span><span class="text"><?php print_share_on_label()?><br/>BUZZ</span></a></li>
				</ul>
			</div>
		</div>
		
		