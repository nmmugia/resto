<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
?>
<div class="col-lg-12">
    <div class="panel panel-default"> 
        <div class="panel-body"> 
            <div class="result">
                <?php
                if (!empty($message_success)) {
                    echo '<div class="alert alert-success" role="alert">';
                    echo $message_success;
                    echo '</div>';
                }
                if (!empty($message)) {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo $message;
                    echo '</div>';
                }
                ?>
            </div>
             <div class="row">
                <div class="col-lg-12 form-group">
                     <div class="col-md-4">
                        <label for="floor_name" class=" control-label">  Jatah Cuti</label> 
                    </div> 
                     <div class="col-md-6">
                     <?php  
                     if(!empty($history_jobs)){
                        echo $history_jobs->vacation;
                     }else{
                      echo "0";
                     }
                      
                      ?> Hari
                    </div>  
                </div> 
            </div>
            <div class="row">
                <div class="col-lg-12 form-group">

                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Sisa Jatah Cuti</label> 
                    </div> 
                     <div class="col-md-2">
                      <?php  
                      if($history_jobs){
                         $sisa = $history_jobs->vacation ;
                         if($an_life_taken_total) $sisa = $history_jobs->vacation - $an_life_taken_total->day_total; 
                          echo $sisa ." Hari";
                        ?>
                         <a href="<?php echo base_url('admincms/hrd_schedule/history_holiday/'.$data_users->id); ?>">Detail</a> 
                        <?php
                      }else{
                        echo "0"." Hari";
                      }
                       
                        ?> 
                    </div>  
                     <div class="col-md-2">
                    
                    </div>  
                </div> 
            </div>
             <div class="row">
                <div class="col-lg-12 form-group">

                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Jatah Reimburse</label> 
                    </div> 
                     <div class="col-md-6"> Rp.
                    <?php    
                    if($history_jobs){
                     echo $history_jobs->reimburse;  
                    }else{
                      echo "0";
                    }
                    ?>  
                    </div>  
                </div> 
            </div>
             <div class="row">
                <div class="col-lg-12 form-group">

                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Sisa Jatah Reimburse</label> 
                    </div> 
                     <div class="col-md-6">Rp.
                        <?php  
                        if($history_jobs){
                           $sisa_reimburse =  $history_jobs->reimburse;
                          if($reimburse_taken_total) $sisa_reimburse = $history_jobs->reimburse - $reimburse_taken_total->total;
                         if($sisa_reimburse < 0) $sisa_reimburse = 0;
                         echo $sisa_reimburse;
                        }else{
                          echo "0";
                        }
                       
                        ?>
                    </div>  
                </div> 
            </div> 
        </div> 
    </div>
</div>