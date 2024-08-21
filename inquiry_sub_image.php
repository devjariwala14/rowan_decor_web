<?php
include "header.php";

$product_id = isset($_COOKIE['edit_id']) ? $_COOKIE['edit_id'] : $_COOKIE['view_id'];

if (isset($_COOKIE['edit_subimg_id']) || isset($_COOKIE['view_subimg_id'])) {
    $mode = isset($_COOKIE['edit_subimg_id']) ? 'edit' : 'view';
    $id = isset($_COOKIE['edit_subimg_id']) ? $_COOKIE['edit_subimg_id'] : $_COOKIE['view_subimg_id'];
    
    $stmt = $obj->con1->prepare("SELECT * FROM `property_image` WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (isset($_REQUEST["save"])) {
    $file_names = $_FILES['file_name']['name'];

    try {
        foreach ($_FILES["file_name"]['name'] as $key => $file_name_one) {
            if ($file_name_one != "") {
                $file_name_one = str_replace(' ', '_', $file_name_one);
                $file_path_one = $_FILES['file_name']['tmp_name'][$key];
                
                if (file_exists("property_image/" . $file_name_one)) {
                    $i = 0;
                    $Arr = explode('.', $file_name_one);
                    $SubImageName = $Arr[0] . $i . "." . $Arr[1];
                    while (file_exists("property_image/" . $SubImageName)) {
                        $i++;
                        $SubImageName = $Arr[0] . $i . "." . $Arr[1];
                    }
                } else {
                    $SubImageName = $file_name_one;
                }
                
                move_uploaded_file($file_path_one, "property_image/" . $SubImageName);

                // Insert into `property_image`
                $stmt_image = $obj->con1->prepare("INSERT INTO `property_image`(`inq_id`, `image`) VALUES (?, ?)");
                $stmt_image->bind_param("is", $product_id, $SubImageName);
                $Resp = $stmt_image->execute();
                $stmt_image->close();

                if (!$Resp) {
                    throw new Exception("Problem in adding! " . strtok($obj->con1->error, "("));
                }
            }
        }
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "data", time() + 3600, "/");
        header("location:inquiry_add.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:inquiry_add.php");
    }
}

if (isset($_REQUEST["update"])) {
    $e_id = $_COOKIE['edit_subimg_id'];
    $file_name_one = $_FILES['file_name']['name'][0]; // For single image in update
    $file_name_one = str_replace(' ', '_', $file_name_one);
    $file_path_one = $_FILES['file_name']['tmp_name'][0]; // For single image in update
    $old_img = $_REQUEST['old_img'];
    
    if ($file_name_one != "") {
        if (file_exists("property_image/" . $file_name_one)) {
            $i = 0;
            $Arr1 = explode('.', $file_name_one);
            $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
            while (file_exists("property_image/" . $PicFileName)) {
                $i++;
                $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
            }
        } else {
            $PicFileName = $file_name_one;
        }
        unlink("property_image/" . $old_img);
        move_uploaded_file($file_path_one, "property_image/" . $PicFileName);
    } else {
        $PicFileName = $old_img;
    }

    try {
        $stmt = $obj->con1->prepare("UPDATE `property_image` SET `image`=? WHERE `id`=?");
        $stmt->bind_param("si", $PicFileName, $e_id);
        $Resp = $stmt->execute();
        $stmt->close();
        
        if (!$Resp) {
            throw new Exception("Problem in updating! " . strtok($obj->con1->error, "("));
        }
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("edit_subimg_id", "", time() - 3600, "/");
        setcookie("msg", "update", time() + 3600, "/");
        header("location:inquiry_add.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:inquiry_add.php");
    }
}
?>

<div class="row" id="p1">
    <div class="col-xl">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"> <?php echo (isset($mode)) ? (($mode == 'view') ? 'View' : 'Edit') : 'Add' ?> Inquiry
                    Images</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="col-md-12">
                        <label for="file_name" class="form-label">Inquiry Images</label>
                        <?php if (!isset($mode) || $mode !== 'view'): ?>
                        <input class="form-control" type="file" id="file_name" name="file_name[]" multiple
                            onchange="readURL_multiple(this)" />
                        <?php endif; ?>
                        <div id="preview_image_div" class="row mt-3">
                            <?php 
                if (isset($mode) && ($mode == 'edit' || $mode == 'view')): 
                    // Fetch and display the existing images
                    $images = explode(",", $data['image']); // Assuming image paths are comma-separated in the database
                    foreach ($images as $image): 
                        $extn = pathinfo($image, PATHINFO_EXTENSION);
                        if (in_array(strtolower($extn), ['jpg', 'jpeg', 'png', 'bmp', 'svg'])): ?>
                            <div class="col-md-4">
                                <img src="property_image/<?php echo $image; ?>" style="width: 400px; height: 300px;"
                                    class="img-thumbnail shadow rounded mb-3">
                            </div>
                            <?php elseif (in_array(strtolower($extn), ['mp4', 'webm', 'ogg'])): ?>
                            <div class="col-md-4">
                                <video src="property_image/<?php echo $image; ?>" style="width: 400px; height: 300px;"
                                    class="img-thumbnail shadow rounded mb-3" controls></video>
                            </div>
                            <?php endif;
                    endforeach; 
                endif; 
                ?>
                        </div>
                        <div id="imgdiv_multiple" style="color:red"></div>
                        <input type="hidden" name="old_img" id="old_img"
                            value="<?php echo (isset($mode) && $mode == 'edit') ? $data['image'] : ''; ?>" />
                    </div>

                    <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save' ?>"
                        id="save" class="btn btn-primary <?php echo isset($mode) && $mode == 'view' ? 'd-none' : '' ?>">
                        <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="javascript:go_back()">Close</button>
                </form>
            </div>
            <script type="text/javascript">
            function go_back() {
                eraseCookie("edit_id");
                eraseCookie("view_id");
                window.location = "inquiry.php";
            }

            function readURL_multiple(input) {
                $('#preview_image_div').html("");
                var filesAmount = input.files.length;
                for (var i = 0; i < filesAmount; i++) {
                    if (input.files && input.files[i]) {
                        var filename = input.files.item(i).name;
                        var reader = new FileReader();
                        var extn = filename.split(".");
                        if (['jpg', 'jpeg', 'png', 'bmp', 'svg', 'mp4', 'webm', 'ogg'].includes(extn[1]
                        .toLowerCase())) {
                            if (['jpg', 'jpeg', 'png', 'bmp', 'svg'].includes(extn[1].toLowerCase())) {
                                reader.onload = function(e) {
                                    $('#preview_image_div').append('<div class="col-md-4"><img src="' + e.target
                                        .result +
                                        '" style="width: 400px; height: 300px;" class="img-thumbnail shadow rounded mb-3"></div>'
                                        );
                                };
                            } else if (['mp4', 'webm', 'ogg'].includes(extn[1].toLowerCase())) {
                                reader.onload = function(e) {
                                    $('#preview_image_div').append('<div class="col-md-4"><video src="' + e.target
                                        .result +
                                        '" style="width: 400px; height: 300px;" class="img-thumbnail shadow rounded mb-3" controls></video></div>'
                                        );
                                };
                            }

                            reader.readAsDataURL(input.files[i]);
                            $('#imgdiv_multiple').html("");
                            document.getElementById('save').disabled = false;
                        } else {
                            $('#imgdiv_multiple').html("Please Select Image Or Video Only");
                            document.getElementById('save').disabled = true;
                        }
                    }
                }
            }
            </script>

            <?php include "footer.php"; ?>