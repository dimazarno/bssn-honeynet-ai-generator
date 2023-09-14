<?php
if (isset($_GET['pin_code'])) {
    $pincode = $_GET['pin_code'];
    $bcryptHash = password_hash($pincode, PASSWORD_BCRYPT);
    echo $bcryptHash;
}
