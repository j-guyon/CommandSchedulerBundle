/**
 * Execution contains logic to show output for a given execution in a modal
 *
 * @type {Execution|*|{initOutputLinks: Function}}
 *
 * @namespace Execution
 */
var Execution = Execution || {
        /**
         * initialize links to show output in modal
         */
        initOutputLinks: function () {
            $('body').on('click', '.openOutput', function (e) {
                e.preventDefault();

                var $this = $(this),
                    url = $this.attr('href');

                $.ajax({
                    url: url,
                    success: function (data) {
                        $('#outputModal').html(data);
                        $('#modal').modal();
                    }
                });
            });
        }
    };
