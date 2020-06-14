<div class="wrap">
    <h3>PayPal Settings</h3>
    <form id="paypal_settings" action="" name="" class="">
        <fieldset>
            <button class="button button-primary alignright" id="ls_pp_save_settings">Save</button>
            <strong>Active enviornment:</strong>
            <p>Sandbox Id: <select id="env" name="paypal_env">
                                <option value="">Select Enviornment</option>
                                <option value="sandbox">Sandbox</option>
                                <option value="live">Live</option>
                            </select>
            </p>
        </fieldset>
        <hr>
        <fieldset>
            <strong class="col-1">Sandbox details:</strong>
            <p class="col-3 alignleft">Authourization Code: <input id="sb_auth_code" size="45" type="text" name="sb_auth_code"></p>
            <p class="col-3 alignleft">Sandbox Id: <input id="sandbox_id" size="45" type="text" name="sandbox_id"></p>
            <p class="col-3 alignright">Sandbox Secret: <input id="sandbox_secret" size="45" type="text" name="sandbox_secret"></p>
        </fieldset>
        <hr>
        <fieldset>
            <strong class="col-1">Live details:</strong>
            <p class="col-3 alignleft">Authourization Code: <input id="lv_auth_code" size="45" type="text" name="lv_auth_code"></p>
            <p class="col-3 alignleft">Live Id: <input id="live_id" size="45" type="text" name="live_id"></p>
            <p class="col-3 alignright">Live Secret: <input id="live_secret" size="45" type="text" name="live_secret"></p>
        </fieldset>
        <?php wp_nonce_field('bspp_paypal_config_admin', '_bspp_settings_paypal_'); ?>
        <input type="hidden" name="action" value="bspp_paypal_config_admin">
    </form>
</div>
<script>
jQuery(function($){
    var data = JSON.parse('<?php echo json_encode($data) ?>');
    if(typeof data.env != undefined){
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