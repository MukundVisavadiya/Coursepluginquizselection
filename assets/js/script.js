// Course Enrollment
jQuery(document).ready(function () {
  jQuery(document).on("click", ".as-enroll-button", function () {
    var courseId = jQuery(this).data("course");

    jQuery.ajax({
      url: as_enroll_course.ajaxurl,
      type: "POST",
      data: {
        action: "as_enroll_course",
        nonce: as_enroll_course.nonce,
        course_id: courseId,
      },
      success: function (response) {
        var data = asConvertJSON(response);
        if (data.success) {
          window.location.href = data.redirect_url;
        } else {
          alert("Enrollment failed: " + response.data.message);
          window.location.href = response.data.redirect_url;
        }
      },
      error: function (xhr, status, error) {
        alert("An error occurred. Please try again.");
      },
    });
  });
});

// Trasition History
jQuery(document).ready(function () {
  jQuery(document).on("click", ".as-transaction-details", function () {
    var courseId = jQuery(this).data("course");

    jQuery.ajax({
      url: as_enroll_course_transaction.ajaxurl,
      type: "POST",
      data: {
        action: "as_enroll_course_transaction",
        nonce: as_enroll_course_transaction.nonce,
        course_id: courseId,
      },
      success: function (response) {
        var data = asConvertJSON(response);
        if (data.success) {
          window.location.href = data.redirect_url;
        } else {
          alert("Enrollment failed: " + response.data.message);
          window.location.href = response.data.redirect_url;
        }
      },
      error: function (xhr, status, error) {
        alert("An error occurred. Please try again.");
      },
    });
  });
});

function asConvertJSON(str) {
  try {
    return JSON.parse(str);
  } catch (e) {
    return str;
  }
}

// datatable jQuery student dashboard tabel
jQuery(document).ready(function () {
  jQuery("#as-student-dashboard").DataTable({
    processing: true,
    serverSide: true,
    serverMethod: "post",
    ajax: {
      url: as_student_dashboard_enrollment.ajaxurl,
      data: {
        action: "as_student_dashboard_enrollment",
        nonce: as_student_dashboard_enrollment.nonce,
      },
    },
    pageLength: 10,
    columns: [
      { data: "course_id" },
      { data: "course_name" },
      { data: "transaction_status" },
      { data: "enrollment_time" },
    ],
  });
});

// Sidebar Manage
jQuery(document).on("click", ".as-navbar-toggler", function () {
  jQuery(".as-navbar").toggleClass("active");

  if (jQuery("nav").hasClass("active")) {
    jQuery(".as-navbar-toggler i")
      .removeClass("fa-angle-right")
      .addClass("fa-angle-left");
    jQuery(".as-sildebar-chapter-list").show();
  } else {
    jQuery(".as-navbar-toggler i")
      .removeClass("fa-angle-left")
      .addClass("fa-angle-right");
    jQuery(".as-sildebar-chapter-list").hide();
  }
});

// Accordion Manage for Single Course page Chapter
jQuery(document).on("click", ".as-chapter-accordion-parent", function () {
  var dataChapterId = jQuery(this).data("chapter-id");
  jQuery(this).toggleClass("active");
  jQuery(`.as-lesson-accordion-child-${dataChapterId}`).slideToggle();

  if (jQuery(this).hasClass("active")) {
    jQuery(this).find("i").removeClass("fa-angle-down").addClass("fa-angle-up");
  } else {
    jQuery(this).find("i").removeClass("fa-angle-up").addClass("fa-angle-down");
  }
});

// Accordion Manage for Single Course page Lesson
jQuery(document).on("click", ".as-lesson-accordion-child", function () {
  var dataLessonId = jQuery(this).data("lesson-id");

  jQuery(this).toggleClass("active");
  jQuery(`.as-topic-accordion-grandchild-${dataLessonId}`).slideToggle();

  if (jQuery(this).hasClass("active")) {
    jQuery(this).find("i").removeClass("fa-angle-down").addClass("fa-angle-up");
  } else {
    jQuery(this).find("i").removeClass("fa-angle-up").addClass("fa-angle-down");
  }
});

// Accordion Manage for Single Course page Topic
jQuery(document).on("click", ".as-topic-accordion-grandchild", function () {
  var dataTopicId = jQuery(this).data("topic-id");
  jQuery(this).toggleClass("active");
  jQuery(`.as-section-accordion-grand-grandchild-${dataTopicId}`).slideToggle();

  if (jQuery(this).hasClass("active")) {
    jQuery(this).find("i").removeClass("fa-angle-down").addClass("fa-angle-up");
  } else {
    jQuery(this).find("i").removeClass("fa-angle-up").addClass("fa-angle-down");
  }
});

// Accordion Manage for chapter for sidebar
jQuery(document).on(
  "click",
  ".as-sidebar-chapter-accordion-parent",
  function () {
    var dataChapterId = jQuery(this).data("chapter-id");
    jQuery(this).toggleClass("active");
    jQuery(
      `.as-sildebar-chapter-list .as-lesson-accordion-sidebar-child-${dataChapterId}`
    ).slideToggle();

    if (jQuery(this).hasClass("active")) {
      jQuery(this)
        .find("i")
        .removeClass("fa-angle-down")
        .addClass("fa-angle-up");
    } else {
      jQuery(this)
        .find("i")
        .removeClass("fa-angle-up")
        .addClass("fa-angle-down");
    }
  }
);

// Accordion Manage for Lesson sidebar
jQuery(document).on("click", ".as-lesson-accordion-sidebar-child", function () {
  var dataLessonId = jQuery(this).data("lesson-id");
  jQuery(this).toggleClass("active");
  jQuery(
    `.as-topic-accordion-sidebar-grandchild-${dataLessonId}`
  ).slideToggle();

  if (jQuery(this).hasClass("active")) {
    jQuery(this).find("i").removeClass("fa-angle-down").addClass("fa-angle-up");
  } else {
    jQuery(this).find("i").removeClass("fa-angle-up").addClass("fa-angle-down");
  }
});

// Accordion Manage for Topic sidebar
jQuery(document).on(
  "click",
  ".as-topic-accordion-sidebar-grandchild",
  function () {
    var dataTopicId = jQuery(this).data("topic-id");
    jQuery(this).toggleClass("active");
    jQuery(
      `.as-section-accordion-sidebar-grand-grandchild-${dataTopicId}`
    ).slideToggle();

    if (jQuery(this).hasClass("active")) {
      jQuery(this)
        .find("i")
        .removeClass("fa-angle-down")
        .addClass("fa-angle-up");
    } else {
      jQuery(this)
        .find("i")
        .removeClass("fa-angle-up")
        .addClass("fa-angle-down");
    }
  }
);

// Accordion Manage for topic lesson-single page
jQuery(document).on("click", ".as-single-topic-accordion", function () {
  var dataTopicId = jQuery(this).data("topic-id");
  jQuery(this).toggleClass("active");
  jQuery(`.as-section-accordion-grand-grandchild-${dataTopicId}`).slideToggle();

  if (jQuery(this).hasClass("active")) {
    jQuery(this).find("i").removeClass("fa-angle-down").addClass("fa-angle-up");
  } else {
    jQuery(this).find("i").removeClass("fa-angle-up").addClass("fa-angle-down");
  }
});

//using ajax to mark to complete in section page
jQuery(document).ready(function () {
  var allSections = JSON.parse(jQuery(".as-all-course-section-data").val());
  var currentSectionIndex = jQuery(".as-current-section").val();
  var courseId = jQuery(".as-course-id").val();
  var sectionId = jQuery(".as-section-id").val();
  var chapterId = jQuery(".as-chapter-id").val();
  var lessonId = jQuery(".as-lesson-id").val();
  var topicId = jQuery(".as-topic-id").val();
  var courseSlug = jQuery(".as-course-slug").val();
  var chapterSlug = jQuery(".as-chapter-slug").val();
  var lessonSlug = jQuery(".as-lesson-slug").val();
  var topicSlug = jQuery(".as-topic-slug").val();

  jQuery(".as-mark-complete-section-btn").on("click", function () {
    jQuery.ajax({
      url: as_mark_complete_section.ajaxurl,
      type: "POST",
      data: {
        action: "as_mark_complete_section",
        nonce: as_mark_complete_section.nonce,
        course_id: courseId,
        section_index: currentSectionIndex,
        chapter_id: chapterId,
        lesson_id: lessonId,
        topic_id: topicId,
        section_id: sectionId,
        course_slug: courseSlug,
        chapter_slug: chapterSlug,
        lesson_slug: lessonSlug,
        topic_slug: topicSlug,
        all_sections: JSON.stringify(allSections),
      },
      success: function (response) {
        if (response.success) {
          if (response.data.first_quiz_url !== "#") {
            window.location.href = response.data.first_quiz_url;
          } else if (response.data.next_section_url !== "#") {
            window.location.href = response.data.next_section_url;
          } else if (response.data.next_quiz_url !== "#") {
            window.location.href = response.data.next_quiz_url;
          }
        } else {
          if (response.data && response.data.next_topic_url) {
            window.location.href = response.data.next_topic_url;
          } else {
            console.log("No next section or fallback URL available.");
          }
        }
      },
      error: function (xhr, status, error) {
        console.log("AJAX call error:", status, error);
      },
    });
  });
});

//using ajax to mark to complete in Topic page
jQuery(document).ready(function () {
  var allTopics = JSON.parse(jQuery(".as-all-course-topic-data").val());
  var currentTopicIndex = jQuery(".as-current-topic").val();
  var courseId = jQuery(".as-course-id").val();
  var chapterId = jQuery(".as-chapter-id").val();
  var lessonId = jQuery(".as-lesson-id").val();
  var topicId = jQuery(".as-topic-id").val();
  var courseSlug = jQuery(".as-course-slug").val();
  var chapterSlug = jQuery(".as-chapter-slug").val();
  var lessonSlug = jQuery(".as-lesson-slug").val();

  jQuery(".as-mark-complete-topic-btn").on("click", function () {
    jQuery.ajax({
      url: as_mark_complete_topic.ajaxurl,
      type: "POST",
      data: {
        action: "as_mark_complete_topic",
        nonce: as_mark_complete_topic.nonce,
        course_id: courseId,
        topic_index: currentTopicIndex,
        chapter_id: chapterId,
        lesson_id: lessonId,
        topic_id: topicId,
        course_slug: courseSlug,
        chapter_slug: chapterSlug,
        lesson_slug: lessonSlug,
        all_topics: JSON.stringify(allTopics),
      },
      success: function (response) {
        if (response.success) {
          if (response.data.next_topic_url) {
            window.location.href = response.data.next_topic_url;
          }
        } else {
          if (response.data && response.data.next_topic_url) {
            window.location.href = response.data.next_topic_url;
          } else {
            console.log("No next section or fallback URL available.");
          }
        }
      },
      error: function (xhr, status, error) {
        console.log("AJAX call error:", status, error);
      },
    });
  });
});

// using ajax to mark to complete in Lesson page
jQuery(document).ready(function () {
  var allLesson = JSON.parse(jQuery(".as-all-course-lesson-data").val());
  var currentLessonIndex = jQuery(".as-current-lesson").val();
  var courseId = jQuery(".as-course-id").val();
  var chapterId = jQuery(".as-chapter-id").val();
  var lessonId = jQuery(".as-lesson-id").val();
  var courseSlug = jQuery(".as-course-slug").val();
  var chapterSlug = jQuery(".as-chapter-slug").val();

  jQuery(".as-mark-complete-lesson-btn").on("click", function () {
    jQuery.ajax({
      url: as_mark_complete_lesson.ajaxurl,
      type: "POST",
      data: {
        action: "as_mark_complete_lesson",
        nonce: as_mark_complete_lesson.nonce,
        course_id: courseId,
        lesson_index: currentLessonIndex,
        chapter_id: chapterId,
        lesson_id: lessonId,
        course_slug: courseSlug,
        chapter_slug: chapterSlug,
        all_lesson: JSON.stringify(allLesson),
      },
      success: function (response) {
        if (response.success) {
          if (response.data.next_lesson_url) {
            window.location.href = response.data.next_lesson_url;
          }
        } else {
          if (response.data && response.data.next_lesson_url) {
            window.location.href = response.data.next_lesson_url;
          } else {
            console.log("No next section or fallback URL available.");
          }
        }
      },
      error: function (xhr, status, error) {
        console.log("AJAX call error:", status, error);
      },
    });
  });
});

// using ajax to mark to complete in chapter page
jQuery(document).ready(function () {
  var allChapter = JSON.parse(jQuery(".as-all-course-chapter-data").val());
  var currentChapterIndex = jQuery(".as-current-chapter").val();
  var courseId = jQuery(".as-course-id").val();
  var chapterId = jQuery(".as-chapter-id").val();
  var courseSlug = jQuery(".as-course-slug").val();

  jQuery(".as-mark-complete-chapter-btn").on("click", function () {
    jQuery.ajax({
      url: as_mark_complete_chapter.ajaxurl,
      type: "POST",
      data: {
        action: "as_mark_complete_chapter",
        nonce: as_mark_complete_chapter.nonce,
        course_id: courseId,
        chapter_index: currentChapterIndex,
        chapter_id: chapterId,
        course_slug: courseSlug,
        all_chapter: JSON.stringify(allChapter),
      },
      success: function (response) {
        if (response.success) {
          if (response.data.next_chapter_url) {
            window.location.href = response.data.next_chapter_url;
          }
        } else {
          if (response.data && response.data.next_chapter_url) {
            window.location.href = response.data.next_chapter_url;
          } else {
            console.log("No next section or fallback URL available.");
          }
        }
      },
      error: function (xhr, status, error) {
        console.log("AJAX call error:", status, error);
      },
    });
  });
});

// Quiz Management Code

jQuery(document).ready(function () {
  const startQuizBtn = jQuery("#as-start-quiz");
  const quizQuestionsContainer = jQuery(".as-quiz-questions");
  const steps = jQuery(".as-quiz-step");
  const timeLimit = jQuery(".as-compete-quiz-time").val();
  const timeLimitToString = timeLimit * 60 * 1000;
  let currentStep = 0;
  let timerInterval;
  let startTime;
  let warningIssued = false;

  //format time function
  function formatTime(timeInMillis) {
    let seconds = Math.floor((timeInMillis / 1000) % 60);
    let minutes = Math.floor((timeInMillis / (1000 * 60)) % 60);
    let hours = Math.floor((timeInMillis / (1000 * 60 * 60)) % 24);

    return [hours, minutes, seconds]
      .map((unit) => String(unit).padStart(2, "0"))
      .join(":");
  }

  // start the quiz timer
  function startTimer() {
    startTime = new Date().getTime();
    timerInterval = setInterval(function () {
      let currentTime = new Date().getTime();
      let elapsedTime = currentTime - startTime;
      let remainingTime = timeLimitToString - elapsedTime;

      jQuery(".as-quiz-timer").text("Time Left: " + formatTime(remainingTime));
      jQuery(".as-quiz-completed-time").hide();

      if (remainingTime <= 60 * 1000 && !warningIssued) {
        alert("You have 1 minute left to complete the quiz!");
        warningIssued = true;
      }

      if (remainingTime <= 0) {
        clearInterval(timerInterval);
        // alert('Time is up! The quiz will be automatically submitted.');
        jQuery(".as-quiz-timer").hide();
        finishQuiz();
      }
    }, 1000);
  }

  // Start quiz
  startQuizBtn.on("click", function () {
    startQuizBtn.hide();
    quizQuestionsContainer.show();
    startTimer();
  });

  //auto finish quiz
  function finishQuiz() {
    clearInterval(timerInterval);
    jQuery("#finish-quiz").click();
  }

  // check if any inputs are selected
  function checkInputs(step) {
    const inputs = jQuery(step).find(
      'input[type="radio"], input[type="checkbox"]'
    );
    const nextButton = jQuery(step).find(".as-next-step");
    const finishButton = jQuery(step).find("#finish-quiz");

    const isChecked = inputs.is(":checked");
    nextButton.prop("disabled", !isChecked);
    finishButton.prop("disabled", !isChecked);
  }

  steps.each(function (index, step) {
    const inputs = jQuery(step).find(
      'input[type="radio"], input[type="checkbox"]'
    );
    const nextButton = jQuery(step).find(".as-next-step");
    const finishButton = jQuery(step).find("#finish-quiz");

    inputs.on("change", function () {
      checkInputs(step);
    });

    checkInputs(step);

    if (nextButton.length) {
      nextButton.on("click", function () {
        jQuery(steps[currentStep]).hide();
        currentStep++;
        jQuery(steps[currentStep]).show();

        checkInputs(steps[currentStep]);
      });
    }

    // get quiz data
    if (finishButton.length) {
      finishButton.on("click", function () {
        let quizData = [];
        let quizId = jQuery(".as_quiz_id").val();
        let userId = jQuery(".as_quiz_user_id").val();

        // Stop the timer
        clearInterval(timerInterval);
        jQuery(".as-quiz-timer").hide();

        let endTime = new Date().getTime();
        let timeTaken = endTime - startTime;
        let formattedTime = formatTime(timeTaken);

        steps.each(function (index, step) {
          const questionId = jQuery(step).data("question-id");
          const selectedAnswers = jQuery(step)
            .find("input:checked")
            .map(function () {
              return jQuery(this).val();
            })
            .get();

          quizData.push({
            user_id: userId,
            question_id: questionId,
            time_taken: formattedTime,
            selected_answers: selectedAnswers,
          });
        });

        jQuery.ajax({
          url: quiz_ajax_object_data.ajaxurl,
          type: "POST",
          data: {
            action: "submit_quiz",
            quiz_id: quizId,
            quiz_data: quizData,
          },
          beforeSend: function () {
            jQuery("#as-overlay").show();
            jQuery(".as-quiz-result-processing").show();
            quizQuestionsContainer.hide();
          },
          success: function (response) {
            jQuery("#as-overlay").hide();
            jQuery(".as-quiz-result-processing").hide();
            quizQuestionsContainer.hide();

            if (response.success === true) {
              var quiz_url = jQuery("#finish-quiz").data("quiz-url");
              var current_path_parts = quiz_url
                .split("/")
                .filter(function (part) {
                  return part !== "";
                });
              var section_path_slug =
                jQuery(".as-section-path-slug").val() || "";
              var topic_path_slug = jQuery(".as-topic-path-slug").val() || "";
              var lesson_path_slug = jQuery(".as-lesson-path-slug").val() || "";
              console.log(lesson_path_slug);

              if (current_path_parts[11] == section_path_slug) {
                var nextSectionUrl =
                  jQuery(".as-next-section-quiz-url").val() || "";
                var previousSectonUrl =
                  jQuery(".as-previous-section-quiz-url").val() || "";
                var nextparenttopicUrl =
                  jQuery(".as-next-topic-url").val() || "";
                var showNext = jQuery(".as-show-next").val() || "";

                jQuery(".as-quiz-results").empty();

                const totalScoreHtml = `<div class="as-score-summary">
                <h3>Results: </h3>
                <p>Your time: ${formattedTime}</p>
                  <div class="as-persantage-wrapper">
                    <p>You have obtained <b>${
                      response.data.score
                    }</b> out of <b>${response.data.total_points}</b> marks</p>
                    <p> ${response.data.message}</p>
                  </div>
                  <div class="as-review-answers-wrapper">
                    <a class="as-clts-quiz-previous-butt" href="${previousSectonUrl}">&laquo; Previous</a>
                    <a class="as-review-answers">See Correct/Wrong</a>
                    <a class="as-restart-quizz" href="${quiz_url}">Restart Quize</a>
                    ${
                      showNext == 1
                        ? `<a class="as-clts-quiz-next-butt" href="${nextSectionUrl}">Next &raquo;</a>`
                        : `
                      <div class="as-current-section-outside-topic" style="margin-top: 20px;">
                        <a href="${nextparenttopicUrl}" class="as-current-section-outside-topic-btn">Proceed to Next Topic</a>
                      </div>
                    `
                    } 
                  </div>
                
                </div> `;
                jQuery(".as-quiz-results").append(totalScoreHtml);

                jQuery(".as-review-answers").on("click", function () {
                  jQuery(".as-quiz-feedback-results-wrapper").toggle();
                });
              }
              // this result for topic quiz
              else if (current_path_parts[9] == topic_path_slug) {
                var nextTopicUrl =
                  jQuery(".as-next-topic-quiz-url").val() || "";
                var previousTopicUrl =
                  jQuery(".as-previous-topic-quiz-url").val() || "";
                var showNext = jQuery(".as-show-topic-next").val() || "";

                jQuery(".as-quiz-results").empty();

                const totalScoreHtml = `<div class="as-score-summary">
                            <h3>Results: </h3>
                            <p>Your time: ${formattedTime}</p>
                              <div class="as-persantage-wrapper">
                                <p>You have obtained <b>${
                                  response.data.score
                                }</b> out of <b>${
                  response.data.total_points
                }</b> marks</p>
                                <p> ${response.data.message}</p>
                              </div>
                              <div class="as-review-answers-wrapper">
                                <a class="as-clts-quiz-previous-butt" href="${previousTopicUrl}">&laquo; Previous</a>
                                <a class="as-review-answers">See Correct/Wrong</a>
                                <a class="as-restart-quizz" href="${quiz_url}">Restart Quize</a>
                                ${
                                  showNext == 1
                                    ? `<a class="as-clts-quiz-next-butt" href="${nextTopicUrl}">Next &raquo;</a>`
                                    : `
                                  <div class="as-current-section-outside-topic" style="margin-top: 20px;">
                                    <a href="${previousTopicUrl}" class="as-current-section-outside-topic-btn">Proceed to Next Lesson</a>
                                  </div>
                                `
                                } 
                              </div>
            
                            </div> `;
                jQuery(".as-quiz-results").append(totalScoreHtml);

                jQuery(".as-review-answers").on("click", function () {
                  jQuery(".as-quiz-feedback-results-wrapper").toggle();
                });
              }
              // this result for lesson quiz
              else if (current_path_parts[7] == lesson_path_slug) {
                var nextLessonUrl =
                  jQuery(".as-next-lesson-quiz-url").val() || "";
                var previousLessonUrl =
                  jQuery(".as-previous-lesson-quiz-url").val() || "";
                var showNext = jQuery(".as-show-lesson-next").val() || "";

                jQuery(".as-quiz-results").empty();

                const totalScoreHtml = `<div class="as-score-summary">
                            <h3>Results: </h3>
                            <p>Your time: ${formattedTime}</p>
                              <div class="as-persantage-wrapper">
                                <p>You have obtained <b>${
                                  response.data.score
                                }</b> out of <b>${
                  response.data.total_points
                }</b> marks</p>
                                <p> ${response.data.message}</p>
                              </div>
                              <div class="as-review-answers-wrapper">
                                <a class="as-clts-quiz-previous-butt" href="${previousLessonUrl}">&laquo; Previous</a>
                                <a class="as-review-answers">See Correct/Wrong</a>
                                <a class="as-restart-quizz" href="${quiz_url}">Restart Quize</a>
                                ${
                                  showNext == 1
                                    ? `<a class="as-clts-quiz-next-butt" href="${nextLessonUrl}">Next &raquo;</a>`
                                    : `
                                  <div class="as-current-section-outside-topic" style="margin-top: 20px;">
                                    <a href="${previousLessonUrl}" class="as-current-section-outside-topic-btn">Proceed to Previous Lesson</a>
                                  </div>
                                `
                                } 
                              </div>
            
                            </div> `;
                jQuery(".as-quiz-results").append(totalScoreHtml);

                jQuery(".as-review-answers").on("click", function () {
                  jQuery(".as-quiz-feedback-results-wrapper").toggle();
                });
              }

              // Append detailed feedback for each question

              response.data.feedback.forEach(function (feedback) {
                const questionFeedback = `
              < div class="as-question-feedback" >
                          <h4>${feedback.question}</h4>
                          <ul>
                              ${feedback.correct_answers
                                .map(function (correctAnswer) {
                                  return `<li class="correct-answer" style="color: green; border: 1px solid;padding: 10px;margin-bottom: 10px;"><b>Correct Answer:</b> ${correctAnswer}</li>`;
                                })
                                .join("")}
                              ${feedback.selected_answers
                                .map(function (selectedAnswer) {
                                  const isCorrect =
                                    feedback.correct_answers.includes(
                                      selectedAnswer
                                    );
                                  return `<li class="${
                                    isCorrect
                                      ? "correct-answer"
                                      : "wrong-answer"
                                  }" style="color: ${isCorrect ? "green" : "red"}; border: 1px solid;padding: 10px;margin-bottom: 10px;"><b>Your Answer:</b> ${selectedAnswer}</li>`;
                                })
                                .join("")}
                          </ul>
                           <p>Points: ${feedback.points}</p>
                          <p>Result: ${
                            feedback.is_correct
                              ? '<span style="color: green;">Correct</span>'
                              : '<span style="color: red;">Incorrect</span>'
                          }</p>
                      </ >
              `;
                jQuery(".as-quiz-feedback-results-wrapper").append(
                  questionFeedback
                );
              });
            } else {
              alert("Error: " + response.data.message);
            }
          },
          error: function () {
            jQuery("#as-overlay").hide();
            alert("Something went wrong, please try again.");
          },
        });
      });
    }
  });

  jQuery('input[type="radio"]').change(function () {
    jQuery('input[name="' + jQuery(this).attr("name") + '"]')
      .parent()
      .parent()
      .removeClass("active");
    if (jQuery(this).is(":checked")) {
      jQuery(this).parent().parent().addClass("active");
    }
  });

  jQuery('input[type="checkbox"]').change(function () {
    if (jQuery(this).is(":checked")) {
      jQuery(this).parent().parent().addClass("active");
    } else {
      jQuery(this).parent().parent().removeClass("active");
    }
  });
});

// section quiz completed manage
jQuery(document).ready(function () {
  var courseId = jQuery(".as-course-id").val();
  var chapterId = jQuery(".as-chapter-id").val();
  var lessonId = jQuery(".as-lesson-id").val();
  var topicId = jQuery(".as-topic-id").val();
  var sectionId = jQuery(".as-section-id").val();
  var quizId = jQuery(".as_quiz_id").val();

  jQuery(".as-finish-quiz-button").on("click", function () {
    jQuery.ajax({
      url: as_quiz_ajax_progress_section.ajaxurl,
      type: "POST",
      data: {
        action: "as_quiz_ajax_progress_section",
        nonce: as_quiz_ajax_progress_section.nonce,
        course_id: courseId,
        chapter_id: chapterId,
        lesson_id: lessonId,
        topic_id: topicId,
        section_id: sectionId,
        quiz_id: quizId,
      },
      success: function (response) {
        console.log(response.data.success);
      },
      error: function (xhr, status, error) {
        console.log("AJAX call error:", status, error);
      },
    });
  });

  jQuery("#as-view-result").on("click", function () {
    jQuery(".as-quiz-feedback-results-wrapper").toggle();
  });
});

// topic quiz completed manage
jQuery(document).ready(function () {
  var courseId = jQuery(".as-topic-course-id").val();
  var chapterId = jQuery(".as-topic-chapter-id").val();
  var lessonId = jQuery(".as-topic-lesson-id").val();
  var topicId = jQuery(".as-topic-topic-id").val();
  var quizId = jQuery(".as_quiz_id").val();

  jQuery(".as-finish-quiz-button-topic").on("click", function () {
    jQuery.ajax({
      url: as_quiz_ajax_progress_topic.ajaxurl,
      type: "POST",
      data: {
        action: "as_quiz_ajax_progress_topic",
        nonce: as_quiz_ajax_progress_topic.nonce,
        course_id: courseId,
        chapter_id: chapterId,
        lesson_id: lessonId,
        topic_id: topicId,
        quiz_id: quizId,
      },
      success: function (response) {
        console.log(response.data.success);
      },
      error: function (xhr, status, error) {
        console.log("AJAX call error:", status, error);
      },
    });
  });

  jQuery("#as-view-result").on("click", function () {
    jQuery(".as-quiz-feedback-results-wrapper").toggle();
  });
});

// lesson quiz complete manage
jQuery(document).ready(function () {
  var courseId = jQuery(".as-lesson-course-id").val();
  var chapterId = jQuery(".as-lesson-chapter-id").val();
  var lessonId = jQuery(".as-lesson-lesson-id").val();
  var quizId = jQuery(".as_quiz_id").val();

  jQuery(".as-finish-quiz-button-lesson").on("click", function () {
    jQuery.ajax({
      url: as_quiz_ajax_progress_lesson.ajaxurl,
      type: "POST",
      data: {
        action: "as_quiz_ajax_progress_lesson",
        nonce: as_quiz_ajax_progress_lesson.nonce,
        course_id: courseId,
        chapter_id: chapterId,
        lesson_id: lessonId,
        quiz_id: quizId,
      },
      success: function (response) {
        console.log(response.data.success);
      },
      error: function (xhr, status, error) {
        console.log("AJAX call error:", status, error);
      },
    });
  });

  jQuery("#as-view-result").on("click", function () {
    jQuery(".as-quiz-feedback-results-wrapper").toggle();
  });
});
