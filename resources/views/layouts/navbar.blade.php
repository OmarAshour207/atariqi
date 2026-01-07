<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
      <span class="fw-bold text-brand">رافقني</span>
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="#about">عن الشركة</a></li>
        <li class="nav-item"><a class="nav-link" href="#app">عن التطبيق</a></li>
        <li class="nav-item"><a class="nav-link" href="#stats">الأرقام</a></li>
        <li class="nav-item"><a class="nav-link" href="#partners">التعاونات</a></li>
        <li class="nav-item"><a class="nav-link" href="#testimonials">تعليقاتكم</a></li>
        <li class="nav-item"><a class="nav-link" href="#achievements">إنجازاتنا</a></li>
      </ul>
      <div class="d-flex gap-2">
        <a href="{{ route('support') }}" class="btn btn-outline-secondary">الدعم الفني</a>
        <a href="{{ route('dashboard.login') }}" class="btn btn-brand">دخول الموظفين</a>
      </div>
    </div>
  </div>
</nav>

