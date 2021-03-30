<?php
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 1);
  error_reporting(-1);
  
  session_start();
  //if(!isset($_SESSION["consensus"])){
  //	header("Location: finance.index.php");
  //	die();
  //}else{
  //	unset($_SESSION["consensus"]);
  //}
  
  include("constant.php");
  date_default_timezone_set("Asia/Bangkok");

  function PDOConnector(){
	try {
	  $conn = new PDO('mysql:host='.DB_SER.';dbname='.DB_NAME.'', DB_USR, DB_PWD);
	  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	  $conn->exec("set names utf8");
	  return $conn;
	}catch(PDOException $e){ return null;}
  }
  
  $conn=PDOConnector();
  
  $error=0;
  $msg="";
  
  if ($_SERVER["REQUEST_METHOD"]=="POST") {
	  $title=$_POST["title"];
	  $firstname=$_POST["firstname"];
	  $lastname=$_POST["lastname"];
	  $idCard=$_POST["idCard"];
	  $houseNo=$_POST["houseNo"];
	  $lane=$_POST["lane"];
	  $subdistrict=$_POST["subdistrict"];
	  $road=$_POST["road"];
	  $villageNo=$_POST["villageNo"];
	  $district=$_POST["district"];
	  $phone=$_POST["phone"];
	  $career=$_POST["career"];
	  $income=$_POST["income"];
	  $household_income=$_POST["household_income"];
	  $problemLevel=$_POST["problemlevel"];
	   
	  $type_vulnerable="";
	  $type_vulnerable_raise="";
	  $type_vulnerable_other="";
	  if(isset($_POST["vulner_type"])){
		for($i=0;$i<count($_POST["vulner_type"]);$i++){
		  if(trim($_POST["vulner_type"][$i]) != ""){
			$type_vulnerable=$type_vulnerable.$_POST["vulner_type"][$i]."/";
			if($_POST["vulner_type"][$i]=="มีภาระเลี้ยงดูคนเปราะบาง"){
			  $type_vulnerable_raise=$_POST["raise"];
			}else if($_POST["vulner_type"][$i]=="อื่นๆ"){
			  $type_vulnerable_other=$_POST["type_other"];
			}
		  }
		}
	  }
	  
	  $status="";
	  $status_other="";
	  
	  $status=$_POST["status"];
	  if($status=="อื่นๆ"){
	    $status_other=$_POST["other"];
	  }
	  
	  $help="";
	  $allowance="";
	  $allowance_amount="";
	  if(isset($_POST["help"])){
		for($i=0;$i<count($_POST["help"]);$i++){
		  if(trim($_POST["help"][$i]) != ""){
			$help=$help.$_POST["help"][$i]."/";
			if($_POST["help"][$i]=="ได้รับเงินสงเคราะห์อื่นๆ"){
			  $allowance=$_POST["other_allowance"];
			  $allowance_amount=$_POST["other_allowance_amount"];
			}
		  }
		}
	  }
	  
	  $comm="SELECT * FROM vulnerable WHERE id_card='".$idCard."'";
	  $query=$conn->prepare($comm); 
	  $query->execute();
	
	  if($query->rowCount()==0){
	    $comm="INSERT INTO vulnerable(title, firstname, lastname, id_card, house_no, lane, subdistrict, road, village_no, district, phone, career, income, household_income, problem_level, vulnerable, vulnerable_raise, vulnerable_other, status, status_other, help, allowance, allowance_amount, created_id, created_date) 
					VALUES (:title, :firstname, :lastname, :id_card, :house_no, :lane, :subdistrict, :road, :village_no, :district, :phone, :career, :income, :household_income, :problem_level, :vulnerable, :vulnerable_raise, :vulnerable_other, :status, :status_other, :help, :allowance, :allowance_amount, :created_id, :created_date)";
		$query=$conn->prepare($comm);
		$result=$query->execute(array(
			"title"=>$title,
			"firstname"=>$firstname,
			"lastname"=>$lastname,
			"id_card"=>$idCard,
			"house_no"=>$houseNo,
			"lane"=>$lane,
			"subdistrict"=>$subdistrict,
			"road"=>$road,
			"village_no"=>$villageNo,
			"district"=>$district,
			"phone"=>$phone,
			"career"=>$career,
			"income"=>$income,
			"household_income"=>$household_income,
			"problem_level"=>$problemLevel,
			"vulnerable"=>$type_vulnerable,
			"vulnerable_raise"=>$type_vulnerable_raise,
			"vulnerable_other"=>$type_vulnerable_other,
			"status"=>$status,
			"status_other"=>$status_other,
			"help"=>$help,
			"allowance"=>$allowance,
			"allowance_amount"=>$allowance_amount,
			"created_id"=>$_SESSION["AUTHEN"]["USER_ID"],
			"created_date"=>date("Y-m-d H:i:s")
			
		));
		
		$vulnerable_id=$conn->lastInsertId();
		$_SESSION["vulnerable_id"]=$vulnerable_id;
		header("Location: vulnerable.2form.php");
		
	  }else{
		$error=1;
		$msg="ไม่สามารถทำการบันทึกข้อมูลได้ เนื่องจากมีข้อมูลอยู่ในระบบฯ แล้ว";
	  }	
   }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo APPLICATION_NAME; ?> | form</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="bower_components/select2/dist/css/select2.min.css">
  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="plugins/timepicker/bootstrap-timepicker.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

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
  </style>
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper clearfix" >
  
  <?php include "header.php"; ?>
  
  <?php include "sidebar.php"; ?>
  
  <div class="content-wrapper">
    <div class="container">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>แบบแจ้งข้อมูลกลุ่มเปราะบาง</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			
			<div id="error" name="error" class="callout callout-danger" style="display:<?php echo ($error==1?'block':'none'); ?>" ><?php echo $msg; ?></div>
			
			<!-- Default box -->
			<div class="box box-primary">
				<form id="frm" name="frm" role="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div class="box-body">
		    
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<span class="fontsize16px">คำนำหน้า</span>
									<select id='title' name='title' class='form-control select2' style='width: 100%;'>
										<option value='นาย' >นาย</option>
										<option value='นาง' >นาง</option>
										<option value='นางสาว' >นางสาว</option>
										<option value='เด็กชาย' >เด็กชาย</option>
										<option value='เด็กหญิง' >เด็กหญิง</option>
									</select>
								</div>
							</div><!-- /.col -->
							<div class="col-md-4">
								<div class="form-group">
									<span class="fontsize16px">ชื่อ</span>
									<input id="firstname" name="firstname" type="text" class="form-control" placeholder="" value="">
								</div>
							</div><!-- /.col -->
							<div class="col-md-6">
								<div class="form-group">
									<span class="fontsize16px">นามสกุล</span>
									<input id="lastname" name="lastname" type="text" class="form-control" placeholder="" value="">
								</div>
							</div><!-- /.col -->
						</div><!-- /.row -->
						
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<span class="fontsize16px">เลขที่บัตรประจำตัวประชาชน</span>
									<input id="idCard" name="idCard" type="text" class="form-control" placeholder="" data-inputmask='"mask": "9999999999999"' data-mask value="" >
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
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<span class="fontsize16px">หมายเลขโทรศัพท์ที่ติดต่อได้</span>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-phone"></i>
										</div>
										<input id="phone" name="phone" type="text" class="form-control" value="" data-inputmask='"mask": "9999999999"' data-mask>
									</div>
								</div>
							</div><!-- /.col -->
							<div class="col-md-6"></div><!-- /.col -->
						</div><!-- /.row -->
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<span class="fontsize16px">อาชีพ</span>
									<input id="career" name="career" type="text" class="form-control" value="">
								</div>
							</div><!-- /.col -->
							<div class="col-md-6">
								<div class="form-group">
									<span class="fontsize16px">รายได้/เดือน (บาท)</span>
									<input id="income" name="income" type="text" class="form-control" value="">
								</div>
							</div><!-- /.col -->
						</div><!-- /.row -->
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<span class="fontsize16px">รายได้ครัวเรือน/เดือน (บาท)</span>
									<input id="household_income" name="household_income" type="text" class="form-control" value="">
								</div>
							</div><!-- /.col -->
							<div class="col-md-6"></div><!-- /.col -->
						</div><!-- /.row -->
						
						<div class="row">
							<div class="col-md-6">
								<h3 class="box-title">ระดับของปัญหา</h3>
								<div class="form-group fontsize16px">
									<input type="radio" name="problemlevel" class="minimal" value="มากที่สุด" checked> มากที่สุด <br/>
									<input type="radio" name="problemlevel" class="minimal" value="มาก"> มาก <br/>
									<input type="radio" name="problemlevel" class="minimal" value="น้อย"> น้อย
								</div>
							</div><!-- /.col -->
							<div class="col-md-6"></div><!-- /.col -->
						</div><!-- /.row -->
						
						<div class="row">
							<div class="col-md-6">
								<h3 class="box-title">ประเภทกลุ่มเปราะบาง (สามารถเลือกได้มากกว่า 1 ข้อ)</h3>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="vulner_type[]" class="minimal" value="พิการ"> พิการ
								</div>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="vulner_type[]" class="minimal" value=" เจ็บป่วย"> เจ็บป่วย
								</div>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="vulner_type[]" class="minimal" value=" เข้าไม่ถึงสิทธ์ หรือสวัสดิการของรัฐ"> เข้าไม่ถึงสิทธ์/สวัสดิการของรัฐ
								</div>
								<div class="form-group fontsize16px">
									<div class="form-inline">
										<input type="checkbox" id="type_other_check" name="vulner_type[]" class="minimal" value="อื่นๆ"> อื่นๆ ระบุ
										<input id="type_other" name="type_other" type="text" class="form-control" placeholder="" value="">
									</div>
								</div>
							</div><!-- /.col -->
							<div class="col-md-6">
								<h3 class="box-title">&nbsp;</h3>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="vulner_type[]" class="minimal" value="ผู้ป่วยติดเตียง"> ผู้ป่วยติดเตียง
								</div>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="vulner_type[]" class="minimal" value="เด็ก หรือผู้สูงอายุ"> เด็ก/ผู้สูงอายุ
								</div>
								<div class="form-group fontsize16px">
									<div class="form-inline">
										<input type="checkbox" id="vulner_raise" name="vulner_type[]" class="minimal" value="มีภาระเลี้ยงดูคนเปราะบาง"> มีภาระเลี้ยงดูคนเปราะบาง จำนวน (คน) 
										<input id="raise" name="raise" type="text" class="form-control" placeholder="" value="">
									</div>
								</div>
							</div><!-- /.col -->
						</div><!-- /.row -->
						
						<div class="row">
							<div class="col-md-4">
								<h3 class="box-title">สถานภาพปัจจุบัน</h3>
								<div class="form-group fontsize16px">
									<input type="radio" name="status" class="minimal" value="ยังต้องการความช่วยเหลือ" checked> ยังต้องการความช่วยเหลือ
								</div>
								<div class="form-group fontsize16px">
									<input type="radio" name="status" class="minimal" value="เสียชีวิต"> เสียชีวิต
								</div>
								<div class="form-group fontsize16px">
									<div class="form-inline">
										<input type="radio" id="status_other" name="status" class="minimal" value="อื่นๆ"> อื่นๆ ระบุ
										<input id="other" name="other" type="text" class="form-control" placeholder="" value="">
									</div>
								</div>
							</div><!-- /.col -->
							<div class="col-md-4">
								<h3 class="box-title">&nbsp;</h3>
								<div class="form-group fontsize16px">
									<input type="radio" name="status" class="minimal" value="ไม่ต้องการความช่วยเหลือแล้ว"> ไม่ต้องการความช่วยเหลือแล้ว
								</div>
								<div class="form-group fontsize16px">
									<input type="radio" name="status" class="minimal" value="ย้ายออกนอกพื้นที่"> ย้ายออกนอกพื้นที่
								</div>
								<div class="form-group fontsize16px">&nbsp;</div>
							</div><!-- /.col -->
							<div class="col-md-4">
								<h3 class="box-title">&nbsp;</h3>
								<div class="form-group fontsize16px">&nbsp;</div>
								<div class="form-group fontsize16px">
									<input type="radio" name="status" class="minimal" value="ติดต่อไม่ได้"> ติดต่อไม่ได้
								</div>
								<div class="form-group fontsize16px">&nbsp;</div>
							</div><!-- /.col -->
						</div><!-- /.row -->
						
						<div class="row">
							<div class="col-md-6">
								<h3 class="box-title">การรับความช่วยเหลือ (สามารถเลือกได้มากกว่า 1 ข้อ)</h3>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="help[]" class="minimal" value="ได้รับเบี้ยยังชีพคนพิการรายเดือน"> ได้รับเบี้ยยังชีพคนพิการรายเดือน
								</div>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="help[]" class="minimal" value="มีบัตรสวัสดิการแห่งรัฐ"> มีบัตรสวัสดิการแห่งรัฐ
								</div>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="help[]" class="minimal" value="ได้รับเงินอุดหนุนเด็กแรกเกิด"> ได้รับเงินอุดหนุนเด็กแรกเกิด
								</div>
							</div><!-- /.col -->
							<div class="col-md-6">
								<h3 class="box-title">&nbsp;</h3>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="help[]" class="minimal" value="ได้รับเบี้ยยังชีพคนชรารายเดือน"> ได้รับเบี้ยยังชีพคนชรารายเดือน
								</div>
								<div class="form-group fontsize16px">
									<input type="checkbox" name="help[]" class="minimal" value="อยู่ในระบบประกันสังคม"> อยู่ในระบบประกันสังคม
								</div>
								<div class="form-group fontsize16px">&nbsp;</div>
							</div><!-- /.col -->
						</div><!-- /.row -->
						
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="form-inline">
										<input type="checkbox" id="help_allowance" name="help[]" class="minimal" value="ได้รับเงินสงเคราะห์อื่นๆ"> ได้รับเงินสงเคราะห์อื่นๆ รายเดือน จาก
										<select id='other_allowance' name='other_allowance' class='form-control select2'>
											<option value='' >เลือก</option>
											<?php
												$comm="SELECT * FROM organization";
												$query=$conn->prepare($comm); 
												$query->execute();
												if($query->rowCount()>0){
													while ($row = $query->fetch()) {
														echo "<option value='".$row["organization_name"]."'>".$row["organization_name"]."</option>";
													}
												}
											?>
										</select>
										จำนวน
										<input id="other_allowance_amount" name="other_allowance_amount" type="text" class="form-control" placeholder="" value="">
										บาท
									</div>
								</div>
							</div><!-- /.col -->
						</div><!-- /.row -->
						
					</div><!-- /.box-body -->
            
					<div class="box-footer">
						<div class="form-inline">
							<button id="submit" name="submit" type="submit" class="btn btn-primary pull-right" style="margin: 5px;"><i class="fa fa-save"></i> บันทึกข้อมูล</button>
							<br/>
						</div>
					</div><!-- /.box-footer-->
				</form>
			</div><!-- /.box -->
		</section><!-- /.content -->
    </div><!-- /.container -->
  </div><!-- /.content-wrapper -->
  
  <?php include "footer.php"; ?>

  <?php include "control.php"; ?>
  
</div><!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Select2 -->
<script src="bower_components/select2/dist/js/select2.full.min.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- InputMask -->
<script src="plugins/input-mask/jquery.inputmask.js"></script>
<script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>
<!-- date-range-picker -->
<script src="bower_components/moment/min/moment.min.js"></script>
<script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- bootstrap datepicker -->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.th.min.js"></script>
<!-- bootstrap time picker -->
<script src="plugins/timepicker/bootstrap-timepicker.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<!-- Page script -->
<script>
  $(document).ready(function () {

    $('[data-mask]').inputmask();
	
	$.validator.addMethod("raise", function(value, element) {
		if($("#vulner_raise").prop("checked")){
			if($('#raise').val()!=""){
				if($('#raise').val().match(/^[0-9]+$/)){
					return true;
				}else{
					$.validator.messages.raise="กรุณาระบุเป็นตัวเลข";
					return false;
				}
			}else{
				$.validator.messages.raise="โปรดระบุจำนวนคนเปราะบางที่เลี้ยงดู";
				return false;
			}
		}else{
			return true;
		}
    });
	
	$.validator.addMethod("status", function(value, element) {
		if($("#status_other").prop("checked")){
			if($('#other').val()!=""){
				return true;
			}else{
				$.validator.messages.status="โปรดระบุ";
				return false;
			}
		}else{
			return true;
		}
    });
	
	$.validator.addMethod("help_other_allowance", function(value, element) {
		if($("#help_allowance").prop("checked")){
			if($('#other_allowance').val()!=""){
				return true;
			}else{
				$.validator.messages.help_other_allowance="โปรดระบุหน่วยงานที่ให้การสงเคราะห์";
				return false;
			}
		}else{
			return true;
		}
    });
	
	$.validator.addMethod("help_other_allowance_amount", function(value, element) {
		if($("#help_allowance").prop("checked")){
			if($('#other_allowance_amount').val()!=""){
				if($('#other_allowance_amount').val().match(/^[0-9]+$/)){
					return true;
				}else{
					$.validator.messages.help_other_allowance_amount="กรุณาระบุเป็นตัวเลข";
					return false;
				}
			}else{
				$.validator.messages.help_other_allowance_amount="โปรดระบุจำนวนเงินที่ได้รับการสงเคราะห์";
				return false;
			}
		}else{
			return true;
		}
    });
	
	$.validator.addMethod("isHouseNo", function(value, element) {
      if($('#houseNo').val().match(/^[0-9\.\-\/]+$/)) { 
        return true;
      }else{ 
        return false;
      }
    }, "กรอกเฉพาะตัวเลขและเครื่องหมาย / เท่านั้น");
	
	//$.validator.addMethod("isVillageNo", function(value, element) {
    //  if($('#villageNo').val().match(/^[0-9\-]+$/)) { 
    //    return true;
    //  }else{ 
    //    return false;
    //  }
    //}, "กรอกเฉพาะตัวเลข หรือ ครื่องหมาย - เท่านั้น");
	
	$.validator.addMethod("isIdCard", function(value, element) {
      if($('#idCard').val().match(/^[0-9]+$/)) { 
        return true;
      }else{ 
        return false;
      }
    }, "กรอกเฉพาะตัวเลข จำนวน 13 หลักเท่านั้น");
	
	$.validator.addMethod("isPhone", function(value, element) {
      if($('#phone').val().match(/^[0-9]+$/)) { 
        return true;
      }else{ 
        return false;
      }
    }, "กรอกเฉพาะตัวเลข จำนวน 10 หลักเท่านั้น");
	
	$.validator.addMethod("income_amount", function(value, element) {
		if($('#income').val()!=""){
			if($('#income').val().match(/^[0-9]+$/)){
				return true;
			}else{
				$.validator.messages.income_amount="กรุณาระบุเป็นตัวเลข";
				return false;
			}
		}else{
			$.validator.messages.income_amount="กรอกรายได้/เดือน";
			return false;
		}
    });
	
	$.validator.addMethod("household_income_amount", function(value, element) {
		if($('#household_income').val()!=""){
			if($('#household_income').val().match(/^[0-9]+$/)){
				return true;
			}else{
				$.validator.messages.household_income_amount="กรุณาระบุเป็นตัวเลข";
				return false;
			}
		}else{
			$.validator.messages.household_income_amount="กรอกรายได้ครัวเรือน/เดือน";
			return false;
		}
    });
	
	$.validator.addMethod("type_other", function(value, element) {
		if($("#type_other_check").prop("checked")){
			if($('#type_other').val()!=""){
				return true;
			}else{
				$.validator.messages.type_other="โปรดระบุ";
				return false;
			}
		}else{
			return true;
		}
    });
	
    $('#frm').validate({
      
      rules: {
		firstname: {
          required: true
        },
		lastname: {
          required: true
        },
		idCard: {
          required: true,
		  isIdCard: true
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
		  //isVillageNo: true
		},
		phone: {
          required: true,
		  isPhone: true
        },
		career: {
          required: true
        },
		income: {
          required: true,
		  income_amount: true
        },
		household_income: {
          required: true,
		  household_income_amount: true
        },
		type_other: {
		  type_other: true
		},
		raise:{
		  raise: true
		},
		other:{
		  status: true
		},
		other_allowance: {
		  help_other_allowance: true
		},
		other_allowance_amount: {
			help_other_allowance_amount: true
		}
      },
      
      messages: {
		firstname: {
          required: "กรอกชื่อ"
        },
		lastname: {
          required: "กรอกนามสกุล"
        },
		idCard: {
          required: "กรอกเลขที่ประจำตัวประชาชน"
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
		phone: {
          required: "กรอกเบอร์โทรศัพท์"
        },
		career: {
          required: "กรอกอาชีพ"
        },
		income: {
          required: "กรอกรายได้/เดือน"
        },
		household_income: {
          required: "กรอกรายได้ครัวเรือน/เดือน"
        },
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
	
  })
  
</script>
</body>
</html>
