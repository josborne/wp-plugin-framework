jQuery(document).ready(function ($)
{
    $('#settings-form').submit(function (e)
    {
        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        var valid = true;

        if (valid)
        {
            $.ajax({
                type: "POST",
                url: admin_ajaxurl,  //this parameter is set in admin_menu_scripts() in the main plugin file
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data)
                {
                    //pure JS head to top of page
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

                    if (data.success)
                    {
                        //show the WordPress styled updated message
                        $("#updateMessage").text("Settings updated");
                        $("#updateDiv").addClass('updated').show();
                        $form.find('button').prop('disabled', false);
                    }
                    else
                    {
                        $form.find('button').prop('disabled', false);
                        $(".tips").html(data.msg);
                    }
                }
            });
        }

        return false;
    });

});
