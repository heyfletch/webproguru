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

//Decision Tree Object
function DTree(donated){
    
    if (typeof donated == "undefined") {
        donated = "0";
    }
    this.max_id = 0;
    this.steps = Array();
    this.donated = donated;
}
var DTree_field_set;
var DTree_version = "1.1.0";

/**
 * Adds a step block to the admin UI
 */
DTree.prototype.add_step_field = function(step_id, number_of_choices){
    
    // set defaults
    if (typeof step_id == "undefined") {
        step_id = this.get_uniq_id();
    }
    if (typeof number_of_choices == "undefined" ){
        number_of_choices = 2;
    }
    
    // get HTML and add it to ID on DOM, update max steps and numbers
    jQuery("#dtree_entry_block").append(DTree_field_set.get_step_block(step_id, number_of_choices));
    this.max_step_id++;
    this.update_step_numbers_on_form();
    
    return step_id;
}

/**
 * Returns a unique id to use for various UI elements
 */
DTree.prototype.get_uniq_id = function(){
    this.max_id++;
    return (this.max_id);
}

/**
 * from update_next_step_select and call update_step_text elswhere
 * @param {type} step_id
 * @returns {undefined}
 */
DTree.prototype.add_step_text = function(step_object){
    if(step_object != null){
        id = step_object.target.dataset.id;
        val = step_object.target.value;
        //decision_tree_next_select
        this.steps[id] = val;
    }
    
}
/**
 * from update_next_step_select and call update_step_text elswhere
 * @param {type} step_id
 * @returns {undefined}
 */
DTree.prototype.remove_step_text = function(id_to_delete){
    if(id_to_delete != null){
        delete this.steps[id_to_delete];
    }
}
/**
 * Updates all next select menus, retaining the current selection, unless of course it is removed
 * TODO: this should only up date the drop down values in the the .decision_tree_next_select classes
 */
DTree.prototype.update_next_step_select = function(){
 
    //loop through each next selection for each step and record the selected item
    selected_values = Array();
    jQuery(".decision_tree_next_select").each(function(index, element){
        selected_values[element.id] = jQuery(element).val();
    });

    //delete the contents of all next select lists
    jQuery(".decision_tree_next_select").empty();
    
    //add a instructional item
    jQuery(".decision_tree_next_select").append(jQuery("<option/>", {
        value: '',
        text: "Select Next Step"
    }));
    //create the new select list
    for(id in this.steps){
        jQuery(".decision_tree_next_select").append(jQuery("<option/>", {
        value: id,
        text: this.steps[id]
        }));   
    }
    
    //re-select the selected item for each step
    jQuery(".decision_tree_next_select").each(function(index, element){
        jQuery(element).val(selected_values[element.id]);
        
    });
}

/**
 * add a new choice field to a step
 * @param {type} event_obj the step obj we are adding a choice to
 */
DTree.prototype.add_choice_field = function(step_number){
    choice_id = this.get_uniq_id();
    jQuery( "#decision_tree_step-" + step_number + 
            " .decision_tree_choice_area" ).length + 1;
    jQuery( "#decision_tree_choice_block_" + step_number).append(
                DTree_field_set.get_choice_block(step_number, choice_id)
            );
    this.update_step_numbers_on_form();
    return choice_id;
};

/**
 * Renumber all visible ste[ numbers 
 */
DTree.prototype.update_step_numbers_on_form = function(){
    step =1;
    jQuery(".decision_tree_step_box").each(function(index, element){
        step_elements = element.id.split("-");
        jQuery("#decision_tree_step_label-"+step_elements[1]).text('Step '+step);
        DTree_field_set.update_choice_numbers(step_elements[1]);
        step++;
    });
};

/**
 * Renumber visible choice numbers for a specific step
 */
DTree.prototype.update_choice_numbers = function(step_id){
    choice =1;
    jQuery("#decision_tree_choice_block_"+step_id+" .number_circle_small").each(function(index, element){
        jQuery("#"+element.id).text('Choice '+choice);
        choice++;
    });
};

/*
 * Remove a choice from a step, will update choice numbers as well
 */
DTree.prototype.remove_choice_field = function(event_obj){
    step_number = event_obj.target.dataset.step_number;
    jQuery("#decision_tree_choice_area-"+event_obj.target.dataset.id).remove();
    DTree_field_set.update_choice_numbers(step_number);
}

/*
 * Remove a specific step, will update step numbers as well
 */
DTree.prototype.remove_step_field = function(event_obj){
    step_number = event_obj.target.dataset.id;
    jQuery("#decision_tree_step-"+event_obj.target.dataset.id).remove();
    DTree_field_set.update_step_numbers_on_form();
    DTree_field_set.remove_step_text(step_number);
    DTree_field_set.update_next_step_select();
}

/*
 * Generate HTML for a step block
 * @param step_number the id of the new steo
 * @param choices how many choices to create for the step
 */
DTree.prototype.get_step_block = function(step_number, choices){
    var step='<div class="menu-item-settings decision_tree_step_box" id="decision_tree_step-' + step_number + '" >\
    <input type="hidden"  class="decision_tree_input decision_tree_raw_step" name="raw_step_number_' + step_number + '" value="' + step_number + '" >\
    <div data-id="' + step_number + '" class="decision_tree_remove_step">Delete</div>\
    <div class="decision_tree_row">\
        <div class="decision_tree_cell decision_tree_cell_label">\
            <span id="decision_tree_step_label-' + step_number + '" class="number_circle">Step ' + step_number + '</span>\
        </div>\
        <div class="decision_tree_cell decision_tree_cell_label">\
            <p class="description " >\
                <label for="' + step_number + '_type">\
                        Question/Answer<br>\
                    <select name="' + step_number + '_type" id="' + step_number + '_type" class="decision_tree_input">\
                    <option value="question">Question</option><option value="answer">Answer</option></select>\
                </label>\
            </p>\
        </div>\
        <div class="decision_tree_cell decision_tree_cell_label">\
            <p class="description description-wide">\
                    <label for="' + step_number + '_question_tex">Text<br \>\
                    <input type="text"  data-id="' + step_number + '" id="' + step_number + '_question_text" \
                    class="decision_tree_input decision_tree_step_text decision_tree_required\
                    widefat edit-menu-item-title"\
                    name="' + step_number + '_question_text" >\
                    </label>\
            </p>\
            <p class="description description-wide">\
                <label for="step_' + step_number + '_subtex">Subtext<br />\
                <input type="text" id="' + step_number + '_subtext" size="60" \
                class="decision_tree_input  widefat edit-menu-item-title"\
                name="' + step_number + '_subtext" >\
                </label>\
            </p>\
            </div>\
        </div>\
        <div id="decision_tree_choice_block_' + step_number + '">';
    
    for(i=1; i<=choices; i++){
        step+= DTree_field_set.get_choice_block(step_number, this.get_uniq_id());
    }
    step+='</div>\
        <div class="decision_tree_choice_add">\
            <div class="decision_tree_row">\
                <div class="decision_tree_cell">\
                    <input type="button" class="button decision_tree_add_choice" \n\
                        step_nuber="' + step_number + '" id="dtaddchoice_' + step_number + '" \n\
                        value="Add Choice">\
                </div>\
            </div>\
        </div>';
    step+='</div>';
    
    return step;
}

/**
 * Generate HTML for a choice and return it
 * @param {type} step_number the step id we are generating choices for
 * @param {type} choice_number the choice id to use for this choice
 * @returns {String}
 */
DTree.prototype.get_choice_block = function(step_number, choice_number){
    var choice_area ='<div id="decision_tree_choice_area-' + choice_number + '" class="decision_tree_choice_area">\
    <input type="hidden"  class="decision_tree_input decision_tree_raw_choice_for_step_' + step_number + '  step_' + step_number + '_choices" \
    name="' + choice_number + '_raw_choice_number" value="' + choice_number + '" >\
    <div data-id="' + choice_number + '" data-step_number="' + step_number + '" class="decision_tree_remove_choice">Delete</div>\
    <div class="decision_tree_row">\
        <div class="decision_tree_cell decision_tree_cell_one">\
            <span id="decision_tree_choice_label-' + choice_number + '" class="number_circle_small">Choice ' + choice_number + '</span>\
        </div>\
    </div>\
    <div class="decision_tree_row">\
        <div class="decision_tree_cell">\
            <div class="decision_tree_cell decision_tree_cell_label">\
                  <label for="choice_' + choice_number + '_text">\
                          Text\
                  </label>\
            </div>\
            <input type="text" id="' + choice_number + '_text"  size="60" \
            class="decision_tree_input decision_tree_required \
                regular-text step_' + step_number + '_choices " \
                name="' + choice_number + '_text" >\
        </div>\
        <div class="decision_tree_cell">\
            <div class="decision_tree_cell decision_tree_cell_label">\
                    <label for="' + choice_number + '_next">\
                        Go To Step #\
                    </label>\
            </div>\
            <select class="decision_tree_input decision_tree_required  \
                decision_tree_next_select decision_tree_no_self  \
                step_' + step_number + '_choices" name="' + choice_number + '_next" \
            id="'   + choice_number + '_next" step_id="' + step_number + '">\
            <option value="">Select Next Step</option></select>\
        </div>\
    </div>\
    </div>';
    return choice_area;
}

/**
 * take a JSON DTree object presumably from the DB and populate a form. form
 * is expected to *not* exist in te DOM and it will be created. if you pass it
 * a non-object or empty object, it will not fill out the form and return false.
 * if the firm is filled out successfully return true.
 * @param {DTree} DTree pre-populated 
 * @returns {Boolean} if the form was filled out or not
 */
DTree.prototype.populateDTFormFromDB = function(DTree_object_restored) {
    
    // init and check for some basics
    var formFilled = false;
    if (typeof DTree_object_restored == "undefined" ){
        return formFilled;
    }
    
    // make a local namespace reference of object so we can use it 
    // inside the for eaches below. 
    var objSelf = this;
    
    // loop through first time to populate just the steps.  choices have to 
    // wait until all steps are in the DOM.
    jQuery.each(DTree_object_restored.data, function(step_number, questionObj) {
        if (typeof step_number != "undefined" && typeof questionObj != "undefined" ){
            // set this a lot, i know (i like alots!)
            formFilled = true;
            
            //bump unique counter as we're populateing object to avoid colisions
            objSelf.get_uniq_id();
            
            // add a question and populate it's fields
            step_id = objSelf.add_step_field(step_number, 0);
            objSelf.steps[step_id] = questionObj.question;
            jQuery('#' + step_number + '_question_text').val(questionObj.question);
            jQuery('#' + step_number + '_type').val(questionObj.type);
            jQuery('#' + step_number + '_subtext').val(questionObj.subtext);
        }        
        
    });

    
    // loop through second time to fill out the choices now that all the questions
    // are populated
    jQuery.each(DTree_object_restored.data, function(step_number, questionObj) {
        if (typeof step_number != "undefined" && typeof questionObj != "undefined" ){
            
            // now do up the choices, if any
            jQuery.each(questionObj.choices, function(id, choiceObj) {
                //bump unique counter as we're populateing object to avoid colisions
                objSelf.get_uniq_id();

                // add a choice and then fill it up
                choice_number = objSelf.add_choice_field(step_number);
                DTree_field_set.update_next_step_select();
                jQuery('#' + choice_number + '_text').val(choiceObj.choice), 
                jQuery('#' + choice_number + '_next').val(choiceObj.next);
            });
        }        
    });
    
    return formFilled;
}
/**
 * Gathers the form values into a DT object. This form is used for:in error
 * handling to check the value of the form, to preview the DT with out saving it,
 * or to get the DT into a JSON object which can then be saved. It will build out a
 * JSON object with the following elements:
 * var DTree_object = new Object();
 *   DTree_object.data = {}; // this is where the steps, answers and choices will be
 *   DTree_object.index = {}; // stores a 0 based index of the order of the steps
 *   DTree_object.start_ID = null; // which step is the first one
 *   DTree_object.version = DTree_version; // what version of the DT JSON object this is
 * 
 * @param {boolean} getFormIDs defaults to false, optionally get the IDs for the fields.  NOT 
 * SAFE for using to save to DB! This is intended to be used with client side
 * error checking
 * @returns {Object} DTree_object
 */
DTree.prototype.getDTObjectFromForm = function(getFormIDs) {
    
    if (typeof getFormIDs == "undefined" ){
        getFormIDs = false;
    }
    
    // init vars we'll use below
    var step_type = null;
    var step_question_text = null; 
    var step_number = null;
    var choice_number = null;
    var count = 0;

    // prep the object for return
    var DTree_object = new Object();
    DTree_object.data = {};
    DTree_object.index = {};
    DTree_object.start_ID = null;
    DTree_object.version = DTree_version;
    
    // make a local namespace reference of object so we can use it 
    // inside the for eaches below. 
    var objSelf = this;
    
    // get all steps
    var steps = objSelf.getFormValues('.decision_tree_raw_step:input');
    
    // loop through each step to gather the sub parts
    jQuery.each(steps, function() {
        
        // get local instance of step number
        step_number = this;
        
        // init the DTree_object[step_number]
        DTree_object.data[step_number] = {};
        
        // build up our index <-> step_number and
        // grab the start which is always 0th for now
        DTree_object.index[count] = step_number;
        if (count == 0 && jQuery('#' + step_number + '_type').val() == "question"){
            DTree_object.start_ID = step_number;
        }
        count++;
        
        // update return object
        DTree_object.data[step_number].question = jQuery('#' + step_number +
                '_question_text').val();
        DTree_object.data[step_number].type = jQuery('#' + step_number + 
                '_type').val();
        DTree_object.data[step_number].subtext = jQuery('#' + step_number + 
                '_subtext').val();
        DTree_object.data[step_number].choices = new Array();
        
        if(getFormIDs){
            DTree_object.data[step_number].questionId = jQuery('#' + step_number + 
                    '_question_text').attr('id');
            DTree_object.data[step_number].typeId = jQuery('#' + step_number + 
                    '_type').attr('id');
            DTree_object.data[step_number].subtextId = jQuery('#' + step_number + 
                    '_subtext').attr('id');
        }
        
        
        // get and loop over chioces from form
        formchoices = objSelf.getFormValues('.decision_tree_raw_choice_for_step_' + 
                step_number + ':input');
        jQuery.each(formchoices, function() {
            choice_number = this;

            // update return object and optionally include the IDs from the fields
            if(getFormIDs){
                DTree_object.data[step_number].choices.push  ({
                        choice: jQuery('#' + choice_number + '_text').val(), 
                        next: jQuery('#' + choice_number + '_next').val(),
                        choiceId: jQuery('#' + choice_number + '_text').attr('id'), 
                        nextId: jQuery('#' + choice_number + '_next').attr('id')
                    });
            } else {
                DTree_object.data[step_number].choices.push  ({
                        choice: jQuery('#' + choice_number + '_text').val(), 
                        next: jQuery('#' + choice_number + '_next').val()
                    });
            }
        });
    });

    return  DTree_object;
};

/**
 * generic class to get name/value pairs from a form
 * thanksthanks http://stackoverflow.com/questions/1184624/convert-form-data-to-js-object-with-jquery 
 * @param {string} formClass to fetch paris from.  eg "form" or  ".yourclass:input"
 * @returns {Array} of name/value pairs
 */
DTree.prototype.getFormValues = function(formClass) {
    var o = {};
    var a = jQuery(formClass).serializeArray();
    jQuery.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

/**
 * ensures a DT form is ready to be saved.  will disable save button if note ready
 * as well as show an error box
 * @param {type} presentError
 * @param {type} hideFieldError
 * @returns {Boolean}
 */
DTree.prototype.validateForm = function(showErrors ) {
// TODO ensure every field that is in error gets highlighted when you click "publish"
// TODO use  jQuery( ).dialog() https://jqueryui.com/dialog/ instead of jank home grown, see decision_tree_insert_tag.js for example
    // init and set defautls
    if (typeof showErrors == "undefined" ){
        showErrors = false;
    }
    
    var haveAQuestion = false;
    var haveAnAnswer = false;
    var haveAChoice = false;
    var foundErrors = false;
    var missingFields = false;
    var firstStepAQuestion = true;
    var nextGoesToCurrent = false;
    var allQuestionsHaveChoices = true;
    var errorMessage = '';
    
    //hide error field 
    jQuery("#dtree_entry_block_errors").hide();
    jQuery('#dtree_entry_block_error_msg').html('&nbsp;');

    // get the entire DT and iterate over it to validate
    DTree_validate_copy = DTree_field_set.getDTObjectFromForm(true);  
    var count = 1;
    jQuery.each(DTree_validate_copy.data, function(step_id, stepObj){

        if (jQuery('#' + stepObj.questionId).val() == ""){
            missingFields = true;
        }

        // ensure we have at least one question and check question's choice
        // values
        type = stepObj.type;
        if (type == "question"){
            haveAQuestion = true;
            
            jQuery.each(stepObj.choices, function(choice_count ,chioceObj){
                // build up ID of next drop down and remove any errors to start
                nextId = '#' + chioceObj.nextId;

                // see if they've choosen "got to step #" that goes to itself
                if (chioceObj.next == step_id){
                    nextGoesToCurrent = true;
                } else 
                    
                // check that the next step drop down isn't empty
                if (jQuery(nextId).val() == ""){
                    missingFields = true;
                }
            
                // check that we have choice text
                if (jQuery( '#' + chioceObj.choiceId).val() == ""){
                    missingFields = true;
                }
                
                haveAChoice = true;
            });

            // ensure we have at lease a single choice per step
            if (!haveAChoice){
                allQuestionsHaveChoices = false;
            }
        } 
        
        // ensure first step is a question
        if (count == 1 && type != "question" ){
            firstStepAQuestion = false;
        }
        
        // ensure we have at least one answer
        if (type == "answer"){
            haveAnAnswer = true;    
        }
        count++;
    })
    
    // spew out errrors
    if (!haveAQuestion){
        errorMessage = '<li>You must have at least one  step that is a question!</li>' 
            + errorMessage;
        foundErrors = true;
    }
    if (!haveAnAnswer ){
        errorMessage = '<li>You must have at least one step that is an answer!</li>' 
            + errorMessage;
        foundErrors = true;
    }
    if (!firstStepAQuestion ){
        errorMessage = '<li>The first step must be a question!</li>'  + errorMessage;
        foundErrors = true;
    }
    if (nextGoesToCurrent ){
        errorMessage = '<li>Make sure a choice doesn\'t go to itself!</li>' 
            + errorMessage;
        foundErrors = true;
    }
    if (missingFields ){
        errorMessage = '<li>Ensure all fields are filled out!</li>' + errorMessage;
        foundErrors = true;
    }
    if (!allQuestionsHaveChoices ){
        errorMessage = '<li>Ensure all questions have at least one choice!</li>'
            + errorMessage;
        foundErrors = true;
    }

    // show errors if any and if asked to
    if (showErrors && foundErrors){
        DTree_field_set.highligthEmptyRequiredFields();
        DTree_field_set.highlightChoicesGoToSelfOrEmpty();
        jQuery('#dtree_entry_block_error_msg').html(errorMessage);
        //jQuery("#dtree_entry_block_errors").fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);
        jQuery("#dtree_entry_block_error_msg").dialog({
            dialogClass: "dtree_entry_error_dialog" ,
            modal: true,
            title: "Oops!"
	});
        jQuery(".decision_tree_textfield_error").fadeOut(400).fadeIn(400);    
    }

    return DTree_field_set.enableForm(foundErrors);
}

/**
 * check the passed in class to see if any of those are empty.  if they are,
 * highlight them with ifEmptyAddClassElseRemove() which means if they're not
 * in error, error class will be removed.
 * NOTE - this should be called *before* highlightChoicesGoToSelfOrEmpty()
 * @param {string} requiredClass defaults to ".decision_tree_required". should be 
 *  passed as ".foo" not "foo"
 */
DTree.prototype.highligthEmptyRequiredFields = function(requiredClass) {
    
    // default to error class
    if (typeof requiredClass == "undefined" ){
        requiredClass = '.decision_tree_required';
    }
    
    jQuery(requiredClass).each(function(){
        DTree_field_set.ifEmptyAddClassElseRemove(this.id);
    });       
}
/**
 * check the passed in class to see if any of have the same parent ID as passed in
 * @param {string} requiredClass defaults to ".decision_tree_required". 
 * will call stepGoesToSelfOrEmptyThenAddRemoveClass() to process each field
 * NOTE - this should be called *after* highligthEmptyRequiredFields().  As well it
 *  checks for required class and will highlight if empty
 *  passed as ".foo" not "foo"
 */
DTree.prototype.highlightChoicesGoToSelfOrEmpty = function(idLocation, noSelfClass, errorClass) {

    // defaults
    if (typeof noSelfClass == "undefined" ){
        noSelfClass = '.decision_tree_no_self';
    }
    
    if (typeof errorClass == "undefined" ){
        errorClass = 'decision_tree_textfield_error';
    }
    
    jQuery(noSelfClass).each(function(){
        DTree_field_set.stepGoesToSelfOrEmptyThenAddRemoveClass(this.id, errorClass);
    });       
}

DTree.prototype.stepGoesToSelfOrEmptyThenAddRemoveClass = function(fieldId,  errorClass) {
    // default to error class
    if (typeof errorClass == "undefined" ){
        errorClass = 'decision_tree_textfield_error';
    }
    step_num = jQuery('#' + fieldId).attr('step_id');
    next_num = jQuery('#' + fieldId).val();
    if (step_num == next_num){
        jQuery('#' + fieldId).addClass(errorClass);
    } else if (jQuery('#' + fieldId).hasClass('decision_tree_required') &&
        jQuery('#' + fieldId).val() == ""){
            jQuery('#' + fieldId).addClass(errorClass);
    } else {
        jQuery('#' + fieldId).removeClass(errorClass);
    }
}

/**
 * helper function to see if the val() of the ID passed is equal to "".  If
 * yes, then add the passed calls to that ID.  If not, remove class.
 * @param {string} fieldId to validate. Shold be passed as "foo" not "#foo"
 * @param {string} className to add to ID. Defaults to "decision_tree_textfield_error" 
 *      if not passed
 * @returns {Boolean} if the field was empty or not
 */
DTree.prototype.ifEmptyAddClassElseRemove = function(fieldId, className) {

    // default to error class
    if (typeof className == "undefined" ){
        className = 'decision_tree_textfield_error';
    }
    // return fals if we didn't get an ID
    if (typeof fieldId == "undefined" ){
        return false;
    }
    
    // flip flop based on == ""
    fieldId = '#' + fieldId;
    if (jQuery( fieldId ).val() == ""){
        isEmpty = true;
        jQuery(fieldId).addClass(className);
    } else {
        isEmpty = false;
        jQuery(fieldId).removeClass(className);
    }

    // return if it was empty or not
    return isEmpty;
    
}

/**
 * Change the forms stated to being enabled or disabled
 * TODO don't have this disable any form (eg blog posts), only the DT form save/update
 * @param {Boolean} foundErrors
 * @returns {Boolean} exactly what was passed in via foundErrors
 */
DTree.prototype.enableForm = function(foundErrors) {
    if (typeof foundErrors == "undefined" ){
        foundErrors = true;
    }

    if (foundErrors){
        jQuery('#publishing-action input[type=submit]').addClass('formSubmitButtonError');
    } else {
        jQuery('#publishing-action input[type=submit]').removeClass('formSubmitButtonError');
    }
    
    return foundErrors;
}
/**
 * ensures a DT form is ready to be saved.  
 * will disable save button if notr ready.
 */
DTree.prototype.previewTreeFromForm = function() {

    foundErrors = DTree_field_set.validateForm(true);
    jQuery("#dtree_entry_preview_inner").hide();
    if (foundErrors){
        return;
    } else {
        jQuery("#dtree_entry_preview_inner").empty()
            .append('<form id="decision_tree_area_0" class="decision_tree_area"></form>')
            .fadeIn(400);
        dt_tree_0 = DTree_field_set.getDTObjectFromForm();
        dt_tree_0.title = jQuery("#title").val();
        dt_tree_0.donated = DTree_field_set.donated;
        process_question(0);
        jQuery("#dtree_entry_preview_inner").dialog({
            minWidth: 500,
            modal: true,
            title: "Preview"
	});
    }

}

/**
 * log something to JS console, prepend with timestamp 
 * @param {string} note to print with your log
 * @param {object} object to run through JSON.stringify();
 */
DTree.prototype.log = function(note, object) {
    
    if (typeof note == "undefined" ){
        note = "No note passed to log() ";
    }
    if (typeof object == "undefined" ){
        object = null;
    }
    var objStr = "";
    if (object != null){
        objStr = ' Object: ' + JSON.stringify(object);
    }
    var nowepoch = Math.round((new Date()).getTime() / 1000);
    var logme = nowepoch + ' Note: ' + note + objStr;
    window.console&&console.log(logme);
}

DTree.prototype.initializeForm= function(){
    // load the form from DB, if that fails add an empty form
    formFilled = false;
    if (typeof DTree_object_restored != "undefined" ){
        formFilled = DTree_field_set.populateDTFormFromDB(DTree_object_restored);
    }
    if(!formFilled){
        //add an initial question box
        DTree_field_set.add_step_field();
    }    
    DTree_field_set.validateForm();
}

jQuery(document).ready(function($) {
    
    //listen for clicks on the add question button
    jQuery( document ).on("click", ".decision_tree_add_step", function(event_obj) {
        DTree_field_set.add_step_field();
        DTree_field_set.update_next_step_select();
        DTree_field_set.validateForm();
        event_obj.preventDefault();
    });
    //listen for clicks on an add choice button
    jQuery( document ).on("click", ".decision_tree_add_choice", function(event_obj) {
        DTree_field_set.add_choice_field($(this).attr("step_nuber"));
        DTree_field_set.update_next_step_select();
        DTree_field_set.validateForm();
        event_obj.preventDefault();
    });
    //listen for changing of the contents of a question field
    jQuery( document ).on("change", ".decision_tree_step_text", function(event_obj) {
        DTree_field_set.add_step_text(event_obj);
        DTree_field_set.update_next_step_select();
        event_obj.preventDefault();
    });   
    //listen for blurs on required fields
    jQuery( document ).on("blur", ".decision_tree_required", function() {
        gotErrror = DTree_field_set.ifEmptyAddClassElseRemove(jQuery(this).attr('id'));
        DTree_field_set.validateForm();
    });   
    //listen for blurs on no self fields
    jQuery( document ).on("blur", ".decision_tree_no_self", function() {
        gotErrror = DTree_field_set.stepGoesToSelfOrEmptyThenAddRemoveClass(jQuery(this).attr('id'));
        DTree_field_set.validateForm();
    });    
    //listen for clicks on removal of a choice button
    jQuery( document ).on("click", ".decision_tree_remove_choice", function(event_obj) {
        DTree_field_set.remove_choice_field(event_obj);
        DTree_field_set.validateForm();
        event_obj.preventDefault();
    });     
    //listen for clicks on removal of a question button
    jQuery( document ).on("click", ".decision_tree_remove_step", function(event_obj) {
        DTree_field_set.remove_step_field(event_obj);
        DTree_field_set.validateForm();
        event_obj.preventDefault();
    });     
    // listen for clicks on "publish" of DT 
    jQuery( document ).on("click", "#publish", function(event_obj) {
        DTree_object = DTree_field_set.getDTObjectFromForm();
        jQuery("#dtree_blob").val(JSON.stringify(DTree_object));
        return true;
    });
    
    jQuery( document ).on("click", "#dtree_entry_preview_close", function(event_obj) {
        jQuery('#dtree_entry_preview_inner').fadeOut(400);
        event_obj.preventDefault();
    }); 
    jQuery( document ).on("click", "#dtree_entry_block_errors_hide", function() {
        jQuery('#dtree_entry_block_errors').fadeOut(400);
    });  
    
    jQuery( document ).on("click", ".formSubmitButtonError", function(event_obj) {        
        event_obj.preventDefault();
        return DTree_field_set.validateForm(true);
        
    });
    
    jQuery( document ).on("click", "#dt_preview_button", function(event_obj) {
        DTree_field_set.previewTreeFromForm();
        event_obj.preventDefault();
    });
    
  });
  