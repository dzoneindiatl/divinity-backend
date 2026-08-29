function dropzoneInit(generalConfig = {}, dropzoneConfig = {}, sessionStorageConfig = {}) {
    // General config
    let id = generalConfig.id ?? null,
        dropzoneInitElement = generalConfig.dropzone_init_element ?? null,
        displayExistingFile = generalConfig.display_existing_files ?? false;

    // Dropzone options and configurations
    let uploadFileUrl = dropzoneConfig.upload_file_url ?? null,
        removeFileUrl = dropzoneConfig.remove_file_url ?? null,
        maxFiles = dropzoneConfig.max_files ?? null,
        maxFilesize = dropzoneConfig.max_file_size ?? 4,
        dictMaxFileSizeExceededMessage = dropzoneConfig.dict_max_file_size_message ?? "you cannot upload files of more than 4 MB",
        dictMaxFilesExceededMessage = dropzoneConfig.dict_max_files_exceeded_message ?? null,
        acceptedFiles = dropzoneConfig.accepted_files ?? null;

    // Session configs
    let sessionKey = sessionStorageConfig.session_key ?? null,
        sessionKeyDataType = sessionStorageConfig.session_key_datatype ?? String;


    dropzoneInitElement.dropzone({
        url: uploadFileUrl,
        acceptedFiles: acceptedFiles,
        // clickable: true,
        addRemoveLinks: true,
        addOpenLinks: true,
        maxFiles: maxFiles,
        maxFilesize: maxFilesize,
        thumbnailWidth: 120,
        thumbnailWidth: 120,
        dictMaxFilesExceeded: dictMaxFilesExceededMessage,
        dictFileTooBig: dictMaxFileSizeExceededMessage,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            // "Access-Control-Allow-Origin": "*",
        },
        init: function () {

            let myDropzone = this;

            // If file is already uploaded on server, to display files on dropzone            
            if (displayExistingFile) {
                let uploadedExistingFiles = generalConfig.uploaded_existing_files ?? [],
                    pathOfUploadedFiles = []; // If session key data type is Array
                if (uploadedExistingFiles.length) {

                    uploadedExistingFiles.forEach((uploadedFile, index) => {
                        // console.log(uploadedFile);

                        let fileName = 'file' + (index + 1),
                            fileSize = uploadedFile.file_size ?? 0,
                            fileUrl = uploadedFile.file_url ?? '',
                            filePath = uploadedFile.file_path ?? '';

                        if (fileUrl != "" && filePath != "") {
                            let mockFile = { name: fileName, size: fileSize, file_path: filePath };
                            let callback = null;
                            let crossOrigin = "anonymous"; // Added to the `img` tag for crossOrigin handling
                            let resizeThumbnail = true; // Tells Dropzone whether it should resize the image first
                            let previewElement = true;

                            myDropzone.displayExistingFile(mockFile, fileUrl, callback, crossOrigin, resizeThumbnail)

                            if (sessionKeyDataType == Array) {
                                pathOfUploadedFiles.push(filePath);
                            }
                        }
                    });

                    myDropzone.on("thumbnail", function (file) {

                        uploadFileUrl = file.dataURL ?? "";
                        thumbnailPreview = file.previewElement ?? "";

                        if (file.hasOwnProperty('file_path')) {
                            var ext = file.file_path.split('.').pop();

                            if (ext == "pdf") {
                                $(thumbnailPreview).find(".dz-image img").attr("src", "/images/pdf.png");
                            } else if (ext.indexOf("doc") != -1) {
                                $(thumbnailPreview).find(".dz-image img").attr("src", "/images/doc.png");
                            } else if (ext.indexOf("xls") != -1) {
                                $(thumbnailPreview).find(".dz-image img").attr("src", "/images/xls.png");
                            }

                            var anchorEl = document.createElement('a');
                            anchorEl.setAttribute('href', uploadFileUrl);
                            anchorEl.setAttribute('class', "dz-view-file");
                            anchorEl.setAttribute('target', '_blank');
                            anchorEl.innerHTML = "<span class='mdi mdi-file mdi-24px text-primary'></span>";
                            thumbnailPreview.appendChild(anchorEl);
                        }

                    });

                    $(".dz-remove").html("<div><span class='mdi mdi-delete mdi-24px text-danger'></span></div>");

                    // let fileCountOnServer = 1; // The number of files already uploaded
                    // myDropzone.options.maxFiles = myDropzone.options.maxFiles - fileCountOnServer;

                    if (sessionKeyDataType == String) {
                        sessionStorage.removeItem(sessionKey);
                        sessionStorage.setItem(sessionKey, uploadedExistingFiles[0].file_path);
                    } else {
                        sessionStorage.removeItem(sessionKey);
                        sessionStorage.setItem(sessionKey, JSON.stringify(pathOfUploadedFiles));
                    }
                }
            }

            myDropzone.on("complete", function (file) {

                $(".dz-remove").html("<div><span class='mdi mdi-delete mdi-24px text-danger'></span></div>");

                let responseData = JSON.parse(file.xhr.response),
                    fileName = responseData.uploadedFile ?? "",
                    uploadFileUrl = responseData.uploadFileUrl ?? "",
                    thumbnailPreview = file.previewElement ?? "";


                var ext = fileName.split('.').pop();

                if (ext == "pdf") {
                    $(thumbnailPreview).find(".dz-image img").attr("src", "/images/pdf.png");
                } else if (ext == "doc") {
                    $(thumbnailPreview).find(".dz-image img").attr("src", "/images/doc.png");
                } else if (ext == "xlsx") {
                    $(thumbnailPreview).find(".dz-image img").attr("src", "/images/xls.png");
                }

                var anchorEl = document.createElement('a');
                anchorEl.setAttribute('href', uploadFileUrl);
                anchorEl.setAttribute('class', "dz-view-file");
                anchorEl.setAttribute('target', '_blank');
                anchorEl.innerHTML = "<span class='mdi mdi-file mdi-24px text-primary'></span>";
                thumbnailPreview.appendChild(anchorEl);


                if (fileName != "") {
                    if (sessionKeyDataType == String) {
                        sessionStorage.removeItem(sessionKey);
                        sessionStorage.setItem(sessionKey, fileName);
                    } else {
                        let uploadingDocuments = sessionStorage.getItem(sessionKey) ?? JSON.stringify([]);
                        uploadingDocuments = JSON.parse(uploadingDocuments);
                        uploadingDocuments.push(fileName);
                        sessionStorage.setItem(sessionKey, JSON.stringify(uploadingDocuments));
                    }
                }

            });

            myDropzone.on('removedfile', function (file) {
                let filePath = file.file_path ?? null;
                let responseData = filePath ?? JSON.parse(file.xhr.response),
                    fileName = filePath ?? responseData.uploadedFile;
                ajaxSetupFun();
                $.ajax({
                    type: 'POST',
                    url: removeFileUrl,
                    data: { name: fileName },
                    success: function (data) {
                        let removeFile = data.name ?? "";
                        if (removeFile != "") {
                            if (sessionKeyDataType == String) {
                                sessionStorage.removeItem(sessionKey);
                            } else {
                                let uploadingDocuments = sessionStorage.getItem(sessionKey) ?? JSON.stringify([]);
                                uploadingDocuments = JSON.parse(uploadingDocuments);
                                // uploadingDocuments.pop(removeFile);
                                let myIndex = uploadingDocuments.indexOf(removeFile);
                                if (myIndex !== -1) {
                                    uploadingDocuments.splice(myIndex, 1);
                                }
                                sessionStorage.setItem(sessionKey, JSON.stringify(uploadingDocuments));
                            }
                        }
                    },
                    error: function (e) {
                        swalWithBootstrapButtons.fire(
                            "Server error",
                            'Something is wrong',
                            "error"
                        );
                    }
                });
            })

            myDropzone.on('error', function (file, message) {

                let errorType = 'Server Error',
                    errorMessage = 'File could not be uploaded';

                if (typeof message == "object") {
                    errorMessage = message.message;
                } else if (typeof message == "string") {
                    errorMessage = message;
                    errorType = 'Validation Error';
                }

                swalWithBootstrapButtons.fire(
                    errorType,
                    errorMessage,
                    "error"
                );

                myDropzone.removeFile(file);
            });
        },
    });
    return true;
}