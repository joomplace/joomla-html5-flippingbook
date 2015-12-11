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

function get_locale_footer_share() {
	global $loc; 
	echo $loc->getLOCALE_FOOTER_SHARE();
}

function get_locale_footer_curator() {
	global $loc; 
	echo $loc->getLOCALE_FOOTER_CURATOR();
}

function get_locale_footer_fullscreen() {
	global $loc; 
	echo $loc->getLOCALE_FOOTER_FULLSCREEN();
}

function get_locale_footer_lights() {
	global $loc; 
	echo $loc->getLOCALE_FOOTER_LIGHTS();
}

function get_locale_footer_print() {
	global $loc; 
	echo $loc->getLOCALE_FOOTER_PRINT();
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
					<a href="/<?php echo $_GET['language']; ?>/all/print" target="_blank"><span class="icon"></span><?php get_locale_footer_print() ?></a>
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
		<script type="text/javascript" src="/js/twentythings.search.js"></script>
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