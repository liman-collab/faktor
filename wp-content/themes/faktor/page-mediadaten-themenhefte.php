<?php
    get_header();
    $page = get_post(get_the_ID());
?>
<div class="page-mediadaten">
    <div class="page-grid">
        <div class="grid-item description">
            <?php echo $page->post_content ?>
        </div>
        <div class="grid-item description">
            <h3>Adressaten</h3>
            <?php
                echo get_field('adressaten', $id);
            ?>
        </div>
    </div>
    <div class="page-grid">
        <div class="grid-item description">
            <img src="<?php
            echo get_field('mediadaten-image', $id)['url'];
            ?>" alt="">
        </div>
        <div class="grid-item format description">
            <?php
            echo get_field('formate_und_anlieferung', $id);
            ?>
        </div>
    </div>
</div>
<?php get_footer() ?>
