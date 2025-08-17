// Renders all queued attendance charts pushed by the widget (jQuery version)
(function ($) {
    function renderCharts() {
        if (!window.MDW_ATTENDANCE || !window.Chart) return;

        $.each(window.MDW_ATTENDANCE, function (i, cfg) {
            var $el = $("#" + cfg.id);
            if ($el.length === 0) return;

            new Chart($el[0].getContext("2d"), {
                type: "doughnut",
                data: {
                    labels: ["Present", "Absent"],
                    datasets: [{
                        data: [cfg.data.present, cfg.data.absent],
                        backgroundColor: ["#10B981", "#EF4444"], // green + red
                        borderColor: ["#ffffff", "#ffffff"],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    cutout: "70%", // thinner ring for modern look
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                font: {
                                    family: "Inter, sans-serif",
                                    size: 13,
                                    weight: "bold"
                                },
                                color: "#374151"
                            }
                        },
                        tooltip: {
                            backgroundColor: "#111827",
                            titleColor: "#F9FAFB",
                            bodyColor: "#D1D5DB",
                            callbacks: {
                                label: function (ctx) {
                                    return ctx.label + ": " + ctx.raw + "%";
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });

        // Clear queue after rendering
        window.MDW_ATTENDANCE = [];
    }

    // jQuery DOM ready
    $(document).ready(function () {
        renderCharts();
    });
})(jQuery);
