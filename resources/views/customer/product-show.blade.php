@extends('layouts.app')

@section('title', $product->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
<div class="product-detail-container">
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>›</span>
            <a href="{{ route('home', ['category' => $product->productCategory->id]) }}">
                {{ $product->productCategory->name }}
            </a>
            <span>›</span>
            <span>{{ $product->name }}</span>
        </div>

        <!-- Product Detail Grid -->
        <div class="product-detail-grid">
            <!-- Image Gallery -->
            <div class="product-gallery">
                <img
                    src="{{ $product->thumbnail_url }}"
                    alt="{{ $product->name }}"
                    class="main-image"
                    id="mainImage">

                @if($product->productImages->count() > 0)
                <div class="thumbnail-grid">
                    @foreach($product->productImages as $image)
                    <img
                        src="{{ asset('storage/' . $image->image) }}"
                        alt="{{ $product->name }}"
                        class="thumbnail {{ $image->is_thumbnail ? 'active' : '' }}"
                        onclick="changeMainImage('{{ asset('storage/' . $image->image) }}', this)">
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="product-detail-info">
                <h1>{{ $product->name }}</h1>

                <!-- Meta Info -->
                <div class="product-meta">
                    <div class="meta-item">
                        <span>🏪</span>
                        <a href="#" style="color: var(--primary); font-weight: 500;">
                            {{ $product->store->name }}
                        </a>
                    </div>
                    <div class="meta-item">
                        <span>📁</span>
                        <span>{{ $product->productCategory->name }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="star">⭐</span>
                        <span>
                            @if($product->total_reviews > 0)
                            {{ $product->average_rating }} ({{ $product->total_reviews }} reviews)
                            @else
                            No reviews yet
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Price Section -->
                <div class="price-section">
                    <div class="product-price-large">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                    <div class="stock-info">
                        @if($product->stock > 0)
                        <span>✓</span>
                        <span>{{ $product->stock }} items available</span>
                        @else
                        <span style="color: var(--danger);">✗ Out of Stock</span>
                        @endif
                    </div>
                    <div style="margin-top: 8px; display: flex; gap: 8px;">
                        @if($product->condition === 'new')
                        <span class="badge badge-primary">🆕 Brand New</span>
                        @else
                        <span class="badge badge-warning">📦 Second Hand</span>
                        @endif
                        <span class="badge badge-info">⚖️ {{ $product->weight }}g</span>
                    </div>
                </div>

                <!-- Quantity Selector -->
                @if($product->stock > 0)
                <form action="{{ route('cart.add', $product) }}" method="POST">
                    @csrf
                    <div class="quantity-section">
                        <div class="quantity-selector">
                            <span class="qty-label">Quantity:</span>
                            <div class="qty-input-group">
                                <button type="button" class="qty-btn" onclick="decrementQty()">−</button>
                                <input
                                    type="number"
                                    name="quantity"
                                    id="qtyInput"
                                    class="qty-input"
                                    value="1"
                                    min="1"
                                    max="{{ $product->stock }}"
                                    readonly>
                                <button type="button" class="qty-btn" onclick="incrementQty()">+</button>
                            </div>
                            <span class="text-sm text-gray">Max: {{ $product->stock }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary btn-lg btn-add-cart">
                            🛒 Add to Cart
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg btn-buy-now" onclick="buyNow()">
                            ⚡ Buy Now
                        </button>
                    </div>
                </form>
                @else
                <div class="alert alert-danger">
                    This product is currently out of stock.
                </div>
                @endif

                <!-- Store Info Card -->
                <div class="store-card">
                    <div class="store-header">
                        <div class="store-logo">
                            🏪
                        </div>
                        <div class="store-info">
                            <h3>{{ $product->store->name }}</h3>
                            <p class="text-sm text-gray">{{ $product->store->city }}</p>
                        </div>
                    </div>
                    <div class="store-stats">
                        <div class="stat-item">
                            <div class="text-xs text-gray">Products</div>
                            <div class="stat-value">{{ $product->store->products->count() }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="text-xs text-gray">Rating</div>
                            <div class="stat-value">⭐ 4.8</div>
                        </div>
                        <div class="stat-item">
                            <div class="text-xs text-gray">Response</div>
                            <div class="stat-value">Fast</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="product-description">
            <h2>Product Description</h2>
            <div class="description-content">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="reviews-section">
            <div class="reviews-header">
                <div>
                    <h2>Customer Reviews</h2>
                </div>
                <div class="rating-summary">
                    @if($product->total_reviews > 0)
                    <div>
                        <div class="rating-number">{{ $product->average_rating }}</div>
                        <div class="rating-stars">⭐⭐⭐⭐⭐</div>
                        <div class="rating-text">{{ $product->total_reviews }} reviews</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($product->productReviews->count() > 0)
            @foreach($product->productReviews as $review)
            <div class="review-item">
                <div class="review-header">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">
                            {{ strtoupper(substr($review->transaction->buyer->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="reviewer-name">{{ $review->transaction->buyer->user->name }}</div>
                            <div class="review-date">{{ $review->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="review-rating">
                        @for($i = 0; $i < $review->rating; $i++)
                            <span class="star">⭐</span>
                            @endfor
                    </div>
                </div>
                <div class="review-text">
                    {{ $review->review }}
                </div>
            </div>
            @endforeach
            @else
            <div class="empty-reviews">
                <div style="font-size: 48px; margin-bottom: 16px;">💬</div>
                <p>No reviews yet. Be the first to review this product!</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Change main image when thumbnail clicked
    function changeMainImage(imageUrl, thumbnail) {
        document.getElementById('mainImage').src = imageUrl;

        // Update active thumbnail
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        thumbnail.classList.add('active');
    }

    // Quantity increment/decrement
    function incrementQty() {
        const input = document.getElementById('qtyInput');
        const max = parseInt(input.max);
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    function decrementQty() {
        const input = document.getElementById('qtyInput');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    // Buy now function
    function buyNow() {
        // Get quantity
        const quantity = document.getElementById('qtyInput').value;

        // Add to cart first, then redirect to checkout
        const form = document.querySelector('form');
        const formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("checkout.index") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add to cart');
            });
    }

    // Max stock validation
    document.getElementById('qtyInput').addEventListener('change', function() {
        const max = parseInt(this.max);
        if (parseInt(this.value) > max) {
            this.value = max;
            alert('Maximum stock available: ' + max);
        }
        if (parseInt(this.value) < 1) {
            this.value = 1;
        }
    });
</script>
@endpush
@endsection