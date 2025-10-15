@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
      <section class="section">
        <div class="section-header">
          <h1>Review Details</h1>
        </div>

        <div class="section-body">

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4>Review Information</h4>
                  <div class="card-header-action">
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back</a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <table class="table">
                        <tbody>
                          <tr>
                            <td><strong>Product:</strong></td>
                            <td>
                              <a href="{{ route('product-detail', $review->product->slug) }}" target="_blank">
                                {{ $review->product->name }}
                              </a>
                            </td>
                          </tr>
                          <tr>
                            <td><strong>User:</strong></td>
                            <td>{{ $review->user->name }}</td>
                          </tr>
                          <tr>
                            <td><strong>User Email:</strong></td>
                            <td>{{ $review->user->email }}</td>
                          </tr>
                          <tr>
                            <td><strong>Rating:</strong></td>
                            <td>
                              @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                  <i class="fas fa-star text-warning"></i>
                                @else
                                  <i class="far fa-star text-muted"></i>
                                @endif
                              @endfor
                              <span class="ml-2">({{ $review->rating }}/5)</span>
                            </td>
                          </tr>
                          <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                              @if($review->status == 1)
                                <span class="badge badge-success">Approved</span>
                              @else
                                <span class="badge badge-warning">Pending</span>
                              @endif
                            </td>
                          </tr>
                          <tr>
                            <td><strong>Date:</strong></td>
                            <td>{{ $review->created_at->format('d M Y, H:i') }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-md-6">
                      @if($review->product->thumb_image)
                        <div class="product-image">
                          <h6><strong>Product Image:</strong></h6>
                          <img src="{{ asset($review->product->thumb_image) }}" 
                               alt="{{ $review->product->name }}" 
                               class="img-thumbnail"
                               style="max-width: 200px;">
                        </div>
                      @endif
                    </div>
                  </div>

                  <div class="row mt-4">
                    <div class="col-12">
                      <h6><strong>Review Text:</strong></h6>
                      <div class="card">
                        <div class="card-body">
                          <p class="mb-0">{{ $review->review }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  @if($review->productReviewGalleries->count() > 0)
                    <div class="row mt-4">
                      <div class="col-12">
                        <h6><strong>Review Images:</strong></h6>
                        <div class="gallery">
                          <div class="row">
                            @foreach($review->productReviewGalleries as $image)
                              <div class="col-md-3 col-sm-6 mb-3">
                                <div class="gallery-item">
                                  <a href="{{ asset($image->image) }}" data-lightbox="review-gallery">
                                    <img src="{{ asset($image->image) }}" 
                                         alt="Review Image" 
                                         class="img-thumbnail"
                                         style="width: 100%; height: 150px; object-fit: cover;">
                                  </a>
                                </div>
                              </div>
                            @endforeach
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif

                  <div class="row mt-4">
                    <div class="col-12">
                      <div class="d-flex justify-content-between">
                        <div>
                          @if($review->status == 0)
                            <button class="btn btn-success approve-review" data-id="{{ $review->id }}">
                              <i class="fas fa-check"></i> Approve Review
                            </button>
                          @else
                            <button class="btn btn-warning disapprove-review" data-id="{{ $review->id }}">
                              <i class="fas fa-times"></i> Disapprove Review
                            </button>
                          @endif
                        </div>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                          <i class="fas fa-arrow-left"></i> Back to Reviews
                        </a>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>
      </section>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<script>
    $(document).ready(function(){
        $('.approve-review, .disapprove-review').on('click', function(){
            let id = $(this).data('id');
            let status = $(this).hasClass('approve-review') ? 'true' : 'false';
            
            $.ajax({
                method: 'PUT',
                url: "{{ route('admin.reviews.change-status') }}",
                data: {
                    id: id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data){
                    toastr.success(data.message);
                    location.reload();
                },
                error: function(xhr, status, error){
                    console.log(error);
                }
            });
        });
    });
</script>
@endpush