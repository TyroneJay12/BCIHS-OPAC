<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location:./login.php");
    exit;
}


require_once('../DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BCIHS-OPAC" />
    <meta name="author" content="STEM2_GRP3" />
    <title><?php echo ucwords(str_replace('_','',$page)) ?> | BCIHS-OPAC</title>
    <link rel="icon" type="image/ico" href="assets/favicon.ico">
    <link rel="stylesheet" href="../Font-Awesome-master/css/all.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../DataTables/datatables.min.css">
    <script src="../DataTables/datatables.min.js"></script>
    <script src="../Font-Awesome-master/js/all.min.js"></script>
    <script src="../js/script.js"></script>

    <style>

:root{
    --bs-bg-success:#00ff80 !important;
    --bs-success-rgb: 0,255,128 !important;
}
html,body{
    height:100%;
    width:100%;
    background-image: url('assets/bg.png');
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center center;
        background-size: 40vw;
}

body::before {
    content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white; /* Set the background color */
        opacity: 0.1; /* Adjust the opacity (0.0 to 1.0) */
        z-index: -1;
    }
}
main{
    height:100%;
    display:flex;
    flex-flow:column;
}
#page-container{
    flex: 1 1 auto; 
    overflow:auto;
}
#topNavBar{
    flex: 0 1 auto; 
}
.thumbnail-img{
    width:50px;
    height:50px;
    margin:2px
}
.truncate-1 {
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}
.truncate-3 {
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}
.modal-dialog.large {
    width: 80% !important;
    max-width: unset;
}
.modal-dialog.mid-large {
    width: 50% !important;
    max-width: unset;
}
@media (max-width:720px){
    
    .modal-dialog.large {
        width: 100% !important;
        max-width: unset;
    }
    .modal-dialog.mid-large {
        width: 100% !important;
        max-width: unset;
    }  

}
.display-select-image{
    width:60px;
    height:60px;
    margin:2px
}
img.display-image {
    width: 100%;
    height: 45vh;
    object-fit: cover;
    background: black;
}
/* width */
::-webkit-scrollbar {
width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
background: #f1f1f1; 
}

/* Handle */
::-webkit-scrollbar-thumb {
background: #888; 
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
background: #555; 
}
.img-del-btn{
    right: 2px;
    top: -3px;
}
.img-del-btn>.btn{
    font-size: 10px;
    padding: 0px 2px !important;
}

.navbar-logo {
    margin-right: 10px; /* Adjust the value based on your preference */
}


    </style>

</head>
<body>
    <main>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-gradient" id="topNavBar">
        <div class="container">
        <img src="assets/BCIHS.png" alt="BCIHS-OPAC Logo" class="navbar-logo">
            <a class="navbar-brand" href="#">
            BCIHS-OPAC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'home')? 'active' : '' ?>" aria-current="page" href="./">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  <?php echo ($page == 'books')? 'active' : '' ?>" href="./?page=books">Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  <?php echo ($page == 'borrowed')? 'active' : '' ?>" href="./?page=borrowed">Borrowed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'users')? 'active' : '' ?>" aria-current="page" href="./?page=users">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'admin')? 'active' : '' ?>" aria-current="page" href="./?page=admin">Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./?page=maintenance">Maintenance</a>
                    </li>
                    
                </ul>
            </div>
            <div>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle bg-transparent  text-light border-0" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    Hello <?php echo $_SESSION['fullname'] ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="./?page=manage_account">Manage Account</a></li>
                    <li><a class="dropdown-item" href="../Actions.php?a=logout">Logout</a></li>
                </ul>
            </div>
            </div>
        </div>
    </nav>
    <div class="container py-3" id="page-container">
        <?php 
            if(isset($_SESSION['flashdata'])):
        ?>
        <div class="dynamic_alert alert alert-<?php echo $_SESSION['flashdata']['type'] ?>">
        <div class="float-end"><a href="javascript:void(0)" class="text-dark text-decoration-none" onclick="$(this).closest('.dynamic_alert').hide('slow').remove()">x</a></div>
            <?php echo $_SESSION['flashdata']['msg'] ?>
        </div>
        <?php unset($_SESSION['flashdata']) ?>
        <?php endif; ?>
        <?php
            include $page.'.php';
        ?>
    </div>
    </main>
    <div class="modal fade" id="uni_modal" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm rounded-0 btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
            <button type="button" class="btn btn-sm rounded-0 btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="uni_modal_secondary" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm rounded-0 btn-primary" id='submit' onclick="$('#uni_modal_secondary form').submit()">Save</button>
            <button type="button" class="btn btn-sm rounded-0 btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body">
            <div id="delete_content"></div>
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-primary btn-sm rounded-0" id='confirm' onclick="">Continue</button>
            <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
</body>
</html>