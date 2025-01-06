<?php
// This function use for topics quiz
function get_lesson_navigation_urls($path_array)
{
    global $wp;

    // Initialize variables
    $show_lesson_previous = false;
    $show_lesson_next = false;
    $previous_lesson_url = '#';
    $next_lesson_url = '#';
    $current_lesson_outside_chapter_url = '#';
    $lesson_course_id = '#';
    $lesson_chapter_id = '#';
    $lesson_lesson_id = '#';

    // Get course details from URL
    $course = get_page_by_path($path_array[2], OBJECT, 'course');
    if (!$course) {
        return compact('show_lesson_previous', 'show_lesson_next', 'previous_lesson_url', 'next_lesson_url', 'current_lesson_outside_chapter_url', 'lesson_course_id', 'lesson_chapter_id', 'lesson_lesson_id');
    }
    $lesson_course_id = $course->ID;
    $course_slug = $course->post_name;

    $course_dataes = get_post_meta($lesson_course_id, 'course_data', true);

    foreach ($course_dataes as $chapter_index => $course_data) {
        $lesson_dataes = $course_data['lessons'];
        $lesson_chapter_id = $course_data['chapter_id'];
        $chapter_meta_slug = get_post_field('post_name', $course_data['chapter_id']);

        foreach ($lesson_dataes as $lesson_index => $lesson_data) {
            $topic_dataes = $lesson_data['topics'];
            $lesson_lesson_id = $lesson_data['lesson_id'];
            $lesson_meta_slug = get_post_field('post_name', $lesson_data['lesson_id']);
            $quiz_ids = $lesson_data['quiz_id'] ?? [];

            if ($lesson_meta_slug == $path_array[6]) {

                foreach ($quiz_ids as $quiz_index => $quiz_id) {

                    if ($lesson_index > 0 || $quiz_index > 0 || $lesson_dataes[0] || $lesson_data['quiz_id'][0]) {
                        if (in_array($quiz_id, $lesson_data['quiz_id'])) {
                            $lessonId = $lesson_data['lesson_id'];
                            $previous_lesson_slug = get_post_field('post_name', $lessonId);
                            $previous_lesson_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $previous_lesson_slug . '/';
                            $show_lesson_previous = true;
                        }
                    }

                    if ($lesson_index < count($lesson_dataes) - 1 || $quiz_index < count($quiz_ids) - 1 || !empty($lesson_data['quiz_id'])) {
                        if (isset($lesson_dataes['quiz_id'][$quiz_index + 1])) {
                            $next_quiz_id = $lesson_dataes['quiz_id'][$quiz_index + 1];
                            $next_quiz_slug = get_post_field('post_name', $next_quiz_id);
                            $next_lesson_url  = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/quiz/' . $next_quiz_slug . '/';
                            $show_lesson_next = true;
                        } else if (isset($lesson_dataes[$lesson_index + 1]['lesson_id'])) {
                            $next_lesson_id = $topic_dataes[$lesson_index + 1]['lesson_id'];
                            $next_lesson_slug = get_post_field('post_name', $next_lesson_id);
                            $next_lesson_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug .  '/';
                            $show_lesson_next = true;
                        }
                    }
                }

                $current_lesson_outside_chapter_url  = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/';

                return compact('show_lesson_previous', 'show_lesson_next', 'previous_lesson_url', 'next_lesson_url', 'current_lesson_outside_chapter_url', 'lesson_course_id', 'lesson_chapter_id', 'lesson_lesson_id');
            }
        }
    }

    // Default return if no matching section is found
    return compact('show_lesson_previous', 'show_lesson_next', 'previous_lesson_url', 'next_lesson_url', 'current_lesson_outside_chapter_url', 'lesson_course_id', 'lesson_chapter_id', 'lesson_lesson_id');
}
// This function use for topics quiz
function get_topics_navigation_urls($path_array)
{
    global $wp;

    // Initialize variables
    $show_topic_previous = false;
    $show_topic_next = false;
    $previous_topic_url = '#';
    $next_topic_url = '#';
    $current_topic_outside_lesson_url = '#';
    $topic_course_id = '#';
    $topic_chapter_id = '#';
    $topic_lesson_id = '#';
    $topic_topic_id = '#';

    // Get course details from URL
    $course = get_page_by_path($path_array[2], OBJECT, 'course');
    if (!$course) {
        return compact('show_topic_previous', 'show_topic_next', 'previous_topic_url', 'next_topic_url', 'current_topic_outside_lesson_url', 'topic_course_id', 'topic_chapter_id', 'topic_topic_id', 'topic_lesson_id');
    }
    $topic_course_id = $course->ID;
    $course_slug = $course->post_name;

    $course_dataes = get_post_meta($topic_course_id, 'course_data', true);

    foreach ($course_dataes as $chapter_index => $course_data) {
        $lesson_dataes = $course_data['lessons'];
        $topic_chapter_id = $course_data['chapter_id'];
        $chapter_meta_slug = get_post_field('post_name', $course_data['chapter_id']);

        foreach ($lesson_dataes as $lesson_index => $lesson_data) {
            $topic_dataes = $lesson_data['topics'];
            $topic_lesson_id = $lesson_data['lesson_id'];
            $lesson_meta_slug = get_post_field('post_name', $lesson_data['lesson_id']);

            foreach ($topic_dataes as $topic_index => $topic_data) {
                $topic_topic_id = $topic_data['topic_id'];
                $topic_meta_slug = get_post_field('post_name', $topic_data['topic_id']);
                $quiz_ids = $topic_data['quiz_id'] ?? [];

                if ($topic_meta_slug == $path_array[8]) {

                    foreach ($quiz_ids as $quiz_index => $quiz_id) {

                        if ($topic_index > 0 || $quiz_index > 0 || $topic_dataes[0] || $topic_data['quiz_id'][0]) {
                            if (in_array($quiz_id, $topic_data['quiz_id'])) {
                                $topicId = $topic_data['topic_id'];
                                $previous_topic_slug = get_post_field('post_name', $topicId);
                                $previous_topic_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $previous_topic_slug . '/';
                                $show_topic_previous = true;
                            }
                        }

                        if ($topic_index < count($topic_dataes) - 1 || $quiz_index < count($quiz_ids) - 1 || !empty($topic_data['quiz_id'])) {
                            if (isset($topic_dataes['quiz_id'][$quiz_index + 1])) {
                                $next_quiz_id = $topic_dataes['quiz_id'][$quiz_index + 1];
                                $next_quiz_slug = get_post_field('post_name', $next_quiz_id);
                                $next_topic_url  = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/quiz/' . $next_quiz_slug . '/';
                                $show_topic_next = true;
                            } else if (isset($section_dataes[$topic_index + 1]['topic_id'])) {
                                $next_topic_id = $topic_dataes[$topic_index + 1]['topic_id'];
                                $next_topic_slug = get_post_field('post_name', $next_topic_id);
                                $next_topic_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $next_topic_slug .  '/';
                                $show_topic_next = true;
                            }
                        }
                    }

                    $current_topic_outside_lesson_url  = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/';

                    return compact('show_topic_previous', 'show_topic_next', 'previous_topic_url', 'next_topic_url', 'current_topic_outside_lesson_url', 'topic_course_id', 'topic_chapter_id', 'topic_topic_id', 'topic_lesson_id');
                }
            }
        }
    }

    // Default return if no matching section is found
    return compact('show_topic_previous', 'show_topic_next', 'previous_topic_url', 'next_topic_url', 'current_topic_outside_lesson_url', 'topic_course_id', 'topic_chapter_id', 'topic_topic_id', 'topic_lesson_id');
}

// This function use for section quiz
function get_section_navigation_urls($path_array)
{
    global $wp;

    // Initialize variables
    $show_previous = false;
    $show_next = false;
    $previous_section_url = '#';
    $next_section_url = '#';
    $current_section_outside_topic_url = '#';
    $course_id = '#';
    $chapter_id = '#';
    $lesson_id = '#';
    $topic_id = '#';
    $section_id = '#';

    // Get course details from URL
    $course = get_page_by_path($path_array[2], OBJECT, 'course');
    if (!$course) {
        return compact('show_previous', 'show_next', 'previous_section_url', 'next_section_url', 'current_section_outside_topic_url', 'course_id', 'chapter_id', 'topic_id', 'lesson_id', 'section_id');
    }
    $course_id = $course->ID;
    $course_slug = $course->post_name;

    $course_dataes = get_post_meta($course_id, 'course_data', true);

    foreach ($course_dataes as $chapter_index => $course_data) {
        $lesson_dataes = $course_data['lessons'];
        $chapter_id = $course_data['chapter_id'];
        $chapter_meta_slug = get_post_field('post_name', $course_data['chapter_id']);

        foreach ($lesson_dataes as $lesson_index => $lesson_data) {
            $topic_dataes = $lesson_data['topics'];
            $lesson_id = $lesson_data['lesson_id'];
            $lesson_meta_slug = get_post_field('post_name', $lesson_data['lesson_id']);

            foreach ($topic_dataes as $topic_index => $topic_data) {
                $section_dataes = $topic_data['sections'];
                $topic_id = $topic_data['topic_id'];
                $topic_meta_slug = get_post_field('post_name', $topic_data['topic_id']);

                foreach ($section_dataes as $section_index => $section_data) {
                    $section_id = $section_data['section_id'];
                    $section_meta_slug = get_post_field('post_name', $section_data['section_id']);
                    $quiz_ids = $section_data['quiz_id'] ?? [];

                    if ($section_meta_slug == $path_array[10]) {

                        foreach ($quiz_ids as $quiz_index => $quiz_id) {

                            if ($section_index > 0 || $quiz_index > 0 || $section_dataes[0] || $section_data['quiz_id'][0]) {
                                if (in_array($quiz_id, $section_data['quiz_id'])) {
                                    $sectionId = $section_data['section_id'];
                                    $previous_section_slug = get_post_field('post_name', $sectionId);
                                    $previous_section_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $previous_section_slug . '/';
                                    $show_previous = true;
                                }
                            }

                            if ($section_index < count($section_dataes) - 1 || $quiz_index < count($quiz_ids) - 1 || !empty($section_data['quiz_id'])) {
                                if (isset($section_dataes['quiz_id'][$quiz_index + 1])) {
                                    $next_quiz_id = $section_dataes['quiz_id'][$quiz_index + 1];
                                    $next_quiz_slug = get_post_field('post_name', $next_quiz_id);
                                    $next_section_url  = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $section_meta_slug . '/quiz/' . $next_quiz_slug . '/';
                                    $show_next = true;
                                } else if (isset($section_dataes[$section_index + 1]['section_id'])) {
                                    $next_section_id = $section_dataes[$section_index + 1]['section_id'];
                                    $next_section_slug = get_post_field('post_name', $next_section_id);
                                    $next_section_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $next_section_slug . '/';
                                    $show_next = true;
                                }
                            }
                        }

                        $current_section_outside_topic_url  = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/';

                        return compact('show_previous', 'show_next', 'previous_section_url', 'next_section_url', 'current_section_outside_topic_url', 'course_id', 'chapter_id', 'topic_id', 'lesson_id', 'section_id');
                    }
                }
            }
        }
    }

    // Default return if no matching section is found
    return compact('show_previous', 'show_next', 'previous_section_url', 'next_section_url', 'current_section_outside_topic_url', 'course_id', 'chapter_id', 'topic_id', 'lesson_id', 'section_id');
}

// section quiz progress ajax call 
function as_quiz_ajax_progress_section()
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
    $quiz_id = intval($_POST['quiz_id']);


    $current_time = current_time('mysql');

    global $wpdb;
    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

    // Update or insert activity
    $activity = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d AND chapter_id = %d AND lesson_id = %d AND topic_id = %d AND section_id = %d AND quiz_id = %d AND course_id = %d",
        $user_id,
        $chapter_id,
        $lesson_id,
        $topic_id,
        $section_id,
        $course_id,
        $quiz_id
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
                'quiz_id' => $quiz_id,
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
                'quiz_id' => $quiz_id,
                'course_id' => $course_id,
                'activity_type' => 'section',
                'activity_status' => 'completed',
                'activity_started' => $current_time,
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            )
        );
    }


    wp_send_json_success(array(
        'success' => 'Quiz Completed',
    ));

    wp_die();
}
add_action('wp_ajax_as_quiz_ajax_progress_section', 'as_quiz_ajax_progress_section');
add_action('wp_ajax_nopriv_as_quiz_ajax_progress_section', 'as_quiz_ajax_progress_section');

// topic quiz progress ajax call 
function as_quiz_ajax_progress_topic()
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
    $course_id = intval($_POST['course_id']);
    $quiz_id = intval($_POST['quiz_id']);


    $current_time = current_time('mysql');

    global $wpdb;
    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

    // Update or insert activity
    $activity = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d AND chapter_id = %d AND lesson_id = %d AND topic_id = %d AND quiz_id = %d AND course_id = %d",
        $user_id,
        $chapter_id,
        $lesson_id,
        $topic_id,
        $course_id,
        $quiz_id
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
                'section_id' => 0,
                'course_id' => $course_id,
                'quiz_id' => $quiz_id,
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
                'section_id' => 0,
                'quiz_id' => $quiz_id,
                'course_id' => $course_id,
                'activity_type' => 'section',
                'activity_status' => 'completed',
                'activity_started' => $current_time,
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            )
        );
    }


    wp_send_json_success(array(
        'success' => 'Quiz Completed',
    ));

    wp_die();
}
add_action('wp_ajax_as_quiz_ajax_progress_topic', 'as_quiz_ajax_progress_topic');
add_action('wp_ajax_nopriv_as_quiz_ajax_progress_topic', 'as_quiz_ajax_progress_topic');

// lesson quiz progress ajax call 
function as_quiz_ajax_progress_lesson()
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
    $course_id = intval($_POST['course_id']);
    $quiz_id = intval($_POST['quiz_id']);


    $current_time = current_time('mysql');

    global $wpdb;
    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

    // Update or insert activity
    $activity = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d AND chapter_id = %d AND lesson_id = %d AND topic_id = %d AND quiz_id = %d AND course_id = %d",
        $user_id,
        $chapter_id,
        $lesson_id,
        $course_id,
        $quiz_id
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
                'topic_id' => 0,
                'section_id' => 0,
                'course_id' => $course_id,
                'quiz_id' => $quiz_id,
            )
        );
    } else {
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'chapter_id' => $chapter_id,
                'lesson_id' => $lesson_id,
                'topic_id' => 0,
                'section_id' => 0,
                'quiz_id' => $quiz_id,
                'course_id' => $course_id,
                'activity_type' => 'lesson',
                'activity_status' => 'completed',
                'activity_started' => $current_time,
                'activity_completed' => $current_time,
                'activity_updated' => $current_time,
            )
        );
    }


    wp_send_json_success(array(
        'success' => 'Quiz Completed',
    ));

    wp_die();
}
add_action('wp_ajax_as_quiz_ajax_progress_lesson', 'as_quiz_ajax_progress_lesson');
add_action('wp_ajax_nopriv_as_quiz_ajax_progress_lesson', 'as_quiz_ajax_progress_lesson');
