jQuery(function () {
    jQuery('#mainTabs a').click(function (e) {

        var pane = jQuery(this);
        pane.tab('show');
        var url = jQuery(this).attr("data-url");
        var href = this.hash;

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

    // store the currently selected tab in the hash value
    jQuery("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
        var id = jQuery(e.target).attr("href").substr(1);
        window.location.hash = id;
    });

    // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    jQuery('#myTab').find('a[href="' + hash + '"]').tab('show');
});