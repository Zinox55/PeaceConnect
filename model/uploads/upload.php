<?php
function uploadImage($file) {
    $target_dir = "./";
    $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $new_name = uniqid() . "." . $extension;
    $target_file = $target_dir . $new_name;

    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array(strtolower($extension), $allowed)) return false;

    if ($file["size"] > 5000000) return false; // 5Mo max

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return "model/uploads/" . $new_name;
    }
    return false;
}
?>