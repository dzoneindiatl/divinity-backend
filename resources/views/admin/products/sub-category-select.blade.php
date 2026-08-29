<div class="col-xl-6">
    <label for="sub_category_id" class="form-label">Sab Category</label>
    <select class="js-example-placeholder-single js-states form-control" name="sub_category_id" id="sub_category_id">
        <option value="" selected>Select Sub Category</option>
        @forelse ($subCategories as $subCategory)
        <option value="{{ $subCategory->id }}">{{ $subCategory->name }}
        </option>
        @empty
        <option value="" selected>No Data found</option>
        @endforelse
    </select>
</div>