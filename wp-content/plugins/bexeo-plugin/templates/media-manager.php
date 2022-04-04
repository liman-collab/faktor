<?php
if (isset($_GET['folder'])) {
    $product_id = $_GET['folder'];
    $product = new WC_product($product_id);
    $attachment_ids = $product->get_gallery_image_ids();
} else {
    $args = array(
        'post_type' => 'product'
    );

    $loop = new WP_Query($args);
}
?>
    <h1>
        Media Manager
        <?php
            if(isset($_GET['folder'])){
                echo ' - ' . $product->get_name();
            }
        ?>
    </h1>
<?php
    if(isset($_GET['folder'])){
        echo '<a href="/wp-admin/admin.php?page=manage_media" class="bexeo-folders-back">
            <i class="fas fa-long-arrow-alt-left"></i>
        </a>';
    }
?>
<?php if (isset($_GET['folder'])) : ?>
    <div class="bexeo-folder-images">
        <div>
            <?php $mainImg = wp_get_attachment_url($product->get_image_id()) ?>
            <a href="<?php echo $mainImg ?>" target="_blank">
                <div class="bexeo-folder-image">
                    <img class="bexeo-folder-image-item" src="<?php echo $mainImg?>" alt="">
                    <p class="bexeo-file-name">
                        <?php echo basename($mainImg); ?>
                    </p>
                </div>
            </a>
        </div>
        <?php foreach ($attachment_ids as $attachment_id) : ?>
            <?php
            $file = wp_get_attachment_url($attachment_id);
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            ?>
            <div>
                <?php if ($ext == 'pdf') : ?>
                    <a href="<?php echo $file ?>" target="_blank">
                        <div class="bexeo-folder-image">
                        <i class="fas fa-file-pdf bexeo-folder-image-item"></i>
                        <p class="bexeo-file-name">
                            <?php echo basename($file); ?>
                        </p>
                    </div>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $file ?>" target="_blank">
                        <div class="bexeo-folder-image">
                        <img class="bexeo-folder-image-item" src="<?php echo wp_get_attachment_url($attachment_id) ?>" alt="">
                        <p class="bexeo-file-name">
                            <?php echo basename($file); ?>
                        </p>
                    </div>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <div class="bexeo-folders">
        <?php while ($loop->have_posts()) : $loop->the_post(); ?>
            <?php global $product; ?>

            <div class="bexeo-folder" data-pid="<?php echo $product->get_id() ?>">
                <img class="bexeo-folder-img" src="/wp-content/plugins/bexeo-plugin/assets/images/folder.png" alt="">
                <p>
                    <?php echo $product->get_name(); ?>
                </p>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>