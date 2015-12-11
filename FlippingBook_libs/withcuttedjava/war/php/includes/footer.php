<?php

// import com.fi.twentythings.Locale;

require('../../locale/locale.php');

function get_locale_footer_share() {
	// global $loc; 
	// echo $loc->getLOCALE_FOOTER_SHARE();
	$get_locale_footer_share="SHARE BOOK";
	return $get_locale_footer_share;
}

function get_locale_footer_curator() {
	// global $loc; 
	// echo $loc->getLOCALE_FOOTER_CURATOR();
	$get_locale_footer_curator="Published by the <a href='http://www.google.com/chrome?brand=CHJN'>Google Chrome</a> Team. &copy,2010 Google Inc. All Rights Reserved.";
	return $get_locale_footer_curator;
}

function get_locale_footer_fullscreen() {
	// global $loc; 
	// echo $loc->getLOCALE_FOOTER_FULLSCREEN();
	$get_locale_footer_fullscreen="FULL SCREEN";
	return $get_locale_footer_fullscreen;
}

function get_locale_footer_lights() {
	// global $loc; 
	// echo $loc->getLOCALE_FOOTER_LIGHTS();
	$get_locale_footer_lights="LIGHTS";
	return $get_locale_footer_lights;
}

function get_locale_footer_print() {
	// global $loc; 
	// echo $loc->getLOCALE_FOOTER_PRINT();
	$get_locale_footer_print="PRINT BOOK";
	return $get_locale_footer_print;
}

?>	
		<footer>		
			<span class="curator"><?php get_locale_footer_curator()?></span>
			<div class="right-side">
				<div class="divider"></div>
				<div class="sharing">
					<p><?php get_locale_footer_share()?></p>
					<input type="text" class="clipboard-notification" value="http://20thingsilearned.com/" readonly="readonly" />
					<ul>
						<li class="facebook"><a href="http://www.facebook.com/sharer.php?u=http://20thingsilearned.com&amp;t=20%20Things%20I%20learned%20About%20Browsers%20and%20the%20Web" title="Facebook"></a></li>
						<li class="twitter"><a href="http://twitter.com/share?original_referer=http://20thingsilearned.com&amp;text=20%20Things%20I%20learned%20About%20Browsers%20and%20the%20Web&amp;url=http://20thingsilearned.com" title="Twitter"></a></li>
						<li class="buzz"><a href="http://www.google.com/buzz/post?url=http://20thingsilearned.com&amp;imageurl=20%20Things%20I%20learned%20About%20Browsers%20and%20the%20Web" title="Buzz"></a></li>
						<li class="url"><a href="#"></a></li>
					</ul>
				</div>
				<div class="divider"></div>
				<div class="print">
					<a href="/en-US/all/print" target="_blank"><span class="icon"></span><?php get_locale_footer_print() ?></a>
				</div>
				<?php if(!is_basic()) : ?>
					<div class="divider"></div>
					<div class="lights-wrapper">
						<div class="lights">
							<a href="#"><span class="icon"><?php get_locale_footer_lights()?></span></a>
						</div>
					</div>
					<div class="divider"></div>
					<div class="fullscreen-wrapper" style="display: none;">
						<div class="fullscreen">
							<a href="#"><span class="icon"></span><?php get_locale_footer_fullscreen()?></a>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</footer>
		
		<?php if(is_basic()) : ?>
			
			<script type="text/javascript" src="/js/thirdparty/jquery.1.4.2.min.js"></script>
      <script type="text/javascript" src="/js/twentythings.locale.js"></script>
			<script type="text/javascript">
				$('img').each(function(){
					if( $(this).attr('data-src') ){
						$(this).attr( 'src', $(this).attr('data-src') );
					}
        });
        
        TT.locale.initialize();
			</script>
		
		<?php elseif(is_live()) : ?>
		
		<script type="text/javascript" src="/js/twentythings.min.js?v=<?php echo $versionNumber; ?>"></script>
		<script type="text/javascript">
			TT.initialize();
		</script>
		
		<?php else : ?>
		
		<script type="text/javascript" src="/js/thirdparty/jquery.1.4.2.min.js"></script>
		<script type="text/javascript" src="/js/thirdparty/jquery.json-2.2.min.js"></script>
		<script type="text/javascript" src="/js/thirdparty/jquery.translate-1.3.9.min.js"></script>
		<script type="text/javascript" src="/js/thirdparty/browserdetect.js"></script>
		<script type="text/javascript" src="/js/twentythings.js"></script>
		<script type="text/javascript" src="/js/twentythings.preloader.js"></script>
		<script type="text/javascript" src="/js/twentythings.history.js"></script>
		<script type="text/javascript" src="/js/twentythings.storage.js"></script>
		<script type="text/javascript" src="/js/twentythings.pageflip.js"></script>
		<script type="text/javascript" src="/js/twentythings.paperstack.js"></script>
		<script type="text/javascript" src="/js/twentythings.illustrations.js"></script>
		<script type="text/javascript" src="/js/twentythings.navigation.js"></script>
		<script type="text/javascript" src="/js/twentythings.cache.js"></script>
		<!--<script type="text/javascript" src="/js/twentythings.search.js"></script>-->
		<script type="text/javascript" src="/js/twentythings.chapternav.js"></script>
		<script type="text/javascript" src="/js/twentythings.sharing.js"></script>
		<script type="text/javascript" src="/js/twentythings.overlay.js"></script>
		<script type="text/javascript" src="/js/twentythings.tableofthings.js"></script>
		<script type="text/javascript" src="/js/twentythings.flipintro.js"></script>
		<script type="text/javascript" src="/js/twentythings.locale.js"></script>
		
		<script type="text/javascript">
			TT.initialize();
		</script>
		
		<?php endif; ?>			
		
	</body>
</html>