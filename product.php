<?php
include "header.php";
// delete data
if(isset($_REQUEST["btndelete"]))
{
  try
  {
    $stmt_del = $obj->con1->prepare("DELETE FROM product WHERE id='".$_REQUEST["u_id"]."'");
  	$Resp=$stmt_del->execute();
    if(!$Resp)
    {
      if(strtok($obj->con1-> error,  ':')=="Cannot delete or update a parent row")
      {
        throw new Exception("This is already in use!");
      }
    }
    $stmt_del->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }

  if($Resp)
  {
	 setcookie("msg", "data_del",time()+3600,"/");
  }
  header("location:product.php");
}


?>

<h4 class="fw-bold py-3 mb-4">Product Master</h4>

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
            <a class="btn btn-primary" href="#" onclick="javascript:adddata()" style="margin-right:15px;"><i class="bx bx-plus"></i> Add
                Product</a>

        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover" id="table_id">

                <thead>
                    <tr>
                        <th>Sr.no</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Company</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php 
                        $stmt_list = $obj->con1->prepare("SELECT p1.*, c1.name as cname,co1.company_name,u1.unit_name FROM product p1 JOIN category c1 ON p1.category_id = c1.id JOIN company co1 ON p1.company_id = co1.id JOIN units u1 ON p1.unit_id=u1.id");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        $stmt_list->close();
                        $i=1;
                        while($res=mysqli_fetch_array($result))
                        {
                          ?>

                    <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $res["name"]?></td>
                        <td><?php echo $res["cname"]?></td>
                        <td><?php echo $res["company_name"]?></td>
                        <td><?php echo $res["unit_name"]?></td>
                        <td><?php echo $res["price"]?></td>
                        <?php if($res["status"]=='Enable'){	?>
                        <td style="color:green"><?php echo $res["status"]?></td>
                        <?php } else if($res["status"]=='Disable'){	?>
                        <td style="color:red"><?php echo $res["status"]?></td>
                        <?php } ?>
                        <td>
                            <a href="javascript:editdata('<?php echo $res["id"]?>');"><i
                                    class="bx bx-edit-alt me-1"></i> </a>
                                    <a
                                href="javascript:deletedata('<?php echo $res["id"] ?>','<?php echo base64_encode($res["name"]) ?>');"><i
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
    window.location = "product_add.php";
}

function editdata(id) {
    eraseCookie("view_id");
    createCookie("edit_id", id, 1);
    window.location = "product_add.php";
}

function viewdata(id) {
    eraseCookie("edit_id");
    createCookie("view_id", id, 1);
    window.location = "product_add.php";
}

function deletedata(id, name) {
    $('#backDropModal').modal('toggle');
    $('#u_id').val(id);
    $('#label_del').html('Are you sure you want to DELETE visitor - ' + atob(name) + ' ?');
}
</script>
<?php 
include "footer.php";
?>
<!-- pushed again -->