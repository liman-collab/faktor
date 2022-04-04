<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>

</div><!-- .col-full -->
</div><!-- #content -->

<?php do_action( 'storefront_before_footer' ); ?>

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="col-full">
        <div class="footer-l">
            <?php
            /**
             * Functions hooked in to storefront_footer action
             *
             * @hooked storefront_footer_widgets - 10
             * @hooked storefront_credit         - 20
             */
            do_action( 'storefront_footer' );
            ?>
            <div class="site-info">
                <?php
                $path = $_SERVER['REQUEST_URI'];
                ?>
                <?php if(is_front_page() || strpos($path, 'buecher') > -1 ) : ?>
                    <div class="fp">
                        Alle Preise verstehen sich inklusive Mehrwertsteuer und zzgl. Versandkosten
                        <!--                    <a href="#" class="link">Versandkoste</a>-->
                    </div>
                <?php endif; ?>
                <div class="footer-menu links">
                    <?php wp_nav_menu(['menu'=>'footer_menu']) ?>
                </div>
            </div><!-- .site-info -->
        </div>
        <div class="footer-r">
            <h4>
                Newsletter
            </h4>
            <p>
                <b>Bleiben Sie auf dem Laufenden rund um
                    Architektur, Technik und Energie.</b>
            </p>
            <div class="input-btn newsletter">
                <?php echo do_shortcode('[wpens_easy_newsletter firstname="no" lastname="no" button_text="Abonnieren"]'); ?>

                <!-- <form action="">
                     <input type="text" placeholder="Ihre E-Mail">
                     <button type="submit">
                         abonnieren
                     </button>
                 </form>-->
            </div>
        </div>

    </div><!-- .col-full -->
    <div class="col-full">
        <div class="copyright">
            <?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; '. date( 'Y' ) .' - Faktor Verlag' ) ); ?>
            <span class="link-splitter">|</span>
            Powered by <a href="https://bexeo.com" class="link" target="_blank">Bexeo GmbH</a>
        </div></div>
</footer><!-- #colophon -->

<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>
<script src="<?php echo get_template_directory_uri(); ?>/main.js"></script>
</body>
</html>
