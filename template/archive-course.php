<?php
get_header();

global $wpdb;
$user_id = get_current_user_id();
$enrollments = $wpdb->get_col($wpdb->prepare(
    "SELECT course_id FROM {$wpdb->prefix}as_user_enrollment_history WHERE user_id = %d",
    $user_id
));

// Get the latest course with 'future' status
$latest_future_course = new WP_Query(array(
    'post_type' => 'course',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'orderby' => 'date',
    'order' => 'DESC'
));

$latest_course_id = $latest_future_course->have_posts() ? $latest_future_course->posts[0]->ID : null;

?>

<div class="as-course-container-fluid">
    <div class="as-course-container">
        <h1>Online Courses</h1>
    </div>
</div>

<div class="as-course-cards-container">
    <?php
    $args = array(
        'post_type' => 'course',
        'post_status' => 'any'
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) : ?>
        <?php while ($query->have_posts()) :
            $query->the_post();
        ?>

            <div class="as-course-card">
                <div class="as-course-card-header">
                    <?php if (!empty(get_the_post_thumbnail_url())) { ?>
                        <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>" />
                    <?php } else { ?>
                        <img src="https://www.generationsforpeace.org/wp-content/uploads/2018/03/empty-300x240.jpg" />
                    <?php } ?>

                    <?php
                    $categories = get_the_terms(get_the_ID(), 'courses_category');

                    if (!empty($categories) && !is_wp_error($categories)) {
                        if (!empty($categories[0]->name)) {
                            echo "<div class='as-course-badge'>";
                            echo "<span>" . esc_html($categories[0]->name) . "</span>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='as-course-badge' style='display:none'></div>";
                    }


                    if (get_post_status(get_the_ID()) == 'future') {
                        echo "<div class='as-registration-status'>";
                        echo "<b>Coming Soon</b>";
                        echo "</div>";
                    } elseif (get_the_ID() == $latest_course_id) {
                        echo "<div class='as-registration-status'>";
                        echo "<b>Register Now</b>";
                        echo "</div>";
                    }
                    ?>
                </div>
                <div class="as-course-card-body">
                    <h3><?php the_title(); ?></h3>
                    <?php
                    $Course_Languages = get_post_meta($post->ID, 'as_course_languages', true);
                    if (!empty($Course_Languages)) {
                        echo "<div class='as-course-languages-wraper'>";
                        echo "<i class='fa-solid fa-language fa-2xl'></i>";
                        echo "<div>";
                        echo "<b>Course Languages</b>";
                        echo "<p>" . $Course_Languages .  "</p>";
                        echo "</div>";
                        echo "</div>";
                    }

                    $course_collaborations = get_post_meta($post->ID, 'as_course_collaboration', true);
                    if (!empty($course_collaborations)) {
                        echo "<div class='as-course-collaborations-wraper as-course-section-border'>";
                        echo "<i class='fa-regular fa-handshake fa-2xl'></i>";
                        echo "<div>";
                        echo "<b>In Collaboration with</b>";
                        echo "<p>" . $course_collaborations .  "</p>";
                        echo "</div>";
                        echo "</div>";
                    }

                    $course_name = get_the_title($post->ID);
                    $table_name = $wpdb->prefix . 'as_user_enrollment_history';
                    $enrolled_users_count = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT COUNT(*) FROM $table_name WHERE course_name = %s ",
                            $course_name
                        )
                    );

                    if (!empty($enrolled_users_count)) {
                        echo "<div class='as-course-user-count-wraper as-course-section-border'>";
                        echo "<i class='fa-regular fa-thumbs-up fa-2xl'></i>";
                        echo "<div>";
                        echo "<b>Students to Date</b>";
                        echo "<p>" . $enrolled_users_count .  "</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
                <div class="as-course-card-footer">
                    <?php if (get_post_status(get_the_ID()) == 'future') : ?>
                        <p class="as-course-future-label">
                            This course will start on <?php echo get_the_date('F j, Y', get_the_ID()); ?>
                        </p>
                    <?php else : ?>
                        <?php if (!is_user_logged_in()) : ?>
                            <a class="as-course-button-user-login" href="http://192.168.29.81/search/wp-login.php?loggedout=true&wp_lang=en_US">
                                <i class="fa-solid fa-right-to-bracket"></i> Login
                            </a>
                        <?php else : ?>
                            <?php if (in_array(get_the_ID(), $enrollments)) : ?>
                                <a class="as-course-button-user-visit-course" href="<?php the_permalink(); ?>"><i class="fa-solid fa-street-view"></i> View Course</a>
                            <?php else : ?>
                                <button class="as-course-button-user-enrollment as-enroll-button as-transaction-details" data-course="<?php the_ID(); ?>">
                                    <i class="fa-regular fa-user"></i> Enroll
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            </div>
        <?php endwhile; ?>
    <?php else : ?>
        <p>No courses found</p>
    <?php endif; ?>
</div>

<?php
get_footer();
?>