/**
 * Table handles DataTable calls as well as sorting and filtering functions
 *
 * @type {Table|*|{initDataTable: Function, buildTableFilter: Function}}
 */
var Table = Table || {
        /**
         * initialize dataTable (list of all executions)
         */
        initDataTable: function () {
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

            Table.buildTableFilter(dataTable, 'commandfilter', 0);
            Table.buildTableFilter(dataTable, 'datefilter', 1);
            Table.cleanDateFilter();
        },

        /**
         * build filteroptions for table filter
         *
         * @param {object} dataTable datatable object
         * @param {string} selector id of select field
         * @param {int} colIdx number of tablecolumn
         */
        buildTableFilter: function (dataTable, selector, colIdx) {
            // Create the select list and search operation
            var $select = $('#' + selector)
                .on('change', function () {
                    var col = dataTable.column(colIdx),
                        searchVal = $(this).val();

                    // multiple selections
                    if (typeof searchVal == 'object') {
                        searchVal = searchVal.join('|');
                    }

                    // filter column
                    col
                        .search(searchVal, true, false) // regexp optional, no smart search
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
        },

        /**
         * filter for dates only (remove time from select field)
         */
        cleanDateFilter: function () {
            var $select = $('#datefilter'),
                $options = $('option', $select),
                dates = [];

            $options.each(function (idx, elem) {
                // skip first entry
                if(!idx) {
                    return;
                }

                var $this = $(elem),
                    date = $this.val().replace(/([^\s]+).*/, "$1"); // remove time

                if (dates.indexOf(date) != -1) {
                    $this.remove(); // remove entry, date already existing
                } else {
                    $this
                        .val(date + '.*') // make regexp
                        .html(date); // change label
                    dates.push(date); // make sure each date only exists once
                }
            });
        }
    };
