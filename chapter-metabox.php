<?php
// Inside Course to create chapter,lesson, topic, section
function as_create_clts_post_meta_boxes()
{
    add_meta_box('as_clts_post_create', 'Create CLTS', 'as_clts_post_create', 'course');
}
add_action('add_meta_boxes', 'as_create_clts_post_meta_boxes');


function as_clts_post_create()
{
    global $wpdb;
    $current_user_id = get_current_user_id();
    $course_id = get_the_ID();
    $table_name = $wpdb->prefix . 'as_user_enrollment_history';

    $is_enrolled = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND course_id = %d",
        $current_user_id,
        $course_id
    ));

    $sortable_class = $is_enrolled ? 'sortable-disabled' : 'sortable-enabled';
?>
    <div class="as-accordion-item-container <?php echo $sortable_class; ?>" id="as-sortable-chapter">
        <?php
        $course_id = get_the_ID();
        $course_data = get_post_meta($course_id, 'course_data', true);

        if (empty($course_data)) {
        ?>
            <div class="as-builder-empty">
                <p>Course has no content yet.</p>
                <p>Add a new Chapter or add an existing one from the sidebar</p>
            </div>
            <?php
        } else {
            foreach ($course_data as $chapter_data) {
                $fetch_che_id = $chapter_data['chapter_id'];
                $fetch_lessones = $chapter_data['lessons'];
                $fetch_chapter_quiz_id = $chapter_data['quiz_id'];
            ?>
                <div class="as-chapter-accordion as-accordion-chapter-<?php echo $fetch_che_id; ?>">
                    <div class="as-accordion-item">
                        <b><?php echo get_the_title($fetch_che_id); ?></b>
                        <a class="as-remove-fetch-chapter" data-chapter-id="<?php echo $fetch_che_id ?>">Remove</a>
                        <input type="hidden" name="chapter_id[]" class="as-hidden-chepter-id" value="<?php echo $fetch_che_id ?>" />
                    </div>
                    <!-- append quiz container for chapter -->
                    <div class="as-quiz-accordion-container-<?php echo $fetch_che_id; ?>">
                        <?php if (!empty($fetch_chapter_quiz_id)) {
                            foreach ($fetch_chapter_quiz_id as $quiz_id) { ?>
                                <div class="as-chapter-quiz-accordion">
                                    <b><?php echo 'Quiz: ' . get_the_title($quiz_id); ?></b>
                                    <a class="as-remove-chapter-quiz" data-quiz-chapter-id="<?php echo $fetch_che_id; ?>">Remove</a>
                                    <input type="hidden" name="quiz_id[<?php echo $fetch_che_id; ?>][]" class="as-hidden-chapter-quiz-id" value="<?php echo $quiz_id; ?>" />
                                </div>
                        <?php }
                        } ?>
                    </div>
                    <div class="as-lesson-accordion-container <?php echo $sortable_class; ?>" id="as-sortable-lesson">
                        <?php foreach ($fetch_lessones as $fetch_lesson) {
                            $fetch_lesson_id = $fetch_lesson['lesson_id'];
                            $fetch_lesson_quiz_id = $fetch_lesson['quiz_id'];
                            $fetch_topices = $fetch_lesson['topics'] ?>
                            <div class="as-lesson-accordion as-accordion-lesson-<?php echo $fetch_lesson_id; ?>">
                                <div class="as-accordion-item">
                                    <b><?php echo get_the_title($fetch_lesson_id); ?></b>
                                    <a class="as-remove-fetch-lesson" data-lesson-id="<?php echo $fetch_lesson_id ?>" data-chapter-id="<?php echo $fetch_che_id ?>">Remove</a>
                                    <input type="hidden" name="lesson_id[<?php echo $fetch_che_id ?>][]" class="as-hidden-lesson-id" value="<?php echo $fetch_lesson_id ?>" />
                                </div>

                                <!-- append quiz container for lesson -->
                                <div class="as-quiz-accordion-container-lesson-<?php echo $fetch_lesson_id; ?>">
                                    <?php if (!empty($fetch_lesson_quiz_id)) {
                                        foreach ($fetch_lesson_quiz_id as $quiz_id) { ?>
                                            <div class="as-lesson-quiz-accordion">
                                                <b><?php echo 'Quiz: ' . get_the_title($quiz_id); ?></b>
                                                <a class="as-remove-lesson-quiz" data-quiz-lesson-id="<?php echo $fetch_lesson_id; ?>">Remove</a>
                                                <input type="hidden" name="quiz_id[<?php echo $fetch_lesson_id; ?>][]" class="as-hidden-leson-quiz-id" value="<?php echo $quiz_id; ?>" />
                                            </div>
                                    <?php }
                                    } ?>
                                </div>
                                <div class="as-topic-accordion-container <?php echo $sortable_class; ?>" id="as-sortable-topic">
                                    <?php foreach ($fetch_topices as $fetch_topic) {
                                        $fetch_topic_id = $fetch_topic['topic_id'];
                                        $fetch_sections = $fetch_topic['sections'];
                                        $fetch_topic_quiz_id = $fetch_topic['quiz_id']; ?>
                                        <div class="as-topic-accordion as-accordion-topic-<?php echo $fetch_topic_id ?>">
                                            <div class="as-accordion-item">
                                                <b><?php echo get_the_title($fetch_topic_id); ?></b>
                                                <a class="as-remove-fetch-topic" data-lesson-id="<?php echo $fetch_lesson_id ?>" data-chapter-id="<?php echo $fetch_che_id ?>" data-topic-id="<?php echo $fetch_topic_id ?>">Remove</a>
                                                <input type="hidden" name="topic_id[<?php echo $fetch_che_id ?>][<?php echo $fetch_lesson_id ?>][]" class="as-hidden-topic-id" value="<?php echo $fetch_topic_id ?>" />
                                            </div>

                                            <!-- append quiz container for topic -->
                                            <div class="as-quiz-accordion-container-topic-<?php echo $fetch_topic_id; ?>">
                                                <?php if (!empty($fetch_topic_quiz_id)) {
                                                    foreach ($fetch_topic_quiz_id as $quiz_id) { ?>
                                                        <div class="as-topic-quiz-accordion">
                                                            <b><?php echo 'Quiz: ' . get_the_title($quiz_id); ?></b>
                                                            <a class="as-remove-topic-quiz" data-quiz-topic-id="<?php echo $fetch_topic_id; ?>">Remove</a>
                                                            <input type="hidden" name="quiz_id[<?php echo $fetch_topic_id; ?>][]" class="as-hidden-topic-quiz-id" value="<?php echo $quiz_id; ?>" />
                                                        </div>
                                                <?php }
                                                } ?>
                                            </div>

                                            <div class="as-section-accordion-container <?php echo $sortable_class; ?>" id="as-sortable-section">
                                                <?php foreach ($fetch_sections as $fetch_section) {
                                                    $fetch_section_id = $fetch_section['section_id'];
                                                    $fetch_section_quiz_id = $fetch_section['quiz_id']; ?>
                                                    <div class="as-section-accordion">
                                                        <div class="as-accordion-item">
                                                            <b><?php echo get_the_title($fetch_section_id); ?></b>
                                                            <a class="as-remove-fetch-section" data-lesson-id="<?php echo $fetch_lesson_id ?>" data-chapter-id="<?php echo $fetch_che_id ?>" data-topic-id="<?php echo $fetch_topic_id ?>" data-section-id="<?php echo $fetch_section_id ?>">Remove</a>
                                                            <input type="hidden" name="section_id[<?php echo $fetch_che_id ?>][<?php echo $fetch_lesson_id ?>][<?php echo $fetch_topic_id ?>][]" class="as-hidden-section-id" value="<?php echo $fetch_section_id ?>" />
                                                        </div>

                                                        <!-- append quiz container for section -->
                                                        <div class="as-quiz-accordion-container-section-<?php echo $fetch_section_id; ?>">
                                                            <?php if (!empty($fetch_section_quiz_id)) {
                                                                foreach ($fetch_section_quiz_id as $quiz_id) { ?>
                                                                    <div class="as-section-quiz-accordion">
                                                                        <b><?php echo 'Quiz: ' . get_the_title($quiz_id); ?></b>
                                                                        <a class="as-remove-section-quiz" data-quiz-section-id="<?php echo $fetch_section_id; ?>">Remove</a>
                                                                        <input type="hidden" name="quiz_id[<?php echo $fetch_section_id; ?>][]" class="as-hidden-section-quiz-id" value="<?php echo $quiz_id; ?>" />
                                                                    </div>
                                                            <?php }
                                                            } ?>
                                                        </div>
                                                    </div>

                                                    <!-- quiz add link for Section-->
                                                    <div class="as-quiz-section-selection as-quiz-section-input-id-<?php echo $fetch_section_id ?>">
                                                        <select class="as-quiz-selection-search-input-section form-control" data-placeholder="Select a Quiz......." style="width:90%;" data-quiz-section-id="<?php echo $fetch_section_id ?>">
                                                        </select>
                                                        <a class="as-cancel-section-quiz" data-quiz-section-id="<?php echo $fetch_section_id ?>">Cancel</a>
                                                    </div>
                                                    <a class="as-icon-quiz as-section-quiz-inputfield-link" data-quiz-section-id="<?php echo $fetch_section_id ?>">Add Quiz Section</a>

                                                <?php } ?>
                                            </div>
                                            <div class="as-section-container">
                                                <div class="as-section-input-field as-topic-input-id-<?php echo $fetch_topic_id ?>">
                                                    <input class="as-section-input-box" type="text" placeholder="Section Title">
                                                    <a class="as-cancel-section">Cancel</a>
                                                    <a class="as-add-section" data-chapter-id="<?php echo $fetch_che_id ?>" data-lesson-id="<?php echo $fetch_lesson_id ?>" data-topic-id="<?php echo $fetch_topic_id ?>">Add Section</a>
                                                </div>
                                                <a class="as-icon as-section-inputfield-link" data-topic-id="<?php echo $fetch_topic_id ?>">New Section</a>
                                            </div>
                                        </div>

                                        <!-- quiz add link for Topic-->
                                        <div class="as-quiz-topic-selection as-quiz-topic-input-id-<?php echo $fetch_topic_id ?>">
                                            <select class="as-quiz-selection-search-input-topic form-control" data-placeholder="Select a Quiz......." style="width:90%;" data-quiz-topic-id="<?php echo $fetch_topic_id ?>">
                                            </select>
                                            <a class="as-cancel-topic-quiz" data-quiz-topic-id="<?php echo $fetch_topic_id ?>">Cancel</a>
                                        </div>
                                        <a class="as-icon-quiz as-topic-quiz-inputfield-link" data-quiz-topic-id="<?php echo $fetch_topic_id ?>">Add Quiz Topic</a>

                                    <?php } ?>
                                </div>
                                <div class="as-topic-container">
                                    <div class="as-topic-input-field as-lesson-input-id-<?php echo $fetch_lesson_id ?>">
                                        <input class="as-topic-input-box" type="text" placeholder="Topic Title">
                                        <a class="as-cancel-topic">Cancel</a>
                                        <a class="as-add-topic" data-chapter-id="<?php echo $fetch_che_id ?>" data-lesson-id="<?php echo $fetch_lesson_id ?>">Add Topic</a>
                                    </div>
                                    <a class="as-icon as-topic-inputfield-link" data-lesson-id="<?php echo $fetch_lesson_id ?>">New Topic</a>
                                </div>
                            </div>

                            <!-- quiz add link for lesson-->
                            <div class="as-quiz-lesson-selection as-quiz-lesson-input-id-<?php echo $fetch_lesson_id ?>">
                                <select class="as-quiz-selection-search-input-lesson form-control" data-placeholder="Select a Quiz......." style="width:90%;" data-quiz-lesson-id="<?php echo $fetch_lesson_id ?>">
                                </select>
                                <a class="as-cancel-lesson-quiz" data-quiz-lesson-id="<?php echo $fetch_lesson_id ?>">Cancel</a>
                            </div>
                            <a class="as-icon-quiz as-lesson-quiz-inputfield-link" data-quiz-lesson-id="<?php echo $fetch_lesson_id ?>">Add Quiz Lesson</a>

                        <?php } ?>
                    </div>
                    <div class="as-lesson-container">
                        <div class="as-lesson-input-field as-chapter-input-id-<?php echo $fetch_che_id ?>">
                            <input class="as-lesson-input-box" type="text" placeholder="Lesson Title">
                            <a class="as-cancel-lesson">Cancel</a>
                            <a class="as-add-lesson" data-chapter-id="<?php echo $fetch_che_id ?>">Add Lesson</a>
                        </div>
                        <a class="as-icon as-lesson-inputfield-link" data-chapter-id="<?php echo $fetch_che_id ?>">New Lesson</a>
                    </div>

                    <!-- quiz add link for Chapter -->
                    <div class="as-quiz-selection as-quiz-chapter-input-id-<?php echo $fetch_che_id ?>">
                        <select class="as-quiz-selection-search-input form-control" data-placeholder="Select a Quiz......." style="width:90%;" data-quiz-chapter-id="<?php echo $fetch_che_id ?>">
                        </select>
                        <a class="as-cancel-chapter-quiz" data-quiz-chapter-id="<?php echo $fetch_che_id ?>">Cancel</a>
                    </div>
                    <a class="as-icon-quiz as-chapter-quiz-inputfield-link" data-quiz-chapter-id="<?php echo $fetch_che_id ?>">Add Quiz Chapter</a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="as-chapter-container">
        <div class="as-chapter-input-field">
            <input class="as-chapter-input-box" type="text" placeholder="Chapter Title">
            <a class="as-cancel-chapter">Cancel</a>
            <a class="as-add-chapter">Add Chapter</a>
        </div>
    </div>
    <a class="as-icon as-chapter-inputfield-link">New Chapter</a>
<?php }

// add chepter ajax response
function as_create_chapter_post()
{
    $chapter_name = sanitize_text_field($_POST['name']);

    if ($chapter_name) {

        $chapter_post_id = wp_insert_post(
            array(
                'post_title' => $chapter_name,
                'post_status' => 'publish',
                'post_type' => 'chapters',
            )
        );

        if ($chapter_post_id) {
            wp_send_json_success(array('chapter_id' => $chapter_post_id, 'chapter_name' => $chapter_name));
        } else {
            wp_send_json_error('Failed to create chapter post');
        }
    }
    wp_die();
}
add_action('wp_ajax_as_create_chapter_post', 'as_create_chapter_post');
add_action('wp_ajax_nopriv_as_create_chapter_post', 'as_create_chapter_post');


//remove chepter ajax response without save
function as_romove_chapter_post()
{
    $chapter_id = intval($_POST['chapter']);

    wp_delete_post($chapter_id);

    wp_send_json_success('success fully deleted post');
    wp_die();
}
add_action('wp_ajax_as_romove_chapter_post', 'as_romove_chapter_post');
add_action('wp_ajax_nopriv_as_romove_chapter_post', 'as_romove_chapter_post');


// add lesson ajax response
function as_create_lesson_post()
{
    $lesson_name = sanitize_text_field($_POST['name']);

    if ($lesson_name) {

        $lesson_post_id = wp_insert_post(
            array(
                'post_title' => $lesson_name,
                'post_status' => 'publish',
                'post_type' => 'lessons',
            )
        );

        if ($lesson_post_id) {
            wp_send_json_success(array('lesson_id' => $lesson_post_id, 'lesson_name' => $lesson_name));
        } else {
            wp_send_json_error('Failed to create lesson post');
        }
    }
    wp_die();
}
add_action('wp_ajax_as_create_lesson_post', 'as_create_lesson_post');
add_action('wp_ajax_nopriv_as_create_lesson_post', 'as_create_lesson_post');

//remove chepter ajax response without save
function as_romove_lesson_post()
{
    $lesson_id = intval($_POST['lesson']);

    wp_delete_post($lesson_id);

    wp_send_json_success('success fully deleted lesson');
    wp_die();
}
add_action('wp_ajax_as_romove_lesson_post', 'as_romove_lesson_post');
add_action('wp_ajax_nopriv_as_romove_lesson_post', 'as_romove_lesson_post');

// add topic ajax response
function as_create_topic_post()
{
    $topic_name = sanitize_text_field($_POST['name']);

    if ($topic_name) {

        $topic_post_id = wp_insert_post(
            array(
                'post_title' => $topic_name,
                'post_status' => 'publish',
                'post_type' => 'topics',
            )
        );

        if ($topic_post_id) {
            wp_send_json_success(array('topic_id' => $topic_post_id, 'topic_name' => $topic_name));
        } else {
            wp_send_json_error('Failed to create Topic post');
        }
    }

    wp_die();
}
add_action('wp_ajax_as_create_topic_post', 'as_create_topic_post');
add_action('wp_ajax_nopriv_as_create_topic_post', 'as_create_topic_post');

//remove topic ajax response without save
function as_romove_topic_post()
{
    $topic_id = intval($_POST['topic']);

    wp_delete_post($topic_id);

    wp_send_json_success('success fully deleted topic');
    wp_die();
}
add_action('wp_ajax_as_romove_topic_post', 'as_romove_topic_post');
add_action('wp_ajax_nopriv_as_romove_topic_post', 'as_romove_topic_post');

// add section ajax response
function as_create_section_post()
{
    $section_name = sanitize_text_field($_POST['name']);

    if ($section_name) {

        $section_post_id = wp_insert_post(
            array(
                'post_title' => $section_name,
                'post_status' => 'publish',
                'post_type' => 'sections',
            )
        );

        if ($section_post_id) {
            wp_send_json_success(array('section_id' => $section_post_id, 'section_name' => $section_name));
        } else {
            wp_send_json_error('Failed to create Section post');
        }
    }
    wp_die();
}
add_action('wp_ajax_as_create_section_post', 'as_create_section_post');
add_action('wp_ajax_nopriv_as_create_section_post', 'as_create_section_post');

//remove section ajax response without save
function as_romove_section_post()
{
    $section_id = intval($_POST['section']);

    wp_delete_post($section_id);

    wp_send_json_success('success fully deleted Section');
    wp_die();
}
add_action('wp_ajax_as_romove_section_post', 'as_romove_section_post');
add_action('wp_ajax_nopriv_as_romove_section_post', 'as_romove_section_post');


// save course post data
function as_create_course_data($post_id)
{
    $chapter_ides = isset($_POST['chapter_id']) ? $_POST['chapter_id'] : array();
    $lessones_ides = isset($_POST['lesson_id']) ? $_POST['lesson_id'] : array();
    $topic_ides = isset($_POST['topic_id']) ? $_POST['topic_id'] : array();
    $section_ides = isset($_POST['section_id']) ? $_POST['section_id'] : array();
    $quiz_ides = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : array();


    $course_data = array();

    foreach ($chapter_ides as $chapter_id) {
        $quiz_id = isset($quiz_ides[$chapter_id]) ?  $quiz_ides[$chapter_id] : null;

        $chapter_data = array(
            'chapter_id' => $chapter_id,
            'quiz_id' =>  $quiz_id,
            'lessons' => array(),
        );


        if (isset($lessones_ides[$chapter_id])) {

            foreach ($lessones_ides[$chapter_id] as $lesson_id) {
                $quiz_id_lesson = isset($quiz_ides[$lesson_id]) ? $quiz_ides[$lesson_id] : null;

                $lesson_data = array(
                    'lesson_id' => $lesson_id,
                    'quiz_id' =>  $quiz_id_lesson,
                    'topics' => array(),
                );

                if (isset($topic_ides[$chapter_id][$lesson_id])) {

                    foreach ($topic_ides[$chapter_id][$lesson_id] as $topic_id) {
                        $quiz_id_topic = isset($quiz_ides[$topic_id]) ? $quiz_ides[$topic_id] : null;

                        $topic_data = array(
                            "topic_id" => $topic_id,
                            "quiz_id" => $quiz_id_topic,
                            "sections" => array(),
                        );

                        if (isset($section_ides[$chapter_id][$lesson_id][$topic_id])) {
                            foreach ($section_ides[$chapter_id][$lesson_id][$topic_id] as $section_id) {
                                $quiz_id_section = isset($quiz_ides[$section_id]) ? $quiz_ides[$section_id] : null;
                                $topic_data['sections'][] = array(
                                    'section_id' => $section_id,
                                    'quiz_id' => $quiz_id_section
                                );
                            }
                        }

                        $lesson_data['topics'][] = $topic_data;
                    }
                }

                $chapter_data['lessons'][] = $lesson_data;
            }
        }
        $course_data[] = $chapter_data;
    }

    if (!empty($course_data)) {
        update_post_meta($post_id, 'course_data', $course_data);
    } else {
        update_post_meta($post_id, 'course_data', array());
    }
}
add_action('save_post', 'as_create_course_data');
