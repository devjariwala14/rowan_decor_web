<?php
include "header.php";

// Delete data
if (isset($_POST["btndelete"])) {
    $u_id=$_REQUEST['u_id'];
    try {
        $stmt_subimg = $obj->con1->prepare("SELECT * FROM `inquiry` WHERE id=?");
        $stmt_subimg->bind_param("i",$u_id);
        $stmt_subimg->execute();
        $Resp_subimg = $stmt_subimg->get_result()->fetch_assoc();
        $stmt_subimg->close();
        
        if (file_exists("property_image/" . $Resp_subimg["inquiry_image"])) {
            unlink("property_image/" . $Resp_subimg["inquiry_image"]);
        }
        $stmt_del = $obj->con1->prepare("DELETE FROM `inquiry` WHERE id=?");
        $stmt_del->bind_param("i", $u_id);
        $Resp = $stmt_del->execute();
        if (!$Resp) {
            throw new Exception("Problem in deleting! " . strtok($obj->con1->error,  '('));
        }
        $stmt_del->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "data_del", time() + 3600, "/");
    }
    header("location:inquiry.php");
}
?>


<h4 class="fw-bold py-3 mb-4">Inquiry Master</h4>

<?php
if (isset($_COOKIE["msg"])) {

    if ($_COOKIE['msg'] == "data") {

        ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <i class='bx bxs-check-circle'></i>Data added succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
<script type="text/javascript">
eraseCookie("msg")
</script>
<?php
        }
    if ($_COOKIE['msg'] == "update") {

        ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <i class='bx bxs-check-circle'></i>Data updated succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
<script type="text/javascript">
eraseCookie("msg")
</script>
<?php
        }
    if ($_COOKIE['msg'] == "data_del") {

        ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <i class='bx bxs-cross-circle'></i>Data deleted succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
<script type="text/javascript">
eraseCookie("msg")
</script>
<?php
        }
    if ($_COOKIE['msg'] == "fail") {
        ?>

<div class="alert alert-danger alert-dismissible" role="alert">
    <i class='bx bx-x-circle'></i>An error occured! Try again.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
<script type="text/javascript">
eraseCookie("msg")
</script>
<?php
        }
    }
if (isset($_COOKIE["sql_error"])) {
    ?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <?php echo urldecode($_COOKIE['sql_error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>

<script type="text/javascript">
eraseCookie("sql_error")
</script>
<?php
    }
if (isset($_COOKIE["excelmsg"])) {
    ?>
<div class="alert alert-primary alert-dismissible" role="alert">
    <?php echo $_COOKIE['excelmsg'] ?>
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
        <!-- Ensure the form uses POST and submits to the current page -->
        <form class="modal-content" method="post">
            <div class="modal-header">
                <h5 class="modal-title" id="backDropModalTitle">Delete Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="visitor_idBackdrop" class="form-label" id="label_del"></label>
                        <!-- Hidden input to hold the ID -->
                        <input type="hidden" name="u_id" id="u_id">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <!-- Submit button for the form -->
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
            <a class="btn btn-primary" href="#" onclick="javascript:adddata()" style="margin-right:15px;"><i
                    class="bx bx-plus"></i> Add</a>

        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover" id="table_id">

                <thead>
                    <tr>
                        <th>Srno</th>
                        <th>Visitor</th>
                        <th>Inquired For</th>
                        <th>Attended By</th>
                        <th>Architect</th>
                        <th>Start Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    $stmt_list = $obj->con1->prepare("SELECT i.*, GROUP_CONCAT(c.name ORDER BY FIND_IN_SET(c.id, i.inquired_for)) AS category_names, v1.full_name,a1.name AS architect_name,u1.name AS attended_by_name,GROUP_CONCAT(p.image ORDER BY p.id) AS property_images FROM inquiry i JOIN category c ON FIND_IN_SET(c.id, REPLACE(REPLACE(i.inquired_for, '[', ''), ']', '')) JOIN visitor v1 ON i.visitor_id = v1.id JOIN architect a1 ON i.architect_id = a1.id LEFT JOIN users u1 ON i.attended_by = u1.id LEFT JOIN property_image p ON i.id = p.inq_id GROUP BY i.id ORDER BY i.id DESC;");
                    $stmt_list->execute();
                    $result = $stmt_list->get_result();

                    $stmt_list->close();
                    $i = 1;
                    while ($row = mysqli_fetch_array($result)) {
                        ?>

                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $row["full_name"] ?></td>
                        <td><?php echo $row["category_names"] ?></td>
                        <td><?php echo $row["attended_by_name"] ?></td>
                        <td><?php echo $row["architect_name"] ?></td>
                        <td><?php echo date('d - m - Y', strtotime($row["start_date"])); ?></td>
                        <?php if ($row["status"] == 'enable') { ?>
                        <td style="color:green"><?php echo "Enable" ?></td>
                        <?php } else if ($row["status"] == 'disable') { ?>
                        <td style="color:red"><?php echo "Disable" ?></td>
                        <?php } ?>
                        <td>
                            <a
                                href="javascript:editdata('<?php echo $row["id"] ?>','<?php echo base64_encode($row["full_name"]) ?>');"><i
                                    class="bx bx-edit-alt me-1"></i> </a>
                            <a
                                href="javascript:deletedata('<?php echo $row["id"] ?>','<?php echo base64_encode($row["full_name"]) ?>');"><i
                                    class="bx bx-trash me-1" style="color:red"></i> </a>
                            <a
                                href="javascript:viewdata('<?php echo $row["id"] ?>','<?php echo base64_encode($row["full_name"]) ?>');"><i
                                    class="fa-regular fa-eye" style="color:green"></i></a>
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
    window.location = "inquiry_add.php";
}

function editdata(id) {
    eraseCookie("view_id");
    createCookie("edit_id", id, 1);
    window.location = "inquiry_add.php";
}

function viewdata(id) {
    eraseCookie("edit_id");
    createCookie("view_id", id, 1);
    window.location = "inquiry_add.php";
}

function deletedata(id) {
    $('#backDropModal').modal('toggle');
    $('#u_id').val(id); // Set the hidden field with the ID to delete
    $('#label_del').html('Are you sure you want to DELETE?');
}
</script>
<?php
include "footer.php";
?>