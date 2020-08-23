<div class="wrap bspp_wrapper">
    <h3 class="wp-heading-inline">Product Plans
        <a class="alignright button vc_ui-button-default" href="<?php echo admin_url('?page=bspp_add_paypal_plan&product='.getArrayValue($_GET, 'product')) ?>">Create Plan</a>
    </h3>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Product Id</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($plans)): foreach($plans as $plan): ?>
            <tr>
                <td><?php echo $plan->id ?></td>
                <td><?php echo $plan->name ?></td>
                <td><?php echo $plan->description ?></td>
                <td><?php echo $plan->status ?></td>
                <td><?php echo $plan->create_time ?></td>
                <td>
                    <a class="button button-small" href="<?php echo admin_url('admin.php?page=bspp_pp_view_plan&plan='.$plan->id) ?>">
                        View
                    </a>
                    <a class="button button-small" href="#" onclick="activatePlan('<?php echo $plan->id ?>', '<?php echo $plan->status ?>')">
                        <?php echo $plan->status == 'ACTIVE' ? 'De-Activate' : 'Activate' ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; endif; ?>
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
<script>
function activatePlan(planId, status){
    if(!confirm('Are you sure?')){
        return false; 
    }
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php') ?>',
        method: 'post',
        data: {plan: planId, status: status, action: 'bspp_change_plan_status', nonce: '<?php echo wp_create_nonce() ?>'}
    }).done(function(res){
        //alert(res);
        location.reload();
    });
}
</script>