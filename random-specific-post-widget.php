<?php
/**
 * @package RandomSpecificPostWidget
 */

/*
 * Plugin Name: Random Specific Post Widget
 * Plugin URI: http://dev.titopub.com/projects/wordpress/plugins/random-specific-post-widget
 * Description: Randomly publish an array of specific post in your sidebar.
 * Version: 1.0.0
 * Author: Karlo Joseph C. Tito
 * Author URI: http://titopub.com
 * Text Domain: random-specific-post-widget
 * License: GPLv3 or later
 */

/* Copyright (C) 2018  Karlo Joseph C. Tito  (email : karlojosephtito@gmail.com)

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Prevent direct access to this file.
 *
 * @since 1.0
 */

if (!defined('ABSPATH')) {
    die('Go away!');
}

include_once ('includes/RandomSpecificPostWidget.php');
include_once ('includes/rsp-functions.php');


class RandomSpecificPost {

    function __construct()
    {
        add_action('widgets_init',array($this,'create_custom_widget'));
    }

    function activate()
    {

        flush_rewrite_rules();

    }

    function deactivate()
    {
        flush_rewrite_rules();

    }

    function create_custom_widget()
    {
        register_widget('RandomSpecificPostWidget');
    }
}

if (class_exists('RandomSpecificPost')) {
    $randomSpecificPost = new RandomSpecificPost();
}


register_activation_hook(__FILE__, array($randomSpecificPost, 'activate'));

register_deactivation_hook(__FILE__, array($randomSpecificPost, 'deactivate'));
