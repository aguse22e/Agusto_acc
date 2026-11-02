<div class="page-header card">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <i class="{{ $pageIcon }} bg-c-blue"></i>
                <div class="d-inline">
                    <h5>{{ $pageTitle }}</h5>
                    @if (isset($pageSubtitle))
                        <span>{{ $pageSubtitle }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb breadcrumb-title">

                    <li class="breadcrumb-item">
                        <a href="{{$parentMenu ? $parentMenu : '#'}}"><i class="{{ $pageIcon }}"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">{{$current}}</a> </li>
                </ul>
            </div>
        </div>
    </div>
</div>
