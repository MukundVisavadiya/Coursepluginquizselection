<?php
get_header();

/**
 * Course Sidebar
 */
require_once dirname(__FILE__)  . '/../sidebar.php';
$current_url = home_url(add_query_arg(array(), $wp->request));
$parsed_url = parse_url($current_url);
$path_array = explode('/', trim($parsed_url['path'], '/'));
$course = get_page_by_path($path_array[2], OBJECT, 'course');
$course_id = $course->ID;
$course_dataes = get_post_meta($course_id, 'course_data', true);
$previous_lesson_url = '#';
$next_lesson_url = '#';
$current_lesson_outside_chapter_url = '#';
$show_previous = false;
$show_next = false;

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

                if ($lesson_meta_slug == $path_array[6]) {

                    if ($lesson_index > 0) {
                        $previous_lesson_id = $lesson_dataes[$lesson_index - 1]['lesson_id'];
                        $previous_lesson_slug = get_post_field('post_name', $previous_lesson_id);
                        $previous_lesson_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $previous_lesson_slug . '/';
                        $show_previous = true;
                    }

                    if ($lesson_index < count($lesson_dataes) - 1) {
                        $next_lesson_id = $lesson_dataes[$lesson_index + 1]['lesson_id'];
                        $next_lesson_slug = get_post_field('post_name', $next_lesson_id);
                        $next_lesson_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $next_lesson_slug . '/';
                        $show_next = true;
                    }

                    $current_lesson_outside_chapter_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/';
                    $user_id = get_current_user_id();

                    global $wpdb;
                    $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

                    /*......... Check Inside this chapter all section completed or not start ........... */
                    // Fetch completed Topic
                    $completedTopic = $wpdb->get_results($wpdb->prepare(
                        "SELECT chapter_id, lesson_id, topic_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
                        $user_id,
                        $course_id
                    ), ARRAY_A);

                    // Get chapter, lesson related topic ,section
                    $relevantCompletedTopic = array_filter($completedTopic, function ($completed) use ($chapter_id, $lesson_id) {
                        return $completed['chapter_id'] == $chapter_id &&
                            $completed['lesson_id'] == $lesson_id;
                    });

                    $completedTopicIds = array_column($relevantCompletedTopic, 'topic_id');

                    $allTopicId = array_column($topic_dataes, 'topic_id');

                    $allTopicCompleted = true;
                    foreach ($allTopicId as $topicId) {
                        if (!in_array($topicId, $completedTopicIds)) {
                            $allTopicCompleted = false;
                            break;
                        }
                    }

                    // /*......... END ........... */

                    $completedLesson = $wpdb->get_results($wpdb->prepare(
                        "SELECT chapter_id, lesson_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
                        $user_id,
                        $course_id
                    ), ARRAY_A);


                    $relevantCompletedLessons = array_filter($completedLesson, function ($completed) use ($chapter_id) {
                        return $completed['chapter_id'] == $chapter_id;
                    });

                    $isCurrentLessonCompleted = false;
                    $previousLessonCompleted = false;
                    $allPreviousChaptersLessonsCompleted = true;


                    foreach ($lesson_dataes as $lesson) {
                        $isCompleted = array_filter($relevantCompletedLessons, function ($completed) use ($chapter_id, $lesson) {
                            return $completed['chapter_id'] == $chapter_id &&
                                $completed['lesson_id'] == $lesson['lesson_id'];
                        });


                        foreach ($completedTopic as $completedLessondata) {
                            // Check if the current lesson is completed
                            if ($completedLessondata['chapter_id'] == $chapter_id && $completedLessondata['lesson_id'] == $lesson_id && $completedLessondata['topic_id'] == 0  && !empty($isCompleted)) {
                                $isCurrentLessonCompleted = true;
                            }

                            $previous_lesson_id_condition = isset($previous_lesson_id) ? $previous_lesson_id : '';
                            if ($completedLessondata['chapter_id'] == $chapter_id && $completedLessondata['lesson_id'] == $previous_lesson_id_condition && $completedLessondata['topic_id'] == 0  && !empty($isCompleted)) {
                                $previousLessonCompleted = true;
                            }
                        }
                    }


                    if ($chapter_index > 0) {
                        foreach ($course_dataes as $prev_chapter_index => $prev_chapter_data) {
                            if ($prev_chapter_index < $chapter_index) {
                                $prev_chapter_lessons = $prev_chapter_data['lessons'];

                                foreach ($prev_chapter_lessons as $prev_lesson_data) {
                                    $prev_lesson_id = $prev_lesson_data['lesson_id'];
                                    $prev_isCompleted = array_filter($completedLesson, function ($completed) use ($prev_chapter_data, $prev_lesson_data) {

                                        return $completed['chapter_id'] == $prev_chapter_data['chapter_id'] &&
                                            $completed['lesson_id'] == $prev_lesson_data['lesson_id'];
                                    });

                                    if (empty($prev_isCompleted)) {
                                        $allPreviousChaptersLessonsCompleted = false;
                                        break 2;
                                    }
                                }
                            }
                        }
                    } ?>

                    <div class="as-lesson-content-wrapper">
                        <?php
                        if (have_posts()) :
                            while (have_posts()) : the_post(); ?>
                                <div class="as-single-lesson-continer">
                                    <header class="as-single-lesson-header">
                                        <h2 class="as-single-lesson-title"><?php the_title(); ?></h2>
                                    </header>
                                    <div class="as-single-lesson-content">
                                        <?php if (has_post_thumbnail($lesson_id)): ?>
                                            <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($lesson_id), 'single-post-thumbnail'); ?>
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
                        if ($chapter_index == 0 && $lesson_index == 0) {
                            if (!$allTopicCompleted) {
                                echo '<div class="as-alert-info-message">';
                                echo '<p class="as-course-info-message"><i class="fa-solid fa-circle-exclamation"></i> Please Complete the All Topic After You Go Next Lesson.</p>';
                                echo '</div>';
                                echo '<style>
                            .as-mark-complete-lesson-btn {
                                display: none;
                            }

                            .as-single-lesson-next-butt {
                                display: none;
                            }
                        </style>';
                            } else {
                                if ($isCurrentLessonCompleted) {
                                    echo '<style>
                            .as-mark-complete-lesson-btn {
                                display: none;
                            }

                            .as-single-lesson-next-butt {
                                display: block;
                            }
                        </style>';
                                } else {
                                    echo '<style>
                            .as-mark-complete-lesson-btn {
                                display: block;
                            }

                            .as-single-lesson-next-butt {
                                display: none;
                            }
                        </style>';
                                }
                            }
                        } elseif ($chapter_index > 0 && !$allPreviousChaptersLessonsCompleted) {

                            echo '<div class="as-alert-error-message">';
                            echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous lessons in the chapter.</p>';
                            echo '</div>';
                            echo '<style>
                            .as-mark-complete-lesson-btn {
                                display: none;
                            }

                            .as-single-lesson-next-butt {
                                display: none;
                            }

                            .as-single-lesson-accordion {
                                display: none;
                            }

                            .as-single-topic-accordion {
                                display: none;
                            }

                            .as-lesson-content-wrapper{
                               display:none
                             }
                        </style>';
                        } else {
                            if (!$allTopicCompleted && $previousLessonCompleted) {
                                echo '<div class="as-alert-info-message">';
                                echo '<p class="as-course-info-message"><i class="fa-solid fa-circle-exclamation"></i> Please complete all sections before you proceed to the next lesson.</p>';
                                echo '</div>';
                                echo '<style>
                            .as-mark-complete-lesson-btn {
                                display: none;
                            }

                            .as-single-lesson-next-butt {
                                display: none;
                            }
                        </style>';
                            } else {

                                if ($previousLessonCompleted || $lesson_index == 0) {
                                    if ($isCurrentLessonCompleted) {
                                        echo '<style>
                            .as-mark-complete-lesson-btn {
                                display: none;
                            }

                            .as-single-lesson-next-butt {
                                display: block;
                            }
                        </style>';
                                    } else {
                                        echo '<style>
                            .as-mark-complete-lesson-btn {
                                display: block;
                            }

                            .as-single-lesson-next-butt {
                                display: none;
                            }
                        </style>';
                                    }
                                } else {
                                    echo '<div class="as-alert-error-message">';
                                    echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous lesson.</p>';
                                    echo '</div>';
                                    echo '<style>
                            .as-mark-complete-lesson-btn {
                                display: none;
                            }

                            .as-single-lesson-next-butt {
                                display: none;
                            }

                            .as-single-lesson-accordion {
                                display: none;
                            }

                            .as-single-topic-accordion {
                                display: none;
                            }

                            .as-lesson-content-wrapper{
                               display:none
                             }
                        </style>';
                                }
                            }
                        }
                        $isLessonCompleted = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, 0, 0);

                        ?>
                        <div class="as-single-lesson-accordion active" data-lesson-id="<?php echo $lesson_id ?>">
                            <input type="hidden" class="as-all-course-lesson-data" value='<?php echo json_encode($lesson_dataes); ?>'>
                            <input type="hidden" class="as-current-lesson" value="<?php echo $lesson_index; ?>">
                            <input type="hidden" class="as-course-id" value="<?php echo $course_id; ?>">
                            <input type="hidden" class="as-chapter-id" value="<?php echo $chapter_id; ?>">
                            <input type="hidden" class="as-lesson-id" value="<?php echo $lesson_id; ?>">
                            <input type="hidden" class="as-course-slug" value="<?php echo $course_slug; ?>">
                            <input type="hidden" class="as-chapter-slug" value="<?php echo $chapter_meta_slug; ?>">
                            <?php
                            echo '<p>' .   get_the_title($lesson_id) . '</p>';
                            if ($isLessonCompleted) {
                                echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                            }
                            ?>
                        </div>

                        <?php
                        foreach ($topic_dataes as $topic_data) {
                            $section_dataes = $topic_data['sections'];
                            $topic_id = $topic_data['topic_id'];
                            $topic_meta_slug = get_post_field('post_name', $topic_id);
                        ?>
                            <div class="as-single-topic-accordion as-topic-accordion-grandchild-<?php echo $lesson_id; ?>" data-topic-id="<?php echo $topic_id ?>">
                                <div class="as-dashboard-content-wrapper">
                                    <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' .  $topic_meta_slug  . '/' ?>">
                                        <?php
                                        echo '<p>' . get_the_title($topic_id) . '</p>';
                                        ?>
                                    </a>
                                    <div>
                                        <?php
                                        $isTopicCompleted = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, 0);
                                        if ($isTopicCompleted) {
                                            echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                        }
                                        ?>
                                        <i class="fa-solid fa-angle-down"></i>
                                    </div>
                                </div>
                            </div>

                            <?php

                            foreach ($section_dataes as $section_data) {
                                $section_id = $section_data['section_id'];
                                $section_meta_slug = get_post_field('post_name', $section_id);
                            ?>
                                <div class="as-single-section-accordion as-section-accordion-grand-grandchild-<?php echo $topic_id; ?>">
                                    <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' .  $topic_meta_slug  . '/sections/' . $section_meta_slug . '/' ?>">
                                        <?php
                                        echo '<p>' . get_the_title($section_id) . '</p>';
                                        $isSectionCompleted = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, $section_id);
                                        if ($isSectionCompleted) {
                                            echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                        }
                                        ?>
                                    </a>
                                </div>

            <?php
                            }
                        }
                    }
                }
            } ?>
                    </div>

                    <div class="as-next-pre-wrapper">
                        <?php if ($show_previous == false) { ?>
                            <div class="as-prev-d-none">
                                <a href="<?php echo $previous_lesson_url; ?>" class="previous">&laquo; Previous Lesson</a>
                            </div>
                        <?php } else { ?>
                            <div class="as-single-lesson-pre-butt">
                                <a href="<?php echo $previous_lesson_url; ?>" class="previous">&laquo; Previous Lesson</a>
                            </div>
                        <?php } ?>

                        <div class="as-course-link">
                            <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/' ?>" class="as-back-to-course">Back to Course</a>
                        </div>


                        <div class="as-mark-complete">
                            <button class="as-mark-complete-lesson-btn"><i class="fa-solid fa-check"></i> Mark to Complete</button>
                        </div>

                        <?php
                        if ($show_next == false && $isCurrentLessonCompleted) { ?>
                            <div class="as-current-lesson-outside-chapter">
                                <a href="<?php echo $current_lesson_outside_chapter_url; ?>" class="as-current-lesson-outside-chapter-btn">Proceed to Next Chapter</a>
                            </div>
                        <?php }
                        ?>

                        <?php if ($show_next == false) { ?>
                            <div class="as-next-d-none">
                                <a href="<?php echo $next_lesson_url; ?>" class="next">Next Lesson &raquo;</a>
                            </div>
                        <?php } else { ?>
                            <div class="as-single-lesson-next-butt">
                                <a href="<?php echo $next_lesson_url; ?>" class="next">Next Lesson &raquo;</a>
                            </div>
                        <?php } ?>
                    </div>
    </div>
</main>