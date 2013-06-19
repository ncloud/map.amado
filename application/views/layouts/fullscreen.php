<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php echo $title_for_layout;?></title>

<meta name="title" content="">
<meta name="description" content="">
<meta name="keywords" content="">

<meta content='width=device-width, initial-scale=1.0' name='viewport' />

<link rel="stylesheet" href="<?php echo site_url('/css/maximage/jquery.maximage.css');?>" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="<?php echo site_url('/css/maximage/screen.css');?>" type="text/css" media="screen" charset="utf-8" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js"></script>
      
<?php echo $styles_for_layout;?>
<?php echo $scripts_for_layout;?>

</head>
<body>      
    <div id="content">
        <?php echo $content_for_layout;?>
    </div>
</body>
</html>