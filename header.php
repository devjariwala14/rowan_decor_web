<?php
//ob_start();
include ("db_connect.php");
$obj = new DB_connect();
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);

session_start();


if (!isset($_SESSION["userlogin"])) {
  header("location:index.php");
  }
  
  

$adminmenu = array( "branch.php","branch_add.php","architect.php","architect_add.php","category.php","category_add.php","units.php","units_add.php","visitor.php","visitor_add.php","user.php","user_add.php","inquiry.php","inquiry_add.php","company.php","company_add.php","product.php","product_add.php","rooms.php","rooms_add.php","objects.php","objects_add.php","product_sel.php","product_sel_add.php", "product_selection_details.php","product_selection_details_add.php"
);
$location = array("state.php","state_add.php","city.php","city_add.php");
$delivery = array("deliveryboy_reg.php", "delivery_settings.php", "collection_time.php");
$coupon = array("coupon.php", "coupon_counter.php");
$mail = array("mail_type.php", "mail_type_tariff.php");
$review_feedback = array("post_review.php", "customer_feedback.php");
$reportmenu = array("customer_report.php", "cust_report.php", "delivery_boy_report.php");
$page_name = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard | Rowan Decor</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/logo.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />


    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->
    <!-- <link rel="stylesheet" href="assets/vendor/css/pages/card-analytics.css" /> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <!-- data tables -->
    <link rel="stylesheet" type="text/css" href="assets/vendor/DataTables/datatables.css">

    <!-- <link rel="stylesheet" href="assets/vendor/libs/quill/typography.css" />
    <link rel="stylesheet" href="assets/vendor/libs/quill/katex.css" />
    <link rel="stylesheet" href="assets/vendor/libs/quill/editor.css" /> -->

    <!-- Row Group CSS -->
    <!-- <link rel="stylesheet" href="assets/vendor/datatables-rowgroup-bs5/rowgroup.bootstrap5.css"> -->
    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script type="text/javascript">
    function createCookie(name, value, days) {
        var expires;
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        } else {
            expires = "";
        }
        document.cookie = (name) + "=" + String(value) + expires + ";path=/ ";

    }

    function readCookie(name) {
        var nameEQ = (name) + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return (c.substring(nameEQ.length, c.length));
        }
        return null;
    }

    function eraseCookie(name) {
        createCookie(name, "", -1);
    }

    function get_dashboard_data(date) {
        createCookie("dash_date", date, 1);

        document.getElementById("dashboard_frm").submit();
    }

    /* $(function() {
         setInterval("get_notification()", 10000);

     });


     function get_notification() {

         $.ajax({
             async: true,
             url: 'ajaxdata.php?action=get_notification',
             type: 'POST',
             data: "",

             success: function(data) {
                 // console.log(data);

                 var resp = data.split("@@@@");
                 $('#notification_list').html('');
                 $('#notification_list').append(resp[0]);

                 $('#noti_count').html('');

                 //if(resp[1]>0) {

                 $("#noti_count").addClass("badge-notifications");
                 $('#noti_count').append(resp[1]);
                 $('#notif_header').show();
                 if (resp[2] == 1) {
                     playSound();
                 }


                 }
                 //this else was commented
                 else
                 {     
                     $('#noti_count').removeClass('badge-notifications');

                      $('#noti_count').append('');
                      $('#notification_list').hide();
                      $('#notif_header').hide();
                      
                 }
             }

         });
     }

     function removeNotification(id, typ) {


         $.ajax({
             async: true,
             type: "GET",
             url: "ajaxdata.php?action=removenotification",
             data: "id=" + id + "&type=" + typ,
             async: true,
             cache: false,
             timeout: 50000,

             success: function(data) {

                 if (typ == "customer_reg") {
                     createCookie("cust_id", data, 1);
                     window.open('cust_report_detail.php', '_blank');
                 } else if (typ == "delivery_reg") {
                     createCookie("deli_boy_id", data, 1);
                     window.open('deliveryboy_report_detail.php', '_blank');
                 } else if (typ == "post_accepted") {
                     //window.location = "post.php";
                     createCookie("post_id", data, 1);
                     window.open('customer_report_detail.php', '_blank');
                 } else if (typ == "post_dispatched") {
                     //window.location = "post.php";
                     createCookie("post_id", data, 1);
                     window.open('customer_report_detail.php', '_blank');
                 } else if (typ == "post_rejected") {
                     //window.location = "post.php";
                     createCookie("post_id", data, 1);
                     window.open('customer_report_detail.php', '_blank');
                 } else {
                     //window.location = "post.php";
                     createCookie("post_id", data, 1);
                     window.open('customer_report_detail.php', '_blank');
                 }


             }
         });
     }

     function playSound() {

         $.ajax({
             async: true,
             url: 'ajaxdata.php?action=get_Playnotification',
             type: 'POST',
             data: "",

             success: function(data) {
                 // console.log(data);

                 var resp = data.split("@@@@");

                 if (resp[0] > 0) {

                     var mp3Source = '<source src="notif_sound.wav" type="audio/mpeg">';
                     document.getElementById("sound").innerHTML = '<audio autoplay="autoplay">' + mp3Source +
                         '</audio>';
                     removeplaysound(resp[1]);
                 }
             }

         });

     }

     function removeplaysound(ids) {

         $.ajax({
             async: true,
             type: "GET",
             url: "ajaxdata.php?action=removeplaysound",
             data: "id=" + ids,
             async: true,
             cache: false,
             timeout: 50000,

         });

     }

     function mark_read_all() {
         $.ajax({
             async: true,
             type: "GET",
             url: "ajaxdata.php?action=mark_read_all",
             data: "",
             async: true,
             cache: false,
             timeout: 50000,
             success: function(data) {
                 $('#notif_header').hide();
                 $('#notification_list').html('');
                 $('#noti_count').html('');
             }

         });
     }
     */
    </script>

</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="home.php" class="app-brand-link">

                        <span class="app-brand-text demo menu-text fw-bolder ms-2">Rowan Decor</span>
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"]) == 'home.php' ? 'active' : '' ?>">
                        <a href="home.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Dashboard</div>
                        </a>
                    </li>

                    <!-- Admin Controls -->
                    <li
                        class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['branch.php', 'branch_add.php', 'architect.php', 'architect_add.php', 'category.php', 'category_add.php', 'units.php', 'units_add.php', 'user.php', 'user_add.php', 'company.php', 'company_add.php', 'product.php', 'product_add.php', 'rooms.php', 'rooms_add.php', 'objects.php', 'objects_add.php']) ? 'active open' : '' ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div data-i18n="Form Elements">Admin Masters</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['architect.php', 'architect_add.php']) ? 'active' : '' ?>">
                                <a href="architect.php" class="menu-link">
                                    <div data-i18n="course">Architect</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['branch.php', 'branch_add.php']) ? 'active' : '' ?>">
                                <a href="branch.php" class="menu-link">
                                    <div data-i18n="course">Branch</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['company.php', 'company_add.php']) ? 'active' : '' ?>">
                                <a href="company.php" class="menu-link">
                                    <div data-i18n="course">Company</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['category.php', 'category_add.php']) ? 'active' : '' ?>">
                                <a href="category.php" class="menu-link">
                                    <div data-i18n="course">Category</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['units.php', 'units_add.php']) ? 'active' : '' ?>">
                                <a href="units.php" class="menu-link">
                                    <div data-i18n="course">Units</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['product.php', 'product_add.php']) ? 'active' : '' ?>">
                                <a href="product.php" class="menu-link">
                                    <div data-i18n="course">Product</div>
                                </a>
                            </li>   
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['rooms.php', 'rooms_add.php']) ? 'active' : '' ?>">
                                <a href="rooms.php" class="menu-link">
                                    <div data-i18n="course">Rooms</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['objects.php', 'objects_add.php']) ? 'active' : '' ?>">
                                <a href="objects.php" class="menu-link">
                                    <div data-i18n="course">Objects</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['user.php', 'user_add.php']) ? 'active' : '' ?>">
                                <a href="user.php" class="menu-link">
                                    <div data-i18n="course">Staff</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Operations -->
                    <li
                        class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['visitor.php', 'visitor_add.php','product_sel.php','product_sel_add.php', 'product_selection_details.php','product_selection_details_add.php','inquiry.php', 'inquiry_add.php']) ? 'active open' : '' ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bxs-cog"></i>
                            <div data-i18n="Form Elements">Operations</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['visitor.php', 'visitor_add.php']) ? 'active' : '' ?>">
                                <a href="visitor.php" class="menu-link">
                                    <div data-i18n="course">Visitor</div>
                                </a>
                            </li>

                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['inquiry.php', 'inquiry_add.php']) ? 'active' : '' ?>">
                                <a href="inquiry.php" class="menu-link">
                                    <div data-i18n="course">Inquiry</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['product_sel.php','product_sel_add.php', 'product_selection_details.php','product_selection_details_add.php']) ? 'active' : '' ?>">
                                <a href="product_sel.php" class="menu-link">
                                    <div data-i18n="course">Product Selection</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Location Controls -->
                    <li
                        class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['state.php', 'state_add.php', 'city.php', 'city_add.php']) ? 'active open' : '' ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='menu-icon bx bx-current-location'></i>
                            <div data-i18n="Form Elements">Location Controls</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['state.php', 'state_add.php']) ? 'active' : '' ?>">
                                <a href="state.php" class="menu-link">
                                    <div data-i18n="course">State</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]), ['city.php', 'city_add.php']) ? 'active' : '' ?>">
                                <a href="city.php" class="menu-link">
                                    <div data-i18n="course">City</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

            </aside>
            <!-- / Menu -->


            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">


                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Place this tag where you want the button to render. -->



                            <!-- <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar">
                     <i class="bx bx-bell"></i>
                     <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20" id="noti_count"></span>
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" id="notification_list">
                  </ul>
                </li> -->
                            <!-- Notification -->
                            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <i class="bx bx-bell bx-sm"></i>
                                    <!-- <span class="badge bg-danger rounded-pill badge-notifications" id="noti_count"></span> -->
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end py-0">
                                    <li class="dropdown-menu-header border-bottom" id="notif_header"
                                        style="display:none">
                                        <div class="dropdown-header d-flex align-items-center py-3">
                                            <h5 class="text-body mb-0 me-auto">Notification</h5>
                                            <a href="javascript:mark_read_all()"
                                                class="dropdown-notifications-all text-body" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Mark all as read">Read All</a>
                                        </div>
                                    </li>
                                    <li class="dropdown-notifications-list scrollable-container">
                                        <ul class="list-group list-group-flush" id="notification_list">

                                        </ul>
                                    </li>

                                </ul>
                            </li>
                            <!--/ Notification -->

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="assets/img/logo.png" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>
                                        <a class="dropdown-item" href="editProfile.php">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle"><?php echo $_SESSION["name"] ?></span>
                                        </a>
                                    </li>


                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="changePassword.php">
                                            <i class="bx bx-lock me-2"></i>
                                            <span class="align-middle">Change Password</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="logout.php">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- / User -->
                        </ul>
                    </div>
                </nav>
                <div id="sound"></div>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">