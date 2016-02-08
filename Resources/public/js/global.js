
// Enable bootstrap-confirmation, initialize other stuff...
$(document).ready(function () {
    //$('[data-toggle="confirmation"]').confirmation();
    $('[data-toggle="confirmation"]').confirmation({
        singleton: true,
        popout: true,
        placement: 'left'
    });

    $('.hasTooltip').tooltip();

    // edit commands
    if (document.getElementById('cronhelper')) {
        CronHelper.initCronHelper();
    }

    // execution log
    if (document.getElementById('runtimeGraph')) {
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(Graph.initGraphs);
        Execution.initOutputLinks();
    }

    if (document.getElementById('dataTable')) {
        Table.initDataTable();
    }
});
