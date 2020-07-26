<?php
/*
    This creates the 'resources' custom post type.
*/

class ResourcesCPT{
    
    /*===================properties===================*/
    public $meta_boxes = array(); //need this for the save post action
    private $svg; //for icons specific to the resources cpt
    private $textdomain; //pertty sure I am not supposed to use it this way for localization
        
    /*===================Constructor===================*/
    function __construct(){
        $this->svg = new resourceIcons();
        $this->get_textdomain();
        $this->declare_custom_metaboxes(); //need to declare the metaboxes I am going to use right away for the save_post action

        add_action( 'init', array($this, 'register_resources_post_type'));
        add_action('init', array($this, 'register_custom_taxonomies'));
        add_action('add_meta_boxes', array($this, 'register_custom_metaboxes'));
        add_action('save_post', array($this, 'save_custom_meta_data' ));
        add_action('wp_enqueue_scripts', array($this, 'custom_styles'));

        add_filter('template_include', array($this, 'custom_page_templates'));

        
    }
        

    /*===================Public Functions===================*/
    public function register_resources_post_type(){
        //copied the labels array from the wordpress codex.  didn't want to type all these out by hand

        $labels = array(
            'name'                  => _x( 'Resources', 'Post type general name', $this->textdomain ),
            'singular_name'         => _x( 'Resource', 'Post type singular name', $this->textdomain ),
            'menu_name'             => _x( 'Resources', 'Admin Menu text', $this->textdomain ),
            'name_admin_bar'        => _x( 'Resource', 'Add New on Toolbar', $this->textdomain ),
            'add_new'               => __( 'Add New', $this->textdomain ),
            'add_new_item'          => __( 'Add New Resource', $this->textdomain ),
            'new_item'              => __( 'New Resource', $this->textdomain ),
            'edit_item'             => __( 'Edit Resource', $this->textdomain ),
            'view_item'             => __( 'View Resource', $this->textdomain ),
            'all_items'             => __( 'All Resources', $this->textdomain ),
            'search_items'          => __( 'Search Resources', $this->textdomain ),
            'parent_item_colon'     => __( 'Parent Resources:', $this->textdomain ),
            'not_found'             => __( 'No Resources found.', $this->textdomain ),
            'not_found_in_trash'    => __( 'No Resources found in Trash.', $this->textdomain ),
            'featured_image'        => _x( 'Resource Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', $this->textdomain ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', $this->textdomain ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', $this->textdomain ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', $this->textdomain ),
            'archives'              => _x( 'Resource archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', $this->textdomain ),
            'insert_into_item'      => _x( 'Insert into Resource', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', $this->textdomain ),
            'uploaded_to_this_item' => _x( 'Uploaded to this Resource', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', $this->textdomain ),
            'filter_items_list'     => _x( 'Filter Resources list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', $this->textdomain ),
            'items_list_navigation' => _x( 'Resources list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', $this->textdomain ),
            'items_list'            => _x( 'Resources list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', $this->textdomain ),
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Resources for your users to review.', $this->textdomain ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'resources' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
            'taxonomies'         => array('Topic', 'category', 'Audience'),
        );

        register_post_type( 'resources', $args );
    }

    public function register_custom_taxonomies(){
        //registers custom taxonomies.  Put the names in the array.
        $taxonomy = array(
            'Topic', 'Audience',
        );

        foreach($taxonomy as $t){
            $labels = array(
                'name' => $t,
                'singular_name' => $t,
                'search_items' => "Search " . $t . 's',
                'popular_items' => 'Popular ' . $t . 's',
                'all_items' => 'All ' . $t . 's',
                'parent_item' => 'Parent ' . $t . 's',
                'edit_item' => 'Edit' . $t,
                'view_item' => 'View ' . $t, 
                'update_item' => 'Update ' . $t,
                'add_new_item' => "Add New " . $t,
                'new_item_name' => 'New ' . $t . ' Name',
                'separate_with_commas' => 'Separate ' . strtolower($t) . 's with commas',
                'add_or_remove_items' => 'Add or Remove' . strtolower($t) . 's',
                'choose_from_most_used' => 'Choose from the most used ' . strtolower($t) . 's, in the metabox',
                'not_found' => 'No ' . $t . 's Found',
                'no_terms' => 'No ' . $t . 's',

            );
            register_taxonomy($t, 'resources', array(
                'labels' => $labels,
                'public' => true,
                'show_in_rest' => true,
                'rewrite' => true,
                'slug' => strtolower($t),

            ));
        }
    }

    public function register_custom_metaboxes(){
        //registers the custom meta boxes for the resource custom post type.  
        foreach($this->meta_boxes as $mb){
            extract($mb);
            add_meta_box($id, $title, $callback, NULL, 'side', 'high', $mb);
        }

            
        
    }

    public function meta_box_callback($post, $args){
        //callback function for metaboxes with nonce.  only works for text, email, and url type metaboxes.
        extract($args['args']);

        wp_nonce_field('save_custom_meta_data', $nonce);
        
        $value = get_post_meta($post->ID, $key, true);

        echo "<label for='$field'>$label</label>";
        echo "<input type='$type' id='$field' name='$field' value='" . esc_attr($value) . "' size='25'>";

    }

    public function save_custom_meta_data($post_id){
        //performs security checks and saves the custom meta data
        foreach($this->meta_boxes as $mb){
            $nonce = $mb['nonce'];
            $field = $mb['field'];
            $key = $mb['key'];

            if($_POST[$field]){
                if(! isset($_POST[$nonce])){return;}
                if(! wp_verify_nonce($_POST[$nonce], 'save_custom_meta_data')){return;}
                if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return;}
                if(! current_user_can('edit_post', $post_id)){return;}
                if(! isset($_POST[$field])){return;}

                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, $key, $value);
            }            
        }

    }

    public function custom_page_templates($template){
        global $post;
        //custom resources single
        if($post->post_type == 'resources'){
            $template = get_stylesheet_directory(). '/resources/resources-details-template.php';

            //custom archive page
            if(is_archive()){
                $template = get_stylesheet_directory(). '/resources/resources-list-template.php';
            }
            //return $template;           
        }

        return $template;
    }

    public function custom_styles(){
        wp_enqueue_style('resources-stylesheet', get_template_directory_uri() . '/resources/resources-css.css', false);
    }

    /*===================Private Functions===================*/
    private function get_textdomain(){
        $theme = wp_get_theme();
        return $theme->get("TextDomain");
    }
    
    private function declare_custom_metaboxes(){
        //declaring the metaboxes to be created later.
        $metaBoxes = array(
            'Download URL',
        );

        foreach($metaBoxes as $m){
            $id = strtolower(str_replace(' ', '_', $m));
            $temp = array(
                'id'        => $id,
                'title'     => $m,
                'callback'  => array($this, 'meta_box_callback'),
                'action'    => 'resource_cpt_save_' . $id . '_data',
                'nonce'      => 'resource_cpt_save_' . $id . '_nonce',
                'key'       => '_resource_cpt_' . $id . '_key',
                'field'     => 'resource_cpt_' . $id . '_field',
                'label'     => $m .": ",
                'type'      =>'url', //this should be text, email, or url for html5 validation reasons
                
            );

            $this->meta_boxes[$id] = $temp;
        }
    }
 
    /*===================single.php functions===================*/
    public function show_meta(){
        //this is a collection of the show author, show date, show topic, and show audience functions
        //use this for a single call rather than calling all of them

        $meta = "<div class='post-meta'>";
        $meta .= $this->show_author(true);
        $meta .= $this->show_date(true);
        $meta .= $this->show_topic(true);
        $meta .= $this->show_audience(true);
        $meta .= "</div>";

        echo $meta;
    }

    public function show_author($meta=false){
        //shows the author for the post this funciton is in
        $author = get_the_author();
        $author == '' ? $author = "admin" : '';
        $icon = $this->svg->get_svg('ui', 'person', 18);
        $reader = __('Author', $this->textdomain);

        $txt = "<span>$icon<span><span class='screen-reader-text'>$reader</span>$author</span></span>";

        if($meta=== true){
            return $txt;
        }else{
            echo $txt;
        }
    }

    public function show_date($meta=false){
        $date = get_the_date();
        $icon = $this->svg->get_svg('ui', 'watch', 18);
        $reader = __('Date', $this->textdomain);
        $txt = "<span>$icon<span><span class='screen-reader-text'>$reader</span>$date</span></span>";

        if($meta=== true){
            return $txt;
        }else{
            echo $txt;
        } 
    }

    public function show_topic($meta=false){
        //shows the topic tag for the post this function is in.
        $terms = get_the_terms($post->ID, 'Topic');
        $temp = array();
        foreach($terms as $t){
            $temp[]=$t->name;
        }
        $topics = implode(", ", $temp);
        $icon = $this->svg->get_svg('ui', 'topic', 18);
        $reader = __('Topic', $this->textdomain);
        
        $txt = "<span >$icon<span ><span class='screen-reader-text'>$reader</span>$topics</span></span>";

        if($meta === true){
            return $txt;
        }else{
            echo $txt;
        }
        
    }

    public function show_audience($meta=false){
        //shows the audience tag for the post this is function is in
        $terms = get_the_terms($post->ID, 'Audience');
        $temp = array();
        foreach($terms as $t){
            $temp[]=$t->name;
        }
        $audience = implode(", ", $temp);
        $icon = $this->svg->get_svg( 'ui', 'audience', 18 );
        $reader = __('Audience', $this->textdomain);

        $txt = "<span>$icon<span><span class='screen-reader-text'>$reader</span>$audience</span></span>";

        if($meta === true){
            return $txt;
        }else{
            echo $txt;
        }
        
    }

    public function show_download_link($id){
        //displays the download icon and link for the post this function is in.
        $key = $this->meta_boxes['download_url']['key'];
        $meta = get_post_meta($id, $key);
        $link = sanitize_text_field($meta[0]);
        $icon = $this->svg->get_svg('ui', 'download', 24);
        $title = get_the_title($id);
        $reader = __('Download', $this->textdomain);

        $txt = "<span class='resource-download'>$icon<span><span class='screen-reader-text'>$reader</span><a href='$link' target='_blank'> Click here to down load the resources for $title</a> </span></span>";

        echo $txt;
        
    }

    public function show_archive_link(){
        // echoes the archive page link
        $link = get_site_url(NULL, 'resources');
        $icon = $this->svg->get_svg('ui', 'chevron_left', 24);
        $reader = __( 'Back', $this->textdomain );
        $message = __( 'Return To Resources Archive', $this->textdomain );
        
        $txt = "<span class=''><a href='$link' style='font-size:smaller;display:flex;align-items:center'>$icon<span class='screen-reader-text'>$reader</span><span class='author vcard'>$message</span></a></span>";
        echo $txt;
        
    }
    /*===================archive.php functions===================*/
    public function query($terms){
        extract($terms);
        
        //custom query for the archive page
        $args = array(
            'post_type' =>'resources',
            'posts_per_page' => 10,
            's' => $search,
            'paged' => get_query_var('paged', 1),
        );

        if($topic || $audience){
            $tax_query = array(
                'relationship' => 'AND',
            );
            if($topic){
                $tax_query[] = array(
                    'taxonomy' => 'Topic',
                    'field' => 'name',
                    'terms' => array($topic),
                );
            }
            if($audience){
                $tax_query[] = array(
                    'taxonomy' => 'Audience',
                    'field' => 'name',
                    'terms' => array($audience),
                );
            }

            $args['tax_query'] = $tax_query;
        }
        
        return new WP_Query($args);
    }

    public function archive_search($args){
        //takes in an array of GET variables and creates the search section for the archive page.

        $args1 = array(
            'topic' => NULL,
            'audience' => NULL,
            'search' => NULL,
        );
        $args1 = array_merge($args1, $args);
        
        extract($args1);

        $form = "<form method='GET' class='page-header' style='display:flex; justify-content:space-around;align-items:center;max-height:2rem;'>";
        $form .= $this->select_topics($topic);
        $form .= $this->select_audience($audience);
        $form .= $this->search_terms($search);
        $form .= '<input type="submit" value="Search">';
        $form .= "</form>";
        echo $form;
    }

    private function select_topics($selected){
        //returns a topic select for the archive_search function

        $terms =  get_terms('Topic');
        $response = "<label for='topic'>Choose a topic: </label><select name='topic' id='topic'><option value='' ></option>";
        foreach($terms as $t){
            if($selected == $t->name){$sel = 'selected';}else{$sel='';}
            $response .= '<option value='. $t->name .' '. $sel .'>' . $t->name .'</option>';
        }
        
        $response .= '</select>';
        return $response ;
    }

    private function select_audience($selected){
        //returns an audience select for the archive_search function

        $audience = get_terms('Audience');
        $response = "<label for='audience'>Choose an audience: </label><select name='audience' id='audience'><option value='' ></option>";
        foreach($audience as $a){
            if($selected == $a->name){$sel = 'selected';}else{$sel='';}
            $response .= '<option value='. $a->name .' '. $sel .'>' . $a->name .'</option>';
        }
        
        $response .= '</select>';
        return ($response);
    }

    private function search_terms($terms){
        //returns a search bar for the archive_search function
        if($terms === NULL){$terms = "";}
        $search = '<label for="search">Search: </label><input type="text" name="search" id="search" value="'. $terms .'" style="height:inherit">';
        return $search;
    }

    public function show_archive_pagination($query){
        //shows pagination links where this funciton is called.
        //copied from wordpress codex.   never used this one for pagination before.  
        //regular next_posts_link() wasn't working because of custom wp_query for archive page.

        $big = 9999999999;
        echo paginate_links( array(
            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'total' => $query->max_num_pages
        ) );
    }

}

class resourceIcons {
    //this class is largely copied from the TwentyNineteen_SVG_Icons class that comes with the twentynineteen theme.  I wanted to be
    //able to call the svg icons the same way the original theme does.  However I do not want any depencies for this class to work
    //so I made it an class to itself with only the icons my files need.

    private $ui_icons = array(
        //add the audience icon
        'audience' => /* material-design – people */ 
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="18px" height="18px"><path d="M0 0h24v24H0z" fill="none"/>
        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>',

        //add the topic icon
        'topic' => /* material-design - pyschology */
        '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="white" width="18px" height="18px"><g><rect fill="none" height="24" width="24"/></g><g><g>
        <path d="M13,8.57c-0.79,0-1.43,0.64-1.43,1.43s0.64,1.43,1.43,1.43s1.43-0.64,1.43-1.43S13.79,8.57,13,8.57z"/><path d="M13,3C9.25,3,6.2,5.94,6.02,9.64L4.1,12.2C3.85,12.53,4.09,13,4.5,13H6v3c0,1.1,0.9,2,2,2h1v3h7v-4.68 c2.36-1.12,4-3.53,4-6.32C20,6.13,16.87,3,13,3z M16,10c0,0.13-0.01,0.26-0.02,0.39l0.83,0.66c0.08,0.06,0.1,0.16,0.05,0.25 l-0.8,1.39c-0.05,0.09-0.16,0.12-0.24,0.09l-0.99-0.4c-0.21,0.16-0.43,0.29-0.67,0.39L14,13.83c-0.01,0.1-0.1,0.17-0.2,0.17h-1.6 c-0.1,0-0.18-0.07-0.2-0.17l-0.15-1.06c-0.25-0.1-0.47-0.23-0.68-0.39l-0.99,0.4c-0.09,0.03-0.2,0-0.25-0.09l-0.8-1.39 c-0.05-0.08-0.03-0.19,0.05-0.25l0.84-0.66C10.01,10.26,10,10.13,10,10c0-0.13,0.02-0.27,0.04-0.39L9.19,8.95 c-0.08-0.06-0.1-0.16-0.05-0.26l0.8-1.38c0.05-0.09,0.15-0.12,0.24-0.09l1,0.4c0.2-0.15,0.43-0.29,0.67-0.39l0.15-1.06 C12.02,6.07,12.1,6,12.2,6h1.6c0.1,0,0.18,0.07,0.2,0.17l0.15,1.06c0.24,0.1,0.46,0.23,0.67,0.39l1-0.4c0.09-0.03,0.2,0,0.24,0.09 l0.8,1.38c0.05,0.09,0.03,0.2-0.05,0.26l-0.85,0.66C15.99,9.73,16,9.86,16,10z"/></g></g></svg>',

        //add the download icon
        'download' => /* material-design - text_snippet */
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" width="18px" height="18px"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>',

        //add the person icon
        'person' => /* material-design – person */ '
        <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
            <path d="M0 0h24v24H0z" fill="none"></path>
        </svg>',

        //add the time icon
        'watch' => /* material-design – watch-later */ '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <defs>
                <path id="a" d="M0 0h24v24H0V0z"></path>
            </defs>
            <clipPath id="b">
                <use xlink:href="#a" overflow="visible"></use>
            </clipPath>
            <path clip-path="url(#b)" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7-.8 1.3z"></path>
        </svg>',

        //add the chevron left icon
        'chevron_left' => /* material-design – chevron_left */ '
        <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
            <path d="M0 0h24v24H0z" fill="none"></path>
        </svg>',
    );

    /**
	 * Gets the SVG code for a given icon.
	 */
	public function get_svg( $group, $icon, $size ) {
        //this was copied directly from the twentynineteen theme files and changed from a static function to a member function.
		if ( 'ui' == $group ) {
			$arr = $this->ui_icons;
		} elseif ( 'social' == $group ) {
			$arr = $this->social_icons;
		} else {
			$arr = array();
		}
		if ( array_key_exists( $icon, $arr ) ) {
			$repl = sprintf( '<svg class="svg-icon" width="%d" height="%d" aria-hidden="true" role="img" focusable="false" ', $size, $size );
			$svg  = preg_replace( '/^<svg /', $repl, trim( $arr[ $icon ] ) ); // Add extra attributes to SVG code.
			$svg  = preg_replace( "/([\n\t]+)/", ' ', $svg ); // Remove newlines & tabs.
			$svg  = preg_replace( '/>\s*</', '><', $svg );    // Remove whitespace between SVG tags.
			return $svg;
		}
		return null;
	}

    private function add_svg_icons(){
        //add the audience icon
        parent::$ui_icons['audience'] = /* material-design – people */ 
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="18px" height="18px"><path d="M0 0h24v24H0z" fill="none"/>
        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>';

        //add the topic icon
        parent::$ui_icons['topic'] = /* material-design - pyschology */
        '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="white" width="18px" height="18px"><g><rect fill="none" height="24" width="24"/></g><g><g>
        <path d="M13,8.57c-0.79,0-1.43,0.64-1.43,1.43s0.64,1.43,1.43,1.43s1.43-0.64,1.43-1.43S13.79,8.57,13,8.57z"/><path d="M13,3C9.25,3,6.2,5.94,6.02,9.64L4.1,12.2C3.85,12.53,4.09,13,4.5,13H6v3c0,1.1,0.9,2,2,2h1v3h7v-4.68 c2.36-1.12,4-3.53,4-6.32C20,6.13,16.87,3,13,3z M16,10c0,0.13-0.01,0.26-0.02,0.39l0.83,0.66c0.08,0.06,0.1,0.16,0.05,0.25 l-0.8,1.39c-0.05,0.09-0.16,0.12-0.24,0.09l-0.99-0.4c-0.21,0.16-0.43,0.29-0.67,0.39L14,13.83c-0.01,0.1-0.1,0.17-0.2,0.17h-1.6 c-0.1,0-0.18-0.07-0.2-0.17l-0.15-1.06c-0.25-0.1-0.47-0.23-0.68-0.39l-0.99,0.4c-0.09,0.03-0.2,0-0.25-0.09l-0.8-1.39 c-0.05-0.08-0.03-0.19,0.05-0.25l0.84-0.66C10.01,10.26,10,10.13,10,10c0-0.13,0.02-0.27,0.04-0.39L9.19,8.95 c-0.08-0.06-0.1-0.16-0.05-0.26l0.8-1.38c0.05-0.09,0.15-0.12,0.24-0.09l1,0.4c0.2-0.15,0.43-0.29,0.67-0.39l0.15-1.06 C12.02,6.07,12.1,6,12.2,6h1.6c0.1,0,0.18,0.07,0.2,0.17l0.15,1.06c0.24,0.1,0.46,0.23,0.67,0.39l1-0.4c0.09-0.03,0.2,0,0.24,0.09 l0.8,1.38c0.05,0.09,0.03,0.2-0.05,0.26l-0.85,0.66C15.99,9.73,16,9.86,16,10z"/></g></g></svg>';

        //add the download icon
        parent::$ui_icons['download'] = /* material-design - text_snippet */
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" width="18px" height="18px"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>';
    }
    
}

