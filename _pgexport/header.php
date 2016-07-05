<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Pinegrow Web Editor">
        <!-- Bootstrap core CSS -->
        <!-- Custom styles for this template -->
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        <?php wp_head(); ?>
    </head>
    <body class="<?php echo implode(' ', get_body_class()); ?>">
        <div class="site-container">
            <header id="masthead" class="site-header">
                <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
                    <div class="container-fluid">
                        <div class="navbar-header site-branding">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                                <span class="sr-only"><?php _e( 'Toggle navigation', 'sb2016' ); ?></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <?php if ( ! has_custom_logo() ) : ?>
                                <a class="navbar-brand site-title" href="<?php echo esc_url( get_home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
                            <?php else : ?>
                                <?php pg_starter_the_custom_logo() ?>
                            <?php endif; ?>
                        </div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <?php wp_nav_menu( array(
                                    'menu_class' => 'nav navbar-nav navbar-right',
                                    'container' => '',
                                    'depth' => '2',
                                    'theme_location' => 'primary',
                                    'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
                                    'walker' => new wp_bootstrap_navwalker()
                            ) ); ?>
                        </div>
                    </div>
                </nav>
                <?php
                    $image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
                    if( !$image_attributes  && ( $header_image = get_header_image() ) ) $image_attributes = array( $header_image );
                ?>
                <div class="jumbotron jumbo-bkg" style="<?php if($image_attributes) echo 'background-image:url(\''.$image_attributes[0].'\')' ?>;color:<?php echo '#'.get_header_textcolor() ?>;">
                    <div class="container dimmer jumbotron-inner">
                        <h1><?php bloginfo( 'name' ); ?></h1>
                        <p><?php bloginfo( 'description' ); ?></p>
                        <p><a class="btn btn-primary btn-lg btn-outline" role="button" href="#more" style="color:<?php echo '#'.get_header_textcolor() ?>;border-color:<?php echo '#'.get_header_textcolor() ?>;"><?php _e( 'Read more', 'sb2016' ); ?></a></p>
                    </div>
                </div>
                <div class="container-fluid breadcrumbs-section" id="more">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="breadcrumbs-cnt">
                                <?php get_template_part( 'assets/breadcrumbs/breadcrumb' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ( is_singular() ) : ?>
                    <?php wp_enqueue_script( 'comment-reply' ); ?>
                <?php endif; ?>
            </header>
            <main class="site-inner site-content">