jQuery(document).ready(function($){
    $('input[name="wp-media-folders-options[auto_detect_tables]"]').change(function(element)
    {
        if (this.checked) {
            $('#table_replace').hide();
            $('#full_search').show();
        } else {
            $('#table_replace').show();
            $('#full_search').hide();
        }
    });

    $('#sync_wpmf').click(function(e){
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data : {
                action: 'wpmfs_import_wpmf',
                nonce: wpmfs_nonce
            }
        });
        $(this).attr('disabled', 'disabled');
        $('#sync_wpmf_doing').show();
    });

    $('#wpmfs-hide-disclaimer').click(function(e){
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data : {
                action: 'wpmfs_disclaimer',
                nonce: wpmfs_nonce
            }
        });
        $('#wpmfs-disclaimer').hide();
    });
});