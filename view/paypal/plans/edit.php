<div class="wrap bspp_wrapper">
    <h3 class="wp-heading-inline">
        Update Pricing
    </h3>
    <form id="pp_add_plans" name="" action="<?php echo admin_url() . 'admin-post.php' ?>" method="post" class="">
        <p>
            <label>Billing Cycle Sequence:
            <input type="number" name="billing_cycle_sequence"/>
            </labe>
        </p>
        <p>
            <label>Value:
            <input type="number" name="pricing_scheme[fixed_price][value]"/>
            </label>
        </p>
        <p>
            <label>Currency Code:
            <input type="text" name="pricing_scheme[fixed_price][currency_code]"/>
            </label>
        </p>
        <input type="hidden" name="action" value="bspp_updateSubscription"/>
        <input type="hidden" name="plan_id" value="<?php echo getArrayValue($_GET, 'plan'); ?>"/>
        <?php wp_nonce_field('bspp_update_subscription', '_bspp_update_subscription_') ?>
        <button class="button button-primary" type="submit">Update Price</button>
    </form>
</div>