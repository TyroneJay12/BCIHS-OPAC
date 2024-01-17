<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM admin_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function faculty_student_login(){
        extract($_POST);
        $sql = "SELECT * FROM user_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            if($qry['status'] != 1){
            $resp['status'] = "failed";
            $resp['msg'] = "Your Account has been blocked by the management. Contact the management to settle.";
            }else{
                $resp['status'] = "success";
                $resp['msg'] = "Login successfully.";
                foreach($qry as $k => $v){
                    if(!is_numeric($k))
                    $_SESSION[$k] = $v;
                }
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./admin");
    }
    function faculty_student_logout(){
        session_destroy();
        header("location:./");
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$_SESSION['admin_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function update_credentials_user(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `user_list` set {$data} where user_id = '{$_SESSION['user_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_category(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `category_list` (`name`,`status`)VALUES('{$name}','{$status}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `category_list` set {$data} where `category_id` = '{$id}' ";
        }
        @$check= $this->query("SELECT COUNT(category_id) as count from `category_list` where `name` = '{$name}' ".($id > 0 ? " and category_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Category Name already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Category successfully saved.";
                else
                    $resp['msg'] = "Category successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Category Failed.";
                else
                    $resp['msg'] = "Updating Category Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_category(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `category_list` where category_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Category successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_cat(){
        extract($_POST);
        @$update = $this->query("UPDATE `category_list` set `status` = '{$status}' where category_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Category Status successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_sub_category(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `sub_category_list` (`name`,`category_id`,`status`)VALUES('{$name}','{$category_id}','{$status}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `sub_category_list` set {$data} where `sub_category_id` = '{$id}' ";
        }
        @$check= $this->query("SELECT COUNT(sub_category_id) as count from `sub_category_list` where `name` = '{$name}' and `category_id` = '{$category_id}' ".($id > 0 ? " and sub_category_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Sub Category Name already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Sub Category successfully saved.";
                else
                    $resp['msg'] = "Sub Category successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Sub Category Failed.";
                else
                    $resp['msg'] = "Updating Sub Category Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_sub_category(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `sub_category_list` where sub_category_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Sub Category successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_sub_cat(){
        extract($_POST);
        @$update = $this->query("UPDATE `sub_category_list` set `status` = '{$status}' where sub_category_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Sub Category Status successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_admin(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id','type'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        @$check= $this->query("SELECT count(admin_id) as `count` FROM admin_list where `username` = '{$username}' ".($id > 0 ? " and admin_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `admin_list` {$data}";
            }else{
                $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'New Admin User successfully saved.';
                else
                $resp['msg'] = 'Admin User Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving Admin User Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_admin(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `admin_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Admin User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_user(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if($k == 'password'){
                if(empty($v))
                    continue;
                else
                    $v= md5($v);
            }
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        @$check= $this->query("SELECT count(user_id) as `count` FROM user_list where `username` = '{$username}' ".($id > 0 ? " and user_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `user_list` {$data}";
            }else{
                $sql = "UPDATE `user_list` set {$data} where user_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'Account successfully Created.';
                else
                $resp['msg'] = 'Account Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_user(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `user_list` where user_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_book(){
        extract($_POST);
        @$check= $this->query("SELECT count(book_id) as `count` FROM `book_list` where `isbn` = '{$name}' ".($id > 0 ? " and book_id != '{$id}'" : ''))->fetchArray()['count'];
        if($check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Bokk ISBN already exists.";
        }else{
            $data = "";
            foreach($_POST as $k =>$v){
                if(!in_array($k,array('id','thumbnail','img','pdf','category_id'))){
                    if(empty($id)){
                        $columns[] = "`{$k}`"; 
                        $values[] = "'{$v}'"; 
                    }else{
                        if(!empty($data)) $data .= ", ";
                        $data .= " `{$k}` = '{$v}'";
                    }
                }
            }
            if(isset($columns) && isset($values)){
                $data = "(".(implode(",",$columns)).") VALUES (".(implode(",",$values)).")";
            }
            if(empty($id)){
                $sql = "INSERT INTO `book_list` {$data}";
            }else{
                $sql = "UPDATE `book_list` set {$data} where book_id = '{$id}'";
            } 
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'Book Successfully added.';
                else
                $resp['msg'] = 'Book Successfully updated.';
                if(empty($id))
                $last_id = $this->query("SELECT max(book_id) as last_id from `book_list`")->fetchArray()['last_id'];
                $pid = !empty($id) ? $id : $last_id;
                if(isset($_FILES)){
                    foreach($_FILES as $k=>$v){
                        $$k=$v;
                    }
                }
                if(isset($thumbnail) && !empty($thumbnail['tmp_name'])){
                    $thumb_file = $thumbnail['tmp_name'];
                    $thumb_fname = $pid.'.png';
                    $file_type = mime_content_type($thumb_file);
                    list($width, $height) = getimagesize($thumb_file);
                    $t_image = imagecreatetruecolor('350', '350');
                    if(in_array($file_type,array('image/png','image/jpeg','image/jpg'))){
                        $gdImg = ($file_type =='image/png') ? imagecreatefrompng($thumb_file) : imagecreatefromjpeg($thumb_file);
                        imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, '350', '350', $width, $height);
                        if($t_image){
                            if(is_file(__DIR__.'/uploads/thumbnails/'.$thumb_fname))
                                unlink(__DIR__.'/uploads/thumbnails/'.$thumb_fname);
                                imagepng($t_image,__DIR__.'/uploads/thumbnails/'.$thumb_fname);
                                imagedestroy($t_image);
                        }else{
                            $resp['msg'] = 'Book Successfully saved but Thumbnail image failed to upload.';
                        }
                    }else{
                            $resp['msg'] = 'Book Successfully saved but Thumbnail image failed to upload due to invalid file type.';
                    }
                }
                if(isset($img) && count($img['tmp_name']) > 0){
                    if(!is_dir(__DIR__.'/uploads/images/'.$pid))
                    mkdir(__DIR__.'/uploads/images/'.$pid);
                    for($i = 0;$i < count($img['tmp_name']); $i++){
                        if(!empty($img['tmp_name'][$i])){
                            $img_file = $img['tmp_name'][$i];
                            $ex = explode('.',$img['name'][$i]);
                            $_fname = $ex[0];
                            $_i = 1;
                            while(true){
                                $_i++;
                                if(is_file(__DIR__.'/uploads/images/'.$pid.'/'.$_fname.'.png')){
                                    $_fname =$ex[0].'_'.$_i;
                                }else{
                                    break;
                                }
                            }
                            $img_fname = $_fname.'.png';
                            $file_type = mime_content_type($img_file);
                            list($width, $height) = getimagesize($img_file);
                            $t_image = imagecreatetruecolor('350', '350');
                            if(in_array($file_type,array('image/png','image/jpeg','image/jpg'))){
                                $gdImg = ($file_type =='image/png') ? imagecreatefrompng($img_file) : imagecreatefromjpeg($img_file);
                                imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, '350', '350', $width, $height);
                                if($t_image){
                                    imagepng($t_image,__DIR__.'/uploads/images/'.$pid.'/'.$img_fname);
                                    imagedestroy($t_image);
                                }else{
                                    $resp['msg'] = 'Book Successfully saved but Book image failed to upload.';
                                }
                            }else{
                                $resp['msg'] = 'Book Successfully saved but Book image failed to upload due to invalid file type.';
                            }

                        }
                    }
                }
                // pdf
                if(isset($pdf) && count($pdf['tmp_name']) > 0){
                    if(!is_dir(__DIR__.'/uploads/pdfs/'.$pid))
                        mkdir(__DIR__.'/uploads/pdfs/'.$pid);
                    
                    for($i = 0; $i < count($pdf['tmp_name']); $i++){
                        if(!empty($pdf['tmp_name'][$i])){
                            $pdf_file = $pdf['tmp_name'][$i];
                            $ex = explode('.',$pdf['name'][$i]);
                            $_fname = $ex[0];
                            $_i = 1;
                
                            while(true){
                                $_i++;
                                if(is_file(__DIR__.'/uploads/pdfs/'.$pid.'/'.$_fname.'.pdf')){
                                    $_fname = $ex[0].'_'.$_i;
                                }else{
                                    break;
                                }
                            }
                
                            $pdf_fname = $_fname.'.pdf';
                            $file_type = mime_content_type($pdf_file);
                
                            if($file_type == 'application/pdf'){
                                move_uploaded_file($pdf_file, __DIR__.'/uploads/pdfs/'.$pid.'/'.$pdf_fname);
                                $resp['msg'] = 'PDF Successfully saved.';
                            } else {
                                $resp['msg'] = 'Failed to upload PDF due to invalid file type.';
                            }
                        }
                    }
                }                
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'An error occured. Error: '.$this->lastErrorMsg();
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    function delete_book(){
        extract($_POST);
        @$delete = $this->query("DELETE FROM `book_list` where book_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Book successfully deleted.';
            if(is_file(__DIR__.'/uploads/thumbnails/'.$id.'.png'))
                unlink(__DIR__.'/uploads/thumbnails/'.$id.'.png');
            if(is_dir(__DIR__.'/uploads/images/'.$id)){
                $scan = scandir(__DIR__.'/uploads/images/'.$id);
                foreach($scan as $img){
                    if(!in_array($img,array('.','..'))){
                        unlink(__DIR__.'/uploads/images/'.$id.'/'.$img);
                    }
                }
                rmdir(__DIR__.'/uploads/images/'.$id);
            }
        }else{
            $resp['status']='failed';
            $resp['msg'] = 'An error occure. Error: '.$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function delete_img(){
        extract($_POST);
        if(is_file(__DIR__.$path)){
            unlink(__DIR__.$path);
        }
        $resp['status'] = 'success';
        return json_encode($resp);
    }
    function add_to_cart(){
        extract($_POST);
        $user_id = $_SESSION['user_id'];
        $check = $this->query("SELECT count(book_id) as `count` FROM `cart_list` where `book_id` = '{$book_id}' and `user_id` = '{$user_id}'")->fetchArray()['count'];
        if($check > 0){
            $resp['status'] ='failed';
            $resp['msg'] ='Book/Material already exists on cart list';
        }else{
            $sql = "INSERT INTO `cart_list` (`book_id`,`user_id`)VALUES('{$book_id}','{$user_id}')";
            $save = $this->query($sql);
            if($save){
                $resp['status'] ='success';
                $count = $this->query("SELECT count(book_id) as total FROM `cart_list` where `user_id` = '{$user_id}'")->fetchArray()['total'];
                $resp['cart_count'] = $count;
            }else{
                $resp['status'] ='failed';
                $resp['sql'] =$sql;
            }
        }

        return json_encode($resp);
    }
    function update_cart(){
        extract($_POST);
        $sql = "UPDATE `cart_list` set `quantity` = '{$quantity}' where `book_id` = '{$book_id}' and `user_id` = '{$user_id}'";
        $save = $this->query($sql);
        if($save){
            $resp['status'] ='success';
            $count = $this->query("SELECT SUM(quantity) as total FROM `cart_list` where `user_id` = '{$user_id}'")->fetchArray()['total'];
            $resp['cart_count'] = $count;
        }else{
            $resp['status'] ='failed';
            $resp['sql'] =$sql;
        }
        return json_encode($resp);
    }
    function delete_from_cart(){
        extract($_POST);
        $user_id = $_SESSION['user_id'];
        $sql = "DELETE FROM `cart_list` where `book_id` = '{$id}' and `user_id` = '{$user_id}'";
        $delete = $this->query($sql);
        if($delete){
            $resp['status'] ='success';
            $count = $this->query("SELECT count(book_id) as total FROM `cart_list` where `user_id` = '{$user_id}'")->fetchArray()['total'];
            $resp['cart_count'] = $count;
        }else{
            $resp['status'] ='failed';
            $resp['sql'] =$sql;
        }
        return json_encode($resp);
    }
    function book_to_borrow(){
        extract($_POST);
        $user_id = $_SESSION['user_id'];
        $data = "";
        $data_items = "";
        $code = "";
        while(true){
            $code = mt_rand(1,mt_getrandmax());
            $code = sprintf("%.9d",$code);
            $check = $this->query("SELECT count(borrowed_id) as `count` from `borrowed_list` where transaction_code = '{$code}' ")->fetchArray()['count'];
            if($check <= 0)
            break;
        }
        $sql = "INSERT INTO `borrowed_list` (`user_id`,`transaction_code`)VALUES('{$user_id}','{$code}')";
        $save = $this->query($sql);
        if($save){
            $resp['status'] = 'success';
            $last_id = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];

            $cart = $this->query("SELECT * FROM `cart_list` where `user_id` = '{$user_id}'");
            while($row = $cart->fetchArray()){
                if(!empty($data_items)) $data_items .= ", ";
                $data_items .= "('{$last_id}','{$row['book_id']}')";
            }
            $this->query("INSERT INTO `borrowed_items` (`borrowed_id`,`book_id`) VALUES {$data_items}");
            $this->query("DELETE FROM `cart_list` where `user_id` = '{$user_id}'");
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Data Successfully Submitted.';
        }else{
            $resp['status'] = 'failed';
        }
        
        return json_encode($resp);
    }
    function update_borrowed_status(){
        extract($_POST);
        $sql = "UPDATE `borrowed_list` set `status` = '{$status}' where `borrowed_id` = '{$borrowed_id}' ";
        @$update = $this->query($sql);
        if($update){
            $resp['status']='success';
            $resp['msg'] = "Borrowed Status successfully updated";
            $resp['return_status'] = $status;
        }else{
            $resp['status']='failed';
            $resp['msg'] = "Failed to Update status. Error: ".$this->lastErrorMsg();
            $resp['sql'] = $sql;
        }
        return json_encode($resp);
    }
    function update_borrowed_due(){
        extract($_POST);
        $sql = "UPDATE `borrowed_list` set `due_date` = '{$due_date}' where `borrowed_id` = '{$borrowed_id}' ";
        @$update = $this->query($sql);
        if($update){
            $resp['status']='success';
            $resp['msg'] = "Returning Date successfully updated";
            $resp['due_formatted'] = date("F d, Y",strtotime($due_date));
            $resp['due'] = date("Y-m-d",strtotime($due_date));
        }else{
            $resp['status']='failed';
            $resp['msg'] = "Failed to Update status. Error: ".$this->lastErrorMsg();
            $resp['sql'] = $sql;
        }
        return json_encode($resp);
    }
    function update_user_status(){
        extract($_POST);
        $sql = "UPDATE `user_list` set `status` = '{$status}' where `user_id` = '{$id}' ";
        @$update = $this->query($sql);
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['msg'] = "User Status successfully updated";
            $_SESSION['flashdata']['type'] = "success";
        }else{
            $resp['status']='failed';
            $resp['msg'] = "Failed to Update status. Error: ".$this->lastErrorMsg();
            $resp['sql'] = $sql;
        }
        return json_encode($resp);
    }
    function delete_transaction(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `borrowed_list` where borrowed_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Order successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'faculty_student_login':
        echo $action->faculty_student_login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'faculty_student_logout':
        echo $action->faculty_student_logout();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'update_credentials_user':
        echo $action->update_credentials_user();
    break;
    case 'save_category':
        echo $action->save_category();
    break;
    case 'delete_category':
        echo $action->delete_category();
    break;
    case 'update_stat_cat':
        echo $action->update_stat_cat();
    break;
    case 'save_sub_category':
        echo $action->save_sub_category();
    break;
    case 'delete_sub_category':
        echo $action->delete_sub_category();
    break;
    case 'update_stat_sub_cat':
        echo $action->update_stat_sub_cat();
    break;
    case 'save_admin':
        echo $action->save_admin();
    break;
    case 'delete_admin':
        echo $action->delete_admin();
    break;
    case 'save_user':
        echo $action->save_user();
    break;
    case 'delete_user':
        echo $action->delete_user();
    break;
    case 'save_book':
        echo $action->save_book();
    break;
    case 'delete_book':
        echo $action->delete_book();
    break;
    case 'save_attendance':
        echo $action->save_attendance();
    break;
    case 'delete_img':
        echo $action->delete_img();
    break;
    case 'add_to_cart':
        echo $action->add_to_cart();
    break;
    case 'update_cart':
        echo $action->update_cart();
    break;
    case 'delete_from_cart':
        echo $action->delete_from_cart();
    break;
    case 'book_to_borrow':
        echo $action->book_to_borrow();
    break;
    case 'update_borrowed_status':
        echo $action->update_borrowed_status();
    break;
    case 'update_borrowed_due':
        echo $action->update_borrowed_due();
    break;
    case 'update_user_status':
        echo $action->update_user_status();
    break;
    case 'delete_transaction':
        echo $action->delete_transaction();
    break;
    default:
    // default action here
    break;
}