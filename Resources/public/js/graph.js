/**
 * Graph provides function to display the return-code graph and the runtime graph
 *
 * @type {Graph|*|{initGraphs: Function, initRuntimeGraph: Function, initReturnGraph: Function, runtimeGraphOptions: {title: *, textStyle: {bold: boolean, fontSize: number, color: string}, titleTextStyle: {bold: boolean, fontSize: number, color: string}, hAxis: {title: *, minValue: number}, vAxis: {title: string, minValue: number}, animation: {duration: number, easing: string, startup: boolean}, legend: {position: string}, lineWidth: number, pointSize: number}, returnGraphOptions: {title: *, textStyle: {bold: boolean, fontSize: number, color: string}, titleTextStyle: {bold: boolean, fontSize: number, color: string}, hAxis: {title: *, minValue: number, gridlines: {count: number}, minorGridlines: {count: number}, ticks: Array}, vAxis: {title: (DataTable.render.number|.render.number)}, animation: {duration: number, easing: string, startup: boolean}, legend: {position: string}}}}
 */
var Graph = Graph || {

        /**
         * initialize runtime and returncode graph
         */
        initGraphs: function () {
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
            Graph.initReturnGraph(returnData);
            Graph.initRuntimeGraph(runtimeData, avgRuntime);
        },

        /**
         * render runtime statistics as line graph with trendline
         *
         * @param data array runtime data
         * @param avgRuntime double average runtime
         */
        initRuntimeGraph: function (data, avgRuntime) {
            var runtimeData = [[js_lang.execution_date, js_lang.runtime, js_lang.avg_runtime, js_lang.exp_runtime]],
                runtimeGraphData,
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
            runtimeChart.draw(runtimeGraphData, Graph.runtimeGraphOptions);
        },

        /**
         * render returncode statistics as bargraph
         *
         * @param data object statistical data
         */
        initReturnGraph: function (data) {
            var returnData = [[js_lang.return_code, js_lang.number]],
                returnGraphData,
                returnChart;

            // convert data to array
            for (var i in data) {
                returnData.push(data[i]);
            }

            // that's it, render graph
            returnGraphData = google.visualization.arrayToDataTable(returnData);

            returnChart = new google.visualization.ColumnChart(document.getElementById('returnGraph'));
            returnChart.draw(returnGraphData, Graph.returnGraphOptions);
        },

        /** options for runtime graph */
        runtimeGraphOptions: {
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

        /** options for return code graph */
        returnGraphOptions: {
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
        }

    };
