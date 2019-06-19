<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>Foodhold</title>
<!--WordPress function that loads all scripts and styles into the header-->
<?php wp_head(); ?>
</head>

<!-- assigns specific class to the body element with respect to the page-->
<body <?php body_class(); ?>>

<!-- Create the navigation menu according to the code provided by Bootstrap-->
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom" role="navigation">
        <a class="navbar-brand" href="<?php echo home_url();?>">
            <img src="https://www.wur.nl/upload/44ae26d7-86b1-478d-9ab4-aa3d759b6f58_logo_WUR_W_internet.png">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"
                aria-controls="bs-example-navbar-collapse-1" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <?php
        //add the WordPress menu using the WordPress function and custom code from Pattonwebz (Pattonwebz, 2018)
    wp_nav_menu( array(
        'theme_location'  => 'primary',
        'depth'           => 2,
        'container'       => 'div',
        'container_class' => 'collapse navbar-collapse',
        'container_id'    => 'bs-example-navbar-collapse-1',
        'menu_class'      => 'nav navbar-nav',
        'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
        'walker'          => new WP_Bootstrap_Navwalker(),
    ) );
        ?>
        </div>
    </nav>
</div>