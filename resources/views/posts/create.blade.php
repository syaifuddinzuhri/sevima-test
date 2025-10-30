<div class="modal fade" id="addPostModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <form id="addPostForm">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addPostModalLabel">Add Post</h1>
                </div>
                <div class="modal-body d-flex gap-3">
                    <div>
                        <label for="image" class="form-label">Image</label>
                        <div class="form-file form-file-post">
                            <div class="text-center">
                                <i class="fas fa-upload"></i>
                                <p class="m-0">Upload File</p>
                            </div>
                        </div>
                        <div class="imagePostPreview-box">
                            <span class="badge rounded-pill text-bg-light edit-post-image p-2 cursor-pointer">
                                <i class="fas fa-pen"></i>
                            </span>
                            <img src="" alt="" id="imagePostPreview">
                        </div>
                        <input type="file" id="postImageInput" accept="image/*" class="d-none">
                    </div>
                    <div class="w-100">
                        <label for="caption" class="form-label">Caption</label>
                        <textarea name="caption" id="caption" class="form-control" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-glass btn-padding"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-glass btn-padding">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            let cropperPostImage;

            const $modal = $('#addPostModal');
            const $form = $('#addPostForm');

            $modal.on('show.bs.modal', function() {
                if (cropperPostImage) cropperPostImage.destroy();
                $('#caption').val('')
                $('.form-file-post').show();
                $('.imagePostPreview-box').hide();
                $('#imagePostPreview').attr("src", "");
            });

            $form.on('submit', function(e) {
                e.preventDefault();
                const caption = $('#caption').val();

                const formData = new FormData();
                formData.append('image', window.croppedPostImageBlob, 'avatar.jpg');
                formData.append('caption', caption);

                axios.post('{{ url('/api/posts') }}', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Create post successfully!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                        const modalInstance = bootstrap.Modal.getInstance($modal[0]);
                        modalInstance.hide();

                        let refetch = '{{ url('/api/posts') }}?limit=9';
                        if (typeof getPosts === 'function') getPosts(refetch, true);
                    })
                    .catch(function(error) {
                        const message = error.response?.data?.message || 'Error';
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


        $('.form-file-post').on('click', function() {
            $('#postImageInput').trigger('click');
        });

        $('.edit-post-image').on('click', function() {
            $('#postImageInput').trigger('click');
        });
    </script>
@endpush
