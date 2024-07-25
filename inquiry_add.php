<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("select * from architect where id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("select * from architect where id=?");
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
    $attended_by = $_REQUEST["attended_by"];
	$architect = $_REQUEST["architect"];
	$address = $_REQUEST["address"];
	$suggetion = $_REQUEST["suggestions"];
	$start_date = $_REQUEST["sdate"];
	$status = $_REQUEST['status'];

	try
	{  
		// echo"INSERT INTO `inquiry`(`visitor_id`=".$visitor.", `inquired_for`, `attended_by`, `architect_id`, `address`, `suggestions`, `start_date`,`status`)";
		$stmt = $obj->con1->prepare("INSERT INTO `inquiry`(`visitor_id`, `inquired_for`, `attended_by`, `architect_id`, `address`, `suggestions`, `start_date`,`status`) VALUES (?,?,?,?,?,?,?,?)");
		$stmt->bind_param("ssssssss",$visitor,$inquired_for,$attended_by,$architect,$address,$suggetion,$start_date,$status);
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
		$stmt->bind_param("ssssssssi",$visitor,$inquired_for,$attended_by,$architect,$address,$suggetion,$start_date,$status,$e_id);
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
                        <label class="form-label" for="basic-default-fullname">Inquired For</label>
                        <input type="text" class="form-control" name="inq_for" id="inq_for"
                            value="<?php echo (isset($mode)) ? $data['inquiry_for'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Attended By</label>
                        <input type="text" class="form-control" name="attended_by" id="attended_by"
                            value="<?php echo (isset($mode)) ? $data['attended_by'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
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
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required><?php echo (isset($mode)) ? $data['address'] : '' ?></textarea>
                    </div>
					<div>
                        <label for="suggestions" class="form-label">Suggestions</label>
                        <textarea class="form-control" id="suggestions" name="suggestions" rows="2"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required><?php echo (isset($mode)) ? $data['suggestions'] : '' ?></textarea>
                    </div>
					<div class="col mb-3">
                        <label for="date" class="col-md-2 col-form-label">Start Date</label>
                          <input class="form-control" type="date" name="sdate" id="sdate" value="<?php echo (isset($mode)) ? $data['date'] : '' ?>"
						  <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                      </div>
                    <div class="mb-3">
                        <label class="form-label d-block" for="basic-default-fullname">Status</label>
                        <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="Enable" value="Enable"
                                <?php echo isset($mode) && $data['status'] == 'Enable' ? 'checked' : '' ?>
                                <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required checked>
                            <label class="form-check-label" for="inlineRadio1">Enable</label>
                        </div>
                        <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="Disable" value="Disable"
                                <?php echo isset($mode) && $data['status'] == 'Disable' ? 'checked' : '' ?>
                                <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                            <label class="form-check-label" for="inlineRadio1">Disable</label>
                        </div>
                    </div>
                    <button type="submit"
                        name="<?php echo isset($mode) && $mode == 'edit' ? 'btnupdate' : 'btnsubmit' ?>" id="save"
                        class="btn btn-primary <?php echo isset($mode) && $mode == 'view' ? 'd-none' : '' ?>">
                        <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                    </button>
                    <button type="button" class="btn btn-secondary"
                        onclick="<?php echo (isset($mode)) ? 'javascript:go_back()' : 'window.location.reload()' ?>">
                        Close</button>

                </form>
            </div>
        </div>
    </div>

</div>
<script>
function go_back() {
    eraseCookie("edit_id");
    eraseCookie("view_id");
    window.location = "inquiry.php";
}
</script>
<?php
include "footer.php";
?>