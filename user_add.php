<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("select * from `users` where id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("select * from `users` where id=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $type = $_REQUEST['type'];
    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $contact = $_REQUEST['contact'];
    $status = $_REQUEST['status'];
	
	try
	{
		$stmt = $obj->con1->prepare("INSERT INTO `users`(`username`,`password`,`type`,`name`,`email`,`contact`,`status`) VALUES (?,?,?,?,?,?,?)");
		$stmt->bind_param("sssssss",$username,$password,$type,$name , $email, $contact, $status );
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
		header("location:user.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:user.php");
	}
}

if(isset($_REQUEST['btnupdate']))
{
    $e_id=$_COOKIE['edit_id'];
	$username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $type = $_REQUEST['type'];
    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $contact = $_REQUEST['contact'];
    $status = $_REQUEST['status'];
	
	
	try
	{
        // echo"UPDATE visitor SET `unit_name`=$unit_name, `abbriviation`=$abbriviation, `status`=$status where id=$e_id";
		$stmt = $obj->con1->prepare("UPDATE users SET `username`=?,`password`=?,`type`=?,`name`=?,`email`=?,`contact`=?,`status`=? where id=?");
		$stmt->bind_param("sssssssi",$username,$password,$type,$name , $email, $contact, $status ,$e_id);
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
		header("location:user.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		 header("location:user.php");
	}
}
?>
<div class="row" id="p1">
    <div class="col-xl">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> User
                </h5>

            </div>
            <div class="card-body">
                <form method="post">
                   
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Username</label>
                            <input type="text" class="form-control" name="username" id="username" 
                                value="<?php echo (isset($mode)) ? $data['username'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Password</label>
                            <input type="password" class="form-control" name="password" id="password"
                                value="<?php echo (isset($mode)) ? $data['password'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Type</label>
                        <select name="type" id="type" class="form-control" <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                        <option value="">Choose Type</option>
                                <option value="user"
                                    <?php echo isset($mode) && $data['type'] == "user" ? "selected" : "" ?>>User
                                </option>
                                <option value="sales person"
                                    <?php echo isset($mode) && $data['type'] == "sales person" ? "selected" : "" ?>>Sales Person
                                </option>
                                <option value="labourers"
                                    <?php echo isset($mode) && $data['type'] == "labourers" ? "selected" : "" ?>>Labourers
                                </option>
                                
                        </select>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Name</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="<?php echo (isset($mode)) ? $data['name'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Email</label>
                        <input type="text" class="form-control" name="email" id="email"
                            value="<?php echo (isset($mode)) ? $data['email'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Contact</label>
                        <input type="text" class="form-control" name="contact" id="contact" maxlength="10" pattern="[6789][0-9]{9}"
                            value="<?php echo (isset($mode)) ? $data['contact'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                    <div class="mb-3">
						<label class="form-label d-block" for="basic-default-fullname">Status</label>
						<div class="form-check form-check-inline mt-3">
							<input class="form-check-input" type="radio" name="status" id="enable" value="Enable" <?php echo isset($mode) && $data['status'] == 'Enable' ? 'checked' : '' ?> <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required checked>
							<label class="form-check-label" for="inlineRadio1">Enable</label>
						</div>
						<div class="form-check form-check-inline mt-3">
							<input class="form-check-input" type="radio" name="status" id="Disable" value="disable" <?php echo isset($mode) && $data['status'] == 'Disable' ? 'checked' : '' ?> <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
							<label class="form-check-label" for="inlineRadio1">Disable</label>
						</div>
					</div>
                    <button type="submit"
                        name="<?php echo isset($mode) && $mode == 'edit' ? 'btnupdate' : 'btnsubmit' ?>" id="save"
                        class="btn btn-success <?php echo isset($mode) && $mode == 'view' ? 'd-none' : '' ?>">
                        <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                    </button>
                    <button type="button" class="btn btn-danger"
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
    window.location = "user.php";
}
</script>
<?php
include "footer.php";
?>