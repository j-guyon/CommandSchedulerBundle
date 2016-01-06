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
                        searchVal = $(this).val(),
                        regexp = false;

                    // multiple selections
                    if (typeof searchVal == 'object') {
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
    };
