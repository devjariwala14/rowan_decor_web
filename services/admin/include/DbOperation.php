<?php
date_default_timezone_set("Asia/Kolkata");
class DbOperation
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }

    public function login($user_id, $password)
    {
        $stmt_login = $this->con->prepare("SELECT `id` FROM `admin` WHERE `username`=? AND BINARY `password`=?");
        $stmt_login->bind_param("ss", $user_id, $password);
        $stmt_login->execute();
        $result = $stmt_login->get_result();
        $stmt_login->close();
        return $result;
    }

    public function add_new_user($full_name, $mobile_no, $whatsapp_no, $ref_name, $place, $visiting_person, $remark)
    {
        $stmt_user = $this->con->prepare("INSERT INTO `visitor`(`full_name`, `mobile_no`, `whatsapp_no`, `ref_name`, `place`, `visiting_person`, `remark`) VALUES (?,?,?,?,?,?,?)");
        $stmt_user->bind_param("siissss", $full_name, $mobile_no, $whatsapp_no, $ref_name, $place, $visiting_person, $remark);
        $result = $stmt_user->execute();
        $stmt_user->close();
        return $result;
    }

    public function all_visitor()
    {
        $stmt_user = $this->con->prepare("select * from visitor");
        $stmt_user->execute();
        $result = $stmt_user->get_result();
        $stmt_user->close();
        return $result;
    }

    public function add_new_inquiry($visitor_id, $inquired_for, $attended_by, $architect_id, $address, $suggestions, $start_date, $image)
    {
        $stmt_inq = $this->con->prepare("INSERT INTO `inquiry`(`visitor_id`, `inquired_for`, `attended_by`, `architect_id`, `address`, `suggestions`, `start_date`) VALUES (?,?,?,?,?,?,?)");
        $stmt_inq->bind_param("iiiisss", $visitor_id, $inquired_for, $attended_by, $architect_id, $address, $suggestions, $start_date);
        $result = $stmt_inq->execute();
        $stmt_inq->close();

        $inq_id = mysqli_insert_id($this->con);

        $stmt_img = $this->con->prepare("INSERT INTO `property_image`(`inq_id`,`image`) VALUES (?,?)");
        $stmt_img->bind_param("ss", $inq_id, $image);
        $result2 = $stmt_img->execute();
        $stmt_img->close();

        return ($result && $result2) ? true : false;
    }

    public function add_new_architect($name, $contact_no, $whatsapp_no)
    {
        $status = "enable";
        $stmt_user = $this->con->prepare("INSERT INTO `architect`(`name`, `contact`, `whatsapp_no`,`status`) VALUES (?,?,?,?)");
        $stmt_user->bind_param("siis", $name, $contact_no, $whatsapp_no, $status);
        $result = $stmt_user->execute();
        $stmt_user->close();
        return $result;
    }

    public function add_product_selection($inq_id, $date_time, $base_product_id, $customer_product_name, $rd_description, $sell_amount, $unit, $total_amount, $unit_of_measure, $room_name, $object, $measurement_details, $notes, $status, $catalogue_image, $rowan_image, $customer_image)
    {
        $status = "enable";
        $stmt_prod = $this->con->prepare("INSERT INTO `product_selection`(`inq_id`, `status`) VALUES (?,?)");
        $stmt_prod->bind_param("is", $inq_id, $status);
        $ressult = $stmt_prod->execute();
        $stmt_prod->close();

        $selection_id = mysqli_insert_id($this->con);

        $stmt_prod = $this->con->prepare("INSERT INTO `product_selection_details`(`selection_id`, `base_product_id`, `customer_product_name`, `rd_description`, `sell_amount`, `unit`, `total_amount`, `unit_of_measure`, `room_name`, `object`, `measurement_details`, `catalouge_notes`, `catalouge_image`, `rowan_image`, `customer_image`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt_prod->bind_param("iisssissssssssss", $selection_id, $base_product_id, $customer_product_name, $rd_description, $sell_amount, $unit, $total_amount, $unit_of_measure, $room_name, $object, $measurement_details, $notes, $catalogue_image, $rowan_image, $customer_image, $status);
        $ressult2 = $stmt_prod->execute();
        $stmt_prod->close();

        return ($ressult && $ressult2) ? true : false;

    }

    public function inquiry_list()
    {
        $stmt_inq = $this->con->prepare("select * from inquiry");
        $stmt_inq->execute();
        $result = $stmt_inq->get_result();
        $stmt_inq->close();
        return $result;
    }

    public function base_product_list()
    {
        $stmt_inq = $this->con->prepare("select * from product");
        $stmt_inq->execute();
        $result = $stmt_inq->get_result();
        $stmt_inq->close();
        return $result;
    }

    public function product_selection_data($selection_id)
    {
        $stmt_prod = $this->con->prepare("select * from product_selection_details where `selection_id` = ?");
        $stmt_prod->bind_param('i', $selection_id);
        $stmt_prod->execute();
        $result = $stmt_prod->get_result();
        $stmt_prod->close();
        return $result;
    }

    public function product_selection_list()
    {
        $stmt_prod = $this->con->prepare("select p.*,i.* from inquiry as i,product_selection as p  where i.id = p.inq_id");
        $stmt_prod->execute();
        $result = $stmt_prod->get_result();
        $stmt_prod->close();
        return $result;
    }

    public function edit_product_selection($product_selection_details_id, $base_product_id, $customer_product_name, $rd_description, $sell_amount, $unit, $total_amount, $unit_of_measue, $room_name, $object, $measurment_details, $catalogue_notes, $ImageFileName1, $ImageFileName2, $ImageFileName3)
    {

        $stmt_prod = $this->con->prepare("UPDATE `product_selection_details` SET `base_product_id`=?,`customer_product_name`=?,`rd_description`=?,`sell_amount`=?,`unit`=?,`total_amount`=?,`unit_of_measure`=?,`room_name`=?,`object`=?,`measurement_details`=?,`catalouge_notes`=?,`catalouge_image`=?,`rowan_image`=?,`customer_image`=? WHERE `id`=?");
        $stmt_prod->bind_param('isssisssssssssi', $base_product_id, $customer_product_name, $rd_description, $sell_amount, $unit, $total_amount, $unit_of_measue, $room_name, $object, $measurment_details, $catalogue_notes, $ImageFileName1, $ImageFileName2, $ImageFileName3, $product_selection_details_id);
        $result = $stmt_prod->execute();
        $stmt_prod->close();
        return $result;

    }

    public function delete_product_selection($product_selection_details_id)
    {

        $stmt_prod = $this->con->prepare("select catalouge_image,rowan_image,customer_image from product_selection_details where id = ?");
        $stmt_prod->bind_param("i", $product_selection_details_id);
        $stmt_prod->execute();
        $result2 = $stmt_prod->get_result();
        $stmt_prod->close();

        $stmt_prod = $this->con->prepare("delete from product_selection_details where id = ?");
        $stmt_prod->bind_param("i", $product_selection_details_id);
        $result = $stmt_prod->execute();
        $stmt_prod->close();

        return ($result)? $result2 : null;
    }

}
?>