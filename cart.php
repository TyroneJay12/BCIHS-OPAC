<h3>Cart</h3>
<hr>
<div class="col-lg-12">
    <div class="w-100">
        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-hover table-stripped">
                    <colgroup>
                        <col width="10%">
                        <col width="90%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Book/Material Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $qry = $conn->query("SELECT c.*,b.title,b.description,b.isbn,b.author,b.sub_category_id FROM `cart_list` c inner join `book_list` b on c.book_id = b.book_id where c.`user_id` = '{$_SESSION['user_id']}'");
                        $total = 0;
                        while($row=$qry->fetchArray()):
                            $total +=1;
                            $row['sub_category'] = "N/A";
                            $row['category'] = "N/A";
                            $sub_cat = $conn->query("SELECT * FROM `sub_category_list` where sub_category_id ='{$row['sub_category_id']}' ");
                            $res_subc = $sub_cat->fetchArray();
                            if($res_subc){
                                $row['sub_category'] = $res_subc['name'];
                                $cat = $conn->query("SELECT * FROM `category_list` where category_id ='{$res_subc['category_id']}' ");
                                $res_cat = $cat->fetchArray();
                                if($res_cat)
                                $row['category'] = $res_cat['name'];
                            }
                        ?>
                        <tr class="item" data-id="<?php echo $row['book_id'] ?>">
                            <td class="text-center align-middle">
                                <button class="btn btn-sm btn-danger rounded-0 del_item" button="button" data-id="<?php echo  $row['book_id'] ?>"><span class="fa fa-trash"></span></button>
                            </td>
                            <td class="align-middle">
                                <div class="w-100 d-flex">
                                    <div class="col-auto me-2">
                                        <img src="<?php echo './uploads/thumbnails/'.$row['book_id'].'.png' ?>" alt="" class="img-fluid border border-dark" height="75px" width="75px">
                                    </div>
                                    <div class="col-auto flex-grow-1">
                                        <div class="fs-5"><b><?php echo $row['title'] ?></b></div>
                                        <div class='lh-1'>
                                            <small><i><span class="text-muted">ISBN: </span><?php echo $row['isbn'] ?></i></small><br>
                                            <small><i><span class="text-muted">Author/s: </span><?php echo $row['author'] ?></i></small><br>
                                            <small><i><span class="text-muted">Category: </span><?php echo $row['category'] ?></i></small><br>
                                            <small><i><span class="text-muted">Sub Category: </span><?php echo $row['sub_category'] ?></i></small>
                                        </div>
                                        
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if(!$qry->fetchArray()): ?>
                            <tr>
                                <th class="text-center" colspan='2'>Cart is empty. <a href="./">Back to Home Page</a></th>
                            </tr>
                        <?php endif; ?>
                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-center fs-5" colspan="2" id="gTotal">Total Item: <?php echo number_format($total) ?></th>
                        </tr>
                    </tfoot>
                </table>
                <center><button class="btn btn-dark btn-sm rounded-0" style="display:none" type="button" id="borrow">Borrow</button></center>

            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        if($('.item').length < 0){
            $('#borrow').hide();
        }else{
            $('#borrow').show();
        }
        $('#borrow').click(function(){
            var conf = confirm("Are you sure to borrow the listed books/materials?")
            if(conf === true)
            book_to_borrow()
        })
        $('.del_item').click(function(){
            _conf("Are you sure to remove this Book/Material from cart list?",'delete_data',[$(this).attr('data-id')])
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'Actions.php?a=delete_from_cart',
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
                    $('#cart_count').text(resp.cart_count)
                    calc()
                    $('table tr.item[data-id="'+$id+'"]').remove()
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function book_to_borrow(){
        $('.pop_msg').remove()
        var _this = $('.card-body')
        var _el = $('<div>')
            _el.addClass('pop_msg')
        _this.find('button').attr('disabled',true)
        $.ajax({
            url:'Actions.php?a=book_to_borrow',
            dataType:'JSON',
            error:err=>{
                console.log(err)
                _el.addClass('alert alert-danger')
                _el.text("An error occurred.")
                _this.prepend(_el)
                _el.show('slow')
                _this.find('button').attr('disabled',false)
                _this.find('button[type="submit"]').text('Save')
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.replace("./?page=borrowed")
                }else{
                    _el.addClass('alert alert-danger')
                }
                _el.text(resp.msg)

                _el.hide()
                _this.prepend(_el)
                _el.show('slow')
                _this.find('button').attr('disabled',false)
                _this.find('button[type="submit"]').text('Save')
            }
        })
    }
</script>