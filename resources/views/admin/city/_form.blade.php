<form action="{{ isset($city) ? route('admin-city.update', [base64_encode($city->id), 'endesid' => request('endesid')]) : route('admin-city.store', ['endesid' => request('endesid')]) }}"
    method="POST" enctype="multipart/form-data" id="cityForm">
    @csrf
    @if (isset($city))
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
                            <label for="name" class="form-label">City Name</label>
                            <input type="text" name="name" required
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $city->name ?? '') }}" placeholder="City Name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-xl-6">
                            <label for="postal_code" class="form-label">City Code</label>
                            <input type="text" name="postal_code"required
                                class="form-control @error('postal_code') is-invalid @enderror"
                                value="{{ old('postal_code', $city->postal_code ?? '') }}" placeholder="City Code">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                       <div class="row gy-3 mt-2">
                        <div class="col-xl-6">
                            <label for="short_name" class="form-label">State Name</label>
                            <select name="state_id" required
                                    class="form-control form-select @error('state_id') is-invalid @enderror">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id', isset($city) ? $city->state_id : ($endesid ?? '')) == $state->id ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach


                                </select>
                                @error('state_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                        </div>
                    </div>



                    <div class="row gy-3 mt-2">
                        <div class="col-xl-6">
                            <label for="short_name" class="form-label">Short Name</label>
                            <input type="text" name="short_name"required
                                class="form-control @error('short_name') is-invalid @enderror"
                                value="{{ old('short_name', $city->short_name ?? '') }}">
                            @error('short_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-xl-6">
                            <label for="std_code" class="form-label">Std Code</label>
                            <input type="text" name="std_code"required
                                class="form-control @error('std_code') is-invalid @enderror"
                                value="{{ old('std_code', $city->std_code ?? '') }}">
                            @error('std_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                  



                    <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                        <button type="submit"
                            class="btn btn-primary">{{ isset($state) ? 'Update' : 'Submit' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
