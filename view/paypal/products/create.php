<div class="wrap bspp_wrapper">
    <h3 class="wp-heading-inline">Add Product</h3>
    <form id="product_form" class="">
        <p class="col-4">
            <label>Product name:</label>
            <input name="product_name" type="text" id="product_name">
        </p>
        <p class="col-4">
            <label>Product description:</label>
            <input name="product_description" type="text" id="product_description">
        </p>
        <p class="col-4">
            <label>Product type:</label>
            <select name="product_type" id="product_type">
                <option value="">Select</option>
                <option value="PHYSICAL">Physical</option>
                <option value="DIGITAL">Digital</option>
                <option value="SERVICE">Service</option>
            </select>
        </p>
        <p class="col-4">
            <label>Product category:</label>
            <input name="product_category" type="text" id="product_category">
        </p>
        <p class="col-4">
            <label>Home url:</label>
            <input name="home_url" type="text" value="<?php echo site_url(); ?>" id="home_url">
        </p>
        <p>
            <button type="button" id="save_product" class="button button-primary alignright">Save Product</button>
        </p>
        <?php wp_nonce_field('bspp_pp_add_product', '_bspp_create_pp_product'); ?>
        <input type="hidden" name="action" value="bspp_add_pp_product">
    </form>
</div>

<script>
jQuery(function($){
    $('#save_product').on('click', function(e){
        e.preventDefault();
        var data = $('#product_form').serialize();

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
});
</script>