jQuery(document).ready(function ($)
{
    $('#showLoading').hide();

    //example of how to access data passed via wp_localize_script
    $('.options').text(pf_options.urlOption);

    $('#pf-form').submit(function (e)
    {
        $('#showLoading').show();

        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        var valid = true;

        if (valid)
        {
            $.ajax({
                type: "POST",
                url: ajaxurl,  //this parameter is set in pf_form() in the main plugin file
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    $('#showLoading').hide();
                    $form.find('button').prop('disabled', false);
                    $(".pf_form_message").html(data.msg);
                }
            });
        }

        return false;
    });

});
