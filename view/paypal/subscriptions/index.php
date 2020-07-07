<div class="wrap bspp_wrapper">
    <h3 class="">Subscribers</h3>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th title="Subscriber Id">Sub. Id</th>
                <th>User</th>
                <th>Status</th>
                <th>Create Time</th>
                <th>Approve</th>
                <th>Edit</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody> 
            <?php foreach($result as $record): $subscriberId = getArrayValue($record, 'id'); ?>
            <tr>
                <td><?php echo $subscriberId; ?></td>
                <td><?php echo getArrayValue($record, 'user_id'); ?></td>
                <td><?php echo getArrayValue($record, 'status'); ?></td>
                <td><?php echo getArrayValue($record, 'create_time'); ?></td>
                <td>
                    <a target="_blank" href="<?php echo getArrayValue($record, 'approve_href') ?>" class="button button-small">Approve</a>
                </td>
                <td>
                    <a href="<?php echo admin_url('?page=bspp_edit_sub&id='.$subscriberId); ?>" class="button button-small">Edit</a>
                </td>
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
                <td colspan="2"></td>
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