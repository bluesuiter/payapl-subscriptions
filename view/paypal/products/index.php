<div class="wrap bspp_wrapper">
    <h3 class="wp-heading-inline">PayPal Products
        <a class="button button-small button-primary alignright" href="<?php echo admin_url('?page=bspp_paypal_add_product') ?>">Add Product</a>
    </h3>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Product Id</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody> 
            <?php foreach($products as $product): ?>
            <tr>
                <td><?php echo getArrayValue($product, 'id') ?></td>
                <td><?php echo getArrayValue($product, 'name') ?></td>
                <td><?php echo getArrayValue($product, 'description') ?></td>
                <td>
                    <a class="button button-small" href="<?php echo admin_url('admin.php?page=bspp_paypal_edit_product&product='.$product->id) ?>">
                        Edit
                    </a>
                    <a class="button button-small" href="<?php echo admin_url('admin.php?page=bspp_paypal_plan&product='.$product->id) ?>">
                        Plans
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>