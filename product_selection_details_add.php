<?php
include "header.php";


if (isset($_COOKIE['edit_id'])) {
	$mode = 'edit';
	$editId = $_COOKIE['edit_id'];
	$stmt = $obj->con1->prepare("SELECT * FROM `product_selection_details` WHERE id=?");
	$stmt->bind_param('i', $editId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}

if (isset($_COOKIE['view_id'])) {
	$mode = 'view';
	$viewId = $_COOKIE['view_id'];
	$stmt = $obj->con1->prepare("SELECT * FROM `product_selection_details` WHERE id=?");
	$stmt->bind_param('i', $viewId);
	$stmt->execute();
	$data = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$selection_id = $_REQUEST['selection_id'];
    $base_product_id = $_REQUEST['base_product_id'];
    $customer_product_name = $_REQUEST['customer_product_name'];
    $rd_description=$_REQUEST['rd_description'];
    $sell_amount=$_REQUEST['sell_amount'];
    $unit = $_REQUEST['unit'];
    $total_amount=$_REQUEST['total_amount'];
    $unit_of_measure = $_REQUEST['unit_of_measure'];
    $room_name = $_REQUEST['room_name'];
    $object = $_REQUEST['object'];
    $measurement_details = $_REQUEST['measurement_details'];
    $catalogue_notes = $_REQUEST['catalogue_notes'];
    $status = $_REQUEST['status'];
    $rowan_img = $_FILES['rowan_img']['name'];
	$rowan_img = str_replace(' ', '_', $rowan_img);
	$rowan_img_path = $_FILES['rowan_img']['tmp_name'];
    $catalouge_img = $_FILES['catalouge_img']['name'];
	$catalouge_img = str_replace(' ', '_', $catalouge_img);
	$catalouge_img_path = $_FILES['catalouge_img']['tmp_name'];
    $customer_img = $_FILES['customer_img']['name'];
	$customer_img = str_replace(' ', '_', $customer_img);
	$customer_img_path = $_FILES['customer_img']['tmp_name']; 	
	
    $rowan_image = "rowan_image/";
    $catalouge_image = "catalogue_image/";
    $customer_image = "customer_image/";

    function getUniqueFileName($directory, $fileName) {
        if (file_exists($directory . $fileName)) {
            $i = 0;
            $Arr1 = explode('.', $fileName);
            $baseName = $Arr1[0];
            $extension = $Arr1[1];

            while (file_exists($directory . $baseName . $i . "." . $extension)) {
                $i++;
            }
            return $baseName . $i . "." . $extension;
        } else {
            return $fileName;
        }
    }
    $PicFileName1 = getUniqueFileName($rowan_image, $rowan_img );
    $PicFileName2 = getUniqueFileName($catalouge_image, $catalouge_img);
    $PicFileName3 = getUniqueFileName($customer_image, $customer_img);

	try
	{
        // echo "INSERT INTO `product_selection_details`(`selection_id`=".$selection_id.",`base_product_id`=".$base_product_id.",`customer_product_name`=".$customer_product_name.",`rd_description`=".$rd_description.",`sell_amount`=".$sell_amount.",`unit`=".$unit.",`total_amount`=".$total_amount.",`unit_of_measure`=".$unit_of_measure.",`room_name`=".$room_name.",`object`=".$object.",`measurement_details`=".$measurement_details.",`catalogue_notes`=".$catalogue_notes.",`rowan_image`=".$PicFileName1 .",`catalouge_image`=".$PicFileName2.",`customer_image`=".$PicFileName3.",`status`=".$status.")";

		$stmt = $obj->con1->prepare("INSERT INTO `product_selection_details`(`selection_id`,`base_product_id`,`customer_product_name`,`rd_description`,`sell_amount`,`unit`,`total_amount`,`unit_of_measure`,`room_name`,`object`,`measurement_details`,`catalogue_notes`,`rowan_image`,`catalouge_image`,`customer_image`,`status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param("iisssdssssssssss",$selection_id,$base_product_id,$customer_product_name,$rd_description,$sell_amount,$unit,$total_amount,$unit_of_measure,$room_name,$object,$measurement_details,$catalogue_notes,$PicFileName1 ,$PicFileName2 ,$PicFileName3 ,$status);
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
        move_uploaded_file($rowan_img_path,  $rowan_image . $PicFileName1);
        move_uploaded_file($catalouge_img_path, $catalouge_image . $PicFileName2);
        move_uploaded_file($customer_img_path, $customer_image. $PicFileName3);
		setcookie("msg", "data",time()+3600,"/");
		header("location:product_selection_details.php");
	}
	else
	{
		setcookie("msg", "fail",time()+3600,"/");
		header("location:product_selection_details.php");
	}
}

if (isset($_REQUEST['btnupdate'])) {
    
    $e_id = $_COOKIE['edit_id'];
    $selection_id  = $_REQUEST['selection_id'];
    $base_product_id = $_REQUEST['base_product_id'];
    $customer_product_name = $_REQUEST['customer_product_name'];
    $rd_description = $_REQUEST['rd_description'];
    $sell_amount = $_REQUEST['sell_amount'];
    $unit = $_REQUEST['unit'];
    $total_amount = $_REQUEST['total_amount'];
    $unit_of_measure = $_REQUEST['unit_of_measure'];
    $room_name = $_REQUEST['room_name'];
    $object = $_REQUEST['object'];
    $measurement_details = $_REQUEST['measurement_details'];
    $catalogue_notes = $_REQUEST['catalogue_notes'];
    $status = $_REQUEST['status'];

    // Image directories
    $rowan_image_dir = "rowan_image/";
    $catalouge_image_dir = "catalogue_image/";
    $customer_image_dir = "customer_image/";

    // Retrieve current images from database
    $stmt_get = $obj->con1->prepare("SELECT rowan_image, catalouge_image, customer_image FROM `product_selection_details` WHERE id=?");
    $stmt_get->bind_param("i", $e_id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $current_images = $result->fetch_assoc();
    $stmt_get->close();

    // Rowan Image Handling
    $rowan_img = $_FILES['rowan_img']['name'];
    $rowan_img = str_replace(' ', '_', $rowan_img);
    $rowan_img_tmp = $_FILES['rowan_img']['tmp_name'];
    $rowan_img_final = $current_images['rowan_image'];

    function getUniqueFilename($directory, $fileName) {
        if (file_exists($directory . $fileName)) {
            $i = 0;
            $Arr1 = explode('.', $fileName);
            $baseName = $Arr1[0];
            $extension = $Arr1[1];
    
            while (file_exists($directory . $baseName . $i . "." . $extension)) {
                $i++;
            }
            return $baseName . $i . "." . $extension;
        } else {
            return $fileName;
        }
    }

    if ($rowan_img != "") {
        $rowan_img_final = getUniqueFilename($rowan_image_dir, $rowan_img);
        move_uploaded_file($rowan_img_tmp, $rowan_image_dir . $rowan_img_final);
        
        // Delete old image if it exists
        if (file_exists($rowan_image_dir . $current_images['rowan_image'])) {
            unlink($rowan_image_dir . $current_images['rowan_image']);
        }
    }

    // catalogue Image Handling
    $catalouge_img = $_FILES['catalouge_img']['name'];
    $catalouge_img = str_replace(' ', '_', $catalouge_img);
    $catalouge_img_tmp = $_FILES['catalouge_img']['tmp_name'];
    $catalouge_img_final = $current_images['catalouge_image'];

    if ($catalouge_img != "") {
        $catalouge_img_final = getUniqueFilename($catalouge_image_dir, $catalouge_img);
        move_uploaded_file($catalouge_img_tmp, $catalouge_image_dir . $catalouge_img_final);
        
        // Delete old image if it exists
        if (file_exists($catalouge_image_dir . $current_images['catalouge_image'])) {
            unlink($catalouge_image_dir . $current_images['catalouge_image']);
        }
    }

    // Customer Image Handling
    $customer_img = $_FILES['customer_img']['name'];
    $customer_img = str_replace(' ', '_', $customer_img);
    $customer_img_tmp = $_FILES['customer_img']['tmp_name'];
    $customer_img_final = $current_images['customer_image'];

    if ($customer_img != "") {
        $customer_img_final = getUniqueFilename($customer_image_dir, $customer_img);
        move_uploaded_file($customer_img_tmp, $customer_image_dir . $customer_img_final);
        
        // Delete old image if it exists
        if (file_exists($customer_image_dir . $current_images['customer_image'])) {
            unlink($customer_image_dir . $current_images['customer_image']);
        }
    }

    // Database Update
    try {
        $stmt = $obj->con1->prepare("UPDATE `product_selection_details` SET `selection_id`=?,`base_product_id`=?,`customer_product_name`=?,`rd_description`=?,`sell_amount`=?,`unit`=?,`total_amount`=?,`unit_of_measure`=?,`room_name`=?,`object`=?,`measurement_details`=?,`catalogue_notes`=?,`rowan_image`=?,`catalouge_image`=?,`customer_image`=?,`status`=? WHERE id=?");
        $stmt->bind_param("iisssdssssssssssi", $selection_id, $base_product_id, $customer_product_name, $rd_description, $sell_amount, $unit, $total_amount, $unit_of_measure, $room_name, $object, $measurement_details, $catalogue_notes, $rowan_img_final, $catalouge_img_final, $customer_img_final, $status, $e_id);
        $Resp = $stmt->execute();
        if (!$Resp) {
            throw new Exception("Problem in updating! " . strtok($obj->con1->error, '('));
        }
        $stmt->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    // Redirect based on the result
    if ($Resp) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:product_selection_details.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:product_selection_details.php");
    }
}

// Function to generate a unique filename


?>
<div class="row" id="p1">
    <div class="col-xl">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> Product
                    Selection Details</h5>

            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">

                    <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Product Selection Id</label>
                            <input type="text" class="form-control" name="selection_id" id="selection_id"
                                value="<?php echo (isset($mode)) ? $data['selection_id'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>

                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Base Product</label>
                            <select name="base_product_id" id="base_product_id" class="form-control"
                                <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                                <option value="">Select Product</option>
                                <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `product` WHERE `status`= 'enable'");
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($product=mysqli_fetch_array($result))
                                        {
                                    ?>
                                <option value="<?php echo $product["id"]?>"
                                    <?php echo isset($mode) && $data['base_product_id'] == $product["id"] ? 'selected' : '' ?>>
                                    <?php echo $product["name"]?></option>
                                <?php
								}
								?>
                            </select>
                            <input type="hidden" name="ttId" id="ttId">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Customer Product Name</label>
                            <input type="text" class="form-control" name="customer_product_name"
                                id="customer_product_name"
                                value="<?php echo (isset($mode)) ? $data['customer_product_name'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                    </div>

                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">RD Description</label>
                        <textarea class="form-control" name="rd_description" id="rd_description" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>><?php echo (isset($mode)) ? $data['rd_description'] : '' ?></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Sell Amount</label>
                            <input type="text" class="form-control" name="sell_amount" id="sell_amount"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                value="<?php echo (isset($mode)) ? $data['sell_amount'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Unit</label>

                            <input type="text" class="form-control" name="unit" id="unit"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                value="<?php echo (isset($mode)) ? $data['unit'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Total Amount</label>
                            <input type="text" class="form-control" name="total_amount" id="total_amount"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                value="<?php echo (isset($mode)) ? $data['total_amount'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Unit Of Measure</label>
                            <input type="text" class="form-control" name="unit_of_measure" id="unit_of_measure"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                value="<?php echo (isset($mode)) ? $data['unit_of_measure'] : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Rooms</label>
                            <select name="room_name" id="room_name" class="form-control"
                                <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                                <option value="">Select Room</option>
                                <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `rooms` WHERE `status`= 'enable'");
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($rooms=mysqli_fetch_array($result))
                                        {
                                    ?>
                                <option value="<?php echo $rooms["room_name"]?>"
                                    <?php echo isset($mode) && $data['room_name'] == $rooms["room_name"] ? 'selected' : '' ?>>
                                    <?php echo $rooms["room_name"]?></option>
                                <?php
								}
								?>
                            </select>
                            <input type="hidden" name="ttId" id="ttId">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Objects</label>
                            <select name="object" id="object" class="form-control"
                                <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                                <option value="">Select Object</option>
                                <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `objects` WHERE `status`= 'enable'");
                                        $stmt_list->execute();
                                        $result = $stmt_list->get_result();
                                        $stmt_list->close();
                                        $i=1;
                                        while($object=mysqli_fetch_array($result))
                                        {
                                    ?>
                                <option value="<?php echo $object["object_name"]?>"
                                    <?php echo isset($mode) && $data['object'] == $object["object_name"] ? 'selected' : '' ?>>
                                    <?php echo $object["object_name"]?></option>
                                <?php
								}
								?>
                            </select>
                            <input type="hidden" name="ttId" id="ttId">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Measurement Details</label>
                            <textarea class="form-control" name="measurement_details" id="measurement_details" required
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>><?php echo (isset($mode)) ? $data['measurement_details'] : '' ?></textarea>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Catalouge Notes</label>
                            <textarea class="form-control" name="catalogue_notes" id="catalogue_notes" required
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>><?php echo (isset($mode)) ? $data['catalouge_notes'] : '' ?></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Catalogue Image -->
                        <div class="col-md-4">
                            <label for="catalouge_img" class="form-label">Catalouge Image</label>
                            <?php if (!isset($mode) || $mode !== 'view'): ?>
                            <input class="form-control" type="file" id="catalouge_img" name="catalouge_img"
                                onchange="readURL(this, 'PreviewcatalogueImage')" />
                            <?php endif; ?>
                            <img src="<?php echo isset($data['catalouge_image']) ? 'catalogue_image/' . $data['catalouge_image'] : ''; ?>"
                                id="PreviewcatalogueImage" height="200" width="300"
                                style="display:<?php echo isset($data['catalouge_image']) ? 'block' : 'none'; ?>"
                                class="object-cover shadow rounded mt-3 mb-3">
                            <div id="imgdiv-catalogue" style="color:red"></div>
                            <input type="hidden" name="old_img_catalogue" id="old_img_catalogue"
                                value="<?php echo (isset($mode) && $mode == 'edit') ? $data['catalouge_image'] : ''; ?>" />
                        </div>

                        <!-- Rowan Image -->
                        <div class="col-md-4">
                            <label for="rowan_img" class="form-label">Rowan Image</label>
                            <?php if (!isset($mode) || $mode !== 'view'): ?>
                            <input class="form-control" type="file" id="rowan_img" name="rowan_img"
                                onchange="readURL(this, 'PreviewRowanImage')" />
                            <?php endif; ?>
                            <img src="<?php echo isset($data['rowan_image']) ? 'rowan_image/' . $data['rowan_image'] : ''; ?>"
                                id="PreviewRowanImage" height="200" width="300"
                                style="display:<?php echo isset($data['rowan_image']) ? 'block' : 'none'; ?>"
                                class="object-cover shadow rounded mt-3 mb-3">
                            <div id="imgdiv-rowan" style="color:red"></div>
                            <input type="hidden" name="old_img_rowan" id="old_img_rowan"
                                value="<?php echo (isset($mode) && $mode == 'edit') ? $data['rowan_image'] : ''; ?>" />
                        </div>

                        <!-- Customer Image -->
                        <div class="col-md-4">
                            <label for="customer_img" class="form-label">Customer Image</label>
                            <?php if (!isset($mode) || $mode !== 'view'): ?>
                            <input class="form-control" type="file" id="customer_img" name="customer_img"
                                onchange="readURL(this, 'PreviewCustomerImage')" />
                            <?php endif; ?>
                            <img src="<?php echo isset($data['customer_image']) ? 'customer_image/' . $data['customer_image'] : ''; ?>"
                                id="PreviewCustomerImage" height="200" width="300"
                                style="display:<?php echo isset($data['customer_image']) ? 'block' : 'none'; ?>"
                                class="object-cover shadow rounded mt-3 mb-3">
                            <div id="imgdiv-customer" style="color:red"></div>
                            <input type="hidden" name="old_img_customer" id="old_img_customer"
                                value="<?php echo (isset($mode) && $mode == 'edit') ? $data['customer_image'] : ''; ?>" />
                        </div>
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

</>
<script>
function go_back() {
    eraseCookie("edit_id");
    eraseCookie("view_id");
    window.location = "product_selection_details.php";
}

function readURL(input, previewId) {
    const previewImage = document.getElementById(previewId);
    const imgDiv = document.getElementById(`imgdiv-${previewId.toLowerCase().replace('preview', '')}`);

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
            imgDiv.innerHTML = ''; // Clear any error messages
        };

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php
include "footer.php";
?>