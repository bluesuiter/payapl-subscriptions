<div class="wrap">
    <table class="">
        <thead>
            <tr>
                <th>Status</th>
                <th>ID</th>
                <th>Amount</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
        <?php $transactions = getArrayValue($result, 'transactions');
                if(!empty($transactions)):
                    foreach($transactions as $row):
         ?>
                        <tr>
                            <td><?php echo $row->status ?></td>
                            <td><?php echo $row->id ?></td>
                            <td><?php $amount = $row->amount_with_breakdown->gross_amount; 
                                        echo $amount->value.' '.$amount->currency_code; ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($row->time)) ?></td>
                        </tr>
        <?php 
                    endforeach;
                endif; ?>
        </tbody>
    </table>
</div>