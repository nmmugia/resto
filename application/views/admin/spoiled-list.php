<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
			<div class="col-sm-3">
				<select class="form-control select2" id="filter_outlet_id">
					<option value="">Semua Outlet</option>
					<?php foreach($outlet_lists as $o): ?>
					<option value="<?php echo $o->id ?>"><?php echo $o->outlet_name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-sm-3">
				<select class="form-control select2" id="filter_inventory_id">
					<option value="">Semua Inventory</option>
					<?php foreach($inventory_lists as $o): ?>
					<option value="<?php echo $o->id ?>"><?php echo $o->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-sm-2">
				<button type="button" id="trigger_filter_spoiled" class="btn btn-primary">Cari</button>
			</div>
      <a id="add-siblings" href="#" class="btn btn-primary pull-right " data-toggle="modal" data-target="#spoiled-modal"><i class='fa fa-plus'></i> Tambah Spoiled</a>
      <div class="clearfix"></div>
    </div>
    <div class="panel-body">
      <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-spoiled">
        <thead>
        <tr>
          <th >Outlet</th>
          <th width="50%">Nama Inventory</th>
					<th>Stok Spoiled</th>
          <th>Cost Spoiled</th>
        </tr>
        </thead>
      </table>
      <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url ?>"/>
    </div>
  </div>
</div>
<div class="modal fade" id="spoiled-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Tambah Stok Spoiled</h4>
      </div>
      <div class="modal-body"> 
				<div class="row">
          <div class="col-lg-12 form-group">
            <div class="col-md-4">
                <label for="floor_name" class=" control-label"><?php echo $this->lang->line('outlet_title');?></label> 
            </div> 
            <div class="col-md-6">
              <select name="from" class="form-control " data-width="100%" id="spoiled_outlet_id">
                <option value="">Pilih <?php echo $this->lang->line('outlet_title');?></option>
                <?php foreach ($outlet_lists as $o) { ?>
                  <option value='<?php echo $o->id?>'><?php echo $o->outlet_name;?></option>
                <?php }?>           
              </select>
            </div> 
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 form-group">
            <div class="col-md-4">
                <label for="floor_name" class=" control-label">Bahan</label> 
            </div> 
            <div class="col-md-6">
              <select name="from" class="form-control " data-width="100%" id="spoiled_inventory_id">
                <option value="">Pilih Inventory</option>
                <?php foreach ($inventories as $inventory) { ?>
                  <option value='<?php echo $inventory->id?>' uom_id="<?php echo $inventory->uom_id ?>"><?php echo $inventory->name;?></option>
                <?php }?>           
              </select>
            </div> 
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 form-group">
            <div class="col-md-4">
                <label for="floor_name" class=" control-label">Satuan</label> 
            </div> 
            <div class="col-md-6">
              <select name="uom_id" class="form-control "  data-width="100%" id="uom_id">
                <option value="">Pilih Satuan</option>
              </select>
            </div> 
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 form-group">
            <div class="col-md-4">
                <label for="floor_name" class=" control-label" >Quantity</label> 
            </div> 
            <div class="col-md-6">
                  <input type="text" id="quantity" class="form-control" placeholder="Masukan Quantity">
            </div>   
          </div>
        </div>
				<div class="row">
          <div class="col-lg-12 form-group">
            <div class="col-md-4">
                <label for="floor_name" class=" control-label">Keterangan</label> 
            </div> 
            <div class="col-md-6">
                  <textarea id="description" rows="5" class="form-control"></textarea>
            </div>   
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary"  id="save-spoiled" data-action='save'>Simpan</button>
      </div>
    </div>
  </div>
</div>