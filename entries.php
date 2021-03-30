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
  
  $conn=PDOConnector();
  
  $error=0;
  $msg="";
  if ($_SERVER["REQUEST_METHOD"]=="POST") {
	
    //$comm="SELECT * FROM organization WHERE organizationId='".$_POST["organizationId"]."' OR email='".$_POST["email"]."'";
	$comm="SELECT * FROM organization WHERE email='".$_POST["email"]."'";
	$query=$conn->prepare($comm); 
	$query->execute();
	
	if($query->rowCount()==0){
		
		$comm="INSERT INTO organization(organizationId, organizationName, businessName, businessType, houseNo, lane, subdistrict, road, villageNo, district, fullname, phone, email, password, active) VALUES(:organizationId, :organizationName, :businessName, :businessType, :houseNo, :lane, :subdistrict, :road, :villageNo, :district, :fullname, :phone, :email, :password, :active)";
		
		$query=$conn->prepare($comm);
		$result=$query->execute(array(
			"organizationId"=>$_POST["organizationId"],	
			"organizationName"=>$_POST["organizationName"],
			"businessName"=>$_POST["businessName"],	
			"businessType"=>$_POST["businessType"],
			"houseNo"=>$_POST["houseNo"],
			"lane"=>$_POST["lane"],
			"subdistrict"=>$_POST["subdistrict"],
			"road"=>$_POST["road"],
			"villageNo"=>$_POST["villageNo"],
			"district"=>$_POST["district"],
			"fullname"=>$_POST["fullname"],
			"phone"=>$_POST["phone"],
			"email"=>$_POST["email"],
			"password"=>$_POST["password"],
			"active"=>1
		));
		
		if($result){
			$_SESSION["AUTHEN"]["ID"]=$conn->lastInsertId();;
			$_SESSION["AUTHEN"]["ORGANIZATION_ID"]=$_POST["organizationId"];
			$_SESSION["AUTHEN"]["FULLNAME"]=$_POST["fullname"];
			
			header("Location: vaccine.management.php");
			die();
		}else{
			$error=1;
			$msg="ไม่สามารถทำการขึ้นทะเบียนได้";
		}
	}else{
		$error=1;
		$msg="ไม่สามารถทำการขึ้นทะเบียนได้ เนื่องจากในระบบมีเลขที่นิติบุคคลหรืออีเมลของท่านแล้ว";
	}
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo APPLICATION_NAME; ?> |  ขึ้นทะเบียนบริษัท/องค์กรเอกชน/หน่วยงาน</title>
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
  <style>
    .fontsize16px {
      font-size: 16px;
	}
	.fontsize24px {
      font-size: 24px;
	}
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box" style="width: 800px">
  <div class="login-logo">
    <a href="#" class="fontsize24px">ขึ้นทะเบียนผู้บันทึกข้อมูลขององค์กร</a>
  </div>
  
  <!-- /.login-logo -->
  <div class="login-box-body">

	<div id="error" name="error" class="callout callout-danger" style="display:<?php echo ($error==1?'block':'none'); ?>" ><?php echo $msg; ?></div>
	
    <p class="login-box-msg fontsize24px">ข้อมูลนิติบุคคล</p>

    <form id="frm" name="frm" role="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	  
	  <div class="row">
		<div class="col-md-6">
		  <div class="form-group">
			<span class="fontsize16px">เลขที่นิติบุคคล</span>
			<input id="organizationId" name="organizationId" type="text" class="form-control" placeholder="" data-inputmask='"mask": "9999999999999"' data-mask value="" >
		  </div>
		</div><!-- /.col -->
		<div class="col-md-6">
		  <div class="form-group">
			<span class="fontsize16px">ชื่อนิติบุคคล</span>
			<input id="organizationName" name="organizationName" type="text" class="form-control" placeholder="" value="">
		  </div>
		</div><!-- /.col -->
	  </div><!-- /.row -->
	  
	  <div class="row">
		<div class="col-md-6">
		  <div class="form-group">
			<span class="fontsize16px">ชื่อกิจการ</span>
			<input id="businessName" name="businessName" type="text" class="form-control" placeholder="" value="">
		  </div>
		</div><!-- /.col -->
		<div class="col-md-6">
		  <div class="form-group">
			<span class="fontsize16px">ประเภทกิจการ</span>
			  <select id='businessType' name='businessType' class='form-control select2' style='width: 100%;'>
				<option value='โรงแรม' >โรงแรม</option>
				<option value='ร้านอาหาร' >ร้านอาหาร</option>
				<option value='สปา' >สปา</option>
				<option value='นวดแผนไทย' >นวดแผนไทย</option>
				<option value='ห้างสรรพสินค้า' >ห้างสรรพสินค้า</option>
				<option value='สายการบิน' >สายการบิน</option>
				<option value='บริษัทนำเที่ยว' >บริษัทนำเที่ยว</option>
				<option value='ผับ' >ผับ</option>
				<option value='คาราโอเกะ' >คาราโอเกะ</option>
				<option value='ร้านกาแฟ' >ร้านกาแฟ</option>
				<option value='ร้านขายของที่ระลึก' >ร้านขายของที่ระลึก</option>
				<option value='สถานศึกษาภาครัฐและเอกชน' >สถานศึกษาภาครัฐและเอกชน</option>
				<option value='ธนาคาร สถาบันการเงิน' >ธนาคาร สถาบันการเงิน</option>
				<option value='ร้านขายยา' >ร้านขายยา</option>
			  </select>
		  </div>
		</div><!-- /.col -->
	  </div><!-- /.row -->
	  
	  <div class="row">
	    <div class="col-md-6">
		  <div class="form-group">
			<span class="fontsize16px">ที่อยู่ปัจจุบัน</span><br/>
			<small>เลขที่</small>
			<input id="houseNo" name="houseNo" type="text" class="form-control" placeholder="" value="">
		  </div>
		  <div class="form-group">
			<small>ตรอก / ซอย (หากไม่มี ให้ระบุ "-")</small>
			<input id="lane" name="lane" type="text" class="form-control" placeholder="" value="">
		  </div>
		  <div class="form-group">
			<small>ตำบล / แขวง</small>
			<select id='subdistrict' name='subdistrict' class='form-control select2' style='width: 100%;'>
										
			  <!-- อำเภอเมืองภูเก็ต -->
			  <option value='ตำบลกะรน' >ตำบลกะรน</option>
			  <option value='ตำบลฉลอง' >ตำบลฉลอง</option>
			  <option value='ตำบลตลาดเหนือ' >ตำบลตลาดเหนือ</option>
			  <option value='ตำบลตลาดใหญ่' >ตำบลตลาดใหญ่</option>
			  <option value='ตำบลรัษฎา' >ตำบลรัษฎา</option>
			  <option value='ตำบลราไวย์' >ตำบลราไวย์</option>
			  <option value='ตำบลวิชิต' >ตำบลวิชิต</option>
			  <option value='ตำบลวเกาะแก้ว' >ตำบลวเกาะแก้ว</option>
								
			  <!-- อำเภอกะทู้ -->
			  <option value='ตำบลกมลา' >ตำบลกมลา</option>
			  <option value='ตำบลกะทู้' >ตำบลกะทู้</option>
			  <option value='ตำบลป่าตอง' >ตำบลป่าตอง</option>
										
			  <!-- อำเภอถลาง -->
			  <option value='ตำบลป่าคลอก' >ตำบลป่าคลอก</option>
			  <option value='ตำบลศรีสุนทร' >ตำบลศรีสุนทร</option>
			  <option value='ตำบลสาคู' >ตำบลสาคู</option>
			  <option value='ตำบลเชิงทะเล' >ตำบลเชิงทะเล</option>
			  <option value='ตำบลเทพกระษัตรี' >ตำบลเทพกระษัตรี</option>
			  <option value='ตำบลไม้ขาว' >ตำบลไม้ขาว</option>
			</select>
		  </div>
		  <div class="form-group">
		  </div>
		</div><!-- /.col -->
		<div class="col-md-6">
		  <div class="form-group">
			<span class="fontsize16px"></span><br/>
			<small>ถนน (หากไม่มี ให้ระบุ "-")</small>
			<input id="road" name="road" type="text" class="form-control" placeholder="" value="">
		  </div>
		  <div class="form-group">
			<small>หมู่ที่ / ชุมชน (หากไม่มี ให้ระบุ "-")</small>
			<input id="villageNo" name="villageNo" type="text" class="form-control" placeholder="" value="" >
		  </div>
		  <div class="form-group">						
		  </div>
			<div class="form-group">
			<small>อำเภอ / เขต</small>
			<select id='district' name='district' class='form-control select2' style='width: 100%;'>
			  <option value='อำเภอเมืองภูเก็ต' >อำเภอเมืองภูเก็ต</option>
			  <option value='อำเภอกะทู้' >อำเภอกะทู้</option>
			  <option value='อำเภอถลาง' >อำเภอถลาง</option>
			</select>
		  </div>
	    </div><!-- /.col -->
	  </div><!-- /.row -->
	
	  <p class="login-box-msg fontsize24px">ข้อมูลติดต่อบุคคล</p>
	
	  <div class="form-group has-feedback">
	    <input id="fullname" name="fullname" type="text" class="form-control" placeholder="ชื่อ-นามสกุล">
		<span class="glyphicon glyphicon-user form-control-feedback"></span>
	  </div>
	  <div class="form-group has-feedback">
        <input id="phone" name="phone" type="text" class="form-control" placeholder="หมายเลขโทรศัพท์" data-inputmask='"mask": "9999999999"' data-mask>
        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="email" name="email" type="email" class="form-control" placeholder="อีเมล">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="password" name="password" type="password" class="form-control" placeholder="รหัสผ่าน">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
	  <div class="form-group has-feedback">
        <input id="repassword" name="repassword" type="password" class="form-control" placeholder="ยืนยันรหัสผ่าน">
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
		  <a href="index.php" class="btn btn-primary">กลับหน้าหลัก</a>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button id="submit" name="submit" type="submit" class="btn btn-primary btn-block btn-flat"> สมัคร </button>
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

<!-- InputMask -->
<script src="plugins/input-mask/jquery.inputmask.js"></script>
<script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<script>
  $(document).ready(function () {
	  
	$('[data-mask]').inputmask()
    
	$('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    })
	
	/*$('#email').change(function(){
      HideMsg();
    })
	
	$('#email').keypress(function(){
	  if($('#email').val()==''){
        HideMsg();
	  }
    })
	
	$('#phone').change(function(){
      HideMsg();
    })
	
	$('#phone').keypress(function(){
	  if($('#phone').val()==''){
        HideMsg();
	  }
    })
	
	$('#password').change(function(){
      HideMsg();
    })
	
	$('#password').keypress(function(){
	  if($('#password1').val()==''){
        HideMsg();
	  }
    })
	
	$('#repassword').change(function(){
      HideMsg();
    })
	
	$('#repassword').keypress(function(){
	  if($('#password1').val()==''){
        HideMsg();
	  }
    })
	*/
	
	$.validator.addMethod("isHouseNo", function(value, element) {
      if($('#houseNo').val().match(/^[0-9\.\-\/]+$/)) { 
        return true;
      }else{ 
        return false;
      }
    }, "กรอกเฉพาะตัวเลขและเครื่องหมาย / เท่านั้น");
	
	$.validator.addMethod("phone", function(value, element) {
	  var rexg = /^\d*(?:\.\d{1,2})?$/;
	  var phone=$('#phone').val();
	  
      if(rexg.test(phone) && phone.length == 10) {
        return true;
	  }else{
        return false;
      }
	}, "กรุณาระบุข้อมูลหมายเลขโทรศัพท์ให้ถูกต้อง");
	
	$('#frm').validate({
	  rules: {
        organizationId: {
          required: true
        },
		organizationName: {
          required: true
        },
		businessName: {
          required: true
        },
		houseNo: {
          required: true,
		  isHouseNo: true
        },
		road: {
          required: true
        },
		lane: {
          required: true
        },
		villageNo: {
		  required: true,
		},
		fullname: {
          required: true
        },
		phone: {
          required: true,
		  phone: true
        },
        email: {
          required: true,
		  email: true
        },
		password: {
		  required: true
		},
		repassword: {
		  required: true,
		  equalTo: "#password"
		},
		financialInstitute1: {
			financialInstitute: true
		}
      },
	  
	  messages: {
        organizationId: {
          required: "กรุณากรอกเลขที่นิติบุคคล"
        },
		 organizationName: {
          required: "กรุณากรอกชื่อนิติบุคคล"
        },
		businessName: {
          required: "กรุณากรอกชื่อกิจการ"
        },
		houseNo: {
          required: "กรอกเลขที่"
        },
		road: {
          required: "กรอกถนน"
        },
		lane: {
          required: "กรอกตรอก / ซอย"
        },
		villageNo: {
		  required: "กรอกหมู่ที่/ชุมชน"
		},
		fullname: {
          required: "กรุณาระบุข้อมูลชื่อ-นามสกุล"
        },
		phone: {
          required: "กรุณาระบุหมายเลขโทรศัพท์"
        },
		email: {
          required: 'กรุณาระบุข้อมูลอีเมล',
		  email: 'กรุณารตรวจสอบรูปแบบอีเมล'
        },
		password: {
          required: 'กรุณาระบุข้อมูลรหัสผ่าน'
        },
		repassword: {
          required: 'กรุณาระบุข้อมูลยืนยันรหัสผ่าน',
		  equalTo: 'กรุณาระบุข้อมูลรหัสผ่านและยืนยันรหัสผ่านให้ตรงกัน',
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
</script>
</body>
</html>
