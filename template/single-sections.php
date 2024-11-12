<?php
get_header();

/**
 * Course Sidebar
 */
require_once dirname(__FILE__)  . '/../sidebar.php';

$current_url = home_url(add_query_arg(array(), $wp->request));

$parsed_url = parse_url($current_url);

$path_array = explode('/', trim($parsed_url['path'], characters: '/'));
$course = get_page_by_path($path_array[2], OBJECT, 'course');
$course_id = $course->ID;
$course_dataes = get_post_meta($course_id, 'course_data', true);

$show_previous = false;
$show_next = false;

$previous_section_url = '#';
$next_section_url = '#';
$current_section_outside_topic_url = "#";

$user_id = get_current_user_id();

global $wpdb;

$table_name = $wpdb->prefix . 'as_learnmore_user_activity';
$completedSteps = $wpdb->get_results($wpdb->prepare(
    "SELECT chapter_id, lesson_id, topic_id, section_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
    $user_id,
    $course_id
), ARRAY_A);

$progress_data = as_calculate_course_progress($course_id, $user_id);


?>

<main class="as-dashboard">
    <div class="as-course-container">

        <!-- This progress add total steps & completed steps -->
        <div class="as-course-progressbar">
            <p><?php echo $progress_data['progress']; ?>% Completed <?php echo $progress_data['completed_steps']; ?>/<?php echo $progress_data['total_steps']; ?> Steps</p>
            <div class="as-progress-bar">
                <div class="as-progress-bar-fill" style="width: <?php echo $progress_data['progress']; ?>%;"></div>
            </div>
        </div>

        <?php
        foreach ($course_dataes as $chapter_index => $course_data) {

            $lesson_dataes = $course_data['lessons'];
            $chapter_id = $course_data['chapter_id'];
            $chapter_meta_slug  = get_post_field('post_name', $chapter_id);

            foreach ($lesson_dataes as $lesson_index => $lesson_data) {
                $topic_dataes = $lesson_data['topics'];
                $lesson_id = $lesson_data['lesson_id'];
                $lesson_meta_slug = get_post_field('post_name', $lesson_id);

                foreach ($topic_dataes as $topic_index => $topic_data) {
                    $section_dataes = $topic_data['sections'];
                    $topic_id = $topic_data['topic_id'];
                    $topic_meta_slug = get_post_field('post_name', $topic_id);

                    foreach ($section_dataes as $section_index => $section_data) {
                        $section_id = $section_data['section_id'];
                        $section_meta_slug = get_post_field('post_name', $section_id);
                        $quiz_ids = $section_data['quiz_id'] ?? [];

                        if ($section_meta_slug == $path_array[10]) {


                            foreach ($quiz_ids as $quiz_index => $quiz_id) {

                                if ($section_index > 0 || $quiz_index > 0) {
                                    if ($quiz_index > 0) {
                                        $previous_quiz_id = $quiz_ids[$quiz_index - 1];
                                        $previous_quiz_slug = get_post_field('post_name', $previous_quiz_id);
                                        $previous_section_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $section_meta_slug . '/quiz/' . $previous_quiz_slug . '/';
                                    } else {
                                        $previous_section_id = $section_dataes[$section_index - 1]['section_id'];
                                        $previous_section_slug = get_post_field('post_name', $previous_section_id);
                                        if (!empty($section_dataes[$section_index - 1]['quiz_id'])) {
                                            $last_quiz_id = end($section_dataes[$section_index - 1]['quiz_id']);
                                            $previous_quiz_slug = get_post_field('post_name', $last_quiz_id);
                                            $previous_section_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $previous_section_slug . '/quiz/' . $previous_quiz_slug . '/';
                                        } else {
                                            $previous_section_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $previous_section_slug . '/';
                                        }
                                    }
                                    $show_previous = true;
                                }

                                if ($section_index < count($section_dataes) - 1 || $quiz_index < count($quiz_ids) - 1 || !empty($section_data['quiz_id'])) {
                                    if ($section_data['quiz_id'][0]) {
                                        $first_quiz_slug = get_post_field('post_name', $quiz_id);
                                        $next_section_url  = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $section_meta_slug . '/quiz/' . $first_quiz_slug . '/';
                                        $show_next = true;
                                    } else if (isset($section_dataes['quiz_id'][$quiz_index + 1])) {
                                        $next_quiz_id = $section_dataes['quiz_id'][$quiz_index + 1];
                                        $next_quiz_slug = get_post_field('post_name', $next_quiz_id);
                                        $next_section_url  = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $section_meta_slug . '/quiz/' . $next_quiz_slug . '/';
                                        $show_next = true;
                                    } else {
                                        $next_section_id = $section_dataes[$section_index + 1]['section_id'];
                                        $next_section_slug = get_post_field('post_name', $next_section_id);
                                        $next_section_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $next_section_slug . '/';
                                        $show_next = true;
                                    }
                                }
                            }


                            $current_section_outside_topic_url  = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/';

                            $user_id = get_current_user_id();

                            global $wpdb;
                            $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

                            // Fetch completed sections
                            $completedSections = $wpdb->get_results($wpdb->prepare(
                                "SELECT chapter_id, lesson_id, topic_id, section_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
                                $user_id,
                                $course_id
                            ), ARRAY_A);

                            // Get chapter, lesson, and topic related sections
                            $relevantCompletedSections = array_filter($completedSections, function ($completed) use ($chapter_id, $lesson_id, $topic_id) {
                                return $completed['chapter_id'] == $chapter_id &&
                                    $completed['lesson_id'] == $lesson_id &&
                                    $completed['topic_id'] == $topic_id;
                            });

                            $isCurrentSectionCompleted = false;
                            $previousSectionCompleted = false;
                            $allPreviousTopicsSectionsCompleted = true;
                            $allPreviousLessonsSectionsCompleted = true;
                            $allPreviousChapterSectionsCompleted = true;

                            foreach ($section_dataes as $section) {

                                $isCompleted = array_filter($relevantCompletedSections, function ($completed) use ($chapter_id, $lesson_id, $topic_id, $section) {
                                    return $completed['chapter_id'] == $chapter_id &&
                                        $completed['lesson_id'] == $lesson_id &&
                                        $completed['topic_id'] == $topic_id &&
                                        $completed['section_id'] == $section['section_id'];
                                });

                                if ($section['section_id'] == $section_id && !empty($isCompleted)) {
                                    $isCurrentSectionCompleted = true;
                                }

                                $previous_section_id_condition = isset($previous_section_id) ? $previous_section_id : '';
                                // Check if the previous section is completed
                                if ($section['section_id'] == $previous_section_id_condition && !empty($isCompleted)) {
                                    $previousSectionCompleted = true;
                                }
                            }

                            if ($chapter_index > 0) {
                                foreach ($course_dataes as $prev_chapter_index => $prev_chapter_data) {

                                    if ($prev_chapter_index < $chapter_index) {
                                        $prev_chapter_lessons = $prev_chapter_data['lessons'];

                                        foreach ($prev_chapter_lessons as $prev_lessons_data) {
                                            $prev_lesson_topics = $prev_lessons_data['topics'];

                                            foreach ($prev_lesson_topics as $prev_topic_data) {
                                                $prev_topic_sections = $prev_topic_data['sections'];

                                                foreach ($prev_topic_sections as $prev_section) {
                                                    $prev_section_id = $prev_section['section_id'];
                                                    $prev_isCompleted = array_filter($completedSections, function ($completed) use ($prev_chapter_data, $prev_lessons_data, $prev_topic_data, $prev_section_id) {
                                                        return $completed['chapter_id'] == $prev_chapter_data['chapter_id'] &&
                                                            $completed['lesson_id'] == $prev_lessons_data['lesson_id'] &&
                                                            $completed['topic_id'] == $prev_topic_data['topic_id'] &&
                                                            $completed['section_id'] == $prev_section_id;
                                                    });

                                                    if (empty($prev_isCompleted)) {
                                                        $allPreviousChapterSectionsCompleted = false;
                                                        break 4;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ($lesson_index > 0) {
                                foreach ($lesson_dataes as $prev_lesson_index => $prev_lesson_data) {

                                    if ($prev_lesson_index < $lesson_index) {
                                        $prev_lesson_topics = $prev_lesson_data['topics'];


                                        foreach ($prev_lesson_topics as $prev_topic_data) {
                                            $prev_topic_sections = $prev_topic_data['sections'];


                                            foreach ($prev_topic_sections as $prev_section) {
                                                $prev_section_id = $prev_section['section_id'];
                                                $prev_lesson_isCompleted = array_filter($completedSections, function ($completed) use ($chapter_id, $prev_lesson_data, $prev_topic_data, $prev_section_id) {
                                                    return $completed['chapter_id'] == $chapter_id &&
                                                        $completed['lesson_id'] == $prev_lesson_data['lesson_id'] &&
                                                        $completed['topic_id'] == $prev_topic_data['topic_id'] &&
                                                        $completed['section_id'] == $prev_section_id;
                                                });


                                                if (empty($prev_lesson_isCompleted)) {
                                                    $allPreviousLessonsSectionsCompleted = false;
                                                    break 3;
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ($topic_index > 0) {
                                foreach ($topic_dataes as $prev_topic_index => $prev_topic_data) {
                                    if ($prev_topic_index < $topic_index) {
                                        $prev_topic_id = $prev_topic_data['topic_id'];
                                        $prev_topic_sections = $prev_topic_data['sections'];

                                        foreach ($prev_topic_sections as $prev_section) {
                                            $prev_section_id = $prev_section['section_id'];
                                            $prev_topic_isCompleted = array_filter($completedSections, function ($completed) use ($chapter_id, $lesson_id, $prev_topic_id, $prev_section_id) {
                                                return $completed['chapter_id'] == $chapter_id &&
                                                    $completed['lesson_id'] == $lesson_id &&
                                                    $completed['topic_id'] == $prev_topic_id &&
                                                    $completed['section_id'] == $prev_section_id;
                                            });

                                            if (empty($prev_topic_isCompleted)) {
                                                $allPreviousTopicsSectionsCompleted = false;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            } ?>

                            <div class="as-section-content-wrapper">
                                <?php
                                if (have_posts()) :
                                    while (have_posts()) : the_post(); ?>
                                        <div class="as-single-section-container">
                                            <header class="as-single-section-header">
                                                <h2 class="as-single-section-title"><?php the_title(); ?></h2>
                                            </header>
                                            <div class="as-single-section-content">
                                                <?php if (has_post_thumbnail($section_id)): ?>
                                                    <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($section_id), 'single-post-thumbnail'); ?>
                                                    <img src="<?php echo $image[0]; ?>" alt="<?php echo $image[0]; ?>" />
                                                <?php endif; ?>
                                                <?php the_content();
                                                ?>
                                            </div>
                                        </div>
                                <?php endwhile;
                                endif; ?>
                            </div>

                            <div class="as-chapter-accordion-list">
                                <?php
                                if ($chapter_index == 0 && $lesson_index == 0 && $topic_index == 0 && $section_index == 0) {
                                    if ($isCurrentSectionCompleted) {
                                        echo '<style>
                                    .as-mark-complete-section-btn {
                                        display: none;
                                    }

                                    .as-single-section-next-butt {
                                        display: block;
                                    }
                                </style>';
                                    } else {
                                        echo '<style>
                                    .as-mark-complete-section-btn {
                                        display: block;
                                    }

                                    .as-single-section-next-butt {
                                        display: none;
                                    }
                                </style>';
                                    }
                                } elseif ($chapter_index > 0 && !$allPreviousChapterSectionsCompleted) {

                                    echo '<div class="as-alert-error-message">';
                                    echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous sections in the chapter.</p>';
                                    echo '</div>';
                                    echo '<style>
                                    .as-mark-complete-section-btn {
                                        display: none;
                                    }

                                    .as-single-section-next-butt {
                                        display: none;
                                    }

                                    .as-section-single-page-accordion {
                                        display: none;
                                    }
                                    
                                    .as-section-content-wrapper{
                                        display:none;
                                    }
                                </style>';
                                } elseif ($lesson_index > 0 && !$allPreviousLessonsSectionsCompleted) {

                                    echo '<div class="as-alert-error-message">';
                                    echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous sections in the lesson.</p>';
                                    echo '</div>';
                                    echo '<style>
                                    .as-mark-complete-section-btn {
                                        display: none;
                                    }

                                    .as-single-section-next-butt {
                                        display: none;
                                    }

                                    .as-section-single-page-accordion {
                                        display: none;
                                    }

                                    .as-section-content-wrapper{
                                        display:none;
                                    }
                                </style>';
                                } elseif ($topic_index > 0 && !$allPreviousTopicsSectionsCompleted) {
                                    echo '<div class="as-alert-error-message">';
                                    echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous sections in the topic.</p>';
                                    echo '</div>';
                                    echo '<style>
                                    .as-mark-complete-section-btn {
                                        display: none;
                                    }

                                    .as-single-section-next-butt {
                                        display: none;
                                    }

                                    .as-section-single-page-accordion {
                                        display: none;
                                    }

                                    .as-section-content-wrapper{
                                        display:none;
                                    }
                                </style>';
                                } else {
                                    if ($previousSectionCompleted || $section_index == 0) {

                                        if ($isCurrentSectionCompleted) {
                                            echo '<style>
                                    .as-mark-complete-section-btn {
                                        display: none;
                                    }

                                    .as-single-section-next-butt {
                                        display: block;
                                    }
                                </style>';
                                        } else {
                                            echo '<style>
                                    .as-mark-complete-section-btn {
                                        display: block;
                                    }

                                    .as-single-section-next-butt {
                                        display: none;
                                    }
                                </style>';
                                        }
                                    } else {
                                        echo '<div class="as-alert-error-message">';
                                        echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous section.</p>';
                                        echo '</div>';
                                        echo '<style>
                                    .as-mark-complete-section-btn {
                                        display: none;
                                    }

                                    .as-single-section-next-butt {
                                        display: none;
                                    }

                                    .as-section-single-page-accordion {
                                        display: none;
                                    }
                                    
                                    .as-section-content-wrapper{
                                        display:none;
                                    }
                                </style>';
                                    }
                                }
                                $isSectionCompleted = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, $section_id, 0);

                                ?>
                                <div class="as-section-single-page-accordion">
                                    <input type="hidden" class="as-current-section" value="<?php echo $section_index; ?>">
                                    <input type="hidden" class="as-course-id" value="<?php echo $course_id; ?>">
                                    <input type="hidden" class="as-section-id" value="<?php echo $section_id; ?>">
                                    <input type="hidden" class="as-chapter-id" value="<?php echo $chapter_id; ?>">
                                    <input type="hidden" class="as-lesson-id" value="<?php echo $lesson_id; ?>">
                                    <input type="hidden" class="as-topic-id" value="<?php echo $topic_id; ?>">
                                    <input type="hidden" class="as-course-slug" value="<?php echo $course_slug; ?>">
                                    <input type="hidden" class="as-chapter-slug" value="<?php echo $chapter_meta_slug; ?>">
                                    <input type="hidden" class="as-lesson-slug" value="<?php echo $lesson_meta_slug; ?>">
                                    <input type="hidden" class="as-topic-slug" value="<?php echo $topic_meta_slug; ?>">
                                    <input type="hidden" class="as-all-course-section-data" value='<?php echo json_encode($section_dataes); ?>'>
                                    <input type="hidden" class="next-section-quiz-url" value="<?php echo $next_section_url; ?>" />
                                    <?php
                                    echo '<p>' . get_the_title($section_id) . '</p>';
                                    if ($isSectionCompleted) {
                                        echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                    }
                                    ?>
                                </div>
            <?php
                        }
                    }
                }
            }
        }

            ?>

                            </div>
                            <div class="as-next-pre-wrapper">
                                <?php if ($show_previous == false) { ?>
                                    <div class="as-prev-d-none">
                                        <a href="<?php echo $previous_section_url; ?>" class="as-previous ">&laquo; Previous Section</a>
                                    </div>
                                <?php } else { ?>
                                    <div class="as-single-section-pre-butt">
                                        <a href="<?php echo $previous_section_url; ?>" class="as-previous ">&laquo; Previous Section</a>
                                    </div>
                                <?php } ?>

                                <div class="as-course-link">
                                    <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/' ?>" class="as-back-to-course">Back to Course</a>
                                </div>
                                <div class="as-mark-complete">
                                    <button class="as-mark-complete-section-btn"><i class="fa-solid fa-check"></i> Mark Complete</button>
                                </div>

                                <?php
                                if ($show_next == false && $isCurrentSectionCompleted) { ?>
                                    <div class="as-current-section-outside-topic">
                                        <a href="<?php echo $current_section_outside_topic_url;
                                                    ?>" class="as-current-section-outside-topic-btn">Proceed to Next Topic</a>
                                    </div>
                                <?php }
                                ?>

                                <?php if ($show_next == false) { ?>
                                    <div class="as-next-d-none">
                                        <a href="<?php echo $next_section_url; ?>" class="as-next">Next Section &raquo;</a>
                                    </div>
                                <?php } else { ?>
                                    <div class="as-single-section-next-butt">
                                        <a href="<?php echo $next_section_url; ?>" class="as-next">Next Section &raquo;</a>

                                    </div>
                                <?php } ?>
                            </div>
    </div>
</main>