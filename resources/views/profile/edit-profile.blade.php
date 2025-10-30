<div class="modal fade" id="editProfileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <form id="editProfileForm">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editProfileModalLabel">Edit Profile</h1>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control rounded-pill" id="name" name="name"
                            placeholder="Enter name">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control rounded-pill" id="username" name="username"
                            placeholder="Enter username">
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
            const $modal = $('#editProfileModal');
            const $form = $('#editProfileForm');

            $modal.on('show.bs.modal', function() {
                axios.get('{{ url('/api/auth/me') }}')
                    .then(function(response) {
                        const data = response.data.data;
                        $form.find('#name').val(data.name || '');
                        $form.find('#username').val(data.username || '');
                    })
                    .catch(function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Failed to load profile data',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    });
            });

            $form.on('submit', function(e) {
                e.preventDefault();
                const name = $('#name').val();
                const username = $('#username').val();

                axios.post('{{ url('/api/auth/update') }}', {
                        name,
                        username
                    })
                    .then(function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Profile updated successfully!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                        if (typeof getAuthMe === 'function') getAuthMe();

                        const modalInstance = bootstrap.Modal.getInstance($modal[0]);
                        modalInstance.hide();
                    })
                    .catch(function(error) {
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
