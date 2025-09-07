<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('finance'); ?> <small><?php echo $this->lang->line('student_fees'); ?></small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('profit_loss_report'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form role="form" action="<?php echo site_url('financereports/profitlossreport') ?>" method="post" class="">
                            <div class="row">
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('search_type'); ?></label>
                                        <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($searchlist as $key => $search) { ?>
                                                <option value="<?php echo $key ?>" <?php
                                                if (isset($search_type)) {
                                                    echo ($search_type == $key) ? "selected" : "";
                                                }
                                                ?>><?php echo $search ?></option>
                                                    <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3" id="date_result">
                                    <?php
                                    if (isset($_POST['date_from']) && $_POST['date_from'] != '' && isset($_POST['date_to']) && $_POST['date_to'] != '') {
                                        ?>
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('date_from'); ?></label>
                                            <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getSchoolDateFormat(), $this->customlib->datetostrtotime($_POST['date_from']))); ?>" readonly />
                                            <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6 col-md-3 col-md-offset-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label><br/>
                                        <input type="submit" name="search" value="<?php echo $this->lang->line('search'); ?>" class="btn btn-primary btn-sm">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if (isset($search_type)) { ?>
                    <div class="box box-primary" id="transfee">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-th"></i> <?php echo $this->lang->line('profit_loss_report'); ?></h3>
                            <div class="box-tools pull-right">
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Print" onclick="printData()"><i class="fa fa-print"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="text-center">
                                        <?php echo $this->lang->line('profit_loss_report'); ?>
                                        <br/>
                                        <?php echo $this->lang->line('for_the_period_of'); ?> <?php echo $label; ?>
                                    </h4>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('particulars'); ?></th>
                                            <th class="text-right"><?php echo $this->lang->line('amount'); ?> (<?php echo $this->customlib->getSchoolCurrencyFormat(); ?>)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2"><b><?php echo $this->lang->line('income'); ?></b></td>
                                        </tr>
                                        
                                        <?php 
                                        if (!empty($income_totals)) {
                                            foreach ($income_totals as $head => $head_total) {
                                                echo '<tr>';
                                                echo '<td><strong>&nbsp;&nbsp;&nbsp;&nbsp;' . htmlspecialchars($head) . '</strong></td>';
                                                echo '<td class="text-right"><strong>' . $currency_symbol . ' ' . amountFormat($head_total) . '</strong></td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr>';
                                            echo '<td class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;No data available</td>';
                                            echo '<td class="text-right">' . $currency_symbol . ' ' . amountFormat(0) . '</td>';
                                            echo '</tr>';
                                        } ?>
                                        
                                        <?php
                                            $total_online_admission = 0;
                                            if (!empty($online_admission_fees)) {
                                                foreach ($online_admission_fees as $fee) {
                                                    $amount = isset($fee->amount) ? $fee->amount : 0;
                                                    $total_online_admission += $amount;
                                                }
                                            }

                                            echo '<tr>';
                                            echo '<td><strong>&nbsp;&nbsp;&nbsp;&nbsp;' . $this->lang->line('online_admission_fees_collection_report') . '</strong></td>';
                                            echo '<td class="text-right"><strong>' . $currency_symbol .' ' . amountFormat($total_online_admission) . '</strong></td>';
                                            echo '</tr>';
                                        ?>
                                        
                                        <?php
                                            // Fee billing (outstanding fees as receivables) - now showing total from collection report
                                            echo '<tr>';
                                            echo '<td><strong>&nbsp;&nbsp;&nbsp;&nbsp;' . $this->lang->line('fee_billing_report') . '</strong></td>';
                                            echo '<td class="text-right"><strong>' . $currency_symbol . amountFormat($total_fee_billing) . '</strong></td>';
                                            echo '</tr>';
                                        ?>
                                        
                                        <tr style="border-top: 2px solid #ccc;">
                                            <td><b><?php echo $this->lang->line('total_income'); ?></b></td>
                                            <td class="text-right"><b><?php echo $currency_symbol .' ' . amountFormat($total_income); ?></b></td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="2"><b><?php echo $this->lang->line('expense'); ?></b></td>
                                        </tr>
                                        
                                        <?php
                                            // Group expenses by head
                                            $expense_by_head = array();
                                            if (!empty($expense_result)) {
                                                foreach ($expense_result as $expense) {
                                                    $head = isset($expense['exp_category']) ? $expense['exp_category'] : 'Uncategorized';
                                                    if (!isset($expense_by_head[$head])) {
                                                        $expense_by_head[$head] = array();
                                                    }
                                                    $expense_by_head[$head][] = $expense;
                                                }
                                            }

                                            $total_expense_category = 0;
                                            if (!empty($expense_by_head)) {
                                                foreach ($expense_by_head as $head => $expenses) {
                                                    $head_total = 0;
                                                    echo '<tr>';
                                                    echo '<td><strong>&nbsp;&nbsp;&nbsp;&nbsp;' . htmlspecialchars($head) . '</strong></td>';
                                                

                                                    foreach ($expenses as $expense) {
                                                        $amount = isset($expense['amount']) ? $expense['amount'] : 0;
                                                        $head_total += $amount;
                                                        $total_expense_category += $amount;
                                                    }

                                                    echo '<td class="text-right"><strong>' . $currency_symbol . amountFormat($head_total) . '</strong></td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr>';
                                                echo '<td class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;No data available</td>';
                                                echo '<td class="text-right">' . $currency_symbol .' '. amountFormat(0) . '</td>';
                                                echo '</tr>';
                                                echo '<tr>';
                                                echo '<td class="text-right"><strong>' . $this->lang->line('subtotal') . '</strong></td>';
                                                echo '<td class="text-right"><strong>' . $currency_symbol . amountFormat(0) . '</strong></td>';
                                                echo '</tr>';
                                            }
                                        ?>
                                        
                                        <?php
                                            $total_payroll = 0;
                                            if (!empty($payroll_result)) {
                                                foreach ($payroll_result as $payroll) {
                                                    $net_salary = isset($payroll['net_salary']) ? $payroll['net_salary'] : 0;
                                                    $total_payroll += $net_salary;
                                                }
                                            }

                                            echo '<tr>';
                                            echo '<th>&nbsp;&nbsp;&nbsp;&nbsp;' . $this->lang->line('payroll_report') . '</th>';
                                            echo '<th class="text-right">' . $currency_symbol . amountFormat($total_payroll) . '</th>';
                                            echo '</tr>';
                                        ?>
                                        
                                        <?php
                                            // Calculate total depreciation
                                            $total_depreciation = 0;
                                            
                                            // If depreciation data exists, sum up all amounts
                                            if (!empty($depreciation_result)) {
                                                foreach ($depreciation_result as $depreciation) {
                                                    $amount = isset($depreciation['amount']) ? $depreciation['amount'] : 0;
                                                    $total_depreciation += $amount;
                                                }
                                            }

                                            // Display depreciation row in report
                                            echo '<tr>';
                                            echo '<th>&nbsp;&nbsp;&nbsp;&nbsp;' . $this->lang->line('depreciation_report') . '</th>';
                                            echo '<th class="text-right">' . $currency_symbol . ' ' . amountFormat($total_depreciation) . '</th>';
                                            echo '</tr>';
                                        ?>
                                        
                                        <?php
                                            echo '<tr>';
                                            echo '<th>&nbsp;&nbsp;&nbsp;&nbsp;' . $this->lang->line('book_inventory') . '</th>';
                                            echo '<th class="text-right">' . $currency_symbol .' ' . amountFormat($total_bookinventory) . '</th>';
                                            echo '</tr>';
                                        ?>
                                        
                                        <?php
                                            echo '<tr>';
                                            echo '<th>&nbsp;&nbsp;&nbsp;&nbsp;' . $this->lang->line('inventory_purchase') . '</th>';
                                            echo '<th class="text-right">' . $currency_symbol .' ' . amountFormat($total_inventory) . '</th>';
                                            echo '</tr>';
                                        ?>
                                        
                                        <tr style="border-top: 2px solid #ccc;">
                                            <th><?php echo $this->lang->line('total_expense'); ?></th>
                                            <th class="text-right"><?php echo $currency_symbol .' ' . amountFormat($total_expense); ?></th>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><b><?php echo ($profit_loss >= 0) ? $this->lang->line('profit') : $this->lang->line('loss'); ?></b></td>
                                            <td class="text-right"><b><?php echo $currency_symbol .' ' . amountFormat($profit_loss); ?></b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>

<script>
    function printData() {
        var divToPrint = document.getElementById('transfee');
        var htmlToPrint = '' +
                '<style type="text/css">' +
                'table th, table td {' +
                'border:1px solid #000;' +
                'padding:5px;' +
                '}'+
                '</style>';
        htmlToPrint += divToPrint.outerHTML;
        newWin = window.open("");
        newWin.document.write(htmlToPrint);
        newWin.print();
        newWin.close();
    }
    
    $(document).ready(function () {
        showdate('<?php echo $search_type; ?>');
    });
</script>