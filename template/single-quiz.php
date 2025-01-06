<?php
get_header();

/**
 * Course Sidebar
 */
// require_once dirname(__FILE__)  . '/../sidebar.php';

if (have_posts()) :
    while (have_posts()) : the_post();
        $quiz_title = get_the_title();
        $quiz_id = get_the_ID();
        $quiz_dataes = get_post_meta($quiz_id, 'as_quiz_questions_and_points', true);
        $user_ID = get_current_user_id();
        $current_url = home_url(add_query_arg(array(), $wp->request));
        $parsed_url = parse_url($current_url);
        $path_array = explode('/', trim($parsed_url['path'], characters: '/'));
        $navigation_data = get_section_navigation_urls($path_array);
        extract($navigation_data);
        $topic_navigation_data = get_topics_navigation_urls($path_array);
        extract($topic_navigation_data);
        $lesson_navigation_data = get_lesson_navigation_urls($path_array);
        extract($lesson_navigation_data);
        global $wpdb;

        // progressbar logic
        $table_name = $wpdb->prefix . 'as_learnmore_user_activity';
        $completedSteps = $wpdb->get_results($wpdb->prepare(
            "SELECT chapter_id, lesson_id, topic_id, section_id, quiz_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
            $user_ID,
            $course_id
        ), ARRAY_A);

        $progress_data = as_calculate_course_progress($course_id, $user_ID);
?>
        <!-- comman hidden field -->
        <input type="hidden" class="as_quiz_id" value="<?php echo $quiz_id ?>" />
        <input type="hidden" class="as_quiz_user_id" value="<?php echo $user_ID  ?>" />

        <div class="as-course-container">
            <!-- This progress add total steps & completed steps -->
            <div class="as-course-progressbar">
                <p><?php echo $progress_data['progress']; ?>% Completed <?php echo $progress_data['completed_steps']; ?>/<?php echo $progress_data['total_steps']; ?> Steps</p>
                <div class="as-progress-bar">
                    <div class="as-progress-bar-fill" style="width: <?php echo $progress_data['progress']; ?>%;"></div>
                </div>
            </div>
        </div>

        <?php
        if ($path_array[9] == 'sections') {
        ?>
            <!-- section hidden field data -->
            <input type="hidden" class="as-next-section-quiz-url" value="<?php echo $next_section_url; ?>" />
            <input type="hidden" class="as-previous-section-quiz-url" value="<?php echo $previous_section_url; ?>" />
            <input type="hidden" class="as-course-id" value="<?php echo $course_id; ?>">
            <input type="hidden" class="as-section-id" value="<?php echo $section_id ?>">
            <input type="hidden" class="as-chapter-id" value="<?php echo $chapter_id; ?>">
            <input type="hidden" class="as-lesson-id" value="<?php echo $lesson_id; ?>">
            <input type="hidden" class="as-topic-id" value="<?php echo $topic_id; ?>">
            <input type="hidden" class="as-show-next" value="<?php echo $show_next; ?>">
            <input type="hidden" class="as-next-topic-url" value="<?php echo  $current_section_outside_topic_url ?>">
            <input type="hidden" class="as-section-path-slug" value="<?php echo $path_array[9] ?>" />
            <?php
            // quiz is not completed so start quiz show otherwise result only show
            if (!as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, $section_id, $quiz_id)) {
                $previousSectionCompleted = false;

                $previous_section = $previous_section_url;
                $parsed_section = parse_url($previous_section);
                $section_path_array = explode('/', trim($parsed_section['path'], characters: '/'));
                $previous_section_slug = $section_path_array[10];
                $previous_section_array = get_page_by_path($previous_section_slug, OBJECT, 'sections');
                $previous_section_id = $previous_section_array->ID;

                $previos_section_completed = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, $previous_section_id, 0);
                if ($previos_section_completed) {
                    $previousSectionCompleted = true;
                }

                if (!$previousSectionCompleted) {
                    echo '<div class="as-quiz-error-message as-alert-error-message">';
                    echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous section.</p>';
                    echo '</div>';
                    echo '<style>
                        .as-quiz-container {
                            display: none;
                        }
                    </style>';
                }
            ?>

                <div class="as-quiz-container">
                    <h4><?php echo esc_html($quiz_title); ?></h4>
                    <?php
                    $quiz_completion_time = get_post_meta(get_the_ID(), 'as_quiz_completion_time', true);
                    if (!empty($quiz_completion_time)) {
                        echo '<p class="as-quiz-completed-time">You complete the quiz in ' . esc_html($quiz_completion_time) . ' minutes..</p>';
                    }
                    ?>
                    <div class="as-quiz-timer"></div>
                    <input type="hidden" value="<?php echo esc_html($quiz_completion_time) ?>" class="as-compete-quiz-time" />
                    <button id="as-start-quiz" class="as-quiz-button">Start Quiz</button>

                    <div class="as-quiz-questions">
                        <?php
                        if (is_array($quiz_dataes) && !empty($quiz_dataes)) {

                            foreach ($quiz_dataes as $index => $question_ids) {
                                $question_id = $question_ids['question'];
                                $question_point = $question_ids['points'];

                                $query = new WP_Query([
                                    'p' => $question_id,
                                    'post_type' => 'question-bank',
                                    'posts_per_page' => 1,
                                ]);

                                $question_post = $query->post;

                                if ($question_post && $question_post->post_type === 'question-bank') {

                                    $question_text = get_the_title($question_post->ID);

                                    $answers = get_post_meta($question_post->ID, 'as_question_bank_data', true);

                                    $quiz_answers_arrays = json_decode($answers, true);

                        ?>
                                    <div class="as-quiz-step" data-step="<?php echo esc_attr($index + 1); ?>" data-question-id="<?php echo $question_post->ID ?>" style="display:<?php echo ($index === 0) ? 'block' : 'none'; ?>;">

                                        <h2><?php echo esc_html($question_text); ?></h2>

                                        <?php if (!empty($quiz_answers_arrays) && is_array($quiz_answers_arrays)) { ?>

                                            <ul class="as-quiz-options">
                                                <?php foreach ($quiz_answers_arrays['answers'] as $key => $answer) { ?>
                                                    <li class="as-answer-selection">
                                                        <label for="question_<?php echo esc_attr($index + 1); ?>_answer_<?php echo esc_attr($key); ?>" class="as-answer-wrapper ">
                                                            <span>
                                                                <?php echo esc_html($answer['answer']); ?>
                                                            </span>
                                                            <?php if ($quiz_answers_arrays['question_type'] === 'single') { ?>
                                                                <input type="radio" name="question_<?php echo esc_attr($index + 1); ?>" value="<?php echo esc_attr($key); ?>" id="question_<?php echo esc_attr($index + 1); ?>_answer_<?php echo esc_attr($key); ?>">
                                                            <?php } else { ?>
                                                                <input type="checkbox" name="question_<?php echo esc_attr($index + 1); ?>[]" value="<?php echo esc_attr($key); ?>" id="question_<?php echo esc_attr($index + 1); ?>_answer_<?php echo esc_attr($key); ?>">
                                                            <?php } ?>
                                                        </label>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } else {
                                            echo '<p>No answers available for this question.</p>';
                                        } ?>

                                        <?php if ($index + 1 < count($quiz_dataes)) { ?>
                                            <div class="as-next-button-wrapper">
                                                <button class="as-next-step" disabled>Next</button>
                                            </div>
                                        <?php } else { ?>
                                            <div class="as-finish-button-wrapper">
                                                <button id="finish-quiz" class="as-finish-quiz-button" data-quiz-url="<?php echo $current_url ?>" disabled>Finish Quiz</button>
                                                <input type="hidden" class="as-quiz-id" value="<?php echo $quiz_id ?>" />
                                            </div>
                                        <?php } ?>
                                    </div>

                            <?php
                                }
                            }
                        } else { ?>
                            <p>No questions found for this quiz.</p>
                        <?php }
                        ?>
                    </div>
                    <div class="as-overlay-wrapper">
                        <div class="as-quiz-result-processing">
                            <h5>Result:</h5>
                            <p>Quiz Complete. Results are Being Recorded</p>
                        </div>
                        <div>
                            <div id="as-overlay">

                            </div>
                        </div>
                    </div>

                    <div class="as-quiz-results">

                    </div>
                    <div class="as-quiz-feedback-results-wrapper">

                    </div>
                </div>
            <?php
            } else {
                $quiz_data_json = get_post_meta($quiz_id, 'user_quiz_score_data', true);
                $quiz_data = json_decode($quiz_data_json, true);
                $feedbackes = $quiz_data['feedback'];
            ?>
                <div class="as-quiz-container">
                    <h4><?php echo esc_html($quiz_title); ?></h4>
                    <div class="as-quiz-results">
                        <div class="as-score-summary">
                            <h3>Results: </h3>
                            <p>Your time: <?php echo $quiz_data['time_taken']; ?></p>
                            <div class="as-persantage-wrapper">
                                <p>You have obtained <b><?php echo $quiz_data['score']; ?></b> out of <b><?php echo $quiz_data['total_points']; ?></b> marks</p>
                                <p> <?php echo $quiz_data['message']; ?></p>
                            </div>
                            <div class="as-review-answers-wrapper">
                                <a class="as-clts-quiz-previous-butt" href="<?php echo $previous_section_url; ?>">&laquo; Previous</a>
                                <a class="as-review-answers" id="as-view-result">See Correct/Wrong</a>
                                <?php if ($show_next == 1) { ?>
                                    <a class="as-clts-quiz-next-butt" href="<?php echo $next_section_url; ?>">Next &raquo; </a>
                                <?php } else { ?>
                                    <a href="<?php echo $current_section_outside_topic_url;
                                                ?>" class="as-current-section-outside-topic-btn">Proceed to Next Topic</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="as-quiz-feedback-results-wrapper">
                        <div class="as-question-feedback">
                            <?php foreach ($feedbackes as $feedback) { ?>
                                <h4 style="padding:10px 0px;"><?php echo $feedback['question'] ?></h4>
                                <ul>
                                    <?php foreach ($feedback['correct_answers'] as $correct_ans) { ?>
                                        <li class="correct-answer" style="color: green; border: 1px solid;padding: 10px;margin-bottom: 10px;"><b>Correct Answer:</b><?php echo $correct_ans ?></li>
                                    <?php } ?>
                                    <?php foreach ($feedback['selected_answers'] as $selected_ans) {
                                        $isCorrect = $feedback['is_correct'];
                                        $class = $isCorrect ? 'correct-answer' : 'wrong-answer';
                                        $color = $isCorrect ? 'green' : 'red';
                                    ?>
                                        <li class="<?php echo $class; ?>" style="color: <?php echo $color; ?>; border: 1px solid;padding: 10px;margin-bottom: 10px;"><b>Your Answer:</b><?php echo $selected_ans ?></li>
                                    <?php } ?>
                                </ul>
                                <p>Points: <?php echo !empty($feedback['points']) ? $feedback['points'] : 0;
                                            ?></p>
                                <p>Result:
                                    <?php if ($feedback['is_correct']) { ?>
                                        <span style="color: green;">Correct</span>
                                    <?php } else { ?>
                                        <span style="color: red;">Incorrect</span>
                                    <?php } ?>
                                </p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else if ($path_array[7] == 'topics') {
            ?>
            <!-- topic hidden field -->
            <input type="hidden" class="as-next-topic-quiz-url" value="<?php echo $next_topic_url; ?>" />
            <input type="hidden" class="as-previous-topic-quiz-url" value="<?php echo $previous_topic_url; ?>" />
            <input type="hidden" class="as-topic-course-id" value="<?php echo $topic_course_id; ?>">
            <input type="hidden" class="as-topic-chapter-id" value="<?php echo $topic_chapter_id; ?>">
            <input type="hidden" class="as-topic-lesson-id" value="<?php echo $topic_lesson_id; ?>">
            <input type="hidden" class="as-topic-topic-id" value="<?php echo $topic_topic_id; ?>">
            <input type="hidden" class="as-show-topic-next" value="<?php echo $show_topic_next; ?>">
            <input type="hidden" class="as-next-lesson-url" value="<?php echo  $current_topic_outside_lesson_url ?>">
            <input type="hidden" class="as-topic-path-slug" value="<?php echo $path_array[7] ?>" />
            <?php
            // quiz is not completed so start quiz show otherwise result only show
            if (!as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, 0, $quiz_id)) {
                $previousTopicCompleted = false;

                $previous_topic = $previous_topic_url;
                $parsed_topic = parse_url($previous_topic);
                $topic_path_array = explode('/', trim($parsed_topic['path'], characters: '/'));
                $previous_topic_slug = $topic_path_array[8];
                $previous_topic_array = get_page_by_path($previous_topic_slug, OBJECT, 'topics');
                $previous_topic_id = $previous_topic_array->ID;

                $previos_topic_completed = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $previous_topic_id, 0, 0);
                if ($previos_topic_completed) {
                    $previousTopicCompleted = true;
                }

                if (!$previousTopicCompleted) {
                    echo '<div class="as-quiz-error-message as-alert-error-message">';
                    echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous topic.</p>';
                    echo '</div>';
                    echo '<style>
                            .as-quiz-container {
                                display: none;
                            }
                        </style>';
                }
            ?>

                <div class="as-quiz-container">
                    <h4><?php echo esc_html($quiz_title); ?></h4>
                    <?php
                    $quiz_completion_time = get_post_meta(get_the_ID(), 'as_quiz_completion_time', true);
                    if (!empty($quiz_completion_time)) {
                        echo '<p class="as-quiz-completed-time">You complete the quiz in ' . esc_html($quiz_completion_time) . ' minutes..</p>';
                    }
                    ?>
                    <div class="as-quiz-timer"></div>
                    <input type="hidden" value="<?php echo esc_html($quiz_completion_time) ?>" class="as-compete-quiz-time" />
                    <button id="as-start-quiz" class="as-quiz-button">Start Quiz</button>

                    <div class="as-quiz-questions">
                        <?php
                        if (is_array($quiz_dataes) && !empty($quiz_dataes)) {

                            foreach ($quiz_dataes as $index => $question_ids) {
                                $question_id = $question_ids['question'];
                                $question_point = $question_ids['points'];

                                $query = new WP_Query([
                                    'p' => $question_id,
                                    'post_type' => 'question-bank',
                                    'posts_per_page' => 1,
                                ]);

                                $question_post = $query->post;

                                if ($question_post && $question_post->post_type === 'question-bank') {

                                    $question_text = get_the_title($question_post->ID);

                                    $answers = get_post_meta($question_post->ID, 'as_question_bank_data', true);

                                    $quiz_answers_arrays = json_decode($answers, true);

                        ?>
                                    <div class="as-quiz-step" data-step="<?php echo esc_attr($index + 1); ?>" data-question-id="<?php echo $question_post->ID ?>" style="display:<?php echo ($index === 0) ? 'block' : 'none'; ?>;">

                                        <h2><?php echo esc_html($question_text); ?></h2>

                                        <?php if (!empty($quiz_answers_arrays) && is_array($quiz_answers_arrays)) { ?>

                                            <ul class="as-quiz-options">
                                                <?php foreach ($quiz_answers_arrays['answers'] as $key => $answer) { ?>
                                                    <li class="as-answer-selection">
                                                        <label for="question_<?php echo esc_attr($index + 1); ?>_answer_<?php echo esc_attr($key); ?>" class="as-answer-wrapper ">
                                                            <span>
                                                                <?php echo esc_html($answer['answer']); ?>
                                                            </span>
                                                            <?php if ($quiz_answers_arrays['question_type'] === 'single') { ?>
                                                                <input type="radio" name="question_<?php echo esc_attr($index + 1); ?>" value="<?php echo esc_attr($key); ?>" id="question_<?php echo esc_attr($index + 1); ?>_answer_<?php echo esc_attr($key); ?>">
                                                            <?php } else { ?>
                                                                <input type="checkbox" name="question_<?php echo esc_attr($index + 1); ?>[]" value="<?php echo esc_attr($key); ?>" id="question_<?php echo esc_attr($index + 1); ?>_answer_<?php echo esc_attr($key); ?>">
                                                            <?php } ?>
                                                        </label>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } else {
                                            echo '<p>No answers available for this question.</p>';
                                        } ?>

                                        <?php if ($index + 1 < count($quiz_dataes)) { ?>
                                            <div class="as-next-button-wrapper">
                                                <button class="as-next-step" disabled>Next</button>
                                            </div>
                                        <?php } else { ?>
                                            <div class="as-finish-button-wrapper">
                                                <button id="finish-quiz" class="as-finish-quiz-button-topic" data-quiz-url="<?php echo $current_url ?>" disabled>Finish Quiz</button>
                                                <input type="hidden" class="as-quiz-id" value="<?php echo $quiz_id ?>" />
                                            </div>
                                        <?php } ?>
                                    </div>

                            <?php
                                }
                            }
                        } else { ?>
                            <p>No questions found for this quiz.</p>
                        <?php }
                        ?>
                    </div>
                    <div class="as-overlay-wrapper">
                        <div class="as-quiz-result-processing">
                            <h5>Result:</h5>
                            <p>Quiz Complete. Results are Being Recorded</p>
                        </div>
                        <div>
                            <div id="as-overlay">

                            </div>
                        </div>
                    </div>

                    <div class="as-quiz-results">

                    </div>
                    <div class="as-quiz-feedback-results-wrapper">

                    </div>
                </div>
            <?php
            } else {
                $quiz_data_json = get_post_meta($quiz_id, 'user_quiz_score_data', true);
                $quiz_data = json_decode($quiz_data_json, true);
                $feedbackes = $quiz_data['feedback'];
            ?>
                <div class="as-quiz-container">
                    <h4><?php echo esc_html($quiz_title); ?></h4>
                    <div class="as-quiz-results">
                        <div class="as-score-summary">
                            <h3>Results: </h3>
                            <p>Your time: <?php echo $quiz_data['time_taken']; ?></p>
                            <div class="as-persantage-wrapper">
                                <p>You have obtained <b><?php echo $quiz_data['score']; ?></b> out of <b><?php echo $quiz_data['total_points']; ?></b> marks</p>
                                <p> <?php echo $quiz_data['message']; ?></p>
                            </div>
                            <div class="as-review-answers-wrapper">
                                <a class="as-clts-quiz-previous-butt" href="<?php echo $previous_topic_url; ?>">&laquo; Previous</a>
                                <a class="as-review-answers" id="as-view-result">See Correct/Wrong</a>
                                <?php if ($show_topic_next == 1) { ?>
                                    <a class="as-clts-quiz-next-butt" href="<?php echo $next_topic_url; ?>">Next &raquo; </a>
                                <?php } else { ?>
                                    <a href="<?php echo $current_topic_outside_lesson_url;
                                                ?>" class="as-current-section-outside-topic-btn">Proceed to Next Lesson</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="as-quiz-feedback-results-wrapper">
                        <div class="as-question-feedback">
                            <?php foreach ($feedbackes as $feedback) { ?>
                                <h4 style="padding:10px 0px;"><?php echo $feedback['question'] ?></h4>
                                <ul>
                                    <?php foreach ($feedback['correct_answers'] as $correct_ans) { ?>
                                        <li class="correct-answer" style="color: green; border: 1px solid;padding: 10px;margin-bottom: 10px;"><b>Correct Answer:</b><?php echo $correct_ans ?></li>
                                    <?php } ?>
                                    <?php foreach ($feedback['selected_answers'] as $selected_ans) {
                                        $isCorrect = $feedback['is_correct'];
                                        $class = $isCorrect ? 'correct-answer' : 'wrong-answer';
                                        $color = $isCorrect ? 'green' : 'red';
                                    ?>
                                        <li class="<?php echo $class; ?>" style="color: <?php echo $color; ?>; border: 1px solid;padding: 10px;margin-bottom: 10px;"><b>Your Answer:</b><?php echo $selected_ans ?></li>
                                    <?php } ?>
                                </ul>
                                <p>Points: <?php echo !empty($feedback['points']) ? $feedback['points'] : 0;
                                            ?></p>
                                <p>Result:
                                    <?php if ($feedback['is_correct']) { ?>
                                        <span style="color: green;">Correct</span>
                                    <?php } else { ?>
                                        <span style="color: red;">Incorrect</span>
                                    <?php } ?>
                                </p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else if ($path_array[5] == 'lessons') {
            ?>
            <!-- topic hidden field -->
            <input type="hidden" class="as-next-lesson-quiz-url" value="<?php echo $next_lesson_url; ?>" />
            <input type="hidden" class="as-previous-lesson-quiz-url" value="<?php echo $previous_lesson_url; ?>" />
            <input type="hidden" class="as-lesson-course-id" value="<?php echo $lesson_course_id; ?>">
            <input type="hidden" class="as-lesson-chapter-id" value="<?php echo $lesson_chapter_id; ?>">
            <input type="hidden" class="as-lesson-lesson-id" value="<?php echo $lesson_lesson_id; ?>">
            <input type="hidden" class="as-show-lesson-next" value="<?php echo $show_lesson_next; ?>">
            <input type="hidden" class="as-next-chapter-url" value="<?php echo  $current_lesson_outside_chapter_url ?>">
            <input type="hidden" class="as-lesson-path-slug" value="<?php echo $path_array[5] ?>" />
            <?php
            // quiz is not completed so start quiz show otherwise result only show
            if (!as_is_step_completed($completedSteps, $chapter_id, $lesson_id, 0, 0, $quiz_id)) {
                $previousLessonCompleted = false;

                $previous_lesson = $previous_lesson_url;
                $parsed_lesson = parse_url($previous_lesson);
                $lesson_path_array = explode('/', trim($parsed_lesson['path'], characters: '/'));
                $previous_lesson_slug = $lesson_path_array[6];
                $previous_lesson_array = get_page_by_path($previous_lesson_slug, OBJECT, 'lessons');
                $previous_lesson_id = $previous_lesson_array->ID;

                $previos_lesson_completed = as_is_step_completed($completedSteps, $chapter_id, $previous_lesson_id, 0, 0, 0);
                if ($previos_lesson_completed) {
                    $previousLessonCompleted = true;
                }

                if (!$previousLessonCompleted) {
                    echo '<div class="as-quiz-error-message as-alert-error-message">';
                    echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous lesson.</p>';
                    echo '</div>';
                    echo '<style>
                            .as-quiz-container {
                                display: none;
                            }
                        </style>';
                }
            ?>

                <div class="as-quiz-container">
                    <h4><?php echo esc_html($quiz_title); ?></h4>
                    <?php
                    $quiz_completion_time = get_post_meta(get_the_ID(), 'as_quiz_completion_time', true);
                    if (!empty($quiz_completion_time)) {
                        echo '<p class="as-quiz-completed-time">You complete the quiz in ' . esc_html($quiz_completion_time) . ' minutes..</p>';
                    }
                    ?>
                    <div class="as-quiz-timer"></div>
                    <input type="hidden" value="<?php echo esc_html($quiz_completion_time) ?>" class="as-compete-quiz-time" />
                    <button id="as-start-quiz" class="as-quiz-button">Start Quiz</button>

                    <div class="as-quiz-questions">
                        <?php
                        if (is_array($quiz_dataes) && !empty($quiz_dataes)) {

                            foreach ($quiz_dataes as $index => $question_ids) {
                                $question_id = $question_ids['question'];
                                $question_point = $question_ids['points'];

                                $query = new WP_Query([
                                    'p' => $question_id,
                                    'post_type' => 'question-bank',
                                    'posts_per_page' => 1,
                                ]);

                                $question_post = $query->post;

                                if ($question_post && $question_post->post_type === 'question-bank') {

                                    $question_text = get_the_title($question_post->ID);

                                    $answers = get_post_meta($question_post->ID, 'as_question_bank_data', true);

                                    $quiz_answers_arrays = json_decode($answers, true);

                        ?>
                                    <div class="as-quiz-step" data-step="<?php echo esc_attr($index + 1); ?>" data-question-id="<?php echo $question_post->ID ?>" style="display:<?php echo ($index === 0) ? 'block' : 'none'; ?>;">

                                        <h2><?php echo esc_html($question_text); ?></h2>

                                        <?php if (!empty($quiz_answers_arrays) && is_array($quiz_answers_arrays)) { ?>

                                            <ul class="as-quiz-options">
                                                <?php foreach ($quiz_answers_arrays['answers'] as $key => $answer) { ?>
                                                    <li class="as-answer-selection">
                                                        <label for="question_<?php echo esc_attr($index + 1); ?>_answer_<?php echo esc_attr($key); ?>" class="as-answer-wrapper ">
                                                            <span>
                                                                <?php echo esc_html($answer['answer']); ?>
                                                            </span>
                                                            <?php if ($quiz_answers_arrays['question_type'] === 'single') { ?>
                                                                <input type="radio" name="question_<?php echo esc_attr($index + 1); ?>" value="<?php echo esc_attr($key); ?>" id="question_<?php echo esc_attr($index + 1); ?>_answer_<?php echo esc_attr($key); ?>">
                                                            <?php } else { ?>
                                                                <input type="checkbox" name="question_<?php echo esc_attr($index + 1); ?>[]" value="<?php echo esc_attr($key); ?>" id="question_<?php echo esc_attr($index + 1); ?>_answer_<?php echo esc_attr($key); ?>">
                                                            <?php } ?>
                                                        </label>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } else {
                                            echo '<p>No answers available for this question.</p>';
                                        } ?>

                                        <?php if ($index + 1 < count($quiz_dataes)) { ?>
                                            <div class="as-next-button-wrapper">
                                                <button class="as-next-step" disabled>Next</button>
                                            </div>
                                        <?php } else { ?>
                                            <div class="as-finish-button-wrapper">
                                                <button id="finish-quiz" class="as-finish-quiz-button-lesson" data-quiz-url="<?php echo $current_url ?>" disabled>Finish Quiz</button>
                                                <input type="hidden" class="as-quiz-id" value="<?php echo $quiz_id ?>" />
                                            </div>
                                        <?php } ?>
                                    </div>

                            <?php
                                }
                            }
                        } else { ?>
                            <p>No questions found for this quiz.</p>
                        <?php }
                        ?>
                    </div>
                    <div class="as-overlay-wrapper">
                        <div class="as-quiz-result-processing">
                            <h5>Result:</h5>
                            <p>Quiz Complete. Results are Being Recorded</p>
                        </div>
                        <div>
                            <div id="as-overlay">

                            </div>
                        </div>
                    </div>

                    <div class="as-quiz-results">

                    </div>
                    <div class="as-quiz-feedback-results-wrapper">

                    </div>
                </div>
            <?php
            } else {
                $quiz_data_json = get_post_meta($quiz_id, 'user_quiz_score_data', true);
                $quiz_data = json_decode($quiz_data_json, true);
                $feedbackes = $quiz_data['feedback'];
            ?>
                <div class="as-quiz-container">
                    <h4><?php echo esc_html($quiz_title); ?></h4>
                    <div class="as-quiz-results">
                        <div class="as-score-summary">
                            <h3>Results: </h3>
                            <p>Your time: <?php echo $quiz_data['time_taken']; ?></p>
                            <div class="as-persantage-wrapper">
                                <p>You have obtained <b><?php echo $quiz_data['score']; ?></b> out of <b><?php echo $quiz_data['total_points']; ?></b> marks</p>
                                <p> <?php echo $quiz_data['message']; ?></p>
                            </div>
                            <div class="as-review-answers-wrapper">
                                <a class="as-clts-quiz-previous-butt" href="<?php echo $previous_lesson_url; ?>">&laquo; Previous</a>
                                <a class="as-review-answers" id="as-view-result">See Correct/Wrong</a>
                                <?php if ($show_topic_next == 1) { ?>
                                    <a class="as-clts-quiz-next-butt" href="<?php echo $next_lesson_url; ?>">Next &raquo; </a>
                                <?php } else { ?>
                                    <a href="<?php echo $current_lesson_outside_chapter_url;
                                                ?>" class="as-current-section-outside-topic-btn">Proceed to Next Chapter</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="as-quiz-feedback-results-wrapper">
                        <div class="as-question-feedback">
                            <?php foreach ($feedbackes as $feedback) { ?>
                                <h4 style="padding:10px 0px;"><?php echo $feedback['question'] ?></h4>
                                <ul>
                                    <?php foreach ($feedback['correct_answers'] as $correct_ans) { ?>
                                        <li class="correct-answer" style="color: green; border: 1px solid;padding: 10px;margin-bottom: 10px;"><b>Correct Answer:</b><?php echo $correct_ans ?></li>
                                    <?php } ?>
                                    <?php foreach ($feedback['selected_answers'] as $selected_ans) {
                                        $isCorrect = $feedback['is_correct'];
                                        $class = $isCorrect ? 'correct-answer' : 'wrong-answer';
                                        $color = $isCorrect ? 'green' : 'red';
                                    ?>
                                        <li class="<?php echo $class; ?>" style="color: <?php echo $color; ?>; border: 1px solid;padding: 10px;margin-bottom: 10px;"><b>Your Answer:</b><?php echo $selected_ans ?></li>
                                    <?php } ?>
                                </ul>
                                <p>Points: <?php echo !empty($feedback['points']) ? $feedback['points'] : 0;
                                            ?></p>
                                <p>Result:
                                    <?php if ($feedback['is_correct']) { ?>
                                        <span style="color: green;">Correct</span>
                                    <?php } else { ?>
                                        <span style="color: red;">Incorrect</span>
                                    <?php } ?>
                                </p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>

<?php
    endwhile;
endif;

get_footer();
?>