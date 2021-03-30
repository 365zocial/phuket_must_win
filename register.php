<?php
if (PHP_SESSION_NONE === session_status()) {
    session_start();
}


// if (!isset($_SESSION["AUTHEN"]["ID"])) {
//     header("Location: person_login.php");
//     die();
// }

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

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo APPLICATION_NAME; ?> | ขึ้นทะเบียนบริษัท/องค์กรเอกชน/หน่วยงาน</title>
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
    <div class="login-box" style="width:100%;max-width: 800px;">
        <!-- /.login-logo -->
        <div class="login-box-body">

            <div id="error" name="error" class="callout callout-danger" style="display:<?php echo ($error == 1 ? 'block' : 'none'); ?>"><?php echo $msg; ?></div>

            <p style="padding-bottom: 0" class="login-box-msg fontsize24px">สถิติผู้ขอขึ้นทะเบียน</p>
            <center>
                <small>ข้อมูลเมื่อ <?= date("d M Y H:i:s") ?></small>
            </center>

            <h2>Organization: <?= number_format($o_rows[0]['organization_count']) ?></h2>
            <h2>Vaccine: <?= number_format($v_rows[0]['vaccine_count']) ?></h2>



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
        $(document).ready(function() {

            $('[data-mask]').inputmask()

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            })



            $.validator.addMethod("isHouseNo", function(value, element) {
                if ($('#houseNo').val().match(/^[0-9\.\-\/]+$/)) {
                    return true;
                } else {
                    return false;
                }
            }, "กรอกเฉพาะตัวเลขและเครื่องหมาย / เท่านั้น");

            $.validator.addMethod("phone", function(value, element) {
                var rexg = /^\d*(?:\.\d{1,2})?$/;
                var phone = $('#phone').val();

                if (rexg.test(phone) && phone.length == 10) {
                    return true;
                } else {
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
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                }

            })


        });
    </script>
</body>

</html>