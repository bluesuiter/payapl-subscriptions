<div class="wrap bspp_wrapper">
    <h3 class="wp-heading-inline">Product Details <button id="save_pp_plan" class="button button-primary alignright">Save Plan</button></h3>
    <form id="pp_add_plans" name="" class="">
        <p class="col-4">
            <label>Product Id:</label>
            <input name="product_id" type="text" value="<?php echo $product; ?>" readonly id="product_id">
        </p>
        <p class="col-4">
            <label>Plan name:</label>
            <input name="plan_name" type="text" id="plan_name">
        </p>
        <p class="col-4">
            <label>Plan description:</label>
            <input name="plan_description" type="text" id="plan_description">
        </p>
        <p class="col-4">
            <label>Plan status:</label>
            <select name="plan_status" id="plan_status">
                <option value=""></option>
                <option value="CREATED">Created</option>
                <option value="INACTIVE">Inactive</option>
                <option value="ACTIVE">Active</option>
            </select>
        </p>
        <hr>
        <h1 class="wp-heading-inline">Billing Cycles</h1>
        <button type="button" id="bspp_add_pp" class="page-title-action">Add Billing Cycles</button>
        <hr>
        <div id="plans_list" class="position-relative">
            <div class="plan_row col-1">
                <span class="pp_plan_removeRow alignright dashicons dashicons-dismiss position-absolute"></span>
                <p class="col-4">
                    <label>Interval unit:</label>
                    <select class="interval_unit" name="billing[0][interval_unit]" id="">
                        <option value=""></option>
                        <option value="DAY">Day</option>
                        <option value="WEEK">Week</option>
                        <option value="MONTH">Month</option>
                        <option value="YEAR">Year</option>
                    </select>
                </p>
                <p class="col-4">
                    <label>Interval count:</label> 
                    <input type="number" size="50" min="1" placeholder="max 365" class="interval_count" name="billing[0][interval_count]" id="">
                </p>
                <p class="col-4">
                    <label>Tenure type:</label> 
                    <select class="tenure_type" name="billing[0][tenure_type]" id="">
                        <option value=""></option>
                        <option value="REGULAR">Regular</option>
                        <option value="TRIAL">Trial</option>
                    </select>
                </p>
                <p class="col-4">
                    <label>Sequence:</label> 
                    <input type="number" placeholder="1-99" min="1" max="99" class="sequence" name="billing[0][sequence]" id="">
                </p>
                <p class="col-4">
                    <label>Total cycles:</label> 
                    <input type="number" placeholder="1-999" min="1" max="999" class="total_cycles" name="billing[0][total_cycles]" id="">
                </p>
                <p class="col-4">
                    <label>Plan price:</label> 
                    <input type="text" class="plan_price" name="billing[0][plan_price]" id="">
                </p>
                <p class="col-4">
                    <label>Currency:</label> 
                    <input type="text" class="currency" name="billing[0][currency]" id="">
                </p>
            </div>
        </div>
        <?php wp_nonce_field('bspp_pp_create_plan', '_bspp_create_paypal_'); ?>
        <input type="hidden" name="action" value="bspp_paypal_add_plan">
    </form>
</div>
<script>
    var $ = jQuery.noConflict();
    jQuery(function($){
        $('#bspp_add_pp').on('click', function(){ 
            var row = $('#plans_list .plan_row:first-child').clone();
            $('#plans_list').append(row);
            correctSequence();            
        });

        $('select.interval_unit').on('change', function(){
            var limit = 365, val = $(this).val(); 
            if(val == 'YEAR'){
                limit = 1;
            }else if(val == 'WEEK'){
                limit = 52;
            }else if(val == 'MONTH'){
                limit = 12;
            }
            var name = $(this).attr('name');
            var rowCount = name.match(/\d+/g);
            $('input[name="billing['+rowCount+'][interval_count]"]').attr({max: limit, placeholder: 'max '+limit});
        });

        $('#save_pp_plan').on('click', function(e){
            e.preventDefault();
            var data = $('#pp_add_plans').serialize();

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

        $('#plans_list').on('click', '.pp_plan_removeRow', function(){
            if($('div.plan_row').length > 1){
                return $(this).parent('div.plan_row').remove();
            }
            $('div.plan_row').find('input, select').val('');
        });
    });

    function correctSequence(){
        $('#plans_list .plan_row').each(function(k, v){
            $(this).find('input').each(function(){
                $(this).attr('name', 'billing['+k+']['+$(this).attr('class')+']');
            });
        });
    }
</script>