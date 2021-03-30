<?php
  session_start();
  
  include("constant.php");
  
  date_default_timezone_set("Asia/Bangkok");
  
  //CONNECTION
  function PDOConnector(){
	try {
	  $conn = new PDO('mysql:host='.DB_SER.';dbname='.DB_NAME.'', DB_USR, DB_PWD);
	  //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	  $conn->exec("set names utf8");
	  return $conn;
	}catch(PDOException $e){ return null;}
  }
  
  // ENCODE
  function encode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
	$j = 0;
	$hash = "";
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string,$i,1));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
    }
    return $hash;
  }
  
  // DECODE
  function decode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
	$j = 0;
	$hash = "";
    for ($i = 0; $i < $strLen; $i+=2) {
        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    return $hash;
  }
  
  $conn=PDOConnector();
  
  $result="";
  if ($_SERVER["REQUEST_METHOD"]=="POST") {
	$email=$_POST["email1"];
	$password=$_POST["password1"];
	
    $comm="SELECT * FROM organization WHERE email='".$_POST["email1"]."' AND password='".$password."' AND active=1 LIMIT 1";
	//echo $comm;
	$query=$conn->prepare($comm); 
	$query->execute();
	
	if($query->rowCount()>0){
	  $row=$query->fetch();
	  $_SESSION["AUTHEN"]["ID"]=$row["id"];
	  $_SESSION["AUTHEN"]["ORGANIZATION_ID"]=$row["organizationId"];
	  $_SESSION["AUTHEN"]["FULLNAME"]=$row["fullname"];
		  
	  header("Location: vaccine.management.php");
	  die();
	 
	}else{
	  $result="0";
	}
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo APPLICATION_NAME; ?> | Login</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Prompt:300,400,600,700,300italic,400italic,600italic">
  <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sarabun:300,400,600,700,300italic,400italic,600italic">-->
</head>
<body class="hold-transition login-page">
<div class="login-box" style="width: 500px">
  <div class="login-logo">
    <a href="#">เข้าสู่ระบบ</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">

	<div id="result" name="result" class="callout callout-danger" style="display:<?php echo ($result=='0'?'block':'none'); ?>" > ข้อมูลการเข้าระบบฯ ไม่ถูกต้อง ลองอีกครั้ง 
    </div> <!--style="display:none;"-->
	
    <p class="login-box-msg"><!--Sign in to start your session--></p>

    <form id="frm" name="frm" role="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <div class="form-group has-feedback">
        <input id="email1" name="email1" type="email" class="form-control" placeholder="อีเมล์">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="password1" name="password1" type="password" class="form-control" placeholder="รหัสผ่าน">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
		  <a href="entries.php"> ขึ้นทะเบียนบริษัท/องค์กรเอกชน/หน่วยงาน </a><br/>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button id="submit" name="submit" type="submit" class="btn btn-primary btn-block btn-flat">เข้าระบบ</button>
        </div>
        <!-- /.col -->
      </div>
	  <br/><br/>
	  <div class="row">
		<div class="col-xs-8">
		    แบบฟอร์มกรอกประวัติผู้ป่วยใหม่
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <a id="download1" name="download1" href="files/new_patient.docx" target="_blank" class="btn btn-success btn-block btn-flat"><i class="fa fa-download"></i> ดาวน์โหลด</a>
        </div>
        <!-- /.col -->
      </div>
	  <br/>
	  <div class="row">
		<div class="col-xs-8">
		      แบบคัดกรองและใบยินยอมฉีดวัคซีนโควิด 19 
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <a id="download2" name="download2" href="files/consent.pdf" target="_blank" class="btn btn-success btn-block btn-flat"><i class="fa fa-download"></i> ดาวน์โหลด</a>
        </div>
        <!-- /.col -->
      </div>
	  <br/>
	  <br/>
	  <br/>
	  <div class="row">
		<div class="col-xs-12">
		     สอบถามข้อสงสัยเกี่ยวกับระบบนำส่งรายชื่อผู้ฉีดวัคซีนของกิจการบนเวปไซด์ได้ที่ Line : @155lmkob
			<br/>
			<br/>
			ส่วนคำถามเกี่ยวกับวัคซีน และ การจัดคิวฉีดวัคซีน กรุณาติดต่อ สำนักงานสาธารณสุขจังหวัดภูเก็ต โทร 094-5938876 หรือ 094-5935342
        </div>
        <!-- /.col -->
      </div>
    </form>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<script>
  $(document).ready(function () {
    
	$('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    })
	
	$('#email1').change(function(){
      HideMsg();
    })
	
	$('#email1').keypress(function(){
	  if($('#email1').val()==''){
        HideMsg();
	  }
    })
	
	$('#password1').change(function(){
      HideMsg();
    })
	
	$('#password1').keypress(function(){
	  if($('#password1').val()==''){
        HideMsg();
	  }
    })
	
	$('#frm').validate({
	  rules: {
        email1: {
          required: true,
		  email: true
        },
		password1: {
		  required: true
		}
      },
	  
	  messages: {
		email1: {
          required: 'กรุณาระบุข้อมูลอีเมล',
		  email: 'กรุณารตรวจสอบรูปแบบอีเมล'
        },
		password1: {
          required: 'กรุณาระบุข้อมูลรหัสผ่าน'
        }
	  },
	  
	  highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
      },
      unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-error');
      },

      errorElement: 'span',
      errorClass: 'help-block',
      errorPlacement: function(error, element) {
        if(element.parent('.input-group').length) {
          error.insertAfter(element.parent());
        }else{
          error.insertAfter(element);
        }
      }
	  
	})
	
  });
  
  function HideMsg(){
    $("#result").css("display", "none");
  }
  
</script>
</body>
</html>
