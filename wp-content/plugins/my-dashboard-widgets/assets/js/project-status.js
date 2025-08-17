(function ($) {
    function renderProjectCharts() {
        if (!window.MDW_PROJECT || !window.Chart) return;

        $.each(window.MDW_PROJECT, function (i, cfg) {
            var $el = $("#" + cfg.id);
            if ($el.length === 0) return;

            var labels = Object.keys(cfg.data);
            var values = Object.values(cfg.data);
            var total  = values.reduce((a, b) => a + b, 0);

            new Chart($el[0].getContext("2d"), {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: ["#3498db", "#2ecc71", "#f39c12"],
                        borderColor: ["#ffffff", "#ffffff", "#ffffff"],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    cutout: "70%",
                    plugins: {
                        legend: { display: false},
                        tooltip: {
                            backgroundColor: "#111827",
                            titleColor: "#F9FAFB",
                            bodyColor: "#D1D5DB",
                            animation: { duration: 200 }, // smooth fade-in/out
                            boxPadding: 6,                // prevent tooltip resize jumps
                            callbacks: {
                                label: function(ctx) {
                                    let val = ctx.raw;
                                    let percent = total ? ((val / total) * 100).toFixed(1) : 0;
                                    return `${ctx.label}: ${percent}%`; // shorter text (like attendance)
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

        window.MDW_PROJECT = [];
    }

    $(document).ready(renderProjectCharts);
})(jQuery);
