@extends('layouts.app')

@section('content')
<!-- Hero -->
<header class="hero py-5">
  <div class="container py-4">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <span class="badge badge-soft mb-3"><i class="bi bi-geo-alt-fill me-1"></i>{{ __('Atariqi - Rafiqni') }}</span>
        <h1 class="display-5 fw-bold lh-sm mb-3">{{ app()->getLocale() === 'ar' ? $slider->title_ar : $slider->title }}</h1>
        <p class="lead text-secondary mb-4">{!! app()->getLocale() === 'ar' ? $slider->content_ar : $slider->content !!}</p>
        <div class="d-flex gap-2 app-badges flex-wrap">
          <a id="appStoreLink" class="d-inline-block" href="{{ setting('app_store') }}" target="_blank" rel="noopener">
            <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="App Store">
          </a>
          <a id="playStoreLink" class="d-inline-block" href="{{ setting('play_store') }}" target="_blank" rel="noopener">
            <img src="https://play.google.com/intl/en_us/badges/static/images/badges/ar_badge_web_generic.png" alt="Google Play" style="height:60px">
          </a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="bg-white rounded-xxl p-3 shadow-soft">
          <img src="{{ $slider->icon ? url($slider->icon) : '' }}" class="w-100 rounded-3" alt="لقطة توضيحية للتطبيق">
        </div>
      </div>
    </div>
  </div>
  <div class="divider"></div>
</header>

<!-- عن الشركة -->
<section id="about" class="py-5">
  <div class="container">
    <div class="row g-4 align-items-center">
      <div class="col-lg-6">
        <img id="about_us_icon" src="https://picsum.photos/800/520?random=9" class="rounded-3 w-100 shadow-soft" alt="عن الشركة">
      </div>
      <div class="col-lg-6" id="about_us_content">
        <!-- <h2 class="fw-bold mb-3 about_us_title">عن الشركة</h2> -->
        <!-- <p id="aboutText" class="text-secondary mb-3">…</p> -->
        <!-- <ul class="list-unstyled small text-secondary mb-0" id="aboutBullets"></ul> -->
      </div>
    </div>
  </div>
</section>

<!-- عن التطبيق -->
<section id="app" class="py-5 bg-white">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-6" id="about_app_content">
        <h2 class="fw-bold mb-3">{{ __('About App') }}</h2>
        <p id="appText">…</p>
      </div>
      <div class="col-lg-6">
        <img id="about_app_icon" src="https://picsum.photos/800/520?random=9" class="rounded-3 w-100 shadow-soft" alt="عن التطبيق">
      </div>
    </div>
  </div>
</section>

<!-- الأرقام -->
<section id="stats" class="py-5">
  <div class="container">
    <h2 class="fw-bold mb-4">{{ __('Contact Us via') }}</h2>
    <div class="row g-4" id="statsRow">
      <!-- يتم ملؤها دِيناميكيًا -->
    </div>
  </div>
</section>

<!-- التعاونات -->
<section id="partners" class="py-5 bg-white">
  <div class="container">
    <h2 class="fw-bold mb-4">{{ __('Our Partners') }}</h2>
    <div class="row g-4" id="partnersRow">
      <!-- شعارات الشركاء -->
    </div>
  </div>
</section>

<!-- تعليقاتكم -->
<section id="testimonials" class="py-5">
  <div class="container">
    <h2 class="fw-bold mb-4">{{ __('What Our Customers Say') }}</h2>
    <div class="row g-4" id="testimonialsRow"></div>
  </div>
</section>

<!-- إنجازاتنا -->
<section id="achievements" class="py-5 bg-white">
  <div class="container">
    <h2 class="fw-bold mb-4">{{ __('Our Achievements') }}</h2>
    <div class="row g-4" id="achievementsRow"></div>
  </div>
</section>

<!-- CTA -->
<section class="py-5 text-center">
  <div class="container">
    <div class="p-4 p-lg-5 bg-brand text-white rounded-3">
      <h3 class="mb-3">{{ __('Start Your Journey with Rafiqni Now') }}</h3>
      <p class="mb-4">{{ __('Download the app and try a reliable and fast transit experience.') }}</p>
      <a href="#top" class="btn btn-light">{{ __('Go Back to Top') }}</a>
    </div>
  </div>
</section>


@endsection
