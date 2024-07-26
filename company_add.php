<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("select * from company where id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("select * from company where id=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$company_name = $_REQUEST['company_name'];
    $address = $_REQUEST['address'];
    $state = $_REQUEST['state_id'];
	$city = $_REQUEST['city_name'];
    $postal_code = $_REQUEST['postal_code'];
    $phone = $_REQUEST['phone'];
    $email = $_REQUEST['email'];
    $website = $_REQUEST['website'];
	$status = $_REQUEST['status'];
	try
	{
		//echo "INSERT INTO `city`(`city_name`,`state_id`,`status`) VALUES ('".$city_name."', '".$state_name."', '".$status."')";


		//echo "INSERT into company(`id`, `company_name`, `address`, `crossing`, `country_name`, `state_name`, `city_name`, `postal_code`, `phone`, `email`, `website`, `tax_num1`, `tax_num2`) VALUES ('".$company_name."','".$address."','".$crossing."','".$country_name."','".$state."','".$city."','".$postal_code."','".$phone."','".$email."','".$website."','".$tax1."','".$tax2."','".$company_name."')";

		$stmt = $obj->con1->prepare("INSERT INTO `company`(`company_name`,`address`,`state_id`,`city_id`,`postal_code`,`phone`,`email`,`website`,`status`) VALUES (?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param("ssiisssss",$company_name,$address,$state,$city,$postal_code,$phone,$email,$website,$status);
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
		header("location:company.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:company.php");
	}
}

if(isset($_REQUEST['btnupdate']))
{
	$company_name = $_REQUEST['company_name'];
    $address = $_REQUEST['address'];
    $state = $_REQUEST['state_id'];
	$city = $_REQUEST['city_name'];
    $postal_code = $_REQUEST['postal_code'];
    $phone = $_REQUEST['phone'];
    $email = $_REQUEST['email'];
    $website = $_REQUEST['website'];
	$status = $_REQUEST['status'];
	$e_id=$_COOKIE['edit_id'];
	
	try
	{
         //echo"UPDATE units SET `unit_name`=$unit_name, `abbriviation`=$abbriviation, `status`=$status where id=$e_id";
		$stmt = $obj->con1->prepare("UPDATE company SET `company_name`=?, `address`=?, `state_id`=?, `city_id`=?, `postal_code`=?, `phone`=?, `email`=?, `website`=?,`status`=? where id=?");
		$stmt->bind_param("ssiisssssi",$company_name,$address,$state,$city,$postel_code,$phone,$email,$website,$status,$e_id);
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
		header("location:company.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		 header("location:company.php");
	}
}
?>
<div class="row" id="p1">
    <div class="col-xl">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> Company
                </h5>

            </div>
            <div class="card-body">
                <form method="post">


                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Company Name</label>
                        <input type="text" class="form-control" name="company_name" id="company_name"
                            value="<?php echo (isset($mode)) ? $data['company_name'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
					<div class="col mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required><?php echo (isset($mode)) ? $data['address'] : '' ?></textarea>
                    </div>

                    <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">State</label>
                            <select onchange="fillcity(this.value)" name="state_id" id="state_id" class="form-control"
                                <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
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
                            <label class="form-label" for="basic-default-fullname">City</label>
                            <select name="city_name" id="city_name" class="form-control"
                                <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>

                                <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `city` WHERE `status`= 'Enable' and `state_id` = ?");
										$stmt_list->bind_param("i", $data["state_id"]);
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($city=mysqli_fetch_array($result))
                                        {
                                    ?>
                                <option value="<?php echo $city["srno"]?>"
                                    <?php echo isset($mode) && $data['city_id'] == $city["srno"] ? 'selected' : '' ?>>
                                    <?php echo $city["ctnm"]?></option>
                                <?php
								}
								?>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Postal Code</label>
                            <input type="text" class="form-control" name="postal_code" id="postal_code"
                                value="<?php echo (isset($mode)) ? $data['postal_code'] : '' ?>"  onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="6"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Contact</label>
                            <input type="text" class="form-control" name="phone" id="phone"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10"
                                value="<?php echo (isset($mode)) ? $data['phone'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Email</label>
                            <input type="text" class="form-control" name="email" id="email"
                                value="<?php echo (isset($mode)) ? $data['email'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Website</label>
                        <input type="text" class="form-control" name="website" id="website"
                            value="<?php echo (isset($mode)) ? $data['website'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                    <div class="col-6">
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
    window.location = "company.php";
}

function fillcity(stid) {
    const xhttp = new XMLHttpRequest();
    xhttp.open("GET", "getcities.php?sid=" + stid);
    xhttp.send();
    xhttp.onload = function() {
        document.getElementById("city_name").innerHTML = xhttp.responseText;
    }
}

// function fillState(cntrid){
// 	const xhttp = new XMLHttpRequest();
// 	xhttp.open("GET","getstate.php?cntrid="+cntrid);
// 	xhttp.send();
// 	xhttp.onload= function(){
// 		var data = xhttp.responseText.split("@@@");
// 		document.getElementById("state").innerHTML = xhttp.responseText;
// 		document.getElementById("country_code").value = "+" + data[1];
// 	}
// }
</script>
<?php
include "footer.php";
?>