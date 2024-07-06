<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("select * from units where id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("select * from units where id=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$unit_name = $_REQUEST['unit_name'];
    $abbriviation = $_REQUEST['abbriviation'];
	$status = $_REQUEST['status'];

	try
	{
		$stmt = $obj->con1->prepare("INSERT INTO `units`(`unit_name`,`abbriviation`,`status`) VALUES (?,?,?)");
		$stmt->bind_param("sss",$unit_name,$abbriviation,$status);
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
		header("location:units.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:units.php");
	}
}

if(isset($_REQUEST['btnupdate']))
{
	$unit_name = $_REQUEST['unit_name'];
    $abbriviation = $_REQUEST['abbriviation'];
	$status = $_REQUEST['status'];
	$e_id=$_COOKIE['edit_id'];
	
	try
	{
        // echo"UPDATE units SET `unit_name`=$unit_name, `abbriviation`=$abbriviation, `status`=$status where id=$e_id";
		$stmt = $obj->con1->prepare("UPDATE units SET `unit_name`=?, `abbriviation`=?, `status`=? where id=?");
		$stmt->bind_param("sssi",$unit_name,$abbriviation,$status,$e_id);
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
		header("location:units.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		 header("location:units.php");
	}
}
?>
<div class="row" id="p1">
	<div class="col-xl">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> Units</h5>

			</div>
			<div class="card-body">
				<form method="post" >

					<div class="row g-2">
						<div class="col mb-3">
							<label class="form-label" for="basic-default-fullname">Unit Name</label>
							<input type="text" class="form-control" name="unit_name" id="unit_name" value="<?php echo (isset($mode)) ? $data['unit_name'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
						</div>
                        <div class="col mb-3">
							<label class="form-label" for="basic-default-fullname">Abbriviation</label>
							<input type="text" class="form-control" name="abbriviation" id="abbriviatione"  value="<?php echo (isset($mode)) ? $data['abbriviation'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
						</div>
					</div>

					<div class="mb-3">
						<label class="form-label d-block" for="basic-default-fullname">Status</label>
						<div class="form-check form-check-inline mt-3">
							<input class="form-check-input" type="radio" name="status" id="Enable" value="Enable" <?php echo isset($mode) && $data['status'] == 'Enable' ? 'checked' : '' ?> <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required checked>
							<label class="form-check-label" for="inlineRadio1">Enable</label>
						</div>
						<div class="form-check form-check-inline mt-3">
							<input class="form-check-input" type="radio" name="status" id="Disable" value="Disable" <?php echo isset($mode) && $data['status'] == 'Disable' ? 'checked' : '' ?> <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
							<label class="form-check-label" for="inlineRadio1">Disable</label>
						</div>
					</div>
					<button type="submit"  name="<?php echo isset($mode) && $mode == 'edit' ? 'btnupdate' : 'btnsubmit' ?>" id="save"
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
		window.location = "units.php";
	}
</script>
<?php
include "footer.php";
?>