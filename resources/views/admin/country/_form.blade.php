<form
    action="{{ isset($country) ? route('admin-country.update', base64_encode($country->id)) : route('admin-country.store') }}"
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
                            <label for="name" class="form-label">Country Name</label>
                            <input type="text" name="name" required
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $country->name ?? '') }}" placeholder="Country Name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-xl-6">
                            <label for="code" class="form-label">Country Code</label>
                            <input type="text" name="code"required
                                class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('code', $country->code ?? '') }}" placeholder="Country Code">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row gy-3 mt-2">
                        <div class="col-xl-6">
                            <label for="country_flag" class="form-label">Country Flag</label>
                            <input type="file" accept="image/*" name="country_flag"required
                                class="form-control @error('country_flag') is-invalid @enderror">
                            @if (!empty($country->country_flag))
                                <img height="50" width="50" src="{{ asset($country->country_flag) }}" />
                            @endif
                            @error('country_flag')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-xl-6">
                            <label for="currency_amount" class="form-label">Currency Amount</label>
                            <input type="text" name="currency_amount"required
                                class="form-control @error('currency_amount') is-invalid @enderror"
                                value="{{ old('currency_amount', $country->currency_amount ?? '') }}">
                            @error('currency_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row gy-3 mt-2">
                        <div class="col-xl-6">
                            <label for="sortname" class="form-label">Short Name</label>
                            <input type="text" name="sortname"required
                                class="form-control @error('sortname') is-invalid @enderror"
                                value="{{ old('sortname', $country->sortname ?? '') }}">
                            @error('sortname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-xl-6">
                            <label for="currency_symbol" class="form-label">Currency Symbol</label>
                            <input type="text" name="currency_symbol"required
                                class="form-control @error('currency_symbol') is-invalid @enderror"
                                value="{{ old('currency_symbol', $country->currency_symbol ?? '') }}">
                            @error('currency_symbol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row gy-3 mt-2">
                        <div class="col-xl-6">
                            <label for="country_time_zone" class="form-label">Country Time Zone</label>
                            <input type="text" name="country_time_zone"required
                                class="form-control @error('country_time_zone') is-invalid @enderror"
                                value="{{ old('country_time_zone', $country->country_time_zone ?? '') }}">
                            @error('country_time_zone')
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
