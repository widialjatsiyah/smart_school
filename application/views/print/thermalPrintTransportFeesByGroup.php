<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1">
<head>
    <style>
        .page-break { display: block; page-break-before: always; }
        
        *{padding: 0; margin: 0;}
        body {
            margin: 0;
            padding: 0;
            font-family: 'arial', sans-serif;
            font-size: 11pt;
        }

         @page {
            size: 2.9in 11in;
            margin-top: 0cm;
            margin-left: 0cm;
            margin-right: 0cm;
            margin-bottom: 0cm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
 
        table th, table td{padding-top: 5px; padding-bottom: 5px;font-size: 9pt;vertical-align: top;}
        p{margin-bottom: 5px;}
        h1 {
            margin: 0;
            padding: 0;
            font-size: 16pt; font-weight:bold
        }
       
.title-around-span {
    position: relative;
    text-align: center;
    padding: 0;
} 

.title-around-span:before {
    content: "";
    display: block;
    width: 100%;
    position: absolute;
    left: 0;
    top: 50%;
    border-top: 2px #000 dashed;
}

.title-around-span span {
    position: relative;
    z-index: 1;
    padding: 0 5px;
    color: #000;
    font-weight: bold;
}

.title-around-span span:before{
    content: "";
    display: block;
    width: 100%;
    z-index: -1;
    position: absolute;
    left: 0;
    top: 50%;
    border-top: 10px #fff solid;
}

    </style>
</head>
<body>
<?php 
// 4th print
$print_copy=explode(',', $settinglist[0]['is_duplicate_fees_invoice']);
for($i=0;$i<count($print_copy);$i++){   ?>
	
	<?php if($sch_setting->single_page_print==0 && $i > 0) {  ?>
    <div class="page-break"></div>
	<?php } ?>

   <div style="margin: 0 auto;padding: 0;">
		<h1 style="text-align: center;padding-bottom: 5px;"><?php echo $thermal_print['school_name'];?></h1>
        <p style="text-align:center; font-weight:bold;"><?php echo $thermal_print['address'];?></p>
		<!--
        <p style="text-align: center; font-weight: bold;line-height: 11px;"><?php echo $this->lang->line('phone_no'); ?>:<?php echo $sch_setting->phone;?></p>
        <p style="text-align: center; font-weight: bold;line-height: 11px;"><?php echo $this->lang->line('email'); ?>:<?php echo $sch_setting->email;?></p>
        <p style="text-align: center; font-weight: bold;line-height: 11px;"><?php echo $this->lang->line('website'); ?>:<?php echo $sch_setting->base_url;?> </p>-->
		
        <h2 style="border-top: 1px solid #000; border-bottom: 1px solid #000; text-align:center;font-size: 12pt;padding-top: 5px; padding-bottom: 5px; margin-top: 8px; margin-bottom:5px;"><?php
        if($print_copy[$i]==0){
            echo $this->lang->line('office_copy'); 
        }else if($print_copy[$i]==1){
            echo $this->lang->line('student_copy');
        }else if($print_copy[$i]==2){
            echo $this->lang->line('bank_copy'); 
        }
        ?></h2>
		
        <table width="100%" cellpadding="0" cellspacing="0">
            
                <tr>
                    <td align="left" style="padding-top:0px; padding-bottom:0; font-weight: bold"><?php echo $this->customlib->getFullName($feeList->firstname,$feeList->middlename,$feeList->lastname,$sch_setting->middlename,$sch_setting->lastname);  ?><?php echo " (".$feeList->admission_no.")"; ?></td>
				</tr>
                <tr>
					<td align="left" style="padding-top:1px;font-weight: bold; padding-bottom:0px;"><?php echo $this->lang->line('class'); ?>: <?php echo $feeList->class . " (" . $feeList->section . ")"; ?></td>
                </tr>
				<tr><td class="title-around-span"><span><?php echo $this->lang->line('fees');//$this->lang->line('fees_group'); ?></span></td></tr>
        </table>
		
        
		<table width="100%" cellpadding="0" cellspacing="0">
            <tbody> 
                <tr>
                    <td align="center" style="padding-bottom: 5px; padding-top: 1px; font-weight: bold;"><?php echo $this->lang->line("transport_fees").' ('.$this->lang->line(strtolower($feeList->month)).')'; ?></td>					
                </tr>        
			 </tbody>             
        </table>							
		
        <table width="100%" cellpadding="0" cellspacing="0" style="border-top: 2px #000 dashed;line-height: 11px;padding-top: 2px;">
            <tr>                    
                    <th style="text-align:left;"><?php echo $this->lang->line('amount'); ?>(<?php echo $currency_symbol;?>)</th>
                    <th style="text-align:center;"><?php echo $this->lang->line('paid'); ?>(<?php echo $currency_symbol;?>)</th>
                    <th style="text-align:right;"><?php echo $this->lang->line('balance'); ?>(<?php echo $currency_symbol;?>)</th>                     
            </tr>
             
                <?php
                    $amount = 0;
                    $discount = 0;
                    $fine = 0;
                    $total = 0;
                    $grd_total = 0;

                    if (empty($feeList)) { ?>
                        <tr>
                           <td colspan="3" class="text-danger text-center"><?php echo $this->lang->line('no_transaction_found'); ?></td>
                        </tr>
                    <?php   } else {
                                $fee_discount = 0;
                                $fee_paid = 0;
                                $fee_fine = 0;
                                $alot_fee_discount = 0;

                                if(!empty($feeList->amount_detail)) {
                                    $fee_deposits = json_decode(($feeList->amount_detail));
                                    foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                        $fee_paid = $fee_paid + $fee_deposits_value->amount;
                                        $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                        $fee_fine = $fee_fine + $fee_deposits_value->amount_fine;
                                    }
                                }
                                $feetype_balance = $feeList->fees - ($fee_paid + $fee_discount);
                    ?>
                                <tr>                                    
                                    <td class="text text-left"><?php 
                                       echo $feeList->fees; 
                                       if(($feeList->due_date != "0000-00-00" && $feeList->due_date != null) && (strtotime($feeList->due_date) < strtotime(date('Y-m-d')))){
                                            $tr_fine_amount = $feeList->fine_amount;
                                            if ($feeList->fine_type != "" && $feeList->fine_type == "percentage") {
                                                $tr_fine_amount = percentageAmount($feeList->fees, $feeList->fine_percentage);
                                            }   ?>
                                        <span  class="text text-danger"><?php echo " + " . amountFormat($tr_fine_amount); ?></span>
                                    <?php  } ?>
                                    </td>
                                    <td class="text text-center"><?php echo (amountFormat($fee_paid, 2, '.', '')); ?></td>
                                    <td class="text text-right"><?php
                                                    $display_none = "ss-none";
                                                    if ($feetype_balance > 0) {
                                                        $display_none = "";
                                                        echo (amountFormat($feetype_balance, 2, '.', ''));
                                                    }
                                                    ?></td>
                                    
                                </tr>
                             <?php }   ?>
						</table>
						
						<table width="100%" cellpadding="0" cellspacing="0">	
								<tr><td class="title-around-span" colspan="5"><span><?php echo $this->lang->line('partial_payment'); ?></span></td></tr> 
                                <tr>
                                    <th class="text text-left" ><?php echo $this->lang->line('date'); ?></th>   
                                    <th class="text text-center" ><?php echo $this->lang->line('pay_id'); ?></th>   
									<th class="text text-center" ><?php echo $this->lang->line('discount'); ?>(<?php echo $currency_symbol;?>)</th>                 
									<th class="text text-center" ><?php echo $this->lang->line('fine'); ?>(<?php echo $currency_symbol;?>)</th>
									<th class="text text-right" ><?php echo $this->lang->line('paid'); ?>(<?php echo $currency_symbol;?>)</th>
                                </tr>

                            <?php $fee_deposits = json_decode(($feeList->amount_detail));
                                if(is_object($fee_deposits)){
                                    foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value){ ?>
                                    <tr>
                                        <td align="left" style="border-bottom:0"><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_deposits_value->date)); ?></td>										
                                        <td class="text text-center"><?php echo $feeList->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?></td>
                                        <td class="text text-center"><?php echo (amountFormat($fee_deposits_value->amount_discount, 2, '.', '')); ?></td>
                                        <td class="text text-center"><?php echo (amountFormat($fee_deposits_value->amount_fine, 2, '.', '')); ?></td>                                      
										<td class="text text-right"><?php echo (amountFormat($fee_deposits_value->amount, 2, '.', '')); ?></td>
                                    </tr>
                            <?php   }
                                } 
                            ?>           
						</table>
			<p style="padding-top:3px; line-height:normal; font-size:8pt; padding-bottom: 5px;border-top: 1px #000 solid; font-style: italic;"><?php echo $thermal_print['footer_text'];?></p>
    </div> 

<?php } ?>

</body>

</html>