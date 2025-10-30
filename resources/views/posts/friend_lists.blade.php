  <div class="col-md-4 futuristic min-vh-100 d-none d-md-block">
      <p class="text-secondary px-4">Suggested for you</p>

      <div class="friend-list-container">
      </div>

      <div class="bubble b1"></div>
      <div class="bubble b2"></div>
      <div class="bubble b3"></div>
  </div>

  @push('script')
      <script>
          $(document).ready(function() {
              const container = document.querySelector('.friend-list-container');

              function renderSkeletonList(count = 5) {
                  for (let i = 0; i < count; i++) {
                      const skeletonItem = document.createElement('div');
                      skeletonItem.classList.add('list-skeleton');

                      skeletonItem.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-skeleton"></div>
                <div>
                    <div class="line-skeleton name-skeleton"></div>
                    <div class="line-skeleton username-skeleton"></div>
                </div>
            </div>
            <div class="line-skeleton" style="width: 60px; height: 30px; border-radius: 20px;"></div>
        `;

                      container.appendChild(skeletonItem);
                  }
              }

              function removeSkeletonList() {
                  container.querySelectorAll('.list-skeleton').forEach(el => el.remove());
              }

              function renderUsers(users) {
                  users.forEach(user => {
                      const item = document.createElement('div');
                      item.classList.add('friend-item', 'd-flex', 'align-items-center',
                          'justify-content-between', 'py-3',
                          'px-4');

                      const imageUrl = user.imagePathUrl || user.image || 'https://via.placeholder.com/500';

                      item.innerHTML = `
            <div class="d-flex align-items-center gap-3 cursor-pointer">
                <div class="avatar-skeleton"></div>
                <img src="${imageUrl}" class="friend-avatar" alt="Avatar" loading="lazy">
                <div>
                    <p class="username">${user.username}</p>
                    <p class="name">${user.name}</p>
                </div>
            </div>
            <a href="#" class="friend-follow me-3" data-id="${user.id}">Follow</a>
        `;

                      const img = item.querySelector('.friend-avatar');
                      const skeleton = item.querySelector('.avatar-skeleton');

                      const tempImg = new Image();
                      tempImg.src = imageUrl;
                      tempImg.onload = () => {
                          img.src = imageUrl;
                          img.style.display = 'block';
                          skeleton.remove();
                      };

                      container.appendChild(item);
                  });
              }


              let nextPageUrlUsers = `${window.baseUrl}/users?is_followed=0&limit=5`;
              let isLoadingUsers = false;

              function getUsers(url, reset = false) {
                  if (!url || isLoadingUsers) return;
                  isLoadingUsers = true;

                  if (reset) {
                      $('.friend-list-container').empty();
                  }

                  renderSkeletonList(5);

                  axios.get(url)
                      .then(function(response) {
                          const users = response.data.data.data;
                          nextPageUrlUsers = response.data.data.next_page_url;
                          removeSkeletonList();
                          renderUsers(users);
                          isLoadingUsers = false;
                      })
                      .catch(function(error) {
                          console.error('Failed to load users:', error.response || error);
                          isLoadingUsers = false;
                      });
              }

              $(document).on('click', '.friend-follow', function(e) {
                  e.preventDefault();

                  const $this = $(this);
                  const userId = $this.data('id');

                  const url = `${window.baseUrl}/users/${userId}/follow`;

                  axios.post(url)
                      .then(response => {
                          nextPageUrlUsers = `${window.baseUrl}/users?is_followed=0&limit=5`;
                          getUsers(nextPageUrlUsers, true);
                      })
                      .catch(error => {})
                      .finally(() => {});
              });

              getUsers(nextPageUrlUsers);

              window.addEventListener('scroll', () => {
                  if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
                      //   getUsers(nextPageUrlUsers);
                  }
              });
          })
      </script>
  @endpush
