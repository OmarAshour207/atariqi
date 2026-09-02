<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
      <span class="fw-bold text-brand">{{ __('Rafiqni') }}</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#about">{{ __('About Company') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#app">{{ __('About App') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#stats">{{ __('Contact Us') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#partners">{{ __('Partners') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#testimonials">{{ __('Testimonials') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#achievements">{{ __('Achievements') }}</a></li>
        <li class="nav-item">
            <a class="nav-link" href="{{ session('locale', 'en') === 'ar' ? route('change.locale', ['locale' => 'en']) : route('change.locale', ['locale' => 'ar']) }}">
                {{ session('locale', 'en') === 'ar' ? 'English' : 'العربية' }}
            </a>
        </li>
      </ul>
      <div class="d-flex gap-2">
        <a href="{{ route('support') }}" class="btn btn-outline-secondary">{{ __('Support') }}</a>
        <a href="{{ route('dashboard.login') }}" class="btn btn-brand">{{ __('Dashboard Login') }}</a>
      </div>
    </div>
  </div>
</nav>

