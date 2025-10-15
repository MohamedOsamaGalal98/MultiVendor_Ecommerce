@extends('admin.layouts.master')

@push('styles')
<style>
.msg-notification {
    border: 3px solid #28a745 !important;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
}

.chat-user-profile {
    cursor: pointer;
    padding: 10px;
    border-radius: 8px;
    transition: background-color 0.3s;
}

.chat-user-profile:hover {
    background-color: #f8f9fa;
}

.chat-user-profile.active {
    background-color: #e9ecef;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush

@section('content')
<section class="section">
    <div class="section-header">
      <h1>Admin Messenger</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active">Dashboard</div>
        <div class="breadcrumb-item">Communication</div>
        <div class="breadcrumb-item">Messages</div>
      </div>
    </div>

    <div class="section-body">
      <!-- Statistics Row -->
      <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
              <i class="far fa-user"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Total Users</h4>
              </div>
              <div class="card-body">
                {{ $chatUsers->where('senderProfile.role', 'user')->count() }}
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-warning">
              <i class="far fa-store"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Vendors</h4>
              </div>
              <div class="card-body">
                {{ $chatUsers->where('senderProfile.role', 'vendor')->count() }}
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-danger">
              <i class="far fa-user-cog"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Admins</h4>
              </div>
              <div class="card-body">
                {{ $chatUsers->where('senderProfile.role', 'admin')->count() }}
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-success">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Active Chats</h4>
              </div>
              <div class="card-body">
                {{ $chatUsers->where('hasExistingChat', true)->count() }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row align-items-center justify-content-center">
        <div class="col-md-3">
          <div class="card" style="height: 70vh;">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4>All Users</h4>
              @php
                  $totalUnseenChats = $chatUsers->where('hasUnseenMessages', true)->count();
              @endphp
              @if($totalUnseenChats > 0)
                  <span class="badge badge-danger badge-pill">{{ $totalUnseenChats }} new</span>
              @endif
            </div>
            <div class="card-body">
              <!-- Search and Filter -->
              <div class="mb-3">
                <input type="text" class="form-control form-control-sm" id="userSearch" placeholder="Search users...">
                <div class="mt-2">
                  <select class="form-control form-control-sm" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="admin">Admins</option>
                    <option value="vendor">Vendors</option>
                    <option value="user">Users</option>
                  </select>
                </div>
              </div>
              <ul class="list-unstyled list-unstyled-border">
                @forelse ($chatUsers as $chatUser)
                @php
                    $unseenMessages = $chatUser->hasUnseenMessages ?? false;
                    $unseenCount = 0;
                    
                    if (isset($chatUser->hasExistingChat) && $chatUser->hasExistingChat) {
                        $unseenCount = \App\Models\Chat::where(['sender_id' => $chatUser->senderProfile->id, 'receiver_id' => auth()->user()->id, 'seen' => 0])->count();
                    }
                    
                    // Set user role badge
                    $roleBadge = '';
                    $roleClass = '';
                    switch($chatUser->senderProfile->role) {
                        case 'admin':
                            $roleBadge = 'Admin';
                            $roleClass = 'badge-danger';
                            break;
                        case 'vendor':
                            $roleBadge = 'Vendor';
                            $roleClass = 'badge-warning';
                            break;
                        case 'user':
                            $roleBadge = 'User';
                            $roleClass = 'badge-info';
                            break;
                    }
                @endphp
                <li class="media chat-user-profile" data-id="{{ $chatUser->senderProfile->id }}">
                  <img alt="image" class="mr-3 rounded-circle {{ $unseenMessages ? 'msg-notification' : '' }}" width="50" src="{{ asset($chatUser->senderProfile->image ?? 'backend/assets/img/avatar/avatar-1.png') }}">
                  <div class="media-body">
                    <div class="mt-0 mb-1 font-weight-bold chat-user-name">{{ $chatUser->senderProfile->name }}</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge {{ $roleClass }}">{{ $roleBadge }}</span>
                        <div class="text-right">
                            @if($unseenMessages && $unseenCount > 0)
                                <span class="badge badge-primary badge-pill">{{ $unseenCount }}</span>
                                <br><small class="text-primary"><i class="fas fa-envelope"></i> New</small>
                            @elseif(!isset($chatUser->hasExistingChat) || !$chatUser->hasExistingChat)
                                <small class="text-muted">No messages</small>
                            @else
                                <small class="text-success"><i class="fas fa-check-double"></i> Active</small>
                            @endif
                        </div>
                    </div>
                  </div>
                </li>
                @empty
                <li class="text-center py-4">
                    <i class="fas fa-users fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No users available to chat with</p>
                </li>
                @endforelse

              </ul>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <div class="card chat-box d-none" id="mychatbox" style="height: 70vh;">
            <div class="card-header">
              <h4 id="chat-inbox-title">Chat with Rizal</h4>
            </div>
            <div class="card-body chat-content" data-inbox="">

            </div>
            <div class="card-footer chat-form">
              <form id="message-form">
                <input type="text" class="form-control message-box" placeholder="Type a message" name="message">
                <input type="hidden" name="receiver_id"
                value="" id="receiver_id">

                <button class="btn btn-primary">
                  <i class="far fa-paper-plane"></i>
                </button>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

@endsection

@push('scripts')
    <script>
        const mainChatInbox = $('.chat-content');

        function formatDateTime(dateTimeString) {
            const options = {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }
            const formatedDateTime = new Intl.DateTimeFormat('en-Us', options).format(new Date(dateTimeString));
            return formatedDateTime;
        }

        function scrollTobottom() {
            mainChatInbox.scrollTop(mainChatInbox.prop("scrollHeight"));
        }

        $(document).ready(function(){
            // Search and Filter functionality
            $('#userSearch').on('keyup', function() {
                filterUsers();
            });

            $('#roleFilter').on('change', function() {
                filterUsers();
            });

            function filterUsers() {
                const searchTerm = $('#userSearch').val().toLowerCase();
                const selectedRole = $('#roleFilter').val();

                $('.chat-user-profile').each(function() {
                    const userName = $(this).find('.chat-user-name').text().toLowerCase();
                    const userRole = $(this).find('.badge').text().toLowerCase();
                    
                    const matchesSearch = userName.includes(searchTerm);
                    const matchesRole = selectedRole === '' || userRole === selectedRole;
                    
                    if (matchesSearch && matchesRole) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            // Add active class on click
            $('.chat-user-profile').on('click', function(){
                $('.chat-user-profile').removeClass('active');
                $(this).addClass('active');
                let receiverId = $(this).data('id');
                let receiverImage = $(this).find('img').attr('src')
                let chatUserName = $(this).find('.chat-user-name').text();
                $(this).find('img').removeClass('msg-notification');
                $('.chat-box').removeClass('d-none');
                mainChatInbox.attr('data-inbox', receiverId);
                $('#receiver_id').val(receiverId);
                $.ajax({
                    method: 'get',
                    url: '{{ route("admin.get-messages") }}',
                    data: {
                        receiver_id: receiverId
                    },
                    beforeSend: function() {
                        mainChatInbox.html("");
                        // set chat inbox title
                        $('#chat-inbox-title').text(`Chat With ${chatUserName}`)
                    },
                    success: function(response) {
                        // Check if no messages exist
                        if (response.length === 0) {
                            var welcomeMessage = `
                                <div class="text-center p-4">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No messages yet</h5>
                                    <p class="text-muted">Start a conversation with ${chatUserName}</p>
                                </div>
                            `;
                            mainChatInbox.append(welcomeMessage);
                        } else {
                            $.each(response, function(index, value) {
                            if(value.sender_id == USER.id) {
                                var message = `
                                <div class="chat-item chat-right" style=""><img style="height: 50px;
                                 object-fit: cover;" src="${USER.image}"><div class="chat-details"><div class="chat-text">${value.message}</div><div class="chat-time">${formatDateTime(value.created_at)}</div></div></div>
                                `
                            }else {
                                var message = `
                                <div class="chat-item chat-left" style=""><img src="${receiverImage}"><div class="chat-details"><div class="chat-text">${value.message}</div><div class="chat-time">${formatDateTime(value.created_at)}</div></div></div>
                                `
                            }

                                mainChatInbox.append(message);
                            });
                        }

                        // scroll to bottom
                        scrollTobottom();
                    },
                    error: function(xhr, status, error) {

                    },
                    complete: function() {

                    }
                })
            })


            // Allow sending message with Enter key
            $('.message-box').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    $('#message-form').submit();
                }
            });

            $('#message-form').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let messageData = $('.message-box').val();

                var formSubmitting = false;

                if(formSubmitting || messageData === "" ) {
                    return;
                }

                // set message in inbox
                let message = `
                <div class="chat-item chat-right" style=""><img style="height: 50px;
                object-fit: cover;" src="${USER.image}"><div class="chat-details"><div class="chat-text">${messageData}</div><div class="chat-time">10:53</div></div></div>
                `
                mainChatInbox.append(message);
                $('.message-box').val('');
                scrollTobottom()

                $.ajax({
                    method: 'POST',
                    url: '{{ route("admin.send-message") }}',
                    data: formData,
                    beforeSend: function() {
                        $('.send-button').prop('disabled', true);
                        formSubmitting = true;
                    },
                    success: function(response) {
                        // Show success notification
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Message sent successfully!');
                        }
                    },
                    error: function(xhr, status, error) {
                       toastr.error(xhr.responseJSON.message);
                       $('.send-button').prop('disabled', false);
                       formSubmitting = false;
                    },
                    complete: function() {
                        $('.send-button').prop('disabled', false);
                        formSubmitting = false;
                    }
                })
            })
        })
    </script>
@endpush
