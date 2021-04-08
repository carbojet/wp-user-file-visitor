<?php
/**
* plugin name: Carbojet File Visitor
* Author: Sunil kumar Mutaka
* Description: This plugin is used to count visitor on created video and pdf ids .
*/

add_action('init','wp_custom_post_cj_video_pdf');
function wp_custom_post_cj_video_pdf(){
	
	register_post_type('cj-vidpdf',array(
        'labels'		=> array(
                                'name'				=>	'Deltagarportal',
                                'singular_name'		=>	'Deltagarportal',
                                'add_new'			=>	'Ladda upp',
                                'add_new_item'		=>	'Ladda upp Deltagarportal',
                                'edit'				=>	'Edit',
                                'edit_item'			=>	'Edit Deltagarportal',
                                'view'				=>	'View',
                                'view_items'		=>	'View Deltagarportal',
                                'search_items'		=>	'Search Deltagarportal',
                                'not_found'			=>	'No Deltagarportal Found',
                                'not_found_in_trash'=>	'No Deltagarportal Found in Trash',
                                'parent'			=>	'Parent Deltagarportal'
                                ),
        'public'		=> true,
        'menu_icon'		=> 'dashicons-editor-kitchensink',
        'has_archive'	=> false,
        'rewrite' 		=> array('slug' => 'cj-vidpdf'),
        'supports'		=> array('title')
        ));
	remove_post_type_support('cj-vidpdf','editor');
}
add_action('save_post','cj_save_vidpdf');
function cj_save_vidpdf($post){
    global $post;
    update_post_meta($post->ID,'cj_video_pdf_name',$_REQUEST['cj_video_pdf_name']);
}

function wp_cj_vidpft_custom_meta(){
    $cj_video_pdf_name = get_post_meta(get_the_id(),'cj_video_pdf_name',true);
    ?>
    <table>
        <tr><td><label>Namn på Video eller PDF:</label></td></tr>
        <tr><td><input type="text" name="cj_video_pdf_name" value="<?php echo $cj_video_pdf_name;?>" /></td></tr>
    </table>
    <?php
}
add_action('admin_init','wp_cj_vidpdf_meta_boxes');
function wp_cj_vidpdf_meta_boxes(){
    add_meta_box('wp_cj_vidpft_custom_meta_box','Filnamn','wp_cj_vidpft_custom_meta','cj-vidpdf','normal','high');
    wp_enqueue_script('jquery');
    wp_enqueue_script('wp-cj-admin-script-js',plugins_url('assets/js/javascript.js',__FILE__));    
}
add_action('admin_menu', 'wp_cj_add_submenu');
function wp_cj_add_submenu(){

	add_submenu_page(
        'edit.php?post_type=cj-vidpdf',
        'Medlemmar',
        'Medlemmar',
        'manage_options',
        'wp-cj-get-visted-users',
		'get_visted_users');
}
add_action('wp_head','wp_cj_frontend_script');
function wp_cj_frontend_script(){

    $args = array(
        'post_type'=>'cj-vidpdf',
        'order'=>'ASC',
        'posts_per_page' => -1,
    );
    $cj_vidpdf_ids =[];
    $result = get_posts( $args );
    foreach($result as $cjpost){
        $cj_vidpdf_ids[$cjpost->ID] = array("id"=>$cjpost->ID,"title"=>$cjpost->post_title);
    }

    wp_enqueue_script('jquery');
    wp_enqueue_style('wp-cj-frontend-stylesheet',plugins_url('assets/css/stylesheet.css',__FILE__));
    wp_enqueue_script('wp-cj-frontend-script-js',plugins_url('assets/js/front-end-javascript.js',__FILE__));
    wp_localize_script('wp-cj-frontend-script-js','adminlocaljs',array(
                                                'ajaxUrl'=>admin_url('admin-ajax.php'),
                                                'ids'=>$cj_vidpdf_ids
												));
                                                
    
}
function get_visted_users(){
    require("templates/admin/visited-users.php");
}
add_action( 'wp_ajax_cj_add_visit_label', 'cj_add_visit_label' );
add_action( 'wp_ajax_nopriv_cj_add_visit_label', 'cj_add_visit_label' ); 
function cj_add_visit_label(){
    $postid = $_POST["postid"];
    $cj_visited_label = get_user_meta(get_current_user_id(),'cj_visited_label',true);
    if($cj_visited_label){
        $cj_visited_label = unserialize($cj_visited_label);
        if(!is_array($postid,$cj_visited_label)){
            array_push($cj_visited_label,$postid);
        }        
    }else{
        $cj_visited_label = array($postid);
    }
    update_user_meta(get_current_user_id(),'cj_visited_label',serialize($cj_visited_label));

    wp_send_json_success(true);
}

function wp_cj_custom_profile_fields($user){
    ?>
    <table class="form-table">
        <tr>
            <th>CJ Register Custom Fields</th><td></td>
        </tr>
        <tr>
            <th>
                <label for="code"><?php _e( 'Address' ); ?></label>
            </th>
            <td>
                <input type="text" name="user_address" value="<?php echo esc_attr( get_user_meta($user->ID, 'user_address', true ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="code"><?php _e( 'Zip Code' ); ?></label>
            </th>
            <td>
                <input type="text" name="user_zipcode" value="<?php echo esc_attr( get_user_meta( $user->ID,'user_zipcode',true ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="code"><?php _e( 'Phone' ); ?></label>
            </th>
            <td>
                <input type="text" name="user_phone" value="<?php echo esc_attr( get_user_meta($user->ID, 'user_phone',true  ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="code"><?php _e( 'Reference No' ); ?></label>
            </th>
            <td>
                <input type="text" name="user_reference" value="<?php echo esc_attr( get_user_meta( $user->ID,'user_reference',true ) ); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'wp_cj_custom_profile_fields');
add_action('edit_user_profile', 'wp_cj_custom_profile_fields');

function cj_new_modify_user_table( $column ) {
    $column['reference'] = 'Reference';
    $column['date'] = 'Date'; 
    return $column;
}
add_filter( 'manage_users_columns', 'cj_new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    $user = get_user_by('id',$user_id);
    switch ($column_name) {
        case 'reference' :
            return get_the_author_meta( 'user_reference', $user_id );
        case 'date' :
            return  $user->user_registered;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );

function wp_cj_register_form(){
    $error_msg =false;
    if(isset($_REQUEST['user_register'])){
        global $wp_roles;

        $all_roles = $wp_roles->roles;
        //var_dump($all_roles);
        $error_msg = '';
        $data = $_REQUEST;
        unset($data['user_register']);
        unset($data['nocache_login']);
        $form_validation = true;
        $formdata = array();
        foreach($data as $k=>$value){
            if($value==''){
                $form_validation = false;
                $error_msg .= '<p>'.$k.' is required</p>';
            }else{
                $formdata[$k] = sanitize_text_field( $value );
            }
        }
        if (!filter_var($formdata['user_email'], FILTER_VALIDATE_EMAIL)) {
            $error_msg .= '<p>Email is not valid</p>';
        }
        if($_REQUEST['user_pass']!=$_REQUEST['user_reenter_pass']){
            $error_msg .= '<p>Password Mis Match</p>';
        }

        if($form_validation){

            $userdata = array(
                'user_pass'         => wp_hash_password( $formdata['user_pass'] ),
                'user_login'        => explode('@',$formdata['user_email'])[0],
                'user_email'        => $formdata['user_email'],
                'display_name'      => $formdata['user_first_name'],
                'first_name'        => $formdata['user_first_name'],
                'last_name'         => $formdata['user_last_name'],
                'user_registered'   => date('Y-m-d H:i:s'),
                'role'              => 'custom_editor',
            );
            
            $user_id = wp_insert_user($userdata);
            add_user_meta($user_id,'user_address', $formdata['user_address'],true);
            add_user_meta($user_id,'user_zipcode', $formdata['user_zipcode'],true);
            add_user_meta($user_id,'user_reference', $formdata['user_reference'],true);
            add_user_meta($user_id,'user_phone', $formdata['user_phone'],true);
            
            $error_msg .='<p>New User Created...</p>';
            $formdata = array(
                "user_first_name"=>'',
                "user_last_name"=>'',
                "user_address"=>'',
                "user_zipcode"=>'',
                "user_phone"=>'',
                "user_email"=>'',
                "user_pass"=>'',
                "user_reenter_pass"=>'',
                "user_reference"=>'',
            );
        }else{
            $error_msg .='<p>All Fields are required...</p>';
        }
    }else{
        $formdata = array(
            "user_first_name"=>'',
            "user_last_name"=>'',
            "user_address"=>'',
            "user_zipcode"=>'',
            "user_phone"=>'',
            "user_email"=>'',
            "user_pass"=>'',
            "user_reenter_pass"=>'',
            "user_reference"=>'',
        );
    }
    if ( is_user_logged_in() ) {
        /*?>
            <div class="wp-cj-register-container">
            <div class="msg">
            <?php $user = wp_get_current_user();?>
                <p>Hi <?php echo $user->nickname;?></p>
            </div>
        <?php 
        */
    }else{
    ?>
        <div class="wp-cj-register-container">
            <div class="msg">
                <?php if($error_msg){echo $error_msg;}?>
            </div>
            <form method="post" action="" class="cleanlogin-form">
                <fieldset>
                    <div class="elementor-row">
                        <div class="elementor-column elementor-col-50">
                            <div class="cleanlogin-field">
                                <input class="cleanlogin-field-username" type="text" value="<?php echo $formdata['user_first_name'];?>" name="user_first_name" placeholder="Förnamn *">
                            </div>
                        </div>
                        <div class="elementor-column elementor-col-50">
                            <div class="cleanlogin-field">
                                <input class="cleanlogin-field-username" type="text" value="<?php echo $formdata['user_last_name'];?>" name="user_last_name" placeholder="Efternamn *">
                            </div>
                        </div>
                    </div>
                    <div class="elementor-row">
                        <div class="elementor-column elementor-col-50">
                            <div class="cleanlogin-field">
                                <input class="cleanlogin-field-username" type="text" value="<?php echo $formdata['user_address'];?>" name="user_address" placeholder="Adress *">
                            </div>
                        </div>
                        <div class="elementor-column elementor-col-50">
                            <div class="cleanlogin-field">
                                <input class="cleanlogin-field-username" type="text" value="<?php echo $formdata['user_zipcode'];?>" name="user_zipcode" placeholder="Postnummer *">
                            </div>
                        </div>
                    </div>
                    <div class="elementor-row">
                        <div class="elementor-column elementor-col-50">
                            <div class="cleanlogin-field">
                                <input class="cleanlogin-field-username" type="text" value="<?php echo $formdata['user_phone'];?>" name="user_phone" placeholder="Telefonnummer *">
                            </div>
                        </div>
                        <div class="elementor-column elementor-col-50">
                            <div class="cleanlogin-field">
                                <input class="cleanlogin-field-username" type="text" value="<?php echo $formdata['user_email'];?>" name="user_email" placeholder="Email *">
                            </div>
                        </div>
                    </div>
                    <div class="elementor-row">
                        <div class="elementor-column elementor-col-50">
                            <div class="cleanlogin-field">
                                <input class="cleanlogin-field-username" type="password" value="<?php echo $formdata['user_pass'];?>" name="user_pass" placeholder="Lösenord">
                            </div>
                        </div>
                        <div class="elementor-column elementor-col-50">
                            <div class="cleanlogin-field">
                                <input class="cleanlogin-field-username" type="password" value="<?php echo $formdata['user_reenter_pass'];?>" name="user_reenter_pass" placeholder="Bekräfta Lösenord">
                            </div>
                        </div>
                    </div>

                    <div class="cleanlogin-field">
                        <input class="cleanlogin-field-username" type="text" value="<?php echo $formdata['user_reference'];?>" name="user_reference" placeholder="Ärendenummer *">
                    </div>
                </fieldset>
                <fieldset>
                    <input class="cleanlogin-field" type="submit" value="Register" name="user_register">
                </fieldset>
            </form>
        </div>
    <?php
    }
}
add_shortcode('wp-cj-register','wp_cj_register_form');

add_action( 'user_register', function($user_id){
    $user = get_user_by('id',$user_id);
    $admin_mail = get_option('admin_email');
    $user_mail = $user->user_email;

    
}, 10, 1 );