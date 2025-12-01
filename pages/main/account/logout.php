<?php
session_start();
session_destroy();

$current_path = $_SERVER['PHP_SELF'];

$path_parts = explode('/', $current_path);

$project_folder = isset($path_parts[1]) ? '/' . $path_parts[1] : '';

if (strpos($project_folder, '.php') !== false) {
    $project_folder = '';
}
?>
<!DOCTYPE html>
<html>

<head>
    <script>
        localStorage.removeItem('chat_session_id');

        window.location.href = "<?php echo $project_folder; ?>/index.php?navigate=login";
    </script>
</head>

<body>
</body>

</html>