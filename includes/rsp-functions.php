<?php
/**
 * Defines a common set of functions that any class responsible for loading
 * stylesheets, JavaScript, or other assets should implement.
 */
function rsp_add_scripts(){

    wp_enqueue_style('rsp-main-style', plugins_url().'/random-specific-post-widget/css/style.css');

}

add_action('wp_enqueue_scripts','rsp_add_scripts');