(function($) {
    $(document).ready(function() {
        $('#sortable1, #sortable2').sortable({
            connectWith: '.connectedSortable'
        }).disableSelection();

        var videos = $('#ostoolbar_videos');

        if (videos[0]) {
            var updateSortableField = function() {
                var selected = $('#sortable2').sortable('toArray');
                var string = selected.join(',');
                videos.val(string);
            };

            $('#sortable2').bind('sortupdate', function(event, ui) {
                updateSortableField();
            });

            updateSortableField();
        }

        var toolbar_permissions = $('#ostoolbar_permissions');
        var role_permissions = $('.role_permission');
        if (toolbar_permissions[0] && role_permissions[0]) {
            role_permissions.on('click', function(evt) {
                var text = {};
                role_permissions.each(function(idx, el) {
                    text[el.name] = el.checked ? 1 : 0;
                });
                console.log(toolbar_permissions.val());
                toolbar_permissions.val(JSON.stringify(text));
            });
            $(role_permissions[0]).fire('click');
        }
    });
})(jQuery);
