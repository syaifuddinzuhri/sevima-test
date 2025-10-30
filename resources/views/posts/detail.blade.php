<div class="modal fade" id="detailPostModal" tabindex="-1" aria-labelledby="detailPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark text-white rounded-4 overflow-hidden">
            <div class="modal-body p-0 position-relative">
                <div class="btn-close-custom">
                    <i class="fas fa-times"></i>
                </div>
                <div class="row g-0">
                    <div class="col-md-7 bg-black">
                        <img src="" class="post-image-detail w-100 h-100 object-fit-cover" alt="">
                    </div>

                    <div class="col-md-5 d-flex flex-column">
                        <div
                            class="p-3 flex-shrink-0 border-bottom border-secondary d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <img src="" class="rounded-circle user-avatar" width="40" height="40"
                                    alt="User Avatar">
                                <span class="fw-bold username-detail"></span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger btn-delete d-none"><i
                                    class="fas fa-trash"></i></button>
                        </div>

                        <div class="p-3 flex-grow-1 overflow-auto">
                            <div class="mb-3 caption-detail" style="max-height: 200px; overflow-y: auto;">
                                <span class="ms-1"></span>
                            </div>

                            <div class="my-2" style="width: 100%; height: 1px; background-color: #ffffff60;"></div>

                            <p class="m-0 fw-bold">Comments</p>

                            <div class="p-3 flex-grow-1">
                                <div class="comments-list" style="max-height: 280px; overflow-y: auto;">
                                </div>
                            </div>
                        </div>

                        <div class="p-3 flex-shrink-0 border-top border-secondary">
                            <div class="d-flex gap-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-heart fa-lg cursor-pointer post-like icon-like"></i>
                                    <span class="fw-semibold detail-post-like"></span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-comment fa-lg cursor-pointer icon-comment"></i>
                                    <span class="fw-semibold detail-post-comment"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="ps-3 replying d-none">Replying to <span
                                        class="reply-user">...</span></small>
                                <div class="position-relative mt-2">
                                    <input type="text" class="form-control rounded-pill"
                                        placeholder="Add a comment..." id="comment">
                                    <span class="reply-icon">
                                        <i class="fa fa-paper-plane"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            window.parentCommentId = null;
            window.postId = null;
            window.parentCommentUser = null;
            const $modal = $('#detailPostModal');

            $modal.find('.btn-close-custom').on('click', function() {
                const modalInstance = bootstrap.Modal.getInstance($modal[0]);
                modalInstance.hide();
            })


            $modal.on('show.bs.modal', function(event) {
                const triggerElement = $(event.relatedTarget);
                const postId = triggerElement.data('id');
                const isMe = triggerElement.data('me');
                const url = `${window.baseUrl}/posts/${postId}`;
                window.postId = postId;

                function getDetail() {
                    $('.comments-list').empty();
                    axios.get(url)
                        .then(function(response) {
                            const data = response.data.data;

                            if (isMe) {
                                $('.btn-delete').removeClass('d-none')
                            }

                            $('.post-image-detail').attr('src', data.imagePathUrl);
                            $('.user-avatar').attr('src', data.user?.imagePathUrl);

                            $('.username-detail').text(data.user?.username);
                            $('.caption-detail span.ms-1').text(data.caption);

                            $('.detail-post-like').text(`${data.likes_count}`);
                            $('.detail-post-comment').text(`${data.comments_count}`);

                            $('.post-like').data('id', data.id)
                            if (data.is_liked) {
                                $('.icon-like').removeClass('fa-regular')
                                $('.icon-like').addClass('fa')
                            } else {
                                $('.icon-like').removeClass('fa')
                                $('.icon-like').addClass('fa-regular')
                            }
                            if (data.is_commented) {
                                $('.icon-comment').removeClass('fa-regular')
                                $('.icon-comment').addClass('fa')
                            } else {
                                $('.icon-comment').removeClass('fa')
                                $('.icon-comment').addClass('fa-regular')
                            }
                        })
                        .catch(function(error) {
                            console.error(error);
                        });
                }

                getDetail();

                function renderCommentItem(comment) {

                    const $item = $(`
        <div class="comment-item mb-2">
            <div class="d-flex align-items-start gap-2">
                <img src="${comment.user?.imagePathUrl || 'https://i.pravatar.cc/30'}" class="rounded-circle" width="30" height="30" alt="${comment.user?.username}">
                <div>
                    <div class="fw-semibold">${comment.user?.username}</div>
                    <div class="comment">${comment.comment}</div>
                    ${!comment.parent_comment_id ? `<span class="cursor-pointer reply-action" data-id="${comment.id}" data-user="${comment.user?.username}">Reply</span>` : ``}
                </div>
            </div>
            <div class="replies ps-4 my-3"></div>
        </div>
    `);

                    if (comment.replies && comment.replies.length) {
                        const $repliesContainer = $item.find('.replies');
                        comment.replies.forEach(reply => {
                            $repliesContainer.append(renderCommentItem(reply));
                        });
                    }

                    return $item;
                }


                let nextPageUrlComments = `${window.baseUrl}/posts/${postId}/comments`;
                let isLoadingComments = false;

                function getComments(url, reset = false) {
                    if (!url || isLoadingComments) return;
                    isLoadingComments = true;

                    if (reset) {
                        $('.comments-list').empty();
                    }

                    axios.get(url)
                        .then(function(response) {
                            const comments = response.data.data.data;
                            comments.forEach(comment => {
                                $('.comments-list').append(renderCommentItem(comment));
                            });

                            $('.reply-action').off('click').on('click', function() {
                                const commentId = $(this).data('id');
                                const commentUser = $(this).data('user');
                                window.parentCommentId = commentId;
                                window.parentCommentUser = commentUser;
                                $('.replying').removeClass('d-none')
                                $('.reply-user').text(commentUser)
                                $('#comment').focus();
                            });
                            isLoadingComments = false;
                        })
                        .catch(function(error) {
                            isLoadingComments = false;
                        });
                }

                getComments(nextPageUrlComments);


                $('.reply-icon').on('click', function() {
                    const comment = $('#comment').val();
                    let url = `${window.baseUrl}/posts/${postId}/comments`;
                    axios.post(url, {
                            parent_comment_id: window.parentCommentId,
                            comment
                        })
                        .then(function(response) {
                            getDetail()
                            const firstUrl = `${window.baseUrl}/posts/${postId}/comments`;
                            getComments(firstUrl, true);
                            $('#comment').val('');
                            $('.replying').addClass('d-none')
                            $('.reply-user').text('')
                        })
                        .catch(function(error) {
                            const message = error.response?.data?.message ||
                                'Error';
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
                })

                $('.btn-delete').on('click', function() {
                    let url = `${window.baseUrl}/posts/${window.postId}`;
                    axios.delete(url)
                        .then(function(response) {
                            if (typeof getPosts === 'function') {
                                let nextPageUrlPosts = '{{ url('/api/posts') }}?limit=9';
                                getPosts(nextPageUrlPosts, true)
                            };
                            const modalInstance = bootstrap.Modal.getInstance($modal[0]);
                            modalInstance.hide();
                        })
                        .catch(function(error) {
                            const message = error.response?.data?.message ||
                                'Error';
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
                })
            });
        });
    </script>
@endpush
