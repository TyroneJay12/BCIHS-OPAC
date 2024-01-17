
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Borrowed Materials/Books List</h3>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bborroweded">
            <colgroup>
                <col width="5%">
                <col width="15%">
                <col width="20%">
                <col width="20%">
                <col width="10%">
                <col width="15%">
                <col width="15%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">DateTime Created</th>
                    <th class="text-center p-0">Transaction Code</th>
                    <th class="text-center p-0">User</th>
                    <th class="text-center p-0">Items</th>
                    <th class="text-center p-0">Status</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT b.*,c.fullname FROM `borrowed_list` b inner join `user_list` c on b.user_id = c.user_id order by b.`status` asc,  strftime('%s',b.`date_created`) asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                        $items = $conn->query("SELECT count(book_id) as `items` from `borrowed_items` where `borrowed_id` = '{$row['borrowed_id']}'")->fetchArray()['items'];
                        $row['date_created'] = new DateTime($row['date_created'], new DateTimeZone(dZone));
                        $row['date_created']->setTimezone(new DateTimeZone(tZone));
                        $row['date_created'] = $row['date_created']->format('Y-m-d H:i');
                        if($row['due_date'] != null){
                            $row['due_date'] = new DateTime($row['due_date'], new DateTimeZone(dZone));
                            $row['due_date']->setTimezone(new DateTimeZone(tZone));
                            $row['due_date'] = $row['due_date']->format('Y-m-d H:i');
                        }
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                    <td class="py-0 px-1 text-center"><?php echo $row['transaction_code'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['fullname'] ?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($items) ?></td>
                    <td class="py-0 px-1 text-center">
                    <?php if($row['status'] == 1): ?>
                        <span class="badge bg-primary rounded-pill"><small>Confirmed</small></span>
                    <?php elseif($row['status'] == 2): ?>
                        <span class="badge bg-warning rounded-pill"><small>Picked-Up</small></span>
                    <?php elseif($row['status'] == 3): ?>
                        <span class="badge bg-success rounded-pill"><small>Returned</small></span>
                    <?php else: ?>
                        <span class="badge bg-dark text-light rounded-pill"><small>Pending</small></span>
                    <?php endif; ?>
                    </td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item view_data" data-id = '<?php echo $row['borrowed_id'] ?>' href="javascript:void(0)">View</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['borrowed_id'] ?>'  data-name = '<?php echo $row['transaction_code']." of ".$row['fullname'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </th>
                </tr>
                <?php endwhile; ?>
               
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#create_new').click(function(){
            uni_modal('Add New Product',"manage_product.php",'mid-large')
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Product Details',"manage_product.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.view_data').click(function(){
            uni_modal('Borrowed Details',"view_borrowed.php?id="+$(this).attr('data-id'),'large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete borrowed <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })
        $('table td,table th').addClass('align-middle')
        $('table').dataTable({
            columnDefs: [
                { borrowedable: false, targets:6 }
            ]
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'../Actions.php?a=delete_transaction',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>