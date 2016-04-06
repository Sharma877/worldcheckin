<h3>API Credentials</h3>


<fieldset>
<legend>Live</legend>
<ul>
<li>
<label for="live_username">Username:</label> <input id="live_username" type="text" name="ginvoice[credentials][live][username]" value="<?php echo $ginvoice['credentials']['live']['username']; ?>" />
</li>
<li>
<label for="live_api">API Token:</label> <input id="live_api_token" type="text" name="ginvoice[credentials][live][api_token]" value="<?php echo $ginvoice['credentials']['live']['api_token']; ?>" />
</li>
</ul>
<input id="ginvoice_enable_live" type="radio" value="live" name="ginvoice[api_status]" <?php echo ($ginvoice['api_status']=='live'?'checked="checked"':''); ?> />
<label for="ginvoice_enable_live">Enable</label>
</fieldset>

<fieldset>
<legend>Test</legend>
<ul>
<li>
<label for="test_username">Username:</label> <input id="test_username" type="text" name="ginvoice[credentials][test][username]" value="<?php echo $ginvoice['credentials']['test']['username']; ?>" />
</li>
<li>
<label for="test_api">API Token:</label> <input id="test_api_token" type="text" name="ginvoice[credentials][test][api_token]" value="<?php echo $ginvoice['credentials']['test']['api_token']; ?>" />
</li>
</ul>
<input id="ginvoice_enable_test" type="radio" value="test" name="ginvoice[api_status]" <?php echo ($ginvoice['api_status']=='test'?'checked="checked"':''); ?> />
<label for="ginvoice_enable_test">Enable</label>
</fieldset>


<p class="submit" style="margin-top:10px;">
    <input name="geodir_invoice_update_settings" class="button-primary" type="submit"
           value="<?php _e('Save changes', 'geodirectory'); ?>"/>
           
</p>
