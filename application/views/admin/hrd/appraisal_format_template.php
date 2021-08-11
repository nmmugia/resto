 <div class="col-lg-6 col-sm-offset-3">
    <div class="panel panel-default">
        <div class="panel-body"> 
    <input type="hidden" id="max_grade" value="<?php echo $max_grade;?>">
        <?php foreach ($data_categories as $data) { ?> 
            <div class="col-lg-12" id="category-0">
                <div class="form-group">
                    <label for="floor_name" class="col-sm-4 control-label">Nama Kategori</label> 
                    <div class="col-sm-8 control-point">
                        <?php  
                        echo $data->name_category;?>
                    </div>
                </div>   
                <div class="col-lg-12" id="detail-container-0">
                <?php  
                    foreach ($data->detail as $detail) { ?> 
                    <div class="panel panel-default" id="category-detail-0">
                        <div class="panel-body">
                            <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="floor_name" class="col-sm-2 control-label">Nama</label> 
                                    <div class="col-sm-10 control-point">
                                     <?php echo $detail->name;?>
                                    </div>
                                </div>    
                            </div>
                             <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="floor_name" class="col-sm-2 control-label">Max Point</label> 
                                    <div class="col-sm-10 control-point">
                                        <div class="slider-range-min-app" 
                                                min ="0"
                                                max ="<?php echo $detail->point;?>"  
                                                child ="<?php echo $data->id;?>-<?php echo $detail->id;?>"
                                                id ="slide-<?php echo $data->id;?>-<?php echo $detail->id;?>"
                                                >
                                        </div>
                                        <input type="text" 
                                                name ="amount[<?php echo $data->id;?>][<?php echo $detail->id;?>]" 
                                                class ="point" 
                                                id ="<?php echo $data->id;?>-<?php echo $detail->id;?>"  
                                                style="border:0; color:#f6931f; font-weight:bold;"
                                                readonly
                                                >
                                        
                                    </div>
                                </div>    
                            </div> 
                        </div>   
                    </div>   
                    <?php }?>
                </div>   
            </div>   
        <?php } ?> 
        </div>  
    </div>  
</div> 
