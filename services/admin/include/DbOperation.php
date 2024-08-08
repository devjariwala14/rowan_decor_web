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
        $stmt_login = $this->con->prepare("SELECT `id`,`type`,`name`,`email`,`contact` FROM `users` WHERE `username`=? AND BINARY `password`=?");
        $stmt_login->bind_param("ss", $user_id, $password);
        $stmt_login->execute();
        $result = $stmt_login->get_result();
        $stmt_login->close();
        return $result;
    }

    public function add_new_user($full_name, $mobile_no, $whatsapp_no, $ref_name, $place, $visiting_person, $remark)
    {
        if ($visiting_person == "Architect") {
            $status = "enable";
            $stmt_user = $this->con->prepare("INSERT INTO `architect`(`name`, `contact`, `whatsapp_no`, `status`) VALUES (?,?,?,?)");
            $stmt_user->bind_param("siis", $full_name, $mobile_no, $whatsapp_no, $status);
            $result = $stmt_user->execute();
            $stmt_user->close();
            return $result;
        } else {
            $stmt_user = $this->con->prepare("INSERT INTO `visitor`(`full_name`, `mobile_no`, `whatsapp_no`, `ref_name`, `place`, `visiting_person`, `remark`) VALUES (?,?,?,?,?,?,?)");
            $stmt_user->bind_param("siissss", $full_name, $mobile_no, $whatsapp_no, $ref_name, $place, $visiting_person, $remark);
            $result = $stmt_user->execute();
            $stmt_user->close();
            return $result;
        }
    }


    public function room_name_list($selection_id)
    {
        
        //$stmt_user = $this->con->prepare("SELECT a.room_name AS room_name FROM product_selection_details AS a WHERE a.selection_id = ? UNION SELECT b.room_name AS room_name FROM rooms AS b");
        $stmt_user = $this->con->prepare("SELECT distinct(room_name) AS room_name FROM product_selection_details WHERE selection_id = ? UNION SELECT room_name AS room_name FROM rooms ");
        $stmt_user->bind_param("i",$selection_id);
        $stmt_user->execute();
        $result = $stmt_user->get_result();
        $stmt_user->close();
        return $result;
    }

    public function object_name_list($selection_id)
    {
        $stmt_user = $this->con->prepare("SELECT distinct(`object`) AS object_name FROM product_selection_details WHERE selection_id = ? UNION select object_name from objects;");
        $stmt_user->bind_param("i",$selection_id);
        $stmt_user->execute();
        $result = $stmt_user->get_result();
        $stmt_user->close();
        return $result;
    }
    public function units_list()
    {
        $stmt_user = $this->con->prepare("select * from units");
        $stmt_user->execute();
        $result = $stmt_user->get_result();
        $stmt_user->close();
        return $result;
    }

    public function edit_visitor(
        $visitor_id,
        $full_name,
        $mobile_no,
        $whatsapp_no,
        $ref_name,
        $place,
        $visiting_person,
        $remark
    ) {
        $stmt_visit = $this->con->prepare("UPDATE `visitor` SET `full_name`=?,`mobile_no`=?,`whatsapp_no`=?,`ref_name`=?,`place`=?,`visiting_person`=?,`remark`=? WHERE `id`=?");
        $stmt_visit->bind_param("siissssi", $full_name, $mobile_no, $whatsapp_no, $ref_name, $place, $visiting_person, $remark, $visitor_id);
        $result = $stmt_visit->execute();
        $stmt_visit->close();
        // $result = "UPDATE `visitor` SET `full_name`='$full_name',`mobile_no`='$mobile_no',`whatsapp_no`='$whatsapp_no',`ref_name`='$ref_name',`place`='$place',`visiting_person`='$visiting_person',`remark`='$remark' WHERE `id`='$visitor_id'";
        return $result;
    }

    public function all_visitor()
    {
        $stmt_user = $this->con->prepare("select * from visitor order by id desc");
        $stmt_user->execute();
        $result = $stmt_user->get_result();
        $stmt_user->close();
        return $result;
    }

    public function category_list()
    {
        $stmt_user = $this->con->prepare("select * from category order by id desc");
        $stmt_user->execute();
        $result = $stmt_user->get_result();
        $stmt_user->close();
        return $result;
    }
    public function architect_list()
    {
        $stmt_user = $this->con->prepare("select * from architect order by id desc");
        $stmt_user->execute();
        $result = $stmt_user->get_result();
        $stmt_user->close();
        return $result;
    }

    public function add_new_inquiry($visitor_id, $inquired_for, $attended_by, $architect_id, $address, $suggestions, $start_date, $image)
    {
        $status = "enable";
        $stmt_inq = $this->con->prepare("INSERT INTO `inquiry`(`visitor_id`, `inquired_for`, `attended_by`, `architect_id`, `address`, `suggestions`, `start_date`,`status`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt_inq->bind_param("isiissss", $visitor_id, $inquired_for, $attended_by, $architect_id, $address, $suggestions, $start_date,$status);
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

    public function add_product_selection($inq_id, $date_time, $base_product_id, $customer_product_name, $rd_description, $sell_amount, $unit, $total_amount, $unit_of_measure, $room_name, $object, $measurement_details, $notes, $status, $catalogue_image, $rowan_image, $customer_image, $product_selection_id)
    {
        $status1 = "on_going";
        $status2 = "enable";
        if ($product_selection_id == 0) {
            $stmt_prod = $this->con->prepare("INSERT INTO `product_selection`(`inq_id`, `status`) VALUES (?,?)");
            $stmt_prod->bind_param("is", $inq_id, $status1);
            $ressult = $stmt_prod->execute();
            $stmt_prod->close();

            $selection_id = mysqli_insert_id($this->con);
        } else {
            $selection_id = $product_selection_id;
        }


        $stmt_prod = $this->con->prepare("INSERT INTO `product_selection_details`(`selection_id`, `base_product_id`, `customer_product_name`, `rd_description`, `sell_amount`, `unit`, `total_amount`, `unit_of_measure`, `room_name`, `object`, `measurement_details`, `catalouge_notes`, `catalouge_image`, `rowan_image`, `customer_image`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt_prod->bind_param("iisssissssssssss", $selection_id, $base_product_id, $customer_product_name, $rd_description, $sell_amount, $unit, $total_amount, $unit_of_measure, $room_name, $object, $measurement_details, $notes, $catalogue_image, $rowan_image, $customer_image, $status2);
        $ressult2 = $stmt_prod->execute();
        $stmt_prod->close();
        return $selection_id;

    }

    public function inquiry_list($status)
    {
        $stmt_inq = ($status == "on_going") ? $this->con->prepare("SELECT a.*, b.full_name, b.mobile_no, COALESCE(c.id, 0) AS product_selection_id FROM inquiry AS a JOIN visitor AS b ON a.visitor_id = b.id LEFT JOIN product_selection AS c ON c.inq_id = a.id AND c.status = ? order by a.id desc;") : $this->con->prepare("SELECT a.*, b.full_name, b.mobile_no, COALESCE(c.id, 0) AS product_selection_id FROM inquiry AS a JOIN visitor AS b ON a.visitor_id = b.id JOIN product_selection AS c ON c.inq_id = a.id WHERE c.status = ? order by a.id desc;");
        $stmt_inq->bind_param('s', $status);
        $stmt_inq->execute();
        $result = $stmt_inq->get_result();
        $stmt_inq->close();
        return $result;
    }

    public function base_product_list()
    {
        $stmt_inq = $this->con->prepare("select a.id , a.name , a.price , b.company_name , c.name as category_name, d.unit_name , a.status from product as a  , company as b , category as c , units as d  where a.company_id = b.id and a.category_id = c.id and a.unit_id = d.id order by a.id desc");
        $stmt_inq->execute();
        $result = $stmt_inq->get_result();
        $stmt_inq->close();
        return $result;
    }

    public function product_selection_data($selection_id)
    {
        //$stmt_prod = $this->con->prepare("select *,sum(unit) as qty from product_selection_details where `selection_id` = ?  GROUP by room_name");
        $stmt_prod = $this->con->prepare("select DISTINCT(room_name) from product_selection_details where `selection_id` = ? ");
        $stmt_prod->bind_param('i', $selection_id);
        $stmt_prod->execute();
        $result = $stmt_prod->get_result();
        $stmt_prod->close();
        return $result;
    }

    public function room_product_data($room_name,$selection_id)
    {
        
        $stmt_prod = $this->con->prepare("select * from product_selection_details where `room_name` = ? and selection_id=?");
        $stmt_prod->bind_param('si', $room_name,$selection_id);
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

        return ($result) ? $result2 : null;
    }
    public function save_product_selection($selection_id)
    {
        $stmt_prod = $this->con->prepare("update product_selection set `status`='pending' where id=?");
        $stmt_prod->bind_param("i", $selection_id);
        $stmt_prod->execute();
        $result_pro = $stmt_prod->affected_rows;
        $stmt_prod->close();
        return $result_pro;

    }
    public function site_visit_measurement()
    {

        $stmt_prod = $this->con->prepare("SELECT ps.id,v.full_name,v.mobile_no,v.whatsapp_no,iq.suggestions,ps.date_time,iq.start_date,iq.address,a1.name as architect FROM `product_selection_details` psd,product_selection ps,product p, inquiry iq,visitor v,architect a1 where psd.selection_id=ps.id and ps.inq_id=iq.id and iq.visitor_id=v.id and psd.base_product_id=p.id and iq.architect_id=a1.id group by ps.id");

        $stmt_prod->execute();
        $result = $stmt_prod->get_result();
        $stmt_prod->close();
        return $result;
    }
    public function get_product_count($sel_id)
    {

        $stmt_prod = $this->con->prepare("SELECT SUM(CASE WHEN c.measurable = 'Y' THEN 1 ELSE 0 END) AS measurable_products_count,SUM(CASE WHEN c.measurable = 'Y' AND svm.base_product_id IS NULL THEN 1 ELSE 0 END) AS pending_measurement_count FROM product_selection_details psd JOIN product p ON psd.base_product_id = p.id JOIN category c ON p.category_id = c.id LEFT JOIN site_visit_measurement svm ON p.id = svm.base_product_id WHERE  psd.selection_id =?");
        $stmt_prod->bind_param("i", $sel_id);
        $stmt_prod->execute();
        $result2 = $stmt_prod->get_result()->fetch_assoc();
        $stmt_prod->close();
        return $result2;
    }

    public function product_link_under($id)
    {
        $stmt_prod = $this->con->prepare("select room_name from product_selection_details where selection_id = ?;");
        $stmt_prod->bind_param("i", $id);
        $stmt_prod->execute();
        $result = $stmt_prod->get_result();
        $stmt_prod->close();
        return $result;
    }

}
?>