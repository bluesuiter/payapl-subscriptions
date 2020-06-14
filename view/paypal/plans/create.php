<div class="wrap bspp_wrapper">
    <form id="pp_add_plans" name="" class="">
        <h3>Product Details <button id="save_pp_plan" class="button button-primary alignright">Save Plan</button></h3>
        <p class="col-4">
            <label>Product Id:</label>
            <input name="product_id" type="text" readonly value="" id="product_id">
        </p>
        <p class="col-4">
            <label>Product Name:</label>
            <input name="product_name" type="text" id="product_name">
        </p>
        <p class="col-4">
            <label>Product Description:</label>
            <input name="product_desc" type="text" id="product_desc">
        </p>
        <p class="col-4">
            <label>Product Status:</label>
            <input name="product_status" type="text" id="product_status">
        </p>
        <hr>
        <h3>Billing Cycles <button type="button" id="bspp_add_pp" class="button button-default alignright">Add Plan</button></h3>
        <div id="plans_list" class="position-relative">
            <div class="plan_row col-1">
                <span class="pp_plan_removeRow alignright dashicons dashicons-dismiss position-absolute"></span>
                <p class="col-4">
                    <label>Interval Unit: 
                    <select class="interval_unit" name="billing[0][interval_unit]" id="">
                        <option value=""></option>
                        <option value="DAY">Day</option>
                        <option value="WEEK">Week</option>
                        <option value="MONTH">Month</option>
                        <option value="YEAR">Year</option>
                    </select>
                </p>
                <p class="col-4">
                    <label>Interval Count:</label> 
                    <input type="number" size="50" min="1" placeholder="max 365" class="interval_count" name="billing[0][interval_count]" id="">
                </p>
                <p class="col-4">
                    <label>Tenure Type:</label> 
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
                    <label>Total Cycles:</label> 
                    <input type="number" placeholder="1-999" min="1" max="999" class="total_cycles" name="billing[0][total_cycles]" id="">
                </p>
                <p class="col-4">
                    <label>Plan Price:</label> 
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

        $('#plans_list').on('click', '.pp_plan_removeRow', function(){ alert('')
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