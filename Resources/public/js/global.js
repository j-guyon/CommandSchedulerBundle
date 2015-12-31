/**
 * Created by Daniel Fischer on 04.12.15.
 */

// Enable bootstrap-confirmation, initialize other stuff...
$(document).ready(function () {
    //$('[data-toggle="confirmation"]').confirmation();
    $('[data-toggle="confirmation"]').confirmation({
        singleton: true,
        popout: true,
        placement: 'left'
    });

    $('.hasTooltip').tooltip();

    if (document.getElementById('runtimeGraph')) {
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(initGraphs);
    }

    if (document.getElementById('cronhelper')) {
        initCronHelper();
    }

    if (document.getElementById('dataTable')) {
        initDataTable();
    }
});

/**
 * initialize Cronhelper
 */
function initCronHelper() {
    var $cronField = $('#scheduled_command_cronExpression'),
        oldExpression = $cronField.val();

    $('body').on('click', '#cronHelperLink', function (e) {
        e.preventDefault();
        $('#cron_expression').val(oldExpression);
        presetCronHelper(oldExpression);
        $('#cronhelper').modal();
    });

    // confirmation for save
    $('#cronhelper_save').confirmation({
        singleton: true,
        popout: true,
        placement: 'left',
        onConfirm: function (event) {
            $cronField.val($('#cron_expression').val());
            $('#cronhelper').modal('hide');
        }
    });

    // change selection
    $('#cronhelper').on('change', '.cron_toggle', function (e) {
        var $this = $(this),
            $selector = $('.' + $this.data('class')),
            selection = $this.val(),
            isModulo = false;

        // empty selection is not allowed, select *
        if ((selection == null) || (selection.length == 0)) {
            $this.val('*');
        }

        // if modulo is selected unselect all values and toggle input
        var len = selection.length;
        for (var i = 0; i < len; i++) {
            isModulo = isModulo || (selection[i] == '-');
        }

        if (isModulo) {
            $this.val('-');
            $selector.toggleClass('hide');
            // select 'wildcard' option for visible input
            $('.' + $this.data('class') + ':visible').val('*');
        }

        // update current cron expression
        buildCronExpression()
    });
}

/**
 * preset selection based on the existing cron expression
 */
function presetCronHelper(expression) {
    var values = expression.split(' ');

    handleCronValue(values[0], 'cron_minute');
    handleCronValue(values[1], 'cron_hour');
    handleCronValue(values[2], 'cron_day');
    handleCronValue(values[3], 'cron_month');
    handleCronValue(values[4], 'cron_week');
}

/**
 * preselect values from existing crontab subentry for given field
 *
 * @param value string cronvalue
 * @param field string fieldname
 */
function handleCronValue(value, field) {
    var classSel = '.' + field,
        idSel = '#' + field;

    if (value.match(/\*\/[0-9]+/)) {
        $(classSel).toggleClass('hide');
        $(idSel + '_modulo').val(value);
    } else if (value == '*') {
        $(classSel).val('*');
    } else {
        var values = value.split(','),
            result = [];

        for (var i = 0; i < values.length; i++) {
            if (values[i].match(/[0-9]+\-[0-9]+/)) {
                var vals = values[i].split('-')
                for (var j = vals[0]; j <= vals[1]; j++) {
                    result.push(j.toString());
                }
            } else {
                result.push(values[i].toString());
            }
        }

        $(classSel).val(result);
    }
}

/**
 * generate new cron expression from input fields
 */
function buildCronExpression() {
    var expression = '';

    expression += handleVal($('.cron_minute:visible').val()) + " ";
    expression += handleVal($('.cron_hour:visible').val()) + " ";
    expression += handleVal($('.cron_day:visible').val()) + " ";
    expression += handleVal($('.cron_month:visible').val()) + " ";
    expression += handleVal($('.cron_week:visible').val());

    $('#cron_expression').val(expression);
}

/**
 * generate textual representation of a cron entry from selected values
 *
 * @param value string|array selected values
 */
function handleVal(value) {
    // we have a simple value - nothing do do, return
    if (typeof value == 'string') {
        return value;
    }

    // now comes the fun part - handle complex selections
    var len = value.length,
        wildcard = false,
        result = [],
        limit = -1,
        j = 0,
        help = false;

    // check if there is a wildcard ('*' or '-') selected - '-' should not happend, handle as '*'
    for (var i = 0; (i < len) && !wildcard; i++) {
        // try to convert entry to integer
        if (!isNaN(help = parseInt(value[i]))) {
            value[i] = help;
        }

        // check for wildcard
        if ((value[i] == '*') || (value[i] == '-')) {
            wildcard = true;
            continue;
        }

        // init search for successing values
        limit = value[i];

        // search for successing values
        for (j = i + 1; (j < len) && (value[j] == (limit + 1)); j++) {
            // try to convert entry to integer
            if (!isNaN(help = parseInt(value[j]))) {
                value[j] = help;
            }

            limit = value[j];
        }

        // difference detected -> add range
        if (limit > value[i]) {
            result.push(value[i] + '-' + limit);
            i = j - 1;
        } else { // add single value
            result.push(value[i]);
        }
    }

    // wildcard detected
    if (wildcard) {
        result = ['*'];
    }

    return result.join(',');
}

/**
 * initialize dataTable (list of all executions)
 */
function initDataTable() {
    var $table = $('#dataTable'),
        dataTable,
        colIdx = 0;

    dataTable = $table.DataTable({
        ordering: true,
        columnDefs: [
            {
                targets: 3,
                orderable: false
            }
        ],
        order: [
            [1, "asc"]
        ]
    });

    buildTableFilter(dataTable, 'commandfilter', 0);
    buildTableFilter(dataTable, 'datefilter', 1);
}

/**
 * build filteroptions for table filter
 *
 * @param {object} dataTable datatable object
 * @param {string} selector id of select field
 * @param {int} colIdx number of tablecolumn
 */
function buildTableFilter(dataTable, selector, colIdx) {
    // Create the select list and search operation
    var $select = $('#' + selector)
        .on('change', function () {
            var col = dataTable.column(colIdx),
                searchVal = $(this).val(),
                regexp = false;

            // multiple selections
            if(typeof searchVal == 'object') {
                searchVal = searchVal.join('|');
                regexp = true;
            }

            // filter column
            col
                .search(searchVal, regexp, false) // regexp optional, no smart search
                .draw(); // refresh table
        });

    // Get the search data for the first column and add to the select list
    dataTable
        .column(colIdx)
        .cache('search')
        .sort()
        .unique()
        .each(function (d) {
            $select.append($('<option value="' + d + '">' + d + '</option>'));
        });
}
/**
 * initialize runtime and returncode graph
 */
function initGraphs() {
    // if there are no executions do nothing
    if (executionData.length == 0) {
        $('.toggleGraph').hide();
        return;
    }

    var returnData = {},
        runtimeData = [],
        avgRuntime = 0,
        help = null;

    // prepare dataset
    for (var i in executionData) {
        help = executionData[i];
        if (returnData.hasOwnProperty(help.returnCode)) {
            returnData[help.returnCode][1]++;
        } else {
            returnData[help.returnCode] = [help.returnCode, 1];
        }

        runtimeData.push([
            help.executionDate,
            help.runtime
        ]);

        avgRuntime += help.runtime;
    }

    avgRuntime /= executionData.length;

    // init graphs
    initReturnGraph(returnData);
    initRuntimeGraph(runtimeData, avgRuntime);
}

/**
 * render runtime statistics as line graph with trendline
 *
 * @param data array runtime data
 * @param avgRuntime double average runtime
 */
function initRuntimeGraph(data, avgRuntime) {
    var runtimeData = [[js_lang.execution_date, js_lang.runtime, js_lang.avg_runtime, js_lang.exp_runtime]],
        runtimeGraphData,
        runtimeGraphOptions = {
            title: js_lang.title_runtime,
            textStyle: {
                bold: true,
                fontSize: 10,
                color: '#4d4d4d'
            },
            titleTextStyle: {
                bold: true,
                fontSize: 14,
                color: '#4d4d4d'
            },
            hAxis: {
                title: js_lang.execution_date,
                minValue: 0
            },
            vAxis: {
                title: js_lang.runtime + '/s',
                minValue: 0
            },
            animation: {
                duration: 750,
                easing: 'linear',
                startup: true
            },
            legend: {
                position: 'bottom'
            },
            //curveType: 'function',
            lineWidth: 1,
            pointSize: 2
        },
        runtimeChart;

    var len = data.length,
        average = 0,
        avglen = 5; // number of values for moving average

    // convert data
    for (var i = 0; i < len; i++) {
        average = 0;

        // first entry has no average - set value
        if (i == 0) {
            average = data[i][1];
        } else {
            // calculate moving average
            for (var j = i; (j >= 0) && (j > (i - avglen)); j--) {
                average += data[j][1];
            }
            average /= ((i >= avglen) ? avglen : (i + 1));
        }

        runtimeData.push([data[i][0].date, data[i][1], average, expectedRuntime]);
    }

    // that's it, render graph
    runtimeGraphData = google.visualization.arrayToDataTable(runtimeData);

    runtimeChart = new google.visualization.LineChart(document.getElementById('runtimeGraph'));
    runtimeChart.draw(runtimeGraphData, runtimeGraphOptions);
}

/**
 * render returncode statistics as bargraph
 *
 * @param data object statistical data
 */
function initReturnGraph(data) {
    var returnData = [[js_lang.return_code, js_lang.number]],
        returnGraphData,
        returnGraphOptions = {
            title: js_lang.title_return,
            textStyle: {
                bold: true,
                fontSize: 10,
                color: '#4d4d4d'
            },
            titleTextStyle: {
                bold: true,
                fontSize: 14,
                color: '#4d4d4d'
            },
            hAxis: {
                title: js_lang.return_code,
                minValue: 0,
                gridlines: {
                    count: 0
                },
                minorGridlines: {
                    count: 0
                },
                ticks: []
            },
            vAxis: {
                title: js_lang.number
            },
            animation: {
                duration: 750,
                easing: 'linear',
                startup: true
            },
            legend: {position: 'none'}
        },
        returnChart;

    // convert data to array
    for (var i in data) {
        returnData.push(data[i]);
    }

    // that's it, render graph
    returnGraphData = google.visualization.arrayToDataTable(returnData);

    returnChart = new google.visualization.ColumnChart(document.getElementById('returnGraph'));
    returnChart.draw(returnGraphData, returnGraphOptions);
}
