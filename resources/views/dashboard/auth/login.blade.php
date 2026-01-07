<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>لوحة موظفي على طريقي – تسجيل دخول</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{--brand:#38B2AC; --ink:#2F3A40}
    .btn-brand{background:var(--brand);border-color:var(--brand);color:#fff}
    .btn-brand:hover{background:#2AA199;border-color:#2AA199;color:#fff}
    .auth-card{max-width:440px}
  </style>
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">

<div class="container">
  <div class="mx-auto bg-white border rounded-3 shadow-sm p-4 auth-card">
    <div class="text-center mb-3">
      <div class="fs-1 text-brand"><i class="bi bi-person-badge"></i></div>
      <h1 class="h4 fw-bold mb-1">تسجيل دخول الموظفين</h1>
      <div class="text-secondary small">متاح فقط لبريد الدومين الرسمي للشركة</div>
    </div>

    <form id="loginForm" class="needs-validation" novalidate>
      <div class="mb-3">
        <label class="form-label">البريد الوظيفي</label>
        <div class="input-group">
          <input type="email" class="form-control" name="email" required placeholder="username@altariqi.com">
          <span class="input-group-text">@company</span>
            <div class="invalid-feedback">أدخل بريدًا صحيحًا ضمن نطاق الدومين.</div>
        </div>
        <div class="form-text">لن يُقبل سوى البريد المنتهي بدومين الشركة (مثلًا: @altariqi.com).</div>
      </div>

      <div class="mb-2">
        <label class="form-label">كلمة المرور</label>
        <div class="input-group">
          <input type="password" class="form-control" name="password" minlength="8" required>
          <button class="btn btn-outline-secondary" type="button" id="togglePass"><i class="bi bi-eye"></i></button>
            <div class="invalid-feedback">كلمة المرور لا تقل عن 8 أحرف.</div>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
          <input class="form-check-input" name="remember" type="checkbox" id="remember">
          <label class="form-check-label" for="remember">تذكرني</label>
        </div>
        <a href="#" class="small">نسيت كلمة المرور؟</a>
      </div>

      <div class="d-grid">
        <button class="btn btn-brand" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i> دخول</button>
      </div>

      <div id="loginAlert" class="alert mt-3 d-none" role="alert"></div>
    </form>

    <hr>
    <div class="text-center">
      <a href="{{ route('home') }}" class="small"><i class="bi bi-arrow-right-short"></i> العودة للموقع</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const toggle = document.getElementById('togglePass');
  toggle.addEventListener('click', ()=>{
    const input = document.querySelector('input[name="password"]');
    input.type = input.type === 'password' ? 'text' : 'password';
    toggle.innerHTML = input.type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
  });

  // تحقق من نطاق الدومين (عدّل الدومين هنا)
  const COMPANY_DOMAIN = 'atariqi.com';

  document.getElementById('loginForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const form = e.currentTarget;
    form.classList.add('was-validated');
    if(!form.checkValidity()) return;

    const email = form.email.value.trim().toLowerCase();
    // if(!email.endsWith('@'+COMPANY_DOMAIN)){
    //   showAlert('loginAlert','يسمح فقط ببريد الدومين الرسمي: @'+COMPANY_DOMAIN,'danger');
    //   return;
    // }

    try{
      const res = await fetch('/dashboard/login', {
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({
          email,
          password: form.password.value,
          remember: document.getElementById('remember').checked,
        })
      });
      if(!res.ok) throw new Error('unauthorized');
      const data = await res.json();
      showAlert('loginAlert','تم الدخول بنجاح. جارٍ تحويلك للوحة التحكم…','success');
      setTimeout(()=>{ window.location.href = data.redirect_url || '/dashboard/index'; }, 900);
    }catch(err){
      showAlert('loginAlert','بيانات الدخول غير صحيحة أو الحساب غير مخوّل.','danger');
    }
  });

  function showAlert(id, msg, type){
    const el = document.getElementById(id);
    el.className = `alert alert-${type}`;
    el.textContent = msg;
    el.classList.remove('d-none');
  }
</script>
</body>
</html>
