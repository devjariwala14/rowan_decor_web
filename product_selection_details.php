<?php
include "header.php";
// delete data
if (isset($_REQUEST["btndelete"])) {
    $u_id = $_REQUEST['u_id'];
 
    try {
        // Prepare statement to fetch director details based on ID
        $stmt_subimg = $obj->con1->prepare("SELECT * FROM `product_selection_details` WHERE id=?");
        $stmt_subimg->bind_param("i", $u_id);
        $stmt_subimg->execute();
        $Resp_subimg = $stmt_subimg->get_result()->fetch_assoc();
        $stmt_subimg->close();
    
        // Define the paths to the three image folders
        $imagePaths = [
            "rowan_image/" . $Resp_subimg["rowan_img"],
            "catalogue_image/" . $Resp_subimg["catalogue_img"], 
            "customer_image/" . $Resp_subimg["customer_img"]  
        ];
    
        // Delete each image if it exists
        foreach ($imagePaths as $imagePath) {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    
        // Prepare statement to delete the record from the database
        $stmt_del = $obj->con1->prepare("DELETE FROM `product_selection_details` WHERE id=?");
        $stmt_del->bind_param("i", $u_id);
        $Resp = $stmt_del->execute();
        if (!$Resp) {
            throw new Exception("Problem in deleting! " . strtok($obj->con1->error, '('));
        }
        $stmt_del->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }
    
    if ($Resp) {
        setcookie("msg", "data_del", time() + 3600, "/");
    }
    header("location:product_selection_details.php");
}  
?>

<h4 class="fw-bold py-3 mb-4">Product Selection Details Master</h4>

<?php 
if(isset($_COOKIE["msg"]) )
{

  if($_COOKIE['msg']=="data")
  {

  ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <i class='bx bxs-check-circle'></i> Data added succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
<script type="text/javascript">
eraseCookie("msg")
</script>
<?php
  }
  if($_COOKIE['msg']=="update")
  {

  ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <i class='bx bxs-check-circle'></i> Data updated succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
<script type="text/javascript">
eraseCookie("msg")
</script>
<?php
  }
  if($_COOKIE['msg']=="data_del")
  {

  ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <i class='bx bxs-cross-circle'></i> Data deleted succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
<script type="text/javascript">
eraseCookie("msg")
</script>
<?php
  }
  if($_COOKIE['msg']=="fail")
  {
  ?>

<div class="alert alert-danger alert-dismissible" role="alert">
    <i class='bx bx-x-circle'></i> An error occured! Try again.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
<script type="text/javascript">
eraseCookie("msg")
</script>
<?php
  }
}
  if(isset($_COOKIE["sql_error"]))
  {
    ?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <?php echo urldecode($_COOKIE['sql_error'])?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>

<script type="text/javascript">
eraseCookie("sql_error")
</script>
<?php
  }
  if(isset($_COOKIE["excelmsg"]))
  {
  ?>
<div class="alert alert-primary alert-dismissible" role="alert">
    <?php echo $_COOKIE['excelmsg']?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>

<script type="text/javascript">
eraseCookie("excelmsg")
</script>

<?php
  }
?>

<!-- Delete Modal -->
<div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="backDropModalTitle">Delete Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBackdrop" class="form-label" id="label_del"></label>
                        <input type="hidden" name="u_id" id="u_id">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="btndelete" class="btn btn-primary">Delete</button>
            </div>
        </form>
    </div>
</div>



<!-- grid -->

<!-- Basic Bootstrap Table -->
<div class="card mb-4">
    <div class="row ms-2 me-3">
        <div class="col-md-6" style="margin:1%">
            <a class="btn btn-primary" href="#" onclick="javascript:adddata()" style="margin-right:15px;">
                <i class="bx bx-plus"></i> Add Product Selection Details</a>

        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover" id="table_id">

                <thead>
                    <tr>
                        <th>Sr.no</th>
                        <th>Product Selection</th>
                        <th>Product</th>
                        <th>Customer Product</th>
                        <th>Total Ammount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php 
                        $stmt_list = $obj->con1->prepare("SELECT psd1.*, p1.name, r1.room_name, o1.object_name FROM `product_selection_details` psd1 JOIN `product_selection` ps1 ON psd1.selection_id = ps1.id JOIN `product` p1 ON psd1.base_product_id = p1.id JOIN `rooms` r1 ON psd1.room_name = r1.id JOIN `objects` o1 ON psd1.object = o1.id LIMIT 0, 25;");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        $stmt_list->close();
                        $i=1;
                        while($res=mysqli_fetch_array($result))
                        {
                          ?>

                    <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $res["selection_id"]?></td>
                        <td><?php echo $res["name"]?></td>
                        <td><?php echo $res["customer_product_name"]?></td>
                        <td><?php echo $res["total_amount"]?></td>
                        <?php if($res["status"]=='enable'){	?>
                        <td style="color:green"><?php echo "Enable"?></td>
                        <?php } else if($res["status"]=='disable'){	?>
                        <td style="color:red"><?php echo "Disable"?></td>
                        <?php } ?>
                        <td>
                            <a href="javascript:editdata('<?php echo $res["id"]?>');"><i
                                    class="bx bx-edit-alt me-1"></i> </a>
                                    <a
                                href="javascript:deletedata('<?php echo $res["id"] ?>','<?php echo base64_encode($res["selection_id"]) ?>');"><i
                                    class="bx bx-trash me-1" style="color:red"></i> </a>
                            <a href="javascript:viewdata('<?php echo $res["id"]?>');"><i class="fa-regular fa-eye"style="color:green"></i></a>
                        </td>
                    </tr>
                    <?php
                          $i++;
                        }
                      ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Basic Bootstrap Table -->


<!-- / grid -->
<!-- / Content -->
<script type="text/javascript">
function adddata() {
    eraseCookie("edit_id");
    eraseCookie("view_id");
    window.location = "product_selection_details_add.php";
}

function editdata(id) {
    eraseCookie("view_id");
    createCookie("edit_id", id, 1);
    window.location = "product_selection_details_add.php";
}

function viewdata(id) {
    eraseCookie("edit_id");
    createCookie("view_id", id, 1);
    window.location = "product_selection_details_add.php";
}

function deletedata(id, name) {
    $('#backDropModal').modal('toggle');
    $('#u_id').val(id);
    $('#label_del').html('Are you sure you want to DELETE product - ' + atob(name) + ' ?');
}
</script>
<?php 
include "footer.php";
?>
<!-- pushed again -->