@extends('admin.layout.master')

@push('styles')

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
@endpush
<style>
    #sortableCategories li {
    cursor: move; /* Change cursor to indicate draggable items */
    padding: 10px;
    margin: 5px 0;
    background-color: #f1f1f1;
    border: 1px solid #ccc;
    border-radius: 5px;
}

#sortableCategories li:hover {
    background-color: #e0e0e0;
}

#sortableCategories {
    list-style-type: none;
    padding: 0;
}
</style>
@section('content')

@include('admin.layout.response_message')

<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <h1 class="page-title fw-semibold fs-18 mb-0">Footer Sub Categories</h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Footer Sub Categories</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card p-4">
    <h4>Manage Footer Category Priority</h4>
    <form id="priorityForm">
        @csrf
        <ul id="sortableCategories">
            @foreach($subCategories as $category)
                <li class="list-group-item" data-id="{{ $category->id }}">
                    {{ $category->title }}
                </li>
            @endforeach
        </ul>

        <button type="submit" class="btn btn-success mt-3">Update Priority</button>
    </form>
</div>
<!-- For drag and drop scripts -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
@endsection

<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        let pathSegments = window.location.pathname.split('/');
        let dep_id = pathSegments[pathSegments.length - 1];
        if (window.location.pathname === "/admin/footer-sub-category/manage-priority/"+dep_id) {
            $("#sortableCategories").sortable();
            $("#sortableCategories").disableSelection();

            // let pathSegments = window.location.pathname.split('/');
            // let dep_id = pathSegments[pathSegments.length - 1];

            $('#priorityForm').submit(function(e) {
                e.preventDefault();
                let order = [];

                $('#sortableCategories li').each(function() {
                    order.push($(this).data('id'));
                });

                $.ajax({
                    url: "{{ route('admin-footer-sub-category.priority.update') }}",
                    method: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order,
                        dep_id: dep_id
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            alert(res.message);
                            window.location.href = "{{ route('admin-footer-subcategory.index') }}/"+ dep_id;
                        } else {
                            alert(res.message || 'Something went wrong');
                        }
                    },
                    error: function(xhr) {
                        alert('Server error: ' + xhr.responseText);
                    }
                });
            });
        }
    });

</script>