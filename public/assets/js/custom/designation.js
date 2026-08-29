$(document).ready(function() {
    if ($(".parent:input").val() == 1) {
        var parentid = $(".parent:input:checked").attr('id');
        $('.child' + parentid).attr('checked', true);
    }
    $(".checkAll").click(function() {
        $(".parent:input").not(this).prop('checked', this.checked);
        $(".children:input").not(this).prop('checked', this.checked);
    });
    $(".parent:input").click(function() {
        var parentid = $(this).attr('id');
        $('.child' + parentid).not(this).prop('checked', this.checked);
    });

    $(".children").click(function() {
        var childid = $(this).attr('id');
        $('#' + childid).not(this).prop('checked', this.checked);
    });
});