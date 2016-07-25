
            </main>
            <footer class="site-footer" id="footer">
                <div class="container">
                    <div class="row">
                        <?php if ( is_active_sidebar( 'footer01_sidebar' ) ) : ?>
                            <div class="col-sm-3">
                                <?php dynamic_sidebar( 'footer01_sidebar' ); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( is_active_sidebar( 'footer02_sidebar' ) ) : ?>
                            <div class="col-sm-3">
                                <?php dynamic_sidebar( 'footer02_sidebar' ); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( is_active_sidebar( 'footer03_sidebar' ) ) : ?>
                            <div class="col-sm-3">
                                <?php dynamic_sidebar( 'footer03_sidebar' ); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( is_active_sidebar( 'footer04_sidebar' ) ) : ?>
                            <div class="col-sm-3">
                                <?php dynamic_sidebar( 'footer04_sidebar' ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </footer>
        </div>
        <!-- Bootstrap core JavaScript
    ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <?php wp_footer(); ?>
    </body>
</html>
