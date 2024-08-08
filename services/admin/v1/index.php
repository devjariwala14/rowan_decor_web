<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
//including the required files
require_once '../include/DbOperation.php';
require '../libs/Slim/Slim.php';

date_default_timezone_set("Asia/Kolkata");
\Slim\Slim::registerAutoloader();

//require_once('../../PHPMailer_v5.1/class.phpmailer.php');

$app = new \Slim\Slim();


/*
 * login
 * Parameters: {"user_id":"","password":""}
 * Method: POST
 */
$app->post('/login', function () use ($app) {

    verifyRequiredParams(array('data'));

    $data_request = json_decode($app->request->post('data'));
    $user_id = $data_request->user_id;
    $password = $data_request->password;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->login($user_id, $password);
    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "Logged in Successfully";
        $data['success'] = true;
    } else {
        $data['message'] = "Incorrect Id or Password";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});

/*
 * add_new_visitor
 * Parameters: {"full_name":"","mobile_no":"","whatsapp_no":"","ref_name":"","place":"(visit site/studio)","visiting_person":"(customer/architect)","remark":""}
 * Method: POST
 */
$app->post('/add_new_visitor', function () use ($app) {

    verifyRequiredParams(array('data'));

    $data_request = json_decode($app->request->post('data'));
    $full_name = $data_request->full_name;
    $mobile_no = $data_request->mobile_no;
    $whatsapp_no = $data_request->whatsapp_no;
    $ref_name = $data_request->ref_name;
    $place = $data_request->place;
    $visiting_person = $data_request->visiting_person;
    $remark = $data_request->remark;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->add_new_user(
        $full_name,
        $mobile_no,
        $whatsapp_no,
        $ref_name,
        $place,
        $visiting_person,
        $remark
    );

    if ($result) {
        $data['message'] = "Visitor added succesfully";
        $data['success'] = true;
    } else {
        $data['message'] = "Some error occured";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});



/*
 * edit_visitor
 * Parameters: {"visitor_id":"","full_name":"","mobile_no":"","whatsapp_no":"","ref_name":"","place":"(visit site/studio)","visiting_person":"(customer/architect)","remark":""}
 * Method: POST
 */
$app->post('/edit_visitor', function () use ($app) {

    verifyRequiredParams(array('data'));

    $data_request = json_decode($app->request->post('data'));
    $visitor_id = $data_request->visitor_id;
    $full_name = $data_request->full_name;
    $mobile_no = $data_request->mobile_no;
    $whatsapp_no = $data_request->whatsapp_no;
    $ref_name = $data_request->ref_name;
    $place = $data_request->place;
    $visiting_person = $data_request->visiting_person;
    $remark = $data_request->remark;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->edit_visitor(
        $visitor_id,
        $full_name,
        $mobile_no,
        $whatsapp_no,
        $ref_name,
        $place,
        $visiting_person,
        $remark
    );

    if ($result) {
        $data['message'] = "Visitor edited succesfully";
        $data['success'] = true;
    } else {
        $data['message'] = "Some error occured";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});


/*
 * add_new_visitor
 * Parameters: none
 * Method: POST
 */
$app->post('/all_visitor', function () use ($app) {

    // verifyRequiredParams(array('data'));

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->all_visitor();

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});


/*
 * add_new_inquiry
 * Parameters: data : {"visitor_id":"","inquired_for":"","attended_by":"","architect_id":"","address":"","suggestions":"","start_date",""} , property_image
 * Method: POST
 * to do : make this able to accept multiple images
 */
$app->post('/add_new_inquiry', function () use ($app) {

    verifyRequiredParams(array('data'));


    $data_request = json_decode($app->request->post('data'));
    $visitor_id = $data_request->visitor_id;
    $inquired_for = $data_request->inquired_for;
    $attended_by = $data_request->attended_by;
    $architect_id = $data_request->architect_id;
    $address = $data_request->address;
    $suggestions = $data_request->suggestions;
    $start_date = $data_request->start_date;

    $db = new DbOperation();
    $data = array();
    $data['data'] = array();

    //rename file for property image
    $property_img = $_FILES["property_image"]["name"];
    $property_img_path = $_FILES["property_image"]["tmp_name"];

    // Convert spaces and special characters in the file name to underscores
    $property_img = preg_replace('/[^A-Za-z0-9.\-]/', '_', $property_img);

    if (file_exists("../../../property_image/" . $property_img)) {
        $i = 0;
        $ImageFileName = $property_img;
        $Arr1 = explode('.', $ImageFileName);

        $ImageFileName = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("../../../property_image/" . $ImageFileName)) {
            $i++;
            $ImageFileName = $Arr1[0] . $i . "." . $Arr1[1];
        }
    } else {
        $ImageFileName = $property_img;
    }

    $result = $db->add_new_inquiry($visitor_id, $inquired_for, $attended_by, $architect_id, $address, $suggestions, $start_date, $ImageFileName);

    if ($result) {
        move_uploaded_file($property_img_path, "../../../property_image/" . $ImageFileName);
        $data['message'] = "Inquiry added succesfully";
        $data['success'] = true;
    } else {
        $data['message'] = "Some error occured";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});

/*
 * add_new_architect
 * Parameters: data : {"name":"","contact_no":"","whatsapp_no":""}
 * Method: POST
 */
$app->post('/add_new_architect', function () use ($app) {

    verifyRequiredParams(array('data'));

    $data_request = json_decode($app->request->post('data'));
    $name = $data_request->name;
    $contact_no = $data_request->contact_no;
    $whatsapp_no = $data_request->whatsapp_no;

    $db = new DbOperation();
    $data = array();
    $data['data'] = array();

    $result = $db->add_new_architect($name, $contact_no, $whatsapp_no);

    if ($result) {
        $data['message'] = "Architect added succesfully";
        $data['success'] = true;
    } else {
        $data['message'] = "Some error occured";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});

/*
 * add_product_selection
 * Parameters:  data : {"inq_id":"","date_time":"","base_product_id":"","customer_product_name":"","rd_description":"","sell_amount":"","unit":"","total_amount":"","unit_of_measure":"","room_name":"","object":"","measurement_details":"","catalogue_notes":"","status":""}
 *              catalogue_image
 *              rowan_image
 *              customer_image
 * Method: POST
 */
$app->post('/add_product_selection', function () use ($app) {
    verifyRequiredParams(array('data'));

    $data_request = json_decode($app->request->post('data'));
    $inq_id = $data_request->inq_id;
    $date_time = $data_request->date_time;
    $base_product_id = $data_request->base_product_id;
    $customer_product_name = $data_request->customer_product_name;
    $rd_description = $data_request->rd_description;
    $sell_amount = $data_request->sell_amount;
    $unit = $data_request->unit;
    $total_amount = $data_request->total_amount;
    $unit_of_measure = $data_request->unit_of_measure;
    $room_name = $data_request->room_name;
    $object = $data_request->object;
    $measurement_details = $data_request->measurement_details;
    $notes = $data_request->catalogue_notes;
    $status = $data_request->status;
    $product_selection_id= $data_request->product_selection_id;


    //rename file for catalogue image
    $catalogue_img = $_FILES["catalogue_image"]["name"];
    $catalogue_img_path = $_FILES["catalogue_image"]["tmp_name"];

    // Convert spaces and special characters in the file name to underscores
    $catalogue_img = preg_replace('/[^A-Za-z0-9.\-]/', '_', $catalogue_img);

    if (file_exists("../../../catalogue_image/" . $catalogue_img)) {
        $i = 0;
        $ImageFileName1 = $catalogue_img;
        $Arr1 = explode('.', $ImageFileName1);

        $ImageFileName1 = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("../../../catalogue_image/" . $ImageFileName1)) {
            $i++;
            $ImageFileName1 = $Arr1[0] . $i . "." . $Arr1[1];
        }
    } else {
        $ImageFileName1 = $catalogue_img;
    }

    //rename file for rowan image
    $rowan_img = $_FILES["rowan_image"]["name"];
    $rowan_img_path = $_FILES["rowan_image"]["tmp_name"];

    // Convert spaces and special characters in the file name to underscores
    $rowan_img = preg_replace('/[^A-Za-z0-9.\-]/', '_', $rowan_img);

    if (file_exists("../../../rowan_image/" . $rowan_img)) {
        $i = 0;
        $ImageFileName2 = $rowan_img;
        $Arr1 = explode('.', $ImageFileName2);

        $ImageFileName2 = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("../../../rowan_image/" . $ImageFileName2)) {
            $i++;
            $ImageFileName2 = $Arr1[0] . $i . "." . $Arr1[1];
        }
    } else {
        $ImageFileName2 = $rowan_img;
    }

    //rename file for customer image
    $customer_img = $_FILES["customer_image"]["name"];
    $customer_img_path = $_FILES["customer_image"]["tmp_name"];

    // Convert spaces and special characters in the file name to underscores
    $customer_img = preg_replace('/[^A-Za-z0-9.\-]/', '_', $customer_img);

    if (file_exists("../../../customer_image/" . $customer_img)) {
        $i = 0;
        $ImageFileName3 = $customer_img;
        $Arr1 = explode('.', $ImageFileName3);

        $ImageFileName3 = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("../../../customer_image/" . $ImageFileName3)) {
            $i++;
            $ImageFileName3 = $Arr1[0] . $i . "." . $Arr1[1];
        }
    } else {
        $ImageFileName3 = $customer_img;
    }



    $db = new DbOperation();
    $data = array();
    $data['data'] = array();

    $result = $db->add_product_selection($inq_id, $date_time, $base_product_id, $customer_product_name, $rd_description, $sell_amount, $unit, $total_amount, $unit_of_measure, $room_name, $object, $measurement_details, $notes, $status, $ImageFileName1, $ImageFileName2, $ImageFileName3,$product_selection_id);

    if ($result>0) {

        move_uploaded_file($catalogue_img_path, "../../../catalogue_image/" . $ImageFileName1);
        move_uploaded_file($rowan_img_path, "../../../rowan_image/" . $ImageFileName2);
        move_uploaded_file($customer_img_path, "../../../customer_image/" . $ImageFileName3);

        $data['message'] = "Product selection added succesfully";
        $data['success'] = true;
        $data['selection_id']=$result;
    } else {
        $data['message'] = "Some error occured";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});

/*
 * save_product_selection
 * Parameters: none
 * Method: POST
 */
$app->post('/save_product_selection', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data_request = json_decode($app->request->post('data'));
    $selection_id = $data_request->selection_id;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->save_product_selection($selection_id);

  //  if ($result > 0) {
       
        $data['message'] = "Product selection saved successfully";
        $data['success'] = true;
    // } else {
    //     $data['message'] = "Some error occured";
    //     $data['success'] = false;
    // }
    echoResponse(200, $data);

});


/*
 * inquiry_list
 * Parameters: data : {"status":"on_going/pending/completed"}
 * Method: POST
 */
$app->post('/inquiry_list', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data_request = json_decode($app->request->post('data'));
    $status = $data_request->status;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->inquiry_list($status);

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});


/*
 * architect_list
 * Parameters: none
 * Method: POST
 */
$app->post('/architect_list', function () use ($app) {

    // verifyRequiredParams(array('data'));

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->architect_list();

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});


/*
 * category_list
 * Parameters: none
 * Method: POST
 */
$app->post('/category_list', function () use ($app) {

    // verifyRequiredParams(array('data'));

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->category_list();

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});


/*
 * base_product_list
 * Parameters: none
 * Method: POST
 */
$app->post('/base_product_list', function () use ($app) {

    // verifyRequiredParams(array('data'));

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->base_product_list();

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});


/*
 * product_selection_data
 * Parameters: data : {"selection_id":""}
 * Method: POST
 */
$app->post('/product_selection_data', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data_request = json_decode($app->request->post('data'));
    $selection_id = $data_request->selection_id;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();


    $result = $db->product_selection_data($selection_id);
    

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $result_room_pro=$db->room_product_data($row["room_name"],$selection_id);
            $temp = array();
            $temp['details']=array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
                while($room_data=$result_room_pro->fetch_assoc())
                {
                    $pro=new stdClass();
                    foreach ($room_data as $key2 => $value2) {
                        //$pro->$key2 = $value2;
                        if ($key2 == "catalouge_image") {
                            $pro->$key2 = "https://pragmanxt.com/rowan_decor/catalogue_image/" . $value2;
                        } else if ($key2 == "customer_image") {
                            $pro->$key2 = "https://pragmanxt.com/rowan_decor/customer_image/" . $value2;
                        } else if ($key2 == "rowan_image") {
                            $pro->$key2 = "https://pragmanxt.com/rowan_decor/rowan_image/" . $value2;
                        } else {
                            $pro->$key2 = $value2;
                        }
                    }
                    
                    array_push($temp['details'], $pro);


                }
                
            }
           // print_r($temp);
           // $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});

/*
 * product_selection_list
 * Parameters: none
 * Method: POST
 */
$app->post('/product_selection_list', function () use ($app) {

    // verifyRequiredParams(array('data'));

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->product_selection_list();

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});


/*
 * room_name_list
 * Parameters: none
 * Method: POST
 */
$app->post('/room_name_list', function () use ($app) {

    // verifyRequiredParams(array('data'));
    verifyRequiredParams(array('data'));
    $data_request = json_decode($app->request->post('data'));
    $product_selection_id = $data_request->product_selection_id;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->room_name_list($product_selection_id);

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});

/*
 * object_name_list
 * Parameters: none
 * Method: POST
 */
$app->post('/object_name_list', function () use ($app) {

    // verifyRequiredParams(array('data'));
    verifyRequiredParams(array('data'));
    $data_request = json_decode($app->request->post('data'));
    $product_selection_id = $data_request->product_selection_id;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->object_name_list($product_selection_id);

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});

/*
 * units_list
 * Parameters: none
 * Method: POST
 */
$app->post('/units_list', function () use ($app) {

    // verifyRequiredParams(array('data'));

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->units_list();

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});


/*
 * edit_product_selection
 * Parameters:  data : {"product_selection_details_id":"","base_product_id":"","customer_product_name":"","rd_description":"","sell_amount":"","unit":"","total_amount":"","unit_of_measue":"","room_name":"","object":"","measurment_details":"","catalogue_notes":""}
 *              catalogue_image
 *              rowan_image
 *              customer_image
 * Method: POST
 */
$app->post('/edit_product_selection', function () use ($app) {
    verifyRequiredParams(array('data'));

    $data_request = json_decode($app->request->post('data'));
    $product_selection_details_id = $data_request->product_selection_details_id;
    $base_product_id = $data_request->base_product_id;
    $customer_product_name = $data_request->customer_product_name;
    $rd_description = $data_request->rd_description;
    $sell_amount = $data_request->sell_amount;
    $unit = $data_request->unit;
    $total_amount = $data_request->total_amount;
    $unit_of_measue = $data_request->unit_of_measue;
    $room_name = $data_request->room_name;
    $object = $data_request->object;
    $measurment_details = $data_request->measurment_details;
    $catalogue_notes = $data_request->catalogue_notes;

    //rename file for catalogue image
    $catalogue_img = $_FILES["catalogue_image"]["name"];
    $catalogue_img_path = $_FILES["catalogue_image"]["tmp_name"];

    // Convert spaces and special characters in the file name to underscores
    $catalogue_img = preg_replace('/[^A-Za-z0-9.\-]/', '_', $catalogue_img);

    if (file_exists("../../../catalogue_image/" . $catalogue_img)) {
        $i = 0;
        $ImageFileName1 = $catalogue_img;
        $Arr1 = explode('.', $ImageFileName1);

        $ImageFileName1 = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("../../../catalogue_image/" . $ImageFileName1)) {
            $i++;
            $ImageFileName1 = $Arr1[0] . $i . "." . $Arr1[1];
        }
    } else {
        $ImageFileName1 = $catalogue_img;
    }

    //rename file for rowan image
    $rowan_img = $_FILES["rowan_image"]["name"];
    $rowan_img_path = $_FILES["rowan_image"]["tmp_name"];

    // Convert spaces and special characters in the file name to underscores
    $rowan_img = preg_replace('/[^A-Za-z0-9.\-]/', '_', $rowan_img);

    if (file_exists("../../../rowan_image/" . $rowan_img)) {
        $i = 0;
        $ImageFileName2 = $rowan_img;
        $Arr1 = explode('.', $ImageFileName2);

        $ImageFileName2 = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("../../../rowan_image/" . $ImageFileName2)) {
            $i++;
            $ImageFileName2 = $Arr1[0] . $i . "." . $Arr1[1];
        }
    } else {
        $ImageFileName2 = $rowan_img;
    }

    //rename file for customer image
    $customer_img = $_FILES["customer_image"]["name"];
    $customer_img_path = $_FILES["customer_image"]["tmp_name"];

    // Convert spaces and special characters in the file name to underscores
    $customer_img = preg_replace('/[^A-Za-z0-9.\-]/', '_', $customer_img);

    if (file_exists("../../../customer_image/" . $customer_img)) {
        $i = 0;
        $ImageFileName3 = $customer_img;
        $Arr1 = explode('.', $ImageFileName3);

        $ImageFileName3 = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("../../../customer_image/" . $ImageFileName3)) {
            $i++;
            $ImageFileName3 = $Arr1[0] . $i . "." . $Arr1[1];
        }
    } else {
        $ImageFileName3 = $customer_img;
    }



    $db = new DbOperation();
    $data = array();
    $data['data'] = array();

    $result = $db->edit_product_selection($product_selection_details_id, $base_product_id, $customer_product_name, $rd_description, $sell_amount, $unit, $total_amount, $unit_of_measue, $room_name, $object, $measurment_details, $catalogue_notes, $ImageFileName1, $ImageFileName2, $ImageFileName3);

    if ($result) {

        move_uploaded_file($catalogue_img_path, "../../../catalogue_image/" . $ImageFileName1);
        move_uploaded_file($rowan_img_path, "../../../rowan_image/" . $ImageFileName2);
        move_uploaded_file($customer_img_path, "../../../customer_image/" . $ImageFileName3);

        $data['message'] = "Product selection edited succesfully";
        $data['success'] = true;
    } else {
        $data['message'] = "Some error occured";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});


/*
 * delete_product_selection
 * Parameters: data : {"product_selection_details_id":""}
 * Method: POST
 */
$app->post('/delete_product_selection', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data_request = json_decode($app->request->post('data'));
    $product_selection_details_id = $data_request->product_selection_details_id;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    $result = $db->delete_product_selection($product_selection_details_id);

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
                if ($key == "catalouge_image") {
                    unlink("../../../catalogue_image/" . $value);
                } else if ($key == "rowan_image") {
                    unlink("../../../rowan_image/" . $value);
                } else if ($key == "customer_image") {
                    unlink("../../../customer_image/" . $value);
                }
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});

/*
 * site_visit_measurement
 * Parameters: none
 * Method: POST
 */
$app->post('/site_visit_measurement', function () use ($app) {

    // verifyRequiredParams(array('data'));

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();
 
    $result = $db->site_visit_measurement();

    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
                $pro_count=$db->get_product_count($row["id"]);
                $temp["total_products"]=$pro_count["measurable_products_count"];
                $temp["pending_products"]=$pro_count["pending_measurement_count"];
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});

/*
 * product_link_under
 * Parameters: data : {"product_selection_id":""}
 * Method: POST
 */
$app->post('/product_link_under', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data_request = json_decode($app->request->post('data'));
    $product_selection_id = $data_request->product_selection_id;

    $db = new DbOperation();
    $data = array();
    $data["data"] = array();
 
    $result = $db->product_link_under($product_selection_id);
    // print_r($result); 
    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp = array();
            foreach ($row as $key => $value) {
                $temp[$key] = $value;
            }
            $temp = array_map('utf8_encode', $temp);
            array_push($data['data'], $temp);
        }
        $data['message'] = "";
        $data['success'] = true;
    } else {
        $data['message'] = "No result found";
        $data['success'] = false;
    }
    echoResponse(200, $data);

});



function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["error_code"] = 99;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}
function echoResponse($status_code, $response)
{
    $app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}


$app->run();
