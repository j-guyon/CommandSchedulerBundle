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
function initCronHelper() {
    var $cronField = $('#command_scheduler_detail_cronExpression'),
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
 * initialize runtime graph
 */
function initRuntimeGraph() {
    var runtimeData = [],
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
