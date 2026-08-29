<?php
$createPermission = functionCheckPermission('BannerController@create');
$viewPermission = functionCheckPermission('BannerController@view');
$deletePermission = functionCheckPermission('BannerController@delete');
$statusPermission = functionCheckPermission('BannerController@changeStatus');

?>
@if ($results->isNotEmpty())
    @forelse($results as $result)
        <tr class="list-data-row" data-total-count="{{ $totalResults }}">
            <td>
                @if (!empty($result->image) && $result->type != 'video')
                    <a class="fancybox-buttons" data-fancybox-group="button"
                        href="{{ isset($result->image) ? $result->image : '' }}"><img height="50" width="50"
                            src="{{ isset($result->image) ? $result->image : '' }}" /></a>
                @elseif (!empty($result->video))
                    <a class="fancybox-buttons" data-fancybox-group="button"
                        href="{{ isset($result->video) ? $result->video : '' }}">
                        <video height="70" controls>
                            <source src="{{ isset($result->video) ? $result->video : '' }}" type="video/mp4">
                        </video>
                    </a>
                @endif
            </td>

            <td>{{ $result->type_name ?? 'N/A' }}</td>
            <td>{{ $result->title ?? 'N/A' }}</td>
            <td>
                <label class="switch">
                    <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}"
                        data-field="show_on_home_slider" {{ $result->show_on_home_slider ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </td>

            <td>
                <label class="switch">
                    <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}"
                        data-field="show_on_home_banner" {{ $result->show_on_home_banner ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </td>

            <td>
                <label class="switch">
                    <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}"
                        data-field="show_on_home_offer_banner"
                        {{ $result->show_on_home_offer_banner ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </td>
            <td>
                <label class="switch">
                    <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}"
                        data-field="button_active" {{ $result->button_active ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </td>

            <td>
                @if ($result->type_name == 'Video')
                    <label class="switch">
                        <input type="checkbox" class="toggle-status" data-id="{{ $result->id }}"
                            data-field="video_auto_play" {{ $result->video_auto_play ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                @else
                    --
                @endif
            </td>
            <td>{{ date('Y-m-d', strtotime($result->created_at)) }}</td>

            <td>
                @if ($result->is_active == 1)
                    <span class="badge bg-success">Activated</span>
                @else
                    <span class="badge bg-danger">Deactivated</span>
                @endif
            </td>

            <td>
                <div class="hstack gap-2 flex-wrap">
                    @if ($statusPermission == 1)
                        @if ($result->is_active == 1)
                            <a href='{{ route('admin-Banner.status', [$result->id, 0]) }}' class="btn btn-danger"
                                id="deactivate-button"><i class="ri-close-line"></i></a>
                        @else
                            <a href='{{ route('admin-Banner.status', [$result->id, 1]) }}' class="btn btn-success"
                                id="activate-button"><i class="ri-check-line"></i></a>
                        @endif
                    @endif
                    @if ($viewPermission == 1)
                        {{-- <a href="{{route('admin-Banner.show',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-eye-line"></i></a> --}}
                    @endif

                    <a href="{{ route('admin-Banner.edit', base64_encode($result->id)) }}" class="btn btn-info"><i
                            class="ri-edit-line"></i></a>

                    @if ($deletePermission == 1)
                        <form method="GET" action="{{ route('admin-Banner.delete', base64_encode($result->id)) }}">
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
