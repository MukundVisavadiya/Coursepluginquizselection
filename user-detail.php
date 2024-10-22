<?php
// User enrollment list get for user
function as_user_profile_and_course_enrollment()
{
    $user_id = get_current_user_id();

    // Fetch user data
    $user_info = get_userdata($user_id);
    $user_name = $user_info->display_name;
    $user_email = $user_info->user_email;

    ob_start();
?>
    <div class="as-course-container-fluid">
        <div class="as-course-container">
            <h1>My Dashboard</h1>
        </div>
    </div>
    <div class="as-course-container">
        <div class="as-course-horizontal-card">
            <div class="as-course-row">
                <div class="as-course-col-sm-five">
                    <h6>Profile</h6>
                    <img class="as-horizontal-card-img" src="<?php echo plugin_dir_url(__FILE__) . 'assets/images/student_profile_photo.webp'; ?>" alt="Profile Photo">
                </div>
                <div class="as-course-col-sm-seven">
                    <div class="as-horizontal-card-body">
                        <h5 class="as-horizontal-card-body-title"><?php echo esc_html($user_name); ?></h5>
                        <p class="as-horizontal-card-body-txt"><?php echo esc_html($user_email); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <h3>My Courses</h3>
        <table id="as-student-dashboard">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Course Name</th>
                    <th>Transactions Status</th>
                    <th>Enrollment Time</th>
                </tr>
            </thead>
        </table>
    </div>
    </div>



<?php
    return ob_get_clean();
}
add_shortcode('as_course_user_dashboard', 'as_user_profile_and_course_enrollment');
