<?php
get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        $quiz_title = get_the_title();
        $quiz_id = get_the_ID();
        $quiz_dataes = get_post_meta($quiz_id, 'as_quiz_questions_and_points', true);
        $user_ID = get_current_user_id();
?>
        <input type="hidden" class="as_quiz_id" value="<?php echo $quiz_id ?>" />
        <input type="hidden" class="as_quiz_user_id" value="<?php echo $user_ID  ?>" />
        <div class="as-course-container-fluid">
            <div class="as-course-container">
                <h1><?php echo esc_html($quiz_title); ?></h1>
            </div>
        </div>

        <div class="as-quiz-container">
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
                                        <button id="finish-quiz" class="as-finish-quiz-button" disabled>Finish Quiz</button>
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
    endwhile;
endif;

get_footer();
?>