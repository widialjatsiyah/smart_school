<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('edit'); ?> <?php echo $this->lang->line('assets'); ?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="employeeform" action="<?php echo base_url() ?>admin/assets/edit/<?php echo $id ?>" name="employeeform" method="post" accept-charset="utf-8">
                                    <div class="box-body">
                                        <?php if ($this->session->flashdata('msg')) { ?>
                                            <?php echo $this->session->flashdata('msg') ?>
                                        <?php } ?>
                                        <?php echo $this->customlib->getCSRF(); ?>
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                                    <input autofocus="" id="name" name="name" placeholder="<?php echo $this->lang->line('enter_name'); ?>" type="text" class="form-control"  value="<?php echo set_value('name', $asset['name']); ?>" required/>
                                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="category"><?php echo $this->lang->line('category'); ?></label>
                                                    <input id="category" name="category" placeholder="<?php echo $this->lang->line('enter_category'); ?>" type="text" class="form-control"  value="<?php echo set_value('category', $asset['category']); ?>" />
                                                    <span class="text-danger"><?php echo form_error('category'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="description"><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>
                                                    <textarea id="description" name="description" placeholder="<?php echo $this->lang->line('enter_description'); ?>" type="text" class="form-control"><?php echo set_value('description', $asset['description']); ?></textarea>
                                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="purchase_date"><?php echo $this->lang->line('purchase_date'); ?></label><small class="req"> *</small>
                                                    <input id="purchase_date" name="purchase_date" placeholder="<?php echo $this->lang->line('select_date'); ?>" type="text" class="form-control date"  value="<?php echo set_value('purchase_date', date($this->customlib->getSchoolDateFormat(), strtotime($asset['purchase_date']))); ?>" required/>
                                                    <span class="text-danger"><?php echo form_error('purchase_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price"><?php echo $this->lang->line('price'); ?></label><small class="req"> *</small>
                                                    <input id="price" name="price" placeholder="<?php echo $this->lang->line('enter_price'); ?>" type="number" class="form-control"  value="<?php echo set_value('price', $asset['price']); ?>" required/>
                                                    <span class="text-danger"><?php echo form_error('price'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="residu"><?php echo $this->lang->line('residu'); ?></label>
                                                    <input id="residu" name="residu" placeholder="<?php echo $this->lang->line('enter_residu'); ?>" type="number" class="form-control"  value="<?php echo set_value('residu', $asset['residu']); ?>" required/>
                                                    <span class="text-danger"><?php echo form_error('residu'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="aging"><?php echo $this->lang->line('aging'); ?></label><small class="req"> *</small>
                                                    <input id="aging" name="aging" placeholder="<?php echo $this->lang->line('enter_aging'); ?>" type="number" class="form-control"  value="<?php echo set_value('aging', $asset['aging']); ?>" required/>
                                                    <span class="text-danger"><?php echo form_error('aging'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="metode"><?php echo $this->lang->line('metode'); ?></label><small class="req"> *</small>
                                                    <select id="metode" name="metode" class="form-control" required>
                                                        <option value="1" <?php echo (set_value('metode', $asset['metode']) == 1) ? 'selected' : ''; ?>>Garis Lurus</option>
                                                        <option value="0" <?php echo (set_value('metode', $asset['metode']) == 0) ? 'selected' : ''; ?>>Tanpa Penyusutan</option>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('metode'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('depresiasi'); ?>/Bulan</label>
                                                    <?php 
                                                    // Calculate monthly depreciation for display
                                                    $monthly_depreciation = 0;
                                                    if ($asset['metode'] == 1 && $asset['aging'] > 0) {
                                                        $monthly_depreciation = $asset['depresiasi'] / 12;
                                                    }
                                                    ?>
                                                    <p class="form-control-static" id="depresiasi_value"><?php echo number_format($monthly_depreciation, 2); ?></p>
                                                    <input type="hidden" id="depresiasi" name="depresiasi" value="<?php echo set_value('depresiasi', $asset['depresiasi']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('accumulated_depresiasi'); ?></label>
                                                    <p class="form-control-static"><?php echo $currency_symbol . (isset($asset['accumulated_depreciation']) ? $asset['accumulated_depreciation'] : '0.00'); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('current_book_value'); ?></label>
                                                    <p class="form-control-static"><?php echo $currency_symbol . (isset($asset['current_book_value']) ? $asset['current_book_value'] : $asset['price']); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('purchase_date'); ?></label>
                                                    <p class="form-control-static"><?php echo set_value('purchase_date', date('d-m-Y', strtotime($asset['purchase_date']))); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </section>
</div>
<script>
    $(document).ready(function () {
        $('.date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        
        // Calculate depreciation when price, residu, aging, or metode changes
        $('#price, #residu, #aging, #metode').on('input change', function() {
            calculateDepreciation();
        });
        
        // Calculate initial depreciation
        calculateDepreciation();
        
        function calculateDepreciation() {
            var price = parseFloat($('#price').val()) || 0;
            var residu = parseFloat($('#residu').val()) || 0;
            var aging = parseFloat($('#aging').val()) || 0;
            var metode = $('#metode').val();
            
            var depresiasi = 0;
            // If metode is "Garis Lurus" (value = 1) and aging > 0, calculate depreciation
            if (metode == '1' && aging > 0) {
                depresiasi = (price - residu) / aging;
            }
            
            // Convert to monthly depreciation for display
            var monthly_depresiasi = depresiasi / 12;
            $('#depresiasi_value').text(monthly_depresiasi.toFixed(2));
            $('#depresiasi').val(depresiasi.toFixed(2)); // Keep annual value in hidden field
        }
    });
</script>