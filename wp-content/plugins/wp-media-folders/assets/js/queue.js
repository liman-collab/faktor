jQuery(document).ready(function($){
    checkQueue = function() {
        $.ajax({
            url : wpmf_ajaxurl,
            type : 'POST',
            data : {
                action: 'wpmfs_queue',
            },
            beforeSend : function(){
                $('#wp-admin-bar-wpmfs-topbar span.wpmfs').addClass('wpmfs-querying');
            },
            success : function(data){
                data = JSON.parse(data);

                $('#wp-admin-bar-wpmfs-topbar span.wpmfs').removeClass('wpmfs-orange wpmfs-green');
                if (data.queue_length > 0) {
                    $('#wp-admin-bar-wpmfs-topbar span.wpmfs').addClass('wpmfs-orange');
                } else {
                    $('#wp-admin-bar-wpmfs-topbar span.wpmfs').addClass('wpmfs-green');
                }
                $('.wpmfs-queue').html(data.queue_length);
                $('.wpmfs-queue').html(data.queue_length);
                $('#wp-admin-bar-wpmfs-topbar').attr('title', data.title);
            },
            complete: function(){
                $('#wp-admin-bar-wpmfs-topbar span.wpmfs').removeClass('wpmfs-querying');
            }
        });
    };
    setTimeout(checkQueue, 1000)
    setInterval(checkQueue, 10 * 1000);

    // Initialize for check queue click
    $('#wp-admin-bar-wpmfs-topbar a').click(function(e){
        e.preventDefault();
        checkQueue();
    });
});