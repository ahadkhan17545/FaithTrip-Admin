<!-- Start Services Area -->
    <div class="services">
        <div class="container">
            <div class="row">
                <div class="col-12 position-relative">
                    <div class="swiper services-slider">
                        <div class="swiper-wrapper">
                            <!-- Slider Item -->

                            @foreach ($banners as $banner)
                            <a href="{{$banner->url}}" target="_blank" class="swiper-slide">
                                <img src="{{url($banner->image)}}" alt="FaithTrip" />
                            </a>
                            @endforeach

                        </div>

                    </div>
                    <div class="swiper-pagination"></div>

                </div>
            </div>
        </div>
    </div>
    <!-- End Servies Area -->
