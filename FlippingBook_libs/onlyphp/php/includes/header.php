<!DOCTYPE html>
<?php 

  
  // PHP Imports.
  require_once('/locale/locale.php');
  require_once('/locale/locale-base-configuration.php');
  
  // Globals.
  $cacheManifest = '';
  $versionNumber = 0;
  
  // Use cache.manifest in production only.
  // if( is_live() ) {
  //   $cacheManifest = 'manifest="/' . $_GET['language'] . '/cache.manifest?v=' . $versionNumber . '"';
  // }
  
  // Data fetchers.
  function get_locale_meta_description() {
    // global $loc;
    // return $loc->getLOCALE_META_DESCRIPTION();
    echo "Things you always wanted to know about the web but were afraid to ask. Learn about the web & browsers in this interactive experience created by Google & illustrated by Christoph Niemann.";
    
  }
  
  function get_locale_facebook_message() {
    // global $loc;
    // return $loc->getLOCALE_FACEBOOK_MESSAGE();
    echo "A fun guidebook from Google on things you've always wanted to know about browsers & the web (but were afraid to ask";
    
  }
  
  function get_locale_facebook_message_single() {
    // global $loc;
    // return $loc->getLOCALE_FACEBOOK_MESSAGE_SINGLE();
  echo "A fun fact I learned today from Google's guidebook to browsers and the web.";
  
  }
  
  function print_locale_meta_description() {
    // global $loc;
    // echo $loc->getLOCALE_META_DESCRIPTION();
    echo "Things you always wanted to know about the web but were afraid to ask. Learn about the web & browsers in this interactive experience created by Google & illustrated by Christoph Niemann.";
    
  }
  
  function print_locale_facebook_message() {
    // global $loc;
    // echo $loc->getLOCALE_FACEBOOK_MESSAGE();
    echo "A fun guidebook from Google on things you've always wanted to know about browsers & the web (but were afraid to ask";
     
  }
  
  function print_locale_facebook_message_single() {
    // global $loc;
    // echo $loc->getLOCALE_FACEBOOK_MESSAGE_SINGLE();
    echo "A fun fact I learned today from Google's guidebook to browsers and the web.";
   
  }
  
  function print_locale_twitter_message() {
    // global $loc;
    // echo $loc->getLOCALE_TWITTER_MESSAGE();
    echo "A fun guidebook from Google on things you've always wanted to know about browsers and the web:";
   
  }
  
  function print_locale_twitter_message_single() {
    // global $loc;
    // echo $loc->getLOCALE_TWITTER_MESSAGE_SINGLE();
    echo "A fun fact I learned today from Google's guidebook to browsers and the web:";
  } 
  
  function print_locale_buzz_message() {
    // global $loc;
    // echo $loc->getLOCALE_BUZZ_MESSAGE();
    echo "A fun guidebook from Google on things you've always wanted to know about browsers & the web (but were afraid to ask): http://goo.gl/20things";
    
  }
  
  function print_locale_buzz_message_single() {
    // global $loc;
    // echo $loc->getLOCALE_BUZZ_MESSAGE_SINGLE();
    echo "A fun fact I learned today from Google's guidebook to browsers and the web:";
   
  }
  
  function print_locale_front_cover_cta() {
    // global $loc;
    // echo $loc->getLOCALE_FRONT_COVER_CTA();
    echo "OPEN BOOK";
    
  }
  
  function print_locale_front_cover_intro() {
    // global $loc;
    // echo $loc->getLOCALE_FRONT_COVER_INTRO();
    echo "What’s a cookie? How do I protect myself on the web? And most importantly: What happens if a truck runs over my laptop? For things you’ve always wanted to know about the web but were afraid to ask, read on.";
   
  }
  
  function print_locale_search_invalid() {
    // global $loc;
    // echo $loc->getLOCALE_SEARCH_INVALID();
    echo "No results found.";
    
  }
  
  function print_locale_search_results_pages() {
    // global $loc;
    // echo $loc->getLOCALE_SEARCH_RESULTS_PAGES();
    echo "KEYWORDS";
    
  }
  
  function print_locale_search_placeholder() {
    // global $loc;
    // echo $loc->getLOCALE_SEARCH_PLACEHOLDER();
    echo "Search Book";
   
  }
  
  function print_locale_select_language() {
    // global $loc;
    // echo $loc->getLOCALE_SELECT_LANGUAGE();
   echo "Change Language";
    
  }
  
  function print_locale_menu_tot() {
    // global $loc;
    // echo $loc->getLOCALE_MENU_TOT();
    echo "TABLE OF THINGS";
  
  }
  
  function print_locale_not_supported_ie() {
    // global $loc;
    // echo $loc->getLOCALE_NOT_SUPPORTED_IE();
    echo "This illustrated book was designed for HTML5-compliant browsers and will not work with your current browser. For the best viewing experience, please download a modern browser, or install the Google Chrome Frame plug-in and try launching this site again.";
   
  }
  
  function print_locale_not_supported() {
    // global $loc;
    // echo $loc->getLOCALE_NOT_SUPPORTED();
    echo "This illustrated book was designed for HTML5-compliant browsers and will not work with your current browser. For the best viewing experience, please upgrade to the latest version of your browser or download a modern browser and try launching this site again.";
    
  }
  
  function print_locale_title() {
    // global $loc;
    // echo $loc->getLOCALE_TITLE();
    echo "20 Things I Learned About Browsers and the Web";
  }
  
  function print_locale_sharing_image() {
    // global $loc;
    // echo $loc->getLOCALE_SHARING_IMAGE();
    echo "http://www.20thingsilearned.com/css/images/front-cover.jpg";
  }

  function print_locale_meta_author() {
    // global $loc;
    // echo $loc->getLOCALE_META_AUTHOR();
    echo "Google, inc.";
 
  }
  
  function print_locale_meta_keywords() {
    // global $loc;
    // echo $loc->getLOCALE_META_KEYWORDS();
    echo "browsers, web, google, cookies, cloud computing, html5 book, web apps, javascript, phishing, malware, internet, online security, online safety, web apps, web applications, html, html5, plugins, browser extensions, online privacy, open source, christoph niemann, what is the web, what is the internet";
    
  }
  
  function print_locale_sharer_label_one() {
    // global $loc;
    // echo $loc->getLOCALE_SHARER_LABEL1();
    echo "THING";
 
  }
  
  function print_locale_sharer_label_two() {
    // global $loc;
    // echo $loc->getLOCALE_SHARER_LABEL2();
    echo "SHARE THING";
    
  }
  
  function print_locale_page_label() {
    // global $loc;
    // echo $loc->getLOCALE_PAGE_LABEL();
    echo "";
    
  }
  
  function print_locale_pages_label() {
    // global $loc;
    // echo $loc->getLOCALE_PAGES_LABEL();
    echo "";
    
  }
  
  function print_locale_menu_foreword() {
    // global $loc;
    // echo $loc->getLOCALE_MENU_FORWARD();
    echo "FOREWORD";
    
  }
  
  function print_locale_menu_credits() {
    // global $loc;
    // echo $loc->getLOCALE_MENU_CREDITS();
    echo "CREDITS";
    
  }
  
  function print_thing() {
    // global $loc;
    // echo $loc->getLOCALE_PRINT_THING_LABEL();
   echo "NULL";
    
  }
  
  function print_compressed_css() {
    global $versionNumber;
    echo '<link type="text/css" href="/css/twentythings.min.css?v='.$versionNumber.'" rel="stylesheet" media="screen" />';
    echo '<link type="text/css" href="/css/print.css?v='.$versionNumber.'" rel="stylesheet" media="print" />';
  }
  
  function print_all_css() {
    global $versionNumber;
    echo '<link type="text/css" href="/css/reset.css?v='.$versionNumber.'" rel="stylesheet" media="screen" />' . "\n";
    echo '<link type="text/css" href="/css/main.css?v='.$versionNumber.'" rel="stylesheet" media="screen" />' . "\n";
    echo '<link type="text/css" href="/css/print.css?v='.$versionNumber.'" rel="stylesheet" media="print" />' . "\n";
    echo '<link type="text/css" href="/css/layouts.css?v='.$versionNumber.'" rel="stylesheet" media="screen" />' . "\n";
    echo '<link type="text/css" href="/css/illustrations.css?v='.$versionNumber.'" rel="stylesheet" media="screen" />' . "\n";
  }

  // Meta data.
  $metaDescription = "";
  $facebookDescription = "";  
  foreach ( $pages as $key => $value ) {
    if( $key == $currentArticle ) {    
      $metaDescription = $value['title'] . " (" . $value['subtitle'] . ")";
      $facebookDescription = "";
    }
  }
  
  // Clean up quotation marks in meta.
  $metaDescription = str_replace( '"', "'", $metaDescription );

?>
<html lang="en">
  <head>
  <!-- 
    20 Things I Learned About Browsers and the Web
    Built by Fi (www.f-i.com) for the Google Chrome Team.
    
    @author Hakim El Hattab
    @author Erik Kallevig
    @author Jon Gray
    -->
    
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="keywords" content="">
  <meta name="author" content="">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width = 1000">
  <meta name="description" content="">
  
  <?php if(is_chromeframe()) : ?>
    <!-- Adds support for the Chrome Frame IE plugin -->
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
  <?php endif; ?>
    
  <?php if(is_facebook()) : ?>
    <!-- Specify image and description for Facebook sharing -->
    <meta property="og:description" content="">
    <meta property="og:image" content="">
    <meta name="medium" content="image">
  <?php endif; ?>
    
  <link rel="image_src" href="<?php print_locale_sharing_image()?>" />
  
  <title><?php print_locale_title()?></title>
  
  <!-- <?php if(is_live()) {print_compressed_css();} else {print_all_css();} ?> -->
  <?php print_all_css(); ?>
  
  <?php if(is_basic()) : ?>

    <link type="text/css" href="/css/basic.css" rel="stylesheet" media="screen" />
    
    <!--[if IE 6]>
    <link type="text/css" href="/css/ie6.css" rel="stylesheet" media="screen" />
    <![endif]-->
    <!--[if lte IE 8]>
    <script src="/js/twentythings.html5shiv.js" type="text/javascript"></script>
    <![endif]-->

    <script type="text/javascript"> 
      if( window.location.hash.match('\/') ) {
        window.location = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + window.location.hash.slice(1);
      }
    </script>
    
  <?php else : ?>
  
    <script type="text/javascript"> 
      document.write('<link rel="stylesheet" type="text/css" media="all" href="/css/hideOnLoad.css" />');
      
      if( window.location.hash.match('\/') ) {
        window.location = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + window.location.hash.slice(1);
      }

      var SERVER_VARIABLES = {
        PAGE: "<?php print_locale_page_label(); ?>",
        PAGES:  "<?php print_locale_pages_label(); ?>",
        THING:  "<?php print_locale_sharer_label_one(); ?>",
        FOREWORD:  "<?php print_locale_menu_foreword(); ?>",
        LANG: <?php echo '"' . 'en-US' . '"'; ?>,
        SITE_VERSION: <?php echo '0'; ?>,
        FACEBOOK_MESSAGE: "<?php print_locale_facebook_message(); ?>",
        FACEBOOK_MESSAGE_SINGLE: "<?php echo print_locale_facebook_message_single(); ?>",
        TWITTER_MESSAGE: "<?php print_locale_twitter_message(); ?>",
        TWITTER_MESSAGE_SINGLE: "<?php print_locale_twitter_message_single(); ?>",
        BUZZ_MESSAGE: "<?php print_locale_buzz_message(); ?>",
        BUZZ_MESSAGE_SINGLE: "<?php print_locale_buzz_message_single(); ?>",
        SOLID_BOOK_COLOR: "<?php echo '#5873a0"' //SOLID_BOOK_COLOR ; ?>
      };
    </script>

  <?php endif; ?>


</head>
<body class="<?php echo 'home en-US'; ?>">

  <?php include_once("analyticstracking.php"); ?>
  
  <?php if(is_basic()) : // Show upgrade message. ?>
    <div id="upgrade">
      <p>
        <?php BROWSER_NAME == Browser::BROWSER_IE ? print_locale_not_supported_ie() : print_locale_not_supported() ?>
      </p>
    </div>
  <?php endif; ?>
  
  <div id="preloader">
    <div class="contents">
      <canvas class="animation"></canvas>
      <div class="progress">
        <div class="fill"></div>
      </div>
    </div>
  </div>
  
  <header>
    <h1><a class="logo" <?php echo 'logo-style' ?> href="/"><?php print_locale_title()?></a></h1>
    <nav>
      <ul>
        <li class="table-of-things"><a href="/table-of-things"><?php print_locale_menu_tot(); ?></a></li>
        <li class="divider1"></li>
        <li class="about"><a href="/foreword/1"><?php print_locale_menu_foreword() ?></a></li>
        <li class="divider2"></li>
        <li class="credits"><a href="/credits"><?php print_locale_menu_credits() ?></a></li>
        <li class="divider2"></li>
      </ul>
    </nav>
    
    <div id="language-selector">
      <div id="language-selector-title"><a>
      <?php 
        // $localedisplayvalues = get_display_locales();
        // foreach($localedisplayvalues as $key => $value ) {
        //   $codeandname = explode('|', $value);
        //   $dataLocaleCode = $codeandname[1];
        //   $dataLocaleName = $codeandname[0];
        //   if ($dataLocaleCode == $_GET['language']) {
        //     echo $dataLocaleName;
        //     break;
        //   }
        // }
        echo "en-US";
      ?>
      </a></div>
     
      // <div id="language-selector-list">
      //   <ul>
      //     <?php 
      //       $pagePath = $_SERVER["REQUEST_URI"];
      //       //echo $pagePath;
      //       $pagePath = preg_replace( '/\?(.*)/gi', '', $pagePath );
      //       //echo '1st mod to pagepath = '.$pagePath;
            
      //       $pos = strrpos($pagePath, 'fil-PH');
      //       //echo 'pos of locale = '.$pos;
            
      //       $specialcase = false;
            
      //       if ($pos == true) {
      //         $pagePath = preg_replace( '/(fil-PH)//gi', '', $pagePath );
      //         //echo 'fil-PH mod to pagepath = '.$pagePath;
      //         $specialcase = true;
      //       }
            
      //       $pos = strrpos($pagePath, 'es-419');
      //       if ($pos == true) {
      //         $pagePath = preg_replace( '/(es-419)//gi', '', $pagePath );
      //         //echo 'es-419 mod to pagepath = '.$pagePath;
      //         $specialcase = true;
      //       }
            
      //       if(!$specialcase == true) {
      //         $pagePath = preg_replace( '/(..\-..)//gi', '', $pagePath );
      //       }
            
      //       // $localedisplayvalues = get_display_locales();
            
      //       // foreach($localedisplayvalues as $key => $value ) {
      //       //   $codeandname = explode('|', $value);
      //       //   $dataLocaleCode = $codeandname[1];
      //       //   $dataLocaleName = $codeandname[0];
      //       //   $dataLocaleURL = "/" . $dataLocaleCode . $pagePath;
              
      //       //   echo "<li data-locale=\"$dataLocaleCode\">";
      //       //   echo "<a href=\"$dataLocaleURL\">$dataLocaleName</a>";
      //       //   echo "</li>";
      //       //}
      //       $dataLocaleCode="en-US";
      //       $dataLocaleName="English (US)";
      //       $dataLocaleURL="/en-US/en-US";
      //       echo "<li data-locale=\"$dataLocaleCode\">";
      //       echo "<a href=\"$dataLocaleURL\">$dataLocaleName</a>";
      //       echo "</li>";
      //     ?>
      //   </ul>
      // </div>
    </div>
    
    <!-- Input type="search" is currently too inconsistent across browsers and platforms -->
    <input id="search-field" type="text" value="<?php print_locale_search_placeholder(); ?>">
  </header>
  
  <!-- Holds search results -->
  <div id="search-dropdown">
    <div class="fader">
      <div class="background-top"></div>
      <div class="background-bottom"></div>
      <div class="results">
        <div class="things">
          <h4><span><?php print_locale_search_placeholder(); ?></span></h4>
          <hr>
        </div>
        <div class="keywords">
          <h4><span><?php print_locale_search_results_pages(); ?></span></h4>
          <hr>
        </div>
        <div class="empty"><?php print_locale_search_invalid(); ?></div>
      </div>
    </div>
  </div>
  
  <!-- Left side grey overlay that masks out the book -->
  <div id="grey-mask"></div>
  
  <div id="book">
    <div id="shadow">
      <div class="shadow-left"></div>
      <div class="shadow-right"></div>
    </div>
    <div id="spine">
      <div class="spine-top"></div>
      <div class="spine-bottom"></div>
    </div>
    <div id="front-cover-bookmark">
      <div class="content">
        <?php  print_locale_front_cover_intro() ?>
        <a href="<?php echo "/en-US/foreword" ?>" class="open-book"><?php print_locale_front_cover_cta(); ?></a>
        <canvas id="flip-intro"></canvas>
      </div>
    </div>
    <div id="sharer">
      <div class="background-top"></div>
      <div class="background-bottom"></div>
      <div class="content">
        <ul>
          <li class="facebook"><a href="#" title="Facebook"></a></li>
          <li class="twitter"><a href="#" title="Twitter"></a></li>
          <li class="buzz"><a href="#" title="Buzz"></a></li>
          <li class="print"><a href="#" target="_blank" title="<?php print_thing()?>"><?php print_thing()?></a></li>
        </ul>
        <p class="index"><?php print_locale_sharer_label_one() ?></p>
        <p class="instruction"><?php print_locale_sharer_label_two() ?></p>
      </div>
    </div>
    <div id="front-cover">
      <img src="<?php echo '/css/images/front-cover.jpg' ?>" width="830" height="520">
    </div>
    <div id="back-cover">
      <img src="<?php echo '/css/images/back-cover.jpg' ?>" data-src-flipped="<?php echo '/css/images/back-cover-flipped.jpg' ?>" width="830" height="520">
    </div>
    <div id="page-shadow-overlay"></div>
    <div id="pages">
