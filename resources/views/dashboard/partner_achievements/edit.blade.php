@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }} </a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0"> {{ $partnerAchievement->type == 'partners' ? __('Our Partners') : __('Our Achievements') }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('partner-achievements.update', $partnerAchievement->id) }}" enctype="multipart/form-data">

                    @csrf
                    @method('put')

                    @include('dashboard.partials._errors')

                    <div class="form-group">
                        <label for="title"> {{ __("Title") }}</label>
                        <input id="title" name="title" dir="auto" type="text" class="form-control" placeholder="{{ __("Title") }}" value="{{ old("title", $partnerAchievement->title) }}">
                    </div>

                    @if($partnerAchievement->type != 'partners')
                        <div class="form-group">
                            <label for="description">{{ __("Description") }}</label>
                            <textarea id="description" name="description" class="form-control">{{ old("description", $partnerAchievement->description) }}</textarea>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="icon"> {{ __("Icon") }}</label>
                        <input id="icon" name="icon" dir="auto" type="file" class="form-control" placeholder="{{ __("Icon") }}" value="{{ old("icon") }}">
                    </div>

                    <div class="text-right mb-5">
                        <input type="submit" class="btn btn-success" value="{{ __('Update') }}">
                    </div>
                </form>
            </div>
        </div>
        <!-- // END drawer-layout__content -->
    </div>
@endsection
