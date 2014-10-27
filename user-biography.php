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
     * Prefix used for textarea name.
     */
    const PART_TEXTAREA_NAME_PREFIX = 'ub_part_';
    
    /**
     * Prefix used for month select name.
     */
    const PART_MONTH_NAME_PREFIX = 'ub_month_';
    
    /**
     * Prefix used for day input name.
     */
    const PART_DAY_NAME_PREFIX = 'ub_day_';
    
    /**
     * Prefix used for year input name.
     */
    const PART_YEAR_NAME_PREFIX = 'up_year_';
    
    /**
     * Prefix used for delete part checkbox.
     */
    const PART_DELETE_NAME_PREFIX = 'ub_delete_';
    
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
            <tr>
                <th><label for="<?php echo User_Biography::ADD_PART_CHECKBOX_NAME; ?>"><input type="checkbox" id="ub_add_part_checkbox" name="<?php echo User_Biography::ADD_PART_CHECKBOX_NAME; ?>" /> <?php echo __('Add Biography Part', 'user_biography'); ?></label></th>
                <td>
                    <textarea id="ub_add_part_textarea" name="<?php echo User_Biography::ADD_PART_TEXTAREA_NAME; ?>" rows="5" cols="30"></textarea>
                    <br>
                    <span class="description">
                        <?php echo __('What did you do over the last couple of weeks? What are you doing at the moment?', 'user_biography'); ?><br>
                        <?php echo __('&mdash; Share some more biographical information to appear in your timeline.', 'user_biography'); ?>
                    </span>
                </td>
            </tr>
            <?php $count = 0; ?>
            <?php while ($loop->have_posts()): ?>
                <?php $loop->the_post(); ?>
                <tr>
                    <th>
                        <select name="<?php echo User_Biography::PART_MONTH_NAME_PREFIX . $post->ID; ?>">
                            <?php $month = get_the_date('n'); ?>
                            <option value="01" <?php if ($month == 1): ?>selected<?php endif; ?>><?php echo __('Jan', 'user_biography'); ?>-1</option>
                            <option value="02" <?php if ($month == 2): ?>selected<?php endif; ?>><?php echo __('Feb', 'user_biography'); ?>-2</option>
                            <option value="03" <?php if ($month == 3): ?>selected<?php endif; ?>><?php echo __('Mar', 'user_biography'); ?>-3</option>
                            <option value="04" <?php if ($month == 4): ?>selected<?php endif; ?>><?php echo __('Apr', 'user_biography'); ?>-4</option>
                            <option value="05" <?php if ($month == 5): ?>selected<?php endif; ?>><?php echo __('May', 'user_biography'); ?>-5</option>
                            <option value="06" <?php if ($month == 6): ?>selected<?php endif; ?>><?php echo __('Jun', 'user_biography'); ?>-6</option>
                            <option value="07" <?php if ($month == 7): ?>selected<?php endif; ?>><?php echo __('Jul', 'user_biography'); ?>-7</option>
                            <option value="08" <?php if ($month == 8): ?>selected<?php endif; ?>><?php echo __('Aug', 'user_biography'); ?>-8</option>
                            <option value="09" <?php if ($month == 9): ?>selected<?php endif; ?>><?php echo __('Sep', 'user_biography'); ?>-9</option>
                            <option value="10" <?php if ($month == 10): ?>selected<?php endif; ?>><?php echo __('Oct', 'user_biography'); ?>-10</option>
                            <option value="11" <?php if ($month == 11): ?>selected<?php endif; ?>><?php echo __('Nov', 'user_biography'); ?>-11</option>
                            <option value="12" <?php if ($month == 12): ?>selected<?php endif; ?>><?php echo __('Dec', 'user_biography'); ?>-12</option>
                        </select>
                        <input type="text" style="width:36px;" name="<?php echo User_Biography::PART_DAY_NAME_PREFIX . $post->ID; ?>" value="<?php echo get_the_date('d'); ?>" />
                        , <input type="text" style="width:48px;" name="<?php echo User_Biography::PART_YEAR_NAME_PREFIX . $post->ID; ?>" value="<?php echo get_the_date('Y'); ?>" />
                        <br>
                        <span style="display:block;margin: 8px 6px;">
                            <label style=color:rgb(170,0,0);" for="<?php echo User_Biography::PART_DELETE_NAME_PREFIX . $post->ID; ?>">
                                <input type="checkbox" name="<?php echo User_Biography::PART_DELETE_NAME_PREFIX . $post->ID; ?>" /> <?php echo __('Delete Part', 'user_biography'); ?>
                            </label>
                        </span>
                    </th>
                    <td>
                        <textarea name="<?php echo User_Biography::PART_TEXTAREA_NAME_PREFIX . $post->ID; ?>" rows="5" cols="30"><?php echo $post->post_content; ?></textarea>
                    </td>
                </tr>
                <?php $count++; ?>
            <?php endwhile; ?>
            <?php if ($count <= 0): ?>
                
            <?php endif; ?>
        </table>
        <?php
    }
    
    /**
     * Action to add or edit biography parts.
     * 
     * Function is called when $action is 'update'.
     */
    static public function update_biography($user_id) {
        $user = get_user_by('id', $user_id);
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST') {
        
            $keys = array_keys($_POST);
            $part_names = preg_grep('#' . User_Biography::PART_TEXTAREA_NAME_PREFIX . '[0-9]+#', $keys);

            foreach ($part_names as $name) {

                $id = intval(str_replace(User_Biography::PART_TEXTAREA_NAME_PREFIX, '', $name));
                $part = get_post($id);

                if ($part !== NULL) {
                    
                    // Check whether to delete the part.
                    if (isset($_POST[User_Biography::PART_DELETE_NAME_PREFIX . $id])) {
                        wp_delete_post($id);
                    }
                    else {
                        $part->post_content = sanitize_text_field($_POST[$name]);

                        $date = intval($_POST[User_Biography::PART_YEAR_NAME_PREFIX . $id])
                                . '-' . intval($_POST[User_Biography::PART_MONTH_NAME_PREFIX . $id])
                                . '-' . intval($_POST[User_Biography::PART_DAY_NAME_PREFIX . $id]);

                        $part->post_date = $date . ' 00:00:00';
                        $part->post_date_gmt = $date . ' 00:00:00';
                        $part->name = $user->user_login . '-' . $date;

                        wp_update_post($part);
                    }
                }
            }

            if (isset($_POST[User_Biography::ADD_PART_CHECKBOX_NAME])) {

                $new_part_content = sanitize_text_field($_POST[User_Biography::ADD_PART_TEXTAREA_NAME]);
                $date = date('Y-m-d') . ' 00:00:00';

                if (!empty($new_part_content)) {

                    // Full post object specification from the documentation:
                    $part = array(
                        // 'ID'
                        'post_content'   => $new_part_content,
                        'post_name'      => $user->user_login . '-' . str_replace(' 00:00:00', '', $date),
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
                        'post_date_gmt'  => $date,
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
}

add_action('init', 'User_Biography::register_post_type');
add_action('show_user_profile', 'User_Biography::show_user_profile');
add_action('personal_options_update', 'User_Biography::update_biography');