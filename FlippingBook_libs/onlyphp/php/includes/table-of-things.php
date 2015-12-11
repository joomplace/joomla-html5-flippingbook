<?php

 

require_once('/locale/locale.php');

function print_locale_toc_title() {
	// global $loc;
	// echo $loc->getLOCALE_TOC_TITLE();
	echo "Table of Things";
			
}

function print_locale_toc_back() {
	// global $loc;
	// echo $loc->getLOCALE_TOC_BACK();
	echo "Go Back";
	
}

function print_locale_thing() {
	// global $loc;
	// echo $loc->getLOCALE_SHARER_LABEL1();
	echo "THING";
			
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