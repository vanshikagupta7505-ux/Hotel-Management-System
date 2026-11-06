<?php
function upload_food_image($field) {
    if (!isset($_FILES[$field]) || $_FILES[$field]["error"] == UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($_FILES[$field]["error"] != UPLOAD_ERR_OK) {
        return [null, "Image upload failed. Please try again."];
    }
    if ($_FILES[$field]["size"] > 2 * 1024 * 1024) {
        return [null, "Image must be smaller than 2 MB."];
    }

    $ext = strtolower(pathinfo($_FILES[$field]["name"], PATHINFO_EXTENSION));
    if (!in_array($ext, ["jpg", "jpeg", "png", "webp"])) {
        return [null, "Only JPG, PNG or WEBP images are allowed."];
    }
    if (!@getimagesize($_FILES[$field]["tmp_name"])) {
        return [null, "This file is not a valid image."];
    }

    $folder = __DIR__ . "/../images/food/";
    if (!is_dir($folder)) { mkdir($folder, 0777, true); }

    $name = uniqid("food_") . "." . $ext;
    if (!move_uploaded_file($_FILES[$field]["tmp_name"], $folder . $name)) {
        return [null, "Could not save the image."];
    }
    return [$name, null];
}
?>