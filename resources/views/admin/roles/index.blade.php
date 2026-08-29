@extends('admin.layout.master')

@section('content')

    @include('admin.layout.response_message')


    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Roles</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Roles</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Roles
                    </div>
                    @can('create_role')
                    <div class="prism-toggle">
                        <a href='{{ route('admin-roles.create') }}' class="btn btn-primary mb-3" style="float: right">Add
                            Role</a>
                    </div>
                    @endcan
                </div>
                <div class="card-body">
                    <table id="datatable-basic" class="table table-bordered text-nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sr.</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @forelse($roles as $result)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $result->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="status-toggle {{ $result->status == 1 ? 'text-success' : 'text-danger' }}" 
                                            data-id="{{ $result->id }}" 
                                            style="cursor:pointer;">
                                            {{ $result->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="hstack gap-2 flex-wrap">
                                            @can('edit_role')
                                                <a href="{{ route('admin-roles.edit', base64_encode($result->id)) }}"
                                                    class="btn btn-info"><i class="ri-edit-line"></i></a>
                                            @endcan

                                            @can('delete_role')
                                                @if (auth()->user()->hasRole(['Super Admin', 'Admin']))
                                                    @if ($result->name != 'Super Admin' && $result->name != 'Admin')
                                                        <form method="POST" action="">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" id="confirm-button">
                                                                <i class="ri-delete-bin-5-line"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endcan

                                        </div>
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Datatables Cdn -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Internal Datatables JS -->
    <script src="{{ asset('assets/js/datatables.js') }}"></script>

    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>

    <script>
        $(document).on('click', '.status-toggle', function() {
            let element = $(this);
            let id = element.data('id');
            $.ajax({
                url: 'status/' + id + '/toggle-status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status == 1) {
                        element.removeClass('text-danger').addClass('text-success').text('Active');
                    } else {
                        element.removeClass('text-success').addClass('text-danger').text('Inactive');
                    }
                }
            });
        });
  </script>

@endpush
