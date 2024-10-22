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
$previous_chapter_url = '#';
$next_chapter_url = '#';
$current_chapter_outside_course_url = '#';
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
            $chapter_meta_slug = get_post_field('post_name', $chapter_id);

            if ($chapter_meta_slug == $path_array[4]) {

                if ($chapter_index > 0) {
                    $previous_chapter_id = $course_dataes[$chapter_index - 1]['chapter_id'];
                    $previous_chapter_slug = get_post_field('post_name', $previous_chapter_id);
                    $previous_chapter_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $previous_chapter_slug . '/';
                    $show_previous = true;
                }

                if ($chapter_index < count($course_dataes) - 1) {
                    $next_chapter_id = $course_dataes[$chapter_index + 1]['chapter_id'];
                    $next_chapter_slug = get_post_field('post_name', $next_chapter_id);
                    $next_chapter_url = get_site_url() . '/course/' . $course_slug . '/chapters/' . $next_chapter_slug . '/';
                    $show_next = true;
                }
                $current_chapter_outside_course_url = get_site_url() . '/course/' . $course_slug . '/';

                $user_id = get_current_user_id();

                global $wpdb;
                $table_name = $wpdb->prefix . 'as_learnmore_user_activity';

                /*......... Check Inside this chapter all section completed or not start ........... */
                $completedLesson = $wpdb->get_results($wpdb->prepare(
                    "SELECT  chapter_id, lesson_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
                    $user_id,
                    $course_id
                ), ARRAY_A);

                // Get chapter, lesson related topic ,section
                $relevantCompletedLesson = array_filter($completedLesson, function ($completed) use ($chapter_id) {
                    return $completed['chapter_id'] == $chapter_id;
                });

                $completedLessonIds = array_column($relevantCompletedLesson, 'lesson_id');

                $allLessonId = array_column($lesson_dataes, 'lesson_id');

                $allLessonCompleted = true;
                foreach ($allLessonId as $lessonId) {
                    if (!in_array($lessonId, $completedLessonIds)) {
                        $allLessonCompleted = false;
                        break;
                    }
                }

                /*......... END ........... */

                $completedChapter = $wpdb->get_results($wpdb->prepare(
                    "SELECT chapter_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
                    $user_id,
                    $course_id
                ), ARRAY_A);

                $isCurrentChapterCompleted = false;
                $previousChapterCompleted = false;
                $allPreviousChaptersCompleted = true;


                foreach ($course_dataes as $chapter) {
                    $isCompleted = array_filter($relevantCompletedLesson, function ($completed) use ($chapter) {
                        return $completed['chapter_id'] == $chapter['chapter_id'];
                    });

                    foreach ($completedLesson as $completedChapterdata) {

                        if ($completedChapterdata['chapter_id'] == $chapter_id && $completedChapterdata['lesson_id'] == 0  && !empty($isCompleted)) {
                            $isCurrentChapterCompleted = true;
                        }

                        $previous_chapter_id_condition = isset($previous_chapter_id) ? $previous_chapter_id : '';
                        if ($completedChapterdata['chapter_id'] == $previous_chapter_id_condition && $completedChapterdata['lesson_id'] == 0) {
                            $previousChapterCompleted = true;
                        }
                    }
                }


                if ($chapter_index > 0) {
                    foreach ($course_dataes as $prev_chapter_index => $prev_chapter_data) {
                        if ($prev_chapter_index < $chapter_index) {
                            $prev_isCompleted = array_filter($completedChapter, function ($completed) use ($prev_chapter_data) {
                                return $completed['chapter_id'] == $prev_chapter_data['chapter_id'];
                            });

                            if (empty($prev_isCompleted)) {
                                $allPreviousChaptersCompleted = false;
                                break;
                            }
                        }
                    }
                } ?>

                <div class="as-chapter-content-wrapper">
                    <?php
                    if (have_posts()) :
                        while (have_posts()) : the_post(); ?>
                            <div class="as-single-chapter-continer">
                                <header class="as-single-chapter-header">
                                    <h2 class="as-single-chapter-title"><?php the_title(); ?></h2>
                                </header>

                                <div class="as-single-chapter-content">
                                    <?php if (has_post_thumbnail($chapter_id)): ?>
                                        <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($chapter_id), 'single-post-thumbnail'); ?>
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
                    if (get_post_status($chapter_id) === 'future') {
                        echo '<div class="as-alert-info-message">';
                        echo '<p class="as-course-info-message"><i class="fa-solid fa-circle-exclamation"></i> Available on  "' . get_the_date('F j, Y g:i a', $chapter_sidebar_id) . '"</p>';
                        echo '</div>';
                    } else {

                        if ($chapter_index == 0) {
                            if (!$allLessonCompleted) {
                                echo '<div class="as-alert-info-message">';
                                echo '<p class="as-course-info-message"><i class="fa-solid fa-circle-exclamation"></i> Please Complete the All Lesson After You Go Next Chapter.</p>';
                                echo '</div>';
                                echo '<style>
                    .as-mark-complete-chapter-btn {
                        display: none;
                    }

                    .as-single-chapter-next-butt {
                        display: none;
                    }
                </style>';
                            } else {
                                if ($isCurrentChapterCompleted) {
                                    echo '<style>
                    .as-mark-complete-chapter-btn {
                        display: none;
                    }

                    .as-single-chapter-next-butt {
                        display: block;
                    }
                </style>';
                                } else {
                                    echo '<style>
                    .as-mark-complete-chapter-btn {
                        display: block;
                    }

                    .as-single-chapter-next-butt {
                        display: none;
                    }
                </style>';
                                }
                            }
                        } elseif ($chapter_index > 0 && !$allPreviousChaptersCompleted) {
                            echo '<div class="as-alert-error-message">';
                            echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous chapters.</p>';
                            echo '</div>';
                            echo '<style>
                    .as-mark-complete-chapter-btn {
                        display: none;
                    }

                    .as-single-chapter-next-butt {
                        display: none;
                    }

                    .as-single-chapter-page-label {
                        display: none;
                    }

                    .as-lesson-accordion-child {
                        display: none;
                    }

                    .as-chapter-content-wrapper{
                        display:none;
                    }
                </style>';
                        } else {
                            if (!$allLessonCompleted && $previousChapterCompleted) {
                                echo '<div class="as-alert-info-message">';
                                echo '<p class="as-course-info-message"><i class="fa-solid fa-circle-exclamation"></i> Please Complete the All Lesson After You Go Next Chapter.</p>';
                                echo '</div>';
                                echo '<style>
                    .as-mark-complete-chapter-btn {
                        display: none;
                    }

                    .as-single-chapter-next-butt {
                        display: none;
                    }
                </style>';
                            } else {
                                if ($previousChapterCompleted || $chapter_index == 0) {
                                    if ($isCurrentChapterCompleted) {
                                        echo '<style>
                    .as-mark-complete-chapter-btn {
                        display: none;
                    }

                    .as-single-chapter-next-butt {
                        display: block;
                    }
                </style>';
                                    } else {
                                        echo '<style>
                    .as-mark-complete-chapter-btn {
                        display: block;
                    }

                    .as-single-chapter-next-butt {
                        display: none;
                    }
                </style>';
                                    }
                                } else {
                                    echo '<div class="as-alert-error-message">';
                                    echo '<p class="as-course-uncompleted-message"><i class="fa-solid fa-circle-exclamation"></i> Please go back and complete the previous chapter.</p>';
                                    echo '</div>';
                                    echo '<style>
                    .as-mark-complete-chapter-btn {
                        display: none;
                    }

                    .as-single-chapter-next-butt {
                        display: none;
                    }

                    .as-single-chapter-page-label {
                        display: none;
                    }

                    .as-lesson-accordion-child {
                        display: none;
                    }

                     .as-chapter-content-wrapper{
                        display:none;
                    }
                </style>';
                                }
                            }
                        }
                    }
                    $isChapterCompleted = as_is_step_completed($completedSteps, $chapter_id, 0, 0, 0);

                    ?>

                    <div class="as-single-chapter-page-label" data-chapter-id="<?php echo $chapter_id; ?>">
                        <input type="hidden" class="as-all-course-chapter-data" value='<?php echo json_encode($course_dataes); ?>'>
                        <input type="hidden" class="as-current-chapter" value="<?php echo $chapter_index; ?>">
                        <input type="hidden" class="as-course-id" value="<?php echo $course_id; ?>">
                        <input type="hidden" class="as-chapter-id" value="<?php echo $chapter_id; ?>">
                        <input type="hidden" class="as-course-slug" value="<?php echo $course_slug; ?>">
                        <?php
                        echo '<p>' .   get_the_title($chapter_id) . '</p>';
                        if ($isChapterCompleted) {
                            echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                        }
                        ?>
                    </div>

                    <?php
                    foreach ($lesson_dataes as $lesson_data) {
                        $topic_dataes = $lesson_data['topics'];
                        $lesson_id = $lesson_data['lesson_id'];
                        $lesson_meta_slug = get_post_field('post_name', $lesson_id);
                    ?>
                        <div class="as-lesson-accordion-child as-lesson-accordion-child-<?php echo $chapter_id; ?>" data-lesson-id="<?php echo $lesson_id ?>">
                            <div class="as-dashboard-content-wrapper">
                                <?php
                                if (get_post_status($chapter_id) === 'future') {
                                    echo '<p style="cursor: default;">' . get_the_title($lesson_id) . '</p>';
                                } else {
                                ?>
                                    <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/' ?>">
                                        <?php
                                        echo '<p>' . get_the_title($lesson_id) . '</p>';
                                        ?>
                                    </a>
                                <?php } ?>
                                <div>
                                    <?php
                                    $isLessonCompleted = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, 0, 0);
                                    if ($isLessonCompleted) {
                                        echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                    }
                                    ?>
                                    <i class="fa-solid fa-angle-down"></i>
                                </div>
                            </div>
                        </div>

                        <?php
                        foreach ($topic_dataes as $topic_data) {
                            $section_dataes = $topic_data['sections'];
                            $topic_id = $topic_data['topic_id'];
                            $topic_meta_slug = get_post_field('post_name', $topic_id);

                        ?>
                            <div class="as-topic-accordion-grandchild as-topic-accordion-grandchild-<?php echo $lesson_id; ?>" data-topic-id="<?php echo $topic_id ?>">
                                <div class="as-dashboard-content-wrapper">
                                    <?php
                                    if (get_post_status($chapter_id) === 'future') {
                                        echo '<p style="cursor: default;">' . get_the_title($topic_id) . '</p>';
                                    } else {
                                    ?>
                                        <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/' ?>">
                                            <?php
                                            echo '<p>' . get_the_title($topic_id) . '</p>';
                                            ?>
                                        </a>
                                    <?php } ?>
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
                                <div class="as-section-accordion-grand-grandchild as-section-accordion-grand-grandchild-<?php echo $topic_id; ?>">
                                    <?php
                                    if (get_post_status($chapter_id) === 'future') {
                                        echo '<p style="cursor: default;">' . get_the_title($topic_id) . '</p>';
                                    } else {
                                    ?>
                                        <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' .  $topic_meta_slug  . '/sections/' . $section_meta_slug . '/' ?>">
                                            <?php
                                            echo '<p>' . get_the_title($section_id) . '</p>';
                                            $isSectionCompleted = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, $section_id);
                                            if ($isSectionCompleted) {
                                                echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                            }
                                            ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            <?php
                            } ?>
                        <?php } ?>
                    <?php } ?>
            <?php }
        } ?>
                </div>
                <div class="as-next-pre-wrapper">
                    <?php if ($show_previous == false) { ?>
                        <div class="as-prev-d-none">
                            <a href="<?php echo $previous_chapter_url ?>" class="previous">&laquo; Previous Chapter</a>
                        </div>
                    <?php } else { ?>
                        <div class="as-single-chaptre-pre-butt">
                            <a href="<?php echo $previous_chapter_url ?>" class="previous">&laquo; Previous Chapter</a>
                        </div>
                    <?php } ?>

                    <div class="as-course-link">
                        <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/' ?>" class="as-back-to-course">Back to Course</a>
                    </div>

                    <div class="as-mark-complete">
                        <button class="as-mark-complete-chapter-btn"><i class="fa-solid fa-check"></i> Mark to Complete</button>
                    </div>

                    <?php
                    if ($show_next == false && $isCurrentChapterCompleted) { ?>
                        <div class="as-current-chapter-outside-course">
                            <a href="<?php echo $current_chapter_outside_course_url; ?>" class="as-current-chapter-outside-course-btn">Course Completed</a>
                        </div>
                    <?php }
                    ?>

                    <?php $next_chapter = get_next_post(); ?>
                    <?php if ($show_next == false) { ?>
                        <div class="as-next-d-none">
                            <a href="<?php echo $next_chapter_url ?>" class="next">Next Chapter &raquo;</a>
                        </div>
                    <?php } else { ?>
                        <div class="as-single-chapter-next-butt">
                            <a href="<?php echo $next_chapter_url ?>" class="next">Next Chapter &raquo;</a>
                        </div>
                    <?php } ?>
                </div>
    </div>
</main>