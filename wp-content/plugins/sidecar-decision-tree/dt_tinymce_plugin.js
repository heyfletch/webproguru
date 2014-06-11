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

(function() {
    tinymce.create('tinymce.plugins.decision_tree_insert_button', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addButton('decision_tree_insert_button', {
                title : 'Insert Decision Tree',
                cmd : 'insert_dt',
                image : url + '/tiny_decision_tree.png'
            });
            ed.addCommand('insert_dt', function() {
                Decision_Tree_Insert.decision_tree_prompt_and_insert('visual');
            });
            Decision_Tree_Insert.decision_tree_init(ed);
            },
 
        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },
 
        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Decition Tree Insert Button',
                author : 'Side Car Apps',
                authorurl : 'http://sidecar.tv',
                infourl : 'http://sidecar/tv',
                version : "0.1"
            };
        }
    });
 
    // Register plugin
    tinymce.PluginManager.add( 'decision_tree_insert_button', tinymce.plugins.decision_tree_insert_button );
})();

