<?php
$options = get_option('WP_PF_Options');
?>
<div class="wrap">
    <h2><?php echo __('Plugin Framework Settings', 'wp-plugin-framework'); ?></h2>
    <div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
    <form class="form-horizontal" action="" method="post" id="settings-form">
        <p class="tips"></p>
        <input type="hidden" name="action" value="pf_update_settings"/>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="textOption"><?php _e("Text Option: ", 'wp-plugin-framework'); ?> </label>
                </th>
                <td>
                    <input type="text" name="textOption" id="textOption" value="<?php echo $options['textOption']; ?>" class="regular-text">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php _e("Toggle Option: ", 'wp-plugin-framework'); ?> </label>
                </th>
                <td>
                    <label class="radio">
                        <input type="radio" name="toggleOption" value="1" <?php echo ($options['toggleOption'] == '1') ? 'checked' : '' ?> > Yes
                    </label> <label class="radio">
                        <input type="radio" name="toggleOption" value="0" <?php echo ($options['toggleOption'] == '0') ? 'checked' : '' ?>> No
                    </label>
                    <p class="description">Choose between these two options</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="numOption"><?php _e("Number Option: ", 'wp-plugin-framework'); ?> </label>
                </th>
                <td>
                    <input type="text" name="numOption" id="numOption" value="<?php echo $options['numOption']; ?>" class="regular-text">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for="urlOption"><?php _e("URL Option: ", 'wp-plugin-framework'); ?> </label>
                </th>
                <td>
                    <input type="text" name="urlOption" id="urlOption" value="<?php echo $options['urlOption']; ?>" class="regular-text">
                    <p class="description">Add a valid URL here</p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_attr_e('Save Changes') ?></button>
        </p>
    </form>
</div>
