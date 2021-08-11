<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<form method="post" class="form-horizontal form-ajax">
<div class="col-lg-12" style="padding: 0 !important">
    <div class="result">
        <?php
        if (! empty($message_success)) {
            echo '<div class="alert alert-success" role="alert">';
            echo $message_success;
            echo '</div>';
        }
        if (! empty($message)) {
            echo '<div class="alert alert-danger" role="alert">';
            echo $message;
            echo '</div>';
        }
        ?>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="supplier_name" class="col-sm-2 control-label">Restoran</label>
                                <div class="col-sm-10">
                                    <select class="def-select form-control" name="supplier">
                                        <?php foreach($stores as $store): ?>
                                            <option value="<?php echo $store->id; ?>" <?php echo $store_id == $store->id ? 'selected' : ''; ?>><?php echo $store->store_name; ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Tanggal</label>
                                <div class="col-sm-10">
                                    <div class='input-group date' id='purchase_date'>
                                        <?php echo form_input(array('name' => 'order_date',
                                            'id' => 'purchase_date_val',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'Tanggal',

                                            )); ?>
                                        <span class="input-group-addon" style="cursor:pointer">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">No Dokumen</label>

                                <div class="col-sm-10">
                                    <input placeholder="No Dokumen" placeholder="No Dokumen" value="<?php echo set_value('po_number') ?>" name="po_number" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Keterangan</label>

                                <div class="col-sm-10">
                                    <textarea placeholder="Keterangan" name="description" cols="40" rows="5" type="text" class="form-control" field-name="Alamat resto"><?php echo set_value('description') ?></textarea>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-general-journal">
                                <thead>
                                <tr>
                                    <th>Cari akun</th>
                                    <th class="col-md-1">Kode</th>
                                    <th class="col-md-2">Nama Akun</th>
                                    <th class="col-md-2">Debit</th>
                                    <th class="col-md-2">Kredit</th>
                                    <th>Info</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $total_debit=0;
                                        if(isset($debits) && is_array($debits)):
                                            foreach($debits as $account_id => $debit):
                                                $total_debit+=$debit;
                                    ?>
                                        <tr>
                                            <td><i class="fa fa-search" style="cursor:pointer"></i></td>
                                            <td><?php echo $code[$account_id]; ?><input type="hidden" name="code[<?php echo $account_id; ?>]" value="<?php echo $code[$account_id]; ?>" /></td>
                                            <td><?php echo $name[$account_id]; ?><input type="hidden" name="name[<?php echo $account_id; ?>]" value="<?php echo $name[$account_id]; ?>" /></td>
                                            <td><input type="text" name="debit[<?php echo $account_id; ?>]" class="form-control debit" value="<?php echo $debit; ?>"/></td>
                                            <td><input type="text" name="credit[<?php echo $account_id; ?>]" class="form-control credit" disabled/></td>
                                            <td><input type="text" name="info[<?php echo $account_id; ?>]" class="form-control" value="<?php echo isset($info[$account_id]) ? $info[$account_id] : ''; ?>"/></td>
                                            <td><a href="#" class="delete-row-account">Hapus</a></td>
                                        </tr>
                                    <?php
                                            endforeach;
                                        endif;
                                    ?>
                                    <?php
                                        $total_credit=0;
                                        if(isset($credits) && is_array($credits)):
                                            foreach($credits as $account_id => $credit):
                                                $total_credit+=$credit;
                                    ?>
                                        <tr>
                                            <td><i class="fa fa-search" style="cursor:pointer"></i></td>
                                            <td><?php echo $code[$account_id]; ?><input type="hidden" name="code[<?php echo $account_id; ?>]" value="<?php echo $code[$account_id]; ?>" /></td>
                                            <td><?php echo $name[$account_id]; ?><input type="hidden" name="name[<?php echo $account_id; ?>]" value="<?php echo $name[$account_id]; ?>" /></td>
                                            <td><input type="text" name="debit[<?php echo $account_id; ?>]" class="form-control debit" disabled placeholder="debit"/></td>
                                            <td><input type="text" name="credit[<?php echo $account_id; ?>]" class="form-control credit" value="<?php echo $credit; ?>" placeholder="credit"/></td>
                                            <td><input type="text" name="info[<?php echo $account_id; ?>]" class="form-control" value="<?php echo isset($info[$account_id]) ? $info[$account_id] : ''; ?>" placeholder="info"/></td>
                                            <td><a href="#" class="delete-row-account">Hapus</a></td>
                                        </tr>
                                    <?php
                                            endforeach;
                                        endif;
                                    ?>
                                    <tr id="sticky-search">
                                        <td><i class="fa fa-search account-pop" style="cursor:pointer"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col-sm-offset-9">
                                <table id="summary">
                                    <tr>
                                        <td class="col-md-3">Total Debit</td>
                                        <td class="col-md-3">: <span id="total-debit"><?php echo $total_debit; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-3">Total Kredit</td>
                                        <td class="col-md-3">: <span id="total-credit"><?php echo $total_credit; ?></span></td>
                                    </tr>
                                </table>
                            </div>
                            <input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
                            <div class="clearfix"></br></div>
                            <div class="form-group">
                                <div class="col-sm-offset-10 col-sm-10">
                                    <button type="submit" name="btnAction" value="save"
                                            class="btn btn-primary" id="save-journal">Simpan
                                    </button>
                                    <a href="#"
                                       class="btn btn-default">Batal</a>
                                </div>
                            </div>

                        </div>
                        <!-- /.row (nested) -->
                    </div>

                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
        </div>
    </div>
</div>
</form>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>