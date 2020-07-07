<div class="wrap bspp_wrapper">
    <h1 class="wp-heading-inline">Edit Product</h1>
    <form id="product_form" class="">
        <p class="col-4">
            <label>Product name:</label>
            <input name="product_name" type="text" value="<?php echo $name?>"  id="product_name">
        </p>
        <p class="col-4">
            <label>Product description:</label>
            <input name="product_description" type="text" value="<?php echo $description ?>" id="product_description">
        </p>
        <p class="col-4">
            <label>Product type:</label>
            <select name="product_type" id="product_type">
                <option value="">Select</option>
                <option <?php echo $type == 'PHYSICAL' ? 'selected' : '' ?> value="PHYSICAL">Physical</option>
                <option <?php echo $type == 'DIGITAL' ? 'selected' : '' ?> value="DIGITAL">Digital</option>
                <option <?php echo $type == 'SERVICE' ? 'selected' : '' ?> value="SERVICE">Service</option>
            </select>
        </p>
        <p class="col-4">
            <label>Product category:</label>
            <input name="product_category" type="text" value="<?php echo $category ?>" id="product_category">
        </p>
        <p class="col-4">
            <label>Home url:</label>
            <input name="home_url" type="text" value="<?php echo $home_url ?>" id="home_url">
        </p>
        <p>
            <button type="button" id="update_product" class="button button-primary alignright">Update Product</button>
        </p>
        <?php wp_nonce_field('bspp_pp_update_product', '_bspp_update_pp_product'); ?>
        <input type="hidden" name="product_id" value="<?php echo $id ?>">
        <input type="hidden" name="action" value="bspp_pp_update_product">
    </form>
</div>

<script>
jQuery(function($){
    $('#update_product').on('click', function(e){
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