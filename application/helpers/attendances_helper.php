<?php
  function Parse_Data($data,$p1,$p2){
    $data=" ".$data;
    $hasil="";
    $awal=strpos($data,$p1);
    if($awal!=""){
      $akhir=strpos(strstr($data,$p1),$p2);
      if($akhir!=""){
        $hasil=substr($data,$awal+strlen($p1),$akhir-strlen($p1));
      }
    }
    return $hasil;	
  }
  function request($Connect,$soap_request)
  {
    $newLine="\r\n";
    fputs($Connect, "POST /iWsService HTTP/1.1".$newLine);
    fputs($Connect, "Content-Type: text/xml".$newLine);
    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
    fputs($Connect, $soap_request.$newLine);
    $buffer="";
    while($Response=fgets($Connect, 1024)){
      $buffer=$buffer.$Response;
    }
    return $buffer;
  }
  function upload_to_fingerprint($data=array())
  {
    $Key="0";
    $Connect = fsockopen($data['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN>".$data['ID']."</PIN><Name>".$data['NAME']."</Name></Arg></SetUserInfo>";
      $buffer=request($Connect,$soap_request);
    }
    return true;
  }
  function get_all_user_fingerprint($param=array())
  {
    $result=array();
    $Key="0";
    $Connect = fsockopen($param['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<GetAllUserInfo><ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey></GetAllUserInfo>";
      $buffer=request($Connect,$soap_request);
      $buffer=Parse_Data($buffer,"<GetAllUserInfoResponse>","</GetAllUserInfoResponse>");
      $buffer=explode("\r\n",$buffer);
      for($a=0;$a<count($buffer);$a++){
        $data=Parse_Data($buffer[$a],"<Row>","</Row>");
        $pin=Parse_Data($data,"<PIN2>","</PIN2>");
        $name=Parse_Data($data,"<Name>","</Name>");
        $privilege=Parse_Data($data,"<Privilege>","</Privilege>");
        if($pin!="")
        {
          array_push($result,array(
            "PIN" => $pin,
            "NAME" => $name,
            "PRIVILEGE" => $privilege
          ));
        }
      }
    }
    return $result;
  }
  function delete_user_fingerprint($data=array())
  {
    $Key="0";
    $Connect = fsockopen($data['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<DeleteUser><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">".$data['ID']."</PIN></Arg></DeleteUser>";
      $buffer=request($Connect,$soap_request);
    }
  }
  function refresh_fingerprint($data=array())
  {
    $Key="0";
    $Connect = fsockopen($data['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<RefreshDB><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey></RefreshDB>";
      $buffer=request($Connect,$soap_request);
    }
  }
  function upload_template_user_fingerprint($data=array())
  {
    $Key="0";
    $Connect = fsockopen($data['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<SetUserTemplate><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">".$data['PIN']."</PIN><FingerID xsi:type=\"xsd:integer\">".$data['FINGER_ID']."</FingerID><Template>".$data['TEMPLATE']."</Template><Size>".$data['SIZE']."</Size><Valid>".$data['VALID']."</Valid></Arg></SetUserTemplate>";
      $buffer=request($Connect,$soap_request);
    } 
  }
  function get_template_user_fingerprint($data=array())
  {
    $result=array();
    $Key="0";
    $Connect = fsockopen($data['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<GetUserTemplate><ArgComKey xsi:type=\"xsd:integer\">ComKey</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetUserTemplate>";
      $buffer=request($Connect,$soap_request);
      $buffer=Parse_Data($buffer,"<GetUserTemplateResponse>","</GetUserTemplateResponse>");
      $buffer=explode("\r\n",$buffer);
      for($a=0;$a<count($buffer);$a++){
        $data=Parse_Data($buffer[$a],"<Row>","</Row>");
        $pin=Parse_Data($data,"<PIN>","</PIN>");
        $finger_id=Parse_Data($data,"<FingerID>","</FingerID>");
        $size=Parse_Data($data,"<Size>","</Size>");
        $valid=Parse_Data($data,"<Valid>","</Valid>");
        $template=Parse_Data($data,"<Template>","</Template>");
        if($pin!="")
        {
          array_push($result,array(
            "PIN" => $pin,
            "FINGER_ID" => $finger_id,
            "SIZE" => $size,
            "VALID" => $valid,
            "TEMPLATE" => $template
          ));
        }
      }
    }
    return $result;
  }
  function delete_user_template_fingerprint($data=array())
  {
    $Key="0";
    $Connect = fsockopen($data['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<DeleteTemplate><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">".$data['PIN']."</PIN></Arg></DeleteTemplate>";
      $buffer=request($Connect,$soap_request);
    }    
  }
  function delete_all_user_fingerprint($data=array())
  {
    $Key="0";
    $Connect = fsockopen($data['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<ClearData><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><Value xsi:type=\"xsd:integer\">1</Value></Arg></ClearData>";
      $buffer=request($Connect,$soap_request);
      $soap_request="<ClearData><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><Value xsi:type=\"xsd:integer\">2</Value></Arg></ClearData>";
      $buffer=request($Connect,$soap_request);
      $soap_request="<ClearData><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><Value xsi:type=\"xsd:integer\">3</Value></Arg></ClearData>";
      $buffer=request($Connect,$soap_request);
    }
  }
  function clear_fingerprint($data=array())
  {
    $Key="0";
    $Connect = fsockopen($data['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<ClearData><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><Value xsi:type=\"xsd:integer\">3</Value></Arg></ClearData>";
      $newLine="\r\n";
      fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
        fputs($Connect, "Content-Type: text/xml".$newLine);
        fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
        fputs($Connect, $soap_request.$newLine);
      $buffer="";
      while($Response=fgets($Connect, 1024)){
        $buffer=$buffer.$Response;
      }
    }
  }
  function get_day_of_works()
  {
    $ci=&get_instance(); 
    $days=array();
    foreach($lists as $l):
      $l->monday==0 ? array_push($days,"Monday") : "";
      $l->tuesday==0 ? array_push($days,"Tuesday") : "";
      $l->wednesday==0 ? array_push($days,"Wednesday") : "";
      $l->thursday==0 ? array_push($days,"Thursday") : "";
      $l->friday==0 ? array_push($days,"Friday") : "";
      $l->saturday==0 ? array_push($days,"Saturday") : "";
      $l->sunday==0 ? array_push($days,"Sunday") : "";
    endforeach;
    return $days;
  }
  
  function connection($parameter=array())
  {
    $Key="0";
    $result=null;
    $Connect = fsockopen($parameter['IP'], "80", $errno, $errstr, 1);
    if($Connect){
      $soap_request="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
      $buffer=request($Connect,$soap_request);
      $buffer=Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
      $buffer=explode("\r\n",$buffer);
      $result=$buffer;
    }
    return $result;
  }
  function fingerprint_attendances($parameter=array())
  {

    $ci=&get_instance(); 
    $result=array();
    // if(!in_array(date("l"),$day_of_ot)){
      $buffer=connection(array("IP"=>$parameter["IP"]));
      // print_r($buffer);
      
      $result_all=array();

      if($buffer==false){
        $result=false;
      }else{
        // echo "<pre>";
        // print_r($buffer);
        if($buffer!=null){
          $result=array();
          $pin_data_in=array();
          $pin_data_out=array();
          for($a=0;$a<count($buffer);$a++){
            $data=Parse_Data($buffer[$a],"<Row>","</Row>");
            $pin=Parse_Data($data,"<PIN>","</PIN>");
            $status=Parse_Data($data,"<Status>","</Status>");
             
            
            $datetime=explode(" ",Parse_Data($data,"<DateTime>","</DateTime>"));
          
            if($pin!=null): 
              
                if(!isset($pin_data_in[$datetime[0]])) $pin_data_in[$datetime[0]] = array();
                if(!isset($result[$datetime[0]])) $result[$datetime[0]] = array();
                if(!in_array($pin, $pin_data_in[$datetime[0]])){
                  array_push($result[$datetime[0]],
                    array(
                      "user_id" => $pin, 
                      "date" => $datetime[0],
                      "time_in" => $datetime[1],
                      "time_out" => "",  
                      "status" => 0
                    )
                  );

                  


                  array_push($pin_data_in[$datetime[0]],$pin);
                }else{
                  $pos=array_search($pin, $pin_data_in[$datetime[0]]);
                  $result[$datetime[0]][$pos]["time_out"]=$datetime[1]; 
                }
             
            endif;
            
          }
          $result_all = array();
          foreach ($result as $key => $value) {
            foreach ($value as $key2 => $value2) {
               array_push($result_all, $value2);
            }
           
          }
        }
      }
    // }
    return $result_all;
  }
  
  function get_all_activities_fp($parameter=array())
  {
    $result=null;
    $buffer=connection(array("IP"=>$parameter['IP']));
    if($buffer==false){
      $result=false;
    }else{
      if($buffer!=null){
        $result=array();
        for($a=0;$a<count($buffer);$a++){
          $data=Parse_Data($buffer[$a],"<Row>","</Row>");
          $pin=Parse_Data($data,"<PIN>","</PIN>");
          $status=Parse_Data($data,"<Status>","</Status>");
          $datetime=explode(" ",Parse_Data($data,"<DateTime>","</DateTime>"));

          
          if($pin!=null): 
              array_push($result,
                array(
                  "user_id" => $pin,
                  "date" => $datetime[0],
                  "time" => $datetime[1],
                  "status" => $status
                )
              );
           
          endif;
          
        }
      }
    }
    return $result;
  }
?>