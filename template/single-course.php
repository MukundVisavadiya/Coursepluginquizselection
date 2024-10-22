<?php
get_header();
/**
 * Course Sidebar
 */
require_once dirname(__FILE__)  . '/../sidebar.php';

$course_id = get_the_ID();
$user_id = get_current_user_id();
$course_dataes = get_post_meta($course_id, 'course_data', true);
$progress_data = as_calculate_course_progress($course_id, $user_id);

global $wpdb;

$table_name = $wpdb->prefix . 'as_learnmore_user_activity';
$completedSteps = $wpdb->get_results($wpdb->prepare(
    "SELECT chapter_id, lesson_id, topic_id, section_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
    $user_id,
    $course_id
), ARRAY_A);

while (have_posts()) {
    the_post();
?>

    <!-- Main code for dasboard data -->
    <main class="as-dashboard">
        <div class="as-course-container">
            <div class="as-single-course-header">
                <h2 class="as-single-course-title"><?php echo get_the_title($course_id) ?></h2>
            </div>
            <div class="as-course-image">
                <?php if (has_post_thumbnail($course_id)): ?>
                    <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($course_id), 'single-post-thumbnail'); ?>
                    <img src="<?php echo $image[0]; ?>" alt="course.jpg" />
                <?php endif; ?>
                <p><?php the_content() ?></p>
            </div>

            <!-- This progress add total steps & completed steps -->
            <div class="as-course-progressbar">
                <p><?php echo $progress_data['progress']; ?>% Completed <?php echo $progress_data['completed_steps']; ?>/<?php echo $progress_data['total_steps']; ?> Steps</p>
                <div class="as-progress-bar">
                    <div class="as-progress-bar-fill" style="width: <?php echo $progress_data['progress']; ?>%;"></div>
                </div>
            </div>
            <div class="as-chapter-accordion-list">
                <?php
                foreach ($course_dataes as $course_data) {
                    $lesson_dataes = $course_data['lessons'];
                    $chapter_id = $course_data['chapter_id'];
                    $chapter_meta_slug = get_post_field('post_name', $chapter_id); ?>
                    <div class="as-chapter-accordion-parent active" data-chapter-id="<?php echo $chapter_id; ?>">
                        <?php
                        if (get_post_status($chapter_id) === 'future') {
                            echo '<p style="cursor:default">' . get_the_title($chapter_id) . '</p>';
                        ?>
                            <div class="as-scheduled-post-tooltip">
                                <svg xmlns="http://www.w3.org/2000/svg" cursor='not-allowed' width="16px" height="16px" viewBox="0 0 448 512">
                                    <path fill="#ff0000" d="M128 0c17.7 0 32 14.3 32 32l0 32 128 0 0-32c0-17.7 14.3-32 32-32s32 14.3 32 32l0 32 48 0c26.5 0 48 21.5 48 48l0 48L0 160l0-48C0 85.5 21.5 64 48 64l48 0 0-32c0-17.7 14.3-32 32-32zM0 192l448 0 0 272c0 26.5-21.5 48-48 48L48 512c-26.5 0-48-21.5-48-48L0 192zm64 80l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm128 0l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM64 400l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zm112 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16z" />
                                </svg>
                                <span class="as-scheduled-post-tooltip-text as-tooltip-descktop-text"><?php echo "Available on " . get_the_date('F j, Y g:i a', $chapter_sidebar_id); ?></span>
                            </div>
                            <div>
                                <i class="fa-solid fa-angle-down" style="cursor:pointer"></i>
                            </div>
                        <?php } else { ?>
                            <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/' ?>">
                                <?php
                                echo '<p>' . get_the_title($chapter_id) . '</p>';
                                $isChapterCompleted = as_is_step_completed($completedSteps, $chapter_id, 0, 0, 0);
                                ?>
                            </a>
                            <div>
                                <?php
                                if ($isChapterCompleted) {
                                    echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                }
                                ?>
                                <i class="fa-solid fa-angle-up"></i>
                            </div>
                        <?php } ?>
                    </div>

                    <?php

                    foreach ($lesson_dataes as $lesson_data) {
                        $topic_dataes = $lesson_data['topics'];
                        $lesson_id = $lesson_data['lesson_id'];
                        $lesson_meta_slug = get_post_field('post_name',  $lesson_id);

                        if (get_post_status($chapter_id) === 'future') { ?>
                            <div class="as-lesson-accordion-child as-lesson-accordion-child-<?php echo $chapter_id; ?>" data-lesson-id="<?php echo $lesson_id ?>">
                                <div class="as-dashboard-content-wrapper">
                                    <?php echo '<p style="cursor:default">' . get_the_title($lesson_id) . '</p>'; ?>
                                    <div>
                                        <i class="fa-solid fa-angle-down" style="cursor:pointer"></i>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="as-lesson-accordion-child as-lesson-accordion-child-<?php echo $chapter_id; ?>" data-lesson-id="<?php echo $lesson_id ?>">
                                <div class="as-dashboard-content-wrapper">
                                    <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/' ?>">
                                        <?php
                                        echo '<p>' . get_the_title($lesson_id) . '</p>';
                                        $isLessonCompleted = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, 0, 0);
                                        ?>
                                    </a>
                                    <div>
                                        <?php
                                        if ($isLessonCompleted) {
                                            echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                        }
                                        ?>
                                        <i class="fa-solid fa-angle-down"></i>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        foreach ($topic_dataes as $topic_data) {
                            $section_dataes = $topic_data['sections'];
                            $topic_id = $topic_data['topic_id'];
                            $topic_meta_slug = get_post_field('post_name',  $topic_id);
                            if (get_post_status($chapter_id) === 'future') {
                        ?>
                                <div class="as-topic-accordion-grandchild as-topic-accordion-grandchild-<?php echo $lesson_id; ?>" data-topic-id="<?php echo $topic_id ?>">
                                    <div class="as-dashboard-content-wrapper">
                                        <?php echo '<p style="cursor:default">' . get_the_title($topic_id) . '</p>'; ?>
                                        <div>
                                            <i class="fa-solid fa-angle-down" style="cursor:pointer"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="as-topic-accordion-grandchild as-topic-accordion-grandchild-<?php echo $lesson_id; ?>" data-topic-id="<?php echo $topic_id ?>">
                                    <div class="as-dashboard-content-wrapper">
                                        <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/' ?>">
                                            <?php
                                            echo '<p>' . get_the_title($topic_id) . '</p>';
                                            $isTopicCompleted = as_is_step_completed($completedSteps, $chapter_id, $lesson_id, $topic_id, 0);
                                            ?>
                                        </a>
                                        <div>
                                            <?php
                                            if ($isTopicCompleted) {
                                                echo '<i class="fa-solid fa-check" style="color: green;"></i>';
                                            }
                                            ?>
                                            <i class="fa-solid fa-angle-down"></i>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            foreach ($section_dataes as $section_data) {
                                $section_id = $section_data['section_id'];
                                $section_meta_slug = get_post_field('post_name', $section_id);
                                if (get_post_status($chapter_id) === 'future') {
                                ?>
                                    <div class="as-section-accordion-grand-grandchild as-section-accordion-grand-grandchild-<?php echo $topic_id; ?>">
                                        <?php echo '<p style="cursor:default">' . get_the_title($topic_id) . '</p>'; ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="as-section-accordion-grand-grandchild as-section-accordion-grand-grandchild-<?php echo $topic_id; ?>">
                                        <a href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' . $topic_meta_slug . '/sections/' . $section_meta_slug . '/' ?>">
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
                            } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </main>

<?php }
?>