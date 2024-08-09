<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("SELECT * FROM `rooms` WHERE id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("SELECT * FROM `rooms` WHERE id=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$room_name = $_REQUEST['room_name'];
	$status = $_REQUEST['status'];

	try
	{
		$stmt = $obj->con1->prepare("INSERT INTO `rooms`(`room_name`,`status`) VALUES (?,?)");
		$stmt->bind_param("ss",$room_name,$status);
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
		header("location:rooms.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:rooms.php");
	}
}

if(isset($_REQUEST['btnupdate']))
{
	$room_name = $_REQUEST['room_name'];
	$status = $_REQUEST['status'];
	$e_id=$_COOKIE['edit_id'];
	
	try
	{
        // echo"UPDATE units SET `unit_name`=$unit_name, `abbriviation`=$abbriviation, `status`=$status where id=$e_id";
		$stmt = $obj->con1->prepare("UPDATE `rooms` SET `room_name`=?,`status`=? where id=?");
		$stmt->bind_param("ssi",$room_name,$status,$e_id);
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
		header("location:rooms.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		 header("location:rooms.php");
	}
}
?>
<div class="row" id="p1">
    <div class="col-xl">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> Room
                </h5>

            </div>
            <div class="card-body">
                <form method="post">
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Room Name</label>
                        <input type="text" class="form-control" name="room_name" id="room_name"
                            value="<?php echo (isset($mode)) ? $data['room_name'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Status</label>
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="radio" name="status" id="enable" value="enable"
                                    <?php echo isset($mode) && $data['status'] == 'enable' ? 'checked' : '' ?>
                                    <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required checked>
                                <label class="form-check-label" for="inlineRadio1">Enable</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="radio" name="status" id="disable" value="disable"
                                    <?php echo isset($mode) && $data['status'] == 'disable' ? 'checked' : '' ?>
                                    <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                                <label class="form-check-label" for="inlineRadio1">Disable</label>
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
<script>
function go_back() {
    eraseCookie("edit_id");
    eraseCookie("view_id");
    window.location = "rooms.php";
}
</script>
<?php
include "footer.php";
?>