<?php

//echo '<pre>';
//print_r($plan);
//echo '</pre>';
?>

<div class="wrap bspp_wrapper">
    <h3 class="wp-heading-inline"> Product Details
        <a class="button button-small button-primary alignright" href="<?php echo admin_url('?page=bspp_edit_plan&plan='.getArrayValue($_GET, 'plan')) ?>">Edit Plan</a>
    </h3>
    <div class="col-1">
        <table class="wp-list-table widefat fixed striped">
            <tbody>
                <tr>
                    <td><strong>Plan Id</strong></td>
                    <td><?php echo $plan->id ?></td>
                </tr>
                <tr>
                    <td><strong>Product Id</strong></td>
                    <td><?php echo $plan->product_id ?></td>
                </tr>
                <tr>
                    <td><strong>Plan Name</strong></td>
                    <td><?php echo $plan->name ?></td>
                </tr>
                <tr>
                    <td><strong>Plan Name</strong></td>
                    <td><?php echo $plan->name ?></td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td><?php echo $plan->status ?></td>
                </tr>
                <tr>
                    <td><strong>Description</strong></td>
                    <td><?php echo $plan->description ?></td>
                </tr>
                <tr>
                    <td><strong>Usage type</strong></td>
                    <td><?php echo $plan->usage_type ?></td>
                </tr>
                <tr>
                    <td><strong>Create time</strong></td>
                    <td><?php echo $plan->create_time ?></td>
                </tr>
                <tr>
                    <td><strong>Update time</strong></td>
                    <td><?php echo $plan->update_time ?></td>
                </tr>
                
                <!-- Billing cycles -->
                <tr>
                    <td colspan="2"><strong>Billing cycles</strong></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        <strong>Pricing Scheme</strong>
                                    </th>
                                    <th colspan="2">
                                        <strong>Frequency</strong>
                                    </th>
                                    <th>
                                        <strong>Tenure Type</strong>
                                    </th>
                                    <th>
                                        <strong>Sequence</strong>
                                    </th>
                                    <th>
                                        <strong>Total cycles</strong>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <strong>Version</strong>
                                    </th>
                                    <th>
                                        <strong>Price</strong>
                                    </th>
                                    <th>
                                        <strong>Create Time</strong>
                                    </th>
                                    <th>
                                        <strong>Update Time</strong>
                                    </th>
                                    <th>
                                        <strong>Interval unit</strong>
                                    </th>
                                    <th>
                                        <strong>Interval count</strong>
                                    </th>
                                    <th colspan="3">
                                        
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $cycles = $plan->billing_cycles;
                                    foreach($cycles as $cycle){
                                        $pricing_scheme = $cycle->pricing_scheme;
                                        $frequency = $cycle->frequency;
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $pricing_scheme->version ?>
                                            </td>
                                            <td>
                                                <?php echo $pricing_scheme->fixed_price->currency_code.' '.
                                                           $pricing_scheme->fixed_price->value
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $pricing_scheme->create_time ?>
                                            </td>
                                            <td>
                                                <?php echo $pricing_scheme->update_time ?>
                                            </td>
                                            <td>
                                                <?php echo $frequency->interval_unit ?>
                                            </td>
                                            <td>
                                                <?php echo $frequency->interval_count ?>
                                            </td>
                                            <td>
                                                <?php echo $cycle->tenure_type ?>
                                            </td>
                                            <td>
                                                <?php echo $cycle->sequence ?>
                                            </td>
                                            <td>
                                                <?php echo $cycle->total_cycles ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }                          
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <!-- Billing cycles end -->
                
                <!-- Payment preferences -->
                <tr>
                    <td colspan="2">
                        <strong>Payment preferences</strong>
                    </td>
                </tr>
                <tr>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><strong>Service type</strong></th>
                                <th><strong>Auto bill outstanding</strong></th>
                                <th><strong>Setup fee</strong></th>
                                <th><strong>Setup fee failure action</strong></th>
                                <th><strong>Payment failure threshold</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $payment_preferences = $plan->payment_preferences; ?>
                            <tr>
                                <th><?php echo $payment_preferences->service_type ?></th>
                                <th><?php echo $payment_preferences->auto_bill_outstanding ?></th>
                                <th><?php echo $payment_preferences->setup_fee->currency_code.' '.
                                               $payment_preferences->setup_fee->value ?></th>
                                <th><?php echo $payment_preferences->setup_fee_failure_action ?></th>
                                <th><?php echo $payment_preferences->payment_failure_threshold ?></th>
                            </tr>
                        </tbody>
                    </table>
                </tr>
                <!-- Payment preferences end -->
            </tbody>
        </table>
    </div>
</div>