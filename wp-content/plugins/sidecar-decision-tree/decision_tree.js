/**
 
 
            __            __       __                  __                         __                     __    __                         __         
        .--|  .-----.----|__.-----|__.-----.-----.    |  |_.----.-----.-----.    |  |--.--.--.    .-----|__.--|  .-----.----.---.-.----. |  |_.--.--.
        |  _  |  -__|  __|  |__ --|  |  _  |     |    |   _|   _|  -__|  -__|    |  _  |  |  |    |__ --|  |  _  |  -__|  __|  _  |   ___|   _|  |  |
        |_____|_____|____|__|_____|__|_____|__|__|    |____|__| |_____|_____|    |_____|___  |    |_____|__|_____|_____|____|___._|__||__|____||___/ 
                                                                                       |_____|                                                       
                    ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||                         
                    ||||This is decision tree! A plugin for WordPress.  Find out more at http://sidecar.tv/decision_tree||||                         
                    ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||                          
    
**/
    
    /**
     * Process the click on a choice and move to the next branch
     * @param {type} i
     * @returns {undefined}
     */
    function process_question(dt_id, i){

        var dtree_name = "dt_tree_" + dt_id;
        
        // derive first ID if not explicitly passed
        if (typeof i == "undefined" ){
            i = window[dtree_name].start_ID;
        }
   
        jQuery( "#decision_tree_area_" + dt_id ).children().detach();
        option = '';
        if(window[dtree_name].data[i].subtext != undefined){
            jQuery( "#decision_tree_area_" + dt_id ).append("<div id='#title_" 
                + dt_id + "' class='dt_display_title'>" + window[dtree_name].title + "</div>");
        }
    
        option = '';
        if(window[dtree_name].data[i].type!='answer'){
            jQuery( "#decision_tree_area_" + dt_id ).append("<div id='#the_question_" 
                + dt_id + "' class='dt_display_question'>" + window[dtree_name].data[i].question + "</div>");
            
            if(window[dtree_name].data[i].subtext != undefined){
                jQuery( "#decision_tree_area_" + dt_id ).append("<div id='#subtext_" 
                    + dt_id + "' class='dt_display_subtext'>" + window[dtree_name].data[i].subtext+ "</div>");
            }
            for (j=0; j<window[dtree_name].data[i].choices.length; j++){ 
                option +='<div data-dtid="' + dt_id + '"  data-qid="' + 
                    window[dtree_name].data[i].choices[j].next + 
                    '" class=" dt_button dt_radio_choice" >';
                    option +=window[dtree_name].data[i].choices[j].choice;                
                option +='</div><br>';
            }
            jQuery("#decision_tree_area_" + dt_id ).append("<div id='dt_choice_set_" + 
                    dt_id + "'>" + option + "</div>");
        }
        else{
            //this is an answer
            option +='<div id="radio_answer_' + dt_id +'" class="dt_radio_answer dt_button">';
            option += window[dtree_name].data[i].question;
            option += '</div><br>';
            
            option += '<div class="dt_button answer-restart" data-dtid="' + dt_id + '">';
            option += '<i class="fa fa-repeat"> '
            option += "Restart ";
            option += "</i>"
            option += '</div><br>';
            
            jQuery("#decision_tree_area_" + dt_id ).append("<div id='#the_question_" + 
                dt_id + "'>Your Answer:</div>");
            if(window[dtree_name].data[i].subtext != undefined){
                jQuery( "#decision_tree_area_" + dt_id ).append("<div id='#subtext_" 
                    + dt_id + "' class='dt_display_subtext'>" + window[dtree_name].data[i].subtext+ "</div>");
            }
            jQuery("#decision_tree_area_" + dt_id).append("<div id='dt_choice_set_" + 
                dt_id + "'>" + option + "</div>");
            jQuery("#dt_choice_set_" + dt_id +" label").addClass("answer-success");
        }
        
        if(window[dtree_name].donated != "1"){
            jQuery( "#decision_tree_area_" + 
                dt_id ).append("<div class='decision_tree_area_donated'><a \
                href='http://sidecar.tv/decision_tree'>Powered by Decision Tree</a></div>");
        }
    }
    
// TODO  $(this).data() seems different than window[dtree_name].data above and is confusing. 
jQuery(document).ready(function($) {
    jQuery( document ).on("click", ".dt_radio_choice", function() {
        process_question($(this).data('dtid'), $(this).data('qid'));
    });    
    jQuery( document ).on("click", ".answer-restart", function() {
        var dtree_name = "dt_tree_" + $(this).data('dtid');
        process_question($(this).data('dtid'), window[dtree_name].start_ID);
    });  
});
  
