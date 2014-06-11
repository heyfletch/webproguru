<?php
         sidecar_track::track_screen('dt_instructions');
?>
<div class="wrap">

    <div class="dt_option_page_box1">
    <h1>Instructions</h1>
    <div class="dt_instruction_block">
    Decision Trees take your users through simple multiple choice questions to arrive at an answer. 
    Decision Trees are made up of steps. There are two types of steps, questions & answers. 
    You create a tree by linking one or more questions to answers. Here is how it works:
    </div>
    <ol>
        <li><a href='#inst1'>Creating A New Tree</a></li>
        <li><a href='#inst2'>Creating A Question Step</a></li>
        <li><a href='#inst3'>Linking Questions</a></li>
        <li><a href='#inst4'>Creating An Answer</a></li>
        <li><a href='#inst5'>Step Diagram</a></li>
        <li><a href='#inst6'>Inserting A Tree</a></li>
        <li><a href='#inst7'>FAQ</a></li>
        
    </ol>
    
    <h2 id='inst1'>1.  Creating A New Tree</h2>
    <div class="dt_instruction_block">
        First start by selecting Add new from the Decision Tree menu. 
        You'll want to give your tree a title, usually the question/decision being solved.
        This will show up for users. 
    </div>
    <img src="<?php print plugins_url( '/img/add_new_tree.png' , __FILE__ ) ?>">
    
    <br>
    <h2 id='inst3'>2. Creating A Question Step</h2>
    <div class="dt_instruction_block">
        Next you'll want to provide the question for the first step and optional a subtext to explain the question. 
        Then enter one or more choices for the user to select from. 
        Click the Add Choice button if you need more than the two added for you.
    </div>
    <img src="<?php print plugins_url( '/img/question_and_choices.png' , __FILE__ ) ?>">
   
    <h2 id='inst3'>3. Linking Questions</h2>
    <div class="dt_instruction_block">
        Each question's choices will lead the user to another question or an answer. 
        To link one question to another step, select the Go To Step menu to the right of each choice.
    </div>
    <img src="<?php print plugins_url( '/img/link_choice.png' , __FILE__ ) ?>">
   
   
    <h2 id='inst4'>4. Creating An Answer</h2>
    <div class="dt_instruction_block">
        To create an answer which terminates a path of your tree, switch the Question/Answer drop down to
        Answer and type in the answer text.
    </div>
    <img src="<?php print plugins_url( '/img/answer.png' , __FILE__ ) ?>">
   
   
    <h2 id='inst5'>5. Step Diagram</h2>
    <div class="dt_instruction_block">
        This illustrates the various elements of a step so you can see how they correspond 
        to the publishing interface.
    </div>
    <img src="<?php print plugins_url( '/img/step_diagram.png' , __FILE__ ) ?>">    

    
    <h2 id='inst6'>6. Inserting A Tree</h2>
    <div class="dt_instruction_block">
       To insert a tree into a post or page you may use our handy post inserter widget. In the Visual or Text 
       WordPress editor click at the desired location within your post/page where you want the tree to be.
       Then select the Decision Tree button from the editor's toolbar. In the widget that pops up select 
       the tree you wish to insert and click the insert button.
       <p/>
       The result will be a Wordpress short code like this <code>[decisiontree id="281"]</code> will be inserted.
       You can delete this from your post/page at any point or edit the id number to point to another tree.
    </div>
    <img src="<?php print plugins_url( '/img/insert_tree.png' , __FILE__ ) ?>">    
    </div>
        <h2  id='inst7'>7. FAQ</h2>
    <div class="dt_instruction_block">
        <ol>
            <li>How do a publish a Decision Tree?</li>
            Press the publish button.
            <li>What happens when I publish a Decision Tree?</li>
            It is now avaliable to insert into a page or post.
            <li>How to I change the colors/fonts, etc..?</li>
            CSS is your friend, my friend. Within your theme you should be able to alter CSS. 
            Here the most common classes you'll want to override:
            <ul>
                <li><code>dt_display_title</code>: The Decision Tree question/title</li>
                <li><code>dt_display_question</code>: The question/answer for the current step</li>
                <li><code>dt_display_subtext</code>: The quesiton subtext</li>
                <li><code>dt_button</code>: The button for choices, answers and restarting a Decision Tree</li>
                <li><code>dt_radio_choice</code>: The button for choices</li>
                <li><code>dt_radio_answer</code>: The button for answer</li>
                <li><code>answer-restart</code>: The restart button</li>
                
            </ul>
            <li>How do I change the Decision Tree inserted in a post?</li>
            Some day we'll have edit, for now delete it and reinsert or just change the id of the shortcode.
            <li>Who's Buried in Grant's Tomb?</li>
            <a href="http://en.wikipedia.org/wiki/Grant's_Tomb">Ulysses S. Grant and his wife Julia Dent Grant</a>
        </ol>
    </div>
</div>
