    /**
     * Process the click on a choice and move to the next branch
     * @param {type} i
     * @returns {undefined}
     */

    function process_question(dt_id, i) {

        var dtree_name = "dt_tree_" + dt_id;

        // derive first ID if not explicitly passed
        if (typeof i == "undefined") {
            i = window[dtree_name].start_ID;
        }

        jQuery("#decision_tree_area_" + dt_id).children().detach();

        option = '';
        startover = '';

        if (window[dtree_name].data[i].type != 'answer') {
            jQuery("#decision_tree_area_" + dt_id).append("<div id='#the_question_" + dt_id + "' class='dt_display_question'>" + window[dtree_name].data[i].question + "</div>");

            if (window[dtree_name].data[i].subtext != undefined) {
                jQuery("#decision_tree_area_" + dt_id).append("<div id='#subtext_" + dt_id + "' class='dt_display_subtext'>" + window[dtree_name].data[i].subtext + "</div>");
            }
            for (j = 0; j < window[dtree_name].data[i].choices.length; j++) {
                option += '<div data-dtid="' + dt_id + '"  data-qid="' +
                    window[dtree_name].data[i].choices[j].next +
                    '" class=" dt_button dt_radio_choice" >';
                option += window[dtree_name].data[i].choices[j].choice;
                option += '</div><br>';
            }
            jQuery("#decision_tree_area_" + dt_id).append("<div id='dt_choice_set_" +
                dt_id + "'>" + option + "</div>");
        } else {

            // ******** this is the answer *********
            option += '<div id="radio_answer_' + dt_id + '" class="bg-success alert-success tree-answer">';
            option += window[dtree_name].data[i].question;
            option += '</div>';

            startover += '<div class="dt_button answer-restart" data-dtid="' + dt_id + '">';
            startover += '<i class="fa fa-repeat"> '
            startover += "Restart ";
            startover += "</i>"
            startover += '</div>';

            // answer
            jQuery("#decision_tree_area_" + dt_id).append("<div id='dt_choice_set_" + dt_id + "'>" + option + "</div>");

            // subtext
            if (window[dtree_name].data[i].subtext != undefined) {
                jQuery("#decision_tree_area_" + dt_id).append("<div id='#subtext_" + dt_id + "' class='dt_display_subtext'>" + window[dtree_name].data[i].subtext + "</div>");
            }

            // start over
            jQuery("#decision_tree_area_" + dt_id).append("<div id='dt_choice_set_" + dt_id + "'>" + startover + "</div>");

        } // ********** end answer *********
    }

    jQuery(document).ready(function($) {
        jQuery(document).on("click", ".dt_radio_choice", function() {
            process_question($(this).data('dtid'), $(this).data('qid'));
        });
        jQuery(document).on("click", ".answer-restart", function() {
            var dtree_name = "dt_tree_" + $(this).data('dtid');
            process_question($(this).data('dtid'), window[dtree_name].start_ID);
        });
    });
