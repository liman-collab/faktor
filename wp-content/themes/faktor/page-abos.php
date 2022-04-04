<?php
    get_header();
    $abos = get_post(get_the_ID());
?>

<h1><?php the_title(); ?></h1>

<?php if( get_field('beschreibung') ): ?>
    <h2><?php the_field('beschreibung'); ?></h2>
<?php endif; ?>

<div class="abos">
    <?php $image = get_field('bild'); ?>
    <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
</div>


<div class="page-mediadaten">

    <div class="page-grid">
        <div class="grid-item description">
            <img src="<?php
            echo get_field('bild', $id)['url'];
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
