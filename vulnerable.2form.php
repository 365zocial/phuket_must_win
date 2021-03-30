<?php
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 1);
  error_reporting(-1);
  
  session_start();
  
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
  
  
  if ($_SERVER["REQUEST_METHOD"]=="POST") {
	  
	  $type=$_POST["help"];
	  $organization=$_POST["organization"];
	  $value=$_POST["amount"];
	  if($type!="ถุงยังชีพ"){
	    $comm="INSERT INTO help(vulnerable_id, type, organization, value, created_id, created_date) 
					VALUES (:vulnerable_id, :type, :organization, :value, :created_id, :created_date)";
		$query=$conn->prepare($comm);
		$result=$query->execute(array(
			"vulnerable_id"=>$_SESSION["vulnerable_id"],
			"type"=>$type,
			"organization"=>$organization,
			"value"=>$value,
			"created_id"=>$_SESSION["AUTHEN"]["USER_ID"],
			"created_date"=>date("Y-m-d H:i:s")
		));
	  }else{
		  for($i=1;$i<=$value;$i++){
			$comm="INSERT INTO help(vulnerable_id, type, organization, value, created_id, created_date) 
						VALUES (:vulnerable_id, :type, :organization, :value, :created_id, :created_date)";
			$query=$conn->prepare($comm);
			$result=$query->execute(array(
				"vulnerable_id"=>$_SESSION["vulnerable_id"],
				"type"=>$type,
				"organization"=>$organization,
				"value"=>1,
				"created_id"=>$_SESSION["AUTHEN"]["USER_ID"],
				"created_date"=>date("Y-m-d H:i:s")
			));
		  }
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
  
  <!-- DataTables -->
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

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
			<!-- Default box -->
			<div class="box box-primary">
				<form id="frm" name="frm" role="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					
					<div class="box-body">
						<div class="row">
							<div class="col-md-12">
								<h3 class="box-title"><u>ได้รับการช่วยเหลือด้านอื่นๆ  (ในห้วงเดือน ธ.ค. 63 - ก.พ. 64)</u></h3><br/>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<span class="fontsize16px">ความช่วยเหลือ</span>
									<select id='help' name='help' class='form-control select2' style='width: 100%;'>
										<option value='เงิน' >เงิน</option>
										<option value='ถุงยังชีพ' >ถุงยังชีพ</option>
										<option value='ข้าวสาร (เปราะบาง)' >ข้าวสาร (เปราะบาง)</option>
										<option value='อื่นๆ' >อื่นๆ</option>
									</select>
								</div>
							</div><!-- /.col -->
							<div class="col-md-3">
								<div class="form-group">
									<span class="fontsize16px">จากหน่วยงาน</span>
									<select id='organization' name='organization' class='form-control select2' style='width: 100%;'>
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
								</div>
							</div><!-- /.col -->
							<div class="col-md-3">
								<div class="form-group">
									<span id="amount_label" name="amount_label" class="fontsize16px">จำนวน (บาท)</span>
									<input id="amount" name="amount" type="text" class="form-control" placeholder="" value="">
								</div>
							</div><!-- /.col -->
							<div class="col-md-3">
								<div class="form-group">
									<br/>
									<button id="submit" name="submit" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> เพิ่มข้อมูล</button>
								</div>
							</div><!-- /.col -->
						</div><!-- /.row -->
						
						<div class="row">
							<hr/>
							<br/>
							<br/>
							<div class="col-md-12">
								<table id="example1" class="table table-bordered table-striped">
									<thead>
									  <tr>
										<th>ความช่วยเหลือ</th>
										<th>หน่วยงาน</th>
										<th>จำนวน</th>
									  </tr>
									</thead>
									<tbody>
									  <?php 
									    $comm="SELECT * FROM help WHERE vulnerable_id=".$_SESSION["vulnerable_id"]." ORDER BY help_id DESC";

										$query=$conn->prepare($comm); 
										$query->execute();
										if($query->rowCount()>0){
											$rows=$query->fetchALL();
											for($i=0; $i<$query->rowCount(); $i++){
									  ?>
											  <tr>
												<td><?php echo $rows[$i]["type"]?></td>
												<td><?php echo $rows[$i]["organization"]?></td>
												<td><?php echo $rows[$i]["value"]?></td>
											  </tr>
									  <?php
											}
										}
									  ?>
									</tbody>
								</table>
							</div><!-- /.col -->
						</div><!-- /.row -->
						
					</div><!-- /.box-body -->
            
					<div class="box-footer">
						<div class="form-inline">
							<button type="button" class="btn btn-primary pull-right" style="margin: 5px;" onclick="location.href='vulnerable.management.php';"><i class="fa fa-save"></i> บันทึกข้อมูล</button>
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

<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!-- Page script -->
<script>
  $(document).ready(function () {
	  
	table=$('#example1').DataTable({
	  'pagingType': 'numbers',
	  'oLanguage': {
        'sLengthMenu': 'แสดง _MENU_ รายการ',
        'sZeroRecords': 'ไม่พบข้อมูลที่ค้นหา',
        'sInfo': 'แสดง _START_ ถึง _END_ ของ _TOTAL_ รายการ',
        'sInfoEmpty': 'แสดง 0 ถึง 0 ของ 0 รายการ',
        'sInfoFiltered': '(จากเร็คคอร์ดทั้งหมด _MAX_ รายการ)',
        'sSearch': 'ค้นหา '
      }
	})
	

    $('#help').change(function() {
		var help=$('#help').children("option:selected").val();
		if(help=="เงิน"){
			$('#organization').val("");
			$('#organization').prop('disabled', false);
			$('#amount_label').html("จำนวน (บาท)");
			$('#amount').val("");
			$('#amount').prop('disabled', false);
		}else if(help=="ถุงยังชีพ"){
			$('#organization').val("");
			$('#organization').prop('disabled', false);
			$('#amount_label').html("จำนวน (ครั้ง)");
			$('#amount').val("");
			$('#amount').prop('disabled', false);
		}else if(help=="ข้าวสาร (เปราะบาง)"){
			$('#organization').val("สำนักงานจังหวัดภูเก็ต และภาคเอกชน");
			//$('#organization').prop('disabled', true);
			$('#amount_label').html("จำนวน (ครั้ง)");
			$('#amount').val("1");
			//$('#amount').prop('disabled', true);
		}else {
			$('#organization').val("");
			$('#organization').prop('disabled', false);
			$('#amount_label').html("ระบุ");
			$('#amount').val("");
			$('#amount').prop('disabled', false);
		}	
	})
	
	$.validator.addMethod("custom_validate", function(value, element) {
		var help=$('#help').children("option:selected").val();
		var amount=$('#amount').val();
		var errorMsg="";
		
		if(amount!=""){
			if(help!="อื่นๆ"){
				if(amount.match(/^[0-9]+$/)){
					return true;
				}else{
					$.validator.messages.custom_validate="กรุณาระบุเป็นตัวเลข";
					return false;
				}
			}else{
				return true;
			}
		}else{
			if(help=="เงิน"){
				$.validator.messages.custom_validate="กรุณาระบุจำนวน (บาท)";
			}else if(help=="ถุงยังชีพ"){
				$.validator.messages.custom_validate="กรุณาระบุจำนวน (ครั้ง)";
			}else{
				$.validator.messages.custom_validate="กรุณาระบุความช่วยเหลือ";
			}
			return false;
		}
    });
	
    $('#frm').validate({
      
      rules: {
		organization: {
          required: true
        },
		amount: {
          custom_validate: true
        }
      },
      
      messages: {
		organization: {
          required: "กรุณาระบุหน่วยงาน"
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
	
  })
  
</script>
</body>
</html>
