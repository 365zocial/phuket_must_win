<?php
session_start();

if (!isset($_SESSION["AUTHEN"]["ID"])) {
	header("Location: index.php");
	die();
}

//CONNECTION
function PDOConnector()
{
	try {
		$conn = new PDO('mysql:host=' . DB_SER . ';dbname=' . DB_NAME . '', DB_USR, DB_PWD);
		//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		$conn->exec("set names utf8");
		return $conn;
	} catch (PDOException $e) {
		return null;
	}
}

require_once "vendor/php-excel/Classes/PHPExcel.php";
include "vendor/php-excel/Classes/PHPExcel/IOFactory.php";

include("constant.php");

$result = 0;
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	if ($_FILES["informFile"]["size"] > 0) {

		$conn = PDOConnector();

		$folder = 'excel/';
		$file = explode(".", basename($_FILES['informFile']['name']));
		$extension = $file[count($file) - 1];
		$fileName = uniqid() . "." . $extension;
		$path = $folder . $fileName;
		if (move_uploaded_file($_FILES['informFile']['tmp_name'], $path)) {
			$fileType = PHPExcel_IOFactory::identify($path);
			$reader = PHPExcel_IOFactory::createReader($fileType);
			$reader->setReadDataOnly(true);
			$excel = $reader->load($path);

			$worksheet = $excel->setActiveSheetIndex(0);
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			#echo $highestRow;

			$heading = $worksheet->rangeToArray('A4:' . $highestColumn . '4', null, true, true, true);
			$heading = $heading[4];
			#print_r($heading);

			$completed = array();
			$incompleted = array();
			$duplicated = array();

			$conn = PDOConnector();

			for ($r = 5; $r <= $highestRow; ++$r) {
				$rows = $worksheet->rangeToArray('A' . $r . ':' . $highestColumn . $r, null, true, true, true);
				// print_r($rows);

				if ($rows[$r]['P'] == '' && $rows[$r]['N'] != '') echo $rows[$r]['I'] . $rows[$r]['J'] . " " . $rows[$r]['K'] . "ไม่ได้เลือกว่าฉีดวัคซีนหรือไม่<br>";
				if (strlen($rows[$r]['N']) != 13 && $rows[$r]['N'] != "") {
					echo "เลขบัตรประชาชน " . $rows[$r]['N'] . " ของ" . $rows[$r]['I'] . $rows[$r]['J'] . " " . $rows[$r]['K'] . "ไม่ถูกต้อง<br>";
					$rows[$r]['N'] = "";
				}


				if (
					(isset($rows[$r]['A'])) && ($rows[$r]['A'] != '') &&
					(isset($rows[$r]['B'])) && ($rows[$r]['B'] != '') &&
					//(isset($rows[$r]['C'])) && ($rows[$r]['C'] != '') &&
					(isset($rows[$r]['D'])) && ($rows[$r]['D'] != '') &&
					(isset($rows[$r]['E'])) && ($rows[$r]['E'] != '') &&
					(isset($rows[$r]['F'])) && ($rows[$r]['F'] != '') &&
					(isset($rows[$r]['G'])) && ($rows[$r]['G'] != '') &&
					//(isset($rows[$r]['H'])) && ($rows[$r]['H'] != '') &&
					(isset($rows[$r]['I'])) && ($rows[$r]['I'] != '') &&
					(isset($rows[$r]['J'])) && ($rows[$r]['J'] != '') &&
					(isset($rows[$r]['K'])) && ($rows[$r]['K'] != '') &&
					(isset($rows[$r]['L'])) && ($rows[$r]['L'] != '') &&
					(isset($rows[$r]['M'])) && ($rows[$r]['M'] != '') &&
					(isset($rows[$r]['N'])) && ($rows[$r]['N'] != '') &&
					(isset($rows[$r]['O'])) && ($rows[$r]['O'] != '') &&
					(isset($rows[$r]['P'])) && ($rows[$r]['P'] != '')
				) {

					if ($rows[$r]['C'] == "") $rows[$r]['C'] = "-";
					if ($rows[$r]['H'] == "") $rows[$r]['H'] = "-";

					if (preg_match("/\//", $rows[$r]['M'])) {
						$arrdate = explode("/", preg_replace("/\s+/", "", $rows[$r]['M']));
						if (!checkdate($arrdate[1], $arrdate[0], $arrdate[2] - 543)) {
							echo "วันเกิด " . $rows[$r]['M'] . " ของ" . $rows[$r]['I'] . $rows[$r]['J'] . " " . $rows[$r]['K'] . "ไม่ถูกต้อง<br>";
							$dateString = "";
							continue;
						} else $dateString = preg_replace("/\s+/", "", $rows[$r]['M']);
					} else {
						$dateNumber = $rows[$r]['M'];
						$dateTime = new DateTime("1899-12-30 + $dateNumber days");
						$dateString = $dateTime->format("d/m/Y");
					}




					$comm = "SELECT * FROM vaccine WHERE idCard LIKE '%" . $rows[$r]['N'] . "%'";
					$query = $conn->prepare($comm);
					$query->execute();

					if ($query->rowCount() == 0) {

						$comm = "INSERT INTO vaccine(orderId, targetGroup, otherTargetGroup, targetType, province, district, subdistrict, villageNo, title, firstname, lastname, sex, dateOfBirth, idCard, phone, vaccine, organization_id, file, created) VALUES(:orderId, :targetGroup, :otherTargetGroup, :targetType, :province, :district, :subdistrict, :villageNo, :title, :firstname, :lastname, :sex, :dateOfBirth, :idCard, :phone, :vaccine, :organization_id, :file, :created)";

						$query = $conn->prepare($comm);
						$result = $query->execute(array(
							"orderId" => $rows[$r]['A'],
							"targetGroup" => $rows[$r]['B'],
							"otherTargetGroup" => $rows[$r]['C'],
							"targetType" => $rows[$r]['D'],
							"province" => $rows[$r]['E'],
							"district" => $rows[$r]['F'],
							"subdistrict" => $rows[$r]['G'],
							"villageNo" => $rows[$r]['H'],
							"title" => $rows[$r]['I'],
							"firstname" => $rows[$r]['J'],
							"lastname" => $rows[$r]['K'],
							"sex" => $rows[$r]['L'],
							"dateOfBirth" => $dateString,
							"idCard" => $rows[$r]['N'],
							"phone" => $rows[$r]['O'],
							"vaccine" => $rows[$r]['P'],
							"organization_id" => $_SESSION["AUTHEN"]["ID"],
							"file" => $fileName,
							"created" => date("Y-m-d H:i:s")
						));
						array_push($completed, $rows[$r]['A']);
					} else {
						array_push($duplicated, $rows[$r]['A']);
					}
				} else {
					array_push($incompleted, $rows[$r]['A']);
				}
			}
			$result = 1;
			$msg = "บันทึกข้อมูลแล้ว จำนวน " . count($completed) . " รายการ  <br/>";
			$msg .= "ข้อมูลซ้ำ จำนวน " . count($duplicated) . " รายการ  <br/>";
			$msg .= "ข้อมูลไม่ครบถ้วน จำนวน " . count($incompleted) . " รายการ  <br/>";
		}
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo APPLICATION_NAME; ?> | Management</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.7 -->
	<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
	<!-- bootstrap datepicker -->
	<link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
	<!-- iCheck for checkboxes and radio inputs -->
	<link rel="stylesheet" href="plugins/iCheck/all.css">
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

		.fontsize24px {
			font-size: 24px;
		}

		div.dataTables_wrapper {
			margin: 0 auto;
		}
	</style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
	<!-- sidebar-mini -->
	<!-- Site wrapper -->
	<div class="wrapper">

		<?php include "header.php"; ?>

		<?php include "sidebar.php"; ?>

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>ข้อมูลสำรวจความประสงค์รับวัคซีน สำหรับภาคธุรกิจด่านหน้าในจังหวัดภูเก็ต </h1>
			</section>

			<!-- Main content -->
			<section class="content">

				<!-- Default box -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<div class="row">

							<div class="col-md-12">
								<div class="form-group">
									<span class="fontsize16px">1. ดาวน์โหลดแบบฟอร์มสำหรับแจ้งข้อมูลของพนักงานของในภาคธุรกิจ ที่มีความประสงค๋รับวัคซีน</span>

									<a id="download" name="download" href="excel/ex.vaccine.xlsx" target="_blank" class="btn btn-success"><i class="fa fa-download"></i> ดาวน์โหลด</a>
								</div>
							</div>

						</div>
						<!-- /.row -->

						<div class="row">

							<div class="col-md-12">
								<div class="form-group">
									<span class="fontsize16px">2. กรอกข้อมูลพนักงานี่มีความประสงค๋รับวัคซีนในแบบฟอร์ม</span>
								</div>
							</div>

						</div>
						<!-- /.row -->
						<form id="frm" name="frm" role="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
							<div class="row">

								<div class="col-md-4">
									<div class="form-group">
										<span class="fontsize16px">3. นำข้อมูลในแบบฟอร์มเข้าระบบฯ</span>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-upload"></i>
											</div>
											<input id="informFile" name="informFile" type="file" class="form-control pull-right">
										</div>
									</div>
								</div>
							</div>
							<!-- /.row -->

							<div class="row">

								<div class="col-md-12">
									<div class="form-group">
										<span class="fontsize16px">4. สำหรับกิจการที่อัพโหลดข้อมูลพนักงานเข้าระบบเรียบร้อยแล้วให้ผู้ได้รับวัคซีนกรอกแบบฟอร์ม<a href="files/new_patient.docx" target="_blank">ประวัติผู้ป่วยใหม่</a>และ<a href="files/consent.pdf" target="_blank">แบบคัดกรอง</a> แล้วนำมาในวันที่เข้ารับวัคซีน<br /><br />
											โดยรอบแรกของกลุ่มกิจการเกี่ยวกับการท่องเที่ยวนี้ กำหนดฉีดจะเป็น 3-7 เมษานี้</span>
									</div>
								</div>

							</div>
							<!-- /.row -->

							<div class="row">
								<div class="col-md-12">
									<button id="submit" name="submit" type="submit" class="btn btn-primary btn-flat"> ยืนยันการนำเข้าข้อมูล </button>
								</div>
							</div>
							<!-- /.row -->
						</form>

						<br />
						<div id="result" name="result" class="callout callout-success" style="display:<?php echo ($result == 1 ? 'block' : 'none'); ?>"><?php echo $msg; ?></div>
						<br />

						<div class="box-body">
							<span class="fontsize24px">ข้อมูลที่นำเข้าระบบฯ แล้ว </span>
							<table id="example1" class="table table-bordered">
								<thead>
									<tr>
										<th>ลำดับนำเข้า</th>
										<th>เลขประจำตัวประชาชน</th>
										<th>ชื่อ-นามสกุล</th>
										<th>เพศ</th>
										<th>วันเกิด</th>
										<th>กลุ่มเป้าหมาย</th>
										<th>ประเภทกลุ่มเป้าหมาย</th>
										<th>หมายเลขโทรศัพท์</th>
										<th>ตำบล</th>
										<th>อำเภอ</th>
										<th>วันที่นำเข้า</th>
										<!--<th>ลบ</th>-->
									</tr>
								</thead>
								<tbody>
									<?php
									$conn = PDOConnector();

									$comm = "SELECT * FROM vaccine WHERE organization_id=" . $_SESSION["AUTHEN"]["ID"] . " ORDER BY id DESC";

									$query = $conn->prepare($comm);
									$query->execute();
									if ($query->rowCount() > 0) {
										$rows = $query->fetchALL();
										for ($i = 0; $i < $query->rowCount(); $i++) {
									?>
											<tr>
												<td><?php echo $rows[$i]["orderId"]; ?></th>
												<td><?php echo $rows[$i]["idCard"]; ?></td>
												<td><?php echo $rows[$i]["title"] . " " . $rows[$i]["firstname"] . " " . $rows[$i]["lastname"]; ?></td>
												<td><?php echo $rows[$i]["sex"]; ?></td>
												<td><?php echo $rows[$i]["dateOfBirth"]; ?></td>
												<td><?php echo $rows[$i]["targetGroup"]; ?></td>
												<td><?php echo $rows[$i]["targetType"]; ?></td>s
												<td><?php echo $rows[$i]["phone"]; ?></td>
												<td><?php echo $rows[$i]["subdistrict"]; ?></td>
												<td><?php echo $rows[$i]["district"]; ?></td>
												<td><?php echo $rows[$i]["created"]; ?></td>
												<!--<td><a href="vulnerable.eform.php?vulnerable_id=<?php //echo $rows[$i]["vulnerable_id"];
																									?>" class="fa fa-pencil"></a></td>-->
											</tr>
									<?php
										}
									}
									?>
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
					<!--
		<div class="box-footer">
		</div>
		-->
					<!-- /.box-footer-->
				</div>
				<!-- /.box -->

			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

		<?php include "footer.php"; ?>
	</div>
	<!-- ./wrapper -->

	<!-- jQuery 3 -->
	<script src="bower_components/jquery/dist/jquery.min.js"></script>
	<!-- Bootstrap 3.3.7 -->
	<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<!-- DataTables -->
	<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
	<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
	<!-- SlimScroll -->
	<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="bower_components/fastclick/lib/fastclick.js"></script>
	<!-- bootstrap datepicker -->
	<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
	<script src="bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.th.min.js"></script>
	<!-- AdminLTE App -->
	<script src="dist/js/adminlte.min.js"></script>
	<!-- AdminLTE for demo purposes -->
	<script src="dist/js/demo.js"></script>
	<!-- iCheck 1.0.1 -->
	<script src="plugins/iCheck/icheck.min.js"></script>

	<script>
		var table;
		$(document).ready(function() {

			$('.sidebar-menu').tree()

			table = $('#example1').DataTable({
				'pagingType': 'numbers',
				'oLanguage': {
					'sLengthMenu': 'แสดง _MENU_ รายการ',
					'sZeroRecords': 'ไม่พบข้อมูลที่ค้นหา',
					'sInfo': 'แสดง _START_ ถึง _END_ ของ _TOTAL_ รายการ',
					'sInfoEmpty': 'แสดง 0 ถึง 0 ของ 0 รายการ',
					'sInfoFiltered': '(จากเร็คคอร์ดทั้งหมด _MAX_ รายการ)',
					'sSearch': 'ค้นหา '
				},
				"scrollX": true
			})
		})
	</script>
</body>

</html>