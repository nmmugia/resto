<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
<form id="formFilter" method="get" target="_blank">
    <input type="hidden" id="report_type" value="sales_category" name="type"/>
    <div class="clear-export"></div>
            <table class="col-lg-8 form-table-cms">
                <tbody>
					<tr>
                        <td><label><?php echo $this->lang->line('outlet_title');?></label></td>
                        <td class="col-sm-8">
                           <?php 
                           echo form_dropdown('outlet_id', $all_outlet, '', 
                            'id="outlet_id" field-name = "Outlet" 
                            class="form-control" autocomplete="on"');
                            ?>
                            
                        </td>
                        
                    </tr>
					<tr>
                        <td><label>Kategori</label></td>
                        <td class="col-sm-8">
                           <?php 
                           echo form_dropdown('category_menu_id', $all_category, '', 
                            'id="category_menu_id" field-name = "Outlet" 
                            class="form-control" autocomplete="on"');
                            ?>
                            
                        </td>
                        
                    </tr>
                    <tr>
                        <td><label>Waktu Mulai</label></td>
                        <td class="col-sm-8">
                            <div class='input-group date ' id='start_date'>
                             <?php echo form_input(array('name' => 'start_date',
                               'id' => 'input_start_date',
                               'type' => 'text',
                               'class' => 'form-control date',
                               'onkeydown'=>'return false',
							   'value'=>date("Y-m-d")." 00:00",

                               )); ?>
                               <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                        </div> 
                    </td>
             
                </tr>
                <tr>
                    <td><label>Waktu Akhir</label></td>
                    <td class="col-sm-8"><!-- div class="col-sm-6">
                        <input id="end_date" type="text" name="end_date"  class="date-input form-control"/>
                    </div> -->


                    <div class='input-group date ' id='end_date'>
                       <?php echo form_input(array('name' => 'end_date',
                         'id' => 'input_end_date',
                         'type' => 'text',
                         'class' => 'form-control date',
                         'onkeydown'=>'return false',
						 'value'=>date("Y-m-d")." 23:59",

                         )); ?>
                         <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar">
                            </span>
                        </span>
                    </div> 

                </td>
                    
                </tr>
                
                

               
                <tr>
                    <td colspan="4" align="right">
                     <!--  <button id="export_xls" class="btn btn-default hide_btn" style="float:right;display: none">Export XLS</button> -->
                     <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                        <button id="export_pdf" class="btn btn-success hide_btn" style="display: none"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                        
                    </td>
                </tr>
                </tbody>
            </table>
</form>

            <div class="clearfix"></div>
            </div>
			<div class="panel-body">

				<div class="clearfix"></div>

				<table class="table table-striped table-bordered table-hover dt-responsive" id="table-sales-category">
					<thead>
						<tr>
							<th><?php echo $this->lang->line('outlet_title');?></th>
							<th>Kategori</th>
							<th>Banyak</th>
							<th>Grand Total</th>
						</tr>
					</thead>
				   <tbody></tbody>
				</table>

				<input type="hidden" id="dataProcessUrl"
					   value="<?php echo $data_url; ?>"/>

				<!-- /.table-responsive -->
			</div>
    </div>
</div>