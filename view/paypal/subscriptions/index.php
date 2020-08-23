<div class="wrap bspp_wrapper">
    <h3 class="">Subscribers</h3>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th title="Subscriber Id">Sub. Id</th>
                <th>User</th>
                <th>Plan</th>
                <th>E-Mail</th>
                <th>Status</th>
                <th>Active From</th>
                <th>Billing Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody> 
            <?php foreach($result as $record): 
                    $subscriberId = getArrayValue($record, 'subscription_id'); 
                    $userId = getArrayValue($record, 'user_id');
                    $user = get_user_by('ID', $userId);
                    //print_R($user);
                ?>
            <tr>
                <td><?php echo $subscriberId; ?></td>
                <td><?php echo get_user_meta($userId, 'first_name', true); ?></td>
                <td style="text-transform:capitalize"><?php echo implode(',', $user->roles) ?></td>
                <td><?php echo $user->data->user_email; ?></td>
                <td><?php echo getArrayValue($record, 'status'); ?></td>
                <td><?php echo getArrayValue($record, 'status_update_time'); ?></td>
                <td><?php echo getArrayValue($record, 'last_payment_currency').' '.getArrayValue($record, 'last_payment_amount'); ?></td>
                <td>
                    <a href="<?php echo admin_url('?page=bspp_view_sub&id='.$subscriberId); ?>" class="button button-small">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <strong class="alignnone">Total Records: <?php echo $totalCount; ?></strong>
                </td>
                <td colspan="3">
                    <?php $page = isset($_GET['index']) ? $_GET['index'] : 0;
                        if($page > 0){ ?>
                    <a class="button button-small alignright" href="<?php echo admin_url('?page=bspp_paypal_subscribers&index='.($page)) ?>">Previous</a>
                    <?php } 
                        $page = $page+1;
                        $totalPage = round($totalCount/25);
                        if($page < $totalPage){
                    ?>
                    <a class="button button-small alignright" href="<?php echo admin_url('?page=bspp_paypal_subscribers&index='.($page)) ?>">Next</a>
                    <?php } ?>                    
                </td>
            </tr>
        </tfoot>
    </table>
</div>