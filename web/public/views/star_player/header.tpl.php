<?php
    $num_notifications = ( static::$num_unread_notifications != 0 ) ? "(" . static::$num_unread_notifications . ")" : "";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $num_notifications . " " . $page_title; ?></title>
        
        <link rel="stylesheet" type="text/css" href="styles/<?php echo $theme; ?>/css/raw.css" />
        
        <script type="text/javascript" src="scripts/javascript.js"></script>
    </head>
    
    <body>
    	<?php include_once( 'status_bar.tpl.php' ); ?>

        <div class="container middle_container">
        	<div id="header">
        	
        	</div>
        	
        	<div id="content">