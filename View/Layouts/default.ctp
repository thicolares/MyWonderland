<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> -->
<?php print $this->Facebook->html(); ?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo Configure::read('SiteTitle'); ?> | <?php echo $title_for_layout; ?></title>
    <?php
        echo $this->Html->css(array(
            'reset',
            '960',
			'bootstrap.min',
			//'profile',
            'default',
			//             'thickbox',
			// '/ui-themes/smoothness/jquery-ui',
			// 'reset_all_inside'
        ));

        echo $this->Layout->js();
        echo $this->Html->script(array(
			            'jquery/jquery.min',
			//             'jquery/jquery-ui.min',
			//             'jquery/jquery.slug',
			//             'jquery/jquery.uuid',
			//             'jquery/jquery.cookie',
			//             'jquery/jquery.hoverIntent.minified',
			//             'jquery/superfish',
			//             'jquery/supersubs',
			//             'jquery/jquery.tipsy',
			//             'jquery/jquery.elastic-1.6.1.js',
			//             'jquery/thickbox-compressed',
			//             'admin',
			// // TinyMCE
			// 'tiny_mce/tiny_mce.js',
			// 'tiny_mce_buttons.js',
        ));

        echo $scripts_for_layout;
    ?>
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-12359215-17']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</head>

	<body>

	    <div class="container">
		
			<div class="navbar">
			  <div class="navbar-inner">
			    <div class="container">
					<a class="brand" href="/">
					  <?php print Configure::read('SiteTitle'); ?>
					</a>
			      	<ul class="nav">

						<li><a href="/">Home</a></li>
					  <li><a href="#">About</a></li>

					</ul>
			    </div>
			  </div>
			</div>

            <?php
                echo $this->Layout->sessionFlash();
               echo $content_for_layout;
            ?>
			
	      <footer>
	        <p>&copy; Company 2012</p>
	      </footer>
	
	    </div> <!-- /container -->
	
    </div>
    <?php //echo $this->element('footer'); ?>
<?php echo $this->Facebook->init(); ?>
    </body>
</html>
