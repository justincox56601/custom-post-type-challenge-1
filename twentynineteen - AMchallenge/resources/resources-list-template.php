<?php
$rcpt = new ResourcesCPT();
$r_args = array();

// check  get and cookie variables for the form fields
if(isset($_GET['topic'])){
    $r_args['topic'] = sanitize_text_field($_GET['topic']);
    setcookie('r_topic', $r_args['topic'], time() + 60*60*24, '/resources');
}elseif(isset($_COOKIE['r_topic'])){
    $r_args['topic'] = $_COOKIE['r_topic'];
}

if(isset($_GET['audience'])){
    $r_args['audience'] = sanitize_text_field($_GET['audience']);
    setcookie('r_audience', $r_args['audience'], time() + 60*60*24, '/resources');
}elseif(isset($_COOKIE['r_topic'])){
    $r_args['audience'] = $_COOKIE['r_audience'];
}

if(isset($_GET['search'])){
    $r_args['search'] = sanitize_text_field($_GET['search']);
    setcookie('r_search', $r_args['search'], time() + 60*60*24, '/resources');
}elseif(isset($_COOKIE['r_search'])){
    $r_args['search'] = $_COOKIE['r_search'];
}

get_header(); //use the standard theme header
$rcpt->archive_search($r_args);
?>
<div class="resources-content">
    <?php
    //the loop
    $r_archive = $rcpt->query($r_args);
    if ( $r_archive->have_posts() ) {
        while ($r_archive->have_posts()){ ?>
        <?php $r_archive->the_post(); ?>
            <div class="resources-archive">
                <a href='<?php echo get_permalink()?>'><?php the_post_thumbnail('post-thumbnail', array('class'=>'image', 'title'=>'featured image', 'alt'=>'featured image')); ?></a>
                <div class="content">
                   <a href='<?php echo get_permalink()?>'><?php the_title("<h3 class='post-title'>", "</h3>");?></a>
                   <?php $rcpt->show_meta();?>
                   <?php the_excerpt();?>
                </div>
                
            </div>
            
        <?php } 
        $rcpt->show_archive_pagination($r_archive);
    }
    
    ?>
</div> <!-- resources-content -->
<?php 
wp_reset_postdata(); // reset post data because I am using my own WP_Query
get_footer();
