<div class="wrap bspp_wrapper">
    <h1 class="wp-heading-inline">Edit Product</h1>
    <form id="cancel_subscription" method="post" class="">
        <div class="">
            <input type="text" class="" required name="reason">
        </div>
        <?php wp_nonce_field('cancel.subscription') ?>
        <input type="hidden" name="action" value="cancel.subscription"/>
        <button type="button" class="button button-primary">Cancel Subscription</button>
    </form>
</div>
<script>
jQuery(function($){
    $('#cancel_subscription').on('submit', function(){
        $.ajax({
            url: '<?php echo admin_url() ?>/admin-ajax.php'
            data: $(this).serialize(),
            type: 'post'
        }).done(function(res){
            console.log(res)
        })
    });
});
</script>