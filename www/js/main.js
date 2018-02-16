$(function(){
    $(document).on('click','a.ajax', function (event) {
        event.preventDefault();
        $.get(this.href);
    });

    $(document).on('click','a.confirm', function(event) {
        if (confirm('Opravdu ' + $(this).text() + '?')) {
            this.href += '&confirmed=1';
            return true;
        }
        return false;
    });
});
