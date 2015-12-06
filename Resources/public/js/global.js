/**
 * Created by daniel on 04.12.15.
 */

// Enable bootstrap-confirmation
$(document).ready(function () {
    //$('[data-toggle="confirmation"]').confirmation();
    $('[data-toggle="confirmation"]').confirmation({
        singleton: true,
        popout: true,
        placement: 'left'
    });

    $('.hasTooltip').tooltip();

    if (document.getElementById('runChartHolder')) {
        google.load("visualization", "1", {packages: ["corechart"]});
        google.setOnLoadCallback(initRuntimeGraph);
    }

    if (document.getElementById('cronhelper')) {
        initCronHelper();
    }
});

/**
 * initialize Cronhelper
 */
function initCronHelp() {
    var $cronField = $('#command_scheduler_detail_cronExpression'),
        oldExpression = $cronField.val();

    $('#cronhelper').on('select', '.cron_toggle', function (e) {
        var $this = $(this),
            selector = '.' + $this.attr('id');

        $(selector).toggleClass('hide');
        $(selector + '.hide').select('*');
    });
}

/**
 * initialize runtime graph
 */
function initRuntimeGraph() {
    var runtimeData = new Array(),
        graphData = null,
        graphOptions = {
            title: 'Runtime',
            hAxis: {title: 'ExecutionDate', titleTextStyle: {color: '#333'}},
            vAxis: {minValue: 0}
        },
        chart = null;

    runtimeData.push(['Execution', 'Runtime', 'Return Code']);
    graphData = google.visualization.arrayToDataTable(runtimeData);

    //chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
    //chart.draw(graphData, options);
}
