<?php //echo '<pre>'; print_r($result); echo '</pre>'; ?>
<div class="wrap bspp_wrapper">
    <h3 class="">Transaction Info</h3>
    <table class="wp-list-table widefat fixed striped">
        <tbody>
            <tr>
                <td>Status</td>
                <td><?php echo getArrayValue($result, 'status') ?></td>
            </tr>
            <tr>
                <td>Status update time</td>
                <td><?php echo getArrayValue($result, 'status_update_time') ?></td>
            </tr>
            <tr>
                <td>Subscription Id</td>
                <td><?php echo getArrayValue($result, 'id') ?></td>
            </tr>
            <tr>
                <td>Plan Id</td>
                <td><?php echo getArrayValue($result, 'plan_id') ?></td>
            </tr>
            <tr>
                <td>Start time</td>
                <td><?php echo getArrayValue($result, 'start_time') ?></td>
            </tr>
            <tr>
                <td>Quantity</td>
                <td><?php echo getArrayValue($result, 'quantity') ?></td>
            </tr>
            <?php $subscriber = getArrayValue($result, 'subscriber'); ?>
            <tr>
                <td colspan="2"><strong>Subscriber Info</strong></td>
            </tr>
            <tr>
                <td>Name</td>
                <td><?php echo getArrayValue(getArrayValue($subscriber, 'name'), 'given_name') .' '.
                            getArrayValue(getArrayValue($subscriber, 'name'), 'surname') ?></td>
            </tr>
            <tr>
                <td>E-Mail</td>
                <td><?php echo getArrayValue($subscriber, 'email_address') ?></td>
            </tr>
            <tr>
                <td>Payer-ID</td>
                <td><?php echo getArrayValue($subscriber, 'payer_id') ?></td>
            </tr>
            <tr>
                <td>Shipping address</td>
                <td><?php $address =  getArrayValue(getArrayValue($subscriber, 'shipping_address'), 'address');
                          if(!empty($address)): 
                            echo $address->address_line_1."<br/>".
                                 $address->address_line_2."<br/>".
                                 $address->admin_area_2."<br/>".
                                 $address->admin_area_1."<br/>".
                                 $address->postal_code."<br/>".
                                 $address->country_code;
                          endif;     ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Billing Info</strong></td>
            </tr>
            <?php $billingInfo = getArrayValue($result, 'billing_info') ?>
            <tr>
                <td>Outstanding balance</td>
                <td><?php echo getArrayValue(getArrayValue($billingInfo, 'outstanding_balance'), 'currency_code').' '.
                                getArrayValue(getArrayValue($billingInfo, 'outstanding_balance'), 'value') ?></td>
            </tr>
            <tr>
                <td>Cycle executions</td>
                <td><?php $executions = getArrayValue($billingInfo, 'cycle_executions');
                            if(!empty($executions)){
                                echo '<table>'.
                                    '<thead><tr><th>Tenure type</th>'.
                                    '<th>Sequence</th><th>Cycles completed</th>'.
                                    '<th>Cycles remaining</th><th>Current pricing scheme version</th>'.
                                    '</tr></thead>';
                                foreach($executions as $val){
                                    echo '<tr>'.
                                        "<td>$val->tenure_type</td>".
                                        "<td>$val->sequence</td>".
                                        "<td>$val->cycles_completed</td>".
                                        "<td>$val->cycles_remaining</td>".
                                        "<td>$val->current_pricing_scheme_version</td>".
                                        '</tr>';
                                }
                                echo '</table>';
                            }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Next billing time</td>
                <td><?php echo getArrayValue($billingInfo, 'next_billing_time') ?></td>
            </tr>
            <tr>
                <td>Failed payments count</td>
                <td><?php echo getArrayValue($billingInfo, 'failed_payments_count') ?></td>
            </tr>
        </tbody>
    </table>
</div>