// datatable jQuery enrollment tabel
jQuery(document).ready(function () {
    jQuery('#as-enrollment-table').DataTable({
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        ajax: {
            url: as_enrollment_course_datatable.ajaxurl + "?action=as_enrollment_course_datatable",
        },
        "pageLength": 10,
        "columns": [
            { data: 'id' },
            { data: 'user_id' },
            { data: 'user_name' },
            { data: 'course_id' },
            { data: 'course_name' },
            { data: 'enrollment_time' }
        ]
    }
    );
});

// datatable jQuery trasition tabel
jQuery(document).ready(function () {
    jQuery('#as-transaction-table').DataTable({
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        ajax: {
            url: as_course_transaction_datatable.ajaxurl + "?action=as_course_transaction_datatable",
        },
        "pageLength": 10,
        "columns": [
            { data: 'id' },
            { data: 'user_id' },
            { data: 'user_name' },
            { data: 'course_id' },
            { data: 'course_name' },
            { data: 'course_price' },
            { data: 'transaction_status' },
            { data: 'enrollment_time' }
        ]
    });
});

// fetch chapter remove
jQuery(document).on('click', '.as-remove-fetch-chapter', function (e) {
    e.preventDefault();
    let chapterId = jQuery(this).data('chapter-id');
    jQuery(`.as-chapter-accordion:has(.as-hidden-chepter-id[value="${chapterId}"])`).remove();
    let chapterAccordionLength = jQuery('.as-chapter-accordion').length;
    if (chapterAccordionLength == 0) {
        jQuery('.as-accordion-item-container').append(
            `<div class="as-builder-empty">
                    <p>Course has no content yet.</p>
                    <p>Add a new Chapter or add an existing one from the sidebar</p>
            </div>`
        )
    }
});

// fetch lesson remove
jQuery(document).on('click', '.as-remove-fetch-lesson', function (e) {
    e.preventDefault();
    let lessonId = jQuery(this).data('lesson-id');
    jQuery(`.as-chapter-accordion`).find(`input[value="${lessonId}"]`).closest('.as-accordion-item').remove();
});


// fetch topic remove
jQuery(document).on('click', '.as-remove-fetch-topic', function (e) {
    e.preventDefault();
    let topicId = jQuery(this).data('topic-id');
    let lessonId = jQuery(this).data('lesson-id');
    let chapterId = jQuery(this).data('chapter-id');
    jQuery(`.as-accordion-chapter-${chapterId}`).find(`.as-accordion-lesson-${lessonId}`).find(`input[value="${topicId}"]`).closest('.as-topic-accordion').remove();
    let topicAccordionLength = jQuery(`.as-accordion-chapter-${chapterId}`).find(`.as-accordion-lesson-${lessonId} `).find('.as-topic-accordion').length;
    if (topicAccordionLength == 0) {
        jQuery(`.as-accordion-chapter-${chapterId}`).find(`.as-accordion-lesson-${lessonId} `).find('.as-topic-inputfield-link').remove();
    }
});

// fetch section remove
jQuery(document).on('click', '.as-remove-fetch-section', function (e) {
    e.preventDefault();
    let topicId = jQuery(this).data('topic-id');
    let lessonId = jQuery(this).data('lesson-id');
    let chapterId = jQuery(this).data('chapter-id');
    let sectionId = jQuery(this).data('section-id');
    jQuery(`.as-accordion-chapter-${chapterId}`).find(`.as-accordion-lesson-${lessonId}`).find(`.as-accordion-topic-${topicId}`).find(`input[value="${sectionId}"]`).closest('.as-section-accordion').remove();
    let sectionAccordionLength = jQuery(`.as-accordion-chapter-${chapterId} `).find(`.as-accordion-lesson-${lessonId} `).find(`.as-accordion-topic-${topicId} `).find('.as-section-accordion').length;
    if (sectionAccordionLength == 0) {
        jQuery(`.as-accordion-chapter-${chapterId} `).find(`.as-accordion-lesson-${lessonId} `).find(`.as-accordion-topic-${topicId} `).find('.section-inputfield-link').remove();
    }
});


// create chapter in chepter post 
// creating new chapter
jQuery(document).on('click', '.as-chapter-inputfield-link', function (e) {
    e.preventDefault();
    jQuery('.as-chapter-input-field').show();
    jQuery('.as-chapter-inputfield-link').hide();
});

//cancel-chepter
jQuery(document).on('click', '.as-cancel-chapter', function (e) {
    e.preventDefault();
    jQuery(this).closest('.as-chapter-input-field').hide();
    jQuery('.as-chapter-inputfield-link').show();
});

// add chapter post using ajax
jQuery(document).on('click', '.as-add-chapter', function (e) {
    e.preventDefault();
    let chapterTitle = jQuery(this).siblings('.as-chapter-input-box').val();

    if (chapterTitle.trim() === '') {
        alert('Please enter a chapter title.');
        return;
    }

    jQuery.ajax({
        url: as_create_chapter_post.ajaxurl,
        type: 'POST',
        data: {
            action: 'as_create_chapter_post',
            name: chapterTitle,
        },
        success: function (response) {
            if (response.success) {
                jQuery('.as-builder-empty').hide();
                jQuery('.as-chapter-input-field').hide();
                jQuery('.as-accordion-item-container').append(
                    `<div class="as-chapter-accordion as-accordion-chapter-${response.data.chapter_id}">
                            <div class="as-accordion-item">
                                    <input type="hidden" name="chapter_id[]" class="as-hidden-chepter-id" value="${response.data.chapter_id}" />
                                    <b>${response.data.chapter_name}</b>
                                    <a class="as-remove-chapter" data-chapter-id="${response.data.chapter_id}">Remove</a>
                            </div>
                            <!-- quiz appending -->
                            <div class="as-quiz-accordion-container-${response.data.chapter_id}">
                            </div>
                             <div class="as-lesson-accordion-container">
                            </div>
                            <div class="as-lesson-container">
                                    <div class="as-lesson-input-field as-chapter-input-id-${response.data.chapter_id}" >
                                        <input class="as-lesson-input-box" type="text" placeholder="Lesson Title">
                                        <a class="as-cancel-lesson">Cancel</a>
                                        <a class="as-add-lesson" data-chapter-id="${response.data.chapter_id}">Add Lesson</a>
                                    </div>
                                    <a class="as-icon as-lesson-inputfield-link" data-chapter-id="${response.data.chapter_id}">New Lesson</a>
                            </div>
                            <!-- quiz add link -->
                            <div class="as-quiz-selection as-quiz-chapter-input-id-${response.data.chapter_id}">
                                <select class="as-quiz-selection-search-input form-control" data-placeholder="Select a Quiz......." style="width:90%;" data-quiz-chapter-id="${response.data.chapter_id}">
                                </select>
                                <a class="as-cancel-chapter-quiz" data-quiz-chapter-id="${response.data.chapter_id}">Cancel</a>
                            </div>
                            <a class="as-icon as-chapter-quiz-inputfield-link" data-quiz-chapter-id="${response.data.chapter_id}">Add Quiz Chapter</a>
                    </div>`
                );
                jQuery('.as-chapter-input-box').val('');
                jQuery('.as-chapter-inputfield-link').show();

                // Reinitialize Select 2
                asInitialize();
            }
            else {
                alert(response.data);
            }

        }
    });
});

// Romove chepter on database
jQuery(document).on('click', '.as-remove-chapter', function (e) {
    e.preventDefault();
    let chapterId = jQuery(this).data('chapter-id');

    jQuery.ajax({
        url: as_romove_chapter_post.ajaxurl,
        type: 'POST',
        data: {
            action: 'as_romove_chapter_post',
            chapter: chapterId,
        },
        success: function (response) {
            if (response.success) {
                jQuery('.as-accordion-item-container').find(`.as-accordion-chapter-${chapterId}`).remove();
                let chapterAccordionLength = jQuery('.as-chapter-accordion').length;
                if (chapterAccordionLength == 0) {
                    jQuery('.as-builder-empty').show();
                }
            }
        }
    });
});

// create lesson in chepter post
// creating new lesson
jQuery(document).on('click', '.as-lesson-inputfield-link', function (e) {
    e.preventDefault();
    let chapterId = jQuery(this).data('chapter-id');
    let lessonContainer = jQuery(this).closest('.as-lesson-container');
    lessonContainer.find(`.as-chapter-input-id-${chapterId}`).show();
    jQuery(this).hide();
});

// cancel lesson button
jQuery(document).on('click', '.as-cancel-lesson', function (e) {
    e.preventDefault();
    let lessonContainer = jQuery(this).closest('.as-lesson-container');
    lessonContainer.find('.as-lesson-input-field').hide();
    lessonContainer.find('.as-lesson-inputfield-link').show();
});

// add lesson post using ajax
jQuery(document).on('click', '.as-add-lesson', function (e) {
    e.preventDefault();
    let lessonTitle = jQuery(this).siblings('.as-lesson-input-box').val();
    let chapterId = jQuery(this).data('chapter-id');

    if (lessonTitle.trim() === '') {
        alert('Please enter a Lesson title.');
        return;
    }

    jQuery.ajax({
        url: as_create_lesson_post.ajaxurl,
        type: 'POST',
        data: {
            action: 'as_create_lesson_post',
            name: lessonTitle,
        },
        success: function (response) {
            if (response.success) {
                jQuery('.as-lesson-input-field').hide();
                let Chapter = jQuery(`.as-accordion-chapter-${chapterId}`);
                Chapter.find('.as-lesson-accordion-container').append(
                    `<div class="as-lesson-accordion">
                            <div class="as-accordion-item">
                                <input type="hidden" name="lesson_id[${chapterId}][]"  value="${response.data.lesson_id}" />
                                <b>${response.data.lesson_name}</b>
                                <a class="as-remove-lesson" data-chapter-id="${chapterId}" data-lesson-id="${response.data.lesson_id}">Remove</a>
                            </div> 
                            <div class="as-quiz-accordion-container-lesson-${response.data.lesson_id}">
                            </div>
                            <div class="as-topic-accordion-container">
                            </div>
                            <div class="as-topic-container">
                                    <div class="as-topic-input-field as-lesson-input-id-${response.data.lesson_id}">
                                        <input class="as-topic-input-box" type="text" placeholder="Topic Title">
                                        <a class="as-cancel-topic">Cancel</a>
                                        <a class="as-add-topic" data-chapter-id="${chapterId}" data-lesson-id="${response.data.lesson_id}">Add Topic</a>
                                    </div>
                                    <a class="as-icon as-topic-inputfield-link" data-lesson-id="${response.data.lesson_id}">New Topic</a>
                            </div>
                        </div>

                        <!-- quiz add link for lesson-->
                        <div class="as-quiz-lesson-selection as-quiz-lesson-input-id-${response.data.lesson_id}">
                            <select class="as-quiz-selection-search-input-lesson form-control" data-placeholder="Select a Quiz......." style="width:90%;" data-quiz-lesson-id="${response.data.lesson_id}">
                            </select>
                            <a class="as-cancel-lesson-quiz" data-quiz-lesson-id="${response.data.lesson_id}">Cancel</a>
                        </div>
                        <a class="as-icon as-lesson-quiz-inputfield-link" data-quiz-lesson-id="${response.data.lesson_id}">Add Quiz Lesson</a>`
                );
                jQuery('.as-lesson-input-box').val('');
                jQuery('.as-lesson-inputfield-link').show();
                asInitializeLesson();

                let lessonDiv = jQuery('.as-lesson-accordion-container .as-lesson-accordion').last();
                lessonDiv.addClass(`as-accordion-lesson-${response.data.lesson_id}`);
            } else {
                alert(response.data);
            }
        }
    });
});

// Romove lesson on database
jQuery(document).on('click', '.as-remove-lesson', function (e) {
    e.preventDefault();
    let lessonId = jQuery(this).data('lesson-id');
    let chapterId = jQuery(this).data('chapter-id');

    jQuery.ajax({
        url: as_romove_lesson_post.ajaxurl,
        type: 'POST',
        data: {
            action: 'as_romove_lesson_post',
            lesson: lessonId,
        },
        success: function (response) {
            if (response.success) {
                jQuery(`.as-accordion-chapter-${chapterId}`).find(`input[value="${lessonId}"]`).closest('.as-accordion-item').remove();
            }
        }
    });
});

// create topic in lesson in chepter post

// creating new topic
jQuery(document).on('click', '.as-topic-inputfield-link', function (e) {
    e.preventDefault();
    let lessonId = jQuery(this).data('lesson-id');
    let topicContainer = jQuery(this).closest('.as-topic-container');
    topicContainer.find(`.as-lesson-input-id-${lessonId}`).show();
    jQuery(this).hide();
});

// cancel lesson button
jQuery(document).on('click', '.as-cancel-topic', function (e) {
    e.preventDefault();
    let topicContainer = jQuery(this).closest('.as-topic-container');
    topicContainer.find('.as-topic-input-field').hide();
    topicContainer.find('.as-topic-inputfield-link').show();
    topicContainer.find('.as-topic-input-box').val('');
});

// add topic using ajax
jQuery(document).on('click', '.as-add-topic', function (e) {
    e.preventDefault();
    let topicTitle = jQuery(this).siblings('.as-topic-input-box').val();
    let chapterId = jQuery(this).data('chapter-id');
    let lessonId = jQuery(this).data('lesson-id');

    if (topicTitle.trim() === '') {
        alert('Please enter a Topic Name.');
        return;
    }

    jQuery.ajax({
        url: as_create_topic_post.ajaxurl,
        type: 'POST',
        data: {
            action: 'as_create_topic_post',
            name: topicTitle,
        },
        success: function (response) {
            if (response.success) {
                jQuery('.as-topic-input-field').hide();
                let chapter = jQuery(`.as-accordion-chapter-${chapterId}`);
                let lesson = chapter.find(`.as-accordion-lesson-${lessonId}`);
                lesson.find('.as-topic-accordion-container').append(
                    `<div class="as-topic-accordion">
                        <div class="as-accordion-item">
                                <input type="hidden" name="topic_id[${chapterId}][${lessonId}][]"  value="${response.data.topic_id}" />
                                <b>${response.data.topic_name}</b>
                                <a class="as-remove-topic" data-chapter-id="${chapterId}" data-lesson-id="${lessonId}" data-topic-id="${response.data.topic_id}">Remove</a>
                        </div> 
                        <div class="as-section-accordion-container">
                        </div>
                        <div class="as-section-container">
                                <div class="as-section-input-field as-topic-input-id-${response.data.topic_id}">
                                    <input class="as-section-input-box" type="text" placeholder="Section Title">
                                    <a class="as-cancel-section">Cancel</a>
                                    <a class="as-add-section" data-chapter-id="${chapterId}" data-lesson-id="${lessonId}" data-topic-id="${response.data.topic_id}">Add Section</a>
                                </div>
                                <a class="as-icon as-section-inputfield-link" data-topic-id="${response.data.topic_id}">New Section</a>
                        </div>
                    </div>`
                );
                jQuery('.as-topic-input-box').val('');
                jQuery('.as-topic-inputfield-link').show();
                let topicDiv = jQuery('.as-topic-accordion-container .as-topic-accordion').last();
                topicDiv.addClass(`as-accordion-topic-${response.data.topic_id}`);
            } else {
                alert(response.data);
            }
        }
    });
});

// Romove topic on database
jQuery(document).on('click', '.as-remove-topic', function (e) {
    e.preventDefault();
    let lessonId = jQuery(this).data('lesson-id');
    let chapterId = jQuery(this).data('chapter-id');
    let topicId = jQuery(this).data('topic-id');

    jQuery.ajax({
        url: as_romove_topic_post.ajaxurl,
        type: 'POST',
        data: {
            action: 'as_romove_topic_post',
            topic: topicId,
        },
        success: function (response) {
            if (response.success) {
                jQuery(`.as-accordion-chapter-${chapterId} `).find(`.as-accordion-lesson-${lessonId} `).find(`input[value = "${topicId}"]`).closest('.as-topic-accordion').remove();
                let topicAccordionLength = jQuery(`.as-accordion-chapter-${chapterId}`).find(`.as-accordion-lesson-${lessonId} `).find('.as-topic-accordion').length;
                if (topicAccordionLength == 0) {
                    jQuery(`.as-accordion-chapter-${chapterId}`).find(`.as-accordion-lesson-${lessonId} `).find('.as-topic-inputfield-link').remove();
                }
            }
        }
    });
});


// create section in topic in lesson in chepter post

// creating new section
jQuery(document).on('click', '.as-section-inputfield-link', function (e) {
    e.preventDefault();
    let topicId = jQuery(this).data('topic-id');
    let sectionContainer = jQuery(this).closest('.as-section-container');
    sectionContainer.find(`.as-topic-input-id-${topicId}`).show();
    jQuery(this).hide();
});

// cancel lesson button
jQuery(document).on('click', '.as-cancel-section', function (e) {
    e.preventDefault();
    let sectionContainer = jQuery(this).closest('.as-section-container');
    sectionContainer.find('.as-section-input-field').hide();
    sectionContainer.find('.as-section-inputfield-link').show();
    sectionContainer.find('.as-section-input-box').val('');
});

// add topic using ajax
jQuery(document).on('click', '.as-add-section', function (e) {
    e.preventDefault();
    let sectionTitle = jQuery(this).siblings('.as-section-input-box').val();
    let chapterId = jQuery(this).data('chapter-id');
    let lessonId = jQuery(this).data('lesson-id');
    let topicId = jQuery(this).data('topic-id');

    if (sectionTitle.trim() === '') {
        alert('Please enter a Section Name.');
        return;
    }

    jQuery.ajax({
        url: as_create_section_post.ajaxurl,
        type: 'POST',
        data: {
            action: 'as_create_section_post',
            name: sectionTitle,
        },
        success: function (response) {
            if (response.success) {
                jQuery('.as-section-input-field').hide();
                let chapter = jQuery(`.as-accordion-chapter-${chapterId}`);
                let lesson = chapter.find(`.as-accordion-lesson-${lessonId}`);
                let topic = lesson.find(`.as-accordion-topic-${topicId}`);
                topic.find('.as-section-accordion-container').append(
                    `<div class="as-section-accordion">
                        <div class="as-accordion-item">
                                <input type="hidden" name="section_id[${chapterId}][${lessonId}][${topicId}][]"  value="${response.data.section_id}" />
                                <b>${response.data.section_name}</b>
                                <a class="as-remove-section" data-chapter-id="${chapterId}" data-lesson-id="${lessonId}" data-topic-id="${topicId}" data-section-id="${response.data.section_id}">Remove</a>
                        </div>`
                );
                jQuery('.as-section-input-box').val('');
                jQuery('.as-section-inputfield-link').show();
            } else {
                alert(response.data);
            }
        }
    });
});

// Romove topic on database
jQuery(document).on('click', '.as-remove-section', function (e) {
    e.preventDefault();
    let lessonId = jQuery(this).data('lesson-id');
    let chapterId = jQuery(this).data('chapter-id');
    let topicId = jQuery(this).data('topic-id');
    let sectionId = jQuery(this).data("section-id")

    jQuery.ajax({
        url: as_romove_section_post.ajaxurl,
        type: 'POST',
        data: {
            action: 'as_romove_section_post',
            section: sectionId,
        },
        success: function (response) {
            if (response.success) {
                jQuery(`.as-accordion-chapter-${chapterId} `).find(`.as-accordion-lesson-${lessonId} `).find(`.as-accordion-topic-${topicId} `).find(`input[value = "${sectionId}"]`).closest('.as-section-accordion').remove();
                let sectionAccordionLength = jQuery(`.as-accordion-chapter-${chapterId} `).find(`.as-accordion-lesson-${lessonId} `).find(`.as-accordion-topic-${topicId} `).find('.as-section-accordion').length;
                if (sectionAccordionLength == 0) {
                    jQuery(`.as-accordion-chapter-${chapterId} `).find(`.as-accordion-lesson-${lessonId} `).find(`.as-accordion-topic-${topicId} `).find('.as-section-inputfield-link').remove();
                }
            }
        }
    });
});


// sortable using user enrollment or not
jQuery(function () {
    jQuery(".sortable-enabled[id^=as-sortable-chapter], .sortable-enabled[id^=as-sortable-lesson], .sortable-enabled[id^=as-sortable-topic], .sortable-enabled[id^=as-sortable-section]").sortable({
        cursor: "move"
    });
    jQuery(".sortable-disabled[id^=as-sortable-chapter], .sortable-disabled[id^=as-sortable-lesson], .sortable-disabled[id^=as-sortable-topic], .sortable-disabled[id^=as-sortable-section]").sortable("disable");
});

// Repeater field for quiz question answer

jQuery(document).ready(function () {

    // Toggle between single and multiple choice
    jQuery('input[name="question_type"]').on('change', function () {
        toggleChoiceType(jQuery(this).val());
    });


    // Remove answer button click handler
    jQuery(document).on("click", ".as-remove-button", function () {
        jQuery(this).parent(".as-answer-form-item").remove();

        // Reindex the remaining answers
        jQuery(".as-answer-list .as-answer-form:not(.as-dummy-answer-form-item)").each(function (index) {
            jQuery(this).find(".as-question-answer").attr("name", "as_question[" + index + "]");
            jQuery(this).find(".as-choice-option").attr("value", index);
            jQuery(this).find("label").attr("for", "answer-" + index);
            jQuery(this).find(".as-choice-option").attr("id", "answer-" + index);
        });
    });

    // Add answer button click handler
    jQuery(document).on("click", "#as-add-button", function () {
        let ele = jQuery(".as-dummy-answer-form-item").clone();
        ele.removeClass("as-dummy-answer-form-item").removeClass("as-d-none");

        // Count the current total number of answers
        let totalCount = jQuery('.as-answer-list .as-answer-form').length;
        let questionType = jQuery('input[name="question_type"]:checked').val();

        if (questionType === 'single') {
            ele.find(".as-choice-option").attr("value", totalCount)
                .attr("id", "answer-" + totalCount)
                .attr("type", "radio")
                .attr("name", "single_answer")
                .removeClass('as-multiple-choice')
                .addClass('as-single-choice');
        }
        else if (questionType === 'multiple') {
            ele.find(".as-choice-option").attr("value", totalCount)
                .attr("id", "answer-" + totalCount)
                .attr("type", "checkbox")
                .attr("name", "multiple_answer[]")
                .removeClass('as-single-choice')
                .addClass('as-multiple-choice');
        }

        // Set the new input fields' name and value
        ele.find(".as-question-answer").attr("name", "as_question[" + totalCount + "]").attr('required', true);
        ele.find('#answer-repeater-label').attr('for', "answer-" + totalCount);

        jQuery(".as-answer-list").append(ele);
    });


    // Function to toggle between radio and checkbox based on selected type
    function toggleChoiceType(type) {
        jQuery('.as-choice-option').each(function () {
            if (type === 'single') {
                jQuery(this).attr('type', 'radio').attr("name", "single_answer").removeClass('as-multiple-choice').addClass('as-single-choice');
            } else if (type === 'multiple') {
                jQuery(this).attr('type', 'checkbox').attr("name", "multiple_answer[]").removeClass('as-single-choice').addClass('as-multiple-choice');
            }
        });
    }

    jQuery("#sortable-answer-list").sortable({
        cursor: "move"
    });

});


// Question Bank Use to Create Quiz Using Search Ajax
jQuery(document).ready(function () {
    var selectedQuestions = [];
    jQuery('.as-quiz-question-search-input').select2({
        ajax: {
            url: as_quiz_question_search.ajaxurl,
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    action: 'as_quiz_question_search',
                    search_query: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: jQuery.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.title
                        };
                    })
                };
            }
        },
        minimumInputLength: 3
    });

    // On selecting a question
    jQuery('.as-quiz-question-search-input').on('select2:select', function (e) {
        const selectedId = e.params.data.id;
        const selectedTitle = e.params.data.text;
        const totalCount = jQuery('#as-selected-questions-list li').length;

        const appendValue = jQuery('.as_append_question').val();
        const appendValueArray = appendValue ? appendValue.split(',') : [];

        const isQuestionAlreadyAppended = false;

        appendValueArray.forEach(function (value) {
            if (Number(value) === Number(selectedId)) {
                isQuestionAlreadyAppended = true;
            }
        });

        if (isQuestionAlreadyAppended || selectedQuestions.includes(selectedId)) {
            alert("This question has already been selected.");
        } else {
            selectedQuestions.push(selectedId);
            jQuery('#as-selected-questions-list').append(
                '<li data-id="' + selectedId + '" ><div class="as-question-wrapper">' + selectedTitle +
                '<a href="#" class="remove-question">Remove</a>' +
                '<input type="hidden" name="as_selected_questions[' + totalCount + ']" value="' + selectedId + '" />' +
                '</div><div class="as_question_point_list"><input type="number" id="as_point" placeholder="point" name="as_point_question[' + totalCount + ']" min="1"></div></li>'
            );
        }
    });


    // Remove a question from the list
    jQuery(document).on('click', '.remove-question', function (e) {
        e.preventDefault();

        jQuery(this).closest('li').remove();

        jQuery('#as-selected-questions-list li').each(function (index) {
            jQuery(this).find('.as-question-wrapper input[type="hidden"]').attr('name', 'as_selected_questions[' + index + ']');
            jQuery(this).find('.as_question_point_list input[type="number"]').attr('name', 'as_point_question[' + index + ']');
        });
    });

    // sortable
    jQuery('#as-selected-questions-list').sortable({
        cursor: "move",
        update: function (event, ui) {
            jQuery('#as-selected-questions-list li').each(function (index) {
                jQuery(this).find('.as-question-wrapper input[type="hidden"]').attr('name', 'as_selected_questions[' + index + ']');
                jQuery(this).find('.as_question_point_list input[type="number"]').attr('name', 'as_point_question[' + index + ']');
            });
        }
    });

});

// quiz selection in course logic - add quiz for chapter
jQuery(document).on('click', '.as-chapter-quiz-inputfield-link', function (e) {
    e.preventDefault();
    let chapterId = jQuery(this).data('quiz-chapter-id');
    jQuery(`.as-quiz-chapter-input-id-${chapterId}`).show();
    jQuery(this).hide();
});

// Cancel Quiz Search 
jQuery(document).on('click', '.as-cancel-chapter-quiz', function (e) {
    e.preventDefault();
    let chapterId = jQuery(this).data('quiz-chapter-id');
    jQuery(this).closest(`.as-quiz-chapter-input-id-${chapterId}`).hide();
    jQuery('.as-chapter-quiz-inputfield-link').show();
});

// quiz selection function
function asInitializeQuizSelection() {
    jQuery('.as-quiz-selection-search-input').select2({
        ajax: {
            url: as_quiz_selection_in_course.ajaxurl,
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    action: 'as_quiz_selection_in_course',
                    search_query: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: jQuery.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.title
                        };
                    })
                };
            }
        },
        minimumInputLength: 3
    });
}

// Append the selected quiz to the chapter
function asAppendSelectedQuiz(selectedQuiz, selectedQuizId, chapterId) {

    var quizItem = `
        <div class="as-chapter-quiz-accordion">
            <b>Quiz: ${selectedQuiz}</b>
            <a class="as-remove-chapter-quiz" data-quiz-chapter-id="${chapterId}">Remove</a>
            <input type="hidden" name="quiz_id[${chapterId}][]" class="as-hidden-chapter-quiz-id" value="${selectedQuizId}" />
        </div>`;

    jQuery(`.as-quiz-accordion-container-${chapterId}`).append(quizItem);
}

// Handle quiz selection event
function asHandleQuizSelection() {
    jQuery('.as-quiz-selection-search-input').on('select2:select', function (e) {
        var selectedQuiz = e.params.data.text;
        var selectedQuizId = e.params.data.id;
        var chapterId = jQuery(this).data('quiz-chapter-id');

        asAppendSelectedQuiz(selectedQuiz, selectedQuizId, chapterId);
    });
}

// Handle quiz removal
function asHandleQuizRemoval() {
    jQuery(document).on('click', '.as-remove-chapter-quiz', function () {
        jQuery(this).closest('.as-chapter-quiz-accordion').remove();
    });
}

// Initialize the functionalities
function asInitialize() {
    asInitializeQuizSelection();
    asHandleQuizSelection();
    asHandleQuizRemoval();
}

// get quiz data using ajax
jQuery(document).ready(function () {
    // Run the initialization
    asInitialize();
});

// quiz selection in course logic - add quiz for lesson
jQuery(document).on('click', '.as-lesson-quiz-inputfield-link', function (e) {
    e.preventDefault();
    let lessonId = jQuery(this).data('quiz-lesson-id');
    jQuery(`.as-quiz-lesson-input-id-${lessonId}`).show();
    jQuery(this).hide();
});

// Cancel Quiz Search 
jQuery(document).on('click', '.as-cancel-lesson-quiz', function (e) {
    e.preventDefault();
    let lessonId = jQuery(this).data('quiz-lesson-id');
    jQuery(this).closest(`.as-quiz-lesson-input-id-${lessonId}`).hide();
    jQuery('.as-lesson-quiz-inputfield-link').show();
});

// quiz selection function
function asInitializeQuizSelectionLesson() {
    jQuery('.as-quiz-selection-search-input-lesson').select2({
        ajax: {
            url: as_quiz_selection_in_course.ajaxurl,
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    action: 'as_quiz_selection_in_course',
                    search_query: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: jQuery.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.title
                        };
                    })
                };
            }
        },
        minimumInputLength: 3
    });
}

// Append the selected quiz to the lesson
function asAppendSelectedQuizLesson(selectedQuiz, selectedQuizId, lessonId, chapterId) {
    console.log("This is comeing here");
    var quizItem = `
        <div class="as-lesson-quiz-accordion">
            <b>Quiz: ${selectedQuiz}</b>
            <a class="as-remove-lesson-quiz" data-quiz-lesson-id="${lessonId}">Remove</a>
            <input type="hidden" name="quiz_id[${lessonId}][]" class="as-hidden-lesson-quiz-id" value="${selectedQuizId}" />
        </div>`;

    jQuery(`.as-quiz-accordion-container-lesson-${lessonId}`).append(quizItem);
}

// Handle quiz selection event
function asHandleQuizSelectionLesson() {
    jQuery('.as-quiz-selection-search-input-lesson').on('select2:select', function (e) {
        var selectedQuiz = e.params.data.text;
        var selectedQuizId = e.params.data.id;
        var chapterId = jQuery(this).data('quiz-chapter-id');
        var lessonId = jQuery(this).data("quiz-lesson-id");

        asAppendSelectedQuizLesson(selectedQuiz, selectedQuizId, lessonId, chapterId);
    });
}

// Handle quiz removal
function asHandleQuizRemovalLesson() {
    jQuery(document).on('click', '.as-remove-lesson-quiz', function () {
        jQuery(this).closest('.as-lesson-quiz-accordion').remove();
    });
}

// Initialize the functionalities
function asInitializeLesson() {
    asInitializeQuizSelectionLesson();
    asHandleQuizSelectionLesson();
    asHandleQuizRemovalLesson();
}

// get quiz data using ajax
jQuery(document).ready(function () {
    // Run the initialization
    asInitializeLesson();
});























