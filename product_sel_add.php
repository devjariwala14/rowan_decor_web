<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("select * from product_selection where id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("select * from product_selection where id=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$inq_id = $_REQUEST['inq_id'];
    $date = $_REQUEST['date_time'];
	$status = $_REQUEST['status'];
	try
	{
		//echo "INSERT INTO `city`(`city_name`,`state_id`,`status`) VALUES ('".$city_name."', '".$state_name."', '".$status."')";


		//echo "INSERT into company(`id`, `company_name`, `address`, `crossing`, `country_name`, `state_name`, `city_name`, `postal_code`, `phone`, `email`, `website`, `tax_num1`, `tax_num2`) VALUES ('".$company_name."','".$address."','".$crossing."','".$country_name."','".$state."','".$city."','".$postal_code."','".$phone."','".$email."','".$website."','".$tax1."','".$tax2."','".$company_name."')";

		$stmt = $obj->con1->prepare("INSERT INTO `product_selection`(`inq_id`,`date_time`,`status`) VALUES (?,?,?)");
		$stmt->bind_param("sss",$inq_id,$date,$status);
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
		header("location:product_sel.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:product_sel.php");
	}
}

if(isset($_REQUEST['btnupdate']))
{
	$inq_id = $_REQUEST['inq_id'];
    $date = $_REQUEST['date_time'];
	$status = $_REQUEST['status'];
	$e_id=$_COOKIE['edit_id'];
	
	try
	{
         //echo"UPDATE units SET `unit_name`=$unit_name, `abbriviation`=$abbriviation, `status`=$status where id=$e_id";
		$stmt = $obj->con1->prepare("UPDATE product_selection SET `inq_id`=?, `date_time`=?, `status`=? where id=?");
		$stmt->bind_param("sssi",$inq_id,$date,$status,$e_id);
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
		header("location:product_sel.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		 header("location:product_sel.php");
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
                        <label class="form-label" for="basic-default-fullname">Inquiry id</label>
                        <input type="text" class="form-control" name="inq_id" id="inq_id"
                            value="<?php echo (isset($mode)) ? $data['inq_id'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                <div class="row">
					<div class="col mb-3">
                        <label for="date_time" class="col-md-2 col-form-label">Datetime</label>
                          <input class="form-control" type="datetime-local" id="date_time" name="date_time" value="<?php echo (isset($mode)) ? $data['date_time'] : '' ?>"  <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required/>
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
    window.location = "product_sel.php";
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