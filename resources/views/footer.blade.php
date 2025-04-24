<div class="footer-top py-2 bg-white pb-0">
  {{-- <div class="container"> --}}
    <div class="row py-4 pb-0">
      <!-- Logo Column -->
      <div class="col-xl-6 col-lg-4 col-12 mb-5 mb-lg-0 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="300ms">
        <div class="footer-widget">
            <a href="{{url('/')}}" class="navbar-brand">
                @if($companyProfile && $companyProfile->logo && file_exists(public_path($companyProfile->logo)))
                    <img class="max-h-45" src="{{url($companyProfile->logo)}}" style="cursor: pointer; max-height: 80px; min-height: 50px; max-width: 200px;"
                  class="image-fluid"/>
                @else
                    <img class="max-h-45" src="{{ url('assets') }}/img/logo.svg" style="cursor: pointer; max-height: 80px; min-height: 50px; max-width: 200px;"
                  class="image-fluid" />
                @endif
            </a>
        </div>
      </div>

      <!-- Know More Column -->
      <div class="col-xl-3 col-lg-3 col-6 wow fadeInRight" data-wow-duration="1.5s" data-wow-delay="300ms">
        <div class="footer-widget">
          <h3 class="widgte-title fs-18 fw-bold mb-4 text-dark">Know More</h3>
          <ul class="widget-menu list-unstyled mb-0">
            <li class="mb-3">
              <a href="/about-us" class="text-decoration-none" >
                <span class="text-clr-blue-light cursor-pointer">About FaithTrip</span>
              </a>
            </li>
            <li class="mb-3">
              <a href="/cookie-policy" class="fs-6 fw-normal text-clr-blue-light text-decoration-none" >
                Cookie Policy
              </a>
            </li>
            <li class="mb-0">
              <a href="{{ route('payment.methods') }}" class="fs-6 fw-normal text-clr-blue-light text-decoration-none" >
                Payment Methods
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Support Column -->
      <div class="col-xl-3 col-lg-4 col-6 wow fadeInLeft" data-wow-duration="1.5s" data-wow-delay="300ms">
        <div class="footer-widget ps-xl-5">
          <h3 class="widgte-title fs-18 fw-bold mb-4 text-dark">Support</h3>
          <ul class="widget-menu list-unstyled mb-0">
            <li class="mb-3">
              <a href="/privacy-policy"  class="text-decoration-none">
                <span class="text-clr-blue-light cursor-pointer">Privacy Policy</span>
              </a>
            </li>
            <li class="mb-3">
              <a href="/terms-and-conditions"  class="text-clr-blue-light text-decoration-none">
                <span class="text-clr-blue-light cursor-pointer">Terms and Conditions</span>
              </a>
            </li>
          </ul>
        </div>
      </div>


        <div class="col-12">
             <div class="text-center mt-4" style="background: #084277;padding: 12px 0px;">
                <img src="{{url('images')}}/ssl_commerz.png" style="width: 50%">
            </div>
        </div>

    </div>
  {{-- </div> --}}
</div>
