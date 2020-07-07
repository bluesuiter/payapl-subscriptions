<div class="wrap bspp_wrapper">
    <h3>PayPal Settings</h3>
    <form id="paypal_settings" action="" name="" class="">
        <fieldset>
            <button class="button button-primary alignright" id="ls_pp_save_settings">Save</button>
            <p>
                <label>Active enviornment:</label>
                <select id="env" name="paypal_env">
                    <option value="">Select Enviornment</option>
                    <option value="sandbox">Sandbox</option>
                    <option value="live">Live</option>
                </select>
            </p>
        </fieldset>
        <hr>
        <fieldset>
            <label class="col-1">Sandbox details:</label>
            <p class="col-3 alignleft"><label>E-Mail:</label> <input id="sb_auth_code" size="45" type="text" name="sb_auth_code"></p>
            <p class="col-3 alignleft"><label>Sandbox Id:</label> <input id="sandbox_id" size="45" type="text" name="sandbox_id"></p>
            <p class="col-3 alignright"><label>Sandbox Secret:</label> <input id="sandbox_secret" size="45" type="text" name="sandbox_secret"></p>
        </fieldset>
        <hr>
        <fieldset>
            <label class="col-1">Live details:</label>
            <p class="col-3 alignleft">
                <label>E-Mail:</label>
                <input id="lv_auth_code" size="45" type="text" name="lv_auth_code">
            </p>
            <p class="col-3 alignleft">
                <label>Live Id:</label>
                <input id="live_id" size="45" type="text" name="live_id">
            </p>
            <p class="col-3 alignright">
                <label>Live Secret:</label>
                <input id="live_secret" size="45" type="text" name="live_secret">
            </p>
        </fieldset>
        <hr>
        <fieldset>
            <p class="col-3">
                <label>Register page:</label>
                <select class="col-1" id="register_page" name="register_page"> 
                    <option value="">
                    <?php echo esc_attr( __( 'Select page' ) ); ?></option> 
                    <?php 
                        $pages = get_pages(); 
                        foreach ( $pages as $page ) {
                            $option = '<option value="' .  $page->ID . '">';
                            $option .= $page->post_title;
                            $option .= '</option>';
                            echo $option;
                        }
                    ?>
                </select>
            </p>
            <p class="col-3">
                <label>Success page:</label>
                <select class="col-1" id="success_page" name="success_page"> 
                    <option value="">
                    <?php echo esc_attr( __( 'Select page' ) ); ?></option> 
                    <?php 
                        $pages = get_pages(); 
                        foreach ( $pages as $page ) {
                            $option = '<option value="' .  $page->ID . '">';
                            $option .= $page->post_title;
                            $option .= '</option>';
                            echo $option;
                        }
                    ?>
                </select>
            </p>
            <p class="col-3">
                <label>Failure page:</label>
                <select class="col-1" id="failure_page" name="failure_page"> 
                    <option value="">
                    <?php echo esc_attr( __( 'Select page' ) ); ?></option> 
                    <?php 
                        $pages = get_pages(); 
                        foreach ( $pages as $page ) {
                            $option = '<option value="' .  $page->ID . '">';
                            $option .= $page->post_title;
                            $option .= '</option>';
                            echo $option;
                        }
                    ?>
                </select>
            </p>
        </fieldset>
        <?php wp_nonce_field('bspp_paypal_config_admin', '_bspp_settings_paypal_'); ?>
        <input type="hidden" name="action" value="bspp_paypal_config_admin">
    </form>
</div>
<script>
jQuery(function($){
    var data = JSON.parse('<?php echo json_encode($data) ?>');
    if(data != null && typeof data.env != undefined){
        $.each(data, function(k,v){
            $('#'+k).val(v);
        })
    }

    $('#ls_pp_save_settings').on('click', function(e){
        e.preventDefault();
        var data = $('#paypal_settings').serialize();

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: data,
            method: 'post',
            dataType : 'json',      
        }).done(function(res){
            alert(res.data);
        }).error(function(){

        });
    });
})
</script>