<?php
function as_calculate_course_progress($course_id, $user_id = null)
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    global $wpdb;

    $total_steps = 0;
    $completed_steps = 0;

    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';
    $completedSteps = $wpdb->get_results($wpdb->prepare(
        "SELECT chapter_id, lesson_id, topic_id, section_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
        $user_id,
        $course_id
    ), ARRAY_A);

    $course_dataes = get_post_meta($course_id, 'course_data', true);

    foreach ($course_dataes as $course_data) {
        $total_steps++;
        if (as_is_step_completed($completedSteps, $course_data['chapter_id'], 0, 0, 0)) {
            $completed_steps++;
        }

        $lesson_dataes = $course_data['lessons'];
        foreach ($lesson_dataes as $lesson_data) {
            $total_steps++;
            if (as_is_step_completed($completedSteps, $course_data['chapter_id'], $lesson_data['lesson_id'], 0, 0)) {
                $completed_steps++;
            }

            $topic_dataes = $lesson_data['topics'];
            foreach ($topic_dataes as $topic_data) {
                $total_steps++;
                if (as_is_step_completed($completedSteps, $course_data['chapter_id'], $lesson_data['lesson_id'], $topic_data['topic_id'], 0)) {
                    $completed_steps++;
                }

                $section_dataes = $topic_data['sections'];
                $total_steps += count($section_dataes);
                foreach ($section_dataes as $section_data) {
                    if (as_is_step_completed($completedSteps, $course_data['chapter_id'], $lesson_data['lesson_id'], $topic_data['topic_id'], $section_data['section_id'])) {
                        $completed_steps++;
                    }
                }
            }
        }
    }

    $progress = ($total_steps > 0) ? ($completed_steps / $total_steps) * 100 : 0;


    return [
        'progress' => round($progress),
        'total_steps' => $total_steps,
        'completed_steps' => $completed_steps
    ];
}

function as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, $section_id)
{
    foreach ($completedSteps as $completed_step) {
        if (
            $completed_step['chapter_id'] == $chapter_id &&
            $completed_step['lesson_id'] == $lesson_id &&
            $completed_step['topic_id'] == $topic_id &&
            $completed_step['section_id'] == $section_id
        ) {
            return true;
        }
    }
    return false;
}
