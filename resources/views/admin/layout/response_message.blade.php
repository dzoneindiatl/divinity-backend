@if (session()->has('success'))
<script type="text/javascript">
function massge() {
    let message = "{{ session()->get('success') }}";
    Swal.fire({
        icon: 'success',
        title: message,
        showConfirmButton: true,
    })
}

window.onload = massge;
</script>
@endif
@if (session()->has('flash_notice'))
<script type="text/javascript">
function massge() {
    let message = "{{ session()->get('flash_notice') }}";
    Swal.fire({
        icon: 'success',
        title: message,
        showConfirmButton: true,
    })
}

window.onload = massge;
</script>
@endif

@if (session()->has('error'))
<script type="text/javascript">
function massge() {
    let message = "{{ session()->get('error') }}";
    Swal.fire({
        icon: 'error',
        title: message,
        showConfirmButton: true,
    })
}

window.onload = massge;
</script>
@endif
<script>
function show_message(message, message_type) {
    if (message_type) {

        Swal.fire({
            icon: message_type,
            title: message,
            showConfirmButton: true,
        })
    }

}
</script>