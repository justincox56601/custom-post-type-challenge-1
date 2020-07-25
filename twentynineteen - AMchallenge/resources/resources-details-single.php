<?php $rcpt = new ResourcesCPT(); ?>
<div class="resources-content">
    <?php
    //the loop
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post(); 
            $rcpt->show_download_link($post->ID);
            the_content();
        } 
    }
    $rcpt->show_archive_link();
    ?>
</div> <!-- resources-content -->

