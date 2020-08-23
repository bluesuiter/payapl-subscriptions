<div class="wrap bspp_wrapper">
    <h1 class="wp-heading-inline">Suspend Subscription</h1>
    <form id="suspend_subscription" method="post" class="">
        <label class="">
            Reason for susupension:
            <input type="text" class="" required name="reason">
        </label>
        <?php wp_nonce_field('suspend.subscription') ?>
        <input type="hidden" name="action" value="suspend.subscription"/>
        <button type="button" class="button button-primary">Suspend Subscription</button>
    </form>
</div>
<script>
jQuery(function($){
    $('#suspend_subscription').on('submit', function(){
        $.ajax({
            url: '<?php echo admin_url() ?>/admin-ajax.php',
            data: $(this).serialize(),
            type: 'post',
        }).done(function(res){
            console.log(res)
        })
    });
});
</script>