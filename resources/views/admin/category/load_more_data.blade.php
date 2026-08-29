@if ($results->isNotEmpty())
    @forelse($results as $result)
        <tr class="list-data-row items-inner" data-total-count="{{ $totalResults }}" data-id = "{{ $result->id }}">
            <td>
                @if (!empty($result->image))
                    <img height="50" width="50" class="cat-img" src="{{ env('WEBSITE_URL') . '/uploads/categories/' . $result->image }}" />
                @endif
            </td>

            <td>{{ $result->name ?? 'N/A' }} ({{ $result->category_type_id == 1 ? 'Collection' : 'Category' }})</td>
            <td>
                <label class="switch">
                    <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}" data-field="show_on_menu"
                        {{ $result->show_on_menu ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </td>
            <td>
                <label class="switch">
                    <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}" data-field="show_on_home"
                        {{ $result->show_on_home ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </td>
            <td>
                <label class="switch">
                    <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}" data-field="whats_new"
                        {{ $result->whats_new ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </td>
            <td>
                <label class="switch">
                    <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}" data-field="shop_by_purpose"
                        {{ $result->shop_by_purpose ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </td>
            <td>
                <label class="switch">
                    <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}" data-field="explore_by_category"
                        {{ $result->explore_by_category ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </td>
            <td>{{ date('Y-m-d', strtotime($result->created_at)) }}</td>

            @php
                $pageTitle = request()->type != 'sub-category' ? 'sub-category' : 'child-category';
            @endphp

            <td>
                @if ($result->is_active == 1)
                    <span class="badge bg-success">Activated</span>
                @else
                    <span class="badge bg-danger">Deactivated</span>
                @endif
            </td>

            <td>
                {{ $result->getUser->name }}
            </td>
            <td>
                <div class="hstack gap-2 flex-wrap">
                    @php
                        $editPermission = 'edit_category';
                        $viewPermission = 'view_sub_category';
                        $deletePermission = 'delete_category';
                        if (request()->type == 'sub-category') {
                            $editPermission = 'edit_sub_category';
                            $viewPermission = 'view_child_category';
                            $deletePermission = 'delete_sub_category';
                        } elseif (request()->type == 'child-category') {
                            $editPermission = 'edit_child_category';
                            $deletePermission = 'delete_child_category';
                        }
                    @endphp
                    @can($editPermission)
                        @if ($result->is_active == 1)
                            <a href='{{ route('admin-category.status', [$result->id, 0]) }}' class="btn btn-danger"
                                id="deactivate-button"><i class="ri-close-line"></i></a>
                        @else
                            <a href='{{ route('admin-category.status', [$result->id, 1]) }}' class="btn btn-success"
                                id="activate-button"><i class="ri-check-line"></i></a>
                        @endif
                        <a href="{{ route('admin-category.edit', base64_encode($result->id)) }}?endesid={{ request()->endesid }}&type={{ request()->type }}"
                            class="btn btn-info">
                            <i class="ri-edit-line"></i>
                        </a>
                    @endcan

                    @can($deletePermission)
                        <form method="GET" action="{{ route('admin-category.delete', base64_encode($result->id)) }}">
                            @csrf
                            <input name="_method" type="hidden" value="DELETE">
                            <button type="submit" class="btn btn-danger" id="confirm-button"><i
                                    class="ri-delete-bin-5-line"></i></button>
                        </form>
                    @endcan


                    @can($viewPermission)
                        @if ($result->category_type_id != 1 && request()->type != 'child-category')
                            <div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-light" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="ri-list-check"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link"
                                                href="{{ route('admin-category.index', ['endesid' => base64_encode($result->id), 'type' => $pageTitle]) }}">
                                                <span class="nav-text">Sub {{ $pageTitle }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    @endcan
                </div>
            </td>
        </tr>
    @empty
    @endforelse
@else
    <tr class="noresults-row">
        <td colspan="7" style="text-align: center;">No results found.</td>
    </tr>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<script type="text/javascript">
    new Sortable(powerwidgets, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function(evt) {
            var counter = 1;
            var requestData = [];
            $(".items-inner").each(function() {
                requestData.push({
                    "id": $(this).attr("data-id"),
                    "order": counter
                });
                counter++;
            });

            $.ajax({
                url: '{{ Route('admin-category.updateCategoryOrder') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "requestData": requestData
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success",
                        text: "Category Order updated successfully!",
                        icon: "success"
                    });
                }
            });
        },
    });
</script>
