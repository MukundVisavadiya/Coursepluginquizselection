<?php
// quiz selection search ajax call
function as_quiz_selection_in_course()
{
    if (isset($_POST['search_query'])) {
        $search_query = sanitize_text_field($_POST['search_query']);

        $args = array(
            'post_type' => 'quiz',
            'posts_per_page' => -1,
            's' => $search_query,
        );

        $query = new WP_Query($args);
        $results = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title()
                );
            }
            wp_reset_postdata();
        }
        echo json_encode($results);
    } else {
        echo json_encode(array('error' => 'No search query provided'));
    }

    wp_die();
}

add_action('wp_ajax_as_quiz_selection_in_course', 'as_quiz_selection_in_course');
add_action('wp_ajax_nopriv_as_quiz_selection_in_course', 'as_quiz_selection_in_course');
