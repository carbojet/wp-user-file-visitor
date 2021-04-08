<?php
    $users = get_users(
                array(
                    'meta_query' => array(
                        array(
                            'key' => 'cj_visited_label',
                            'value' => false,
                            'compare' => '!='
                        ),
                    )
                )
            );

    
?>
<style>
    td label {
        border: 1px solid #d7d7d7;
        border-radius: 20px;
        margin: 0 2px;
        padding: 0px 8px;
    }
</style>
<div class="wrap">
<table class="wp-list-table widefat fixed striped table-view-list posts" cellspacing="0">
    <thead>
        <tr>
            <th class="manage-column column-title column-primary" scope="col">Medlem</th>
            <th class="manage-column column-title column-primary" scope="col">Avklarade</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($users as $user){
                $cj_visited_label = unserialize( get_user_meta($user->ID,'cj_visited_label',true) );
                $args = array(
                    'post_type'=>'cj-vidpdf',
                    'post__in' => $cj_visited_label,
                );
                $posts = get_posts($args);
                // get the first name of the user as a string
                $user_firstname = get_user_meta( $user->ID, 'first_name', true );

                // get the last name of the user as a string
                $user_lastname = get_user_meta( $user->ID, 'last_name', true );
                ?>
                <tr>
                    <td class="column-primary"><?php echo $user_firstname.' '.$user_lastname;?></td>
                    <td class="column-primary">
                        <?php
                            foreach($posts as $post){
                                $link_title = get_post_meta($post->ID,'cj_video_pdf_name',true);
                                ?>
                                    <label><?php echo $link_title;?></label>
                                <?php 
                            }
                        ?>
                    </td>
                </tr>
                <?php
            }
        ?>
    </tbody>
    <tfoot>

    </tfoot>
</table>
</div>