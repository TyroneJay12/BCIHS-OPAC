<style>
    #uni_modal .modal-footer{
        display:none !important
    }
</style>
<?php 
require_once('../DBConnection.php');
$qry = $conn->query("SELECT * FROM user_list where user_id = '{$_GET['id']}'")->fetchArray();
foreach($qry as $k => $v){
    if(!is_numeric($k))
    $$k = $v;
}
?>
<div class="cotainer-flui">
    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Name:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $fullname ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Email:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $email ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Contact:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $contact ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Address:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $address ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Username:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $username ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Department:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $department ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Type:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $type ?></span>
                </div>
                <?php if($type =='Student'): ?>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Course/Level/Section:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $level_section ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-12">
        <div class="row justify-content-end mt-3">
            <button class="btn btn-sm rounded-0 btn-dark col-auto me-3" type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>