<?php

function sortByPriority($a, $b)
{
    $priority_a = isset($a['priority']) ? $a['priority'] : 0;
    $priority_b = isset($b['priority']) ? $b['priority'] : 0;

    if ($priority_a == $priority_b) {
        return 0;
    }

    return ($priority_a < $priority_b) ? -1 : 1;
}

function getIncomeArray($result)
{
    $array = array();
    foreach ($result as $res) {
        $income_head = isset($res['income_category']) ? $res['income_category'] : '';
        $amount = isset($res['amount']) ? $res['amount'] : 0;

        if (array_key_exists($income_head, $array)) {
            $array[$income_head] += $amount;
        } else {
            $array[$income_head] = $amount;
        }
    }
    return $array;
}
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('finance'); ?> <small><?php echo $this->lang->line('reports'); ?></small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form role="form" action="<?php echo site_url('financereports/profitlossreportbyclass') ?>" method="post" class="">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('search_type'); ?></label>
                                            <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                                <?php foreach ($searchlist as $key => $search) { ?>
                                                    <option value="<?php echo $key ?>" <?php
                                                                                        if ((isset($search_type)) && ($search_type == $key)) {
                                                                                            echo "selected";
                                                                                        }
                                                                                        ?>><?php echo $search ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('class'); ?></label>
                                            <select class="form-control" name="class_id">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($classlist as $class) { ?>
                                                    <option value="<?php echo $class['id'] ?>" <?php
                                                                                                if (isset($class_id) && $class_id == $class['id']) {
                                                                                                    echo "selected=selected";
                                                                                                }
                                                                                                ?>><?php echo $class['class'] ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                        </div>
                                    </div>
                                    <div id='date_result'>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                        </div>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <?php if (!empty($class_id)) { ?>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('profit') . " " . $this->lang->line('loss') . " " . $this->lang->line('report') . " " . $this->lang->line('by') . " " . $this->lang->line('class'); ?></h3>
                                <div class="box-tools pull-right">
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-center"><?php echo $this->lang->line('profit') . " " . $this->lang->line('loss') . " " . $this->lang->line('report') . " " . $this->lang->line('by') . " " . $this->lang->line('class'); ?></h4>
                                        <?php if (isset($class_id) && $class_id != '') {
                                            $selected_class = array_filter($classlist, function ($class) use ($class_id) {
                                                return $class['id'] == $class_id;
                                            });
                                            $selected_class = reset($selected_class);
                                            if ($selected_class) {
                                                echo '<p class="text-center">' . $this->lang->line('class') . ': ' . $selected_class['class'] . '</p>';
                                            }
                                        } ?>
                                        <p class="text-center"><?php echo $label; ?></p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped table-bordered">
                                        <tr>
                                            <th><?php echo $this->lang->line('description'); ?></th>
                                            <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('income'); ?></th>
                                            <th></th>
                                        </tr>
                                        <?php
                                        $total_income = 0;
                                        $income_array = getIncomeArray($income_result);
                                        if (!empty($income_array)) {
                                            foreach ($income_array as $income_head => $head_total) {
                                                echo '<tr>';
                                                echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . $income_head . '</td>';
                                                echo '<td class="text-right">' . $currency_symbol . ' ' . amountFormat($head_total) . '</td>';
                                                echo '</tr>';
                                                $total_income += $head_total;
                                            }
                                            echo '<tr>';
                                            echo '<td class="text-right"><strong>' . $this->lang->line('subtotal') . '</strong></td>';
                                            echo '<td class="text-right"><strong>' . $currency_symbol . amountFormat($total_income) . '</strong></td>';
                                            echo '</tr>';
                                        } else {
                                            echo '<tr>';
                                            echo '<td class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;No data available</td>';
                                            echo '<td class="text-right">' . $currency_symbol . ' ' . amountFormat(0) . '</td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo '<td class="text-right"><strong>' . $this->lang->line('subtotal') . '</strong></td>';
                                            echo '<td class="text-right"><strong>' . $currency_symbol . amountFormat(0) . '</strong></td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                        <tr>
                                            <th><?php echo $this->lang->line('total_income'); ?></th>
                                            <th class="text-right"><?php echo $currency_symbol . ' ' . amountFormat($total_income); ?></th>
                                        </tr>
                                        <tr style="border-top: 2px solid #ccc;">
                                            <th><?php echo $this->lang->line('expense'); ?></th>
                                            <th></th>
                                        </tr>
                                        <?php
                                        $total_expense = 0;
                                        if (!empty($expense_result)) {
                                            $expense_by_head = array();
                                            foreach ($expense_result as $expense) {
                                                $head = isset($expense['exp_category']) ? $expense['exp_category'] : 'Unknown';
                                                $amount = isset($expense['amount']) ? $expense['amount'] : 0;

                                                if (!isset($expense_by_head[$head])) {
                                                    $expense_by_head[$head] = 0;
                                                }
                                                $expense_by_head[$head] += $amount;
                                            }

                                            foreach ($expense_by_head as $head => $head_total) {
                                                echo '<tr>';
                                                echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . $head . '</td>';
                                                echo '<td class="text-right">' . $currency_symbol . ' ' . amountFormat($head_total) . '</td>';
                                                echo '</tr>';
                                                $total_expense += $head_total;
                                            }
                                            echo '<tr>';
                                            echo '<td class="text-right"><strong>' . $this->lang->line('subtotal') . '</strong></td>';
                                            echo '<td class="text-right"><strong>' . $currency_symbol . amountFormat($total_expense) . '</strong></td>';
                                            echo '</tr>';
                                        } else {
                                            echo '<tr>';
                                            echo '<td class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;No data available</td>';
                                            echo '<td class="text-right">' . $currency_symbol . ' ' . amountFormat(0) . '</td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo '<td class="text-right"><strong>' . $this->lang->line('subtotal') . '</strong></td>';
                                            echo '<td class="text-right"><strong>' . $currency_symbol . amountFormat(0) . '</strong></td>';
                                            echo '</tr>';
                                        }
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
                                        echo '<th>&nbsp;&nbsp;&nbsp;&nbsp;' . $this->lang->line('inventory_purchase') . '</th>';
                                        echo '<th class="text-right">' . $currency_symbol . ' ' . amountFormat($total_inventory) . '</th>';
                                        echo '</tr>';
                                        ?>

                                        <tr style="border-top: 2px solid #ccc;">
                                            <th><?php echo $this->lang->line('total_expense'); ?></th>
                                            <th class="text-right"><?php echo $currency_symbol . ' ' . amountFormat($total_expense + $total_depreciation); ?></th>
                                        </tr>
                                        <tr class="dark-shadow" style="border-top: 2px solid #ccc;">
                                            <th><?php echo $this->lang->line('profit') . " " . $this->lang->line('loss'); ?></th>
                                            <th class="text-right"><?php echo $currency_symbol . ' ' . amountFormat($total_income - ($total_expense + $total_depreciation)); ?></th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('profit') . " " . $this->lang->line('loss') . " " . $this->lang->line('report') . " " . $this->lang->line('by') . " " . $this->lang->line('class'); ?></h3>
                                <div class="box-tools pull-right">
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="alert alert-info">
                                    <p class="text-center"><?php echo $this->lang->line('please_select_a_class_to_view_the_report'); ?></p>
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
            '}' +
            '</style>';
        htmlToPrint += divToPrint.outerHTML;
        newWin = window.open("");
        newWin.document.write(htmlToPrint);
        newWin.print();
        newWin.close();
    }

    $(document).ready(function() {
        showdate('<?php echo $search_type; ?>');
    });
</script>