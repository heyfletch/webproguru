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

function Decision_Tree_Inserter(){
    this.init = Array();
}

Decision_Tree_Insert = new Decision_Tree_Inserter();

/**
 * This method will get a list of decision trees via an AJAX call and populate the select list in the dialog
 * Then it will open the dialog
 * @param {string} editor indicates the type of editor being used either 'visual' or 'text'
 */
Decision_Tree_Inserter.prototype.decision_tree_prompt_and_insert = function(editor){
    //console.log("decision_tree_prompt_and_insert called for " + editor);
    jQuery.ajax({
      url: ajaxurl,
      data: {action:'decision_tree_list_trees'},
      success: function (data){
            dt_select='';
            for(tree in data){
                dt_select+="<option value='" + tree + "'>" + data[tree] + "</option>";
            }           
            jQuery("#dt_select_tree_list_"+editor).html(dt_select)
      },
      fail: function(){alert ('failed getting data')},
      dataType:'json'
    });
    jQuery( "#decision_tree_prompt_"+editor ).dialog("open");
}

/**
 * This method will initialize the decision tree inserter, it makes sure it can only be called
 * once for each editor
 */
Decision_Tree_Inserter.prototype.decision_tree_init = function(ed, e, c){
  //if clicked on visual, hide text
jQuery( "#content-tmce" ).click(function() {
    if(jQuery( "#decision_tree_prompt_text" ).dialog( "isOpen" )){
        jQuery( "#decision_tree_prompt_text" ).dialog("close");
    }
});
//if clicked on text, hide visual
jQuery( "#content-html" ).click(function() {
    if(jQuery( "#decision_tree_prompt_visual" ).dialog( "isOpen" )){
        jQuery( "#decision_tree_prompt_visual" ).dialog("close");
    }
});

if(e!=null){
        editor = 'text';
    }
    else{
        editor = 'visual';
    }
    if(this.init[editor] != true){
        this.init[editor] = true;
        //console.log("decision_tree_init init called: " + editor);
            dt_select ="<div style='display: none;' id='decision_tree_prompt_"+editor+"' title='Select Decision Tree'><p>";
            dt_select+="<form><fieldset>";
            dt_select+="<b>Tree: </b><select style='width:225px' id='dt_select_tree_list_"+editor+"' class='text ui-widget-content ui-corner-all'>";
            dt_select+="</select></p>";
            dt_select+="</fieldset></form>";
            dt_select+= "</div>";
        jQuery( "body" ).append(dt_select);
        
        if(editor=='visual'){
            if(jQuery( "#decision_tree_prompt_text" ).dialog( "isOpen" )){
                jQuery( "#decision_tree_prompt_text" ).dialog("close");
            }
            jQuery( "#decision_tree_prompt_"+editor ).dialog({
                    autoOpen: false,
                  height: 170,
                  width: 300,
                  resizable: false,
                 buttons: [ { text: "Insert", click: function() { 
                                    jQuery( this ).dialog( "close" ); 
                                    shortcode = '[decisiontree id="' + jQuery("#dt_select_tree_list_visual").val() + '"]';
                                        //console.log("inserting via visual editor");
                                     ed.execCommand('mceInsertContent', 0, shortcode);

                         } } ]
                });
        }
        else{
            if(jQuery( "#decision_tree_prompt_visual" ).dialog( "isOpen" )){
                jQuery( "#decision_tree_prompt_visual" ).dialog("close");
            }
            jQuery( "#decision_tree_prompt_"+editor ).dialog({
                        autoOpen: false,
                      height: 170,
                      width: 300,
                      resizable: false,
                     buttons: [ { text: "Insert", click: function() { 
                                        jQuery( this ).dialog( "close" ); 
                                        shortcode = '[decisiontree id="' + jQuery("#dt_select_tree_list_text").val() + '"]';
                                            //console.log("inserting via txt editor");
                                            //console.log("this is " + this);
                                            this.tagStart = shortcode;
                                            QTags.TagButton.prototype.callback.call(this, e, c, ed);

                             } } ]
                });
        }
    }
}
//
