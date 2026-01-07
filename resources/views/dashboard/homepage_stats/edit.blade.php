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
                    <h1 class="m-0"> {{ __('Homepage Stats') }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('homepage-stats.update', $stat->id) }}">

                    @csrf
                    @method('put')

                    @include('dashboard.partials._errors')

                    <div class="form-group">
                        <label for="number"> {{ __("Number") }}</label>
                        <input id="number" name="number" dir="auto" type="text" class="form-control" placeholder="{{ __("Number") }}" value="{{ old("number", $stat->number) }}">
                    </div>

                    <div class="form-group">
                        <label for="label"> {{ __("Label") }}</label>
                        <input id="label" name="label" dir="auto" type="text" class="form-control" placeholder="{{ __("Label") }}" value="{{ old("label", $stat->label) }}">
                    </div>

                    <!-- <div class="form-group">
                        <label for="icon"> {{ __("Icon") }}</label>
                        <input id="icon" name="icon" dir="auto" type="file" class="form-control" placeholder="{{ __("Icon") }}" value="{{ old("icon") }}">
                    </div> -->

                    <div class="text-right mb-5">
                        <input type="submit" class="btn btn-success" value="{{ __('Update') }}">
                    </div>
                </form>
            </div>
        </div>
        <!-- // END drawer-layout__content -->
    </div>
@endsection
