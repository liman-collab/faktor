<?php get_header() ?>
<?php
$post = get_post();
$id = $post->ID;
$detailFields = get_field('produktedetails', $id);
$infoFields = get_field('info', $id);
$leseprobeFields = get_field('leseprobe_links', $id);
?>
<div class="single-abo single-product storefront-full-width-content" data-abo="<?php echo $post->post_title ?>">
    <div id="content" class="site-content" tabindex="-1">
        <div class="col-full" style="padding: 0 !important;">

            <div class="woocommerce"></div>
            <div id="primary" class="content-area">
                <main id="main" class="site-main" role="main">

                    <div class="woocommerce-notices-wrapper"></div>
                    <div class="product type-product status-publish first instock product_cat-themenhefte product_tag-architektur product_tag-energieeffizienz product_tag-nachhaltig-bauen has-post-thumbnail taxable shipping-taxable purchasable product-type-simple">

                        <div class="woocommerce-product-gallery woocommerce-product-gallery--with-images woocommerce-product-gallery--columns-5 images" data-columns="5" style="opacity: 1; transition: opacity 0.25s ease-in-out 0s;">
                            <figure class="woocommerce-product-gallery__wrapper">
                                <div data-thumb="<?php echo $detailFields['bild']['url'] ?>" data-thumb-alt="" class="woocommerce-product-gallery__image"><a href="<?php echo $detailFields['bild']['url'] ?>"><img src="<?php echo $detailFields['bild']['url'] ?>" class="wp-post-image" alt="" loading="lazy" title="<?php echo $detailFields['bild']['title'] ?>" data-caption="" data-src="<?php echo $detailFields['bild']['url'] ?>" data-large_image="<?php echo $detailFields['bild']['url'] ?>" data-large_image_width="800" data-large_image_height="1048"></a></div>	</figure>
                        </div>

                        <div class="summary entry-summary">
                            <h1 class="product_title entry-title" id="aboTitle">
                                <?php echo $detailFields['produktname'] ?>
                            </h1>

                            <p class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">Fr.</span>&nbsp;<span id="aboPrice" data-price="<?php echo $detailFields['preis'] ?>"><?php echo $detailFields['preis'] ?></span></bdi></span> <small class="woocommerce-price-suffix">Inkl. MwSt.</small></p>
                            <p><?php echo $infoFields ?></p>
                            <p class="abo-price-desc">Preis gilt nur für Lieferungen in die Schweiz und nach Liechtenstein. Bitte beachten Sie die Informationen zu Bestellungen aus dem Ausland.</p>
<!--                            <p class="product-amount-label"><strong>Menge</strong></p>-->
<!--                            <div class="quantity">-->
<!--                                <label class="screen-reader-text" for="quantity_61de96fb65b56">Themenheft 55: Kreislauf Menge</label>-->
<!--                                <input type="number" id="quantity_61de96fb65b56" class="input-text qty text" step="1" min="1" max="" name="quantity" value="1" title="Menge" size="4" placeholder="" inputmode="numeric">-->
<!--                            </div>-->

                            <div class="product-accordion">
                                <div class="acc-openers">
                                    <?php if(!empty(trim($detailFields['beschreibung']))) : ?>
                                        <a href="javascript:void(0)" class="link acc-open" data-target="#productDesc">Beschreibung</a>
                                    <?php endif; ?>
                                    <?php if($leseprobeFields['leseprobe_pdf']) : ?>
                                        <a href="javascript:void(0)" class="acc-open" data-target="#productLeseprobe">Leseprobe</a>
                                    <?php endif; ?>
                                    <?php if(!empty(trim($detailFields['orders_from_abroad']))) : ?>
                                        <a href="javascript:void(0)" class="acc-open" data-target="#productAbroadOrders">Bestellungen aus dem Ausland</a>
                                    <?php endif; ?>
                                </div>
                                <div class="acc-content">
                                    <?php if(!empty(trim($detailFields['beschreibung']))) : ?>
                                        <div id="productDesc">
                                            <div>
                                                <?php echo $detailFields['beschreibung'] ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($leseprobeFields['leseprobe_pdf']) : ?>
                                        <div id="productLeseprobe">
                                            <div class="lp-overlay-parent">
                                                <img src="<?php echo $leseprobeFields['leseprobe_image']['url'] ?>" alt="<?php echo $leseprobeFields['leseprobe_image']['title'] ?>">
                                                <a class="lp-link-overlay" href="<?php echo $leseprobeFields['leseprobe_pdf']['url'] ?>" target="_blank">
                                                    <span class="dashicons dashicons-visibility"></span>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty(trim($detailFields['orders_from_abroad']))) : ?>
                                        <div id="productAbroadOrders">
                                            <?php echo $detailFields['orders_from_abroad'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <br/>
                            <div class="abo-contact-form">
                            <?php echo $detailFields['formular'] ?>
                            </div>
                            <!--                        <div class="edit-link"><a class="post-edit-link" href="http://faktor.local/wp-admin/post.php?post=663&amp;action=edit">Bearbeiten <span class="screen-reader-text">Themenheft 55: Kreislauf</span></a></div>	</div>-->
                        </div>
                </main><!-- #main -->
            </div><!-- #primary -->
        </div><!-- .col-full -->
    </div>
</div>
<?php get_footer() ?>
