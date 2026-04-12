@extends('dashboard.layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إدارة المستخدمين</h3>
                </div>
                <div class="card-body">
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
