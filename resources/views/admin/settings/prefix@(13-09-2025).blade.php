@extends('admin.layout.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('public/assets/js/ckeditor/ckeditor.js') }}"></script>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home 1</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($prefix) }} Setting</li>
            </ol>
        </nav>
    </div>
</div>


<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{URL('admin/settings/prefix')}}/{{$prefix}}" method="post" enctype="multipart/form-data"
            id="settingsForm">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Basic Info
                    </div>
                </div>
                <div class="card-body add-products p-0">
                    <div class="p-4">
						<div class="row gx-5">
							<div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                                <div class="card custom-card shadow-none mb-0 border-0">
                                    <div class="card-body p-0">
                                        <div class="row gy-3">

											<?php
											if (!empty($result)) {
												$i = 0;
												$half = floor(count($result) / 2);
												foreach ($result as $setting) {
													$text_extention 	= 	'';
													$key				= 	$setting['key'];
													$keyE 				= 	explode('.', $key);
													$keyTitle 			= 	$keyE['1'];

													$label = $keyTitle;
													if ($setting['title'] != null) {
														$label = $setting['title'];
													}

													$inputType = 'text';
													if ($setting['input_type'] != null) {
														$inputType = $setting['input_type'];
													} ?>
													<input type="hidden" name="Setting[{{$i}}]['type']" value="{{$inputType ?? ''}}">
													<input type="hidden" name="Setting[{{$i}}]['id']" value="{{$setting['id'] ?? ''}}">
													<input type="hidden" name="Setting[{{$i}}]['key']" value="{{$setting['key'] ?? ''}}">

													<?php
													switch ($inputType) {
														case 'checkbox': ?>
															<div class="col-xl-6">
																<label class="form-label" style="width:300px;"><?php echo $label; ?></label>
																<div class="mws-form-item clearfix">
																	<ul class="mws-form-list inline">
																		<?php
																		$checked = ($setting['value'] == 1) ? true : false;
																		$val	 = (!empty($setting['value'])) ? $setting['value'] : 0;
																		?>
																		<input type="checkbox" name="Setting[{{$i}}]['value']" value="{{$val ?? ''}}">

																	</ul>
																</div>
															</div>
														<?php
															break;
														case 'text': ?>
															<div class="col-xl-6">
																<label class="form-label"><?php echo $label; ?></label>
																<input type="{{$inputType}}" name="Setting[{{$i}}]['value']" value="{{$setting['value'] ?? ''}}" class="form-control" id="$key" ]>
																<div class="invalid-feedback"></div>
															</div>
														<?php
															break;
														case 'select': ?>
															<div class="col-xl-6">
																<label class="form-label"><?php echo $label; ?></label>
																<select name="Setting[{{$i}}]['value']" class="form-control " id="$key">
																	<option value="pay_later">Pay Later</option>
																	<option value="pay_now">Pay Now</option>
																</select>
																<div class="invalid-feedback"></div>
															</div>
														<?php
															break;
														case 'file': ?>
															<div class="col-xl-6">
																<label class="form-label"><?php echo $label; ?></label>
																<input type="{{$inputType}}" name="Setting[{{$i}}]['value']" class="form-control" accept="image/*" id="$key" ]>
																@if (!empty($setting['value']))
																	<img height="70" width="70" src="{{isset($setting['value'])? $setting['value']:''}}" />
																@endif
																<div class="invalid-feedback"></div>
															</div>
														<?php
															break;
														case 'textarea': ?>
															<div class="col-xl-12">
																<label class="form-label"><?php echo $label; ?></label>
																<textarea name="Setting[{{$i}}]['value']" id="textarea_resize" class="form-control textarea_resize" rows=3,cols=3>{!!$setting['value'] ?? ''!!}</textarea>
															</div>
														<?php
															break;
														}
														if ($i == $half)
															echo '</div><div class="row">';
														$i++;
															}
											}
											?>
										</div>
									</div>
								</div>
							</div>
							<div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
						</div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>

<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>

<script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

<script src="{{ asset('assets/js/custom/product.js') }}"></script>

{{-- <script src="{{ asset('assets/js/fileupload.js') }}"></script> --}}
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->


<script type="text/javascript">
    document.querySelectorAll('.textarea_resize').forEach(function(textarea) {
        CKEDITOR.replace(textarea, {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
    });
    
	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}

	var empty_msg = 'This field is required';
	var numuric_empty_msg = 'This field is allow only numuric value';
	var image_validation = 'Please upload a valid image. Valid extensions are jpg, jpeg, png, jpeg';
	var allowedExtensions = ['gif', 'GIF', 'jpeg', 'JPEG', 'PNG', 'png', 'jpg', 'JPG'];

	function submit_form() {
		var $inputs = $('.mws-form :input.valid');
		var error = 0;
		$inputs.each(function() {
			if ($(this).val().trim() == '') {
				$(this).next().html(empty_msg);
				error = 1;
			} else {
				if ($(this).attr('id') == 'Site.email') {
					if (!isEmail($(this).val().trim())) {
						$(this).next().html("Please enter a valid email");
						error = 1;
					} else {
						$(this).next().html("");
					}
				} else if ($(this).attr('id') == 'Reading.records_per_page') {
					if (!$.isNumeric($(this).val().trim())) {
						$(this).next().html(numuric_empty_msg);
						error = 1;
					} else {
						$(this).next().html("");
					}
				} else {
					$(this).next().html("");
				}
			}
		});
		if (error == 0) {
			$('.mws-form').submit();
		}
	}
	$('#settingsForm').each(function() {
		$(this).find('input').keypress(function(e) {
			if (e.which == 10 || e.which == 13) {
				submit_form();
				return false;
			}
		});
	});
</script>
@endpush
