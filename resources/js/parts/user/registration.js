$(function()
{
    $('[name=mail_address]').on('change',function() {
        let mail_address = $(this).val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: location.origin + "/user/ajax/check_mail_address",
            type: 'POST',
            data: {'mail_address': mail_address, '_method': 'POST'}
        })
        // Ajaxリクエストが成功した場合
        .done(function(data) {
            //console.log("connection success");
            let message = $("#mail_address_validation");
            if (data.regist == 'invalid') {
                message.removeClass("alert alert-success");
                message.addClass("alert alert-danger");
                message.text("登録できないメールアドレスです。");
            } else if (data.regist == 'ng') {
                message.removeClass("alert alert-success");
                message.addClass("alert alert-danger");
                message.text("すでに登録されているメールアドレスです。");
            } else if (data.regist == 'ok') {
                message.removeClass("alert alert-danger");
                message.addClass("alert alert-success");
                message.text("登録可能なメールアドレスです。");
            }
        })
        // Ajaxリクエストが失敗した場合
        .fail(function(data) {
            // ajax失敗時の処理
            console.log("not connnect");
        });
    });

    $('[name=name]').on('change',function() {
        let name = $(this).val();
        let old_name = $('#old_name').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: location.origin + "/user/ajax/check_name",
            type: 'POST',
            data: {'name': name, 'old_name': old_name, '_method': 'POST'}
        })
        // Ajaxリクエストが成功した場合
        .done(function(data) {
            //console.log("connection success");
            let message = $("#name_validation");
            if (data.regist == 'invalid') {
                message.removeClass("alert alert-success");
                message.addClass("alert alert-danger");
                message.text("登録できないニックネームです。");
            } else if (data.regist == 'ng') {
                message.removeClass("alert alert-success");
                message.addClass("alert alert-danger");
                message.text("すでに登録されているニックネームです。");
            } else if (data.regist == 'ok') {
                message.removeClass("alert alert-danger");
                message.addClass("alert alert-success");
                message.text("登録可能なニックネームです。");
            }
        })
        // Ajaxリクエストが失敗した場合
        .fail(function(data) {
            // ajax失敗時の処理
            console.log("not connnect");
        });
    });

});
