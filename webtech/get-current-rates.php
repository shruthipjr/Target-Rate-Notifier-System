<?php
    error_reporting(0);
    $errorMessage = "";

    $_POST = json_decode(file_get_contents('php://input'), true);

    if($_POST['SetUp'] == "SetUp"){


    	
      if(empty($_POST['currency'])) {
        $errorMessage .= "<li style='color:red;' class='active'>Required currency!</li>";
      }else{
        $currency=$_POST["currency"];
        $currency=trim($currency, ' ');
      }
      
      if(empty($_POST['target'])) {
        $errorMessage .= "<li style='color:red;' class='active'>Required target!</li>";
      }else{
        $target = $_POST["target"];
        $target = trim($target, ' ');
        $target = (float) $target;
        $target = round($target, 2);
      }
      if(empty($errorMessage)) {
      //$i=1;
      //while($i>=0){
      $xml=simplexml_load_file("http://rates.fxcm.com/RatesXML") or die("Error: Cannot create object");
      $resp = new stdClass();
      foreach($xml->children() as $rate) {
          $xmlCurrency = $rate->attributes()->Symbol;
          $high = (float) $rate->High;
          $high = round($high, 2);

          $resp->currency = $xmlCurrency;
          $resp->high = $high;

            if(($currency == $xmlCurrency) and ($high  >= $target)) {
              // echo "<h1>The target reached at ".$target."<br><br>";
              // echo "Target: ".$target."<br>";
              // //echo "Root Name: ".$xml->getName() . "<br>";
              // echo "Currency Name : ".$xmlCurrency. "<br>";
              // echo "Bid: ".$rate->Bid . "<br> "; 
              // echo "Rate: ".$rate->Ask . "<br> ";
              // echo "High: ".$rate->High . "<br> ";
              // echo "Low: ".$rate->Low. " <br>";
              // echo "Direction: ".$rate->Direction. "<br>";
              // echo "Last: ".$rate->Last . "<br><br></h1>";

              $resp->rate = $rate;

              break;
            }
        }
      //$i=$i+1;
     //}
      } // if close
    }

    echo json_encode($resp);
?>