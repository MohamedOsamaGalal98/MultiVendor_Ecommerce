@extends('frontend.dashboard.layouts.master')

@section('title')
{{$settings->site_name}} || Review Details
@endsection

@section('content')
  <!--=============================
    DASHBOARD START
  ==============================-->
  <section id="wsus__dashboard">
    <div class="container-fluid">
        @include('frontend.dashboard.layouts.sidebar')

      <div class="row">
        <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
          <div class="dashboard_content mt-2 mt-md-0">
            <h3><i class="far fa-star"></i> Review Details</h3>
            
            <div class="wsus__dashboard_profile">
              <div class="wsus__dash_pro_area">
                
                <div class="row">
                  <div class="col-md-12">
                    <div class="card">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Review</h5>
                        <a href="{{ route('user.review.index') }}" class="btn btn-primary btn-sm">
                          <i class="fas fa-arrow-left"></i> Back to Reviews
                        </a>
                      </div>
                      <div class="card-body">
                        
                        <!-- Product Information -->
                        <div class="row mb-4">
                          <div class="col-md-8">
                            <h6><strong>Product:</strong></h6>
                            <p>
                              <a href="{{ route('product-detail', $review->product->slug) }}" 
                                 class="text-decoration-none fw-bold" 
                                 target="_blank">
                                {{ $review->product->name }}
                              </a>
                            </p>
                          </div>
                          <div class="col-md-4">
                            @if($review->product->thumb_image)
                              <div class="product-image text-center">
                                <img src="{{ asset($review->product->thumb_image) }}" 
                                     alt="{{ $review->product->name }}" 
                                     class="img-thumbnail"
                                     style="max-width: 150px;">
                              </div>
                            @endif
                          </div>
                        </div>

                        <hr>

                        <!-- Review Information -->
                        <div class="row mb-4">
                          <div class="col-md-6">
                            <h6><strong>My Rating:</strong></h6>
                            <div class="mb-3">
                              @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                  <i class="fas fa-star text-warning"></i>
                                @else
                                  <i class="far fa-star text-muted"></i>
                                @endif
                              @endfor
                              <span class="ms-2 fw-bold">({{ $review->rating }}/5)</span>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <h6><strong>Status:</strong></h6>
                            <p>
                              @if($review->status == 1)
                                <span class="badge bg-success">Approved</span>
                              @else
                                <span class="badge bg-warning">Pending Approval</span>
                              @endif
                            </p>
                          </div>
                        </div>

                        <div class="row mb-4">
                          <div class="col-md-6">
                            <h6><strong>Review Date:</strong></h6>
                            <p>{{ $review->created_at->format('d M Y, H:i') }}</p>
                          </div>
                          <div class="col-md-6">
                            @if($review->updated_at != $review->created_at)
                            <h6><strong>Last Updated:</strong></h6>
                            <p>{{ $review->updated_at->format('d M Y, H:i') }}</p>
                            @endif
                          </div>
                        </div>

                        <hr>

                        <!-- Review Text -->
                        <div class="row mb-4">
                          <div class="col-12">
                            <h6><strong>My Review:</strong></h6>
                            <div class="card bg-light">
                              <div class="card-body">
                                <p class="mb-0">{{ $review->review }}</p>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Review Images -->
                        @if($review->productReviewGalleries->count() > 0)
                          <div class="row mb-4">
                            <div class="col-12">
                              <h6><strong>My Photos:</strong></h6>
                              <div class="row">
                                @foreach($review->productReviewGalleries as $image)
                                  <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="review-image-container">
                                      <a href="{{ asset($image->image) }}" data-lightbox="review-gallery" data-title="Review Photo">
                                        <img src="{{ asset($image->image) }}" 
                                             alt="Review Photo" 
                                             class="img-thumbnail w-100"
                                             style="height: 150px; object-fit: cover; cursor: pointer;">
                                      </a>
                                    </div>
                                  </div>
                                @endforeach
                              </div>
                            </div>
                          </div>
                        @endif

                        <hr>

                        <!-- Action Buttons -->
                        <div class="row">
                          <div class="col-12">
                            <div class="d-flex justify-content-between flex-wrap gap-2">
                              <div>
                                <a href="{{ route('product-detail', $review->product->slug) }}" 
                                   class="btn btn-info btn-sm" 
                                   target="_blank">
                                  <i class="fas fa-external-link-alt"></i> View Product
                                </a>
                                @if($review->status == 0)
                                  <span class="badge bg-info ms-2">
                                    <i class="fas fa-clock"></i> Waiting for admin approval
                                  </span>
                                @endif
                              </div>
                              <a href="{{ route('user.review.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to My Reviews
                              </a>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--=============================
    DASHBOARD END
  ==============================-->
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<style>
.review-image-container {
  position: relative;
  overflow: hidden;
  border-radius: 8px;
}

.review-image-container:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.card {
  border: 1px solid #e0e0e0;
  border-radius: 10px;
}

.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
}

.badge {
  font-size: 0.85em;
  padding: 0.5em 0.75em;
}

.text-warning {
  color: #ffc107 !important;
}
</style>
@endpush