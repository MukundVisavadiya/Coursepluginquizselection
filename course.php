<?php

/**
 * Plugin Name: Course List
 * Description:  A plugin to create a custom post type for courses and manage enrollments.
 * Version: 1.0
 * Author: Akshar Soft Solutions
 * Author URI: http://aksharsoftsolutions.com/
 * Text Domain:course_list
 */

/**
 * Quiz selection in course
 */
require_once dirname(__FILE__) . '/quiz-selection.php';
/**
 * Course Quiz & Questions Settings Code
 */
require_once dirname(__FILE__)  . '/quiz-questions.php';
/**
 * Course Progress Bar Setting
 */
require_once dirname(__FILE__)  . '/progressbar-setting.php';
/**
 * Course User Dashboard
 */
require_once dirname(__FILE__)  . '/user-detail.php';
/**
 * Course Inside chapter Meta box Logic
 */
require_once dirname(__FILE__) . '/chapter-metabox.php';
/**
 * Rewrite Permalink Setting
 */
require_once dirname(__FILE__) . '/rewrite-permalink-setting.php';
/**
 * CLTS Quiz Next Pre Manage
 */
require_once dirname(__FILE__) . '/clts-quiz-next-pre.php';


// Enqueue Plugin Styles & Script in user side
function as_enqueue_course_styles_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('as-datatable', plugin_dir_url(__FILE__) . '/assets/js/datatable.js');
    wp_enqueue_script('as-course-plugin-script', plugin_dir_url(__FILE__) . '/assets/js/script.js', array('jquery'));
    wp_enqueue_script('as-font-awesome', plugin_dir_url(__FILE__) . '/assets/js/font-awesome.js');
    $course_enroll = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('as-course-ajax-nonce'),
    );
    // as_enroll_course localization
    wp_localize_script("as-course-plugin-script", "as_enroll_course", $course_enroll);
    // enroll_course_transaction localization
    wp_localize_script("as-course-plugin-script", "as_enroll_course_transaction", $course_enroll);
    // student_dashboard_enrollment localization
    wp_localize_script("as-course-plugin-script", "as_student_dashboard_enrollment", $course_enroll);
    // mark to complete section localization
    wp_localize_script("as-course-plugin-script", "as_mark_complete_section", $course_enroll);
    // mark to complete topic localization
    wp_localize_script("as-course-plugin-script", "as_mark_complete_topic", $course_enroll);
    // mark to complete lesson localization
    wp_localize_script("as-course-plugin-script", "as_mark_complete_lesson", $course_enroll);
    // mark to complete chapter localization
    wp_localize_script("as-course-plugin-script", "as_mark_complete_chapter", $course_enroll);
    // quiz object data localization
    wp_localize_script("as-course-plugin-script", "quiz_ajax_object_data", $course_enroll);
    // quiz Progress for section localization
    wp_localize_script("as-course-plugin-script", "as_quiz_ajax_progress_section", $course_enroll);
    // wp_localize_script("as-course-plugin-script", "quiz_ajax_section_progress", $course_enroll);
    wp_enqueue_style('as-datatable', plugin_dir_url(__FILE__) . 'assets/css/datatable.css');
    wp_enqueue_style('as-course-plugin-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}
add_action('wp_enqueue_scripts', 'as_enqueue_course_styles_script');

// Enqueue admin Plugin Styles & Script admin panel
function as_enqueue_admin_course_style_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('as-datatable', plugin_dir_url(__FILE__) . '/assets/js/datatable.js');
    wp_enqueue_script('as-course-plugin-admin-script', plugin_dir_url(__FILE__) . '/assets/js/admin-script.js', array('jquery'));
    wp_enqueue_script('as-font-awesome', plugin_dir_url(__FILE__) . '/assets/js/font-awesome.js');
    wp_enqueue_script('select2', plugin_dir_url(__FILE__) . '/assets/js/select-two/select2.min.js');
    $data_table_course = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    );
    wp_localize_script("as-course-plugin-admin-script", "as_enrollment_course_datatable",  $data_table_course);
    wp_localize_script("as-course-plugin-admin-script", "as_course_transaction_datatable",  $data_table_course);

    // chapter action ajax call (create & remove)
    wp_localize_script("as-course-plugin-admin-script", "as_create_chapter_post",  $data_table_course);
    wp_localize_script("as-course-plugin-admin-script", "as_romove_chapter_post", $data_table_course);

    // lesson action ajax call (create & remove)
    wp_localize_script("as-course-plugin-admin-script", "as_create_lesson_post",  $data_table_course);
    wp_localize_script("as-course-plugin-admin-script", "as_romove_lesson_post", $data_table_course);

    // topic action ajax call (create & remove)
    wp_localize_script("as-course-plugin-admin-script", "as_create_topic_post",  $data_table_course);
    wp_localize_script("as-course-plugin-admin-script", "as_romove_topic_post", $data_table_course);

    // section action ajax call (create & remove)
    wp_localize_script("as-course-plugin-admin-script", "as_create_section_post",  $data_table_course);
    wp_localize_script("as-course-plugin-admin-script", "as_romove_section_post", $data_table_course);

    // quiz question create question bank to selected question and create quiz
    wp_localize_script("as-course-plugin-admin-script", "as_quiz_question_search", $data_table_course);

    // quiz selection in course using ajax localization
    wp_localize_script("as-course-plugin-admin-script", "as_quiz_selection_in_course", $data_table_course);

    wp_enqueue_style('as-datatable', plugin_dir_url(__FILE__) . 'assets/css/datatable.css');
    wp_enqueue_style('as-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');
    wp_enqueue_style('select2', plugin_dir_url(__FILE__) . 'assets/css/select-two/select2.min.css');
}
add_action('admin_enqueue_scripts', 'as_enqueue_admin_course_style_script');


// Register Custom Post Type: course
function as_create_course_post_type()
{
    $labels = array(
        'name'                  => _x('Courses', 'Post Type General Name', 'course_list'),
        'singular_name'         => _x('Courses', 'Post Type Singular Name', 'course_list'),
        'menu_name'             => __('LearnMore LMS', 'course_list'),
        'name_admin_bar'        => __('Courses', 'course_list'),
        'archives'              => __('Courses Archives', 'course_list'),
        'attributes'            => __('Courses Attributes', 'course_list'),
        'parent_item_colon'     => __('Parent Courses:', 'course_list'),
        'all_items'             => __('Courses', 'course_list'),
        'add_new_item'          => __('Add New Courses', 'course_list'),
        'add_new'               => __('Add New Courses', 'course_list'),
        'new_item'              => __('New Courses', 'course_list'),
        'edit_item'             => __('Edit Courses', 'course_list'),
        'update_item'           => __('Update Courses', 'course_list'),
        'view_item'             => __('View Courses', 'course_list'),
        'view_items'            => __('View Courses', 'course_list'),
        'search_items'          => __('Search Courses', 'course_list'),
        'not_found'             => __('Courses Not found', 'course_list'),
        'not_found_in_trash'    => __('Courses Not found in Trash', 'course_list'),
        'featured_image'        => __('Courses Featured Image', 'course_list'),
        'set_featured_image'    => __('Set Courses featured image', 'course_list'),
        'remove_featured_image' => __('Remove Courses featured image', 'course_list'),
        'use_featured_image'    => __('Use as Courses featured image', 'course_list'),
        'insert_into_item'      => __('Insert into Course', 'course_list'),
        'uploaded_to_this_item' => __('Uploaded to this Course', 'course_list'),
        'items_list'            => __('Courses list', 'course_list'),
        'items_list_navigation' => __('Courses list navigation', 'course_list'),
        'filter_items_list'     => __('Filter Courses list', 'course_list'),
    );
    $args = array(
        'label'                 => __('Courses', 'course_list'),
        'description'           => __('Courses Description', 'course_list'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'menu_icon'             => 'dashicons-welcome-learn-more',
    );
    register_post_type('course', $args);
}
add_action('init', 'as_create_course_post_type');


// Register Custom Post Type: Lessions
function as_create_lessons_post_type()
{
    $labels = array(
        'name'                  => _x('Lessons', 'Post Type General Name', 'course_list'),
        'singular_name'         => _x('Lessons', 'Post Type Singular Name', 'course_list'),
        'menu_name'             => __('Lessons', 'course_list'),
        'name_admin_bar'        => __('Lessons', 'course_list'),
        'archives'              => __('Lessons Archives', 'course_list'),
        'attributes'            => __('Lessons Attributes', 'course_list'),
        'parent_item_colon'     => __('Parent Lessons:', 'course_list'),
        'all_items'             => __('Lessons', 'course_list'),
        'add_new_item'          => __('Add New Lessons', 'course_list'),
        'add_new'               => __('Add New Lessons', 'course_list'),
        'new_item'              => __('New Lessons', 'course_list'),
        'edit_item'             => __('Edit Lessons', 'course_list'),
        'update_item'           => __('Update Lessons', 'course_list'),
        'view_item'             => __('View Lessons', 'course_list'),
        'view_items'            => __('View Lessons', 'course_list'),
        'search_items'          => __('Search Lessons', 'course_list'),
        'not_found'             => __('Lessons Not found', 'course_list'),
        'not_found_in_trash'    => __('Lessons Not found in Trash', 'course_list'),
        'featured_image'        => __('Lessons Featured Image', 'course_list'),
        'set_featured_image'    => __('Set Lessons featured image', 'course_list'),
        'remove_featured_image' => __('Remove Lessons featured image', 'course_list'),
        'use_featured_image'    => __('Use as Lessons featured image', 'course_list'),
        'insert_into_item'      => __('Insert into Lessons', 'course_list'),
        'uploaded_to_this_item' => __('Uploaded to this Lessons', 'course_list'),
        'items_list'            => __('Lessons list', 'course_list'),
        'items_list_navigation' => __('Lessons list navigation', 'course_list'),
        'filter_items_list'     => __('Filter Lessons list', 'course_list'),
    );
    $args = array(
        'label'                 => __('Lessons', 'course_list'),
        'description'           => __('Lessons Description', 'course_list'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => 'edit.php?post_type=course',
        'menu_position'         => 7,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type('lessons', $args);
}
add_action('init', 'as_create_lessons_post_type');

// Register Custom Post Type: Topics
function as_create_topics_post_type()
{
    $labels = array(
        'name'                  => _x('Topics', 'Post Type General Name', 'course_list'),
        'singular_name'         => _x('Topics', 'Post Type Singular Name', 'course_list'),
        'menu_name'             => __('Topics', 'course_list'),
        'name_admin_bar'        => __('Topics', 'course_list'),
        'archives'              => __('Topics Archives', 'course_list'),
        'attributes'            => __('Topics Attributes', 'course_list'),
        'parent_item_colon'     => __('Parent Topics:', 'course_list'),
        'all_items'             => __('Topics', 'course_list'),
        'add_new_item'          => __('Add New Topics', 'course_list'),
        'add_new'               => __('Add New Topics', 'course_list'),
        'new_item'              => __('New Topics', 'course_list'),
        'edit_item'             => __('Edit Topics', 'course_list'),
        'update_item'           => __('Update Topics', 'course_list'),
        'view_item'             => __('View Topics', 'course_list'),
        'view_items'            => __('View Topics', 'course_list'),
        'search_items'          => __('Search Topics', 'course_list'),
        'not_found'             => __('Topics Not found', 'course_list'),
        'not_found_in_trash'    => __('Topics Not found in Trash', 'course_list'),
        'featured_image'        => __('Topics Featured Image', 'course_list'),
        'set_featured_image'    => __('Set Topics featured image', 'course_list'),
        'remove_featured_image' => __('Remove Topics featured image', 'course_list'),
        'use_featured_image'    => __('Use as Topics featured image', 'course_list'),
        'insert_into_item'      => __('Insert into Topics', 'course_list'),
        'uploaded_to_this_item' => __('Uploaded to this Topics', 'course_list'),
        'items_list'            => __('Topics list', 'course_list'),
        'items_list_navigation' => __('Topics list navigation', 'course_list'),
        'filter_items_list'     => __('Filter Topics list', 'course_list'),
    );
    $args = array(
        'label'                 => __('Topics', 'course_list'),
        'description'           => __('Topics Description', 'course_list'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => false,
        'show_ui'               => true,
        'show_in_menu'          => 'edit.php?post_type=course',
        'menu_position'         => 8,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type('topics', $args);
}
add_action('init', 'as_create_topics_post_type');


// Register Custom Post Type: Chapter
function as_create_chapteres_post_type()
{
    $labels = array(
        'name'                  => _x('Chapter', 'Post Type General Name', 'course_list'),
        'singular_name'         => _x('Chapter', 'Post Type Singular Name', 'course_list'),
        'menu_name'             => __('Chapter', 'course_list'),
        'name_admin_bar'        => __('Chapter', 'course_list'),
        'archives'              => __('Chapter Archives', 'course_list'),
        'attributes'            => __('Chapter Attributes', 'course_list'),
        'parent_item_colon'     => __('Parent Chapter:', 'course_list'),
        'all_items'             => __('Chapter', 'course_list'),
        'add_new_item'          => __('Add New Chapter', 'course_list'),
        'add_new'               => __('Add New Chapter', 'course_list'),
        'new_item'              => __('New Chapter', 'course_list'),
        'edit_item'             => __('Edit Chapter', 'course_list'),
        'update_item'           => __('Update Chapter', 'course_list'),
        'view_item'             => __('View Chapter', 'course_list'),
        'view_items'            => __('View Chapter', 'course_list'),
        'search_items'          => __('Search Chapter', 'course_list'),
        'not_found'             => __('Chapter Not found', 'course_list'),
        'not_found_in_trash'    => __('Chapter Not found in Trash', 'course_list'),
        'featured_image'        => __('Chapter Featured Image', 'course_list'),
        'set_featured_image'    => __('Set Chapter featured image', 'course_list'),
        'remove_featured_image' => __('Remove Chapter featured image', 'course_list'),
        'use_featured_image'    => __('Use as Chapter featured image', 'course_list'),
        'insert_into_item'      => __('Insert into Chapter', 'course_list'),
        'uploaded_to_this_item' => __('Uploaded to this Chapter', 'course_list'),
        'items_list'            => __('Chapter list', 'course_list'),
        'items_list_navigation' => __('Chapter list navigation', 'course_list'),
        'filter_items_list'     => __('Filter Chapter list', 'course_list'),
    );
    $args = array(
        'label'                 => __('Chapter', 'course_list'),
        'description'           => __('Chapter Description', 'course_list'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => false,
        'show_ui'               => true,
        'show_in_menu'          => 'edit.php?post_type=course',
        'menu_position'         => 6,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type('chapters ', $args);
}
add_action('init', 'as_create_chapteres_post_type');



// Register Custom Post Type: Section
function as_create_section_post_type()
{
    $labels = array(
        'name'                  => _x('Section', 'Post Type General Name', 'course_list'),
        'singular_name'         => _x('Section', 'Post Type Singular Name', 'course_list'),
        'menu_name'             => __('Section', 'course_list'),
        'name_admin_bar'        => __('Section', 'course_list'),
        'archives'              => __('Section Archives', 'course_list'),
        'attributes'            => __('Section Attributes', 'course_list'),
        'parent_item_colon'     => __('Parent Section:', 'course_list'),
        'all_items'             => __('Section', 'course_list'),
        'add_new_item'          => __('Add New Section', 'course_list'),
        'add_new'               => __('Add New Section', 'course_list'),
        'new_item'              => __('New Section', 'course_list'),
        'edit_item'             => __('Edit Section', 'course_list'),
        'update_item'           => __('Update Section', 'course_list'),
        'view_item'             => __('View Section', 'course_list'),
        'view_items'            => __('View Section', 'course_list'),
        'search_items'          => __('Search Section', 'course_list'),
        'not_found'             => __('Section Not found', 'course_list'),
        'not_found_in_trash'    => __('Section Not found in Trash', 'course_list'),
        'featured_image'        => __('Section Featured Image', 'course_list'),
        'set_featured_image'    => __('Set Section featured image', 'course_list'),
        'remove_featured_image' => __('Remove Section featured image', 'course_list'),
        'use_featured_image'    => __('Use as Section featured image', 'course_list'),
        'insert_into_item'      => __('Insert into Section', 'course_list'),
        'uploaded_to_this_item' => __('Uploaded to this Section', 'course_list'),
        'items_list'            => __('Section list', 'course_list'),
        'items_list_navigation' => __('Section list navigation', 'course_list'),
        'filter_items_list'     => __('Filter Section list', 'course_list'),
    );
    $args = array(
        'label'                 => __('Section', 'course_list'),
        'description'           => __('Section Description', 'course_list'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => false,
        'show_ui'               => true,
        'show_in_menu'          => 'edit.php?post_type=course',
        'menu_position'         => 9,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type('sections', $args);
}
add_action('init', 'as_create_section_post_type');

// register custom course category
function as_courses_category()
{
    $labels = array(
        'name' => _x('Courses Categories', 'taxonomy general name'),
        'add_new_item' => __('Add New Category'),
        'menu_name' => __('Courses Categories'),
        'singular_name' => _x('Category', 'taxonomy singular name'),
        'search_items' => __('Search Courses Categories'),
        'all_items' => __('All Courses Categories'),
        'parent_item' => __('Courses Parent Category'),
        'parent_item_colon' => __('Parent Courses Category:'),
        'edit_item' => __('Edit Courses Category'),
        'update_item' => __('Update Courses Category'),
        'new_item_name' => __('New Courses Category Name'),
    );

    register_taxonomy('courses_category', array('course'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_menu' => false,
        'show_in_rest' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'courses-category'),
    ));
}
add_action('init', 'as_courses_category', 0);

// show admin bar archive page link
function as_add_course_link_to_admin_bar($wp_admin_bar)
{

    global $pagenow, $typenow;

    if ($pagenow == 'edit.php' && $typenow == 'course') {

        $args = array(
            'id' => 'course_link',
            'title' => 'View Course',
            'href' => site_url('/course/'),
            'meta' => array(
                'class' => 'course-link',
                'title' => 'Manage Courses'
            )
        );

        $wp_admin_bar->add_node($args);
    }
}
add_action('admin_bar_menu', 'as_add_course_link_to_admin_bar', 100);

// Add page in theme
add_filter('template_include', 'as_course_page_template');
function as_course_page_template($template)
{
    $plugin_dir = plugin_basename(dirname(__FILE__));

    if (is_post_type_archive('course')) {
        $archive_template = WP_PLUGIN_DIR . '/' . $plugin_dir . '/template/archive-course.php';
        if (file_exists($archive_template)) {
            return $archive_template;
        }
    }

    if (is_singular('course')) {
        $single_template = WP_PLUGIN_DIR . '/' . $plugin_dir . '/template/single-course.php';
        if (file_exists($single_template)) {
            return $single_template;
        }
    }

    if (is_singular('chapters')) {
        $single_chapter_template = WP_PLUGIN_DIR . '/' . $plugin_dir . '/template/single-chapters.php';
        if (file_exists($single_chapter_template)) {
            return $single_chapter_template;
        }
    }

    if (is_singular('lessons')) {
        $single_lessons_template = WP_PLUGIN_DIR . '/' . $plugin_dir . '/template/single-lessons.php';
        if (file_exists($single_lessons_template)) {
            return $single_lessons_template;
        }
    }

    if (is_singular('topics')) {
        $single_topics_template = WP_PLUGIN_DIR . '/' . $plugin_dir . '/template/single-topics.php';
        if (file_exists($single_topics_template)) {
            return $single_topics_template;
        }
    }

    if (is_singular('sections')) {
        $single_sections_template = WP_PLUGIN_DIR . '/' . $plugin_dir . '/template/single-sections.php';
        if (file_exists($single_sections_template)) {
            return $single_sections_template;
        }
    }

    if (is_singular('quiz')) {
        $single_quiz_template = WP_PLUGIN_DIR . '/' . $plugin_dir . '/template/single-quiz.php';
        if (file_exists($single_quiz_template)) {
            return $single_quiz_template;
        }
    }
    return $template;
}


// create custom price meta box in course 
function as_course_fee_register_meta_boxes()
{
    add_meta_box('course_price', __('Course Fee', 'course_list'), 'as_cf_display_callback', 'course');
}
add_action('add_meta_boxes', 'as_course_fee_register_meta_boxes');

// display meta box
function as_cf_display_callback($post)
{
    $price = get_post_meta($post->ID, 'as_course_price', true);
?>
    <div>
        <label>Course Price</label>
        <input type="text" name="as_course_price" id="as_course_price" value="<?php echo esc_attr($price) ?>" />
    </div>
<?php
}

// save meta box value
function as_cf_save_meta_box($post_id)
{
    if (isset($_POST['as_course_price'])) {
        $price = sanitize_text_field($_POST['as_course_price']);
        update_post_meta($post_id, 'as_course_price', $price);
    } else {
        delete_post_meta($post_id, 'as_course_price');
    }
}
add_action('save_post', 'as_cf_save_meta_box');

// meta box register for course language
function as_course_language_register_meta_boxes()
{
    add_meta_box('course_language', __('Course Language', 'course_list'), 'as_course_language_display_callback', 'course');
}
add_action('add_meta_boxes', 'as_course_language_register_meta_boxes');

// display meta box
function as_course_language_display_callback($post)
{
    $Languages = get_post_meta($post->ID, 'as_course_languages', true);
?>
    <div>
        <label>Course Languages</label>
        <input type="text" name="as_course_languages" id="as_course_languages" value="<?php echo esc_attr($Languages) ?>" />
    </div>
<?php
}

// save meta box value
function as_course_language_save_meta_box($post_id)
{
    if (isset($_POST['as_course_languages'])) {
        $languages = sanitize_text_field($_POST['as_course_languages']);
        update_post_meta($post_id, 'as_course_languages', $languages);
    } else {
        delete_post_meta($post_id, 'as_course_languages');
    }
}
add_action('save_post', 'as_course_language_save_meta_box');


// meta box register for course language
function as_course_collaboration_register_meta_boxes()
{
    add_meta_box('course_collaboration', __('Course Collaboration', 'course_list'), 'as_course_collaboration_display_callback', 'course');
}
add_action('add_meta_boxes', 'as_course_collaboration_register_meta_boxes');

// display meta box
function as_course_collaboration_display_callback($post)
{
    $collaborations = get_post_meta($post->ID, 'as_course_collaboration', true);
?>
    <div>
        <label>Course Collaboration</label>
        <input type="text" name="as_course_collaboration" id="as_course_collaboration" value="<?php echo esc_attr($collaborations) ?>" />
    </div>
<?php
}

// save meta box value
function as_course_collaborations_save_meta_box($post_id)
{
    if (isset($_POST['as_course_collaboration'])) {
        $collaborations = sanitize_text_field($_POST['as_course_collaboration']);
        update_post_meta($post_id, 'as_course_collaboration', $collaborations);
    } else {
        delete_post_meta($post_id, 'as_course_collaboration');
    }
}
add_action('save_post', 'as_course_collaborations_save_meta_box');


// manage user login & enrollment history
function as_enroll_course()
{
    global $wpdb;
    $user_id = get_current_user_id();
    $course_id = intval($_POST['course_id']);
    $course_name = get_the_title($course_id);
    $user_name = wp_get_current_user()->user_login;
    $course_slug = get_post_field('post_name', $course_id);


    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'as-course-ajax-nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce.'));
        wp_die();
    } else {
        if (!is_user_logged_in()) {
            $redirect_url = wp_login_url(get_permalink());

            wp_send_json_error(
                array(
                    'message' => 'You must be logged in to enroll.',
                    'redirect_url' => $redirect_url
                )
            );

            wp_die();
        }

        $user_roal = new WP_User($user_id);

        if (!in_array('administrator', $user_roal->roles)) {
            $user_roal->set_role('student');
        }

        $table_name = $wpdb->prefix . 'as_user_enrollment_history';

        $enrollment = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND course_id = %d",
                $user_id,
                $course_id
            )
        );


        if ($enrollment) {
            wp_send_json_error(array('message' => 'You are already enrolled in this course.'));
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'course_id' => $course_id,
                    'course_name' => $course_name,
                    'enrollment_time' => current_time('mysql')
                )
            );
            $redirect_url = home_url('/course/' . $course_slug . '/');

            echo json_encode(array('success' => true, 'redirect_url' => $redirect_url));
            wp_die();
        }
    }
}
add_action('wp_ajax_as_enroll_course', 'as_enroll_course');
add_action('wp_ajax_nopriv_as_enroll_course', 'as_enroll_course');


// enrollment history database table create
function as_create_user_enrollment_history_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'as_user_enrollment_history';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        user_name VARCHAR(255) NOT NULL,
        course_id bigint(20) NOT NULL,
        course_name VARCHAR(255) NOT NULL,
        enrollment_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'as_create_user_enrollment_history_table');

// display enrollment History in admin panel
function as_display_enrollment_history()
{
    echo '<div><h2>User Course Enrollment History</h2><table id="as-enrollment-table"><thead><tr><th>ID</th><th>User ID</th><th>User Name</th><th>Course ID</th><th>Course Name</th><th>Enrollment Time</th></tr></thead></table></div>';
}

function as_add_enrollment_menu()
{
    add_submenu_page('edit.php?post_type=course', 'Enrollment History', 'Enrollment History', 'manage_options', 'enrollment-history', 'as_display_enrollment_history');
}
add_action('admin_menu', 'as_add_enrollment_menu');


// manage user course Transaction history
function as_enroll_course_transaction()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'as-course-ajax-nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce.'));
        wp_die();
    } else {
        if (!is_user_logged_in()) {
            $redirect_url = wp_login_url(get_permalink());

            wp_send_json_error(
                array(
                    'message' => 'You must be logged in to enroll.',
                    'redirect_url' => $redirect_url
                )
            );
            wp_die();
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $course_id = intval($_POST['course_id']);
        $course_name = get_the_title($course_id);
        $user_name = wp_get_current_user()->user_login;
        $course_slug = get_post_field('post_name', $course_id);
        $course_price = get_post_meta($course_id, 'course_price', true);


        $table_name = $wpdb->prefix . 'as_course_transaction_detail';

        $enrollment = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND course_id = %d",
                $user_id,
                $course_id
            )
        );


        if ($enrollment) {
            wp_send_json_error(array('message' => 'You are already enrolled in this course.'));
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'course_id' => $course_id,
                    'course_name' => $course_name,
                    'course_price' => $course_price,
                    'transaction_status' => 'completed',
                    'enrollment_time' => current_time('mysql')
                )
            );
            $redirect_url = home_url('/course/' . $course_slug . '/');

            echo json_encode(array('success' => true, 'redirect_url' => $redirect_url));
            wp_die();
        }
    }
}
add_action('wp_ajax_as_enroll_course_transaction', 'as_enroll_course_transaction');
add_action('wp_ajax_nopriv_as_enroll_course_transaction', 'as_enroll_course_transaction');


// transaction-detil history database table create
function as_create_transaction_detail_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'as_course_transaction_detail';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        user_name VARCHAR(255) NOT NULL,
        course_id bigint(20) NOT NULL,
        course_name VARCHAR(255) NOT NULL,
        course_price VARCHAR(255) NOT NULL,
        transaction_status VARCHAR(100) NOT NULL,
        enrollment_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'as_create_transaction_detail_table');

// display enrollment Transaction History in admin panel
function as_display_enrollment_transaction_history()
{
    echo '<div><h2>User Course Transaction History</h2><table id="as-transaction-table"><thead><tr><th>ID</th><th>User ID</th><th>User Name</th><th>Course ID</th><th>Course Name</th><th>Course Price</th><th>Course transaction Status</th><th>Enrollment Time</th></tr></thead></tabel>';
}

function as_add_enrollment_transaction_menu()
{
    add_submenu_page('edit.php?post_type=course', 'Transaction History', 'Transaction History', 'manage_options', 'transaction-history', 'as_display_enrollment_transaction_history');
}
add_action('admin_menu', 'as_add_enrollment_transaction_menu');


// Enrollment DataTable Pagination
add_action('wp_ajax_as_enrollment_course_datatable', 'as_enrollment_course_datatable');
add_action('wp_ajax_nopriv_as_enrollment_course_datatable', 'as_enrollment_course_datatable');

function as_enrollment_course_datatable()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'as_user_enrollment_history';

    $start = intval($_POST['start']);
    $length = intval($_POST['length']);

    $total_query = "SELECT COUNT(*) FROM $table_name";
    $total_records = $wpdb->get_var($total_query);

    $query = "SELECT * FROM $table_name LIMIT $start, $length";
    $results = $wpdb->get_results($query);



    // Prepare data for DataTables
    $data = [];
    foreach ($results as $row) {
        $data[] = array(
            'id' => $row->id,
            'user_id' => $row->user_id,
            'user_name' => $row->user_name,
            'course_id' => $row->course_id,
            'course_name' => $row->course_name,
            'enrollment_time' => $row->enrollment_time
        );
    }

    // Response format required by DataTables
    echo json_encode(array(
        'draw' => intval($_POST['draw']),
        'iTotalRecords' => $total_records,
        'iTotalDisplayRecords' => $total_records,
        'data' => $data,
    ));
    wp_die();
}

// Trasition DataTable Pagination 
add_action('wp_ajax_as_course_transaction_datatable', 'as_course_transaction_datatable');
add_action('wp_ajax_nopriv_as_course_transaction_datatable', 'as_course_transaction_datatable');

function as_course_transaction_datatable()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'as_course_transaction_detail';

    $start = intval($_POST['start']);
    $length = intval($_POST['length']);

    $total_query = "SELECT COUNT(*) FROM $table_name";
    $total_records = $wpdb->get_var($total_query);

    $query = "SELECT * FROM $table_name LIMIT $start, $length";
    $results = $wpdb->get_results($query);



    // Prepare data for DataTables
    $data = [];
    foreach ($results as $row) {
        $data[] = array(
            'id' => $row->id,
            'user_id' => $row->user_id,
            'user_name' => $row->user_name,
            'course_id' => $row->course_id,
            'course_name' => $row->course_name,
            'course_price' => $row->course_price,
            'transaction_status' => $row->transaction_status,
            'enrollment_time' => $row->enrollment_time
        );
    }

    // Response format required by DataTables
    echo json_encode(array(
        'draw' => intval($_POST['draw']),
        'iTotalRecords' => $total_records,
        'iTotalDisplayRecords' => $total_records,
        'data' => $data,
    ));
    wp_die();
}


// student dashboard DataTable Pagination 
add_action('wp_ajax_as_student_dashboard_enrollment', 'as_student_dashboard_enrollment');
add_action('wp_ajax_nopriv_as_student_dashboard_enrollment', 'as_student_dashboard_enrollment');

function as_student_dashboard_enrollment()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'as-course-ajax-nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce.'));
        wp_die();
    } else {
        global $wpdb;
        $user_id = get_current_user_id();

        $table_enrollment = $wpdb->prefix . 'as_user_enrollment_history';
        $table_transaction = $wpdb->prefix . 'as_course_transaction_detail';

        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $draw = intval($_POST['draw']);

        // Count total records
        $total_query = $wpdb->prepare(
            "SELECT COUNT(*) 
         FROM $table_enrollment 
         LEFT JOIN $table_transaction 
         ON $table_enrollment.user_id = $table_transaction.user_id 
         AND $table_enrollment.course_id = $table_transaction.course_id 
         WHERE $table_enrollment.user_id = %d",
            $user_id
        );
        $total_records = $wpdb->get_var($total_query);

        // Fetch paginated records
        $query = $wpdb->prepare(
            "SELECT $table_enrollment.course_id, $table_enrollment.course_name, DATE($table_enrollment.enrollment_time) AS enrollment_time, 
                $table_transaction.transaction_status
         FROM $table_enrollment 
         LEFT JOIN $table_transaction 
         ON $table_enrollment.user_id = $table_transaction.user_id 
         AND $table_enrollment.course_id = $table_transaction.course_id 
         WHERE $table_enrollment.user_id = %d
         LIMIT %d, %d",
            $user_id,
            $start,
            $length
        );
        $results = $wpdb->get_results($query);

        // Prepare data for DataTables
        $data = [];
        foreach ($results as $row) {
            $data[] = array(
                'course_id' => $row->course_id,
                'course_name' => $row->course_name,
                'enrollment_time' => $row->enrollment_time,
                'transaction_status' => $row->transaction_status,
            );
        }

        // Response format required by DataTables
        echo json_encode(
            array(
                'draw' => $draw,
                'iTotalRecords' => $total_records,
                'iTotalDisplayRecords' => $total_records,
                'data' => $data,
            )
        );
        wp_die();
    }
}


function as_delete_course_data_before_post_deletion($post_id)
{
    global $wpdb;

    if (get_post_type($post_id) !== 'course') {
        return;
    }

    $table_enrollment = $wpdb->prefix . 'as_user_enrollment_history';
    $table_transaction = $wpdb->prefix . 'as_course_transaction_detail';

    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM $table_transaction WHERE course_id = %d",
            $post_id
        )
    );

    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM $table_enrollment WHERE course_id = %d",
            $post_id
        )
    );
}

add_action('before_delete_post', 'as_delete_course_data_before_post_deletion');


// as_learnmore_user_activity table create
function as_create_learnmore_user_activity()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        chapter_id BIGINT(20) UNSIGNED NOT NULL,
        lesson_id BIGINT(20) UNSIGNED NOT NULL,
        topic_id BIGINT(20) UNSIGNED NOT NULL,
        section_id BIGINT(20) UNSIGNED NOT NULL,
        quiz_id BIGINT(20) UNSIGNED NOT NULL,
        course_id BIGINT(20) UNSIGNED NOT NULL,
        activity_type VARCHAR(50) NOT NULL,
        activity_status VARCHAR(50) NOT NULL,
        activity_started DATETIME NOT NULL,
        activity_completed DATETIME,
        activity_updated DATETIME NOT NULL,
        PRIMARY KEY  (id),
        INDEX (user_id),
        INDEX (chapter_id),
        INDEX (lesson_id),
        INDEX (topic_id),
        INDEX (section_id),
        INDEX (quiz_id),
        INDEX (course_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'as_create_learnmore_user_activity');

// mark to read  logic for a section
function as_mark_complete_section()
{
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'as-course-ajax-nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce.'));
        wp_die();
    }

    $user_id = get_current_user_id();
    $chapter_id = intval($_POST['chapter_id']);
    $lesson_id = intval($_POST['lesson_id']);
    $topic_id = intval($_POST['topic_id']);
    $section_id = intval($_POST['section_id']);
    $course_id = intval($_POST['course_id']);

    $current_time = current_time('mysql');

    global $wpdb;
    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

    // Update or insert activity
    $activity = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d AND chapter_id = %d AND lesson_id = %d AND topic_id = %d AND section_id = %d AND course_id = %d",
        $user_id,
        $chapter_id,
        $lesson_id,
        $topic_id,
        $section_id,
        $course_id
    ));

    if ($activity) {
        $wpdb->update(
            $table_name,
            array(
                'activity_status' => 'completed',
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            ),
            array(
                'user_id' => $user_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id,
                'topic_id' => $topic_id,
                'section_id' => $section_id,
                'course_id' => $course_id,
            )
        );
    } else {
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id,
                'topic_id' => $topic_id,
                'section_id' => $section_id,
                'course_id' => $course_id,
                'activity_type' => 'section',
                'activity_status' => 'completed',
                'activity_started' => $current_time,
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            )
        );
    }

    $allSections = json_decode(stripslashes($_POST['all_sections']), true);

    // Determine next section URL
    $next_section_url = '#';
    $first_quiz_url = '#';
    $next_quiz_url = '#';

    foreach ($allSections as $index => $section) {

        if ($section['section_id'] == $section_id) {

            if (!empty($section['quiz_id'])) {
                $section_slug = get_post_field('post_name', $section['section_id']);

                foreach ($section['quiz_id'] as $quiz_index => $quiz_id) {
                    $quiz_slug = get_post_field('post_name', $quiz_id);

                    if ($quiz_index == 0) {
                        $first_quiz_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/chapters/' . $_POST['chapter_slug'] . '/lessons/' . $_POST['lesson_slug'] . '/topics/' . $_POST['topic_slug'] . '/sections/' . $section_slug . '/quiz/' . $quiz_slug . '/';
                    }

                    if (isset($section['quiz_id'][$quiz_index + 1])) {
                        $next_quiz_id = $section['quiz_id'][$quiz_index + 1];
                        $next_quiz_slug = get_post_field('post_name', $next_quiz_id);
                        $next_quiz_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/chapters/' . $_POST['chapter_slug'] . '/lessons/' . $_POST['lesson_slug'] . '/topics/' . $_POST['topic_slug'] . '/sections/' . $section_slug . '/quiz/' . $next_quiz_slug . '/';
                        break;
                    }
                }
            }

            if ($next_quiz_url === '#' && isset($allSections[$index + 1])) {
                $next_section = $allSections[$index + 1];
                $next_section_slug = get_post_field('post_name', $next_section['section_id']);
                $next_section_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/chapters/' . $_POST['chapter_slug'] . '/lessons/' . $_POST['lesson_slug'] . '/topics/' . $_POST['topic_slug'] . '/sections/' . $next_section_slug . '/';
            }

            break;
        }
    }

    if ($next_section_url !== '#' || $first_quiz_url !== '#' || $next_quiz_url !== '#') {
        wp_send_json_success(array(
            'next_section_url' => $next_section_url,
            'first_quiz_url' => $first_quiz_url,
            'next_quiz_url' => $next_quiz_url
        ));
    } else {
        $fallback_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/chapters/' . $_POST['chapter_slug'] . '/lessons/' . $_POST['lesson_slug'] . '/topics/' . $_POST['topic_slug'] . '/';
        wp_send_json_error(array(
            'next_topic_url' => $fallback_url
        ));
    }


    wp_die();
}
add_action('wp_ajax_as_mark_complete_section', 'as_mark_complete_section');
add_action('wp_ajax_nopriv_as_mark_complete_section', 'as_mark_complete_section');

// mark to read logic for a Topic
function as_mark_complete_topic()
{
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'as-course-ajax-nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce.'));
        wp_die();
    }

    $user_id = get_current_user_id();
    $chapter_id = intval($_POST['chapter_id']);
    $lesson_id = intval($_POST['lesson_id']);
    $topic_id = intval($_POST['topic_id']);
    $section_id = '';
    $course_id = intval($_POST['course_id']);
    $current_time = current_time('mysql');

    global $wpdb;
    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

    // Update or insert activity
    $activity = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d AND chapter_id = %d AND lesson_id = %d AND topic_id = %d AND section_id = %d AND course_id = %d",
        $user_id,
        $chapter_id,
        $lesson_id,
        $topic_id,
        $section_id,
        $course_id
    ));

    if ($activity) {
        $wpdb->update(
            $table_name,
            array(
                'activity_status' => 'completed',
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            ),
            array(
                'user_id' => $user_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id,
                'topic_id' => $topic_id,
                'section_id' => $section_id,
                'course_id' => $course_id,
            )
        );
    } else {
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id,
                'topic_id' => $topic_id,
                'section_id' => $section_id,
                'course_id' => $course_id,
                'activity_type' => 'Topic',
                'activity_status' => 'completed',
                'activity_started' => $current_time,
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            )
        );
    }

    $allTopics = json_decode(stripslashes($_POST['all_topics']), true);

    // Determine next section URL
    $next_topic_url = '#'; // default value
    foreach ($allTopics as $index => $topic) {
        if ($topic['topic_id'] == $topic_id && isset($allTopics[$index + 1])) {
            $next_topic = $allTopics[$index + 1];
            $next_topic_slug = get_post_field('post_name', $next_topic['topic_id']);
            $next_topic_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/chapters/' . $_POST['chapter_slug'] . '/lessons/' . $_POST['lesson_slug'] . '/topics/' . $next_topic_slug . '/';
            break;
        }
    }

    if ($next_topic_url !== '#') {
        wp_send_json_success(array(
            'next_topic_url' => $next_topic_url
        ));
    } else {
        $fallback_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/chapters/' . $_POST['chapter_slug'] . '/lessons/' . $_POST['lesson_slug'] . '/';
        wp_send_json_error(array(
            'next_topic_url' => $fallback_url
        ));
    }

    wp_die();
}
add_action('wp_ajax_as_mark_complete_topic', 'as_mark_complete_topic');
add_action('wp_ajax_nopriv_as_mark_complete_topic', 'as_mark_complete_topic');

// mark to read logic for a Lesson
function as_mark_complete_lesson()
{
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'as-course-ajax-nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce.'));
        wp_die();
    }

    $user_id = get_current_user_id();
    $chapter_id = intval($_POST['chapter_id']);
    $lesson_id = intval($_POST['lesson_id']);
    $topic_id = '';
    $section_id = '';
    $course_id = intval($_POST['course_id']);
    $current_time = current_time('mysql');

    global $wpdb;
    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

    // Update or insert activity
    $activity = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d AND chapter_id = %d AND lesson_id = %d AND topic_id = %d AND section_id = %d AND course_id = %d",
        $user_id,
        $chapter_id,
        $lesson_id,
        $topic_id,
        $section_id,
        $course_id
    ));

    if ($activity) {
        $wpdb->update(
            $table_name,
            array(
                'activity_status' => 'completed',
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            ),
            array(
                'user_id' => $user_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id,
                'topic_id' => $topic_id,
                'section_id' => $section_id,
                'course_id' => $course_id,
            )
        );
    } else {
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id,
                'topic_id' => $topic_id,
                'section_id' => $section_id,
                'course_id' => $course_id,
                'activity_type' => 'lesson',
                'activity_status' => 'completed',
                'activity_started' => $current_time,
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            )
        );
    }

    $allLesson = json_decode(stripslashes($_POST['all_lesson']), true);

    // Determine next lesson URL
    $next_lesson_url = '#'; // default value
    foreach ($allLesson as $index => $lesson) {
        if ($lesson['lesson_id'] == $lesson_id && isset($allLesson[$index + 1])) {
            $next_lesson = $allLesson[$index + 1];
            $next_lesson_slug = get_post_field('post_name', $next_lesson['lesson_id']);
            $next_lesson_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/chapters/' . $_POST['chapter_slug'] . '/lessons/' . $next_lesson_slug . '/';
            break;
        }
    }

    if ($next_lesson_url !== '#') {
        wp_send_json_success(array(
            'next_lesson_url' => $next_lesson_url
        ));
    } else {
        $fallback_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/chapters/' . $_POST['chapter_slug'] . '/';
        wp_send_json_error(array(
            'next_lesson_url' => $fallback_url
        ));
    }

    wp_die();
}
add_action('wp_ajax_as_mark_complete_lesson', 'as_mark_complete_lesson');
add_action('wp_ajax_nopriv_as_mark_complete_lesson', 'as_mark_complete_lesson');


// mark to read logic for a chapter
function as_mark_complete_chapter()
{
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'as-course-ajax-nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce.'));
        wp_die();
    }

    $user_id = get_current_user_id();
    $chapter_id = intval($_POST['chapter_id']);
    $lesson_id = '';
    $topic_id = '';
    $section_id = '';
    $course_id = intval($_POST['course_id']);
    $current_time = current_time('mysql');

    global $wpdb;
    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

    // Update or insert activity
    $activity = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d AND chapter_id = %d AND lesson_id = %d AND topic_id = %d AND section_id = %d AND course_id = %d",
        $user_id,
        $chapter_id,
        $lesson_id,
        $topic_id,
        $section_id,
        $course_id
    ));

    if ($activity) {
        $wpdb->update(
            $table_name,
            array(
                'activity_status' => 'completed',
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            ),
            array(
                'user_id' => $user_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id,
                'topic_id' => $topic_id,
                'section_id' => $section_id,
                'course_id' => $course_id,
            )
        );
    } else {
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id,
                'topic_id' => $topic_id,
                'section_id' => $section_id,
                'course_id' => $course_id,
                'activity_type' => 'chapter',
                'activity_status' => 'completed',
                'activity_started' => $current_time,
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            )
        );
    }

    $allChapter = json_decode(stripslashes($_POST['all_chapter']), true);

    // Determine next lesson URL
    $next_chapter_url = '#'; // default value
    foreach ($allChapter as $index => $chapter) {
        if ($chapter['chapter_id'] == $chapter_id && isset($allChapter[$index + 1])) {
            $next_chapter = $allChapter[$index + 1];
            $next_chapter_slug = get_post_field('post_name', $next_chapter['chapter_id']);
            $next_chapter_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/chapters/' . $next_chapter_slug . '/';
            break;
        }
    }

    if ($next_chapter_url !== '#') {
        wp_send_json_success(array(
            'next_chapter_url' => $next_chapter_url
        ));
    } else {
        $fallback_url = get_site_url() . '/course/' . $_POST['course_slug'] . '/';
        wp_send_json_error(array(
            'next_chapter_url' => $fallback_url
        ));
    }

    wp_die();
}
add_action('wp_ajax_as_mark_complete_chapter', 'as_mark_complete_chapter');
add_action('wp_ajax_nopriv_as_mark_complete_chapter', 'as_mark_complete_chapter');

// Remove the "Add New" submenu under the custom post type
function as_remove_add_new_course_menu()
{
    $post_type = 'course';
    remove_submenu_page('edit.php?post_type=' . $post_type, 'post-new.php?post_type=' . $post_type);
}
add_action('admin_menu', 'as_remove_add_new_course_menu', 999);

// Remove the automatically added submenu pages
function as_reorder_courses_submenu()
{
    remove_submenu_page('edit.php?post_type=course', 'edit.php?post_type=lessons');
    remove_submenu_page('edit.php?post_type=course', 'edit.php?post_type=topics');
    remove_submenu_page('edit.php?post_type=course', 'edit.php?post_type=chapters');
    remove_submenu_page('edit.php?post_type=course', 'edit.php?post_type=sections');
    remove_submenu_page('edit.php?post_type=course', 'edit.php?post_type=quiz');
    remove_submenu_page('edit.php?post_type=course', 'edit.php?post_type=question-bank');
    remove_submenu_page('edit.php?post_type=course', 'enrollment-history');
    remove_submenu_page('edit.php?post_type=course', 'transaction-history');

    // Manually add them in the desired order
    add_submenu_page('edit.php?post_type=course', 'Chapters', 'Chapters', 'manage_options', 'edit.php?post_type=chapters');
    add_submenu_page('edit.php?post_type=course', 'Lessons', 'Lessons', 'manage_options', 'edit.php?post_type=lessons');
    add_submenu_page('edit.php?post_type=course', 'Topics', 'Topics', 'manage_options', 'edit.php?post_type=topics');
    add_submenu_page('edit.php?post_type=course', 'Sections', 'Sections', 'manage_options', 'edit.php?post_type=sections');
    add_submenu_page('edit.php?post_type=course', 'Quiz', 'Quiz', 'manage_options', 'edit.php?post_type=quiz');
    add_submenu_page('edit.php?post_type=course', 'Question Bank', 'Question Bank', 'manage_options', 'edit.php?post_type=question-bank');
    add_submenu_page('edit.php?post_type=course', 'Enrollment History', 'Enrollment History', 'manage_options', 'enrollment-history');
    add_submenu_page('edit.php?post_type=course', 'Transaction History', 'Transaction History', 'manage_options', 'transaction-history');
}
add_action('admin_menu', 'as_reorder_courses_submenu', 99);

// as_create_learnmore_quiz_user_scores create
function as_create_learnmore_quiz_user_scores()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'as_quiz_user_scores';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        quiz_id BIGINT(20) UNSIGNED NOT NULL,
        course_id BIGINT(20) UNSIGNED NOT NULL,
        selected_question_answers VARCHAR(200) NOT NULL,
        time_taken VARCHAR(50) NOT NULL,
        PRIMARY KEY  (id),
        INDEX (user_id),
        INDEX (quiz_id),
        INDEX (course_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'as_create_learnmore_quiz_user_scores');
