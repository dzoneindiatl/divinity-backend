<?php
    $createPermission   = functionCheckPermission("UsersController@create");
    $editPermission   = functionCheckPermission("UsersController@edit");
    $viewPermission     = functionCheckPermission("UsersController@view");
    $deletePermission   = functionCheckPermission("UsersController@delete");
    $statusPermission   = functionCheckPermission("UsersController@changeStatus");

?>
@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">
    <td>
        @if (!empty($result->image))
        <img height="50" width="50" src="{{isset($result->image)? $result->image:''}}" />
        @endif
    </td>

    <td>{{ $result->name ?? "N/A" }}</td>
    <td>
        {{ $result->email ?? "N/A" }}
    </td>
    <td>
        {{ $result->phone_number ?? "N/A" }}
    </td>
    <td>{{ ucfirst($result->gender) ?? "N/A" }}</td>
    <td>
        @if($result->is_active == 1)
        <span class="badge bg-success">Activated</span>
        @else
        <span class="badge bg-danger">Deactivated</span>
        @endif
    </td>

    <td>
        <div class="hstack gap-2 flex-wrap">
            @if($statusPermission == 1)
            @if($result->is_active == 1)
            <a href='{{route("admin-partners.status",array($result->id,0))}}' class="btn btn-danger"
                id="deactivate-button"><i class="ri-close-line"></i></a>
            @else
            <a href='{{route("admin-partners.status",array($result->id,1))}}' class="btn btn-success"
                id="activate-button"><i class="ri-check-line"></i></a>
            @endif

            @endif
            @if($viewPermission == 1)
            <a href="{{route('admin-partners.show',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-eye-line"></i></a>
            @endif
            @if($editPermission == 1)
            <a href="{{route('admin-partners.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>
            @endif
            @if($deletePermission == 1)
            <form method="GET" action="{{route('admin-partners.delete',base64_encode($result->id))}}">
                @csrf
                <input name="_method" type="hidden" value="DELETE">
                <button type="submit" class="btn btn-danger" id="confirm-button"><i
                        class="ri-delete-bin-5-line"></i></button>
            </form>
            @endif

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