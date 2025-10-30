<div class="modal fade" id="cropAvatarModal" tabindex="-1" ata-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="cropAvatarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="cropAvatarLabel">Crop Avatar</h5>
            </div>
            <div class="modal-body text-center">
                <div class="cropper-container-box">
                    <img id="avatarPreview" src="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-glass" data-bs-dismiss="modal"
                    onclick="destroy()">Cancel</button>
                <button type="button" class="btn btn-primary btn-glass" id="saveCrop">Save</button>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        let cropper;

        $('#avatarInput').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                const $img = $('#avatarPreview');
                $img.attr('src', event.target.result);

                const modalEl = document.getElementById('cropAvatarModal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                $(modalEl).one('shown.bs.modal', function() {
                    if (cropper) cropper.destroy();

                    cropper = new Cropper($img[0], {
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
                            cropper.crop();
                        }
                    });
                });
            };
            reader.readAsDataURL(file);
        });

        function destroy() {
            if (cropper) cropper.destroy();
        }


        $('#saveCrop').on('click', function() {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 500,
                height: 500
            });
            canvas.toBlob(function(blob) {
                const formData = new FormData();
                formData.append('image', blob, 'avatar.jpg');

                axios.post('{{ url('/api/auth/update-avatar') }}', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    if (cropper) cropper.destroy();
                    if (typeof getAuthMe === 'function') getAuthMe();
                    const modalEl = document.getElementById('cropAvatarModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();

                }).catch(err => {
                    const message = error.response?.data?.message || 'Error updating profile';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            });
        });
    </script>
@endpush
