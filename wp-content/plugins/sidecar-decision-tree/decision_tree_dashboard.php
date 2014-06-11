<?php
         sidecar_track::track_screen('dt_dashboard');
?>

    <h2>Decision Tree 1.0.2</h2>
    <br>
<div class="dt_simple_float">
	<div class="dt_dashboard_box">
	    <h3>Pro Version</h3>
	
	    Great news! You are running the pro version already with all features.
	    <b>But</b> this isn't free free, it is donation-ware. Here's the deal, try this out all you want
	    and when you decide to use please donate according to the following:
	    
	    <table class='dt_price_chart'>
	        <tr>
	            <th> Non-Commercial </th><td> $2</td>
	        </tr>
	        <tr>
	        <th> Business/Commercial  </th><td> $5</td>
	        </tr>
	        <tr>
	        <th> Registered Non-Profit</th><td> $0</td>
	        </tr>
	    </table>
	
	    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	    <input type="hidden" name="cmd" value="_s-xclick">
	    <table>
	    <tr><td><input type="hidden" name="on0" value="Version">Version</td></tr><tr><td><select name="os0">
	            <option value="Personal">Personal $2.00 USD</option>
	            <option value="Business">Business $5.00 USD</option>
	    </select> </td></tr>
	    </table>
	    <input type="hidden" name="currency_code" value="USD">
	    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
	    ">
	    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	    </form>
	</div>
	<div class="dt_dashboard_box">
	        <h3>Remove Attribution</h3>
	        If you've paid and you'd like to remove the attribution on your trees, just click below and save.
	        <form method="post" action="options.php" id='dt_idonated_form'> 
	            <?php settings_fields( 'dt_options_group' );
	            do_settings_sections( 'dt_options_group' );
	            submit_button();
	            ?>
	        </form>
	</div>
</div>

<div class="dt_simple_float">	
	<div class="dt_dashboard_box">
	    <h3>About & Support</h3>
	        <div class="dt_instruction_block">
	
	        Decision Trees is a super cool product of Side Car Apps. We have more apps, and are always 
	        looking for great ideas so check out our portfolio and let us know if you have ideas for new plug-ins. 
	        <p>
	        <b>Support</b><br>
	        Need help? Visit the <a href="http://sidecar.tv/decision_tree/">Decision Tree</a> page for all the details.
	        <p>
	        <b>Privacy & Terms Of Service</b><br>
	        This Plug-in is covered by our <a href="http://sidecar.tv/about-privacy-tos/">Terms Of Service</a> and <a href="http://sidecar.tv/about-privacy-tos/">Privacy Policy</a>.
		</div>
	</div>
    
    <div class="dt_dashboard_box">
    <h3>Diagnostics</h3>
        <div class="dt_instruction_block">
            By default this pluging doesn't track anything, about anybody, doing anything, anywhere, anyhow. 
            However, we the developers would love to understand how you use this plugin so we can make it better
            for you. We don't care who you are, we won't try to sell you an Ab Master, we won't track your name,
            or your 3rd grade teacher, just how this plugin is used. See more details on our <a href='http://sidecar.tv/about-privacy-tos/'>Privacy Policy</a>.
        <p>
        <form method="post" action="options.php" id='dt_track_form'> 
            <?php settings_fields( 'dt_track_options_group' );
            do_settings_sections( 'dt_track_options_group' );
            submit_button();
            ?>
        </form>
		</div>
	</div>
</div>