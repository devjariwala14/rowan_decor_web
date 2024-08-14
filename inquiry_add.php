<?php
include "header.php";

if (isset($_COOKIE['edit_id'])) {
    $mode = 'edit';
    $editId = $_COOKIE['edit_id'];
    $stmt = $obj->con1->prepare("SELECT i.*, pi.image FROM `inquiry` i LEFT JOIN `property_image` pi ON i.id = pi.inq_id WHERE i.id=?");
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (isset($_COOKIE['view_id'])) {
    $mode = 'view';
    $viewId = $_COOKIE['view_id'];
    $stmt = $obj->con1->prepare("SELECT i.*, pi.image FROM `inquiry` i LEFT JOIN `property_image` pi ON i.id = pi.inq_id WHERE i.id=?");
    $stmt->bind_param('i', $viewId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// insert data
if (isset($_REQUEST['btnsubmit'])) {
    $visitor = $_REQUEST["visitor"];
    $inquired_for = $_REQUEST["inq_for"];
    $inquired_for_str = implode(',', $inquired_for);
    $attended_by = $_REQUEST["attended_by"];
    $architect = $_REQUEST["architect"];
    $address = $_REQUEST["address"];
    $suggetion = $_REQUEST["suggestions"];
    $start_date = date('Y-m-d', strtotime($_REQUEST['start_date']));
    $status = $_REQUEST["status"];

    $inquiry_img = $_FILES['inquiry_img']['name'];
    $inquiry_img = str_replace(' ', '_', $inquiry_img);
    $inquiry_img_path = $_FILES['inquiry_img']['tmp_name'];

    try {
        $stmt = $obj->con1->prepare("INSERT INTO `inquiry`(`visitor_id`, `inquired_for`, `attended_by`, `architect_id`, `address`, `suggestions`, `start_date`, `status`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isiissss", $visitor, $inquired_for_str, $attended_by, $architect, $address, $suggetion, $start_date, $status);
        $Resp = $stmt->execute();
        
        if (!$Resp) {
            throw new Exception("Problem in adding inquiry! " . strtok($obj->con1->error, '('));
        }

        $inquiry_id = $stmt->insert_id; // Get the ID of the newly inserted inquiry
        $stmt->close();

        // Insert image
        if ($inquiry_img != "") {
            $PicFileName = handleImageUpload($inquiry_img, $inquiry_img_path);

            $stmt = $obj->con1->prepare("INSERT INTO `property_image`(`inq_id`, `image`) VALUES (?,?)");
            $stmt->bind_param("is", $inquiry_id, $PicFileName);
            $stmt->execute();
            $stmt->close();
        }

        setcookie("msg", "data", time() + 3600, "/");
        header("location:inquiry.php");
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }
}

// update data
if (isset($_REQUEST['btnupdate'])) {
    $e_id = $_COOKIE['edit_id'];
    $visitor = $_REQUEST["visitor"];
    $inquired_for = $_REQUEST["inq_for"];
    $inquired_for_str = implode(',', $inquired_for);
    $attended_by = $_REQUEST["attended_by"];
    $architect = $_REQUEST["architect"];
    $address = $_REQUEST["address"];
    $suggetion = $_REQUEST["suggestions"];
    $start_date = date('Y-m-d', strtotime($_REQUEST['start_date']));
    $status = $_REQUEST["status"];

    $inquiry_img = $_FILES['inquiry_img']['name'];
    $inquiry_img = str_replace(' ', '_', $inquiry_img);
    $inquiry_img_path = $_FILES['inquiry_img']['tmp_name'];
    $old_img = $_REQUEST['old_img_inquiry'];

    try {
        $stmt = $obj->con1->prepare("UPDATE `inquiry` SET `visitor_id`=?, `inquired_for`=?, `attended_by`=?, `architect_id`=?, `address`=?, `suggestions`=?, `start_date`=?, `status`=? WHERE id=?");
        $stmt->bind_param("isiissssi", $visitor, $inquired_for_str, $attended_by, $architect, $address, $suggetion, $start_date, $status, $e_id);
        $Resp = $stmt->execute();

        if (!$Resp) {
            throw new Exception("Problem in updating inquiry! " . strtok($obj->con1->error, '('));
        }
        $stmt->close();

        if ($inquiry_img != "") {
            $PicFileName = handleImageUpload($inquiry_img, $inquiry_img_path, $old_img);

            // Check if image already exists for the inquiry
            $stmt = $obj->con1->prepare("SELECT COUNT(*) as cnt FROM `property_image` WHERE `inq_id` = ?");
            $stmt->bind_param("i", $e_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($result['cnt'] > 0) {
                // Update existing image
                $stmt = $obj->con1->prepare("UPDATE `property_image` SET `image`=? WHERE `inq_id`=?");
                $stmt->bind_param("si", $PicFileName, $e_id);
                $stmt->execute();
                $stmt->close();
            } else {
                // Insert new image
                $stmt = $obj->con1->prepare("INSERT INTO `property_image`(`inq_id`, `image`) VALUES (?,?)");
                $stmt->bind_param("is", $e_id, $PicFileName);
                $stmt->execute();
                $stmt->close();
            }
        }

        setcookie("edit_id", "", time() - 3600, "/");
        setcookie("msg", "update", time() + 3600, "/");
        header("location:inquiry.php");
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }
}

function handleImageUpload($image_name, $image_tmp, $old_image = null)
{
    if ($image_name != "") {
        $PicFileName = $image_name;
        $i = 0;
        $Arr1 = explode('.', $PicFileName);
        while (file_exists("property_image/" . $PicFileName)) {
            $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
            $i++;
        }
        if (!move_uploaded_file($image_tmp, "property_image/" . $PicFileName)) {
            throw new Exception("Error in uploading image.");
        }
        if ($old_image && file_exists("property_image/" . $old_image)) {
            unlink("property_image/" . $old_image); // Delete old image
        }
        return $PicFileName;
    }
    return $old_image;
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
                <form method="post" enctype="multipart/form-data">
                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Visitor</label>
                        <select name="visitor" id="visitor" class="form-control"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                            <option value="">Select Visitor</option>
                            <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `visitor` WHERE `status`= 'enable'");
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
                        <label for="inq_for" class="form-label">Inquired For</label>
                        <select required class="form-select js-example-basic-multiple"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> name="inq_for[]"
                            id="inq_for" multiple>
                            <option value="">Select Category</option> <!-- Optional placeholder -->
                            <?php
                                $stmt_list = $obj->con1->prepare("SELECT * FROM `category` WHERE `status`= 'enable'");
                                $stmt_list->execute();
                                $result = $stmt_list->get_result();
                                $stmt_list->close();
                                $selected_categories = explode(',', $data['inquired_for']); // Handle multiple selections
                                while($res = mysqli_fetch_array($result))
                                {
                            ?>
                            <option value="<?php echo $res["id"]?>"
                                <?php echo isset($mode) && in_array($res["id"], $selected_categories) ? 'selected' : '' ?>>
                                <?php echo $res["name"]?></option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Attended By</label>
                        <?php
                            // Accessing the current user's ID and name from the session
                            $current_user_id = $_SESSION['id']; 
                            $current_user_name = $_SESSION['name'];
                            ?>
                        <input type="hidden" name="attended_by" id="attended_by"
                            value="<?php echo $current_user_id; ?>">
                        <input type="text" class="form-control" value="<?php echo $current_user_name; ?>" readonly>
                    </div>


                    <div class="col mb-3">
                        <label class="form-label" for="basic-default-fullname">Architect</label>
                        <select name="architect" id="architect" class="form-control"
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required>
                            <option value="">Select Architect</option>
                            <?php
                                        $stmt_list = $obj->con1->prepare("SELECT * FROM `architect` WHERE `status`= 'enable'");
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
                    <div class="row g-2">
                        <div class="col mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>
                                required><?php echo (isset($mode)) ? $data['address'] : '' ?></textarea>
                        </div>
                        <div class="col mb-3">
                            <label for="suggestions" class="form-label">Suggestions</label>
                            <textarea class="form-control" id="suggestions" name="suggestions" rows="2"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>
                                required><?php echo (isset($mode)) ? $data['suggestions'] : '' ?></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="inquiry_img" class="form-label">Inquiry Image</label>

                        <?php if (!isset($mode) || $mode !== 'view'): // Show the input field only in Add and Edit modes ?>
                        <input class="form-control" type="file" id="inquiry_img" name="inquiry_img" onchange="readURL(this, 'PreviewInquiryImage')" />
                        <?php endif; ?>

                        <!-- Display image when in edit or view mode -->
                        <img src="<?php echo isset($data['image']) ? 'property_image/' . $data['image'] : ''; ?>"
                            id="PreviewInquiryImage" height="300" width="400"
                            style="display:<?php echo isset($data['image']) ? 'block' : 'none'; ?>"
                            class="object-cover shadow rounded mt-3 mb-3">

                        <div id="imgdiv-inquiry" style="color:red"></div>

                        <input type="hidden" name="old_img_inquiry" id="old_img_inquiry"
                            value="<?php echo (isset($mode) && $mode == 'edit') ? $data['image'] : ''; ?>" />
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <label for="date" class="col-md-2 col-form-label">Start Date</label>
                            <input class="form-control" type="date" name="start_date" id="start_date"
                                value="<?php echo (isset($mode)) ? date('Y-m-d', strtotime($data['start_date'])) : '' ?>"
                                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
                        </div>


                        <div class="col-6 mb-3 mt-2">
                            <div class="mb-3">
                                <label class="form-label d-block" for="basic-default-fullname">Status</label>
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="status" id="enable"
                                        value="enable"
                                        <?php echo isset($mode) && $data['status'] == 'enable' ? 'checked' : '' ?>
                                        <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> required
                                        checked>
                                    <label class="form-check-label" for="inlineRadio1">Enable</label>
                                </div>
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="status" id="disable"
                                        value="disable"
                                        <?php echo isset($mode) && $data['status'] == 'disable' ? 'checked' : '' ?>
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});

function go_back() {
    eraseCookie("edit_id");
    eraseCookie("view_id");
    window.location = "inquiry.php";
}

function readURL(input, previewId) {
    if (input.files && input.files[0]) {
        var filename = input.files.item(0).name;
        var extn = filename.split(".").pop().toLowerCase(); // Extract the file extension
        var validExtensions = ["jpg", "jpeg", "png"]; // Allowed extensions

        if (validExtensions.includes(extn)) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                document.getElementById(previewId).style.display = "block";
                document.getElementById('imgdiv-inquiry').innerHTML = ""; // Clear error message
                document.getElementById('save').disabled = false; // Enable save button
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            document.getElementById('imgdiv-inquiry').innerHTML = "Please select an image file (JPG, JPEG, PNG).";
            input.value = ""; // Clear the input
            document.getElementById(previewId).style.display = "none"; // Hide the preview
            document.getElementById('save').disabled = true; // Disable save button
        }
    }
}
document.getElementById('start_date').addEventListener('input', function() {
    let inputDate = new Date(this.value);
    if (!isNaN(inputDate)) {
        // Format to yyyy-mm-dd
        let formattedDate = inputDate.toISOString().split('T')[0];
        this.value = formattedDate;
    }
});
</script>
<?php
include "footer.php";
?>