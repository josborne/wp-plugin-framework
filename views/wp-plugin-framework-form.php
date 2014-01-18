<form class="pf_form" action="" method="POST" id="pf-form">
    <p class="pf_form_message"></p>
    <input type="hidden" name="action" value="pf_form_post"/>
    <p><strong>Form: </strong> <?php echo $form; ?></p>
    <p class="options"></p>
    <p>
        <label for="textVal">Enter a value:</label>
        <input name="textVal" id="textVal" type="text" />
    </p>
    <p>
        <button type="submit" class="button button-primary"><?php esc_attr_e('Submit') ?></button>
        <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." id="showLoading"/>
    </p>
</form>