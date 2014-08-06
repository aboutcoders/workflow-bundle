jQuery(function () {
    jQuery('.history-details').click(function (e) {
        e.preventDefault();
        var url = jQuery(this).attr("data-url");

        if (url) {
            var l = Ladda.create(this);
            l.start();
        }
        // ajax load from data-url
        jQuery(".execution-history").load(url, function (result) {
            l.stop();
        });
    });
});