(function () {
    'use strict'

//     let myDropzone = new Dropzone(".dropzone");
//     myDropzone.on("addedfile", file => {
// });

    /* filepond */
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginImageExifOrientation,
        FilePondPluginFileValidateSize,
        FilePondPluginFileEncode,
        FilePondPluginImageEdit,
        FilePondPluginFileValidateType,
        FilePondPluginImageCrop,
        FilePondPluginImageResize,
        FilePondPluginImageTransform

    );

    /* multiple upload */
    const multipleProductImages = document.querySelector('.multiple-products-images');
    FilePond.create(multipleProductImages,);


    const multipleProductVideos = document.querySelector('.multiple-products-videos');
    FilePond.create(multipleProductVideos,);

    FilePond.setOptions({
        server: '/product-store',
        Headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })

})();