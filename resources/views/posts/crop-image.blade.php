<div class="modal fade modal-nested" id="cropImageModal" tabindex="-1" ata-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="cropImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="cropAvatarLabel">Crop Image</h5>
            </div>
            <div class="modal-body text-center">
                <div class="cropper-container-box">
                    <img id="imagePreview" src="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-glass" data-bs-dismiss="modal"
                    onclick="destroyCropperPost()">Cancel</button>
                <button type="button" class="btn btn-primary btn-glass" id="saveImageCrop">Save</button>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        let cropperPostImage;

        $('#postImageInput').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                if (cropperPostImage) cropperPostImage.destroy();

                const $img = $('#imagePreview');
                $img.attr('src', event.target.result);

                const modalEl = document.getElementById('cropImageModal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                $(modalEl).one('shown.bs.modal', function() {
                    $('.modal-backdrop').last().addClass('modal-backdrop-nested');

                    cropperPostImage = new Cropper($img[0], {
                        aspectRatio: 1,
                        viewMode: 1,
                        background: false,
                        autoCropArea: 1,
                        responsive: true,
                        movable: true,
                        zoomable: true,
                        rotatable: false,
                        scalable: false,
                        dragMode: 'move',
                        cropBoxResizable: true,
                        ready() {
                            cropperPostImage.crop();
                        }
                    });
                });
            };
            reader.readAsDataURL(file);
        });

        function destroyCropperPost() {
            if (cropperPostImage) cropperPostImage.destroy();
        }


        $('#saveImageCrop').on('click', function() {
            if (!cropperPostImage) return;
            const canvas = cropperPostImage.getCroppedCanvas({
                width: 500,
                height: 500
            });
            canvas.toBlob(function(blob) {
                const imageUrl = URL.createObjectURL(blob);
                $('.form-file-post').hide();
                $('.imagePostPreview-box').show();
                $('#imagePostPreview').attr("src", imageUrl);

                window.croppedPostImageBlob = blob;

                const modalEl = document.getElementById('cropImageModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            });
        });
    </script>
@endpush
