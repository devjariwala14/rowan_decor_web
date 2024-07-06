<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("select * from visitor where id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("select * from visitor where id=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$visitor_name = $_REQUEST['full_name'];
    $mobile = $_REQUEST['mobile'];
    $whatsapp = $_REQUEST['whatsapp'];
    $ref_name = $_REQUEST['ref_name'];
    $place = $_REQUEST['place'];
    $vis_person = $_REQUEST['v_per'];
    $remark = $_REQUEST['remark'];
	
	try
	{
		$stmt = $obj->con1->prepare("INSERT INTO `visitor`(`full_name`,`mobile_no`,`whatsapp_no`,`ref_name`,`place`,`visiting_person`,`remark`) VALUES (?,?,?,?,?,?,?)");
		$stmt->bind_param("sssssss",$visitor_name, $mobile,$whatsapp ,$ref_name,$place, $vis_person, $remark );
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
		header("location:visitor.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:visitor.php");
	}
}

if(isset($_REQUEST['btnupdate']))
{
    $e_id=$_COOKIE['edit_id'];
	$visitor_name = $_REQUEST['full_name'];
    $mobile = $_REQUEST['mobile'];
    $whatsapp = $_REQUEST['whatsapp'];
    $ref_name = $_REQUEST['ref_name'];
    $place = $_REQUEST['place'];
    $vis_person = $_REQUEST['v_per'];
    $remark = $_REQUEST['remark'];
	
	
	try
	{
        // echo"UPDATE visitor SET `unit_name`=$unit_name, `abbriviation`=$abbriviation, `status`=$status where id=$e_id";
		$stmt = $obj->con1->prepare("UPDATE visitor SET`full_name`=?,`mobile_no`=?,`whatsapp_no`=?,`ref_name`=?,`place`=?,`visiting_person`=?,`remark`=? where id=?");
		$stmt->bind_param("sssssssi",$visitor_name, $mobile, $whatsapp ,$ref_name,$place, $vis_person, $remark,$e_id);
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
		header("location:visitor.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		 header("location:visitor.php");
	}
}
?>
<div class="row" id="p1">
    <div class="col-xl">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> Visitor
                </h5>

            </div>
            <div class="card-body">
                <form method="post">
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Visitor Name</label>
                        <input type="text" class="form-control" name="full_name" id="full_name"
                            value="<?php echo (isset($mode)) ? $data['full_name'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Mobile Number</label>
                            <input type="text" class="form-control" name="mobile" id="mobile" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10"
                                value="<?php echo (isset($mode)) ? $data['mobile_no'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">WhatsApp Number</label>
                            <input type="text" class="form-control" name="whatsapp" id="whatsapp" keypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10"
                                value="<?php echo (isset($mode)) ? $data['whatsapp_no'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Refrence Name</label>
                        <input type="text" class="form-control" name="ref_name" id="ref_name"
                            value="<?php echo (isset($mode)) ? $data['ref_name'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Place</label>
                        <select name="place" id="place" class="form-control" <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                        <option value="">Choose Place</option>
                                <option value="site"
                                    <?php echo isset($mode) && $data['place'] == "site" ? "selected" : "" ?>>Visit Site
                                </option>
                                <option value="studio"
                                    <?php echo isset($mode) && $data['place'] == "studio" ? "selected" : "" ?>>Visit Studio
                                </option>
                                
                        </select>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Visiting Person</label>
                        <select name="v_per" id="v_per" class="form-control" <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                        <option value="">Choose Place</option>
                                <option value="customer"
                                    <?php echo isset($mode) && $data['visiting_person'] == "customer" ? "selected" : "" ?>>Customer
                                </option>
                                <option value="architect"
                                    <?php echo isset($mode) && $data['visiting_person'] == "architect" ? "selected" : "" ?>>Architect
                                </option>
                                
                        </select>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Remark</label>
                        <textarea class="form-control" name="remark" id="remark" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>><?php echo (isset($mode)) ? $data['remark'] : '' ?></textarea>
                    </div>


                    <button type="submit"
                        name="<?php echo isset($mode) && $mode == 'edit' ? 'btnupdate' : 'btnsubmit' ?>" id="save"
                        class="btn btn-primary <?php echo isset($mode) && $mode == 'view' ? 'd-none' : '' ?>">
                        <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                    </button>
                    <button type="button" class="btn btn-secondary"
                        onclick="javascript:go_back()">
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
    window.location = "visitor.php";
}
</script>
<?php
include "footer.php";
?>