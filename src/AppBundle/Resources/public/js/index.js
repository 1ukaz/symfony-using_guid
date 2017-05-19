$(document).ready(function() {

    $alertEl = $("#alert");

    $(".del").click(function(ev) {
        ev.preventDefault();
        var $anchor = $(this);
        var $sid = $anchor.data("pid");

        bootbox.confirm({
            title: "Deletion Confirmation",
            message: "Are you sure to delete Order: '"+$sid+"'?",
            buttons: {
                cancel: {
                    label: '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel'
                },
                confirm: {
                    label: '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Confirm'
                }
            },
            callback: function (response) {
                if (response === true) {
                    $.getJSON($anchor.data("href"), function(data) {
                        if (data.response.status == "200") {
                            $anchor.closest("tr").fadeOut(1200, function() {
                                $(this).remove();
                                $alertEl.removeClass().find("span").empty();
                                $alertEl.addClass("alert alert-"+data.response.class).find("span").html(data.response.msg);
                                $alertEl.show();
                            });
                        } else {
                            $alertEl.removeClass().find("span").empty();
                            $alertEl.addClass("alert alert-"+data.response.class).find("span").html(data.response.msg);
                            $alertEl.show();
                        }
                    });
                }
            }
        });
    });

})
