/**
 * Created by daniel on 04.12.15.
 */
var initFunction = false;

function initRuntimeGraph() {
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawRuntimeGraph);
}

function drawRuntimeGraph() {
    var graphData = google.visualization.arrayToDataTable([
        ['Execution', 'Runtime', 'Return Code'],
        ['2013',       400],
        ['2014',       460],
        ['2015',         1120],
        ['2016',        540]
    ]);

    var options = {
        title: 'Runtime',
        hAxis: {title: 'ExecutionDate',  titleTextStyle: {color: '#333'}},
        vAxis: {minValue: 0}
    };

    //var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
    //chart.draw(graphData, options);
}
