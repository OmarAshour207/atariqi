@extends('dashboard.layouts.app')

@section('title', 'تقييمات المستخدم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تقييمات المستخدم: {{ $user->full_name }}</h3>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary float-right">العودة</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ number_format($stats['average_rating'], 1) }}</h4>
                                    <p>متوسط التقييم</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $stats['total_ratings'] }}</h4>
                                    <p>إجمالي التقييمات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $stats['five_star_ratings'] }}</h4>
                                    <p>تقييمات 5 نجوم</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $stats['one_star_ratings'] }}</h4>
                                    <p>تقييمات 1 نجمة</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>تاريخ الرحلة</th>
                                    <th>نوع الرحلة</th>
                                    <th>السائق</th>
                                    <th>التقييم</th>
                                    <th>التعليق</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allRatings as $rating)
                                <tr>
                                    <td>{{ $rating->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($rating->trip_type == 'immediate')
                                            فورية
                                        @elseif($rating->trip_type == 'daily')
                                            يومية
                                        @else
                                            أسبوعية
                                        @endif
                                    </td>
                                    <td>{{ $rating->driver->name ?? 'غير محدد' }}</td>
                                    <td>
                                        <div class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span class="ml-2">{{ $rating->rating }}/5</span>
                                        </div>
                                    </td>
                                    <td>{{ $rating->comment ?? 'لا يوجد تعليق' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد تقييمات لهذا المستخدم</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating {
    display: inline-flex;
    align-items: center;
}
.rating i {
    font-size: 14px;
}
</style>
@endsection
