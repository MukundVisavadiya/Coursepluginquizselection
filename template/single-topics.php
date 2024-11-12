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
$previous_topic_url = '#';
$next_topic_url = '#';
$current_topic_outside_lesson_url = '#';
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
echo "<pre>";
print_r($progress_data['testing']);
echo "</pre>";
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

                    if ($topic_meta_slug == $path_array[8]) {

                        if ($topic_index > 0) {
                            $previous_topic_id = $topic_dataes[$topic_index - 1]['topic_id'];
                            $previous_topic_slug = get_post_field('post_name',  $previous_topic_id);
                            $previous_topic_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $previous_topic_slug . '/';
                            $show_previous = true;
                        }

                        if ($topic_index < count($topic_dataes) - 1) {
                            $next_topic_id = $topic_dataes[$topic_index + 1]['topic_id'];
                            $next_topic_slug = get_post_field('post_name', $next_topic_id);
                            $next_topic_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $next_topic_slug . '/';
                            $show_next = true;
                        }

                        $current_topic_outside_lesson_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug  . '/';

                        $user_id = get_current_user_id();

                        global $wpdb;
                        $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

                        /*......... Check Inside this chapter all section completed or not start ........... */
                        // Fetch completed sections
                        $completedSection = $wpdb->get_results($wpdb->prepare(
                            "SELECT chapter_id, lesson_id, topic_id, section_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
                            $user_id,
                            $course_id
                        ), ARRAY_A);

                        // Get chapter, lesson, topic related section
                        $relevantCompletedSections = array_filter($completedSection, function ($completed) use ($chapter_id, $lesson_id, $topic_id) {
                            return $completed['chapter_id'] == $chapter_id &&
                                $completed['lesson_id'] == $lesson_id &&
                                $completed['topic_id'] == $topic_id;
                        });


                        $completedSectionIds = array_column($relevantCompletedSections, 'section_id');

                        $allSectionId = array_column($section_dataes, 'section_id');

                        $allSectionsCompleted = true;
                        foreach ($allSectionId as $sectionId) {
                            if (!in_array($sectionId, $completedSectionIds)) {
                                $allSectionsCompleted = false;
                                break;
                            }
                        }

                        /*......... END ........... */

                        $completedTopics = $wpdb->get_results($wpdb->prepare(
                            "SELECT chapter_id, lesson_id, topic_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
                            $user_id,
                            $course_id
                        ), ARRAY_A);

                        // Get chapter, lesson related topic 
                        $relevantCompletedTopics = array_filter($completedTopics, function ($completed) use ($chapter_id, $lesson_id) {
                            return $completed['chapter_id'] == $chapter_id &&
                                $completed['lesson_id'] == $lesson_id;
                        });

                        $isCurrentTopicsCompleted = false;
                        $previousTopicCompleted = false;
                        $allPreviousLessonsTopicsCompleted = true;
                        $allPreviousChapterTopicsCompleted = true;

                        // Check current and previous topic completion status
                        foreach ($topic_dataes as $topic) {
                            $isCompleted = array_filter($relevantCompletedTopics, function ($completed) use ($chapter_id, $lesson_id, $topic) {
                                return $completed['chapter_id'] == $chapter_id &&
                                    $completed['lesson_id'] == $lesson_id &&
                                    $completed['topic_id'] == $topic['topic_id'];
                            });

                            foreach ($completedSection as $completedTopicdata) {
                                // Check if the current section is completed
                                if ($completedTopicdata['chapter_id'] == $chapter_id && $completedTopicdata['lesson_id'] == $lesson_id && $completedTopicdata['topic_id'] == $topic_id && $completedTopicdata['section_id'] == 0 && !empty($isCompleted)) {
                                    $isCurrentTopicsCompleted = true;
                                }

                                $previous_topic_id_condition = isset($previous_topic_id) ? $previous_topic_id : '';
                                // if previous topic is completed
                                if ($completedTopicdata['chapter_id'] == $chapter_id && $completedTopicdata['lesson_id'] == $lesson_id && $completedTopicdata['topic_id'] == $previous_topic_id_condition && $completedTopicdata['section_id'] == 0 && !empty($isCompleted)) {
                                    $previousTopicCompleted = true;
                                }
                            }
                        }

                        if ($chapter_index > 0) {
                            foreach ($course_dataes as $prev_chapter_index => $prev_chapter_data) {
                                if ($prev_chapter_index < $chapter_index) {
                                    $prev_chapter_lessons = $prev_chapter_data['lessons'];
                                    foreach ($prev_chapter_lessons as $prev_lessons_data) {
                                        $prev_lesson_topics = $prev_lessons_data['topics'];
                                        foreach ($prev_lesson_topics as $prev_topic_data) {
                                            $prev_topic_id = $prev_topic_data['topic_id'];
                                            $prev_isCompleted = array_filter($completedTopics, function ($completed) use ($prev_chapter_data, $prev_lessons_data, $prev_topic_id) {
                                                return $completed['chapter_id'] == $prev_chapter_data['chapter_id'] &&
                                                    $completed['lesson_id'] == $prev_lessons_data['lesson_id'] &&
                                                    $completed['topic_id'] == $prev_topic_id;
                                            });

                                            if (empty($prev_isCompleted)) {
                                                $allPreviousChapterTopicsCompleted = false;
                                                break 3;
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
                                        $prev_topic_id = $prev_topic_data['topic_id'];
                                        $prev_lesson_isCompleted = array_filter($completedTopics, function ($completed) use ($chapter_id, $prev_lesson_data, $prev_topic_id) {
                                            return $completed['chapter_id'] == $chapter_id &&
                                                $completed['lesson_id'] == $prev_lesson_data['lesson_id'] &&
                                                $completed['topic_id'] == $prev_topic_id;
                                        });

                                        if (empty($prev_lesson_isCompleted)) {
                                            $allPreviousLessonsTopicsCompleted = false;
                                            break 2;
                                        }
                                    }
                                }
                            }
                        } ?>

                        <div class="as-topic-content-wrapper">
                            <?php if (have_posts()) :
                                while (have_posts()) : the_post(); ?>
                                    <div class="as-single-topic-container">
                                        <header class="as-single-topic-header">
                                            <h2 class="as-single-topic-title"><?php the_title(); ?></h2>
                                        </header>
                                        <div class="as-single-topic-content">
                                            <?php if (has_post_thumbnail($topic_id)): ?>
                                                <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($topic_id), 'single-post-thumbnail'); ?>
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
                            if ($chapter_index == 0 && $lesson_index == 0 && $topic_index == 0) {
                                if (!$allSectionsCompleted) {
                                    echo '<div class="as-alert-info-message">';
                                    echo '<p class="as-course-info-message"><i class="fa-solid fa-circle-exclamation"></i> Please complete all sections before you proceed to the next lesson.</p>';
                                    echo '</div>';
                                    echo '<style>
                                .as-mark-complete-topic-btn {
                                    display: none;
                                }

                                .as-single-topic-next-butt {
                                    display: none;
                                }
                            </style>';
                                } else {
                                    if ($isCurrentTopicsCompleted) {
                                        echo '<style>
                                .as-mark-complete-topic-btn {
                                    display: none;
                                }

                                .as-single-topic-next-butt {
                                    display: block;
                                }
                            </style>';
                                    } else {
                                        echo '<style>
                                .as-mark-complete-topic-btn {
                                    display: block;
                                }

                                .as-single-topic-next-butt {
                                    display: none;
                                }
                            </style>';
                                    }
                                }
                            } elseif ($chapter_index > 0 && !$allPreviousChapterTopicsCompleted) {

                                echo '<div class="as-alert-error-message">';
                                echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous topics in the chapter.</p>';
                                echo '</div>';
                                echo '<style>
                                .as-mark-complete-topic-btn {
                                    display: none;
                                }

                                .as-single-topic-next-butt {
                                    display: none;
                                }

                                .as-topic-single-page-accordion {
                                    display: none;
                                }

                                .as-section-topic-single-page-accordion {
                                    display: none
                                }

                                .as-topic-content-wrapper{
                                    display:none;
                                }
                            </style>';
                            } elseif ($lesson_index > 0 && !$allPreviousLessonsTopicsCompleted) {

                                echo '<div class="as-alert-error-message">';
                                echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous topics in the lesson.</p>';
                                echo '</div>';
                                echo '<style>
                                .as-mark-complete-topic-btn {
                                    display: none;
                                }

                                .as-single-topic-next-butt {
                                    display: none;
                                }

                                .as-topic-single-page-accordion {
                                    display: none;
                                }

                                .as-section-topic-single-page-accordion {
                                    display: none
                                }

                                .as-topic-content-wrapper{
                                    display:none;
                                }
                            </style>';
                            } else {
                                if (!$allSectionsCompleted && $previousTopicCompleted) {
                                    echo '<div class="as-alert-info-message">';
                                    echo '<p class="as-course-info-message"><i class="fa-solid fa-circle-exclamation"></i> Please complete all sections before you proceed to the next lesson.</p>';
                                    echo '</div>';
                                    echo '<style>
                                .as-mark-complete-topic-btn {
                                    display: none;
                                }

                                .as-single-topic-next-butt {
                                    display: none;
                                }
                            </style>';
                                } else {
                                    if ($previousTopicCompleted || $topic_index == 0) {
                                        if ($isCurrentTopicsCompleted) {
                                            echo '<style>
                                .as-mark-complete-topic-btn {
                                    display: none;
                                }

                                .as-single-topic-next-butt {
                                    display: block;
                                }
                            </style>';
                                        } else {
                                            echo '<style>
                                .as-mark-complete-topic-btn {
                                    display: block;
                                }

                                .as-single-topic-next-butt {
                                    display: none;
                                }
                            </style>';
                                        }
                                    } else {
                                        echo '<div class="as-alert-error-message">';
                                        echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous topic.</p>';
                                        echo '</div>';
                                        echo '<style>
                                .as-mark-complete-topic-btn {
                                    display: none;
                                }

                                .as-single-topic-next-butt {
                                    display: none;
                                }

                                .as-topic-single-page-accordion {
                                    display: none;
                                }

                                .as-section-topic-single-page-accordion {
                                    display: none
                                }

                                .as-topic-content-wrapper{
                                    display:none;
                                }
                            </style>';
                                    }
                                }
                            }
                            $isTopicCompleted = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, 0);


                            ?>
                            <div class="as-topic-single-page-accordion" data-topic-id="<?php echo $topic_id ?>">
                                <input type="hidden" class="as-all-course-topic-data" value='<?php echo json_encode($topic_dataes); ?>'>
                                <input type="hidden" class="as-current-topic" value="<?php echo $topic_index; ?>">
                                <input type="hidden" class="as-course-id" value="<?php echo $course_id; ?>">
                                <input type="hidden" class="as-chapter-id" value="<?php echo $chapter_id; ?>">
                                <input type="hidden" class="as-lesson-id" value="<?php echo $lesson_id; ?>">
                                <input type="hidden" class="as-topic-id" value="<?php echo $topic_id; ?>">
                                <input type="hidden" class="as-course-slug" value="<?php echo $course_slug; ?>">
                                <input type="hidden" class="as-chapter-slug" value="<?php echo $chapter_meta_slug; ?>">
                                <input type="hidden" class="as-lesson-slug" value="<?php echo $lesson_meta_slug; ?>">
                                <?php
                                echo '<p>' .   get_the_title($topic_id) . '</p>';
                                if ($isTopicCompleted) {
                                    echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                }
                                ?>
                            </div>
                            <!-- Topic Quiz -->
                            <?php if (!empty($topic_data['quiz_id'])) {
                                foreach ($topic_data['quiz_id'] as $quiz_topic_id) {
                                    $quiz_topic_meta_slug = get_post_field('post_name', $quiz_topic_id);
                            ?>
                                    <div class="as-topic-single-page-accordion">
                                        <a style="display: flex; flex-direction: row; align-items: center;" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug .  '/lessons/' . $lesson_meta_slug . '/topics/' .  $topic_meta_slug  . '/quiz/' . $quiz_topic_meta_slug . '/' ?>">
                                            <i style="padding-right:10px" class="fa-solid fa-circle-question"></i>
                                            <?php
                                            echo '<p>' . get_the_title($quiz_topic_id) . '</p>';
                                            ?>
                                        </a>
                                    </div>
                            <?php
                                }
                            } ?>

                            <?php

                            foreach ($section_dataes as $section_data) {
                                $section_id = $section_data['section_id'];
                                $section_meta_slug = get_post_field('post_name', $section_id);
                            ?>
                                <div class="as-section-topic-single-page-accordion as-section-accordion-grand-grandchild-<?php echo $topic_id; ?>">
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

                                <!-- Section Quiz -->
                                <?php if (!empty($section_data['quiz_id'])) {
                                    foreach ($section_data['quiz_id'] as $quiz_id) {
                                        $quiz_meta_slug = get_post_field('post_name', $quiz_id);
                                ?>
                                        <div class="as-section-quiz-accordion">
                                            <a style="display: flex; flex-direction: row; align-items: center;" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug .  '/lessons/' . $lesson_meta_slug . '/topics/' .  $topic_meta_slug  . '/sections/' . $section_meta_slug . '/quiz/' . $quiz_meta_slug . '/' ?>">
                                                <i style="padding-right:10px" class="fa-solid fa-circle-question"></i>
                                                <?php
                                                echo '<p>' . get_the_title($quiz_id) . '</p>';
                                                ?>
                                            </a>
                                        </div>
                                <?php
                                    }
                                } ?>
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
                                    <a href="<?php echo $previous_topic_url; ?>" class="previous">&laquo; Previous Topic</a>
                                </div>
                            <?php } else { ?>
                                <div class="as-single-topic-pre-butt">
                                    <a href="<?php echo $previous_topic_url; ?>" class="previous">&laquo; Previous Topic</a>
                                </div>
                            <?php } ?>
                            <div class="as-course-link">
                                <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/' ?>" class="as-back-to-course">Back to Course</a>
                            </div>
                            <div class="as-mark-complete">
                                <button class="as-mark-complete-topic-btn"><i class="fa-solid fa-check"></i> Mark Complete</button>
                            </div>

                            <?php
                            if ($show_next == false && $isCurrentTopicsCompleted) { ?>
                                <div class="as-current-topic-outside-lesson">
                                    <a href="<?php echo $current_topic_outside_lesson_url; ?>" class="as-current-topic-outside-lesson-btn">Proceed to Next Lesson</a>
                                </div>
                            <?php }
                            ?>

                            <?php if ($show_next == false) { ?>
                                <div class="as-prev-d-none">
                                    <a href="<?php echo $next_topic_url; ?>" class="next">Next Topic &raquo;</a>
                                </div>
                            <?php } else { ?>
                                <div class="as-single-topic-next-butt">
                                    <a href="<?php echo $next_topic_url; ?>" class="next">Next Topic &raquo;</a>
                                </div>
                            <?php } ?>
                        </div>
    </div>
</main>