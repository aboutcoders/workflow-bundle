jQuery(function () {
    jQuery('#mainTabs a').click(function (e) {
        var url = jQuery(this).attr("data-url");
        var href = this.hash;
        var pane = jQuery(this);

        if (url) {
            e.preventDefault();
            var l = Ladda.create(this);
            l.start();
        }
        // ajax load from data-url
        jQuery(href).load(url, function (result) {
            pane.tab('show');
            l.stop();
        });
    });
});