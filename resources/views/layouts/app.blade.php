<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>رافقني – الصفحة التعريفية</title>
  <meta name="description" content="رافقني | منصة على طريقي – تعريف بالشركة والتطبيق، إنجازات، تعاونات، وتعليقات العملاء." />

  <!-- Bootstrap 5 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root{
        --brand:#38B2AC;     /* Teal */
        --brand-700:#2AA199;
        --ink:#2F3A40;       /* Dark gray */
        --muted:#6B7280;     /* Gray-500 */
        --bg:#F7FAFA;
        }
        body {
        font-family: 'Tajawal', system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji"; color:var(--ink); background:var(--bg);
        }
        .btn-brand{background:var(--brand); border-color:var(--brand); color:#fff}
        .btn-brand:hover{background:var(--brand-700); border-color:var(--brand-700); color:#fff}
        .text-brand{color:var(--brand)!important}
        .bg-brand{background:var(--brand)!important}
        .rounded-xxl{border-radius:2rem}
        .hero{
        background: radial-gradient(1400px 400px at 50% -100px, var(--brand) 0%, transparent 60%), #fff;
        }
        .badge-soft{
        background: rgba(56,178,172,.12);
        color: var(--brand);
        border: 1px solid rgba(56,178,172,.24);
        }
        .shadow-soft{box-shadow: 0 10px 30px rgba(0,0,0,.06)}
        .divider{
        height: 12px;
        background: repeating-linear-gradient(135deg, var(--brand) 0 20px, #e6f5f4 20px 40px);
        }
        .app-badges img{height: 52px}
        .avatar{width:48px;height:48px;border-radius:50%;object-fit:cover}
        footer a{color:#e6f5f4;text-decoration:none}
        footer a:hover{text-decoration:underline}
    </style>

    @stack('styles')
</head>

<body>
        {{-- header --}}
        @include('layouts.navbar')
        {{--End header--}}

        @yield('content')

    <footer class="bg-dark text-light pt-5">
    <div class="container pb-4">
        <div class="row g-4">
        <div class="col-md-6">
            <h5>عن رافقني</h5>
            <p class="small text-white-50" id="footerAbout">…</p>
        </div>
        <div class="col-md-3">
            <h6>روابط</h6>
            <ul class="list-unstyled small">
            <li><a href="{{ route('support') }}">الدعم الفني</a></li>
            <li><a href="{{ route('dashboard.login') }}">دخول الموظفين</a></li>
            </ul>
        </div>
        <div class="col-md-3">
            <h6>تابعنا</h6>
            <div class="d-flex gap-3 fs-4">
            <a href="{{ setting('twitter') }}"><i class="bi bi-twitter-x"></i></a>
            <a href="{{ setting('instagram') }}"><i class="bi bi-instagram"></i></a>
            <a href="{{ setting('linkedin') }}"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
        </div>
        <hr class="border-secondary">
        <div class="d-flex justify-content-between small text-white-50">
        <span>© <span id="year"></span> على طريقي – رافقني</span>
        <span>جميع الحقوق محفوظة</span>
        </div>
    </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Define the base URL for use in JavaScript
        const BASE_URL = "{{ url('/') }}";

        // سنة الفوتر
        document.getElementById('year').textContent = new Date().getFullYear();

        // جلب بيانات الصفحة من قاعدة البيانات
        fetch('/homepage-sections')
            .then(r => r.ok ? r.json() : Promise.reject(r))
            .then(data => {
                // عن الشركة
                document.getElementById('about_us_content').innerHTML = data.about_us?.content ?? '';
                document.getElementById('about_us_icon').src = data.about_us?.icon ? BASE_URL + data.about_us.icon : 'https://i.pravatar.cc/80';

                // عن التطبيق – المزايا
                document.getElementById('appText').innerHTML = data.about_app?.content ?? '';
                // const fWrap = document.getElementById('features');
                // fWrap.innerHTML = data.about_app?.content ?? '';

                // الأرقام
                const sRow = document.getElementById('statsRow');

                (data.stats ?? []).forEach(s => {
                    sRow.insertAdjacentHTML('beforeend', `
                    <div class="col-6 col-lg-3">
                        <div class="p-4 bg-white rounded-3 shadow-soft text-center">
                            <div class="display-6 fw-bold text-brand">${s.number}</div>
                            <div class="small text-secondary">${s.label}</div>
                        </div>
                    </div>
                    `);
                });

                // الشركاء
                const pRow = document.getElementById('partnersRow');
                (data.partners ?? []).forEach(p => {
                    pRow.insertAdjacentHTML('beforeend', `
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="p-3 bg-white rounded-3 shadow-soft text-center">
                            <img src="${p.icon ? BASE_URL + p.icon : 'https://i.pravatar.cc/80'}" class="img-fluid" alt="${p.name}">
                        </div>
                    </div>
                    `);
                });

                // التعليقات
                const tRow = document.getElementById('testimonialsRow');
                (data.testimonials ?? []).forEach(t => {
                    tRow.insertAdjacentHTML('beforeend', `
                    <div class="col-md-6 col-lg-4">
                        <div class="h-100 p-4 bg-white rounded-3 shadow-soft">
                            <div class="d-flex align-items-center gap-3">
                                <img class="avatar" src="${t.icon ? BASE_URL + t.icon : 'https://i.pravatar.cc/80'}" alt="${t.name}">
                                <div>
                                    <div class="fw-semibold">${t.name}</div>
                                    <div class="small text-secondary">${t.title || ''}</div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0">“${t.description}”</p>
                        </div>
                    </div>
                    `);
                });

                // الإنجازات
                const aRow = document.getElementById('achievementsRow');
                (data.achievements ?? []).forEach(a => {
                    aRow.insertAdjacentHTML('beforeend', `
                    <div class="col-md-6 col-lg-4">
                        <div class="h-100 p-4 bg-white rounded-3 shadow-soft">
                            <div class="fs-2 text-brand"><i class="bi ${a.icon || 'bi-award'}"></i></div>
                            <h5 class="mt-2">${a.title}</h5>
                            <p class="text-secondary mb-0">${a.description}</p>
                        </div>
                    </div>
                    `);
                });

                // روابط المتاجر + فوتر
                document.getElementById('appStoreLink').href = data.stores?.appStore || '#';
                document.getElementById('playStoreLink').href = data.stores?.playStore || '#';
                document.getElementById('footerAbout').innerHTML = data.about_us?.content ?? '';
            })
            .catch(err => console.error('API /api/homepage-sections error', err));
    </script>

    @stack('scripts')

</body>
</html>
