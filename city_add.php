<?php
include "header.php";

$stmt_slist = $obj->con1->prepare("select * from state");
$stmt_slist->execute();
$res = $stmt_slist->get_result();
$stmt_slist->close();

if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("select * from city WHERE srno=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("select * from city WHERE srno=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$state_id = $_REQUEST['state'];
	$ctnm = $_REQUEST['ctnm'];
	$status = $_REQUEST['status'];

	try
	{


		// SELECT c1.*, s1.name as 'state_name' FROM `city` c1 , `state` s1 WHERE c1.state_id=s1.id AND c1.status='Enable'
		$stmt = $obj->con1->prepare("INSERT INTO `city`(`ctnm`,`state_id`,`status`) VALUES (?,?,?)");
		$stmt->bind_param("sis",$ctnm,$state_id,$status);
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
		header("location:city.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:city.php");
	}
}

if(isset($_REQUEST['btnupdate']))
{
	$e_id=$_COOKIE['edit_id'];
	$state_id = $_REQUEST['state'];
	$ctnm = $_REQUEST['ctnm'];
	$status = $_REQUEST['status'];
	
	try
	{
		$stmt = $obj->con1->prepare("UPDATE `city` SET ctnm=?, state_id=?, status=? WHERE srno=?");
		$stmt->bind_param("sissi", $ctnm,$state_id,$status,$action,$e_id);
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
		header("location:city.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:city.php");
	}
}
?>
<div class="row" id="p1">
	<div class="col-xl">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> City</h5>

			</div>
			<div class="card-body">
				<form method="post" >

					<div class="row g-2">
						<div class="col mb-3">
							<label class="form-label" for="basic-default-fullname">State</label>
							<select name="state" id="state" class="form-control" <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
								<option value="">Select State</option>
								<?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `state` WHERE `status`= 'Enable'");
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($state=mysqli_fetch_array($result))
                                        {
                                    ?>
									<option value="<?php echo $state["id"]?>"
                                    <?php echo isset($mode) && $data['state_id'] == $state["id"] ? 'selected' : '' ?>>
                                    <?php echo $state["name"]?></option>
									<?php
								}
								?>
							</select>
							<input type="hidden" name="ttId" id="ttId">
						</div>

						<div class="col mb-3">
							<label class="form-label" for="basic-default-fullname">City Name</label>
							<input type="text" class="form-control" name="ctnm" id="ctnm" value="<?php echo (isset($mode)) ? $data['ctnm'] : '' ?>"
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
		window.location = "city.php";
	}
</script>
<?php
include "footer.php";
?>