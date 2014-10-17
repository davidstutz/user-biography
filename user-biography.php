<?php
/**
 * Plugin Name: User Biography
 * Description: Enhanced user biography: users may add multiple "parts" of their biography over time - similar to a timeline.
 * Version: 0.9
 * Author: David Stutz
 * Author URI: http://davidstutz.de
 * License: GPL 2
 */
/**
 * Copyright (C) 2014  David Stutz
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

/**
 * Main class of the pluign.
 * 
 * Mainly used to avoid collisions.
 */
class User_Biography {
    
    /**
     * The "post_type" element of table wp_posts used for biography parts.
     */
    const BIO_TYPE = 'ub_part';
    
    /**
     * Prefix used for textarea name of parts.
     */
    const PART_TEXTAREA_NAME_PREFIX = 'ub_part_';
    
    /**
     * Name for user id field.
     */
    const USER_ID_NAME = 'ub_user_id';
    
    /**
     * Name for new part textarea.
     */
    const ADD_PART_NAME = 'ub_add_part';
    
    /**
     * Name for add part checkbox.
     */
    const ADD_PART_TEXTAREA_NAME = 'ub_add_part_textarea';
    
    /**
     * Name for add part textarea.
     */
    const ADD_PART_CHECKBOX_NAME = 'ub_add_part_checkbox';
    
    /**
     * Registers a new post type named according to User_Biography::BIO_TYPE.
     */
    static public function register_post_type() {
        register_post_type(User_Biography::BIO_TYPE, array(
            'labels' => array(
                'name' => __('Biography Parts', 'user_biography'),
                'singular_name' => __('Biography Part', 'user_biography')
            ),
            'public' => false,
            'has_archive' => false
        ));
    }
    
    /**
     * Hook into user profile to add form elements.
     * 
     * For each bio part, a textarea is added to edit the part. An additional
     * textarea for adding a new part is added as well.
     */
    static public function show_user_profile($user) {
        global $post;
        
        $args = array(
            'post_type' => User_Biography::BIO_TYPE,
            'posts_per_page' => 10
        );
        
        $loop = new WP_Query($args);
        $format = get_option('date_format'); ?>     
        
        <h3><?php echo __('Biography', 'user_biography'); ?></h3>
        <table class="form-table">
            <?php $count = 0; ?>
            <?php while ($loop->have_posts()): ?>
                <?php $loop->the_post(); ?>
                <tr>
                    <th><label><?php echo the_date($format); ?></label></th>
                    <td>
                        <textarea name="<?php echo User_Biography::PART_TEXTAREA_NAME_PREFIX . $post->ID; ?>" rows="5" cols="30"><?php echo $post->post_content; ?></textarea>
                    </td>
                </tr>
                <?php $count++; ?>
            <?php endwhile; ?>
            <?php if ($count <= 0): ?>
                
            <?php endif; ?>
            <tr>
                <th><label for="<?php echo User_Biography::ADD_PART_CHECKBOX_NAME; ?>"><input type="checkbox" id="ub_add_part_checkbox" name="<?php echo User_Biography::ADD_PART_CHECKBOX_NAME; ?>" /> <?php echo __('Add Biography Part', 'user_biography'); ?></label></th>
                <td>
                    <textarea id="ub_add_part_textarea" name="<?php echo User_Biography::ADD_PART_TEXTAREA_NAME; ?>" rows="5" cols="30"></textarea>
                    <br>
                    <span class="description"><?php echo __('What did you do over the last couple of weeks? What are you doing at the moment? - Share some more biographical information to appear in your timeline.', 'user_biography'); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Add AJAX JS code to footer.
     */
    static public function update_biography($user_id) {  
        $user = get_user_by('id', $user_id);
            
        $keys = array_keys($_POST);
        $part_names = preg_grep('#' . User_Biography::PART_TEXTAREA_NAME_PREFIX . '[0-9]+#', $keys);
        
        foreach ($part_names as $name) {

            $id = intval(str_replace(User_Biography::PART_TEXTAREA_NAME_PREFIX, '', $name));
            $part = get_post($id);

            $response[$id] = FALSE;
            if ($part !== NULL) {
                $part->post_content = sanitize_text_field($_POST[$name]);
                wp_update_post($part);
            }
        }

        if (isset($_POST[User_Biography::ADD_PART_CHECKBOX_NAME])) {

            $new_part_content = sanitize_text_field($_POST[User_Biography::ADD_PART_TEXTAREA_NAME]);
            
            $gmdate = gmdate('Y-m-d H:i:s');
            $date = date('Y-m-d H:i:s');

            if (!empty($new_part_content)) {
                
                // Full post object specification from the documentation:
                $part = array(
                    // 'ID'
                    'post_content'   => $new_part_content,
                    'post_name'      => $user->user_login . '-' . substr($gmdate, 0, 5),
                    // 'post_title'
                    'post_status'    => 'publish', // Default 'draft'.
                    'post_type'      => User_Biography::BIO_TYPE,
                    'post_author'    => $user->ID,
                    'ping_status'    => 'closed', // 'closed' or 'open'.
                    // 'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
                    // 'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
                    // 'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
                    // 'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
                    // 'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
                    // 'guid'           => // Skip this and let Wordpress handle it, usually.
                    // 'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
                    // 'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
                    'post_date'      => $date,
                    'post_date_gmt'  => $gmdate,
                    'comment_status' => 'closed', // 'closed' or 'open'.
                    // 'post_category'  => [ array(<category id>, ...) ] // Default empty.
                    // 'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
                    // 'tax_input'      => [ array( <taxonomy> => <array | string> ) ] // For custom taxonomies. Default empty.
                    // 'page_template'  => [ <string> ] // Requires name of template file, eg template.php. Default empty.
                );

                wp_insert_post($part);
            }
        }
    }
}

add_action('init', 'User_Biography::register_post_type');
add_action('show_user_profile', 'User_Biography::show_user_profile');
add_action('edit_user_profile_update', 'User_Biography::update_biography');
add_action('personal_options_update', 'User_Biography::update_biography');