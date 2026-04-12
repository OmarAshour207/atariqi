@extends('dashboard.layouts.app')

@section('title', 'تقييمات الرحلات غير المستخدمة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تقييمات الرحلات غير المستخدمة</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $stats['total_unride_rates'] }}</h4>
                                    <p>إجمالي التقييمات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $stats['immediate_count'] }}</h4>
                                    <p>رحلات فورية</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $stats['daily_count'] }}</h4>
                                    <p>رحلات يومية</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $stats['weekly_count'] }}</h4>
                                    <p>رحلات أسبوعية</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>تاريخ التقييم</th>
                                    <th>نوع الرحلة</th>
                                    <th>الراكب</th>
                                    <th>السائق</th>
                                    <th>التقييم</th>
                                    <th>التعليق</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allUnrideRates as $unrideRate)
                                <tr>
                                    <td>{{ $unrideRate->sort_date }}</td>
                                    <td>
                                        @if($unrideRate->trip_type == 'immediate')
                                            فورية
                                        @elseif($unrideRate->trip_type == 'daily')
                                            يومية
                                        @else
                                            أسبوعية
                                        @endif
                                    </td>
                                    <td>{{ $unrideRate->ride->passenger->full_name ?? 'غير محدد' }}</td>
                                    <td>{{ $unrideRate->ride->driver->full_name ?? 'غير محدد' }}</td>
                                    <td>
                                        @if($unrideRate->rate)
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $unrideRate->rate ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                                <span class="ml-2">{{ $unrideRate->rate }}/5</span>
                                            </div>
                                        @else
                                            <span class="text-muted">لا يوجد تقييم</span>
                                        @endif
                                    </td>
                                    <td>{{ $unrideRate->comment ?? 'لا يوجد تعليق' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد تقييمات للرحلات غير المستخدمة</td>
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
