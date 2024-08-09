<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("SELECT * FROM `product` WHERE id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("SELECT * FROM `product` WHERE id=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$name = $_REQUEST['name'];
    $category = $_REQUEST['category'];
    $company = $_REQUEST['company'];
    $unit = $_REQUEST['unit'];
    $price = $_REQUEST['price'];
	$status = $_REQUEST['status'];

	try
	{
		$stmt = $obj->con1->prepare("INSERT INTO `product`(`name`,`category_id`,`company_id`,`unit_id`,`price`,`status`) VALUES (?,?,?,?,?,?)");
		$stmt->bind_param("siiiss",$name,$category,$company,$unit,$price,$status);
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
		header("location:product.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:product.php");
	}
}

if(isset($_REQUEST['btnupdate']))
{
	$e_id=$_COOKIE['edit_id'];
    $name = $_REQUEST['name'];
    $category = $_REQUEST['category'];
    $company = $_REQUEST['company'];
    $unit = $_REQUEST['unit'];
    $price = $_REQUEST['price'];
	$status = $_REQUEST['status'];
	
	try
	{
        // echo"UPDATE units SET `unit_name`=$unit_name, `abbriviation`=$abbriviation, `status`=$status where id=$e_id";
		$stmt = $obj->con1->prepare("UPDATE `product` SET `name`=?,`category_id`=?,`company_id`=?,`unit_id`=?,`price`=?,`status`=? WHERE id=?");
		$stmt->bind_param("siiissi",$name,$category,$company,$unit,$price,$status,$e_id);
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
		header("location:product.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		 header("location:product.php");
	}
}
?>
<div class="row" id="p1">
    <div class="col-xl">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> Product
                </h5>

            </div>
            <div class="card-body">
                <form method="post">
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Product Name</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="<?php echo (isset($mode)) ? $data['name'] : '' ?>"
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Category</label>
                        <select name="category" id="category" class="form-control"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                            <option value="">Select Category</option>
                            <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `category` WHERE `status`= 'Enable'");
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($product=mysqli_fetch_array($result))
                                        {
                                    ?>
                            <option value="<?php echo $product["id"]?>"
                                <?php echo isset($mode) && $data['category_id'] == $product["id"] ? 'selected' : '' ?>>
                                <?php echo $product["name"]?></option>
                            <?php
								}
								?>
                        </select>
                        <input type="hidden" name="ttId" id="ttId">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Company</label>
                        <select name="company" id="company" class="form-control"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                            <option value="">Select Company</option>
                            <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `company` WHERE `status`= 'Enable'");
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($product=mysqli_fetch_array($result))
                                        {
                                    ?>
                            <option value="<?php echo $product["id"]?>"
                                <?php echo isset($mode) && $data['company_id'] == $product["id"] ? 'selected' : '' ?>>
                                <?php echo $product["company_name"]?></option>
                            <?php
								}
								?>
                        </select>
                        <input type="hidden" name="ttId" id="ttId">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Units</label>
                        <select name="unit" id="unit" class="form-control"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                            <option value="">Select Unit</option>
                            <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `units` WHERE `status`= 'Enable'");
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($product=mysqli_fetch_array($result))
                                        {
                                    ?>
                            <option value="<?php echo $product["id"]?>"
                                <?php echo isset($mode) && $data['unit_id'] == $product["id"] ? 'selected' : '' ?>>
                                <?php echo $product["unit_name"]?></option>
                            <?php
								}
								?>
                        </select>
                        <input type="hidden" name="ttId" id="ttId">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Price</label>
                        <input type="text" class="form-control" name="price" id="price" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                            value="<?php echo (isset($mode)) ? $data['price'] : '' ?>"
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
            <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'btnupdate' : 'btnsubmit' ?>"
                id="save" class="btn btn-primary <?php echo isset($mode) && $mode == 'view' ? 'd-none' : '' ?>">
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

</>
<script>
function go_back() {
    eraseCookie("edit_id");
    eraseCookie("view_id");
    window.location = "product.php";
}
</script>
<?php
include "footer.php";
?>