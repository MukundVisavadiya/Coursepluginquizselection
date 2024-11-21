<?php
get_header();
$current_url = home_url(add_query_arg(array(), $wp->request));
$parsed_url = parse_url($current_url);
$path_array = explode('/', trim($parsed_url['path'], '/'));
$course = get_page_by_path($path_array[2], OBJECT, 'course');
$course_id = $course->ID;
$course_dataes = get_post_meta($course_id, 'course_data', true);

$user_id = get_current_user_id();

global $wpdb;

$table_name = $wpdb->prefix . 'as_learnmore_user_activity';
$completedSteps = $wpdb->get_results($wpdb->prepare(
    "SELECT chapter_id, lesson_id, topic_id, section_id, quiz_id FROM $table_name WHERE user_id = %d AND course_id = %d AND activity_status = 'completed'",
    $user_id,
    $course_id
), ARRAY_A);

?>
<nav id="course-sidebar" class="as-navbar">
    <div class="as-navbar-container">
        <!--logo div-->
        <div class="as-course-label-wrapper">
            <div class="as-navbar-course-div">
                <a class="as-navbar-logo-link" href="<?php echo get_site_url() . '/course/' ?>">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <?php echo get_the_title($course_id); ?>
                    </p>
                </a>
                <button class="as-navbar-toggler"><i class="fa-solid fa-angle-right"></i></button>
            </div>
        </div>

        <div class="as-sildebar-chapter-list">
            <?php
            foreach ($course_dataes as $course_data) {
                $lesson_sidebar_dataes = $course_data['lessons'];
                $chapter_sidebar_id = $course_data['chapter_id'];
                $chapter_meta_slug = get_post_field('post_name', $chapter_sidebar_id);
                $course_slug = get_post_field('post_name', $course_id);
            ?>
                <div class="as-sidebar-chapter-accordion-parent  as-sidebar-chapter-parent-<?php echo $chapter_sidebar_id; ?>" data-chapter-id="<?php echo  $chapter_sidebar_id  ?>">
                    <?php
                    if (get_post_status($chapter_sidebar_id) === 'future') {

                        echo '<p style="cursor:default">' . get_the_title($chapter_sidebar_id) . '</p>';
                    ?>
                        <div class="as-scheduled-post-tooltip">
                            <svg xmlns="http://www.w3.org/2000/svg" cursor='not-allowed' width="16px" height="16px" viewBox="0 0 448 512">
                                <path fill="#ff0000" d="M128 0c17.7 0 32 14.3 32 32l0 32 128 0 0-32c0-17.7 14.3-32 32-32s32 14.3 32 32l0 32 48 0c26.5 0 48 21.5 48 48l0 48L0 160l0-48C0 85.5 21.5 64 48 64l48 0 0-32c0-17.7 14.3-32 32-32zM0 192l448 0 0 272c0 26.5-21.5 48-48 48L48 512c-26.5 0-48-21.5-48-48L0 192zm64 80l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm128 0l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM64 400l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zm112 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16z" />
                            </svg>
                            <span class="as-scheduled-post-tooltip-text"><?php echo "Available on " . get_the_date('F j, Y g:i a', $chapter_sidebar_id); ?></span>
                        </div>
                        <div>
                            <i class="fa-solid fa-angle-down" style="cursor:pointer"></i>
                        </div>
                    <?php } else { ?>
                        <a class="chapter-link" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/' ?>">
                            <?php
                            echo '<p>' . get_the_title($chapter_sidebar_id) . '</p>';
                            ?>
                        </a>
                        <div>
                            <?php
                            $isChapterCompleted = as_is_step_completed($completedSteps, $chapter_sidebar_id, 0, 0, 0, 0);
                            if ($isChapterCompleted) {
                                echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                            }
                            ?>
                            <i class="fa-solid fa-angle-down" style="cursor:pointer"></i>
                        </div>

                    <?php } ?>

                </div>
                <!-- Chapter Quiz -->
                <?php if (!empty($course_data['quiz_id'])) {
                    foreach ($course_data['quiz_id'] as $quiz_chapter_id) {
                        $quiz_chapter_meta_slug = get_post_field('post_name', $quiz_chapter_id);
                ?>
                        <div class="as-sidebar-chapter-quiz-accordion as-sidebar-chapter-parent-<?php echo $chapter_sidebar_id; ?>">
                            <a style="display: flex; justify-content: space-between;" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug   . '/quiz/' .  $quiz_chapter_meta_slug . '/' ?>">
                                <div style="display: flex; align-items: center;">
                                    <i style="padding-right:10px" class="fa-solid fa-circle-question"></i>
                                    <?php
                                    echo '<p>' . get_the_title($quiz_chapter_id) . '</p>';
                                    ?>
                                </div>
                                <div>
                                    <?php
                                    $isChapterQuizCompleted = as_is_step_completed($completedSteps, $chapter_sidebar_id, 0, 0, 0, $quiz_id);
                                    if ($isChapterQuizCompleted) {
                                        echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                    }
                                    ?>
                                </div>
                            </a>
                        </div>
                <?php
                    }
                } ?>
                <?php
                foreach ($lesson_sidebar_dataes as $lesson_data) {
                    $topic_sidebar_dataes = $lesson_data['topics'];
                    $lesson_sidebar_id = $lesson_data['lesson_id'];
                    $lesson_meta_slug = get_post_field('post_name',  $lesson_sidebar_id);

                    if (get_post_status($chapter_sidebar_id) === 'future') { ?>
                        <div class="as-lesson-accordion-sidebar-child as-lesson-accordion-sidebar-child-<?php echo $chapter_sidebar_id; ?>" data-lesson-id="<?php echo  $lesson_sidebar_id  ?>">
                            <div class="as-icon-title-wrapper-lesson">
                                <?php echo '<p style="cursor:default">' . get_the_title($lesson_sidebar_id) . '</p>'; ?>
                                <div>
                                    <i class="fa-solid fa-angle-down" style="cursor:pointer"></i>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="as-lesson-accordion-sidebar-child as-lesson-accordion-sidebar-child-<?php echo $chapter_sidebar_id; ?>" data-lesson-id="<?php echo  $lesson_sidebar_id  ?>">
                            <div class="as-icon-title-wrapper-lesson">
                                <a class="lesson-link" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/' ?>">
                                    <?php
                                    echo '<p>' . get_the_title($lesson_sidebar_id) . '</p>';
                                    ?>
                                </a>
                                <div>
                                    <?php
                                    $isLessonCompleted = as_is_step_completed($completedSteps, $chapter_sidebar_id, $lesson_sidebar_id, 0, 0, 0);
                                    if ($isLessonCompleted) {
                                        echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                    }
                                    ?>
                                    <i class="fa-solid fa-angle-down" style="cursor:pointer"></i>
                                </div>
                            </div>
                        </div>
                    <?php  }
                    ?>
                    <!-- Lesson Quiz -->
                    <?php if (!empty($lesson_data['quiz_id'])) {
                        foreach ($lesson_data['quiz_id'] as $quiz_lesson_id) {
                            $quiz_lesson_meta_slug = get_post_field('post_name', $quiz_lesson_id);
                    ?>
                            <div class="as-single-chapter-lesson-quiz-sidebar-accordion as-lesson-accordion-sidebar-child-<?php echo $chapter_sidebar_id; ?>">
                                <a style="display: flex; justify-content: space-between;" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug .  '/lessons/' . $lesson_meta_slug  . '/quiz/' . $quiz_topic_meta_slug . '/' ?>">
                                    <div style="display: flex; align-items: center;">
                                        <i style="padding-right:10px" class="fa-solid fa-circle-question"></i>
                                        <?php
                                        echo '<p>' . get_the_title($quiz_lesson_id) . '</p>';
                                        ?>
                                    </div>
                                    <div>
                                        <?php
                                        $isLessonQuizCompleted = as_is_step_completed($completedSteps, $chapter_sidebar_id, $lesson_sidebar_id, 0, 0, $quiz_id);
                                        if ($isLessonQuizCompleted) {
                                            echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                        }
                                        ?>
                                    </div>
                                </a>
                            </div>
                    <?php
                        }
                    } ?>

                    <?php
                    foreach ($topic_sidebar_dataes as $topic_data) {
                        $section_sidebar_dataes = $topic_data['sections'];
                        $topic_sidebar_id = $topic_data['topic_id'];
                        $topic_meta_slug = get_post_field('post_name',  $topic_sidebar_id);
                        if (get_post_status($chapter_sidebar_id) === 'future') { ?>
                            <div class="as-topic-accordion-sidebar-grandchild as-topic-accordion-sidebar-grandchild-<?php echo $lesson_sidebar_id; ?>" data-topic-id="<?php echo $topic_sidebar_id ?>">
                                <div class="as-icon-title-wrapper-topic">
                                    <?php echo '<p style="cursor:default">' . get_the_title($topic_sidebar_id) . '</p>'; ?>
                                    <div>
                                        <i class="fa-solid fa-angle-down" style="cursor:pointer"></i>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="as-topic-accordion-sidebar-grandchild as-topic-accordion-sidebar-grandchild-<?php echo $lesson_sidebar_id; ?>" data-topic-id="<?php echo $topic_sidebar_id ?>">
                                <div class="as-icon-title-wrapper-topic">
                                    <a class="topic-link" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' .  $topic_meta_slug  . '/' ?>">
                                        <?php
                                        echo '<p>' . get_the_title($topic_sidebar_id) . '</p>';
                                        ?>
                                    </a>
                                    <div>
                                        <?php
                                        $isTopicCompleted = as_is_step_completed($completedSteps, $chapter_sidebar_id, $lesson_sidebar_id, $topic_sidebar_id, 0, 0);
                                        if ($isTopicCompleted) {
                                            echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                        }
                                        ?>
                                        <i class="fa-solid fa-angle-down" style="cursor:pointer"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Topic Quiz -->
                            <?php

                            if (!empty($topic_data['quiz_id'])) {
                                foreach ($topic_data['quiz_id'] as $quiz_topic_id) {
                                    $quiz_topic_meta_slug = get_post_field('post_name', $quiz_topic_id);
                            ?>
                                    <div class="as-topic-chapter-sidebar-accordion-quiz as-topic-accordion-sidebar-grandchild-<?php echo $lesson_sidebar_id; ?>" data-topic-id="<?php echo $topic_id ?>">
                                        <a style="display: flex; justify-content: space-between;" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug .  '/lessons/' . $lesson_meta_slug . '/topics/' .  $topic_meta_slug  . '/quiz/' . $quiz_topic_meta_slug . '/' ?>">
                                            <div style="display: flex; align-items: center;">
                                                <i style="padding-right:10px" class="fa-solid fa-circle-question"></i>
                                                <?php
                                                echo '<p>' . get_the_title($quiz_topic_id) . '</p>';
                                                ?>
                                            </div>
                                            <div>
                                                <?php
                                                $isTopicQuizCompleted = as_is_step_completed($completedSteps, $chapter_sidebar_id, $lesson_sidebar_id, $topic_sidebar_id, 0, $quiz_id);
                                                if ($isTopicQuizCompleted) {
                                                    echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                                }
                                                ?>
                                            </div>
                                        </a>
                                    </div>
                            <?php
                                }
                            } ?>


                            <?php }

                        foreach ($section_sidebar_dataes as $section_data) {
                            $section_sidebar_id = $section_data['section_id'];
                            $sections_meta_slug = get_post_field('post_name',  $section_sidebar_id);

                            if (get_post_status($chapter_sidebar_id) === 'future') { ?>
                                <div class="as-section-accordion-sidebar-grand-grandchild as-section-accordion-sidebar-grand-grandchild-<?php echo $topic_sidebar_id; ?>">
                                    <div>
                                        <?php echo '<p style="cursor:default">' . get_the_title($section_sidebar_id) . '</p>'; ?>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="as-section-accordion-sidebar-grand-grandchild as-section-accordion-sidebar-grand-grandchild-<?php echo $topic_sidebar_id; ?>">
                                    <a class="section-link" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug . '/lessons/' . $lesson_meta_slug . '/topics/' .  $topic_meta_slug  . '/sections/' . $sections_meta_slug . '/' ?>">
                                        <div>
                                            <?php
                                            echo '<p>' . get_the_title($section_sidebar_id) . '</p>';
                                            $isSectionCompleted = as_is_step_completed($completedSteps, $chapter_sidebar_id, $lesson_sidebar_id, $topic_sidebar_id, $section_sidebar_id, 0);
                                            if ($isSectionCompleted) {
                                                echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                            }
                                            ?>
                                        </div>
                                    </a>
                                </div>
                                <!-- Section Quiz -->
                                <?php if (!empty($section_data['quiz_id'])) {
                                    foreach ($section_data['quiz_id'] as $quiz_id) {
                                        $quiz_meta_slug = get_post_field('post_name', $quiz_id);
                                ?>
                                        <div class="as-section-quiz-sidebar-accordion as-section-accordion-sidebar-grand-grandchild-<?php echo $topic_sidebar_id; ?>">
                                            <a style="display: flex; justify-content: space-between;" href="<?php echo get_site_url() . '/course/' . $course_slug . '/chapters/' . $chapter_meta_slug .  '/lessons/' . $lesson_meta_slug . '/topics/' .  $topic_meta_slug  . '/sections/' . $section_meta_slug . '/quiz/' . $quiz_meta_slug . '/' ?>">
                                                <div style="display: flex; align-items: center;">
                                                    <i style="padding-right:10px" class="fa-solid fa-circle-question"></i>
                                                    <?php
                                                    echo '<p>' . get_the_title($quiz_id) . '</p>';
                                                    ?>
                                                </div>
                                                <div>
                                                    <?php
                                                    $isSectionQuizCompleted = as_is_step_completed($completedSteps, $chapter_sidebar_id, $lesson_sidebar_id, $topic_sidebar_id, $section_sidebar_id, $quiz_id);
                                                    if ($isSectionQuizCompleted) {
                                                        echo ' <i class="fa-solid fa-check" style="color: green;"></i>';
                                                    }
                                                    ?>
                                                </div>
                                            </a>
                                        </div>
                                <?php
                                    }
                                } ?>
                        <?php }
                        } ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</nav>