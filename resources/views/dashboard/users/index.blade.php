@extends('dashboard.layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إدارة المستخدمين</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('users.index') }}" class="form-inline">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">البحث بالاسم أو البريد الإلكتروني</label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{ request('name') }}" placeholder="الاسم أو البريد الإلكتروني">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="phone">رقم الهاتف</label>
                                            <input type="text" name="phone" id="phone" class="form-control" value="{{ request('phone') }}" placeholder="رقم الهاتف">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="gender">الجنس</label>
                                            <select name="gender" id="gender" class="form-control">
                                                <option value="">الكل</option>
                                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="university_id">الجامعة</label>
                                            <select name="university_id" id="university_id" class="form-control">
                                                <option value="">الكل</option>
                                                @foreach($universities as $university)
                                                    <option value="{{ $university->id }}" {{ request('university_id') == $university->id ? 'selected' : '' }}>
                                                        {{ $university->{'name-ar'} }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="stage_id">المرحلة</label>
                                            <select name="stage_id" id="stage_id" class="form-control">
                                                <option value="">الكل</option>
                                                @foreach($stages as $stage)
                                                    <option value="{{ $stage->id }}" {{ request('stage_id') == $stage->id ? 'selected' : '' }}>
                                                        {{ $stage->{'name-ar'} }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary form-control">
                                                <i class="fas fa-search"></i> بحث
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_from">تاريخ التسجيل من</label>
                                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_to">تاريخ التسجيل إلى</label>
                                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <a href="{{ route('users.index') }}" class="btn btn-secondary form-control">
                                                <i class="fas fa-times"></i> مسح الفلاتر
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>رقم الهاتف</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>عدد الرحلات الفورية</th>
                                    <th>عدد الرحلات اليومية</th>
                                    <th>عدد الرحلات الأسبوعية</th>
                                    <th>إجمالي الرحلات</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->full_phone_number }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->immediate_trips_count }}</td>
                                    <td>{{ $user->daily_trips_count }}</td>
                                    <td>{{ $user->weekly_trips_count }}</td>
                                    <td>{{ $user->total_trips }}</td>
                                    <td>
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">عرض التفاصيل</a>
                                        <a href="{{ route('users.rates', $user->id) }}" class="btn btn-warning btn-sm">التقييمات</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
