@extends('dashboard.layouts.app')

@section('title', 'تفاصيل المستخدم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تفاصيل المستخدم: {{ $user->full_name }}</h3>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary float-right">العودة</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>المعلومات الأساسية</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>الاسم:</th>
                                    <td>{{ $user->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>رقم الهاتف:</th>
                                    <td>{{ $user->full_phone_number }}</td>
                                </tr>
                                <tr>
                                    <th>البريد الإلكتروني:</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ التسجيل:</th>
                                    <td>{{ $user->{"date-of-add"} }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>إحصائيات الرحلات</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>الرحلات الفورية:</th>
                                    <td>{{ $user->immediate_trips_count }}</td>
                                </tr>
                                <tr>
                                    <th>الرحلات اليومية:</th>
                                    <td>{{ $user->daily_trips_count }}</td>
                                </tr>
                                <tr>
                                    <th>الرحلات الأسبوعية:</th>
                                    <td>{{ $user->weekly_trips_count }}</td>
                                </tr>
                                <tr>
                                    <th><strong>إجمالي الرحلات:</strong></th>
                                    <td><strong>{{ $user->total_trips }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
