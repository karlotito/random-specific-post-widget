<?php
/**
 * This file contains the functions for the widget
 *
 * @since 1.0
 */

/**
 * Prevent direct access to this file.
 *
 * @since 1.0
 */

if (!defined('ABSPATH')) {
    die('Go away!');
}
include_once('rsp-widget-form-functions.php');

class RandomSpecificPostWidget extends WP_widget {

    /**
     * Create the widget's base settings.
     *
     * @since 1.0
     */
    public function __construct()
    {

        /* The widget contructor */
        parent::__construct(
            'rsp_widget',
            esc_html__('Random Specific Post', 'random-specific-post'),
            array('description' => esc_html__('Display a random list of posts in sidebar', 'random-specific-post'),)

        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        // outputs the content of the widget
        $title = apply_filters('widget_title', $instance['title']);
        $post_type = $instance['post_type'];
        $posts_id = $instance['posts_id'];
        $number = $instance['number'];
        $default_image = $instance['default_image'];

        $image = plugin_dir_url(__DIR__) . 'images/default.jpg';

        if ($post_type == NULL): $post_type = 'post'; endif;
        if ($number == NULL): $number = 4; endif;
        if ($default_image == NULL): $default_image = $image; endif;


// before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title)):
            echo $args['before_title'] . $title . $args['after_title'];
        endif;

        if ($posts_id != NULL):
            $postId = explode(",", $posts_id);
            $random_id = array_rand($postId, $number);

            $posts = array();
            for ($i = 0; $i < $number; $i ++) {
                array_push($posts, $postId[$random_id[$i]]);
            }
        else:
            $posts = '';
        endif;
        $args = array(
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'post__in'       => $posts,
            'posts_per_page' => $number
        );

        $the_query = new WP_Query($args);

// This is where you run the code and display the output
        if ($the_query->have_posts()) {
            echo '<ul class="rsp-ul">';
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                if ($thumbnail == NULL) {
                    $thumbnail = $default_image;
                }
                ?>
                <li class="rsp-li">
                    <p class="rsp-thumbnail"><a class="rsp-thumbnail-link" href="<?= get_permalink(); ?>"
                                                rel="bookmark"><img src="<?= $thumbnail ?>" alt=""
                                                                    class="rsp-thumbnail-img  alignleft"></a></p>
                    <div class="rsp-content">
                        <p class="rsp-title"><a class="rsp-title-link" href="<?= get_permalink(); ?>"
                                                rel="bookmark"><?= get_the_title(); ?></a></p>
                        <p class="rsp-excerpt"><?= wp_strip_all_tags(get_the_excerpt(), TRUE); ?></p>
                    </div>
                </li>
                <?php

            }
            echo '</ul>';
            /* Restore original Post Data */
            wp_reset_postdata();
        } else {
            echo 'No results found';
        }
        echo $args['after_widget'];

    }

    /**
     * Outputs the options form on admin
     * @param array $instance The widget option
     */
    public function form($instance)
    {
        // outputs the options form on admin
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('New title', 'random-specific-post');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'random-specific-post'); ?></label>
            <input
                    class="widefat"
                    id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                    type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <?php // ================= Post types

        $args = array(
            'public' => TRUE,
        );
        $post_types = (array) get_post_types($args, 'objects', 'and');

        $options = array(
            array(
                'value' => 'any',
                'desc'  => esc_html__('Any', 'random-specific-post'),
            )
        );
        foreach ($post_types as $post_type) {
            $options[] = array(
                'value' => $post_type->name,
                'desc'  => $post_type->labels->singular_name,
            );
        }

        rsp_form_select(
            esc_html__('Post type', 'random-specific-post'),
            $this->get_field_id('post_type'),
            $this->get_field_name('post_type'),
            $options,
            $instance['post_type'],
            esc_html__('Select a post type.', 'random-specific-post')
        ); ?>

        <?php // ================= Posts ID
        rsp_form_input_text(
            esc_html__('Get these posts exactly', 'random-specific-post'),
            $this->get_field_id('posts_id'),
            $this->get_field_name('posts_id'),
            esc_attr($instance['posts_id']),
            '521, 39, 142, 431',
            esc_html__('Enter IDs, comma separated.', 'random-specific-post')
        ); ?>

        <?php // ================= Posts quantity
        rsp_form_input_text(
            esc_html__('Get this number of posts', 'random-specific-post'),
            $this->get_field_id('number'),
            $this->get_field_name('number'),
            esc_attr($instance['number']),
            '4',
            sprintf(esc_html__('The value %s shows all the posts.', 'random-specific-post'), '<code>-1</code>')
        ); ?>

        <?php // ================= Default Image
        rsp_form_input_text(
            esc_html__('Default featured image', 'random-specific-post'),
            $this->get_field_id('default_image'),
            $this->get_field_name('default_image'),
            esc_attr($instance['default_image']),
            esc_html__('Enter image url', 'random-specific-post')
        ); ?>
        <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        // processes widget options to be saved
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['post_type'] = (!empty($new_instance['post_type'])) ? sanitize_text_field($new_instance['post_type']) : '';
        $instance['posts_id'] = (!empty($new_instance['posts_id'])) ? sanitize_text_field($new_instance['posts_id']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? sanitize_text_field($new_instance['number']) : '';
        $instance['default_image'] = (!empty($new_instance['default_image'])) ? sanitize_text_field($new_instance['default_image']) : '';


        return $instance;
    }

}

