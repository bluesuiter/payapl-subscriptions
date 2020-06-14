<form id="register_user" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
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
            <span><input name="plan" type="radio" class="required" value="gold">Gold (10 GBP)</span>
            <span><input name="plan" type="radio" class="required" value="yearl">Yearl (100 GBP)</span>
        </p>
    </div>
    <?php wp_nonce_field('bspp_reg_call', 'bspp_r3g_ca11',) ?>
    <input type="hidden" name="action" value="register_user" />
    <input type="submit" name="submit" value="Register" />
</form>

<style>
    div { margin-bottom: 2px; }
    input { margin-bottom: 4px; }
</style>
<script>
jQuery(function($){
    $('#register_user').on('submit', function(e){
        e.preventDefault();
        if(validateForm('#register_user')){
            $.post('<?php echo admin_url('admin-ajax.php') ?>', $(this).serialize(), function(res){
                alert(res);
            });
        }
    });
})
</script>