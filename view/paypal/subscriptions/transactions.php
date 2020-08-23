
<div class="">
    <div class="">
        <table>
            <tbody>
                <tr>
                    <td>Active</td>
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
                <tr colspan="2">
                    <td>Subscriber Info</td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><?php echo getArrayValue(getArrayValue($subscriber, 'name'), 'given_name') .' '.
                              getArrayValue(getArrayValue($subscriber, 'name'), 'surname') ?></td>
                </tr>
                <tr>
                    <td>E-Mail</td>
                    <td><?php echo getArrayValue(getArrayValue($subscriber, 'name'), 'email_address') ?></td>
                </tr>
                <tr>
                    <td>Payer-ID</td>
                    <td><?php echo getArrayValue($subscriber, 'payer_id') ?></td>
                </tr>
                <tr>
                    <td>Shipping address</td>
                    <td><?php echo getArrayValue($subscriber, 'payer_id') ?></td>
                </tr>
                <tr colspan="2">
                    <td>Billing Info</td>
                </tr>
                <?php $billingInfo = getArrayValue($subscriber, 'billing_info') ?>
                <tr>
                    <td>Outstanding balance</td>
                    <td><?php echo getArrayValue(getArrayValue($billingInfo, 'outstanding_balance'), 'currency_code').' '.
                                    getArrayValue(getArrayValue($billingInfo, 'outstanding_balance'), 'value') ?></td>
                </tr>
                <tr>
                    <td>Cycle executions</td>
                    <td><?php $executions = getArrayValue($subscriber, 'cycle_executions');
                                if(!empty($executions)){
                                    echo '<table>';
                                    foreach($executions as $val){
                                        echo '<tr>';
                                        echo "<td>Tenure type</td><td>$val->tenure_type</td>";
                                        echo "<td>Sequence</td><td>$val->sequence</td>";
                                        echo "<td>Cycles completed</td><td>$val->cycles_completed</td>";
                                        echo "<td>Cycles remaining</td><td>$val->cycles_remaining</td>";
                                        echo "<td>Current pricing scheme version</td><td>$val->current_pricing_scheme_version</td>";
                                        echo '</tr>';
                                    }
                                    echo '</table>';
                                }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Next billing time</td>
                    <td><?php echo getArrayValue($subscriber, 'next_billing_time') ?></td>
                </tr>
                <tr>
                    <td>Failed payments count</td>
                    <td><?php echo getArrayValue($subscriber, 'failed_payments_count') ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>