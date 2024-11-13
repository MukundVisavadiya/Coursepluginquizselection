<?php
get_header();

/**
 * Course Sidebar
 */
require_once dirname(__FILE__)  . '/../sidebar.php';

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
        $completedSteps = $wpdb->get_results($wpdb->prepare(
            "SELECT chapter_id, lesson_id, topic_id, section_id, quiz_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
            $user_ID,
            $course_id
        ), ARRAY_A);

        $progress_data = as_calculate_course_progress($course_id, $user_ID);
?>
        <input type="hidden" class="as-next-section-quiz-url" value="<?php echo $next_section_url; ?>" />
        <input type="hidden" class="as-previous-section-quiz-url" value="<?php echo $previous_section_url; ?>" />
        <input type="hidden" class="as_quiz_id" value="<?php echo $quiz_id ?>" />
        <input type="hidden" class="as_quiz_user_id" value="<?php echo $user_ID  ?>" />
        <input type="hidden" class="as-course-id" value="<?php echo $course_id; ?>">
        <input type="hidden" class="as-section-id" value="<?php echo $section_id ?>">
        <input type="hidden" class="as-chapter-id" value="<?php echo $chapter_id; ?>">
        <input type="hidden" class="as-lesson-id" value="<?php echo $lesson_id; ?>">
        <input type="hidden" class="as-topic-id" value="<?php echo $topic_id; ?>">
        <?php
        if (!as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, $section_id, $quiz_id)) { ?>
            <div class="as-course-container">
                <!-- This progress add total steps & completed steps -->
                <div class="as-course-progressbar">
                    <p><?php echo $progress_data['progress']; ?>% Completed <?php echo $progress_data['completed_steps']; ?>/<?php echo $progress_data['total_steps']; ?> Steps</p>
                    <div class="as-progress-bar">
                        <div class="as-progress-bar-fill" style="width: <?php echo $progress_data['progress']; ?>%;"></div>
                    </div>
                </div>
            </div>
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
            // echo "<pre>";
            // print_r($quiz_data);
            // echo "</pre>";
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
                            <a class="as-restart-quizz" href="<?php echo $current_url; ?>">Restart Quize</a>
                            <a class="as-clts-quiz-next-butt" href="<?php echo $next_section_url; ?>">Next &raquo; </a>
                        </div>
                    </div>
                </div>
                <div class="as-quiz-feedback-results-wrapper">

                </div>
            </div>
        <?php
        }
        ?>
<?php
    endwhile;
endif;

get_footer();
?>