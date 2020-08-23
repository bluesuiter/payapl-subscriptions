<form id="register_user" action="" method="post">
    <div>
        <label for="fname">First Name <strong>*</strong></label>
        <input class="required" type="text" name="ufname" error-message="Please enter your first-name">
    </div>

    <div>
        <label for="lname">Last Name <strong>*</strong></label>
        <input class="required" type="text" name="ulname" error-message="Please enter your last-name">
    </div>

    <div>
        <label for="uemail">Email <strong>*</strong></label>
        <input class="required" type="text" name="uemail" error-message="Please enter an email address">
    </div>

    <div>
        <label for="password">Password <strong>*</strong></label>
        <input class="required" type="password" name="password" error-message="Please enter password">
    </div>

    <div>
        <label for="cpassword">Confirm Password <strong>*</strong></label>
        <input class="required" type="password" name="cpassword" error-message="Please enter password">
    </div>
        
    <div>
        <label for="plan">Plan <strong>*</strong></label>
        <p>
            <span><input name="plan" type="radio" class="required" value="free" checked>Free</span>
            <span><input name="plan" type="radio" class="required" value="monthly">Gold (10 GBP)</span>
            <span><input name="plan" type="radio" class="required" value="yearly">Yearly (100 GBP)</span>
        </p>
    </div>
    <?php wp_nonce_field('bspp_reg_call', 'bspp_r3g_ca11',) ?>
    <input type="hidden" name="action" value="register_user" />
    <input type="submit" name="submit" id="request_register" value="Register" />
</form>

<style>
    div { margin-bottom: 2px; }
    input { margin-bottom: 4px; }
</style>

<?php $objSettings = new \LcFramework\Controllers\Paypal\SettingsController(); ?>
<script defer>
jQuery(function($){
    $('#register_user').on('submit', function(){
        var form = '#register_user';
        var res = validateForm(form);
        console.log(res)
        return res;
        // if(res == true){
            //$(form).submit();
            // $.post('<?php echo admin_url('admin-ajax.php') ?>', $(form).serialize(), function(res){
            //     if(typeof res.success != 'undefined'){
            //         if(res.success === 'true'){
            //             window.location.href("<?php echo $objSettings::readSettings('success_page') ?>");
            //         }

            //         if(res.success === 'false'){
            //             window.location.href("<?php echo $objSettings::readSettings('failure_page') ?>");
            //         }
            //     }
            // });
        // }
    });
});
</script>