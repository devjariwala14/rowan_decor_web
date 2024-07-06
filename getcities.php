<?php 
    include "db_connect.php";
    $obj = new db_connect();
    $s = $_REQUEST["sid"];
    $ctid = $_REQUEST["ctid"];
    $stmt = $obj->con1->prepare("SELECT * FROM `city` WHERE `state_id` = ?");
    $stmt->bind_param("i", $s);
    $stmt->execute();
    $result = $stmt->get_result();
?>
<option value = "">Choose City</option>
<?php
    while($row=mysqli_fetch_assoc($result)){
?>
<option value = "<?php echo $row["srno"];?>"><?php echo $row["ctnm"];?></option>
<?php
}
?>
