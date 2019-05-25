<?php
/**
 * GIT DEPLOYMENT SCRIPT
 *
 * Used for automatically deploying websites via GitHub
 * Based on: https://gist.github.com/oodavid/1809044
 */
$key = '[SET_YOUR_RANDOM_KEY]';
if ($_GET['key'] != $key){
    header('Location: ./');
    die();
}
    
// array of commands
$commands = array(
    'echo $PWD',
    'whoami',
    'git checkout -- .', 
    'git pull',
    'git status',
    'git submodule sync',
    'git submodule update',
    'git submodule status',
);
chdir("/home/[SET_PATCH_TO_PROJECT]/public_html");
// exec commands
$output = '';
foreach($commands AS $command){
    $tmp = shell_exec($command);
    
    $output .= "<span style=\"color: #6BE234;\">\$</span><span style=\"color: #729FCF;\">{$command}\n</span><br />";
    $output .= htmlentities(trim($tmp)) . "\n<br /><br />";
}
?>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>GIT DEPLOYMENT SCRIPT</title>
</head>
<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
<div style="width:700px">
    <div style="float:left;width:350px;">
    <p style="color:white;">Git Deployment Script</p>
    <?php echo $output; ?>
    </div>
</div>
</body>
</html>