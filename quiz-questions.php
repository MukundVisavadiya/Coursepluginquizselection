<?php

// Register Custom Post Type: Quiz
function as_create_quiz_post_type()
{
    $labels = array(
        'name'                  => _x('Quiz', 'Post Type General Name', 'course_list'),
        'singular_name'         => _x('Quiz', 'Post Type Singular Name', 'course_list'),
        'menu_name'             => __('Quiz', 'course_list'),
        'name_admin_bar'        => __('Quiz', 'course_list'),
        'archives'              => __('Quiz Archives', 'course_list'),
        'attributes'            => __('Quiz Attributes', 'course_list'),
        'parent_item_colon'     => __('Parent Quiz:', 'course_list'),
        'all_items'             => __('Quiz', 'course_list'),
        'add_new_item'          => __('Add New Quiz', 'course_list'),
        'add_new'               => __('Add New Quiz', 'course_list'),
        'new_item'              => __('New Quiz', 'course_list'),
        'edit_item'             => __('Edit Quiz', 'course_list'),
        'update_item'           => __('Update Quiz', 'course_list'),
        'view_item'             => __('View Quiz', 'course_list'),
        'view_items'            => __('View Quiz', 'course_list'),
        'search_items'          => __('Search Quiz', 'course_list'),
        'not_found'             => __('Quiz Not found', 'course_list'),
        'not_found_in_trash'    => __('Quiz Not found in Trash', 'course_list'),
        'featured_image'        => __('Quiz Featured Image', 'course_list'),
        'set_featured_image'    => __('Set Quiz featured image', 'course_list'),
        'remove_featured_image' => __('Remove Quiz featured image', 'course_list'),
        'use_featured_image'    => __('Use as Quiz featured image', 'course_list'),
        'insert_into_item'      => __('Insert into Quiz', 'course_list'),
        'uploaded_to_this_item' => __('Uploaded to this Quiz', 'course_list'),
        'items_list'            => __('Quiz list', 'course_list'),
        'items_list_navigation' => __('Quiz list navigation', 'course_list'),
        'filter_items_list'     => __('Filter Quiz list', 'course_list'),
    );
    $args = array(
        'label'                 => __('Quiz', 'course_list'),
        'description'           => __('Quiz Description', 'course_list'),
        'labels'                => $labels,
        'supports'              => array('title'),
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => 'edit.php?post_type=course',
        'menu_position'         => 10,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type('quiz', $args);
}
add_action('init', 'as_create_quiz_post_type');

// Register Custom Post Type: Question-Bank
function as_create_question_bank_post_type()
{
    $labels = array(
        'name'                  => _x('Question Bank', 'Post Type General Name', 'course_list'),
        'singular_name'         => _x('Question Bank', 'Post Type Singular Name', 'course_list'),
        'menu_name'             => __('Question Bank', 'course_list'),
        'name_admin_bar'        => __('Question Bank', 'course_list'),
        'archives'              => __('Question Bank Archives', 'course_list'),
        'attributes'            => __('Question Bank Attributes', 'course_list'),
        'parent_item_colon'     => __('Parent Question Bank:', 'course_list'),
        'all_items'             => __('Question Bank', 'course_list'),
        'add_new_item'          => __('Add New Question Bank', 'course_list'),
        'add_new'               => __('Add New Question Bank', 'course_list'),
        'new_item'              => __('New Question Bank', 'course_list'),
        'edit_item'             => __('Edit Question Bank', 'course_list'),
        'update_item'           => __('Update Question Bank', 'course_list'),
        'view_item'             => __('View Question Bank', 'course_list'),
        'view_items'            => __('View Question Bank', 'course_list'),
        'search_items'          => __('Search Question Bank', 'course_list'),
        'not_found'             => __('Question Bank Not found', 'course_list'),
        'not_found_in_trash'    => __('Question Bank Not found in Trash', 'course_list'),
        'featured_image'        => __('Question Bank Featured Image', 'course_list'),
        'set_featured_image'    => __('Set Question Bank featured image', 'course_list'),
        'remove_featured_image' => __('Remove Question Bank featured image', 'course_list'),
        'use_featured_image'    => __('Use as Question Bank featured image', 'course_list'),
        'insert_into_item'      => __('Insert into Question Bank', 'course_list'),
        'uploaded_to_this_item' => __('Uploaded to this Question Bank', 'course_list'),
        'items_list'            => __('Question Bank list', 'course_list'),
        'items_list_navigation' => __('Question Bank list navigation', 'course_list'),
        'filter_items_list'     => __('Filter Question Bank list', 'course_list'),
    );
    $args = array(
        'label'                 => __('Question Bank', 'course_list'),
        'description'           => __('Question Bank Description', 'course_list'),
        'labels'                => $labels,
        'supports'              => array('title'),
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => 'edit.php?post_type=course',
        'menu_position'         => 11,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type('question-bank', $args);
}
add_action('init', 'as_create_question_bank_post_type');

// Inside Question Bank to create Answer List
function as_create_clts_question_bank_create_answer_meta_boxes()
{
    add_meta_box('as_clts_question_bank_answer_repeater_field', 'Answer Single Choice/Multiple Choice', 'as_clts_question_bank_answer_repeater_field', 'question-bank');
}
add_action('add_meta_boxes', 'as_create_clts_question_bank_create_answer_meta_boxes');



function as_clts_question_bank_answer_repeater_field($post)
{
    $question_bank_render_data = get_post_meta($post->ID, 'as_question_bank_data', true);
    $render_data = json_decode($question_bank_render_data, true);
?>
    <div class="as-answer-container">
        <div class="as-answer-choose-option">
            <label for="single-option">
                <input id="single-option" type="radio" name="question_type" value="single" checked <?php checked(isset($render_data['question_type']) ? $render_data['question_type'] : '', 'single'); ?>> Single Choice Answer
            </label>
            <label for="multiple-option">
                <input id="multiple-option" type="radio" name="question_type" value="multiple" <?php checked(isset($render_data['question_type']) ? $render_data['question_type'] : '', 'multiple'); ?>> Multiple Choice Answer
            </label>
        </div>
        <div class="as-answer-wrapper">
            <div class="as-answer-list" id="sortable-answer-list">
                <?php
                if (!empty($render_data['answers'])) {
                    foreach ($render_data['answers'] as $index => $answer) {
                ?>
                        <div class="as-answer-form-item">
                            <div class="as-answer-form">
                                <input type="text" placeholder="Answer" name="as_question[<?php echo $index; ?>]" value="<?php echo $answer['answer']; ?>" class="input-field as-question-answer" required>
                                <label for="option-<?php echo $index; ?>">
                                    <input id="option-<?php echo $index; ?>" type="<?php echo $render_data['question_type'] === 'single' ? 'radio' : 'checkbox'; ?>" name="<?php echo $render_data['question_type'] === 'single' ? 'single_answer' : 'multiple_answer[]'; ?>" class="as-choice-option <?php echo $render_data['question_type'] === 'single' ? 'as-single-choice' : 'as-multiple-choice'; ?>" <?php checked($answer['correct'], true); ?> value="<?php echo $index; ?>" />
                                    Correct
                                </label>
                            </div>
                            <input type="button" class="as-remove-button" value="Remove Answer" />
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="as-answer-form-item">
                        <div class="as-answer-form">
                            <input type="text" placeholder="Answer" name="as_question[0]" class="input-field as-question-answer" required>
                            <label for="answer-0">
                                <input id="answer-0" type="radio" name="single_answer" class="as-choice-option as-single-choice" value="0" /> Correct
                            </label>
                        </div>
                        <input type="button" class="as-remove-button" value="Remove Answer" />
                    </div>
                <?php } ?>
            </div>
            <input class="as-add-button" id="as-add-button" type="button" value="Add Answer" />
            <div class="as-dummy-answer-form-item as-answer-form-item as-d-none">
                <div class="as-answer-form">
                    <input type="text" placeholder="Answer" class="input-field as-question-answer">
                    <label id="answer-repeater-label">
                        <input type="radio" name="single_answer" class="as-choice-option as-single-choice" /> Correct
                    </label>
                </div>
                <input type="button" class="as-remove-button" value="Remove Answer" />
            </div>
        </div>
    </div>
<?php }

// save querstion bank data
function as_save_question_bank_data($post_id)
{
    if (get_post_type($post_id) !== 'question-bank') {
        return;
    }

    if (isset($_POST['as_question']) && isset($_POST['question_type'])) {
        $question_type = sanitize_text_field($_POST['question_type']);

        $answers = [];
        foreach ($_POST['as_question'] as $index => $answer) {

            $is_correct = false;

            if ($question_type == 'single') {
                if (isset($_POST['single_answer']) && $_POST['single_answer'] == $index) {
                    $is_correct = true;
                }
            } elseif ($question_type == 'multiple') {

                if (isset($_POST['multiple_answer']) && is_array($_POST['multiple_answer']) && in_array($index, $_POST['multiple_answer'])) {
                    $is_correct = true;
                }
            }

            $answers[] = [
                'answer'  => $answer,
                'correct' => $is_correct,
            ];
        }

        $data = [
            'question_type' => $question_type,
            'answers'       => $answers,
        ];

        update_post_meta($post_id, 'as_question_bank_data', wp_json_encode($data));
    }
}
add_action('save_post', 'as_save_question_bank_data');

// preview button hide in question bank post type
function as_hide_publishing_actions()
{
    $my_post_type = 'question-bank';
    global $post;
    if ($post->post_type == $my_post_type) {
        echo '
             <style type="text/css">
                #edit-slug-box,
                #minor-publishing-actions{
                     display:none;
                }
             </style>
           ';
    }
}
add_action('admin_head-post.php', 'as_hide_publishing_actions');
add_action('admin_head-post-new.php', 'as_hide_publishing_actions');

// Update popup hide
function as_hide_post_updated_message()
{
    echo '<style>
        .updated.notice {
            display: none;
        }
    </style>';
}
add_action('admin_head', 'as_hide_post_updated_message');

// Inside Question Bank to create Answer List
function as_create_clts_create_quiz_meta_boxes()
{
    add_meta_box('as_clts_question_quiz_creation', 'Quiz Question Selection', 'as_clts_quiz_cretion', 'quiz');
}
add_action('add_meta_boxes', 'as_create_clts_create_quiz_meta_boxes');

function as_clts_quiz_cretion()
{ ?>
    <div class="as-quiz-question-search-wrapper">
        <select class="as-quiz-question-search-input form-control" data-placeholder="Select a Questions......." name="question" style="width:100%;">
        </select>
    </div>
<?php }

// search ajax call
function as_quiz_question_search()
{
    if (isset($_POST['search_query'])) {
        $search_query = sanitize_text_field($_POST['search_query']);

        $args = array(
            'post_type' => 'question-bank',
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

add_action('wp_ajax_as_quiz_question_search', 'as_quiz_question_search');
add_action('wp_ajax_nopriv_as_quiz_question_search', 'as_quiz_question_search');

// Selected Questiones list
function as_create_clts_selected_question_meta_boxes()
{
    add_meta_box('as_clts_selected_question_creation', 'Selected Question List', 'as_clts_selected_question_creation', 'quiz');
}
add_action('add_meta_boxes', 'as_create_clts_selected_question_meta_boxes');

function as_clts_selected_question_creation($post)
{
    $selected_questions = get_post_meta($post->ID, 'as_quiz_questions_and_points', true);

    if (!is_array($selected_questions)) {
        $selected_questions = [];
    }
?>
    <input class="as_append_question" type="hidden" value="<?php echo implode(',', array_column($selected_questions, 'question')); ?>" />
    <div class="as-selected-questions-wrapper">
        <ul id="as-selected-questions-list">
            <?php foreach ($selected_questions as $index => $item) {
                $question_id = $item['question'];
                $question_title = get_the_title($question_id);
                $as_question_point = isset($item['points']) ? esc_attr($item['points']) : ''; ?>
                <li data-id="<?php echo esc_attr($question_id); ?>">
                    <div class="as-question-wrapper">
                        <?php echo esc_html($question_title); ?>
                        <a href="#" class="remove-question">Remove</a>
                        <input type="hidden" name="as_selected_questions[<?php echo $index; ?>]" value="<?php echo esc_attr($question_id); ?>" />
                    </div>
                    <div class="as_question_point_list">
                        <input type="number" id="as_point" placeholder="point" name="as_point_question[<?php echo $index; ?>]" min="1" value="<?php echo $as_question_point; ?>">
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php
}

// Save selected questions 
function as_save_quiz_questions_meta($post_id)
{
    if (get_post_type($post_id) == 'quiz') {

        if (isset($_POST['as_selected_questions']) && is_array($_POST['as_selected_questions']) && isset($_POST['as_point_question']) && is_array($_POST['as_point_question'])) {

            $selected_questions = array_map('sanitize_text_field', $_POST['as_selected_questions']);
            $points = array_map('sanitize_text_field', $_POST['as_point_question']);

            $questions_and_points = [];
            foreach ($selected_questions as $index => $question_id) {
                if (!empty($question_id) || isset($points[$index]) || is_numeric($points[$index])) {
                    $questions_and_points[] = [
                        'question' => $question_id,
                        'points'   => intval($points[$index])
                    ];
                }
            }

            update_post_meta($post_id, 'as_quiz_questions_and_points', $questions_and_points);
        } else {
            update_post_meta($post_id, 'as_quiz_questions_and_points', []);
        }
    }
}
add_action('save_post', 'as_save_quiz_questions_meta');


// quiz submit data ajax response
function as_handle_quiz_submission()
{

    if (!isset($_POST['quiz_data'], $_POST['quiz_id'])) {
        wp_send_json_error(['message' => 'Invalid data received.']);
    }

    $score = 0;
    $total_points = 0;
    $feedback = [];

    // Store User Selected Data in database
    global $wpdb;

    $table_name = $wpdb->prefix . 'as_quiz_user_scores';

    $quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;

    // This course id is not come in ajax this add in future
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

    $quiz_data = isset($_POST['quiz_data']) ? $_POST['quiz_data'] : array();

    if ($quiz_id && !empty($quiz_data)) {
        foreach ($quiz_data as $data) {
            $user_id = intval($data['user_id']);
            $question_id = intval($data['question_id']);
            $selected_answers_data = array(
                'question_id' => $question_id,
                'selected_answers' => $data['selected_answers']
            );
            $serialized_selected_answers = maybe_serialize($selected_answers_data);
            $time_taken = sanitize_text_field($data['time_taken']);

            $existing_record = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND quiz_id = %d",
                $user_id,
                $quiz_id,
            ));

            if ($existing_record) {
                $wpdb->update(
                    $table_name,
                    array(
                        'selected_question_answers' => $serialized_selected_answers,
                        'time_taken' => $time_taken
                    ),
                    array(
                        'user_id' => $user_id,
                        'quiz_id' => $quiz_id,
                        'course_id' => $course_id
                    ),
                    array('%s', '%s'),
                    array('%d', '%d', '%d')
                );
            } else {
                $wpdb->insert(
                    $table_name,
                    array(
                        'user_id' => $user_id,
                        'quiz_id' => $quiz_id,
                        'course_id' => $course_id,
                        'selected_question_answers' => $serialized_selected_answers,
                        'time_taken' => $time_taken
                    ),
                    array('%d', '%d', '%d', '%s', '%s')
                );
            }
        }
    }

    // Retrieve correct answers and points from post meta
    $questions_and_points = get_post_meta($quiz_id, 'as_quiz_questions_and_points', true);
    foreach ($quiz_data as $question) {
        $question_id = intval($question['question_id']);
        $selected_answers = $question['selected_answers'];

        // Retrieve question's point value by matching the question ID
        $matched_question = array_filter($questions_and_points, function ($q) use ($question_id) {
            return intval($q['question']) === $question_id;
        });

        if (!empty($matched_question)) {
            $matched_question = array_values($matched_question)[0];
            $question_points = intval($matched_question['points']);
        } else {
            $question_points = 1;
        }

        $total_points += $question_points;

        // Get correct answers for this question
        $question_meta = get_post_meta($question_id, 'as_question_bank_data', true);
        $correct_answers = json_decode($question_meta, true)['answers'];

        $quiz_slug = get_post_field('post_name', $quiz_id);

        $is_correct = true;
        $correct_answer_keys = [];
        $selected_answer_texts = [];
        $feedback_details = [
            'question' => get_the_title($question_id),
            'selected_answers' => [],
            'correct_answers' => [],
            'is_correct' => false,
            'points' => 0,
        ];

        // Collect the correct answer keys
        foreach ($correct_answers as $key => $answer) {
            if ($answer['correct']) {
                $correct_answer_keys[] = $answer['answer'];
                $feedback_details['correct_answers'][] = $answer['answer'];
            }
        }

        foreach ($selected_answers as $selected_key) {
            if (isset($correct_answers[$selected_key])) {
                $selected_answer_texts[] = $correct_answers[$selected_key]['answer'];
                $feedback_details['selected_answers'][] = $correct_answers[$selected_key]['answer'];
            }
        }

        // Compare selected answers with correct answers
        if (array_diff($correct_answer_keys, $selected_answer_texts) || array_diff($selected_answer_texts, $correct_answer_keys)) {
            $is_correct = false;
        }

        // Update score if the answer is correct
        if ($is_correct) {
            $score += $question_points;
            $feedback_details['points'] = $question_points;
        }

        $feedback_details['is_correct'] = $is_correct;
        $feedback[] = $feedback_details;
    }

    // Calculate percentage score
    $percentage_score = ($total_points > 0) ? ($score / $total_points) * 100 : 0;
    $percentage = substr($percentage_score, 0, 5);

    $data = array(
        'message' => "Your Percentage: $percentage%",
        'score' => $score,
        'total_points' => $total_points,
        'feedback' => $feedback,
        'time_taken' => $time_taken
    );

    $json_data = json_encode($data);

    update_post_meta($quiz_id, 'user_quiz_score_data', $json_data);

    wp_send_json_success([
        'message' => "You Percentage:  $percentage%",
        'score' => $score,
        'total_points' => $total_points,
        'feedback' => $feedback,
    ]);
}

add_action('wp_ajax_submit_quiz', 'as_handle_quiz_submission');
add_action('wp_ajax_nopriv_submit_quiz', 'as_handle_quiz_submission');

// Selected Questiones list
function as_create_clts_total_time_taken_meta_boxes()
{
    add_meta_box('as_clts_create_total_time_taken', 'Quiz Total Time', 'as_clts_total_time_field', 'quiz', 'side');
}
add_action('add_meta_boxes', 'as_create_clts_total_time_taken_meta_boxes');

function as_clts_total_time_field($post)
{
    $quiz_completion_time = get_post_meta($post->ID, 'as_quiz_completion_time', true);
?>
    <div class="as-quiz-time-wrapper">
        <label for="quiz-completion-time" class="as-quiz-time-label">Total Quiz Completion Time (in minutes):</label>
        <input type="number" id="quiz-completion-time" name="quiz-completion-time" class="as-quiz-time-input" placeholder="Enter time" value="<?php echo $quiz_completion_time ?>">
    </div>
<?php
}

function as_save_quiz_completion_time_meta($post_id)
{
    if (isset($_POST['quiz-completion-time'])) {
        update_post_meta($post_id, 'as_quiz_completion_time', $_POST['quiz-completion-time']);
    }
}
add_action('save_post', 'as_save_quiz_completion_time_meta');
