<?php 
    //do stuff
    $rcpt = new ResourcesCPT();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo wp_get_document_title(); ?></title>
    <?php wp_head();?>
</head>
<body>
<header class='resources-header'>
    <?php the_post_thumbnail('post-thumbnail', array('class'=>'image', 'title'=>'featured image', 'alt'=>'featured image')); ?>
    <div class="content">
        <p class="site-title"><?php bloginfo('name')?> <span class='smaller'>- <?php bloginfo('description')?></span></p>
        <?php wp_nav_menu(array(
            'theme-location' => 'primary',
            'container_class' => 'resources-nav',
        )); ?>
        <?php the_title("<h1 class='post-title'>", "</h1>") ?>
        <?php $rcpt->show_meta(); ?>
    </div>
    
</header>    
