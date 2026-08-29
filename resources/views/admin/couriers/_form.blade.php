<form
    action="{{ isset($country) ? route('admin-couriers.update', base64_encode($country->id)) : route('admin-couriers.store') }}"
    method="POST" enctype="multipart/form-data" id="countryForm">
    @csrf
    @if (isset($country))
        @method('PUT')
    @endif

    <div class="card custom-card">
        <div class="card-header">
            <div class="card-title">Basic Info</div>
        </div>
        <div class="card-body add-products p-0">
            <div class="p-4">
                <div class="row gx-5">
                    <div class="row gy-3">
                        <div class="col-xl-6">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" required
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $country->name ?? '') }}" placeholder="Name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-xl-6">
                            <label for="tracking_url" class="form-label">Tracking Url</label>
                            <input type="text" name="tracking_url"required
                                class="form-control @error('tracking_url') is-invalid @enderror"
                                value="{{ old('tracking_url', $country->tracking_url ?? '') }}" placeholder="Tracking Url">
                            @error('tracking_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        
                    </div>


                    <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                        <button type="submit"
                            class="btn btn-primary">{{ isset($country) ? 'Update' : 'Submit' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
