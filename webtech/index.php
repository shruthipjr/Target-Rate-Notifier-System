<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
  <style>
    
    .navbar{
      background: url(background.png) no-repeat center top;
      font-size: 24px !important;
      line-height: 1.42857143 !important;
      border-radius: 0;
      font-family: Montserrat, sans-serif;
    }
    .navbar-default {
      border-color: #fff;
    }
    ul#myNav {
      margin-left: 10%;

    }
    .container.my-context {
      background-color: rgba(228, 107, 107, 0.79);
      min-height: 50%;
    }
    .logo{
      border-bottom: 1px solid #E4B46C;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-default">
<div class="row">
  <div class="col-md-6 col-md-push-1">
    
  </div>
 
</div>
<div class="row">
  <div class="text-center logo"><h3><b>Target Rate Notify System</b></h3></div>
</div>
  <ul id="myNav" class="nav navbar-nav">
    <li class="active"><a href="./index.php">Home</a></li>
  </ul>
</nav>
<div class="container my-context">
<div class="row">

<section>
  <div>
  <div class="row">
  <div style="margin-left: 12%;padding: 1%">
    <?php
    error_reporting(0);
    $errorMessage = "";

   

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
        //$target = round($target, 2);
      }



      if(empty($errorMessage)) {
      //$i=1;
      //while($i>=0){
      $xml=simplexml_load_file("http://rates.fxcm.com/RatesXML") or die("Error: Cannot create object");
      foreach($xml->children() as $rate) {
          $xmlCurrency = $rate->attributes()->Symbol;
          $high = (float) $rate->High;
          $low = (float) $rate->Low;
          //$high = round($high, 2);

          if ($target < $low || $target > $high) {
	      	$errorMessage .= "<li style='color:red;' class='active'>Target must be within range from ".$low." to ".$high."</li>";
	      	break;
	      }
            else if(($currency == $xmlCurrency) and ($high  >= $target)) {
              echo "<h1>The target has reached at ".$target."<br><br>";
              //echo "Target: ".$target."<br>";
              echo "------------------------------------------------------------<br>";
              //echo "Root Name: ".$xml->getName() . "<br>";
              echo "<div id='result'>Currency Name : ".$xmlCurrency. "<br>";
              echo "Bid: ".$rate->Bid . "<br> "; 
              echo "Rate: ".$rate->Ask . "<br> ";
              echo "High: ".$rate->High . "<br> ";
              echo "Low: ".$rate->Low. " <br>";
              echo "Direction: ".$rate->Direction. "<br>";
              echo "Last: ".$rate->Last . "<br><br></h1></div>";
              //exit(0);
              break;
            }
        }
      //$i=$i+1;
     //}
      } // if close
    }
?></div>
    <div class="col-md-offset-1"><h3><u>Please fill up the below details:</u></h3><br>
    <?php
    if(!empty($errorMessage)) 
    {
      echo("<h3 style='color: blue;'>There was an error with your form:</h3>\n");
      echo("<h4><ul>" . $errorMessage . "</ul></h4>\n");
    }
    ?>
</div>
    <div class="col-md-6"><hr>
      <form class="form-horizontal" id="SetUp_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
<!--         <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
          <div class="col-sm-10">
            <input type="text" name="email" class="form-control" placeholder="Email">
          </div>
        </div> -->
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Currency </label>
          <div class="col-sm-10">
            <input type="text" name="currency" class="form-control" placeholder="Currency Pair">
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Target</label>
          <div class="col-sm-10">
            <input type="text" name="target" class="form-control" placeholder="Target Rate">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <input type="submit" name="SetUp" class="btn btn-success" value="SetUp">
            <input type="reset" name="clear" class="btn btn-default" value="Clear">
          </div>
        </div>
      </form><hr>
    </div>
    </div>
  </div>
  </section>
  </div>  
</div>

<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript">
	$(function () {
		if (<?php echo (empty($errorMessage) && $_POST['SetUp'] == "SetUp") ? "true" : "false" ?>) {
			setInterval(function() {
				$.ajax({
					url: 'get-current-rates.php',
					type: 'POST',
					dataType: 'json',
					data: JSON.stringify({
						SetUp: 'SetUp',
						currency: '<?php echo empty($currency) ? "" : $currency ?>',
						target: <?php echo empty($target) ? "-1" : $target ?>
					}),
					success: function(data) {
						//console.log(data);
						if (data.rate) {
							$('#result').html("Last checked on: " + new Date() + "<br><br>" + 
								"Currency Name : " + data.currency['0'] + "<br>" +
					              "Bid: " + data.rate.Bid + "<br> " +
					              "Rate: " + data.rate.Ask + "<br> " +
					              "High: " + data.rate.High + "<br> " +
					              "Low: " + data.rate.Low + " <br>" +
					              "Direction: " + data.rate.Direction + "<br>" +
					              "Last: " + data.rate.Last + "<br><br></h1>");
						}
					}
				});
			}, 5000);
		}
	})
</script>

</body>
</html>


