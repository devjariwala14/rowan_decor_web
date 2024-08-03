<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("SELECT * FROM `inquiry` WHERE id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("SELECT * FROM `inquiry` WHERE id=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$visitor = $_REQUEST["visitor"];
    $inquired_for = $_REQUEST["inq_for"];
    $inquired_for_str = implode(',', $inquired_for); 
    $attended_by = $_REQUEST["attended_by"];
	$architect = $_REQUEST["architect"];
	$address = $_REQUEST["address"];
	$suggetion = $_REQUEST["suggestions"];
	$start_date = $_REQUEST["sdate"];
	$status = $_REQUEST['status'];

	try
	{  
		// echo "INSERT INTO `inquiry`(`visitor_id`=".$visitor.", `inquired_for`=".$inquired_for_str.", `attended_by`=".$attended_by.", `architect_id`=".$architect.", `address`=".$address.", `suggestions`=".$suggetion.", `start_date`=".$start_date.",`status`=".$status.")";
		$stmt = $obj->con1->prepare("INSERT INTO `inquiry`(`visitor_id`, `inquired_for`, `attended_by`, `architect_id`, `address`, `suggestions`, `start_date`,`status`) VALUES (?,?,?,?,?,?,?,?)");
		$stmt->bind_param("isiissss",$visitor,$inquired_for_str,$attended_by,$architect,$address,$suggetion,$start_date,$status);
		$Resp=$stmt->execute();
		if(!$Resp)
		{
			throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
		}
		$stmt->close();
	} 
	catch(\Exception  $e) {
		setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
	}


	if($Resp)
	{
		setcookie("msg", "data",time()+3600,"/");
		header("location:inquiry.php");
	 }
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:inquiry.php");
	}
}

if(isset($_REQUEST['btnupdate']))
{
	$e_id=$_COOKIE['edit_id'];
	$visitor = $_REQUEST["visitor"];
    $inquired_for = $_REQUEST["inq_for"];
    $inquired_for_str = implode(',', $inquired_for);
    $attended_by = $_REQUEST["attended_by"];
	$architect = $_REQUEST["architect"];
	$address = $_REQUEST["address"];
	$suggetion = $_REQUEST["suggestions"];
	$start_date = $_REQUEST["sdate"];
	$status = $_REQUEST["status"];
	
	
	try
	{
        // echo"UPDATE architect SET `name`=$name, `contact`=$contact, `status`=$status where id=$e_id";
		$stmt = $obj->con1->prepare("UPDATE `inquiry` SET `visitor_id`=?, `inquired_for`=?, `attended_by`=?, `architect_id`=?, `address`=?, `suggestions`=?, `start_date`=?,`status`=? WHERE id =?");
		$stmt->bind_param("isiissssi",$visitor,$inquired_for_str,$attended_by,$architect,$address,$suggetion,$start_date,$status,$e_id);
		$Resp=$stmt->execute();
		if(!$Resp)
		{
			throw new Exception("Problem in updating! ". strtok($obj->con1-> error,  '('));
		}
		$stmt->close();
	} 
	catch(\Exception  $e) {
		setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
	}


	if($Resp)
	{
		setcookie("msg", "update",time()+3600,"/");
		header("location:inquiry.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		 header("location:inquiry.php");
	}
}

?>
<div class="row" id="p1">
    <div class="col-xl">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> Inquiry
                </h5>

            </div>
            <div class="card-body">
                <form method="post">
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Visitor</label>
                        <select name="visitor" id="visitor" class="form-control"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                            <option value="">Select Visitor</option>
                            <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `visitor` WHERE `status`= 'Enable'");
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($product=mysqli_fetch_array($result))
                                        {
                                    ?>
                            <option value="<?php echo $product["id"]?>"
                                <?php echo isset($mode) && $data['visitor_id'] == $product["id"] ? 'selected' : '' ?>>
                                <?php echo $product["full_name"]?></option>
                            <?php
								}
								?>
                        </select>
                        <input type="hidden" name="ttId" id="ttId">
                    </div>

                    <div class="col mb-3">
                        <label for="inq_for" class="form-label">Inquired For</label>
                        <select required class="form-select js-example-basic-multiple"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> multiple name="inq_for[]"
                            id="inq_for">
                            <option value="">Select Category</option> <!-- Optional placeholder -->
                            <?php
                                $stmt_list = $obj->con1->prepare("SELECT * FROM `category` WHERE `status`= 'Enable'");
                                $stmt_list->execute();
                                $result = $stmt_list->get_result();
                                $stmt_list->close();
                                $selected_categories = explode(',', $data['inquired_for']); // Handle multiple selections
                                while($res = mysqli_fetch_array($result))
                                {
                            ?>
                            <option value="<?php echo $res["id"]?>"
                                <?php echo isset($mode) && in_array($res["id"], $selected_categories) ? 'selected' : '' ?>>
                                <?php echo $res["name"]?></option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Attended By</label>
                        <select class="form-control" name="attended_by" id="attended_by"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                            <option value="">Select User</option>
                            <?php
                                // Retrieve users from the user table
                                $stmt_users = $obj->con1->prepare("SELECT id, name FROM users ORDER BY name ASC");
                                $stmt_users->execute();
                                $result_users = $stmt_users->get_result();

                                // Populate the dropdown with user names
                                while ($user = $result_users->fetch_assoc()) {
                                    $selected = (isset($mode) && $data['attended_by'] == $user['id']) ? 'selected' : '';
                                    echo "<option value='".$user['id']."' $selected>".$user['name']."</option>";
                                }

                                    $stmt_users->close();
                                    ?>
                        </select>
                    </div>

                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Architect</label>
                        <select name="architect" id="architect" class="form-control"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                            <option value="">Select Architect</option>
                            <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `architect` WHERE `status`= 'Enable'");
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($product=mysqli_fetch_array($result))
                                        {
                                    ?>
                            <option value="<?php echo $product["id"]?>"
                                <?php echo isset($mode) && $data['architect_id'] == $product["id"] ? 'selected' : '' ?>>
                                <?php echo $product["name"]?></option>
                            <?php
								}
								?>
                        </select>
                        <input type="hidden" name="ttId" id="ttId">
                    </div>
                    <div>
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>
                            required><?php echo (isset($mode)) ? $data['address'] : '' ?></textarea>
                    </div>
                    <div>
                        <label for="suggestions" class="form-label">Suggestions</label>
                        <textarea class="form-control" id="suggestions" name="suggestions" rows="2"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>
                            required><?php echo (isset($mode)) ? $data['suggestions'] : '' ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="date" class="col-md-2 col-form-label">Start Date</label>
                            <input class="form-control" type="date" name="sdate" id="sdate"
                                value="<?php echo (isset($mode)) ? $data['start_date'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                        <div class="col-6 mb-3 mt-2">
                            <div class="mb-3">
                                <label class="form-label d-block" for="basic-default-fullname">Status</label>
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="status" id="Enable"
                                        value="Enable"
                                        <?php echo isset($mode) && $data['status'] == 'Enable' ? 'checked' : '' ?>
                                        <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required
                                        checked>
                                    <label class="form-check-label" for="inlineRadio1">Enable</label>
                                </div>
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="status" id="Disable"
                                        value="Disable"
                                        <?php echo isset($mode) && $data['status'] == 'Disable' ? 'checked' : '' ?>
                                        <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                                    <label class="form-check-label" for="inlineRadio1">Disable</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit"
                        name="<?php echo isset($mode) && $mode == 'edit' ? 'btnupdate' : 'btnsubmit' ?>" id="save"
                        class="btn btn-primary <?php echo isset($mode) && $mode == 'view' ? 'd-none' : '' ?>">
                        <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="javascript:go_back()">
                        Close</button>

                </form>
            </div>
        </div>
    </div>

</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});

function go_back() {
    eraseCookie("edit_id");
    eraseCookie("view_id");
    window.location = "inquiry.php";
}
</script>
<?php
include "footer.php";
?>