$(function()
{
    let login_info = $('#login_info');
    if(login_info.val() != undefined){
        let user_id    = login_info.children('[name=user_id]').val();
        let session_id = login_info.children('[name=session_id]').val();
        let app_url     = login_info.children('[name=app_url]').val();

        localStorage.setItem('user_id', user_id);
        localStorage.setItem('session_id', session_id);
        window.location.href = app_url;
    }
});
