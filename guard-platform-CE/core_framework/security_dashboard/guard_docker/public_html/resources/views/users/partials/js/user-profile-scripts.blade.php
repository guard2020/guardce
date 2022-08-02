<script src="{!! asset('js/parsley.min.js') !!}"></script>
<script type="text/javascript">

    $(function () {
        let url = window.location.href;
        let urlLastSegment = url.substr(url.lastIndexOf('/') + 1);

        let formSettings = $('#smart-dashboard-form-settings');
        formSettings.parsley();

        let profile = $("#profile");
        let setting = $("#setting");
        let users = $("#users");
        let profileSection = $('#profileSection');
        let settingSection = $('#settingSection');
        let usersSection   = $('#usersSection');

        if(urlLastSegment === 'profile'){
            usersSection.hide();
            settingSection.hide();
            profileSection.show();
        } else if(urlLastSegment === 'setting'){
            profileSection.hide();
            profile.removeClass('active');
            users.removeClass('active')
            usersSection.hide();
            setting.addClass('active')
            settingSection.show();
        } else if(urlLastSegment === 'list'){
            profileSection.hide();
            settingSection.hide();
            profile.removeClass('active');
            setting.removeClass('active')
            users.addClass('active')
            usersSection.show();
        }
        else {
            settingSection.hide();
        }

        profile.click(function(){
            settingSection.hide();
            profileSection.show();
            usersSection.hide();
        });

        setting.click(function(){
            profileSection.hide();
            usersSection.hide();
            settingSection.show();
        });

        users.click(function(){
            profileSection.hide();
            settingSection.hide();
            usersSection.show();
        });

    });
</script>