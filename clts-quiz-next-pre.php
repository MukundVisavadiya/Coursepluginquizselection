<?php
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
