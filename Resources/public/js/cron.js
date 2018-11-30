/**
 * CronHelper provides the logic for the Cronhelper modal
 *
 * @type {CronHelper|*|{initCronHelper: Function, changeCronExpression: Function, presetCronHelper: Function, handleCronValue: Function, buildCronExpression: Function, handleVal: Function}}
 *
 * @namespace CronHelper
 */
var CronHelper = CronHelper || {

        /**
         * initialize Cronhelper
         */
        initCronHelper: function () {
            var $cronField = $('#scheduled_command_cronExpression'),
                oldExpression = $cronField.val();

            $('body').on('click', '#cronHelperLink', function (e) {
                e.preventDefault();
                $('#cron_expression').val(oldExpression);
                if (!oldExpression.match(/@.+/)) {
                    CronHelper.presetCronHelper(oldExpression);
                }
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
            $('#cronhelper')
                .on('change', '.cron_expression', function (e) {
                    CronHelper.changeCronExpression($(this));
                });
        },

        /**
         * change cron expression after change/select event
         *
         * @param $this object jQuery object of input field
         */
        changeCronExpression: function ($this) {
            var $selector = $('.' + $this.data('class')),
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
            CronHelper.buildCronExpression()
        },

        /**
         * preset selection based on the existing cron expression
         *
         * @param expression string cron expression
         */
        presetCronHelper: function (expression) {
            var values = expression.split(' ');

            CronHelper.handleCronValue(values[0], 'cron_minute');
            CronHelper.handleCronValue(values[1], 'cron_hour');
            CronHelper.handleCronValue(values[2], 'cron_day');
            CronHelper.handleCronValue(values[3], 'cron_month');
            CronHelper.handleCronValue(values[4], 'cron_week');
        },


        /**
         * preselect values from existing crontab subentry for given field
         *
         * @param value string cronvalue
         * @param field string fieldname
         */
        handleCronValue: function (value, field) {
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
        },

        /**
         * generate new cron expression from input fields
         */
        buildCronExpression: function () {
            var expression = [],
                fields = ['minute', 'hour', 'day', 'month', 'week'];

            // build expression values
            for(var i in fields) {
                var field = fields[i],
                    val = $('.cron_' + field + ':visible').val();

                expression.push(CronHelper.handleVal(val));
            }

            $('#cron_expression').val(expression.join(' '));
        },

        /**
         * generate textual representation of a cron entry from selected values
         *
         * @param value string|array selected values
         */
        handleVal: function (value) {
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
    };
